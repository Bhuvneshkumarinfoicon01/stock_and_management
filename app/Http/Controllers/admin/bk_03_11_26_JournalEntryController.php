<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\GlAccount;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Services\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JournalEntryController extends Controller
{
    public function index(): View
    {
        $entries = JournalEntry::query()
            ->with(['user'])
            ->withCount('lines')
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->paginate(25);

        return view('admin.accounting.journals.index', compact('entries'));
    }

    public function create(): View
    {
        $accounts = GlAccount::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('admin.accounting.journals.create', compact('accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'reference' => 'nullable|string|max:128',
            'description' => 'nullable|string|max:2000',
            'lines' => 'required|array|min:2',
            'lines.*.gl_account_id' => 'required|exists:gl_accounts,id',
            'lines.*.line_description' => 'nullable|string|max:500',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
        ]);

        foreach ($request->input('lines', []) as $i => $line) {
            $d = round((float) ($line['debit'] ?? 0), 2);
            $c = round((float) ($line['credit'] ?? 0), 2);
            if ($d > 0 && $c > 0) {
                return back()->withErrors(['lines' => 'Line ' . ($i + 1) . ' cannot have both a debit and a credit.'])->withInput();
            }
        }

        $normalized = $this->normalizeLines($validated['lines']);
        if (count($normalized) < 2) {
            return back()->withErrors(['lines' => 'Enter at least two lines with an amount on one side (debit or credit) each.'])->withInput();
        }

        $balanceError = $this->balanceErrorMessage($normalized);
        if ($balanceError !== null) {
            return back()->withErrors(['lines' => $balanceError])->withInput();
        }

        DB::transaction(function () use ($validated, $normalized) {
            $entry = JournalEntry::create([
                'entry_date' => $validated['entry_date'],
                'reference' => $validated['reference'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => 'draft',
                'user_id' => Auth::id(),
            ]);

            foreach ($normalized as $line) {
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'gl_account_id' => $line['gl_account_id'],
                    'line_description' => $line['line_description'] ?: null,
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                ]);
            }
        });

        return redirect()->route('admin.accounting.journals.index')->with('success', 'Journal saved as draft. Post it when ready.');
    }

    public function show(JournalEntry $journalEntry, AccountingService $accountingService): View
    {
        $journalEntry->load(['lines.glAccount', 'user']);
        $saleJournalContext = $accountingService->saleJournalDisplayContext($journalEntry);

        return view('admin.accounting.journals.show', compact('journalEntry', 'saleJournalContext'));
    }

    public function post(JournalEntry $journalEntry): RedirectResponse
    {
        if (!$journalEntry->isDraft()) {
            return redirect()->route('admin.accounting.journals.show', $journalEntry)->withErrors(['status' => 'Only draft journals can be posted.']);
        }

        $journalEntry->load('lines');
        if ($journalEntry->lines->count() < 2) {
            return redirect()->route('admin.accounting.journals.show', $journalEntry)->withErrors(['lines' => 'Journal must have at least two lines.']);
        }

        $normalized = $journalEntry->lines->map(fn (JournalLine $l) => [
            'gl_account_id' => $l->gl_account_id,
            'line_description' => $l->line_description,
            'debit' => (float) $l->debit,
            'credit' => (float) $l->credit,
        ])->all();

        $balanceError = $this->balanceErrorMessage($normalized);
        if ($balanceError !== null) {
            return redirect()->route('admin.accounting.journals.show', $journalEntry)->withErrors(['lines' => $balanceError]);
        }

        foreach ($journalEntry->lines as $line) {
            if ((float) $line->debit > 0 && (float) $line->credit > 0) {
                return redirect()->route('admin.accounting.journals.show', $journalEntry)->withErrors(['lines' => 'Each line may only have a debit or a credit, not both.']);
            }
            if ((float) $line->debit <= 0 && (float) $line->credit <= 0) {
                return redirect()->route('admin.accounting.journals.show', $journalEntry)->withErrors(['lines' => 'Remove empty lines or enter amounts before posting.']);
            }
        }

        $journalEntry->update([
            'status' => 'posted',
            'posted_at' => now(),
        ]);

        return redirect()->route('admin.accounting.journals.show', $journalEntry)->with('success', 'Journal posted. Amounts are now in the general ledger.');
    }

    public function destroy(JournalEntry $journalEntry): RedirectResponse
    {
        if (!$journalEntry->isDraft()) {
            return redirect()->route('admin.accounting.journals.show', $journalEntry)->withErrors(['status' => 'Only draft journals can be deleted.']);
        }

        $journalEntry->delete();

        return redirect()->route('admin.accounting.journals.index')->with('success', 'Draft journal deleted.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array<int, array{gl_account_id: int, line_description: string|null, debit: float, credit: float}>
     */
    private function normalizeLines(array $lines): array
    {
        $out = [];
        foreach ($lines as $line) {
            $debit = round((float) ($line['debit'] ?? 0), 2);
            $credit = round((float) ($line['credit'] ?? 0), 2);
            if ($debit <= 0 && $credit <= 0) {
                continue;
            }
            if ($debit > 0 && $credit > 0) {
                continue;
            }
            $out[] = [
                'gl_account_id' => (int) $line['gl_account_id'],
                'line_description' => isset($line['line_description']) ? (string) $line['line_description'] : null,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        return $out;
    }

    /**
     * @param  array<int, array{debit: float, credit: float}>  $normalized
     */
    private function balanceErrorMessage(array $normalized): ?string
    {
        $totalDebit = 0.0;
        $totalCredit = 0.0;
        foreach ($normalized as $line) {
            $totalDebit += $line['debit'];
            $totalCredit += $line['credit'];
        }
        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            return 'Double-entry requires total debits (' . number_format($totalDebit, 2) . ') to equal total credits (' . number_format($totalCredit, 2) . ').';
        }

        return null;
    }
}

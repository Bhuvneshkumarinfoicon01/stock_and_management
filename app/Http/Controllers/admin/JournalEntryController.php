<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\GlAccount;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\SaleReturn;
use App\Models\Supplier;
use App\Services\AccountingService;
use App\Services\RecordNavigator;
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
        $canEditJournal = $this->userCanEditJournals();
        $canEditGeneratedJournals = $this->userIsSuperAdmin();

        return view('admin.accounting.journals.index', compact('entries', 'canEditJournal', 'canEditGeneratedJournals'));
    }

    public function create(): View
    {
        $accounts = GlAccount::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
        $suppliers = Supplier::query()->orderBy('company_name')->get(['id', 'company_name']);
        $customers = Customer::query()->orderBy('customer_name')->get(['id', 'customer_name']);

        return view('admin.accounting.journals.create', compact('accounts', 'suppliers', 'customers'));
    }

    public function edit(JournalEntry $journalEntry): View
    {
        $this->authorizeJournalEdit($journalEntry);
        $journalEntry->load('lines');
        $sourceParty = $this->sourcePartyForJournal($journalEntry);
        $existingPartyLine = $journalEntry->lines->first(
            fn (JournalLine $line) => $line->supplier_id || $line->customer_id
        );
        $journalPartyReference = $existingPartyLine?->supplier_id
            ? 'supplier:' . $existingPartyLine->supplier_id
            : ($existingPartyLine?->customer_id
                ? 'customer:' . $existingPartyLine->customer_id
                : ($sourceParty['reference'] ?? ''));
        $accounts = GlAccount::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
        $suppliers = Supplier::query()->orderBy('company_name')->get(['id', 'company_name']);
        $customers = Customer::query()->orderBy('customer_name')->get(['id', 'customer_name']);

        return view('admin.accounting.journals.create', compact('accounts', 'journalEntry', 'journalPartyReference', 'suppliers', 'customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'reference' => 'nullable|string|max:128',
            'description' => 'nullable|string|max:2000',
            'party_reference' => 'nullable|string|max:64',
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

        $normalized = $this->normalizeLines($this->attachParties(
            $validated['lines'],
            $validated['party_reference'] ?? null,
        ));
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
                    'supplier_id' => $line['supplier_id'],
                    'customer_id' => $line['customer_id'],
                    'party_name' => $line['party_name'],
                    'line_description' => $line['line_description'] ?: null,
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                ]);
            }
        });

        return redirect()->route('admin.accounting.journals.index')->with('success', 'Journal saved as draft. Post it when ready.');
    }

    public function update(Request $request, JournalEntry $journalEntry): RedirectResponse
    {
        $this->authorizeJournalEdit($journalEntry);
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'reference' => 'nullable|string|max:128',
            'description' => 'nullable|string|max:2000',
            'party_reference' => 'nullable|string|max:64',
            'lines' => 'required|array|min:2',
            'lines.*.gl_account_id' => 'required|exists:gl_accounts,id',
            'lines.*.line_description' => 'nullable|string|max:500',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated['lines'] as $index => $line) {
            $debit = round((float) ($line['debit'] ?? 0), 2);
            $credit = round((float) ($line['credit'] ?? 0), 2);
            if ($debit > 0 && $credit > 0) {
                return back()->withErrors([
                    'lines' => 'Line ' . ($index + 1) . ' cannot have both a debit and a credit.',
                ])->withInput();
            }
        }

        $normalized = $this->normalizeLines($this->attachParties(
            $validated['lines'],
            $validated['party_reference'] ?? null,
            $journalEntry->source_type,
        ));
        if (count($normalized) < 2) {
            return back()->withErrors([
                'lines' => 'Enter at least two lines with an amount on one side (debit or credit) each.',
            ])->withInput();
        }

        $balanceError = $this->balanceErrorMessage($normalized);
        if ($balanceError !== null) {
            return back()->withErrors(['lines' => $balanceError])->withInput();
        }

        DB::transaction(function () use ($journalEntry, $validated, $normalized) {
            $entry = JournalEntry::query()
                ->whereKey($journalEntry->id)
                ->lockForUpdate()
                ->firstOrFail();
            $this->authorizeJournalEdit($entry);

            $entry->update([
                'entry_date' => $validated['entry_date'],
                'reference' => $validated['reference'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);
            $entry->lines()->delete();

            foreach ($normalized as $line) {
                $entry->lines()->create([
                    'gl_account_id' => $line['gl_account_id'],
                    'supplier_id' => $line['supplier_id'],
                    'customer_id' => $line['customer_id'],
                    'party_name' => $line['party_name'],
                    'line_description' => $line['line_description'] ?: null,
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                ]);
            }
        });

        return redirect()->route('admin.accounting.journals.show', $journalEntry)
            ->with('success', 'Journal updated.');
    }

    public function show(JournalEntry $journalEntry, AccountingService $accountingService): View
    {
        $journalEntry->load(['lines.glAccount', 'lines.supplier', 'lines.customer', 'user']);
        $partyLine = $journalEntry->lines->first(
            fn (JournalLine $line) => $line->party_name || $line->supplier_id || $line->customer_id
        );
        $journalPartyName = $partyLine?->party_name
            ?: $partyLine?->supplier?->company_name
            ?: $partyLine?->customer?->customer_name;
        $sourceParty = $this->sourcePartyForJournal($journalEntry);
        $journalPartyName = $journalPartyName ?: ($sourceParty['name'] ?? null);
        $saleJournalContext = $accountingService->saleJournalDisplayContext($journalEntry);
        $canEditJournal = $this->userCanEditJournals();
        $canEditGeneratedJournals = $this->userIsSuperAdmin();
        $recordNav = RecordNavigator::links(
            JournalEntry::query()->orderByDesc('entry_date')->orderByDesc('id'),
            $journalEntry->id,
            fn ($id) => route('admin.accounting.journals.show', $id),
        );

        return view('admin.accounting.journals.show', compact('journalEntry', 'journalPartyName', 'saleJournalContext', 'recordNav', 'canEditJournal', 'canEditGeneratedJournals'));
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

    private function authorizeJournalEdit(JournalEntry $journalEntry): void
    {
        abort_unless($this->userCanEditJournals(), 403);
        abort_if(
            $journalEntry->source_type !== null && ! $this->userIsSuperAdmin(),
            403,
            'Automatically generated journals must be corrected from their source document.'
        );
    }

    private function userIsSuperAdmin(): bool
    {
        $user = Auth::user();

        return $user !== null && (int) $user->role_id === 1;
    }

    private function userCanEditJournals(): bool
    {
        $user = Auth::user();

        return $user !== null
            && (in_array((int) $user->role_id, [1, 2], true) || hasPermission('update', 'journal'));
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array<int, array<string, mixed>>
     */
    private function attachParties(array $lines, ?string $partyReference, ?string $sourceType = null): array
    {
        $accounts = GlAccount::query()
            ->whereIn('id', collect($lines)->pluck('gl_account_id')->unique())
            ->where('is_active', true)
            ->get()
            ->keyBy('id');
        $selectedPartyType = null;
        $partyId = null;
        $partyName = null;
        if ($partyReference !== null && $partyReference !== '') {
            if (! preg_match('/^(supplier|customer):([1-9][0-9]*)$/', $partyReference, $matches)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'party_reference' => 'Choose a supplier for a purchase or a customer for a sale.',
                ]);
            }

            $selectedPartyType = $matches[1];
            $partyId = (int) $matches[2];
            if ($selectedPartyType === 'supplier') {
                $supplier = Supplier::query()->find($partyId);
                if (! $supplier) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'party_reference' => 'The selected supplier no longer exists.',
                    ]);
                }
                $partyName = $supplier->company_name;
            } else {
                $customer = Customer::query()->find($partyId);
                if (! $customer) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'party_reference' => 'The selected customer no longer exists.',
                    ]);
                }
                $partyName = $customer->customer_name;
            }
        }

        $partyAssigned = false;
        $cashLineIndexes = [];
        $activeLineIndexes = [];
        foreach ($lines as $index => &$line) {
            $line['supplier_id'] = null;
            $line['customer_id'] = null;
            $line['party_name'] = null;
            if (
                round((float) ($line['debit'] ?? 0), 2) <= 0
                && round((float) ($line['credit'] ?? 0), 2) <= 0
            ) {
                continue;
            }

            $account = $accounts->get((int) $line['gl_account_id']);
            if (! $account) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "lines.{$index}.gl_account_id" => 'Choose an active account for this journal line.',
                ]);
            }

            $activeLineIndexes[] = $index;
            $accountPartyType = $this->counterpartyTypeForAccount($account);
            if ($accountPartyType === null) {
                if ($this->isCashAccount($account)) {
                    $cashLineIndexes[] = $index;
                }
                continue;
            }

            if ($selectedPartyType !== $accountPartyType) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'party_reference' => 'Choose a supplier for Accounts Payable or a customer for Accounts Receivable.',
                ]);
            }

            if ($accountPartyType === 'supplier') {
                $line['supplier_id'] = $partyId;
            } else {
                $line['customer_id'] = $partyId;
            }
            $line['party_name'] = $partyName;
            $partyAssigned = true;
        }
        unset($line);

        if ($selectedPartyType !== null && ! $partyAssigned) {
            if ($sourceType === 'sale_return_cogs' && $selectedPartyType === 'customer') {
                foreach ($activeLineIndexes as $index) {
                    $lines[$index]['customer_id'] = $partyId;
                    $lines[$index]['party_name'] = $partyName;
                }
                $partyAssigned = $activeLineIndexes !== [];
            } elseif ($cashLineIndexes !== []) {
                $lineIndex = $cashLineIndexes[0];
                $lines[$lineIndex][$selectedPartyType . '_id'] = $partyId;
                $lines[$lineIndex]['party_name'] = $partyName;
                $partyAssigned = true;
            }

            if (! $partyAssigned) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'party_reference' => 'Choose a customer for Accounts Receivable or a cash settlement line, or a supplier for Accounts Payable or a cash settlement line.',
                ]);
            }
        }

        return $lines;
    }

    private function isCashAccount(GlAccount $account): bool
    {
        return (string) $account->code === (string) config('accounting.accounts.cash', '1000')
            || str_starts_with(strtolower(trim($account->name)), 'cash on hand');
    }

    /**
     * @return array{reference: string, name: string|null}|null
     */
    private function sourcePartyForJournal(JournalEntry $journalEntry): ?array
    {
        if (
            ! in_array($journalEntry->source_type, ['sale_return', 'sale_return_cogs'], true)
            || ! $journalEntry->source_id
        ) {
            return null;
        }

        $saleReturn = SaleReturn::query()
            ->with('customer')
            ->find($journalEntry->source_id);
        if (! $saleReturn?->customer_id) {
            return null;
        }

        return [
            'reference' => 'customer:' . $saleReturn->customer_id,
            'name' => $saleReturn->customer?->customer_name,
        ];
    }

    private function counterpartyTypeForAccount(GlAccount $account): ?string
    {
        $name = strtolower(trim($account->name));
        if (
            (string) $account->code === (string) config('accounting.accounts.accounts_payable', '2000')
            || str_starts_with($name, 'accounts payable')
        ) {
            return 'supplier';
        }
        if (
            (string) $account->code === (string) config('accounting.accounts.accounts_receivable', '1100')
            || str_starts_with($name, 'accounts receivable')
        ) {
            return 'customer';
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array<int, array{gl_account_id: int, supplier_id: ?int, customer_id: ?int, party_name: ?string, line_description: string|null, debit: float, credit: float}>
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
                'supplier_id' => $line['supplier_id'] ?? null,
                'customer_id' => $line['customer_id'] ?? null,
                'party_name' => $line['party_name'] ?? null,
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

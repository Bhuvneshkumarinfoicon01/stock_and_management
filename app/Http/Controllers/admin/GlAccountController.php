<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\GlAccount;
use App\Models\JournalLine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GlAccountController extends Controller
{
    public function index(): View
    {
        $accounts = GlAccount::query()
            ->with('parent')
            ->orderBy('code')
            ->get();

        return view('admin.accounting.chart-of-accounts', compact('accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'parent_id' => $request->filled('parent_id') ? $request->input('parent_id') : null,
        ]);

        $validated = $request->validate([
            'code' => 'required|string|max:32|unique:gl_accounts,code',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|exists:gl_accounts,id',
            'description' => 'nullable|string|max:2000',
        ]);

        GlAccount::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'parent_id' => $validated['parent_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('admin.accounting.chart-of-accounts')->with('success', 'Account added to chart of accounts.');
    }

    public function update(Request $request, GlAccount $glAccount): RedirectResponse
    {
        $request->merge([
            'parent_id' => $request->filled('parent_id') ? $request->input('parent_id') : null,
        ]);

        $validated = $request->validate([
            'code' => 'required|string|max:32|unique:gl_accounts,code,' . $glAccount->id,
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|exists:gl_accounts,id|not_in:' . $glAccount->id,
            'description' => 'nullable|string|max:2000',
        ]);

        $hasPostedLines = JournalLine::query()
            ->where('gl_account_id', $glAccount->id)
            ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted'))
            ->exists();

        if ($hasPostedLines && $glAccount->type !== $validated['type']) {
            return back()->withErrors(['type' => 'Cannot change account type while posted journal lines exist for this account.'])->withInput();
        }

        $glAccount->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'parent_id' => $validated['parent_id'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.accounting.chart-of-accounts')->with('success', 'Account updated.');
    }

    public function toggle(GlAccount $glAccount): RedirectResponse
    {
        $glAccount->is_active = !$glAccount->is_active;
        $glAccount->save();

        return redirect()->route('admin.accounting.chart-of-accounts')->with('success', 'Account status updated.');
    }
}

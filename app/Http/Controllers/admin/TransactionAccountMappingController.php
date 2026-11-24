<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\GlAccount;
use App\Models\TransactionAccountMapping;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionAccountMappingController extends Controller
{
    /** @var array<string, string> */
    private const SOURCE_LABELS = [
        'sale_invoice' => 'Sale invoice',
        'purchase_invoice' => 'Purchase invoice',
        'credit_note' => 'Credit note',
        'debit_note' => 'Debit note',
        'customer_payment' => 'Customer receipt',
        'supplier_payment' => 'Supplier payment',
    ];

    public function index(): View
    {
        $mappings = TransactionAccountMapping::query()
            ->with(['debitAccount', 'creditAccount'])
            ->orderBy('source_type')
            ->get();

        $accounts = GlAccount::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('admin.accounting.transaction-account-mappings', [
            'mappings' => $mappings,
            'accounts' => $accounts,
            'sourceLabels' => self::SOURCE_LABELS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mappings' => 'required|array',
            'mappings.*.debit_account_id' => 'required|exists:gl_accounts,id',
            'mappings.*.credit_account_id' => 'required|exists:gl_accounts,id',
        ]);

        foreach ($validated['mappings'] as $sourceType => $row) {
            if ((int) $row['debit_account_id'] === (int) $row['credit_account_id']) {
                return back()->withErrors([
                    'mappings' => 'Debit and credit accounts must differ for ' . $sourceType . '.',
                ]);
            }
            TransactionAccountMapping::query()
                ->where('source_type', $sourceType)
                ->update([
                    'debit_account_id' => (int) $row['debit_account_id'],
                    'credit_account_id' => (int) $row['credit_account_id'],
                ]);
        }

        return back()->with('success', 'Transaction account mappings saved.');
    }
}

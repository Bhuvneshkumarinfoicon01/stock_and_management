<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\SupplierPurchaseInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SupplierLedgerController extends Controller
{
    public function index(Request $request): View
    {
        $suppliers = Supplier::query()->where('status', 1)->orderBy('company_name')->get();
        $supplierId = $request->integer('supplier_id');
        $transactions = new Collection();
        $supplier = null;

        if ($supplierId) {
            $supplier = Supplier::find($supplierId);
            if ($supplier) {
                $invoices = SupplierPurchaseInvoice::query()
                    ->where('supplier_id', $supplier->id)
                    ->orderBy('invoice_date')
                    ->orderBy('id')
                    ->get()
                    ->map(fn ($inv) => [
                        'date' => $inv->invoice_date,
                        'type' => 'Purchase invoice',
                        'reference' => $inv->internal_invoice_number ?: ($inv->invoice_number ?: ('PINV/' . $inv->id)),
                        'debit' => (float) $inv->total,
                        'credit' => 0.0,
                        'sort_key' => 'I-' . $inv->id,
                    ]);

                $payments = SupplierPayment::query()
                    ->where('supplier_id', $supplier->id)
                    ->orderBy('payment_date')
                    ->orderBy('id')
                    ->get()
                    ->map(fn ($p) => [
                        'date' => $p->payment_date,
                        'type' => 'Payment',
                        'reference' => $p->reference_no ?: ('PAY-' . $p->id),
                        'debit' => 0.0,
                        'credit' => (float) $p->amount,
                        'sort_key' => 'P-' . $p->id,
                    ]);

                $transactions = $invoices->concat($payments)->sortBy(function ($row) {
                    return $row['date']->format('Y-m-d') . $row['sort_key'];
                })->values();

                $balance = 0.0;
                $transactions = $transactions->map(function ($row) use (&$balance) {
                    $balance += $row['debit'] - $row['credit'];
                    $row['balance'] = round($balance, 2);

                    return $row;
                });
            }
        }

        return view('admin.suppliers.ledger', compact('suppliers', 'supplier', 'transactions', 'supplierId'));
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\AccountingService;
use App\Services\StoreSequentialNumberService;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\SupplierPurchaseInvoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupplierPaymentController extends Controller
{
    public function index(): View
    {
        $payments = SupplierPayment::query()
            ->with(['supplier', 'purchaseInvoice'])
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(25);

        return view('admin.suppliers.payments-index', compact('payments'));
    }

    public function create(): View
    {
        $suppliers = Supplier::query()->where('status', 1)->orderBy('company_name')->get();
        $openInvoices = SupplierPurchaseInvoice::query()
            ->with('supplier')
            ->where('due_amount', '>', 0)
            ->orderBy('invoice_date')
            ->get();

        return view('admin.suppliers.payments-create', compact('suppliers', 'openInvoices'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'supplier_purchase_invoice_id' => 'nullable|exists:supplier_purchase_invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:64',
            'reference_no' => 'nullable|string|max:128',
            'notes' => 'nullable|string|max:2000',
        ]);

        if (!empty($validated['supplier_purchase_invoice_id'])) {
            $invCheck = SupplierPurchaseInvoice::find($validated['supplier_purchase_invoice_id']);
            if (!$invCheck || (int) $invCheck->supplier_id !== (int) $validated['supplier_id']) {
                return back()->withErrors(['supplier_purchase_invoice_id' => 'The selected invoice must belong to the selected supplier.'])->withInput();
            }
        }

        $referenceNo = trim((string) ($validated['reference_no'] ?? ''));
        if ($referenceNo === '') {
            $storeId = 0;
            if (! empty($validated['supplier_purchase_invoice_id'])) {
                $storeId = (int) (SupplierPurchaseInvoice::find($validated['supplier_purchase_invoice_id'])?->store_id ?? 0);
            }
            if ($storeId < 1) {
                $storeId = (int) (session('selected_store_id') ?? 0);
            }

            $referenceNo = app(StoreSequentialNumberService::class)->issueNextForStore(
                $storeId,
                'receipt_prefix',
                'next_receipt_number',
                'RCPT',
                'supplier_payments',
                'reference_no'
            );
            // Old fallback when store sequence is unavailable.
            if ($referenceNo === null) {
                $referenceNo = 'RCPT' . time();
            }
        }
        $validated['reference_no'] = $referenceNo;

        DB::transaction(function () use ($validated) {
            $payment = SupplierPayment::create($validated);

            try {
                app(AccountingService::class)->postSupplierPayment($payment);
            } catch (\Throwable $e) {
                report($e);
            }

            if (!empty($validated['supplier_purchase_invoice_id'])) {
                $invoice = SupplierPurchaseInvoice::lockForUpdate()->find($validated['supplier_purchase_invoice_id']);
                if ($invoice && (int) $invoice->supplier_id === (int) $validated['supplier_id']) {
                    $paid = round((float) $invoice->paid_amount + (float) $validated['amount'], 2);
                    $total = (float) $invoice->total;
                    $paid = min($paid, $total);
                    $due = round(max(0, $total - $paid), 2);
                    $status = $due <= 0.005 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
                    $invoice->update([
                        'paid_amount' => $paid,
                        'due_amount' => $due,
                        'status' => $status,
                    ]);
                }
            }
        });

        return redirect()->route('admin.supplier-payments.index')->with('success', 'Payment recorded.');
    }
}

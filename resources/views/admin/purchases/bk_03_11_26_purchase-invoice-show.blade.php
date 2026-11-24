@extends('layouts.app_admin')
@section('title') Purchase invoice @endsection

@section('content')
@php
    $headerActions = [];
    if ($invoice->purchase_order_id) {
        $headerActions[] = [
            'url' => route('admin.purchase-orders.show', $invoice->purchase_order_id),
            'label' => 'View PO',
            'class' => 'btn-outline-secondary',
        ];
    }
@endphp

@component('admin.purchases.partials.document-show-layout', [
    'title' => 'Purchase invoice ' . ($invoice->internal_invoice_number ?: ('#' . $invoice->id)),
    'subtitle' => format_date($invoice->invoice_date),
    'backUrl' => route('admin.supplier-purchase-invoices.index'),
    'headerActions' => $headerActions,
])
    @if($invoice->invoice_status === 'Confirmed')
        <div class="mb-3">
            <button type="button" class="btn btn-outline-primary js-print-purchase-invoice">Print invoice</button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3"><strong>Supplier</strong><div>{{ $invoice->supplier->company_name ?? '—' }}</div></div>
                <div class="col-md-3"><strong>Store</strong><div>{{ $invoice->store->name ?? '—' }}</div></div>
                <div class="col-md-3"><strong>Supplier invoice #</strong><div>{{ $invoice->invoice_number ?: '—' }}</div></div>
                <div class="col-md-3"><strong>Status</strong><div>{{ $invoice->invoice_status }}</div></div>
                <div class="col-md-3"><strong>Due date</strong><div>{{ format_date($invoice->due_date) }}</div></div>
                <div class="col-md-3"><strong>Paid</strong><div>{{ number_format((float) $invoice->paid_amount, 2) }}</div></div>
                <div class="col-md-3"><strong>Due</strong><div>{{ number_format((float) $invoice->due_amount, 2) }}</div></div>
                @if($invoice->purchaseOrder)
                    <div class="col-md-3"><strong>Purchase order</strong><div><a href="{{ route('admin.purchase-orders.show', $invoice->purchaseOrder) }}">{{ $invoice->purchaseOrder->po_number }}</a></div></div>
                @endif
                @if($invoice->notes)
                    <div class="col-12"><strong>Notes</strong><div>{{ $invoice->notes }}</div></div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Line items</h5></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Unit price</th>
                        <th class="text-end">Tax</th>
                        <th class="text-end">Line total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-end">{{ $item->qty }}</td>
                            <td class="text-end">{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="text-end">
                                @if($item->is_taxable)
                                    {{ number_format((float) $item->tax_amount, 2) }} ({{ $item->tax_rate }}%)
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end">{{ number_format((float) $item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="4" class="text-end">Subtotal</td><td class="text-end">{{ number_format((float) $invoice->subtotal, 2) }}</td></tr>
                    <tr><td colspan="4" class="text-end">Tax</td><td class="text-end">{{ number_format((float) $invoice->tax_amount, 2) }}</td></tr>
                    <tr class="fw-bold"><td colspan="4" class="text-end">Total</td><td class="text-end">{{ number_format((float) $invoice->total, 2) }}</td></tr>
                </tfoot>
            </table>
        </div>
    </div>
@endcomponent

@include('admin.purchases.partials.purchase-invoice-print-modal')
@endsection

@section('scripts')
@include('admin.purchases.partials.purchase-invoice-print-scripts')
<script>
    $(function () {
        $('.js-print-purchase-invoice').on('click', function () {
            if (typeof window.openPurchaseInvoicePrint === 'function') {
                window.openPurchaseInvoicePrint({{ (int) $invoice->id }});
            }
        });
    });
</script>
@endsection

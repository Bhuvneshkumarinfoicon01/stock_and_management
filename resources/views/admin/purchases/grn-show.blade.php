@extends('layouts.app_admin')
@section('title') GRN {{ $grn->grn_number }} @endsection

@section('content')
@php
    $headerActions = [];
    if ($grn->purchaseOrder) {
        $headerActions[] = [
            'url' => route('admin.purchase-orders.show', $grn->purchaseOrder),
            'label' => 'View PO',
            'class' => 'btn-outline-secondary',
        ];
    }
    if ($grn->supplierPurchaseInvoice) {
        $headerActions[] = [
            'url' => route('admin.supplier-purchase-invoices.show', $grn->supplierPurchaseInvoice),
            'label' => 'View invoice',
            'class' => 'btn-outline-primary',
        ];
    }
@endphp

@component('admin.purchases.partials.document-show-layout', [
    'title' => 'GRN ' . $grn->grn_number,
    'subtitle' => format_date($grn->grn_date),
    'backUrl' => route('admin.goods-receipt-notes.index'),
    'headerActions' => $headerActions,
    'recordNav' => $recordNav ?? null,
])
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3"><strong>Supplier</strong><div>{{ $grn->supplier->company_name ?? '—' }}</div></div>
                <div class="col-md-3"><strong>Store</strong><div>{{ $grn->store->name ?? '—' }}</div></div>
                <div class="col-md-3"><strong>Status</strong><div>{{ ucfirst($grn->status) }}</div></div>
                <div class="col-md-3"><strong>Total</strong><div>{{ number_format((float) $grn->total, 2) }}</div></div>
                @if($grn->purchaseOrder)
                    <div class="col-md-3"><strong>Purchase order</strong><div><a href="{{ route('admin.purchase-orders.show', $grn->purchaseOrder) }}">{{ $grn->purchaseOrder->po_number }}</a></div></div>
                @endif
                @if($grn->supplierPurchaseInvoice)
                    <div class="col-md-3"><strong>Purchase invoice</strong><div><a href="{{ route('admin.supplier-purchase-invoices.show', $grn->supplierPurchaseInvoice) }}">{{ $grn->supplierPurchaseInvoice->internal_invoice_number ?: $grn->supplierPurchaseInvoice->invoice_number }}</a></div></div>
                @endif
                @if($grn->notes)
                    <div class="col-12"><strong>Notes</strong><div>{{ $grn->notes }}</div></div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Received lines</h5></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-end">Qty received</th>
                        <th class="text-end">Unit cost</th>
                        <th class="text-end">Line total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grn->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-end">{{ $item->qty_received }}</td>
                            <td class="text-end">{{ number_format((float) $item->unit_cost, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end">{{ number_format((float) $grn->subtotal, 2) }}</td></tr>
                    <tr><td colspan="3" class="text-end">Tax</td><td class="text-end">{{ number_format((float) $grn->tax_amount, 2) }}</td></tr>
                    <tr class="fw-bold"><td colspan="3" class="text-end">Total</td><td class="text-end">{{ number_format((float) $grn->total, 2) }}</td></tr>
                </tfoot>
            </table>
        </div>
    </div>
@endcomponent
@endsection

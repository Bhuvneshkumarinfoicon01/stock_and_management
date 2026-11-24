@extends('layouts.app_admin')
@section('title') Purchase order {{ $purchaseOrder->po_number }} @endsection

@section('content')
@php
    $latestInvoice = $purchaseOrder->latestPurchaseInvoice();
    $receivedGrn = $purchaseOrder->latestReceivedGrn();
    $headerActions = [];

    if ($purchaseOrder->hasPurchaseInvoice() && $latestInvoice) {
        $headerActions[] = [
            'url' => route('admin.supplier-purchase-invoices.show', $latestInvoice),
            'label' => 'View invoice',
            'class' => 'btn-outline-primary',
        ];
    } else {
        $headerActions[] = [
            'url' => route('admin.supplier-purchase-invoices.create', ['purchase_order_id' => $purchaseOrder->id]),
            'label' => 'Create invoice',
            'class' => 'btn-primary',
        ];
    }

    if ($receivedGrn) {
        $headerActions[] = [
            'url' => route('admin.goods-receipt-notes.show', $receivedGrn),
            'label' => 'View GRN',
            'class' => 'btn-outline-secondary',
        ];
    } elseif ($purchaseOrder->canCreateGrn()) {
        $headerActions[] = [
            'url' => route('admin.goods-receipt-notes.create', ['purchase_order_id' => $purchaseOrder->id]),
            'label' => 'Create GRN',
            'class' => 'btn-outline-secondary',
        ];
    }
@endphp

@component('admin.purchases.partials.document-show-layout', [
    'title' => 'Purchase order ' . $purchaseOrder->po_number,
    'subtitle' => 'Created ' . format_date($purchaseOrder->order_date),
    'backUrl' => route('admin.purchase-orders.index'),
    'headerActions' => $headerActions,
])
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3"><strong>Supplier</strong><div>{{ $purchaseOrder->supplier->company_name ?? '—' }}</div></div>
                <div class="col-md-3"><strong>Store</strong><div>{{ $purchaseOrder->store->name ?? '—' }}</div></div>
                <div class="col-md-3"><strong>Status</strong><div>{{ \App\Models\PurchaseOrder::statusLabel($purchaseOrder->status) }}</div></div>
                <div class="col-md-3"><strong>Total</strong><div>{{ number_format((float) $purchaseOrder->total, 2) }}</div></div>
                @if($purchaseOrder->notes)
                    <div class="col-12"><strong>Notes</strong><div>{{ $purchaseOrder->notes }}</div></div>
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
                    @foreach($purchaseOrder->items as $item)
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
                    <tr><td colspan="4" class="text-end">Subtotal</td><td class="text-end">{{ number_format((float) $purchaseOrder->subtotal, 2) }}</td></tr>
                    <tr><td colspan="4" class="text-end">Tax</td><td class="text-end">{{ number_format((float) $purchaseOrder->tax_amount, 2) }}</td></tr>
                    <tr class="fw-bold"><td colspan="4" class="text-end">Total</td><td class="text-end">{{ number_format((float) $purchaseOrder->total, 2) }}</td></tr>
                </tfoot>
            </table>
        </div>
    </div>
@endcomponent
@endsection

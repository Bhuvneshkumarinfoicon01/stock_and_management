@extends('layouts.app_admin')
@section('title') GRN @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title"><h4>Goods receipt notes (GRN)</h4></div>
            <a href="{{ route('admin.goods-receipt-notes.create') }}" class="btn btn-primary">New GRN</a>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card"><div class="card-body table-responsive">
            @include('admin.purchases.partials.list-pagination', ['paginator' => $grns, 'showLinks' => false, 'showSummary' => false, 'containerClass' => 'mb-3'])
            <table class="table table-striped">
                <thead><tr><th>GRN #</th><th>Date</th><th>Supplier</th><th>PO</th><th>Purchase invoice</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($grns as $g)
                        <tr>
                            <td><a href="{{ route('admin.goods-receipt-notes.show', $g) }}">{{ $g->grn_number }}</a></td>
                            <td>{{ format_date($g->grn_date) }}</td>
                            <td>{{ $g->supplier->company_name ?? '—' }}</td>
                            <td>
                                @if($g->purchaseOrder)
                                    <a href="{{ route('admin.purchase-orders.show', $g->purchaseOrder) }}">{{ $g->purchaseOrder->po_number }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($g->supplierPurchaseInvoice)
                                    <a href="{{ route('admin.supplier-purchase-invoices.show', $g->supplierPurchaseInvoice) }}">
                                        {{ $g->supplierPurchaseInvoice->internal_invoice_number ?: ($g->supplierPurchaseInvoice->invoice_number ?: ('#'.$g->supplierPurchaseInvoice->id)) }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $g->status }}</td>
                            <td>
                                <a href="{{ route('admin.goods-receipt-notes.show', $g) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No GRNs.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @include('admin.purchases.partials.list-pagination', ['paginator' => $grns, 'showPerPage' => false])
        </div></div>
    </div>
</div>
@endsection

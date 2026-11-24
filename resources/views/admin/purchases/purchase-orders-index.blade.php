@extends('layouts.app_admin')
@section('title') Purchase orders @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title"><h4>Purchase orders</h4></div>
            <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary">Purchase order</a>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card"><div class="card-body table-responsive">
            @include('admin.purchases.partials.list-pagination', ['paginator' => $orders, 'showLinks' => false, 'showSummary' => false, 'containerClass' => 'mb-3'])
            <table class="table table-striped">
                <thead><tr><th>PO #</th><th>Date</th><th>Supplier</th><th>Total</th><th>Invoices</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($orders as $o)
                        @php
                            $confirmedInvoices = $o->purchaseInvoices->where('invoice_status', 'Confirmed');
                            $hasConfirmedInvoice = $confirmedInvoices->isNotEmpty();
                            $latestInvoice = $o->latestPurchaseInvoice();
                            $receivedGrn = $o->latestReceivedGrn();
                        @endphp
                        <tr>
                            <td><a href="{{ route('admin.purchase-orders.show', $o) }}">{{ $o->po_number }}</a></td>
                            <td>{{ format_date($o->order_date) }}</td>
                            <td>{{ $o->supplier->company_name ?? '—' }}</td>
                            <td>{{ number_format((float)$o->total, 2) }}</td>
                            <td>
                                @if($hasConfirmedInvoice)
                                    <span class="badge bg-success">{{ $confirmedInvoices->count() }} confirmed</span>
                                @elseif($o->hasPurchaseInvoice())
                                    <span class="badge bg-info text-dark">Draft invoice</span>
                                @else
                                    <span class="badge bg-warning text-dark">No invoice</span>
                                @endif
                            </td>
                            <td>{{ \App\Models\PurchaseOrder::statusLabel($o->status) }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="{{ route('admin.purchase-orders.show', $o) }}" class="btn btn-sm btn-outline-secondary" title="View purchase order">
                                        View PO
                                    </a>

                                    @if($latestInvoice)
                                        <a href="{{ route('admin.supplier-purchase-invoices.show', $latestInvoice) }}" class="btn btn-sm btn-outline-primary" title="View purchase invoice">
                                            View invoice
                                        </a>
                                    @else
                                        {{-- Old: always showed Create invoice even when one existed.
                                        <a href="{{ route('admin.supplier-purchase-invoices.create', ['purchase_order_id' => $o->id]) }}" class="btn btn-sm btn-outline-primary">
                                            Create invoice
                                        </a>
                                        --}}
                                        <a href="{{ route('admin.supplier-purchase-invoices.create', ['purchase_order_id' => $o->id]) }}" class="btn btn-sm btn-outline-primary" title="Create purchase invoice from PO">
                                            Create invoice
                                        </a>
                                    @endif

                                    @if($receivedGrn)
                                        <a href="{{ route('admin.goods-receipt-notes.show', $receivedGrn) }}" class="btn btn-sm btn-outline-secondary" title="View goods receipt note">
                                            View GRN
                                        </a>
                                    @elseif($o->canCreateGrn())
                                        {{-- Old: showed GRN whenever any confirmed invoice existed.
                                        @if($hasConfirmedInvoice)
                                            <a href="{{ route('admin.goods-receipt-notes.create', ['purchase_order_id' => $o->id]) }}" class="btn btn-sm btn-outline-secondary">
                                                GRN
                                            </a>
                                        @endif
                                        --}}
                                        <a href="{{ route('admin.goods-receipt-notes.create', ['purchase_order_id' => $o->id]) }}" class="btn btn-sm btn-outline-secondary" title="Create goods receipt note">
                                            Create GRN
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No purchase orders.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @include('admin.purchases.partials.list-pagination', ['paginator' => $orders, 'showPerPage' => false])
        </div></div>
    </div>
</div>
@endsection

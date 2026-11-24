@extends('layouts.app_admin')
@section('title') Store purchase {{ $storePurchase->purchase_number }} @endsection

@section('content')
@component('admin.purchases.partials.document-show-layout', [
    'title' => 'Store purchase ' . $storePurchase->purchase_number,
    'subtitle' => format_date($storePurchase->purchase_date),
    'backUrl' => route('admin.store-purchases.index'),
    'headerActions' => [],
])
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3"><strong>Supplier</strong><div>{{ $storePurchase->supplier->company_name ?? '—' }}</div></div>
                <div class="col-md-3"><strong>Store</strong><div>{{ $storePurchase->store->name ?? '—' }}</div></div>
                <div class="col-md-3"><strong>Status</strong><div>{{ ucfirst($storePurchase->status) }}</div></div>
                <div class="col-md-3"><strong>Total</strong><div>{{ number_format((float) $storePurchase->total, 2) }}</div></div>
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
                        <th class="text-end">Line total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($storePurchase->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-end">{{ $item->qty }}</td>
                            <td class="text-end">{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endcomponent
@endsection

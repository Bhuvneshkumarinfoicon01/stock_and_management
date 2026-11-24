@extends('layouts.app_admin')
@section('title') Batch purchase {{ $batch->batch_number }} @endsection

@section('content')
@component('admin.purchases.partials.document-show-layout', [
    'title' => 'Batch purchase ' . $batch->batch_number,
    'subtitle' => format_date($batch->batch_date),
    'backUrl' => route('admin.batch-purchases.index'),
    'headerActions' => [],
])
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3"><strong>Store</strong><div>{{ $batch->store->name ?? '—' }}</div></div>
                <div class="col-md-3"><strong>Status</strong><div>{{ ucfirst($batch->status) }}</div></div>
                <div class="col-md-3"><strong>Total</strong><div>{{ number_format((float) $batch->total, 2) }}</div></div>
                @if($batch->notes)
                    <div class="col-12"><strong>Notes</strong><div>{{ $batch->notes }}</div></div>
                @endif
            </div>
        </div>
    </div>

    @if($batch->productInvoices->isNotEmpty())
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">Product invoices</h5></div>
            <div class="card-body">
                <ul class="mb-0">
                    @foreach($batch->productInvoices as $invoice)
                        <li><a href="{{ route('admin.supplier-purchase-invoices.show', $invoice) }}">{{ $invoice->internal_invoice_number ?: $invoice->invoice_number ?: ('#' . $invoice->id) }}</a> — {{ $invoice->supplier->company_name ?? '' }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Consolidated lines</h5></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Unit price</th>
                        <th class="text-end">Additional charges</th>
                        <th class="text-end">Final unit</th>
                        <th class="text-end">Grand total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batch->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->sku ?: '—' }}</td>
                            <td class="text-end">{{ $item->qty }}</td>
                            <td class="text-end">{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $item->additional_charges, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $item->final_unit, 4) }}</td>
                            <td class="text-end">{{ number_format((float) $item->grand_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-end">Sum of grand totals</th>
                        <th class="text-end">{{ number_format((float) $batch->items->sum('grand_total'), 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endcomponent
@endsection

@extends('layouts.app_admin')
@section('title') New purchase order @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title">
                <h4>New purchase order</h4>
                <h6>Select store, search products, then save.</h6>
            </div>
            <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <div class="card"><div class="card-body">
            <form method="post" action="{{ route('admin.purchase-orders.store') }}" id="po-form">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Supplier *</label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">Choose supplier</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" @selected(old('supplier_id')==$s->id)>{{ $s->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Store *</label>
                        <select name="store_id" id="purchase_store_id" class="form-select" required>
                            <option value="">Choose store</option>
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}" @selected(old('store_id', $selectedStoreId)==$st->id)>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Order date *</label>
                        <input type="date" name="order_date" class="form-control" required value="{{ old('order_date', now()->toDateString()) }}">
                    </div>
                    {{--<div class="col-md-4">
                        <label class="form-label">Order tax</label>
                        <select name="tax_percent" class="form-select">
                            @foreach([0,5,10,15,20,25,30] as $t)
                                <option value="{{ $t }}" @selected(old('tax_percent',0)==$t)>{{ $t }}%</option>
                            @endforeach
                        </select>
                    </div>--}}
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>
                @include('admin.purchases.partials.product-lines-picker', [
                    'pickerPrefix' => 'po',
                    'perLineTax' => true,
                    'taxExemptDefault' => (bool) old('tax_exempt', false),
                ])
                <button type="submit" class="btn btn-primary mt-3" id="po-submit">Save PO</button>
            </form>
        </div></div>
    </div>
</div>
@endsection
@section('scripts')
<script>
window.PerLineTaxConfig = {
    taxExemptSelector: '#po_tax_exempt',
    tbodySelector: '#po_product_tbody',
    subTotalSelector: '#po_sub_total',
    taxSelector: '#po_tax',
    taxLabelSelector: '#po_tax_label',
    totalSelector: '#po_total',
};
</script>
@include('admin.partials.invoice-per-line-tax-scripts')
@include('admin.purchases.partials.product-lines-picker-scripts', [
    'pickerPrefix' => 'po',
    'storeSelectId' => 'purchase_store_id',
    'formId' => 'po-form',
    'submitBtnId' => 'po-submit',
    'emptyMessage' => 'Add at least one product before saving.',
    'perLineTax' => true,
])
@endsection

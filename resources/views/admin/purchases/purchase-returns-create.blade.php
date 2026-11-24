@extends('layouts.app_admin')
@section('title') New purchase return @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title"><h4>New purchase return</h4><h6>Select store, search products, then save.</h6></div>
            <a href="{{ route('admin.purchase-returns.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <div class="card"><div class="card-body">
            <form method="post" action="{{ route('admin.purchase-returns.store') }}" id="pr-form">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-4"><label class="form-label">Supplier *</label>
                        <select name="supplier_id" class="form-select" required><option value="">Choose supplier</option>@foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id')==$s->id)>{{ $s->company_name }}</option>@endforeach</select></div>
                    <div class="col-md-4"><label class="form-label">Store *</label>
                        <select name="store_id" id="purchase_store_id" class="form-select" required><option value="">Choose store</option>@foreach($stores as $st)<option value="{{ $st->id }}" @selected(old('store_id', $selectedStoreId)==$st->id)>{{ $st->name }}</option>@endforeach</select></div>
                    <div class="col-md-4"><label class="form-label">Related invoice</label>
                        <select name="supplier_purchase_invoice_id" class="form-select"><option value="">—</option>@foreach($invoices as $inv)<option value="{{ $inv->id }}" @selected(old('supplier_purchase_invoice_id')==$inv->id)>{{ $inv->internal_invoice_number ?: ($inv->invoice_number ?: ('#' . $inv->id)) }}</option>@endforeach</select></div>
                    <div class="col-md-4"><label class="form-label">Return date *</label><input type="date" name="return_date" class="form-control" required value="{{ old('return_date', now()->toDateString()) }}"></div>
                    <div class="col-12"><label class="form-label">Reason</label><textarea name="reason" class="form-control" rows="2">{{ old('reason') }}</textarea></div>
                </div>
                @include('admin.purchases.partials.product-lines-picker', [
                    'pickerPrefix' => 'pr',
                    'perLineTax' => true,
                    'taxExemptDefault' => (bool) old('tax_exempt', false),
                ])
                <button type="submit" class="btn btn-primary mt-3" id="pr-submit">Save return</button>
            </form>
        </div></div>
    </div>
</div>
@endsection
@section('scripts')
<script>
window.PerLineTaxConfig = {
    taxExemptSelector: '#pr_tax_exempt',
    tbodySelector: '#pr_product_tbody',
    subTotalSelector: '#pr_sub_total',
    taxSelector: '#pr_tax',
    taxLabelSelector: '#pr_tax_label',
    totalSelector: '#pr_total',
};
</script>
@include('admin.partials.invoice-per-line-tax-scripts')
@include('admin.purchases.partials.product-lines-picker-scripts', [
    'pickerPrefix' => 'pr',
    'storeSelectId' => 'purchase_store_id',
    'formId' => 'pr-form',
    'submitBtnId' => 'pr-submit',
    'emptyMessage' => 'Add at least one product before saving.',
    'perLineTax' => true,
])
@endsection

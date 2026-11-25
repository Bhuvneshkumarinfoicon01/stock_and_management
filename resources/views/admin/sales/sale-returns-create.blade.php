@extends('layouts.app_admin')
@section('title') New sale return @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title"><h4>New sale return</h4><h6>Choose the customer and store, then add returned products.</h6></div>
            <a href="{{ route('admin.sale-returns.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <div class="card"><div class="card-body">
            <form method="post" action="{{ route('admin.sale-returns.store') }}" id="sale-return-form">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Customer *</label>
                        <select name="customer_id" id="sale_return_customer_id" class="form-select" required>
                            <option value="">Choose customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Store *</label>
                        <select name="store_id" id="sale_return_store_id" class="form-select" required>
                            <option value="">Choose store</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" @selected(old('store_id', $selectedStoreId) == $store->id)>{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Related sales invoice</label>
                        <select name="order_id" id="sale_return_order_id" class="form-select">
                            <option value="">—</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}" data-customer="{{ $order->customer_id }}" data-store="{{ $order->store_id }}"
                                    @selected(old('order_id') == $order->id)>
                                    {{ $order->invoice_number ?: ('INV/' . $order->id) }} — {{ $order->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Return date *</label>
                        <input type="date" name="return_date" class="form-control" required value="{{ old('return_date', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Settlement *</label>
                        <select name="settlement_method" class="form-select" required>
                            <option value="cash_refund" @selected(old('settlement_method', 'cash_refund') === 'cash_refund')>Refund cash</option>
                            <option value="customer_balance" @selected(old('settlement_method') === 'customer_balance')>Credit customer balance</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="2">{{ old('reason') }}</textarea>
                    </div>
                </div>
                @include('admin.purchases.partials.product-lines-picker', [
                    'pickerPrefix' => 'sale_return',
                    'priceColumnLabel' => 'Unit Selling Price',
                    'perLineTax' => true,
                    'taxExemptDefault' => (bool) old('tax_exempt', false),
                ])
                <button type="submit" class="btn btn-primary mt-3" id="sale-return-submit">Save sale return</button>
            </form>
        </div></div>
    </div>
</div>
@endsection
@section('scripts')
<script>
window.PerLineTaxConfig = {
    taxExemptSelector: '#sale_return_tax_exempt',
    tbodySelector: '#sale_return_product_tbody',
    subTotalSelector: '#sale_return_sub_total',
    taxSelector: '#sale_return_tax',
    taxLabelSelector: '#sale_return_tax_label',
    totalSelector: '#sale_return_total',
};

$(function () {
    $('#sale_return_order_id').on('change', function () {
        const $option = $(this).find(':selected');
        if ($option.val()) {
            $('#sale_return_customer_id').val(String($option.data('customer')));
            if ($option.data('store')) {
                $('#sale_return_store_id').val(String($option.data('store'))).trigger('change');
            }
        }
    });
    $('#sale_return_order_id').trigger('change');
});
</script>
@include('admin.partials.invoice-per-line-tax-scripts')
@include('admin.purchases.partials.product-lines-picker-scripts', [
    'pickerPrefix' => 'sale_return',
    'storeSelectId' => 'sale_return_store_id',
    'formId' => 'sale-return-form',
    'submitBtnId' => 'sale-return-submit',
    'emptyMessage' => 'Add at least one product before saving.',
    'perLineTax' => true,
    'priceDataField' => 'price',
])
@endsection

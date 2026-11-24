@extends('layouts.app_admin')
@section('title') Add purchase @endsection

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title">
                <h4>Add purchase</h4>
                <h6>Select a store, search products from stock, then save.</h6>
            </div>
            <a href="{{ route('admin.store-purchases.index') }}" class="btn btn-outline-secondary">Back to list</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('admin.store-purchases.store') }}" id="store-purchase-form">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select select" required>
                                <option value="">Choose supplier</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>{{ $s->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label">Store <span class="text-danger">*</span></label>
                            <select name="store_id" id="purchase_store_id" class="form-select select" required>
                                <option value="">Choose store</option>
                                @foreach($stores as $st)
                                    <option value="{{ $st->id }}" @selected(old('store_id', $selectedStoreId) == $st->id)>{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label">Purchase date <span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" class="form-control" required value="{{ old('purchase_date', now()->toDateString()) }}">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label">Order tax</label>
                            <select name="tax_percent" class="form-select select">
                                @foreach([0, 5, 10, 15, 20, 25, 30] as $t)
                                    <option value="{{ $t }}" @selected(old('tax_percent', 0) == $t)>{{ $t }}%</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    @include('admin.purchases.partials.product-lines-picker', ['pickerPrefix' => 'spur'])

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary" id="store-purchase-submit">Save purchase</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.purchases.partials.product-lines-picker-scripts', [
    'pickerPrefix' => 'spur',
    'storeSelectId' => 'purchase_store_id',
    'formId' => 'store-purchase-form',
    'submitBtnId' => 'store-purchase-submit',
    'emptyMessage' => 'Add at least one product before saving the purchase.',
])
@endsection

@extends('layouts.app_admin')

@section('title')
Store Markup & Selling Prices
@endsection

@section('content')
@php
    $storeMarkups = $storeMarkups ?? [];
@endphp

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Store markup &amp; selling prices</h4>
                    <h6>Manage per-store markup and selling prices for a product</h6>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.stock.store-markups') }}" class="row g-3 align-items-end">
                    <div class="col-lg-8 col-md-7">
                        <label class="form-label">Product</label>
                        <select name="product_id" id="markup_product_id" class="form-control select2-product" required>
                            <option value="">Choose product...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->product_id }}"
                                    {{ (string) $selectedProductId === (string) $product->product_id ? 'selected' : '' }}>
                                    {{ $product->product_name }} ({{ $product->product_id }} / {{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-5">
                        <button type="submit" class="btn btn-primary">Load product</button>
                        <a href="{{ route('admin.stock.store-markups') }}" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedProductId && $selectedProduct)
            <form method="POST" action="{{ route('admin.stock.store-markups.update') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $selectedProductId }}">
                <input type="hidden" id="markup_product_cost" value="{{ number_format($productCost, 2, '.', '') }}">

                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h5 class="mb-1">{{ $selectedProduct->product_name }}</h5>
                            <small class="text-muted">Product ID: {{ $selectedProduct->product_id }} &middot; SKU: {{ $selectedProduct->sku }}</small>
                        </div>
                        <div class="text-md-end">
                            <label class="form-label mb-1">Cost</label>
                            <div class="fw-semibold">{{ number_format($productCost, 2) }}</div>
                            <small class="text-muted">Computed from stock transactions (see product form).</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">Filter stores</label>
                                <input type="text" id="store-markup-filter" class="form-control" placeholder="Search store name...">
                            </div>
                        </div>

                        <div class="store-markup-scroll border rounded p-3 bg-light" id="store-markup-scroll">
                            @include('admin.partials.product-store-markup-fields', [
                                'stores' => $stores,
                                'storeMarkups' => $storeMarkups,
                                'storeStockCosts' => $storeStockCosts ?? [],
                                'productCost' => $productCost,
                            ])
                        </div>

                        <div class="mt-3">
                            @if(hasPermission('update', 'stock'))
                                <button type="submit" class="btn btn-submit">Save markup &amp; prices</button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        @else
            <div class="card">
                <div class="card-body text-center text-muted py-5">
                    Select a product above to manage store markup and selling prices.
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: @json(session('success')),
            confirmButtonText: 'OK',
        });
    });
</script>
@endif
@include('admin.partials.product-store-markup-scripts')
<script>
$(document).ready(function () {
    $('.select2-product').select2({
        width: '100%',
        placeholder: 'Choose product...',
    });

    $('#store-markup-filter').on('input', function () {
        const term = $(this).val().toLowerCase().trim();
        $('.store-markup-row').each(function () {
            const storeName = $(this).find('.form-label').first().text().toLowerCase();
            $(this).toggle(term === '' || storeName.includes(term));
        });
    });
});
</script>
<style>
    .store-markup-scroll {
        max-height: 65vh;
        overflow-y: auto;
    }
</style>
@endsection

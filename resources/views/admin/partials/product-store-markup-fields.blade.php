@php
    $stores = $stores ?? collect();
    $storeMarkups = $storeMarkups ?? [];
    $storeStockCosts = $storeStockCosts ?? [];
    $productCost = $productCost ?? old('cost', isset($stock) ? $stock->cost : '');
@endphp

<div class="border rounded p-3 mb-3 bg-light" id="product-store-markup-section">
    <h6 class="mb-2">Store markup &amp; selling prices</h6>
    <p class="text-muted small mb-3">
        Set a markup percentage per store. Selling price is calculated as <strong>Cost + (Cost × Markup %)</strong>.
        Cost is computed automatically from stock transactions.
    </p>

    @if($stores->isNotEmpty())
        <div class="form-check mb-3">
            <input type="checkbox"
                class="form-check-input"
                id="unified_store_markup"
                value="1"
                {{ old('unified_store_markup') ? 'checked' : '' }}>
            <label class="form-check-label" for="unified_store_markup">
                Use the same markup as the first store for all stores
            </label>
            <p class="small text-muted mb-0">When checked, other store markup fields are locked to match the first store. Unchecking unlocks them without clearing values.</p>
        </div>
    @endif

    @foreach($stores as $store)
        @php
            $markupValue = old('store_markups.' . $store->id, $storeMarkups[$store->id] ?? 0);
            $rowCost = (float) ($storeStockCosts[$store->id] ?? ($productCost !== '' ? $productCost : 0));
            $computedPrice = \App\Models\ProductStoreMarkup::priceFromCost(
                $rowCost,
                (float) $markupValue
            );
            $isFirstStore = $loop->first;
        @endphp
        <div class="row g-2 mb-2 align-items-end store-markup-row {{ $isFirstStore ? 'store-markup-row-first' : 'store-markup-row-follower' }}"
            data-store-id="{{ $store->id }}"
            data-store-cost="{{ number_format($rowCost, 2, '.', '') }}">
            <div class="col-md-4">
                <label class="form-label mb-1">{{ $store->name }}</label>
                <div class="form-control bg-white" style="min-height: 38px;">{{ $store->name }}</div>
            </div>
            <div class="col-md-4">
                <label class="form-label mb-1">Markup % @if($isFirstStore)<span class="text-muted small">(first store)</span>@endif</label>
                <input type="number"
                    step="0.01"
                    min="0"
                    name="store_markups[{{ $store->id }}]"
                    class="form-control store-markup-input {{ $isFirstStore ? 'store-markup-input-first' : 'store-markup-input-follower' }}"
                    value="{{ $markupValue }}"
                    placeholder="e.g. 25">
            </div>
            <div class="col-md-4">
                <label class="form-label mb-1">Selling price</label>
                <input type="text"
                    class="form-control store-computed-price bg-white"
                    readonly
                    value="{{ number_format($computedPrice, 2, '.', '') }}">
            </div>
        </div>
    @endforeach
</div>

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductStoreMarkup;
use App\Models\Stock;
use App\Models\Store;
use App\Services\ProductUnitCostService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductStoreMarkupController extends Controller
{
    public function index(Request $request): View
    {
        if (! hasPermission('update', 'stock') && ! hasPermission('create', 'stock')) {
            return view('admin.partials.authorisation');
        }

        $products = Stock::query()
            ->orderBy('product_name')
            ->get()
            ->unique('product_id')
            ->values();

        $stores = Store::orderBy('name')->get();

        $selectedProductId = $request->input('product_id');
        $storeMarkups = [];
        $storeStockCosts = [];
        $productCost = 0;
        $selectedProduct = null;

        if ($selectedProductId) {
            $selectedProduct = $products->firstWhere('product_id', $selectedProductId);
            app(ProductUnitCostService::class)->syncForProduct($selectedProductId);

            $referenceStock = Stock::query()
                ->where('product_id', $selectedProductId)
                ->orderBy('id')
                ->first();

            if ($referenceStock) {
                $costService = app(ProductUnitCostService::class);
                $productCost = $costService->unitCostForStock($referenceStock->fresh());
                $storeMarkups = ProductStoreMarkup::query()
                    ->where('product_id', $selectedProductId)
                    ->pluck('markup_percentage', 'store_id')
                    ->map(fn ($value) => (float) $value)
                    ->all();

                foreach (Stock::query()->where('product_id', $selectedProductId)->get() as $stockRow) {
                    $storeStockCosts[(int) $stockRow->store_id] = $costService->unitCostForStock($stockRow->fresh());
                }
            } else {
                $selectedProductId = null;
                $selectedProduct = null;
            }
        }

        return view('admin.product-store-markups', compact(
            'products',
            'stores',
            'selectedProductId',
            'storeMarkups',
            'storeStockCosts',
            'productCost',
            'selectedProduct'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        if (! hasPermission('update', 'stock')) {
            return redirect()->back()->withErrors(['stock' => 'Unauthorized access.']);
        }

        $request->validate([
            'product_id' => 'required|string',
            // Point 18 — cost is system-calculated; not submitted from this form.
            // 'cost' => 'required|numeric|min:0',
            'store_markups' => 'nullable|array',
            'store_markups.*' => 'nullable|numeric|min:0|max:1000',
        ]);

        $productId = $request->input('product_id');
        $markups = $this->normalizeStoreMarkups($request->input('store_markups', []));

        ProductStoreMarkup::syncForProduct($productId, $markups);

        return redirect()
            ->route('admin.stock.store-markups', ['product_id' => $productId])
            ->with('success', 'Store markup and selling prices updated successfully.');
    }

    private function normalizeStoreMarkups(array $markups): array
    {
        $normalized = [];

        foreach ($markups as $storeId => $markup) {
            if ($markup === null || $markup === '') {
                continue;
            }

            $normalized[(int) $storeId] = round((float) $markup, 2);
        }

        return $normalized;
    }
}

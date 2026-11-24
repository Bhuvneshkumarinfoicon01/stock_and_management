<?php

namespace App\Http\Controllers\admin\Concerns;

use App\Models\ProductStoreMarkup;
use App\Models\Stock;
use App\Services\ProductUnitCostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

trait ReceivesPurchaseIntoStock
{
    /**
     * Increase store stock when goods are received (GRN).
     *
     * @param  array<int, array{product_id: ?string, product_name: string, qty: int, unit_price: float, line_total: float}>  $lines
     */
    protected function receiveLinesIntoStock(
        int $storeId,
        array $lines,
        Request $request,
        ?string $supplierName,
        string $receiptDate,
    ): void {
        $metaByProduct = $this->productMetaFromRequest($request);

        foreach ($lines as $line) {
            $productId = isset($line['product_id']) && $line['product_id'] !== ''
                ? (string) $line['product_id']
                : null;

            if ($productId === null) {
                continue;
            }

            $qty = max(1, (int) ($line['qty'] ?? 1));
            $unitCost = round((float) ($line['unit_price'] ?? 0), 2);
            $meta = $metaByProduct[$productId] ?? ['sku' => '', 'category_id' => null];

            $stock = Stock::query()
                ->where('store_id', $storeId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            if ($stock) {
                $oldQty = (int) ($stock->quantity ?? 0);
                $newQty = $oldQty + $qty;
                $oldAvg = (float) ($stock->average_cost ?? $stock->last_purchase_price ?? $stock->price ?? 0);
                $newAvg = $newQty > 0
                    ? round((($oldQty * $oldAvg) + ($qty * $unitCost)) / $newQty, 2)
                    : $unitCost;

                $stock->increment('quantity', $qty);

                DB::table('stocks')->where('id', $stock->id)->update([
                    'last_purchase_price' => $unitCost,
                    'average_cost' => $newAvg,
                    'total_stock' => $newQty,
                    'purchase_date' => $receiptDate,
                    'supplier_name' => $supplierName ?? $stock->supplier_name,
                    'updated_at' => now(),
                ]);

                // app(\App\Services\ProductUnitCostService::class)->syncStock($stock->fresh());
                $this->syncCostAndSellingPrice($stock->fresh());
            } else {
                $template = Stock::query()->where('product_id', $productId)->first();
                $categoryId = (int) ($meta['category_id'] ?: ($template?->category_id ?? 0));

                if ($categoryId <= 0) {
                    continue;
                }

                $sellingPrice = (float) ($template?->selling_price ?? $unitCost);
                $price = (float) ($template?->price ?? $unitCost);

                Stock::create([
                    'store_id' => $storeId,
                    'product_id' => $productId,
                    'product_name' => $line['product_name'],
                    'sku' => $meta['sku'] ?: ($template?->sku ?? ''),
                    'category_id' => $categoryId,
                    'unit_id' => $template?->unit_id,
                    'supplier_name' => $supplierName,
                    'purchase_date' => $receiptDate,
                    'description' => $template?->description,
                    'quantity' => $qty,
                    'price' => $price,
                    'discount' => $template?->discount ?? 0,
                    'tax' => $template?->tax ?? 0,
                    'manufactured_date' => $template?->manufactured_date,
                    'expiry_date' => $template?->expiry_date,
                    'images' => $template?->images,
                    'selling_price' => $sellingPrice,
                    'average_cost' => $unitCost,
                    'last_purchase_price' => $unitCost,
                    'cost' => $unitCost,
                    'total_stock' => $qty,
                    'status' => 1,
                ]);

                $createdStock = Stock::query()
                    ->where('store_id', $storeId)
                    ->where('product_id', $productId)
                    ->first();

                if ($createdStock) {
                    // app(\App\Services\ProductUnitCostService::class)->syncStock($createdStock);
                    $this->syncCostAndSellingPrice($createdStock);
                }
            }
        }
    }

    /**
     * Remove returned quantities from their store inventory.
     *
     * @param  array<int, array{product_id: ?string, product_name: string, qty: int}>  $lines
     */
    protected function removeLinesFromStock(int $storeId, array $lines): void
    {
        $quantities = [];
        $productNames = [];

        foreach ($lines as $line) {
            $productId = (string) ($line['product_id'] ?? '');
            if ($productId === '') {
                throw ValidationException::withMessages([
                    'lines' => 'Select an inventory product for every purchase return line.',
                ]);
            }

            $qty = (int) ($line['qty'] ?? 0);
            if ($qty <= 0) {
                throw ValidationException::withMessages([
                    'lines' => 'Purchase return quantities must be greater than zero.',
                ]);
            }

            $quantities[$productId] = ($quantities[$productId] ?? 0) + $qty;
            $productNames[$productId] = (string) ($line['product_name'] ?? $productId);
        }

        ksort($quantities, SORT_STRING);
        foreach ($quantities as $productId => $qty) {
            $stock = Stock::query()
                ->where('store_id', $storeId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            if (! $stock) {
                throw ValidationException::withMessages([
                    'lines' => "No inventory record exists for {$productNames[$productId]} in this store.",
                ]);
            }

            $available = (int) $stock->quantity;
            if ($available < $qty) {
                throw ValidationException::withMessages([
                    'lines' => "Cannot return {$qty} units of {$productNames[$productId]}; only {$available} are in stock.",
                ]);
            }

            $newQuantity = $available - $qty;
            $stock->update([
                'quantity' => $newQuantity,
                'total_stock' => $newQuantity,
            ]);

            $this->syncCostAndSellingPrice($stock->fresh());
        }
    }

    private function syncCostAndSellingPrice(Stock $stock): void
    {
        app(ProductUnitCostService::class)->syncStock($stock);
        ProductStoreMarkup::refreshSellingPrice($stock->fresh() ?? $stock);
    }

    /**
     * @return array<string, array{sku: string, category_id: mixed}>
     */
    private function productMetaFromRequest(Request $request): array
    {
        $productIds = $request->input('product_id', []);
        $skus = $request->input('sku', []);
        $categoryIds = $request->input('category_id', []);
        $meta = [];

        foreach ($productIds as $i => $productId) {
            if ($productId === '' || $productId === null) {
                continue;
            }
            $meta[(string) $productId] = [
                'sku' => isset($skus[$i]) ? (string) $skus[$i] : '',
                'category_id' => $categoryIds[$i] ?? null,
            ];
        }

        return $meta;
    }
}

<?php

namespace App\Services;

use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProductUnitCostService
{
    public function __construct(
        private readonly InventoryValuationService $valuationService,
    ) {}

    public function unitCostForStock(Stock $stock, ?Carbon $asAt = null): float
    {
        return $this->unitCostForStoreProduct(
            (int) $stock->store_id,
            (string) $stock->product_id,
            $asAt,
            $stock,
        );
    }

    public function unitCostForStoreProduct(
        int $storeId,
        string $productId,
        ?Carbon $asAt = null,
        ?Stock $stock = null,
    ): float {
        $asAt ??= now()->endOfDay();

        $valuation = $this->valuationService->valueForStoreProduct($storeId, $productId, $asAt);
        if ($valuation['qty'] > 0) {
            return round($valuation['value'] / $valuation['qty'], 2);
        }

        $stock ??= Stock::query()
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->first();

        if (! $stock) {
            return 0.0;
        }

        if ((float) ($stock->average_cost ?? 0) > 0) {
            return (float) $stock->average_cost;
        }

        if ((float) ($stock->last_purchase_price ?? 0) > 0) {
            return (float) $stock->last_purchase_price;
        }

        // Old fallback used selling price — incorrect for unit cost (Point 18).
        // if ((float) ($stock->price ?? 0) > 0) {
        //     return (float) $stock->price;
        // }

        return (float) ($stock->cost ?? 0);
    }

    public function syncStock(Stock $stock, ?Carbon $asAt = null): void
    {
        $cost = $this->unitCostForStock($stock, $asAt);

        if (round((float) ($stock->cost ?? 0), 2) === round($cost, 2)) {
            return;
        }

        Stock::query()->whereKey($stock->id)->update([
            'cost' => $cost,
            'updated_at' => now(),
        ]);

        $stock->cost = $cost;
    }

    public function syncForProduct(string $productId, ?int $storeId = null, ?Carbon $asAt = null): void
    {
        $query = Stock::query()->where('product_id', $productId);

        if ($storeId !== null) {
            $query->where('store_id', $storeId);
        }

        $query->get()->each(fn (Stock $stock) => $this->syncStock($stock, $asAt));
    }

    /**
     * @param  Collection<int, Stock>|array<int, Stock>  $stocks
     */
    public function syncMany(Collection|array $stocks, ?Carbon $asAt = null): void
    {
        foreach ($stocks as $stock) {
            $this->syncStock($stock, $asAt);
        }
    }
}

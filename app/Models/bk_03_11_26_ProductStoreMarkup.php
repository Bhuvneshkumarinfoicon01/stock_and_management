<?php

namespace App\Models;

use App\Services\ProductUnitCostService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStoreMarkup extends Model
{
    protected $fillable = [
        'product_id',
        'store_id',
        'markup_percentage',
    ];

    protected function casts(): array
    {
        return [
            'markup_percentage' => 'decimal:2',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public static function priceFromCost(float $cost, float $markupPercentage): float
    {
        return round($cost * (1 + ($markupPercentage / 100)), 2);
    }

    /**
     * Recalculate stored selling price from the latest unit cost and this store's markup.
     * The product form does this on save; GRN and batch purchase must do it too.
     */
    public static function refreshSellingPrice(Stock $stock): void
    {
        $markup = static::query()
            ->where('product_id', $stock->product_id)
            ->where('store_id', $stock->store_id)
            ->value('markup_percentage');

        // Product form treats a missing markup as 0%.
        $markupPercent = $markup !== null ? (float) $markup : 0.0;
        $cost = (float) ($stock->cost ?? 0);
        if ($cost <= 0) {
            $cost = (float) ($stock->average_cost ?? $stock->last_purchase_price ?? 0);
        }

        $sellingPrice = static::priceFromCost($cost, $markupPercent);

        Stock::query()->whereKey($stock->id)->update([
            'price' => $sellingPrice,
            'selling_price' => $sellingPrice,
            'updated_at' => now(),
        ]);
    }

    public static function syncForProduct(string $productId, array $markups, ?float $legacyCost = null): void
    {
        $costService = app(ProductUnitCostService::class);

        foreach ($markups as $storeId => $markupPercent) {
            static::updateOrCreate(
                [
                    'product_id' => $productId,
                    'store_id' => $storeId,
                ],
                [
                    'markup_percentage' => $markupPercent,
                ]
            );

            $stock = Stock::query()
                ->where('product_id', $productId)
                ->where('store_id', $storeId)
                ->first();

            if (! $stock) {
                continue;
            }

            $costService->syncStock($stock);
            $stock->refresh();

            $cost = $costService->unitCostForStock($stock);
            if ($cost <= 0 && $legacyCost !== null) {
                // Old flow passed a single manual cost for all stores.
                $cost = $legacyCost;
            }

            $sellingPrice = static::priceFromCost($cost, $markupPercent);

            Stock::query()
                ->where('product_id', $productId)
                ->where('store_id', $storeId)
                ->update([
                    'cost' => $cost,
                    'price' => $sellingPrice,
                    'selling_price' => $sellingPrice,
                ]);
        }
    }
}

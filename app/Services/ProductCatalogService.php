<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductCatalogService
{
    /**
     * Product master data is shared. Quantity is the selected store's on-hand amount.
     *
     * @return Collection<int, object>
     */
    public function search(int $storeId, string $query = '', bool $withPricing = false): Collection
    {
        $select = '
            MIN(stocks.id) as id,
            stocks.product_id,
            MAX(stocks.product_name) as product_name,
            MAX(stocks.sku) as sku,
            MAX(stocks.category_id) as category_id,
            MAX(categories.name) as category_name,
            COALESCE(SUM(CASE WHEN stocks.store_id = ? THEN stocks.quantity ELSE 0 END), 0) as total_quantity
        ';
        $bindings = [$storeId];

        if ($withPricing) {
            $select .= ',
                COALESCE(MAX(CASE WHEN stocks.store_id = ? THEN stocks.price END), MAX(stocks.price)) as price,
                COALESCE(MAX(CASE WHEN stocks.store_id = ? THEN stocks.is_taxable END), MAX(stocks.is_taxable)) as is_taxable,
                COALESCE(MAX(CASE WHEN stocks.store_id = ? THEN stocks.tax_rate END), MAX(stocks.tax_rate)) as tax_rate
            ';
            $bindings = array_merge($bindings, [$storeId, $storeId, $storeId]);
        }

        return DB::table('stocks')
            ->leftJoin('categories', 'stocks.category_id', '=', 'categories.id')
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('stocks.product_name', 'like', '%' . $query . '%')
                        ->orWhere('stocks.sku', 'like', '%' . $query . '%');
                });
            })
            ->selectRaw($select, $bindings)
            ->groupBy('stocks.product_id')
            ->orderBy('product_name')
            ->get();
    }
}

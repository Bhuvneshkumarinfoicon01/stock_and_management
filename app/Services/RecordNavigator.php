<?php

namespace App\Services;

use App\Models\Stock;
use Illuminate\Database\Eloquent\Builder;

class RecordNavigator
{
    /**
     * Previous and next links for one record in an already ordered query.
     *
     * @param  callable(int|string): string  $urlFor
     * @return array{previous: ?string, next: ?string, position: int, total: int}
     */
    public static function links(Builder $query, int|string $currentId, callable $urlFor, string $column = 'id'): array
    {
        $ids = $query->pluck($column)->map(fn ($id) => (string) $id)->all();

        return self::fromList($ids, $currentId, $urlFor);
    }

    /**
     * Products are one catalog row per product. Open the row for the header store when it exists.
     *
     * @return array{previous: ?string, next: ?string, position: int, total: int}
     */
    public static function forProduct(Stock $stock): array
    {
        $productIds = Stock::query()
            ->select('product_id')
            ->groupBy('product_id')
            ->orderByRaw('MIN(product_name)')
            ->orderBy('product_id')
            ->pluck('product_id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $storeId = session('selected_store_id');

        return self::fromList($productIds, (string) $stock->product_id, function (string $productId) use ($storeId) {
            $query = Stock::query()->where('product_id', $productId);
            if ($storeId) {
                $match = (clone $query)->where('store_id', $storeId)->value('id');
                if ($match) {
                    return route('admin.stock.edit', $match);
                }
            }

            $id = $query->orderBy('id')->value('id');

            return $id ? route('admin.stock.edit', $id) : null;
        });
    }

    /**
     * @param  array<int, int|string>  $orderedIds
     * @param  callable(int|string): ?string  $urlFor
     * @return array{previous: ?string, next: ?string, position: int, total: int}
     */
    public static function fromList(array $orderedIds, int|string $currentId, callable $urlFor): array
    {
        $orderedIds = array_values($orderedIds);
        $total = count($orderedIds);
        $current = (string) $currentId;
        $index = null;

        foreach ($orderedIds as $i => $id) {
            if ((string) $id === $current) {
                $index = $i;
                break;
            }
        }

        if ($index === null) {
            return [
                'previous' => null,
                'next' => null,
                'position' => 0,
                'total' => $total,
            ];
        }

        $previous = $index > 0 ? $urlFor($orderedIds[$index - 1]) : null;
        $next = $index < $total - 1 ? $urlFor($orderedIds[$index + 1]) : null;

        return [
            'previous' => $previous ?: null,
            'next' => $next ?: null,
            'position' => $index + 1,
            'total' => $total,
        ];
    }
}

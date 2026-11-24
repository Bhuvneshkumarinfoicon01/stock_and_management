<?php

namespace App\Services;

use App\Models\BatchPurchaseItem;
use App\Models\GoodsReceiptNoteItem;
use App\Models\OrderItem;
use App\Models\Stock;
use App\Models\StockAdjustment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InventoryValuationService
{
    public const SETTING_METHOD = 'inventory_valuation_method';

    public const SETTING_METHOD_SET_AT = 'inventory_valuation_method_set_at';

    public const SETTING_YEAR_START_MONTH = 'accounting_year_start_month';

    public const METHOD_AVCO = 'avco';

    public const METHOD_FIFO = 'fifo';

    public const METHOD_LIFO = 'lifo';

    /** @return array<string, string> */
    public function methodOptions(): array
    {
        return [
            self::METHOD_AVCO => 'Average Cost (AVCO)',
            self::METHOD_FIFO => 'First In First Out (FIFO)',
            self::METHOD_LIFO => 'Last In Last Out (LIFO)',
        ];
    }

    public function method(): string
    {
        $method = strtolower((string) setting(self::SETTING_METHOD, self::METHOD_AVCO));

        return array_key_exists($method, $this->methodOptions())
            ? $method
            : self::METHOD_AVCO;
    }

    public function methodLabel(?string $method = null): string
    {
        $method ??= $this->method();

        return $this->methodOptions()[$method] ?? $method;
    }

    /**
     * Valuation method may be changed during initial setup or within the first
     * 30 days of an accounting year (per accounting_year_start_month).
     *
     * Changing the accounting year start month can reopen this window; that is
     * accepted for now and is not blocked here.
     */
    public function canChangeMethod(): bool
    {
        $setAt = trim((string) setting(self::SETTING_METHOD_SET_AT, ''));

        if ($setAt === '') {
            return true;
        }

        return $this->isAccountingPeriodStartWindow();
    }

    public function isAccountingPeriodStartWindow(): bool
    {
        $month = max(1, min(12, (int) setting(self::SETTING_YEAR_START_MONTH, 1)));
        $now = now();
        $periodStart = Carbon::create($now->year, $month, 1)->startOfDay();

        if ($now->lt($periodStart)) {
            $periodStart = $periodStart->subYear();
        }

        return $now->lte($periodStart->copy()->addDays(29));
    }

    public function recordMethodChange(string $method): void
    {
        Setting::query()->where('key_name', self::SETTING_METHOD)->update([
            'value' => $method,
            'updated_at' => now(),
        ]);

        Setting::query()->where('key_name', self::SETTING_METHOD_SET_AT)->update([
            'value' => now()->toDateTimeString(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @return array{qty: int, value: float}
     */
    public function valueForStoreProduct(int $storeId, string $productId, Carbon $asAt): array
    {
        $method = $this->method();
        $qty = $this->quantityAsAt($storeId, $productId, $asAt);

        if ($qty <= 0) {
            return ['qty' => 0, 'value' => 0.0];
        }

        if ($method === self::METHOD_AVCO) {
            return $this->avcoValue($storeId, $productId, $qty);
        }

        $layers = $this->buildCostLayers($storeId, $productId, $asAt);
        $this->applyOutboundMovements($layers, $storeId, $productId, $asAt, $method);

        $remainingQty = array_sum(array_column($layers, 'qty'));
        if ($remainingQty <= 0 && $qty > 0) {
            return $this->avcoValue($storeId, $productId, $qty);
        }

        $value = 0.0;
        foreach ($layers as $layer) {
            if ($layer['qty'] > 0) {
                $value += $layer['qty'] * $layer['unit_cost'];
            }
        }

        return [
            'qty' => $qty,
            'value' => round($value, 2),
        ];
    }

    /**
     * @param  Collection<int, \App\Models\Store>  $stores
     * @return array<int, array<string, mixed>>
     */
    public function buildReportRows(Collection $stores, Carbon $asAt, string $search = '', ?int $categoryId = null): array
    {
        $storeIds = $stores->pluck('id');
        $stockQuery = Stock::query()
            ->with(['category', 'unit'])
            ->when($storeIds->isNotEmpty(), fn ($q) => $q->whereIn('store_id', $storeIds));

        if ($categoryId) {
            $stockQuery->where('category_id', $categoryId);
        }

        if ($search !== '') {
            $stockQuery->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $grouped = $stockQuery->get()->groupBy('product_id');
        $rows = [];

        foreach ($grouped as $productId => $items) {
            $first = $items->first();
            $storeData = [];
            $combinedQty = 0;
            $combinedValue = 0.0;

            foreach ($stores as $store) {
                $stock = $items->firstWhere('store_id', $store->id);
                if (! $stock) {
                    $storeData[(string) $store->id] = null;

                    continue;
                }

                $valuation = $this->valueForStoreProduct((int) $store->id, (string) $productId, $asAt);
                if ($valuation['qty'] <= 0) {
                    $storeData[(string) $store->id] = null;

                    continue;
                }

                $storeData[(string) $store->id] = [
                    'qty' => $valuation['qty'],
                    'value' => $valuation['value'],
                ];
                $combinedQty += $valuation['qty'];
                $combinedValue += $valuation['value'];
            }

            if ($combinedQty <= 0) {
                continue;
            }

            $rows[] = [
                'product_id' => $productId,
                'product_name' => $first->product_name,
                'sku' => $first->sku,
                'category' => $first->category->name ?? '',
                'unit' => $first->unit->name ?? '',
                'combined_qty' => $combinedQty,
                'combined_value' => round($combinedValue, 2),
                'stores' => $storeData,
            ];
        }

        usort($rows, fn ($a, $b) => strnatcasecmp((string) $a['product_name'], (string) $b['product_name']));

        return $rows;
    }

    /** @return array{qty: int, value: float} */
    private function avcoValue(int $storeId, string $productId, int $qty): array
    {
        $stock = Stock::query()
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->first();

        $unitCost = (float) ($stock->average_cost ?? $stock->last_purchase_price ?? $stock->price ?? 0);

        return [
            'qty' => $qty,
            'value' => round($qty * $unitCost, 2),
        ];
    }

    private function quantityAsAt(int $storeId, string $productId, Carbon $asAt): int
    {
        $asAtDate = $asAt->toDateString();
        $inbound = 0;
        $outbound = 0;

        $inbound += (int) GoodsReceiptNoteItem::query()
            ->join('goods_receipt_notes as grn', 'goods_receipt_note_items.goods_receipt_note_id', '=', 'grn.id')
            ->where('grn.store_id', $storeId)
            ->where('goods_receipt_note_items.product_id', $productId)
            ->whereDate('grn.grn_date', '<=', $asAtDate)
            ->sum('goods_receipt_note_items.qty_received');

        $inbound += (int) BatchPurchaseItem::query()
            ->join('batch_purchases as bp', 'batch_purchase_items.batch_purchase_id', '=', 'bp.id')
            ->where('bp.store_id', $storeId)
            ->where('bp.status', 'confirmed')
            ->where('batch_purchase_items.product_id', $productId)
            ->whereDate('bp.batch_date', '<=', $asAtDate)
            ->sum('batch_purchase_items.qty');

        $inbound += (int) StockAdjustment::query()
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->where('adjust_quantity', '>', 0)
            ->whereDate('adjust_date', '<=', $asAtDate)
            ->sum('adjust_quantity');

        $outbound += (int) OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.store_id', $storeId)
            ->where('order_items.product_id', $productId)
            ->whereDate('orders.created_at', '<=', $asAtDate)
            ->sum('order_items.qty');

        $outbound += (int) abs((int) StockAdjustment::query()
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->where('adjust_quantity', '<', 0)
            ->whereDate('adjust_date', '<=', $asAtDate)
            ->sum('adjust_quantity'));

        $calculated = max(0, $inbound - $outbound);

        if ($calculated > 0) {
            return $calculated;
        }

        $stock = Stock::query()
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->first();

        if ($stock && $asAt->isToday()) {
            return max(0, (int) $stock->quantity);
        }

        return $calculated;
    }

    /**
     * @return array<int, array{qty: int, unit_cost: float, date: string}>
     */
    private function buildCostLayers(int $storeId, string $productId, Carbon $asAt): array
    {
        $asAtDate = $asAt->toDateString();
        $layers = [];

        $grnItems = GoodsReceiptNoteItem::query()
            ->join('goods_receipt_notes as grn', 'goods_receipt_note_items.goods_receipt_note_id', '=', 'grn.id')
            ->where('grn.store_id', $storeId)
            ->where('goods_receipt_note_items.product_id', $productId)
            ->whereDate('grn.grn_date', '<=', $asAtDate)
            ->orderBy('grn.grn_date')
            ->orderBy('goods_receipt_note_items.id')
            ->get([
                'goods_receipt_note_items.qty_received',
                'goods_receipt_note_items.unit_cost',
                'grn.grn_date',
            ]);

        foreach ($grnItems as $item) {
            $qty = (int) $item->qty_received;
            if ($qty <= 0) {
                continue;
            }
            $layers[] = [
                'qty' => $qty,
                'unit_cost' => (float) $item->unit_cost,
                'date' => Carbon::parse($item->grn_date)->toDateString(),
            ];
        }

        $batchItems = BatchPurchaseItem::query()
            ->join('batch_purchases as bp', 'batch_purchase_items.batch_purchase_id', '=', 'bp.id')
            ->where('bp.store_id', $storeId)
            ->where('bp.status', 'confirmed')
            ->where('batch_purchase_items.product_id', $productId)
            ->whereDate('bp.batch_date', '<=', $asAtDate)
            ->orderBy('bp.batch_date')
            ->orderBy('batch_purchase_items.id')
            ->get([
                'batch_purchase_items.qty',
                'batch_purchase_items.final_unit',
                'batch_purchase_items.unit_price',
                'bp.batch_date',
            ]);

        foreach ($batchItems as $item) {
            $qty = (int) $item->qty;
            if ($qty <= 0) {
                continue;
            }
            $unitCost = (float) ($item->final_unit > 0 ? $item->final_unit : $item->unit_price);
            $layers[] = [
                'qty' => $qty,
                'unit_cost' => $unitCost,
                'date' => Carbon::parse($item->batch_date)->toDateString(),
            ];
        }

        $fallbackCost = (float) (Stock::query()
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->value('average_cost') ?? 0);

        $positiveAdjustments = StockAdjustment::query()
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->where('adjust_quantity', '>', 0)
            ->whereDate('adjust_date', '<=', $asAtDate)
            ->orderBy('adjust_date')
            ->orderBy('id')
            ->get();

        foreach ($positiveAdjustments as $adjustment) {
            $qty = (int) $adjustment->adjust_quantity;
            if ($qty <= 0) {
                continue;
            }
            $layers[] = [
                'qty' => $qty,
                'unit_cost' => $fallbackCost,
                'date' => Carbon::parse($adjustment->adjust_date)->toDateString(),
            ];
        }

        if ($layers === []) {
            $stock = Stock::query()
                ->where('store_id', $storeId)
                ->where('product_id', $productId)
                ->first();

            if ($stock && (int) $stock->quantity > 0) {
                $unitCost = (float) ($stock->average_cost ?? $stock->last_purchase_price ?? $stock->price ?? 0);
                $layers[] = [
                    'qty' => (int) $stock->quantity,
                    'unit_cost' => $unitCost,
                    'date' => optional($stock->created_at)->toDateString() ?? $asAtDate,
                ];
            }
        }

        return $layers;
    }

    /**
     * @param  array<int, array{qty: int, unit_cost: float, date: string}>  $layers
     */
    private function applyOutboundMovements(
        array &$layers,
        int $storeId,
        string $productId,
        Carbon $asAt,
        string $method,
    ): void {
        $asAtDate = $asAt->toDateString();
        $outMovements = [];

        $orderItems = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.store_id', $storeId)
            ->where('order_items.product_id', $productId)
            ->whereDate('orders.created_at', '<=', $asAtDate)
            ->orderBy('orders.created_at')
            ->orderBy('order_items.id')
            ->get(['order_items.qty', 'orders.created_at']);

        foreach ($orderItems as $item) {
            $qty = (int) $item->qty;
            if ($qty > 0) {
                $outMovements[] = $qty;
            }
        }

        $negativeAdjustments = StockAdjustment::query()
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->where('adjust_quantity', '<', 0)
            ->whereDate('adjust_date', '<=', $asAtDate)
            ->orderBy('adjust_date')
            ->orderBy('id')
            ->get();

        foreach ($negativeAdjustments as $adjustment) {
            $qty = abs((int) $adjustment->adjust_quantity);
            if ($qty > 0) {
                $outMovements[] = $qty;
            }
        }

        foreach ($outMovements as $qtyToConsume) {
            $this->consumeLayers($layers, $qtyToConsume, $method);
        }
    }

    /**
     * @param  array<int, array{qty: int, unit_cost: float, date: string}>  $layers
     */
    private function consumeLayers(array &$layers, int $qty, string $method): void
    {
        while ($qty > 0 && $layers !== []) {
            $index = $method === self::METHOD_LIFO ? count($layers) - 1 : 0;
            $available = (int) $layers[$index]['qty'];

            if ($available <= 0) {
                array_splice($layers, $index, 1);

                continue;
            }

            $take = min($qty, $available);
            $layers[$index]['qty'] -= $take;
            $qty -= $take;

            if ($layers[$index]['qty'] <= 0) {
                array_splice($layers, $index, 1);
            }
        }
    }
}

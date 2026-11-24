<?php

namespace App\Services;

use App\Models\BatchPurchaseItem;
use App\Models\GoodsReceiptNoteItem;
use App\Models\OrderItem;
use App\Models\Stock;
use App\Models\StockAdjustment;
use App\Models\StockTransferItem;
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
            self::METHOD_LIFO => 'Last In First Out (LIFO)',
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
     * Inventory still on hand at $asAt, valued with the method selected in settings.
     *
     * @return array{qty: int, value: float}
     */
    public function valueForStoreProduct(int $storeId, string $productId, Carbon $asAt): array
    {
        $state = $this->replayMovements($storeId, $productId, $asAt, null, $this->method(), true);
        $qty = $state['qty'];
        if ($qty <= 0.0000001) {
            return ['qty' => 0, 'value' => 0.0];
        }

        return [
            'qty' => (int) round($qty),
            'value' => round($state['value'], 2),
        ];
    }

    /**
     * Cost of selling $qty, using the valuation method selected in settings.
     * Earlier receipts and issues are applied first. $excludeOrderId is the sale
     * being costed, so its own lines are not treated as stock already sold.
     */
    public function costOfIssue(
        int $storeId,
        string $productId,
        float $qty,
        ?Carbon $asAt = null,
        ?int $excludeOrderId = null,
        float $alreadyIssuedQty = 0.0,
        ?string $method = null,
    ): float {
        $qty = round($qty, 4);
        if ($storeId <= 0 || $productId === '' || $qty <= 0) {
            return 0.0;
        }

        $asAt ??= now();
        $method = $this->normalizeMethod($method);
        $state = $this->replayMovements($storeId, $productId, $asAt, $excludeOrderId, $method, true);

        if ($method === self::METHOD_AVCO) {
            $average = $state['qty'] > 0.0000001
                ? round($state['value'] / $state['qty'], 2)
                : $this->fallbackUnitCost($storeId, $productId);

            return round($qty * $average, 2);
        }

        $layers = $state['layers'];
        if ($alreadyIssuedQty > 0) {
            $this->consumeLayers($layers, $alreadyIssuedQty, $method);
        }

        $cost = $this->consumeLayersCost($layers, $qty, $method);
        $unfilled = $this->unfilledQty;
        if ($unfilled > 0.0000001) {
            $average = $state['qty'] > 0.0000001
                ? $state['value'] / $state['qty']
                : $this->fallbackUnitCost($storeId, $productId);
            $cost += $unfilled * $average;
        }

        return round($cost, 2);
    }

    /**
     * Unit cost shown on the stock adjustment form.
     * A reduction uses the same figure a sale would (FIFO, LIFO, or AVCO).
     * An increase uses the on-hand unit cost under that same method.
     */
    public function suggestedAdjustmentUnitCost(int $storeId, string $productId, float $qty, string $type): float
    {
        if ($storeId <= 0 || $productId === '') {
            return 0.0;
        }

        $qty = $qty > 0 ? $qty : 1.0;

        if ($type === StockAdjustment::TYPE_ADD) {
            $asAt = now()->endOfDay();
            $onHand = $this->valueForStoreProduct($storeId, $productId, $asAt);
            if ($onHand['qty'] > 0 && $onHand['value'] > 0) {
                return round($onHand['value'] / $onHand['qty'], 2);
            }

            return $this->averageUnitCost($storeId, $productId, $asAt);
        }

        $total = $this->costOfIssue($storeId, $productId, $qty, now());

        return round($total / $qty, 2);
    }

    /**
     * Perpetual average of stock on hand. Selling price is a markup on this
     * figure, whichever valuation method is selected for COGS.
     */
    public function averageUnitCost(
        int $storeId,
        string $productId,
        ?Carbon $asAt = null,
        bool $includeTransfers = true,
    ): float {
        if ($storeId <= 0 || $productId === '') {
            return 0.0;
        }

        $asAt ??= now();
        $state = $this->replayMovements(
            $storeId,
            $productId,
            $asAt,
            null,
            self::METHOD_AVCO,
            $includeTransfers,
        );

        if ($state['qty'] <= 0.0000001) {
            return $this->fallbackUnitCost($storeId, $productId);
        }

        return round($state['value'] / $state['qty'], 2);
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

    private float $unfilledQty = 0.0;

    private function normalizeMethod(?string $method): string
    {
        $method = strtolower((string) ($method ?: $this->method()));

        return array_key_exists($method, $this->methodOptions())
            ? $method
            : self::METHOD_AVCO;
    }

    /**
     * @return array{qty: float, value: float, layers: array<int, array{qty: float, unit_cost: float}>}
     */
    private function replayMovements(
        int $storeId,
        string $productId,
        Carbon $asAt,
        ?int $excludeOrderId,
        string $method,
        bool $includeTransfers,
    ): array {
        $layers = [];
        $qtyOnHand = 0.0;
        $valueOnHand = 0.0;

        foreach ($this->movementEvents($storeId, $productId, $asAt, $excludeOrderId, $includeTransfers) as $event) {
            $eventQty = (float) $event['qty'];
            if ($eventQty <= 0) {
                continue;
            }

            if ($event['kind'] === 'in') {
                $unitCost = $event['unit_cost'];
                if ($unitCost === null) {
                    $unitCost = $qtyOnHand > 0.0000001 ? $valueOnHand / $qtyOnHand : 0.0;
                }

                $layers[] = ['qty' => $eventQty, 'unit_cost' => (float) $unitCost];
                $valueOnHand += $eventQty * (float) $unitCost;
                $qtyOnHand += $eventQty;

                continue;
            }

            if ($qtyOnHand <= 0.0000001) {
                continue;
            }

            // A stock adjustment with its own cost removes that value, the way a sale
            // removes COGS, so the inventory value matches the journal.
            if (! empty($event['explicit_cost']) && $event['unit_cost'] !== null) {
                $this->applyPricedIssue(
                    $layers,
                    $qtyOnHand,
                    $valueOnHand,
                    min($eventQty, $qtyOnHand),
                    (float) $event['unit_cost'],
                    $method,
                );

                continue;
            }

            $take = min($eventQty, $qtyOnHand);
            if ($method === self::METHOD_AVCO) {
                $valueOnHand -= $take * ($valueOnHand / $qtyOnHand);
                $qtyOnHand -= $take;
                if ($qtyOnHand <= 0.0000001) {
                    $qtyOnHand = 0.0;
                    $valueOnHand = 0.0;
                    $layers = [];
                }

                continue;
            }

            $this->consumeLayers($layers, $take, $method);
            $qtyOnHand = 0.0;
            $valueOnHand = 0.0;
            foreach ($layers as $layer) {
                $qtyOnHand += $layer['qty'];
                $valueOnHand += $layer['qty'] * $layer['unit_cost'];
            }
        }

        if ($method === self::METHOD_AVCO) {
            $layers = $qtyOnHand > 0.0000001
                ? [['qty' => $qtyOnHand, 'unit_cost' => $valueOnHand / $qtyOnHand]]
                : [];
        }

        return [
            'qty' => $qtyOnHand,
            'value' => $valueOnHand,
            'layers' => $layers,
        ];
    }

    /**
     * @return array<int, array{kind: string, qty: float, unit_cost: ?float, at: string, rank: int, seq: int}>
     */
    private function movementEvents(
        int $storeId,
        string $productId,
        Carbon $asAt,
        ?int $excludeOrderId,
        bool $includeTransfers,
    ): array {
        $asAtDate = $asAt->toDateString();
        $events = [];
        $seq = 0;

        $grnItems = GoodsReceiptNoteItem::query()
            ->join('goods_receipt_notes as grn', 'goods_receipt_note_items.goods_receipt_note_id', '=', 'grn.id')
            ->where('grn.store_id', $storeId)
            ->where('goods_receipt_note_items.product_id', $productId)
            ->whereDate('grn.grn_date', '<=', $asAtDate)
            ->orderBy('grn.grn_date')
            ->orderBy('goods_receipt_note_items.id')
            ->get([
                'goods_receipt_note_items.id',
                'goods_receipt_note_items.qty_received',
                'goods_receipt_note_items.unit_cost',
                'grn.grn_date',
            ]);

        foreach ($grnItems as $item) {
            $qty = (float) $item->qty_received;
            if ($qty <= 0) {
                continue;
            }
            $events[] = [
                'kind' => 'in',
                'qty' => $qty,
                'unit_cost' => (float) $item->unit_cost,
                'at' => Carbon::parse($item->grn_date)->toDateString().' 00:00:00',
                'rank' => 0,
                'seq' => $seq++,
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
                'batch_purchase_items.id',
                'batch_purchase_items.qty',
                'batch_purchase_items.final_unit',
                'batch_purchase_items.unit_price',
                'bp.batch_date',
            ]);

        foreach ($batchItems as $item) {
            $qty = (float) $item->qty;
            if ($qty <= 0) {
                continue;
            }
            $events[] = [
                'kind' => 'in',
                'qty' => $qty,
                'unit_cost' => (float) ($item->final_unit > 0 ? $item->final_unit : $item->unit_price),
                'at' => Carbon::parse($item->batch_date)->toDateString().' 00:00:00',
                'rank' => 0,
                'seq' => $seq++,
            ];
        }

        $adjustments = StockAdjustment::query()
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->whereDate('adjust_date', '<=', $asAtDate)
            ->orderBy('adjust_date')
            ->orderBy('id')
            ->get();

        foreach ($adjustments as $adjustment) {
            $qty = abs((float) $adjustment->adjust_quantity);
            if ($qty <= 0) {
                continue;
            }
            $isIn = $adjustment->adjustment_type === StockAdjustment::TYPE_ADD;
            $storedCost = $adjustment->unit_cost !== null ? (float) $adjustment->unit_cost : null;
            $events[] = [
                'kind' => $isIn ? 'in' : 'out',
                'qty' => $qty,
                'unit_cost' => $storedCost,
                'explicit_cost' => $storedCost !== null && $storedCost > 0,
                'at' => Carbon::parse($adjustment->adjust_date)->toDateString().' 12:00:00',
                'rank' => $isIn ? 0 : 1,
                'seq' => $seq++,
            ];
        }

        $orderItems = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.store_id', $storeId)
            ->where('order_items.product_id', $productId)
            ->where('orders.created_at', '<=', $asAt)
            ->when($excludeOrderId, fn ($query) => $query->where('orders.id', '!=', $excludeOrderId))
            ->where(function ($query) {
                $query->whereNull('orders.invoice_status')
                    ->orWhere('orders.invoice_status', '!=', 'Draft');
            })
            ->where(function ($query) {
                $query->whereNull('orders.status')
                    ->orWhere('orders.status', '!=', 3);
            })
            ->orderBy('orders.created_at')
            ->orderBy('order_items.id')
            ->get(['order_items.id', 'order_items.qty', 'orders.created_at']);

        foreach ($orderItems as $item) {
            $qty = (float) $item->qty;
            if ($qty <= 0) {
                continue;
            }
            $events[] = [
                'kind' => 'out',
                'qty' => $qty,
                'unit_cost' => null,
                'at' => Carbon::parse($item->created_at)->format('Y-m-d H:i:s'),
                'rank' => 1,
                'seq' => $seq++,
            ];
        }

        if ($includeTransfers) {
            $transferItems = StockTransferItem::query()
                ->join('stock_transfers as st', 'stock_transfer_items.reference_no', '=', 'st.reference_no')
                ->where('stock_transfer_items.product_id', $productId)
                ->where('stock_transfer_items.status', 1)
                ->where(function ($query) use ($storeId) {
                    $query->where('st.store_from', $storeId)
                        ->orWhere('st.store_to', $storeId);
                })
                ->whereDate('st.arrival_date', '<=', $asAtDate)
                ->orderBy('st.arrival_date')
                ->orderBy('stock_transfer_items.id')
                ->get([
                    'stock_transfer_items.id',
                    'stock_transfer_items.qty',
                    'st.store_from',
                    'st.store_to',
                    'st.arrival_date',
                ]);

            foreach ($transferItems as $item) {
                $qty = (float) $item->qty;
                if ($qty <= 0) {
                    continue;
                }
                $at = Carbon::parse($item->arrival_date)->toDateString().' 12:00:00';
                $leaving = (int) $item->store_from === $storeId;
                $unitCost = null;
                if (! $leaving) {
                    $unitCost = $this->averageUnitCost(
                        (int) $item->store_from,
                        $productId,
                        Carbon::parse($item->arrival_date)->endOfDay(),
                        false,
                    );
                }
                $events[] = [
                    'kind' => $leaving ? 'out' : 'in',
                    'qty' => $qty,
                    'unit_cost' => $unitCost,
                    'at' => $at,
                    'rank' => $leaving ? 1 : 0,
                    'seq' => $seq++,
                ];
            }
        }

        usort($events, function (array $a, array $b): int {
            $byTime = strcmp($a['at'], $b['at']);
            if ($byTime !== 0) {
                return $byTime;
            }
            $byRank = $a['rank'] <=> $b['rank'];
            if ($byRank !== 0) {
                return $byRank;
            }

            return $a['seq'] <=> $b['seq'];
        });

        return $events;
    }

    private function fallbackUnitCost(int $storeId, string $productId): float
    {
        $stock = Stock::query()
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

        return (float) ($stock->cost ?? 0);
    }

    /**
     * Remove quantity at a stated unit cost and keep the remaining inventory value in line with that cost.
     *
     * @param  array<int, array{qty: float, unit_cost: float}>  $layers
     */
    private function applyPricedIssue(
        array &$layers,
        float &$qtyOnHand,
        float &$valueOnHand,
        float $qty,
        float $unitCost,
        string $method,
    ): void {
        if ($qty <= 0 || $qtyOnHand <= 0.0000001) {
            return;
        }

        $take = min($qty, $qtyOnHand);
        $removeValue = min($valueOnHand, $take * $unitCost);
        $valueBefore = $valueOnHand;

        if ($method === self::METHOD_AVCO) {
            $qtyOnHand -= $take;
            $valueOnHand = max(0.0, $valueBefore - $removeValue);
            if ($qtyOnHand <= 0.0000001) {
                $qtyOnHand = 0.0;
                $valueOnHand = 0.0;
                $layers = [];
            }

            return;
        }

        $this->consumeLayers($layers, $take, $method);
        $qtyOnHand = 0.0;
        $layerValue = 0.0;
        foreach ($layers as $layer) {
            $qtyOnHand += $layer['qty'];
            $layerValue += $layer['qty'] * $layer['unit_cost'];
        }

        $target = max(0.0, $valueBefore - $removeValue);
        if ($qtyOnHand <= 0.0000001) {
            $qtyOnHand = 0.0;
            $valueOnHand = 0.0;
            $layers = [];

            return;
        }

        if ($layerValue > 0.0000001 && abs($layerValue - $target) > 0.0001) {
            $scale = $target / $layerValue;
            foreach ($layers as &$layer) {
                $layer['unit_cost'] *= $scale;
            }
            unset($layer);
        }

        $valueOnHand = $target;
    }

    /**
     * @param  array<int, array{qty: float, unit_cost: float}>  $layers
     */
    private function consumeLayers(array &$layers, float $qty, string $method): void
    {
        $this->consumeLayersCost($layers, $qty, $method);
    }

    /**
     * @param  array<int, array{qty: float, unit_cost: float}>  $layers
     */
    private function consumeLayersCost(array &$layers, float $qty, string $method): float
    {
        $cost = 0.0;

        while ($qty > 0.0000001 && $layers !== []) {
            $index = $method === self::METHOD_LIFO ? count($layers) - 1 : 0;
            $available = (float) $layers[$index]['qty'];

            if ($available <= 0.0000001) {
                array_splice($layers, $index, 1);

                continue;
            }

            $take = min($qty, $available);
            $cost += $take * (float) $layers[$index]['unit_cost'];
            $layers[$index]['qty'] = $available - $take;
            $qty -= $take;

            if ($layers[$index]['qty'] <= 0.0000001) {
                array_splice($layers, $index, 1);
            }
        }

        $this->unfilledQty = max(0.0, $qty);

        return $cost;
    }
}


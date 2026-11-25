<?php

namespace App\Services;

use App\Models\CustomerPayment;
use App\Models\GlAccount;
use App\Models\GoodsReceiptNoteItem;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\SaleReturn;
use App\Models\Stock;
use App\Models\StorePurchaseItem;
use App\Models\SupplierPurchaseInvoiceItem;
use App\Models\SupplierPayment;
use App\Models\SupplierPurchaseInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccountingService
{
    /** @var array<string, int> */
    private array $accountCache = [];

    /** @var array<string, ?Stock> */
    private array $stockCache = [];

    public function hasPosting(string $sourceType, int $sourceId): bool
    {
        return JournalEntry::query()
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->where('status', 'posted')
            ->exists();
    }

    /**
     * @param  array<int, array{account_code?: string, account_id?: int, debit?: float, credit?: float, line_description?: string|null}>  $lines
     */
    public function postEntry(
        string $sourceType,
        int $sourceId,
        string $entryDate,
        string $reference,
        string $description,
        array $lines,
        ?int $storeId = null,
    ): ?JournalEntry {
        if ($this->hasPosting($sourceType, $sourceId)) {
            return null;
        }

        $normalized = [];
        foreach ($lines as $line) {
            $debit = round((float) ($line['debit'] ?? 0), 2);
            $credit = round((float) ($line['credit'] ?? 0), 2);
            if ($debit <= 0 && $credit <= 0) {
                continue;
            }

            $glAccountId = isset($line['account_id'])
                ? (int) $line['account_id']
                : $this->accountId($line['account_code']);

            $normalized[] = [
                'gl_account_id' => $glAccountId,
                'line_description' => $line['line_description'] ?? null,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        if (count($normalized) < 2) {
            return null;
        }

        $totalDebit = round(array_sum(array_column($normalized, 'debit')), 2);
        $totalCredit = round(array_sum(array_column($normalized, 'credit')), 2);
        if ($totalDebit !== $totalCredit || $totalDebit <= 0) {
            Log::warning('Accounting: unbalanced entry skipped', [
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'debit' => $totalDebit,
                'credit' => $totalCredit,
            ]);

            return null;
        }

        $entry = JournalEntry::create([
            'entry_date' => $entryDate,
            'reference' => $reference,
            'description' => $description,
            'status' => 'posted',
            'user_id' => Auth::id(),
            'posted_at' => now(),
            'store_id' => $storeId,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ]);

        foreach ($normalized as $line) {
            JournalLine::create([
                'journal_entry_id' => $entry->id,
                'gl_account_id' => $line['gl_account_id'],
                'line_description' => $line['line_description'],
                'debit' => $line['debit'],
                'credit' => $line['credit'],
            ]);
        }

        return $entry;
    }

    /**
     * Remove a previous posting so an edited document can be posted again.
     */
    public function deletePosting(string $sourceType, int $sourceId): void
    {
        $entries = JournalEntry::query()
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->get();

        foreach ($entries as $entry) {
            $entry->lines()->delete();
            $entry->delete();
        }
    }

    /**
     * Stock adjustment journal.
     * A reduction matches a sale: debit cost of goods sold, credit inventory.
     * An increase matches a purchase: debit inventory. There is no supplier bill,
     * so the credit is cost of goods sold rather than accounts payable.
     */
    public function postStockAdjustment(\App\Models\StockAdjustment $adjustment): ?JournalEntry
    {
        $this->deletePosting('stock_adjustment', (int) $adjustment->id);

        $qty = (float) $adjustment->adjust_quantity;
        $unitCost = round((float) $adjustment->unit_cost, 2);
        $amount = round($qty * $unitCost, 2);
        if ($qty <= 0 || $amount <= 0) {
            return null;
        }

        $stock = $this->stockForStoreProduct((int) $adjustment->store_id, (string) $adjustment->product_id);
        $label = $stock->product_name ?? $adjustment->product_id;
        $isIncrease = $adjustment->adjustment_type === \App\Models\StockAdjustment::TYPE_ADD;
        $inventoryAccountId = $this->inventoryCreditAccountId($stock);
        $expenseAccountId = $isIncrease || ! $adjustment->adjustment_account_id
            ? $this->cogsDebitAccountId($stock)
            : (int) $adjustment->adjustment_account_id;

        if ($isIncrease) {
            $lines = [
                [
                    'account_id' => $inventoryAccountId,
                    'debit' => $amount,
                    'line_description' => 'Inventory received — ' . $label,
                ],
                [
                    'account_id' => $expenseAccountId,
                    'credit' => $amount,
                    'line_description' => 'Stock adjustment increase — ' . $label,
                ],
            ];
            $description = 'Stock adjustment increase — ' . $label;
        } else {
            $lines = [
                [
                    'account_id' => $expenseAccountId,
                    'debit' => $amount,
                    'line_description' => 'Cost of stock adjustment — ' . $label,
                ],
                [
                    'account_id' => $inventoryAccountId,
                    'credit' => $amount,
                    'line_description' => 'Inventory reduction — ' . $label,
                ],
            ];
            $description = 'Stock adjustment reduction — ' . $label;
        }

        return $this->postEntry(
            'stock_adjustment',
            (int) $adjustment->id,
            $adjustment->adjust_date
                ? Carbon::parse($adjustment->adjust_date)->toDateString()
                : now()->toDateString(),
            'ADJ-' . $adjustment->id,
            $description,
            $lines,
            $adjustment->store_id ? (int) $adjustment->store_id : null,
        );
    }

    /**
     * Point 17: sale invoices post two journals — revenue (mapping) and cost (product accounts).
     */
    public function postSaleOrder(Order $order): ?JournalEntry
    {
        if (($order->invoice_status ?? null) === 'Draft') {
            return null;
        }

        if (config('accounting.skip_estimate_orders', true) && (int) $order->status === 3) {
            return null;
        }

        $revenueEntry = $this->postSaleOrderRevenue($order);
        $this->postSaleOrderCost($order);

        return $revenueEntry;
    }

    /**
     * Journal #1 — sale value using transaction_account_mappings (point 16 / 17).
     */
    public function postSaleOrderRevenue(Order $order): ?JournalEntry
    {
        $amounts = $this->saleRevenueAmounts($order);
        $total = $amounts['total'];
        if ($total <= 0) {
            return null;
        }

        $mappingService = app(TransactionAccountMappingService::class);
        $paid = round(min((float) ($order->paid_amount ?? 0), $total), 2);
        $due = round(max(0, $total - $paid), 2);

        $lines = [];
        if ($paid > 0) {
            $lines[] = [
                'account_id' => $mappingService->debitAccountId('customer_payment', (string) config('accounting.accounts.cash', '1000')),
                'debit' => $paid,
                'line_description' => 'Cash received',
            ];
        }
        if ($due > 0) {
            $lines[] = [
                'account_id' => $mappingService->debitAccountId('sale_invoice', (string) config('accounting.accounts.accounts_receivable', '1100')),
                'debit' => $due,
                'line_description' => 'Customer receivable',
            ];
        }
        if ($paid <= 0 && $due <= 0) {
            $lines[] = [
                'account_id' => $mappingService->debitAccountId('sale_invoice', (string) config('accounting.accounts.accounts_receivable', '1100')),
                'debit' => $total,
                'line_description' => 'Customer receivable',
            ];
        }

        // Old: entire total credited to sales revenue (tax not separated).
        // $lines[] = [
        //     'account_id' => $mappingService->creditAccountId('sale_invoice', ...),
        //     'credit' => $total,
        //     'line_description' => 'Sales revenue',
        // ];

        $lines[] = [
            'account_id' => $mappingService->creditAccountId('sale_invoice', (string) config('accounting.accounts.sales_revenue', '4000')),
            'credit' => $amounts['revenue_credit'],
            'line_description' => 'Sales revenue',
        ];

        if ($amounts['tax_amount'] > 0) {
            $lines[] = [
                'account_code' => (string) config('accounting.accounts.taxation_payable', '2200'),
                'credit' => $amounts['tax_amount'],
                'line_description' => 'Taxation payable',
            ];
        }

        $reference = $order->invoice_number ?: ('INV/' . $order->id);

        return $this->postEntry(
            'order_sale',
            (int) $order->id,
            optional($order->created_at)->toDateString() ?? now()->toDateString(),
            $reference,
            'Sale revenue — ' . $reference . ' (' . ($order->customer_name ?? 'Customer #' . $order->customer_id) . ')',
            $lines,
            $order->store_id ? (int) $order->store_id : null,
        );
    }

    /**
     * Journal #2 — product cost using stock debit/credit accounts (point 17).
     */
    public function postSaleOrderCost(Order $order): ?JournalEntry
    {
        $costLines = $this->saleCostLines($order);
        if ($costLines === []) {
            Log::info('Accounting: COGS journal skipped — no cost lines for this sale.', [
                'order_id' => $order->id,
            ]);

            return null;
        }

        $totalCost = round(array_sum(array_column($costLines, 'debit')), 2);
        if ($totalCost <= 0) {
            return null;
        }

        $reference = $order->invoice_number ?: ('INV/' . $order->id);

        return $this->postEntry(
            'order_cogs',
            (int) $order->id,
            optional($order->created_at)->toDateString() ?? now()->toDateString(),
            $reference,
            'Sale cost — ' . $reference,
            $costLines,
            $order->store_id ? (int) $order->store_id : null,
        );
    }

    /*
    // Old single combined sale journal (revenue + COGS in one entry).
    public function postSaleOrder(Order $order): ?JournalEntry
    {
        ...
        $lines = array_merge($lines, $this->saleRevenueLines($order));
        $inventoryReductionLines = $this->inventoryReductionLines($order);
        ...
        return $this->postEntry('order', ...);
    }
    */

    public function postCustomerPayment(CustomerPayment $payment): ?JournalEntry
    {
        $amount = round((float) $payment->amount, 2);
        if ($amount <= 0) {
            return null;
        }

        $order = $payment->order;
        $storeId = $order?->store_id ? (int) $order->store_id : null;

        $mappingService = app(TransactionAccountMappingService::class);

        return $this->postEntry(
            'customer_payment',
            (int) $payment->id,
            $payment->payment_date?->format('Y-m-d') ?? now()->toDateString(),
            $payment->reference_no ?: 'CPAY-' . $payment->id,
            'Customer payment' . ($order ? ' for ' . ($order->invoice_number ?: ('INV/' . $order->id)) : ''),
            [
                [
                    'account_id' => $mappingService->debitAccountId('customer_payment', (string) config('accounting.accounts.cash', '1000')),
                    'debit' => $amount,
                    'line_description' => 'Payment received',
                ],
                [
                    'account_id' => $mappingService->creditAccountId('customer_payment', (string) config('accounting.accounts.accounts_receivable', '1100')),
                    'credit' => $amount,
                    'line_description' => 'Reduce receivable',
                ],
            ],
            $storeId,
        );
    }

    public function postPurchase(
        string $sourceType,
        int $sourceId,
        string $entryDate,
        string $reference,
        string $description,
        float $amount,
        ?int $storeId = null,
    ): ?JournalEntry {
        $amount = round($amount, 2);
        if ($amount <= 0) {
            return null;
        }

        $mappingService = app(TransactionAccountMappingService::class);
        $inventoryAmount = $amount;
        $taxAmount = 0.0;

        if ($sourceType === 'supplier_purchase_invoice') {
            $invoice = SupplierPurchaseInvoice::query()->find($sourceId);
            if ($invoice) {
                $inventoryAmount = round((float) ($invoice->subtotal ?? $amount), 2);
                $taxAmount = $invoice->tax_exempt ? 0.0 : round((float) ($invoice->tax_amount ?? 0), 2);
                $amount = round((float) ($invoice->total ?? $amount), 2);
            }
        }

        $lines = $this->purchaseInventoryLines($sourceType, $sourceId, $storeId, $inventoryAmount, 'debit');

        if ($taxAmount > 0) {
            $lines[] = [
                'account_code' => (string) config('accounting.accounts.taxation_payable', '2200'),
                'debit' => $taxAmount,
                'line_description' => 'Input tax (taxation payable)',
            ];
        }

        $lines[] = [
            'account_id' => $mappingService->creditAccountId('purchase_invoice', (string) config('accounting.accounts.accounts_payable', '2000')),
            'credit' => $amount,
            'line_description' => 'Supplier payable',
        ];

        return $this->postEntry(
            $sourceType,
            $sourceId,
            $entryDate,
            $reference,
            $description,
            $lines,
            $storeId,
        );
    }

    public function postPurchaseReturn(
        string $sourceType,
        int $sourceId,
        string $entryDate,
        string $reference,
        string $description,
        float $amount,
        ?int $storeId = null,
    ): ?JournalEntry {
        $amount = round($amount, 2);
        if ($amount <= 0) {
            return null;
        }

        $purchaseReturn = $sourceType === 'purchase_return'
            ? PurchaseReturn::query()->find($sourceId)
            : null;
        $isCashReturn = $purchaseReturn && ! $purchaseReturn->supplier_purchase_invoice_id;
        $settlementAccountCode = $isCashReturn
            ? (string) config('accounting.accounts.cash', '1000')
            : (string) config('accounting.accounts.accounts_payable', '2000');
        $inventoryAmount = $purchaseReturn
            ? round((float) $purchaseReturn->subtotal, 2)
            : $amount;
        $taxAmount = $purchaseReturn && ! $purchaseReturn->tax_exempt
            ? round((float) $purchaseReturn->tax_amount, 2)
            : 0.0;
        $lines = array_merge(
            [
                [
                    'account_code' => $settlementAccountCode,
                    'debit' => $amount,
                    'line_description' => $isCashReturn
                        ? 'Cash received for purchase return'
                        : 'Reduce supplier payable',
                ],
            ],
            $this->purchaseInventoryLines($sourceType, $sourceId, $storeId, $inventoryAmount, 'credit'),
        );

        if ($taxAmount > 0) {
            $lines[] = [
                'account_code' => (string) config('accounting.accounts.taxation_payable', '2200'),
                'credit' => $taxAmount,
                'line_description' => 'Taxation payable on purchase return',
            ];
        }

        return $this->postEntry(
            $sourceType,
            $sourceId,
            $entryDate,
            $reference,
            $description,
            $lines,
            $storeId,
        );
    }

    public function postSaleReturn(SaleReturn $saleReturn): void
    {
        $settlementAccountCode = $saleReturn->settlement_method === 'cash_refund'
            ? (string) config('accounting.accounts.cash', '1000')
            : (string) config('accounting.accounts.accounts_receivable', '1100');
        $taxAmount = $saleReturn->tax_exempt ? 0.0 : round((float) $saleReturn->tax_amount, 2);
        $lines = [
            [
                'account_id' => $this->accountId((string) config('accounting.accounts.sales_revenue', '4000')),
                'debit' => round((float) $saleReturn->subtotal, 2),
                'line_description' => 'Sales returns',
            ],
        ];

        if ($taxAmount > 0) {
            $lines[] = [
                'account_code' => (string) config('accounting.accounts.taxation_payable', '2200'),
                'debit' => $taxAmount,
                'line_description' => 'Reverse taxation payable on sale return',
            ];
        }
        $lines[] = [
            'account_id' => $this->accountId($settlementAccountCode),
            'credit' => round((float) $saleReturn->total_amount, 2),
            'line_description' => $saleReturn->settlement_method === 'cash_refund'
                ? 'Cash refunded to customer'
                : 'Customer account credit',
        ];

        $reference = $saleReturn->return_number;
        $revenueEntry = $this->postEntry(
            'sale_return',
            (int) $saleReturn->id,
            $saleReturn->return_date->format('Y-m-d'),
            $reference,
            'Sale return — ' . $reference,
            $lines,
            $saleReturn->store_id ? (int) $saleReturn->store_id : null,
        );
        if (! $revenueEntry) {
            throw new \RuntimeException("Could not post the sale return journal for [{$reference}].");
        }

        $costLines = [];
        foreach ($saleReturn->items as $item) {
            $amount = round((float) $item->qty * (float) $item->unit_cost, 2);
            if ($amount <= 0) {
                continue;
            }
            $stock = $this->stockForStoreProduct(
                $saleReturn->store_id ? (int) $saleReturn->store_id : null,
                (string) $item->product_id,
            );
            $costLines[] = [
                'account_id' => $this->inventoryCreditAccountId($stock),
                'debit' => $amount,
                'line_description' => 'Inventory restored — ' . ($item->product_name ?: $item->product_id),
            ];
            $costLines[] = [
                'account_id' => $this->cogsDebitAccountId($stock),
                'credit' => $amount,
                'line_description' => 'Reverse cost of goods sold — ' . ($item->product_name ?: $item->product_id),
            ];
        }

        if ($costLines !== []) {
            $costEntry = $this->postEntry(
                'sale_return_cogs',
                (int) $saleReturn->id,
                $saleReturn->return_date->format('Y-m-d'),
                $reference,
                'Sale return inventory cost — ' . $reference,
                $costLines,
                $saleReturn->store_id ? (int) $saleReturn->store_id : null,
            );
            if (! $costEntry) {
                throw new \RuntimeException("Could not post the sale return inventory journal for [{$reference}].");
            }
        }
    }

    public function postSupplierPayment(SupplierPayment $payment): ?JournalEntry
    {
        $amount = round((float) $payment->amount, 2);
        if ($amount <= 0) {
            return null;
        }

        $mappingService = app(TransactionAccountMappingService::class);

        return $this->postEntry(
            'supplier_payment',
            (int) $payment->id,
            $payment->payment_date?->format('Y-m-d') ?? now()->toDateString(),
            $payment->reference_no ?: 'SPAY-' . $payment->id,
            'Supplier payment',
            [
                [
                    'account_id' => $mappingService->debitAccountId('supplier_payment', (string) config('accounting.accounts.accounts_payable', '2000')),
                    'debit' => $amount,
                    'line_description' => 'Pay supplier',
                ],
                [
                    'account_id' => $mappingService->creditAccountId('supplier_payment', (string) config('accounting.accounts.cash', '1000')),
                    'credit' => $amount,
                    'line_description' => 'Cash paid',
                ],
            ],
            null,
        );
    }

    private function cogsForOrder(Order $order): float
    {
        return round(array_sum($this->cogsAmountsByItem($order)), 2);
    }

    /**
     * COGS for each order line, using the inventory valuation method in settings.
     * FIFO and LIFO consume purchase layers. AVCO uses the average of stock on hand.
     *
     * @return array<int, float>
     */
    private function cogsAmountsByItem(Order $order): array
    {
        $items = $order->relationLoaded('orderItems')
            ? $order->orderItems->sortBy('id')->values()
            : OrderItem::query()->where('order_id', $order->id)->orderBy('id')->get();

        if ($items->isEmpty() || ! $order->store_id) {
            return [];
        }

        $valuation = app(InventoryValuationService::class);
        $asAt = $order->created_at ? Carbon::parse($order->created_at) : now();
        $storeId = (int) $order->store_id;
        $issued = [];
        $amounts = [];

        foreach ($items as $item) {
            $productId = (string) $item->product_id;
            $qty = (float) $item->qty;
            $prior = (float) ($issued[$productId] ?? 0);
            $amounts[(int) $item->id] = $valuation->costOfIssue(
                $storeId,
                $productId,
                $qty,
                $asAt,
                (int) $order->id,
                $prior,
            );
            $issued[$productId] = $prior + $qty;
        }

        return $amounts;
    }

    /**
     * Product-level revenue split. Falls back to the global sales account for older products.
     *
     * @return array<int, array{account_code?: string, account_id?: int, debit?: float, credit?: float, line_description?: string|null}>
     */
    private function saleRevenueLines(Order $order): array
    {
        $items = OrderItem::query()
            ->where('order_id', $order->id)
            ->get(['product_id', 'product_name', 'qty', 'price']);

        if ($items->isEmpty()) {
            return [[
                'account_code' => config('accounting.accounts.sales_revenue'),
                'credit' => round((float) $order->total_price, 2),
                'line_description' => 'Sales revenue',
            ]];
        }

        $buckets = [];
        foreach ($items as $item) {
            $amount = round((float) $item->qty * (float) $item->price, 2);
            if ($amount <= 0) {
                continue;
            }

            $stock = $this->stockForStoreProduct($order->store_id ? (int) $order->store_id : null, (string) $item->product_id);
            $line = [
                'credit' => $amount,
                'line_description' => 'Sales revenue — ' . ($item->product_name ?: $item->product_id),
            ];

            if ($stock?->credit_account_id) {
                $line['account_id'] = (int) $stock->credit_account_id;
            } else {
                $line['account_code'] = config('accounting.accounts.sales_revenue');
            }

            $this->addPostingBucket($buckets, $line, 'credit');
        }

        $credited = round(array_sum(array_column($buckets, 'credit')), 2);
        $residual = round((float) $order->total_price - $credited, 2);
        if ($residual !== 0.0) {
            $adjustmentSide = $residual > 0 ? 'credit' : 'debit';
            $this->addPostingBucket($buckets, [
                'account_code' => config('accounting.accounts.sales_revenue'),
                $adjustmentSide => abs($residual),
                'line_description' => 'Sales revenue adjustment',
            ], $adjustmentSide);
        }

        return array_values($buckets);
    }

    /**
     * Point 17 — product cost journal: Dr COGS (debit account), Cr Inventory (credit account).
     *
     * @return array<int, array{account_code?: string, account_id?: int, debit?: float, credit?: float, line_description?: string|null}>
     */
    private function saleCostLines(Order $order): array
    {
        $items = $order->orderItems()->orderBy('id')->get(['id', 'product_id', 'product_name', 'qty']);
        $cogsByItem = $this->cogsAmountsByItem($order);

        $debitBuckets = [];
        $creditBuckets = [];
        foreach ($items as $item) {
            $stock = $this->stockForStoreProduct($order->store_id ? (int) $order->store_id : null, (string) $item->product_id);
            $amount = round((float) ($cogsByItem[$item->id] ?? 0), 2);
            if ($amount <= 0) {
                continue;
            }

            $label = $item->product_name ?: $item->product_id;

            // Point 17: Dr COGS, Cr Inventory (defaults 5000 / 1200).
            // Old: used product debit_account (inventory on receipt) for COGS debit — incorrect.
            $debitLine = [
                'account_id' => $this->cogsDebitAccountId($stock),
                'debit' => $amount,
                'line_description' => 'Cost of goods sold — ' . $label,
            ];
            $this->addPostingBucket($debitBuckets, $debitLine, 'debit');

            $creditLine = [
                'account_id' => $this->inventoryCreditAccountId($stock),
                'credit' => $amount,
                'line_description' => 'Inventory reduction — ' . $label,
            ];
            $this->addPostingBucket($creditBuckets, $creditLine, 'credit');
        }

        return array_merge(array_values($debitBuckets), array_values($creditBuckets));
    }

    /**
     * @return array{total: float, revenue_credit: float, tax_amount: float}
     */
    private function saleRevenueAmounts(Order $order): array
    {
        $total = round((float) $order->total_price, 2);
        $subtotal = round((float) ($order->subtotal_price ?? 0), 2);
        $taxAmount = ($order->tax_exempt ?? false) ? 0.0 : round((float) ($order->tax_amount ?? 0), 2);
        $discountPercent = (float) ($order->discount ?? 0);
        $shipping = round((float) ($order->shipping ?? 0), 2);
        $discountAmount = round($subtotal * $discountPercent / 100, 2);

        $revenueCredit = round($subtotal - $discountAmount + $shipping, 2);

        if (round($revenueCredit + $taxAmount, 2) !== $total && $total > 0) {
            $revenueCredit = round(max(0, $total - $taxAmount), 2);
        }

        return [
            'total' => $total,
            'revenue_credit' => $revenueCredit,
            'tax_amount' => $taxAmount,
        ];
    }

    private function unitCostForStock(?Stock $stock): float
    {
        if (! $stock) {
            return 0.0;
        }

        // Point 18 — transaction-derived product unit cost, then movement averages.
        if ((float) ($stock->cost ?? 0) > 0) {
            return (float) $stock->cost;
        }

        if ((float) $stock->average_cost > 0) {
            return (float) $stock->average_cost;
        }
        if ((float) $stock->last_purchase_price > 0) {
            return (float) $stock->last_purchase_price;
        }

        // Old fallback used selling price — incorrect for COGS (Point 18).
        // if ((float) $stock->price > 0) {
        //     return (float) $stock->price;
        // }

        return 0.0;
    }

    /** COGS debit account: product expense account if configured, else 5000. */
    private function cogsDebitAccountId(?Stock $stock): int
    {
        if ($stock?->debit_account_id) {
            $stock->loadMissing('debitAccount');
            if ($stock->debitAccount && $stock->debitAccount->type === 'expense') {
                return (int) $stock->debit_account_id;
            }
        }

        return $this->accountId((string) config('accounting.accounts.cogs', '5000'));
    }

    /** Inventory credit account: product asset account if configured, else 1200. */
    private function inventoryCreditAccountId(?Stock $stock): int
    {
        if ($stock?->credit_account_id) {
            $stock->loadMissing('creditAccount');
            if ($stock->creditAccount && $stock->creditAccount->type === 'asset') {
                return (int) $stock->credit_account_id;
            }
        }

        return $this->accountId((string) config('accounting.accounts.inventory', '1200'));
    }

    /*
    // Old inventory reduction used debit_account for credit side (single combined journal).
    private function inventoryReductionLines(Order $order): array { ... }
    */

    /**
     * Product-level purchase inventory split. Falls back to the global inventory account.
     *
     * @return array<int, array{account_code?: string, account_id?: int, debit?: float, credit?: float, line_description?: string|null}>
     */
    private function purchaseInventoryLines(
        string $sourceType,
        int $sourceId,
        ?int $storeId,
        float $fallbackAmount,
        string $side,
    ): array {
        $items = $this->purchaseItemsForSource($sourceType, $sourceId);
        if ($items === []) {
            $mappingService = app(TransactionAccountMappingService::class);

            return [[
                'account_id' => $mappingService->debitAccountId('purchase_invoice', (string) config('accounting.accounts.inventory', '1200')),
                $side => round($fallbackAmount, 2),
                'line_description' => $side === 'debit' ? 'Inventory received' : 'Inventory returned',
            ]];
        }

        $buckets = [];
        foreach ($items as $item) {
            $amount = round((float) ($item['amount'] ?? 0), 2);
            if ($amount <= 0) {
                continue;
            }

            $stock = $this->stockForStoreProduct($storeId, (string) $item['product_id']);
            $line = [
                $side => $amount,
                'line_description' => ($side === 'debit' ? 'Inventory received — ' : 'Inventory returned — ')
                    . ($item['product_name'] ?: $item['product_id']),
            ];

            if ($stock?->debit_account_id) {
                $line['account_id'] = (int) $stock->debit_account_id;
            } else {
                $line['account_code'] = config('accounting.accounts.inventory');
            }

            $this->addPostingBucket($buckets, $line, $side);
        }

        $postedAmount = round(array_sum(array_column($buckets, $side)), 2);
        $residual = round($fallbackAmount - $postedAmount, 2);
        if ($residual !== 0.0) {
            $adjustmentSide = $residual > 0 ? $side : ($side === 'debit' ? 'credit' : 'debit');
            $this->addPostingBucket($buckets, [
                'account_code' => config('accounting.accounts.inventory'),
                $adjustmentSide => abs($residual),
                'line_description' => $side === 'debit' ? 'Inventory received adjustment' : 'Inventory returned adjustment',
            ], $adjustmentSide);
        }

        return array_values($buckets);
    }

    /**
     * @return array<int, array{product_id: string, product_name: string|null, amount: float}>
     */
    private function purchaseItemsForSource(string $sourceType, int $sourceId): array
    {
        return match ($sourceType) {
            'purchase_order' => PurchaseOrderItem::query()
                ->where('purchase_order_id', $sourceId)
                ->get(['product_id', 'product_name', 'line_total'])
                ->map(fn (PurchaseOrderItem $item) => [
                    'product_id' => (string) $item->product_id,
                    'product_name' => $item->product_name,
                    'amount' => (float) $item->line_total,
                ])->all(),
            'store_purchase' => StorePurchaseItem::query()
                ->where('store_purchase_id', $sourceId)
                ->get(['product_id', 'product_name', 'line_total'])
                ->map(fn (StorePurchaseItem $item) => [
                    'product_id' => (string) $item->product_id,
                    'product_name' => $item->product_name,
                    'amount' => (float) $item->line_total,
                ])->all(),
            'supplier_purchase_invoice' => SupplierPurchaseInvoiceItem::query()
                ->where('supplier_purchase_invoice_id', $sourceId)
                ->get(['product_id', 'product_name', 'line_total'])
                ->map(fn (SupplierPurchaseInvoiceItem $item) => [
                    'product_id' => (string) $item->product_id,
                    'product_name' => $item->product_name,
                    'amount' => (float) $item->line_total,
                ])->all(),
            'goods_receipt_note' => GoodsReceiptNoteItem::query()
                ->where('goods_receipt_note_id', $sourceId)
                ->get(['product_id', 'product_name', 'line_total'])
                ->map(fn (GoodsReceiptNoteItem $item) => [
                    'product_id' => (string) $item->product_id,
                    'product_name' => $item->product_name,
                    'amount' => (float) $item->line_total,
                ])->all(),
            'purchase_return' => PurchaseReturnItem::query()
                ->where('purchase_return_id', $sourceId)
                ->get(['product_id', 'product_name', 'line_total'])
                ->map(fn (PurchaseReturnItem $item) => [
                    'product_id' => (string) $item->product_id,
                    'product_name' => $item->product_name,
                    'amount' => (float) $item->line_total,
                ])->all(),
            default => [],
        };
    }

    //imporv method
    /*private function purchaseItemsForSource(string $sourceType, int $sourceId): array
        {
            $sources = [
                'purchase_order' => [
                    'model' => PurchaseOrderItem::class,
                    'key' => 'purchase_order_id',
                ],
                'store_purchase' => [
                    'model' => StorePurchaseItem::class,
                    'key' => 'store_purchase_id',
                ],
                'supplier_purchase_invoice' => [
                    'model' => SupplierPurchaseInvoiceItem::class,
                    'key' => 'supplier_purchase_invoice_id',
                ],
                'goods_receipt_note' => [
                    'model' => GoodsReceiptNoteItem::class,
                    'key' => 'goods_receipt_note_id',
                ],
                'purchase_return' => [
                    'model' => PurchaseReturnItem::class,
                    'key' => 'purchase_return_id',
                ],
            ];

            if (! isset($sources[$sourceType])) {
                return [];
            }

            $model = $sources[$sourceType]['model'];
            $key = $sources[$sourceType]['key'];

            return $model::query()
                ->where($key, $sourceId)
                ->get(['product_id', 'product_name', 'line_total'])
                ->map(fn ($item) => [
                    'product_id' => (string) $item->product_id,
                    'product_name' => $item->product_name,
                    'amount' => (float) $item->line_total,
                ])
                ->all();
        }*/



    /**
     * @param  array<string, array{account_code?: string, account_id?: int, debit?: float, credit?: float, line_description?: string|null}>  $buckets
     * @param  array{account_code?: string, account_id?: int, debit?: float, credit?: float, line_description?: string|null}  $line
     */
    private function addPostingBucket(array &$buckets, array $line, string $side): void
    {
        $accountKey = isset($line['account_id'])
            ? 'id:' . $line['account_id']
            : 'code:' . $line['account_code'];

        if (!isset($buckets[$accountKey])) {
            $buckets[$accountKey] = [
                'line_description' => $line['line_description'] ?? null,
            ];

            if (isset($line['account_id'])) {
                $buckets[$accountKey]['account_id'] = (int) $line['account_id'];
            } else {
                $buckets[$accountKey]['account_code'] = $line['account_code'];
            }
        }

        $buckets[$accountKey][$side] = round(
            (float) ($buckets[$accountKey][$side] ?? 0) + (float) ($line[$side] ?? 0),
            2
        );
    }

    /**
     * Invoice summary + grouped journal lines for sale journal detail view (credit / cash sales).
     *
     * @return array<string, mixed>|null
     */
    public function saleJournalDisplayContext(JournalEntry $journalEntry): ?array
    {
        $saleSourceTypes = ['order_sale', 'order_cogs', 'order'];
        if (! in_array($journalEntry->source_type, $saleSourceTypes, true) || ! $journalEntry->source_id) {
            return null;
        }

        $order = Order::query()
            ->with(['orderItems', 'customer'])
            ->find($journalEntry->source_id);

        if (! $order) {
            return null;
        }

        $relatedJournals = JournalEntry::query()
            ->where('status', 'posted')
            ->where('source_id', $order->id)
            ->whereIn('source_type', $saleSourceTypes)
            ->with(['lines.glAccount'])
            ->orderByRaw("CASE source_type WHEN 'order_sale' THEN 1 WHEN 'order' THEN 1 WHEN 'order_cogs' THEN 2 ELSE 3 END")
            ->orderBy('id')
            ->get();

        if ($relatedJournals->isEmpty()) {
            $journalEntry->loadMissing(['lines.glAccount']);
            $relatedJournals = collect([$journalEntry]);
        }

        $cogsByItem = $this->cogsAmountsByItem($order);
        $invoiceLines = [];
        foreach ($order->orderItems as $item) {
            $qty = (float) $item->qty;
            $unitPrice = (float) $item->price;
            $lineSubtotal = round($qty * $unitPrice, 2);
            $lineCogs = round((float) ($cogsByItem[$item->id] ?? 0), 2);
            $invoiceLines[] = [
                'product_name' => (string) ($item->product_name ?: $item->product_id),
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'tax_rate' => (float) ($item->tax_rate ?? 0),
                'is_taxable' => (bool) ($item->is_taxable ?? false),
                'line_total' => $lineSubtotal,
                'unit_cost' => $qty > 0 ? round($lineCogs / $qty, 2) : 0.0,
            ];
        }

        $subtotal = round((float) ($order->subtotal_price ?? 0), 2);
        $discountPercent = (float) ($order->discount ?? 0);
        $discountAmount = round($subtotal * $discountPercent / 100, 2);
        $taxAmount = ($order->tax_exempt ?? false) ? 0.0 : round((float) ($order->tax_amount ?? 0), 2);
        $total = round((float) $order->total_price, 2);

        $journalGroups = [];
        $previousWasRevenue = false;
        foreach ($relatedJournals as $journal) {
            $isCogs = $journal->source_type === 'order_cogs';
            if ($isCogs && $previousWasRevenue && $journalGroups !== []) {
                $journalGroups[] = ['spacer' => true];
            }

            // $dateLabel = $journal->entry_date->format('d-M-y');
            $dateLabel = format_date($journal->entry_date);
            $rows = [];
            $first = true;
            foreach ($journal->lines as $line) {
                $debit = round((float) $line->debit, 2);
                $credit = round((float) $line->credit, 2);
                $rows[] = [
                    'date' => $first ? $dateLabel : '',
                    'particulars' => $line->glAccount->code . ' - ' . $line->glAccount->name,
                    'memo' => (string) ($line->line_description ?? ''),
                    'debit' => $debit > 0 ? $debit : null,
                    'credit' => $credit > 0 ? $credit : null,
                ];
                $first = false;
            }

            if ($rows !== []) {
                $journalGroups[] = ['rows' => $rows, 'source_type' => $journal->source_type];
            }

            $previousWasRevenue = in_array($journal->source_type, ['order_sale', 'order'], true);
        }

        return [
            'invoice_number' => $order->invoice_number ?: ('INV/' . $order->id),
            'customer_name' => $order->customer_name ?: ($order->customer->name ?? ('Customer #' . $order->customer_id)),
            // 'entry_date' => $journalEntry->entry_date->format('d-M-y'),
            'entry_date' => format_date($journalEntry->entry_date),
            'invoice_lines' => $invoiceLines,
            'subtotal' => $subtotal,
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'tax_exempt' => (bool) ($order->tax_exempt ?? false),
            'total' => $total,
            'journal_groups' => $journalGroups,
            'related_journal_ids' => $relatedJournals->pluck('id')->all(),
        ];
    }

    private function stockForStoreProduct(?int $storeId, string $productId): ?Stock
    {
        $cacheKey = (string) ($storeId ?? 0) . ':' . $productId;
        if (array_key_exists($cacheKey, $this->stockCache)) {
            return $this->stockCache[$cacheKey];
        }

        $query = Stock::query()->where('product_id', $productId);
        if ($storeId !== null) {
            $query->where('store_id', $storeId);
        }

        return $this->stockCache[$cacheKey] = $query->first();
    }

    private function accountId(string $code): int
    {
        if (isset($this->accountCache[$code])) {
            return $this->accountCache[$code];
        }

        $id = GlAccount::query()->where('code', $code)->where('is_active', true)->value('id');
        if (!$id) {
            throw new \RuntimeException("GL account code [{$code}] not found or inactive. Run migrations / seed chart of accounts.");
        }

        $this->accountCache[$code] = (int) $id;

        return (int) $id;
    }
}


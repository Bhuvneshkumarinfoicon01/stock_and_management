<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\Concerns\NormalizesPurchaseLines;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\Order;
use App\Models\ProductStoreMarkup;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\Stock;
use App\Models\Store;
use App\Services\AccountingService;
use App\Services\DatatablePaginationService;
use App\Services\InventoryValuationService;
use App\Services\ProductUnitCostService;
use App\Services\StoreSequentialNumberService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleReturnController extends Controller
{
    use NormalizesPurchaseLines;

    public function index(Request $request): View
    {
        $perPage = app(DatatablePaginationService::class)->defaultLength();
        $returns = SaleReturn::query()
            ->with(['customer', 'store', 'order'])
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.sales.sale-returns-index', compact('returns'));
    }

    public function create(): View
    {
        $customers = Customer::query()->where('status', 1)->orderBy('customer_name')->get();
        $stores = Store::query()->orderBy('name')->get();
        $orders = Order::query()
            ->where(function ($query) {
                $query->whereNull('invoice_status')->orWhere('invoice_status', '!=', 'Draft');
            })
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status', '!=', 3);
            })
            ->orderByDesc('id')
            ->limit(200)
            ->get(['id', 'store_id', 'customer_id', 'customer_name', 'invoice_number']);
        $selectedStoreId = auth()->user()->selected_store_id ?? null;

        return view('admin.sales.sale-returns-create', compact('customers', 'stores', 'orders', 'selectedStoreId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(array_merge([
            'customer_id' => 'required|exists:customers,id',
            'store_id' => 'required|exists:stores,id',
            'order_id' => 'nullable|exists:orders,id',
            'return_date' => 'required|date',
            'settlement_method' => 'required|in:cash_refund,customer_balance',
            'tax_exempt' => 'nullable|boolean',
            'reason' => 'nullable|string|max:2000',
        ], $this->invoiceStyleLineValidationRules()));

        $lines = $this->linesFromInvoiceStyleArrays($request);
        if ($lines === []) {
            return back()->withErrors(['lines' => 'Add at least one product.'])->withInput();
        }

        foreach ($lines as $line) {
            if (! $line['product_id']) {
                throw ValidationException::withMessages([
                    'lines' => 'Select an inventory product for every sale return line.',
                ]);
            }
        }

        DB::transaction(function () use ($validated, $lines, $request) {
            $storeId = (int) $validated['store_id'];
            $customerId = (int) $validated['customer_id'];
            $order = null;
            if (! empty($validated['order_id'])) {
                $order = Order::query()->lockForUpdate()->findOrFail((int) $validated['order_id']);
                if ((int) $order->store_id !== $storeId || (int) $order->customer_id !== $customerId) {
                    throw ValidationException::withMessages([
                        'order_id' => 'The selected sales invoice must belong to this customer and store.',
                    ]);
                }
                if (($order->invoice_status ?? null) === 'Draft' || (int) $order->status === 3) {
                    throw ValidationException::withMessages([
                        'order_id' => 'Draft invoices and estimates cannot be returned.',
                    ]);
                }
            }

            $quantities = [];
            foreach ($lines as $line) {
                $productId = (string) $line['product_id'];
                $quantities[$productId] = ($quantities[$productId] ?? 0) + (int) $line['qty'];
            }
            ksort($quantities, SORT_STRING);

            $stocks = [];
            foreach ($quantities as $productId => $qty) {
                $stock = Stock::query()
                    ->where('store_id', $storeId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();
                if (! $stock) {
                    throw ValidationException::withMessages([
                        'lines' => "Product {$productId} is not available in the selected store.",
                    ]);
                }
                $stocks[$productId] = $stock;
            }

            $returnDate = Carbon::parse($validated['return_date']);
            $valuation = app(InventoryValuationService::class);
            $soldQuantities = [];
            $orderUnitCosts = [];
            $previouslyReturned = [];
            if ($order) {
                $orderItems = $order->orderItems()->orderBy('id')->get(['product_id', 'qty']);
                foreach ($orderItems as $item) {
                    $productId = (string) $item->product_id;
                    $soldQuantities[$productId] = ($soldQuantities[$productId] ?? 0) + (int) $item->qty;
                }

                $previouslyReturned = SaleReturnItem::query()
                    ->join('sale_returns as sr', 'sale_return_items.sale_return_id', '=', 'sr.id')
                    ->where('sr.order_id', $order->id)
                    ->where('sr.status', 'completed')
                    ->selectRaw('sale_return_items.product_id, SUM(sale_return_items.qty) as returned_qty')
                    ->groupBy('sale_return_items.product_id')
                    ->pluck('returned_qty', 'product_id')
                    ->all();

                foreach ($soldQuantities as $productId => $soldQty) {
                    if ($soldQty <= 0) {
                        continue;
                    }
                    $orderUnitCosts[$productId] = $valuation->costOfIssue(
                        $storeId,
                        $productId,
                        (float) $soldQty,
                        $order->created_at ? Carbon::parse($order->created_at) : $returnDate,
                        (int) $order->id,
                    ) / $soldQty;
                }

                foreach ($quantities as $productId => $qty) {
                    $remainingQty = (int) ($soldQuantities[$productId] ?? 0)
                        - (int) ($previouslyReturned[$productId] ?? 0);
                    if ($remainingQty < $qty) {
                        throw ValidationException::withMessages([
                            'lines' => "Cannot return {$qty} units of {$productId}; only {$remainingQty} units remain returnable on this invoice.",
                        ]);
                    }
                }
            }

            $costedQuantity = [];
            $stockCostTotals = [];
            foreach ($lines as &$line) {
                $productId = (string) $line['product_id'];
                $qty = (float) $line['qty'];
                $alreadyCosted = (float) ($costedQuantity[$productId] ?? 0);
                $cost = isset($orderUnitCosts[$productId])
                    ? $qty * $orderUnitCosts[$productId]
                    : $valuation->costOfIssue(
                        $storeId,
                        $productId,
                        $qty,
                        $returnDate->copy()->endOfDay(),
                        null,
                        $alreadyCosted,
                    );
                $stock = $stocks[$productId];
                if ($cost <= 0) {
                    $fallbackUnitCost = (float) ($stock->average_cost ?: $stock->last_purchase_price ?: $stock->cost ?: 0);
                    $cost = round($qty * $fallbackUnitCost, 2);
                }
                $line['unit_cost'] = round($cost / $qty, 2);
                if ($line['unit_cost'] <= 0) {
                    throw ValidationException::withMessages([
                        'lines' => "A valuation cost is not available for {$line['product_name']}.",
                    ]);
                }
                $line['line_total'] = round($qty * (float) $line['unit_price'], 2);
                $costedQuantity[$productId] = $alreadyCosted + $qty;
                $stockCostTotals[$productId] = ($stockCostTotals[$productId] ?? 0)
                    + round($qty * $line['unit_cost'], 2);
            }
            unset($line);

            [$subtotal, $taxAmount, $totalAmount] = $this->totalsFromPerLineTax($lines);
            if ($totalAmount <= 0) {
                throw ValidationException::withMessages([
                    'lines' => 'The sale return total must be greater than zero.',
                ]);
            }
            $returnNumber = app(StoreSequentialNumberService::class)->issueNextForStore(
                $storeId,
                'sale_return_prefix',
                'next_sale_return_number',
                'SR',
                'sale_returns',
                'return_number',
            ) ?? ('SR-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4)));

            $saleReturn = SaleReturn::query()->create([
                'customer_id' => $customerId,
                'store_id' => $storeId,
                'order_id' => $order?->id,
                'return_number' => $returnNumber,
                'return_date' => $validated['return_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'tax_exempt' => $request->boolean('tax_exempt'),
                'total_amount' => $totalAmount,
                'settlement_method' => $validated['settlement_method'],
                'status' => 'completed',
                'reason' => $validated['reason'] ?? null,
            ]);

            foreach ($lines as $line) {
                $saleReturn->items()->create($line);
            }

            foreach ($quantities as $productId => $qty) {
                $stock = $stocks[$productId];
                $oldQty = (int) $stock->quantity;
                $oldUnitCost = (float) ($stock->average_cost ?: $stock->last_purchase_price ?: $stock->cost ?: 0);
                $newQty = $oldQty + $qty;
                $newAverageCost = $newQty > 0
                    ? round((($oldQty * $oldUnitCost) + (float) $stockCostTotals[$productId]) / $newQty, 2)
                    : 0;

                $stock->update([
                    'quantity' => $newQty,
                    'total_stock' => $newQty,
                    'average_cost' => $newAverageCost,
                ]);
                app(ProductUnitCostService::class)->syncStock($stock->fresh());
                ProductStoreMarkup::refreshSellingPrice($stock->fresh() ?? $stock);
            }

            app(AccountingService::class)->postSaleReturn($saleReturn->load('items'));

            if ($saleReturn->settlement_method === 'customer_balance') {
                $balanceQuery = CustomerLedgerEntry::query()
                    ->where('customer_id', $customerId)
                    ->when(
                        $saleReturn->order_id,
                        fn ($query) => $query->where('order_id', $saleReturn->order_id),
                        fn ($query) => $query->whereNull('order_id'),
                    );
                $balance = (float) ($balanceQuery->latest('id')->value('balance_after') ?? 0);
                CustomerLedgerEntry::query()->create([
                    'customer_id' => $customerId,
                    'order_id' => $saleReturn->order_id,
                    'entry_type' => 'adjustment',
                    'entry_date' => $returnDate,
                    'description' => 'Sale return '.$saleReturn->return_number,
                    'debit' => 0,
                    'credit' => $totalAmount,
                    'balance_after' => round($balance - $totalAmount, 2),
                    'meta' => ['sale_return_id' => $saleReturn->id],
                ]);
            }
        });

        return redirect()->route('admin.sale-returns.index')->with('success', 'Sale return recorded.');
    }
}

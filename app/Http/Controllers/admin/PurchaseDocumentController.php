<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\Concerns\NormalizesPurchaseLines;
use App\Http\Controllers\admin\Concerns\ReceivesPurchaseIntoStock;
use App\Http\Controllers\Controller;
use App\Services\AccountingService;
use App\Services\CreditLimitService;
use App\Services\DatatablePaginationService;
use App\Services\DraftReferenceService;
use App\Services\RecordNavigator;
use App\Services\StoreSequentialNumberService;
use App\Models\BatchPurchase;
use App\Models\BatchPurchaseItem;
use App\Models\Category;
use App\Models\GoodsReceiptNote;
use App\Models\GoodsReceiptNoteItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Stock;
use App\Models\Store;
use App\Models\StorePurchase;
use App\Models\StorePurchaseItem;
use App\Models\Supplier;
use App\Models\SupplierPurchaseInvoice;
use App\Models\SupplierPurchaseInvoiceItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PurchaseDocumentController extends Controller
{
    use NormalizesPurchaseLines;
    use ReceivesPurchaseIntoStock;

    // public function __construct()
    // {
    //     dd('Working');
    //     parent::__construct();
    // }

    private function uniqueRef(string $prefix): string
    {
        return $prefix . '-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
    }

    private function resolvePerPage(Request $request): int
    {
        $service = app(DatatablePaginationService::class);
        $options = $service->lengthOptions();
        $raw = trim((string) $request->query('per_page', ''));

        if ($raw === 'all') {
            return 10000;
        }

        if ($raw !== '' && ctype_digit($raw)) {
            $requested = (int) $raw;
            if ($requested > 0 && $requested <= 10000) {
                return $requested;
            }
        }

        $default = $service->defaultLength();

        return in_array($default, $options, true) ? $default : ($options[0] ?? 25);
    }

    /* ---------- Purchase orders ---------- */

    public function purchaseOrdersIndex(Request $request): View
    {
        $perPage = $this->resolvePerPage($request);
        $orders = PurchaseOrder::query()
            ->with(['supplier', 'purchaseInvoices', 'goodsReceiptNotes'])
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $orders->each(fn (PurchaseOrder $order) => $order->syncStatusFromDocuments());

        return view('admin.purchases.purchase-orders-index', [
            'orders' => $orders,
            'datatableConfig' => app(DatatablePaginationService::class)->config('purchase-orders'),
        ]);
    }

    public function purchaseOrdersShow(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['supplier', 'store', 'items', 'purchaseInvoices', 'goodsReceiptNotes']);
        $purchaseOrder->syncStatusFromDocuments();
        $recordNav = $this->recordNav(PurchaseOrder::query()->orderByDesc('id'), $purchaseOrder->id, 'admin.purchase-orders.show');

        return view('admin.purchases.purchase-order-show', compact('purchaseOrder', 'recordNav'));
    }

    public function purchaseOrdersCreate(): View
    {
        $suppliers = Supplier::query()->where('status', 1)->orderBy('company_name')->get();
        $stores = Store::query()->orderBy('name')->get();
        $selectedStoreId = auth()->user()->selected_store_id ?? null;

        return view('admin.purchases.purchase-orders-create', compact('suppliers', 'stores', 'selectedStoreId'));
    }

    public function purchaseOrdersStore(Request $request): RedirectResponse
    {
        $validated = $request->validate(array_merge([
            'supplier_id' => 'required|exists:suppliers,id',
            'store_id' => 'required|exists:stores,id',
            'order_date' => 'required|date',
            // Old global order tax: 'tax_percent' => 'nullable|numeric|min:0|max:100',
            'tax_exempt' => 'nullable|boolean',
            'notes' => 'nullable|string|max:2000',
        ], $this->invoiceStyleLineValidationRules()));

        $lines = $this->resolvePurchaseLines($request);
        if (count($lines) === 0) {
            return back()->withErrors(['lines' => 'Add at least one product.'])->withInput();
        }

        // Old: $taxPercent = (float) ($validated['tax_percent'] ?? 0);
        // [$subtotal, $taxAmount, $total] = $this->totalsFromLines($lines, $taxPercent);
        [$subtotal, $taxAmount, $total] = $this->totalsFromPerLineTax($lines);
        $taxExempt = $request->boolean('tax_exempt');

        $po = null;

        DB::transaction(function () use ($validated, $lines, $subtotal, $taxAmount, $total, $taxExempt, &$po) {
            $poNumber = app(StoreSequentialNumberService::class)->issueNextForStore(
                (int) $validated['store_id'],
                'purchase_order_prefix',
                'next_purchase_order_number',
                'PO',
                'purchase_orders',
                'po_number'
            );
            if ($poNumber === null) {
                $poNumber = $this->uniqueRef('PO');
            }

            $po = PurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'store_id' => $validated['store_id'],
                'po_number' => $poNumber,
                'order_date' => $validated['order_date'],
                'status' => 'draft',
                'subtotal' => $subtotal,
                'tax_percent' => 0,
                'tax_amount' => $taxAmount,
                'tax_exempt' => $taxExempt,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);
            foreach ($lines as $line) {
                $po->items()->create($line);
            }

            $this->postPurchaseGl(
                'purchase_order',
                (int) $po->id,
                $po->order_date->format('Y-m-d'),
                $po->po_number,
                'Purchase order — supplier commitment',
                (float) $total,
                $po->store_id ? (int) $po->store_id : null,
            );
        });

        // Old: return redirect()->route('admin.purchase-orders.index')->with('success', 'Purchase order created.');
        return redirect()
            ->route('admin.purchase-orders.show', $po)
            ->with('success', 'Purchase order created.');
    }

    public function purchaseOrderItems(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $storeId = $request->integer('store_id') ?: $purchaseOrder->store_id;
        $purchaseOrder->load('items');

        $items = $this->mapDocumentItemsForPicker($purchaseOrder->items, $storeId, 'qty', 'unit_price');

        return response()->json([
            'purchase_order' => [
                'id' => $purchaseOrder->id,
                'supplier_id' => $purchaseOrder->supplier_id,
                'store_id' => $purchaseOrder->store_id,
                'po_number' => $purchaseOrder->po_number,
            ],
            'items' => $items,
        ]);
    }

    public function purchaseOrderGrnContext(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $storeId = $request->integer('store_id') ?: $purchaseOrder->store_id;
        $invoiceId = $request->integer('invoice_id') ?: null;
        $usedInvoiceIds = $this->purchaseInvoiceIdsWithCompletedGrn();

        $purchaseOrder->load('items');

        $confirmedInvoices = SupplierPurchaseInvoice::query()
            ->where('purchase_order_id', $purchaseOrder->id)
            ->where('invoice_status', 'Confirmed')
            ->whereNotIn('id', $usedInvoiceIds)
            ->orderByDesc('id')
            ->get(['id', 'internal_invoice_number', 'invoice_number', 'invoice_date', 'total'])
            ->filter(fn (SupplierPurchaseInvoice $inv) => ! $inv->isAssignedToConfirmedBatchPurchase())
            ->values();

        $invoiceList = $confirmedInvoices->map(fn (SupplierPurchaseInvoice $inv) => [
            'id' => $inv->id,
            'label' => $this->purchaseInvoiceLabel($inv),
            'invoice_date' => $inv->invoice_date?->format('Y-m-d'),
            'total' => (float) $inv->total,
        ])->values();

        $selectedInvoice = null;
        if ($invoiceId) {
            $selectedInvoice = $confirmedInvoices->firstWhere('id', $invoiceId);
        } elseif ($confirmedInvoices->count() === 1) {
            $selectedInvoice = $confirmedInvoices->first();
        }

        $poItems = $this->mapDocumentItemsForPicker($purchaseOrder->items, $storeId, 'qty', 'unit_price');
        $piItems = [];
        $discrepancies = [];

        $hasAnyConfirmedInvoice = SupplierPurchaseInvoice::query()
            ->where('purchase_order_id', $purchaseOrder->id)
            ->where('invoice_status', 'Confirmed')
            ->exists();

        if (! $this->purchaseOrderIsAvailableForGrn($purchaseOrder)
            || ($hasAnyConfirmedInvoice && $confirmedInvoices->isEmpty())) {
            return response()->json([
                'purchase_order' => [
                    'id' => $purchaseOrder->id,
                    'supplier_id' => $purchaseOrder->supplier_id,
                    'store_id' => $purchaseOrder->store_id,
                    'po_number' => $purchaseOrder->po_number,
                ],
                'purchase_invoices' => [],
                'has_confirmed_invoice' => false,
                'already_received' => true,
                'message' => $hasAnyConfirmedInvoice
                    ? 'All confirmed purchase invoices for this purchase order have already been received.'
                    : 'This purchase order has already been received via a goods receipt note.',
                'purchase_order_items' => $poItems,
                'items_to_receive' => [],
                'discrepancies' => [],
            ]);
        }

        if ($selectedInvoice) {
            if ($selectedInvoice->isAssignedToConfirmedBatchPurchase()) {
                return response()->json([
                    'purchase_order' => [
                        'id' => $purchaseOrder->id,
                        'supplier_id' => $purchaseOrder->supplier_id,
                        'store_id' => $purchaseOrder->store_id,
                        'po_number' => $purchaseOrder->po_number,
                    ],
                    'purchase_invoices' => $invoiceList,
                    'has_confirmed_invoice' => false,
                    'batch_locked' => true,
                    'message' => 'The linked purchase invoice is part of a confirmed batch purchase and cannot be used for GRN.',
                    'purchase_order_items' => $poItems,
                    'items_to_receive' => [],
                    'discrepancies' => [],
                ]);
            }

            $selectedInvoice->load('items');
            $piItems = $this->mapDocumentItemsForPicker($selectedInvoice->items, $storeId, 'qty', 'unit_price');
            $discrepancies = $this->comparePurchaseDocumentLines($poItems, $piItems);
        }

        return response()->json([
            'purchase_order' => [
                'id' => $purchaseOrder->id,
                'supplier_id' => $purchaseOrder->supplier_id,
                'store_id' => $purchaseOrder->store_id,
                'po_number' => $purchaseOrder->po_number,
            ],
            'purchase_invoices' => $invoiceList,
            'has_confirmed_invoice' => $confirmedInvoices->isNotEmpty(),
            'selected_invoice_id' => $selectedInvoice?->id,
            'purchase_order_items' => $poItems,
            'items_to_receive' => $piItems,
            'discrepancies' => $discrepancies,
        ]);
    }

    public function purchaseInvoiceItems(Request $request, SupplierPurchaseInvoice $invoice): JsonResponse
    {
        if ($invoice->invoice_status !== 'Confirmed') {
            return response()->json(['message' => 'Only confirmed purchase invoices can be used for goods receipt.'], 422);
        }

        if ($this->purchaseInvoiceAlreadyReceived((int) $invoice->id)) {
            return response()->json(['message' => 'This purchase invoice has already been received via a goods receipt note.'], 422);
        }

        if ($invoice->isAssignedToConfirmedBatchPurchase()) {
            return response()->json([
                'message' => 'This purchase invoice is part of a confirmed batch purchase and cannot be used for GRN.',
            ], 422);
        }

        $storeId = $request->integer('store_id') ?: $invoice->store_id;
        $invoice->load('items');

        $items = $this->mapDocumentItemsForPicker($invoice->items, $storeId, 'qty', 'unit_price');

        return response()->json([
            'purchase_invoice' => [
                'id' => $invoice->id,
                'supplier_id' => $invoice->supplier_id,
                'store_id' => $invoice->store_id,
                'purchase_order_id' => $invoice->purchase_order_id,
                'label' => $this->purchaseInvoiceLabel($invoice),
            ],
            'items' => $items,
        ]);
    }

  /**
     * @param  iterable<int, mixed>  $items
     * @return array<int, array<string, mixed>>
     */
    private function mapDocumentItemsForPicker(iterable $items, ?int $storeId, string $qtyField, string $priceField): array
    {
        $mapped = [];
        foreach ($items as $item) {
            $sku = '';
            $category = '';
            $categoryId = '';

            if ($item->product_id) {
                $stockQuery = DB::table('stocks')
                    ->leftJoin('categories', 'stocks.category_id', '=', 'categories.id')
                    ->where('stocks.product_id', $item->product_id)
                    ->select('stocks.sku', 'stocks.category_id', 'categories.name as category_name');

                // Prefer this store's stock row, then any store — products are not store-owned.
                // $stock = DB::table('stocks')
                //     ->leftJoin('categories', 'stocks.category_id', '=', 'categories.id')
                //     ->where('stocks.store_id', $storeId)
                //     ->where('stocks.product_id', $item->product_id)
                //     ->select('stocks.sku', 'stocks.category_id', 'categories.name as category_name')
                //     ->first();
                $stock = (clone $stockQuery)->when($storeId, fn ($q) => $q->where('stocks.store_id', $storeId))->first()
                    ?: (clone $stockQuery)->first();

                if ($stock) {
                    $sku = (string) ($stock->sku ?? '');
                    $categoryId = $stock->category_id !== null ? (string) $stock->category_id : '';
                    $category = (string) ($stock->category_name ?? '');
                }
            }

            $mapped[] = [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'qty' => (int) $item->{$qtyField},
                'unit_price' => (float) $item->{$priceField},
                'is_taxable' => (bool) ($item->is_taxable ?? true),
                'tax_rate' => (float) ($item->tax_rate ?? 0),
                'sku' => $sku,
                'category' => $category,
                'category_id' => $categoryId,
            ];
        }

        return $mapped;
    }

    /** @param  array<int, array<string, mixed>>  $poItems
     * @param  array<int, array<string, mixed>>  $piItems
     * @return array<int, array<string, mixed>>
     */
    private function comparePurchaseDocumentLines(array $poItems, array $piItems): array
    {
        $poByProduct = collect($poItems)->keyBy(fn ($line) => (string) ($line['product_id'] ?? $line['product_name']));
        $piByProduct = collect($piItems)->keyBy(fn ($line) => (string) ($line['product_id'] ?? $line['product_name']));
        $discrepancies = [];

        foreach ($poByProduct as $key => $poLine) {
            $piLine = $piByProduct->get($key);
            if (! $piLine) {
                $discrepancies[] = [
                    'type' => 'missing_on_invoice',
                    'product_name' => $poLine['product_name'],
                    'po_qty' => (int) $poLine['qty'],
                    'pi_qty' => 0,
                    'po_unit_price' => (float) $poLine['unit_price'],
                    'pi_unit_price' => null,
                    'message' => 'On PO but not on purchase invoice',
                ];
                continue;
            }

            $qtyMismatch = (int) $poLine['qty'] !== (int) $piLine['qty'];
            $priceMismatch = abs((float) $poLine['unit_price'] - (float) $piLine['unit_price']) > 0.009;
            if ($qtyMismatch || $priceMismatch) {
                $discrepancies[] = [
                    'type' => 'mismatch',
                    'product_name' => $poLine['product_name'],
                    'po_qty' => (int) $poLine['qty'],
                    'pi_qty' => (int) $piLine['qty'],
                    'po_unit_price' => (float) $poLine['unit_price'],
                    'pi_unit_price' => (float) $piLine['unit_price'],
                    'message' => trim(
                        ($qtyMismatch ? 'Quantity differs. ' : '')
                        . ($priceMismatch ? 'Unit price differs.' : '')
                    ),
                ];
            }
        }

        foreach ($piByProduct as $key => $piLine) {
            if (! $poByProduct->has($key)) {
                $discrepancies[] = [
                    'type' => 'extra_on_invoice',
                    'product_name' => $piLine['product_name'],
                    'po_qty' => 0,
                    'pi_qty' => (int) $piLine['qty'],
                    'po_unit_price' => null,
                    'pi_unit_price' => (float) $piLine['unit_price'],
                    'message' => 'On purchase invoice but not on PO',
                ];
            }
        }

        return $discrepancies;
    }

    private function purchaseInvoiceLabel(SupplierPurchaseInvoice $invoice): string
    {
        $internal = trim((string) ($invoice->internal_invoice_number ?? ''));
        $supplier = trim((string) ($invoice->invoice_number ?? ''));

        if ($internal !== '' && $supplier !== '') {
            return $internal . ' (Supplier: ' . $supplier . ')';
        }

        return $internal !== '' ? $internal : ($supplier !== '' ? $supplier : ('#' . $invoice->id));
    }

    /** @return array<int, int> */
    private function purchaseInvoiceIdsWithCompletedGrn(): array
    {
        return GoodsReceiptNote::query()
            ->where('status', 'received')
            ->whereNotNull('supplier_purchase_invoice_id')
            ->pluck('supplier_purchase_invoice_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function purchaseInvoiceAlreadyReceived(int $invoiceId): bool
    {
        return in_array($invoiceId, $this->purchaseInvoiceIdsWithCompletedGrn(), true);
    }

    private function purchaseOrderIsAvailableForGrn(PurchaseOrder $purchaseOrder): bool
    {
        $usedInvoiceIds = $this->purchaseInvoiceIdsWithCompletedGrn();

        $openConfirmedInvoices = SupplierPurchaseInvoice::query()
            ->where('purchase_order_id', $purchaseOrder->id)
            ->where('invoice_status', 'Confirmed')
            ->whereNotIn('id', $usedInvoiceIds)
            ->get()
            ->contains(fn (SupplierPurchaseInvoice $invoice) => ! $invoice->isAssignedToConfirmedBatchPurchase());

        if ($openConfirmedInvoices) {
            return true;
        }

        return ! GoodsReceiptNote::query()
            ->where('purchase_order_id', $purchaseOrder->id)
            ->where('status', 'received')
            ->exists();
    }

    /** @return array<string, string>|null */
    private function validateGrnPurchaseLinks(?int $purchaseOrderId, ?int $purchaseInvoiceId): ?array
    {
        if ($purchaseOrderId) {
            $po = PurchaseOrder::query()->find($purchaseOrderId);
            if (! $po) {
                return ['purchase_order_id' => 'Selected purchase order was not found.'];
            }

            if (! $purchaseInvoiceId) {
                return ['supplier_purchase_invoice_id' => 'A confirmed purchase invoice linked to this purchase order is required before receiving goods.'];
            }

            $invoice = SupplierPurchaseInvoice::query()->find($purchaseInvoiceId);
            if (! $invoice || $invoice->invoice_status !== 'Confirmed') {
                return ['supplier_purchase_invoice_id' => 'Select a confirmed purchase invoice for this purchase order.'];
            }

            if ((int) $invoice->purchase_order_id !== (int) $purchaseOrderId) {
                return ['supplier_purchase_invoice_id' => 'The selected purchase invoice is not linked to the selected purchase order.'];
            }
            if ($invoice->isAssignedToConfirmedBatchPurchase()) {
                return ['supplier_purchase_invoice_id' => 'This purchase invoice is part of a confirmed batch purchase and cannot be used for GRN.'];
            }

            if ($this->purchaseInvoiceAlreadyReceived((int) $purchaseInvoiceId)) {
                return ['supplier_purchase_invoice_id' => 'This purchase invoice has already been received via a goods receipt note.'];
            }

            return null;
        }

        if ($purchaseInvoiceId) {
            $invoice = SupplierPurchaseInvoice::query()->find($purchaseInvoiceId);
            if (! $invoice || $invoice->invoice_status !== 'Confirmed') {
                return ['supplier_purchase_invoice_id' => 'Only confirmed purchase invoices can be used for goods receipt.'];
            }
            if ($invoice->isAssignedToConfirmedBatchPurchase()) {
                return ['supplier_purchase_invoice_id' => 'This purchase invoice is part of a confirmed batch purchase and cannot be used for GRN.'];
            }
            if ($this->purchaseInvoiceAlreadyReceived((int) $purchaseInvoiceId)) {
                return ['supplier_purchase_invoice_id' => 'This purchase invoice has already been received via a goods receipt note.'];
            }
        }

        return null;
    }

    /* ---------- Add purchase (store purchase) ---------- */

    public function storePurchasesIndex(Request $request): View
    {
        $perPage = $this->resolvePerPage($request);
        $purchases = StorePurchase::query()
            ->with('supplier')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.purchases.store-purchases-index', [
            'purchases' => $purchases,
            'datatableConfig' => app(DatatablePaginationService::class)->config('store-purchases'),
        ]);
    }

    public function storePurchasesShow(StorePurchase $storePurchase): View
    {
        $storePurchase->load(['supplier', 'store', 'items']);
        $recordNav = $this->recordNav(StorePurchase::query()->orderByDesc('id'), $storePurchase->id, 'admin.store-purchases.show');

        return view('admin.purchases.store-purchase-show', compact('storePurchase', 'recordNav'));
    }

    public function storePurchasesCreate(): View
    {
        $suppliers = Supplier::query()->where('status', 1)->orderBy('company_name')->get();
        $stores = Store::query()->orderBy('name')->get();
        $selectedStoreId = auth()->user()->selected_store_id ?? null;

        return view('admin.purchases.store-purchases-create', compact('suppliers', 'stores', 'selectedStoreId'));
    }

    public function storePurchasesStore(Request $request): RedirectResponse
    {
        $validated = $request->validate(array_merge([
            'supplier_id' => 'required|exists:suppliers,id',
            'store_id' => 'required|exists:stores,id',
            'purchase_date' => 'required|date',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:2000',
        ], $this->invoiceStyleLineValidationRules()));

        $lines = $this->resolvePurchaseLines($request);
        if (count($lines) === 0) {
            return back()->withErrors(['lines' => 'Add at least one product.'])->withInput();
        }

        $taxPercent = (float) ($validated['tax_percent'] ?? 0);
        [$subtotal, $taxAmount, $total] = $this->totalsFromLines($lines, $taxPercent);

        $doc = null;
        DB::transaction(function () use ($validated, $lines, $subtotal, $taxAmount, $total, $taxPercent, &$doc) {
            $doc = StorePurchase::create([
                'supplier_id' => $validated['supplier_id'],
                'store_id' => $validated['store_id'],
                'purchase_number' => $this->uniqueRef('PUR'),
                'purchase_date' => $validated['purchase_date'],
                'status' => 'completed',
                'subtotal' => $subtotal,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);
            foreach ($lines as $line) {
                $doc->items()->create($line);
            }

            $this->postPurchaseGl(
                'store_purchase',
                (int) $doc->id,
                $doc->purchase_date->format('Y-m-d'),
                $doc->purchase_number,
                'Store purchase from supplier',
                (float) $total,
                $doc->store_id ? (int) $doc->store_id : null,
            );
        });

        // Old: return redirect()->route('admin.store-purchases.index')->with('success', 'Purchase recorded.');
        return redirect()
            ->route('admin.store-purchases.show', $doc)
            ->with('success', 'Purchase recorded.');
    }

    /* ---------- Purchase returns ---------- */

    public function purchaseReturnsIndex(Request $request): View
    {
        $perPage = $this->resolvePerPage($request);
        $returns = PurchaseReturn::query()
            ->with('supplier')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.purchases.purchase-returns-index', [
            'returns' => $returns,
            'datatableConfig' => app(DatatablePaginationService::class)->config('purchase-returns'),
        ]);
    }

    public function purchaseReturnsCreate(): View
    {
        $suppliers = Supplier::query()->where('status', 1)->orderBy('company_name')->get();
        $stores = Store::query()->orderBy('name')->get();
        $invoices = SupplierPurchaseInvoice::query()->orderByDesc('invoice_date')->limit(200)->get();
        $selectedStoreId = auth()->user()->selected_store_id ?? null;

        return view('admin.purchases.purchase-returns-create', compact('suppliers', 'stores', 'invoices', 'selectedStoreId'));
    }

    public function purchaseReturnsStore(Request $request): RedirectResponse
    {
        $validated = $request->validate(array_merge([
            'supplier_id' => 'required|exists:suppliers,id',
            'store_id' => 'required|exists:stores,id',
            'supplier_purchase_invoice_id' => 'nullable|exists:supplier_purchase_invoices,id',
            'return_date' => 'required|date',
            'tax_exempt' => 'nullable|boolean',
            'reason' => 'nullable|string|max:2000',
        ], $this->invoiceStyleLineValidationRules()));

        $lines = $this->resolvePurchaseLines($request);
        if (count($lines) === 0) {
            return back()->withErrors(['lines' => 'Add at least one product.'])->withInput();
        }

        // Old: $totalAmount = round(array_sum(array_column($lines, 'line_total')), 2);
        [$subtotal, $taxAmount, $totalAmount] = $this->totalsFromPerLineTax($lines);
        $taxExempt = $request->boolean('tax_exempt');

        DB::transaction(function () use ($validated, $lines, $subtotal, $taxAmount, $totalAmount, $taxExempt) {
            $returnNumber = app(StoreSequentialNumberService::class)->issueNextForStore(
                (int) $validated['store_id'],
                'purchase_return_prefix',
                'next_purchase_return_number',
                'PR',
                'purchase_returns',
                'return_number'
            );
            if ($returnNumber === null) {
                $returnNumber = $this->uniqueRef('PR');
            }

            $ret = PurchaseReturn::create([
                'supplier_id' => $validated['supplier_id'],
                'store_id' => $validated['store_id'],
                'supplier_purchase_invoice_id' => $validated['supplier_purchase_invoice_id'] ?? null,
                'return_number' => $returnNumber,
                'return_date' => $validated['return_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'tax_exempt' => $taxExempt,
                'total_amount' => $totalAmount,
                'status' => 'completed',
                'reason' => $validated['reason'] ?? null,
            ]);
            foreach ($lines as $line) {
                $ret->items()->create($line);
            }

            $this->removeLinesFromStock((int) $validated['store_id'], $lines);

            try {
                app(AccountingService::class)->postPurchaseReturn(
                    'purchase_return',
                    (int) $ret->id,
                    $ret->return_date->format('Y-m-d'),
                    $ret->return_number,
                    'Purchase return to supplier',
                    (float) $totalAmount,
                    $ret->store_id ? (int) $ret->store_id : null,
                );
            } catch (\Throwable $e) {
                report($e);
            }
        });

        return redirect()->route('admin.purchase-returns.index')->with('success', 'Purchase return recorded.');
    }

    /* ---------- GRN ---------- */

    public function grnIndex(Request $request): View
    {
        $perPage = $this->resolvePerPage($request);
        $grns = GoodsReceiptNote::query()
            ->with(['supplier', 'purchaseOrder', 'supplierPurchaseInvoice'])
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.purchases.grn-index', [
            'grns' => $grns,
            'datatableConfig' => app(DatatablePaginationService::class)->config('goods-receipt-notes'),
        ]);
    }

    public function grnShow(GoodsReceiptNote $grn): View
    {
        $grn->load(['supplier', 'store', 'items', 'purchaseOrder', 'supplierPurchaseInvoice']);
        $recordNav = $this->recordNav(GoodsReceiptNote::query()->orderByDesc('id'), $grn->id, 'admin.goods-receipt-notes.show');

        return view('admin.purchases.grn-show', compact('grn', 'recordNav'));
    }

    public function grnCreate(Request $request): View
    {
        $suppliers = Supplier::query()->where('status', 1)->orderBy('company_name')->get();
        $stores = Store::query()->orderBy('name')->get();
        $usedInvoiceIds = $this->purchaseInvoiceIdsWithCompletedGrn();
        $purchaseOrders = PurchaseOrder::query()
            ->orderByDesc('order_date')
            ->limit(200)
            ->get()
            ->filter(fn (PurchaseOrder $po) => $this->purchaseOrderIsAvailableForGrn($po))
            ->values();
        $purchaseInvoices = SupplierPurchaseInvoice::query()
            ->where('invoice_status', 'Confirmed')
            ->whereNotIn('id', $usedInvoiceIds)
            ->orderByDesc('invoice_date')
            ->limit(200)
            ->get()
            ->filter(fn (SupplierPurchaseInvoice $invoice) => ! $invoice->isAssignedToConfirmedBatchPurchase())
            ->values();
        $selectedStoreId = auth()->user()->selected_store_id ?? null;
        $selectedPurchaseOrderId = $request->integer('purchase_order_id') ?: null;
        $selectedPurchaseInvoiceId = $request->integer('supplier_purchase_invoice_id') ?: null;

        return view('admin.purchases.grn-create', compact(
            'suppliers',
            'stores',
            'purchaseOrders',
            'purchaseInvoices',
            'selectedStoreId',
            'selectedPurchaseOrderId',
            'selectedPurchaseInvoiceId'
        ));
    }

    public function grnStore(Request $request): RedirectResponse
    {
        $validated = $request->validate(array_merge([
            'supplier_id' => 'required|exists:suppliers,id',
            'store_id' => 'required|exists:stores,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'supplier_purchase_invoice_id' => 'nullable|exists:supplier_purchase_invoices,id',
            'grn_date' => 'required|date',
            'tax_exempt' => 'nullable|boolean',
            'notes' => 'nullable|string|max:2000',
        ], $this->invoiceStyleLineValidationRules()));

        $linkError = $this->validateGrnPurchaseLinks(
            isset($validated['purchase_order_id']) ? (int) $validated['purchase_order_id'] : null,
            isset($validated['supplier_purchase_invoice_id']) ? (int) $validated['supplier_purchase_invoice_id'] : null,
        );
        if ($linkError) {
            return back()->withErrors($linkError)->withInput();
        }

        $purchaseLines = $this->resolvePurchaseLines($request);
        $lines = $this->mapLinesForGrn($purchaseLines);
        if (count($lines) === 0) {
            return back()->withErrors(['lines' => 'Add at least one product.'])->withInput();
        }

        [$subtotal, $taxAmount, $grnTotal] = $this->totalsFromPerLineTax($purchaseLines);
        $taxExempt = $request->boolean('tax_exempt');
        $supplierName = Supplier::query()->where('id', $validated['supplier_id'])->value('company_name');

        $grn = null;

        DB::transaction(function () use ($request, $validated, $lines, $purchaseLines, $supplierName, $subtotal, $taxAmount, $grnTotal, $taxExempt, &$grn) {
            $grnNumber = app(StoreSequentialNumberService::class)->issueNextForStore(
                (int) $validated['store_id'],
                'grn_prefix',
                'next_grn_number',
                'GRN',
                'goods_receipt_notes',
                'grn_number'
            );
            if ($grnNumber === null) {
                $grnNumber = $this->uniqueRef('GRN');
            }

            $grn = GoodsReceiptNote::create([
                'supplier_id' => $validated['supplier_id'],
                'store_id' => $validated['store_id'],
                'purchase_order_id' => $validated['purchase_order_id'] ?? null,
                'supplier_purchase_invoice_id' => $validated['supplier_purchase_invoice_id'] ?? null,
                'grn_number' => $grnNumber,
                'grn_date' => $validated['grn_date'],
                'status' => 'received',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $grnTotal,
                'tax_exempt' => $taxExempt,
                'notes' => $validated['notes'] ?? null,
            ]);
            foreach ($lines as $line) {
                $grn->items()->create($line);
            }

            // Increase stock quantity at the receiving store (was not applied before).
            $this->receiveLinesIntoStock(
                (int) $validated['store_id'],
                $purchaseLines,
                $request,
                $supplierName,
                $validated['grn_date'],
            );

            // Old: $grnTotal = round(array_sum(array_column($lines, 'line_total')), 2);
            $poId = $validated['purchase_order_id'] ?? null;
            $accounting = app(AccountingService::class);
            if (!$poId || !$accounting->hasPosting('purchase_order', (int) $poId)) {
                $this->postPurchaseGl(
                    'goods_receipt_note',
                    (int) $grn->id,
                    $grn->grn_date->format('Y-m-d'),
                    $grn->grn_number,
                    'Goods receipt note',
                    $grnTotal,
                    $grn->store_id ? (int) $grn->store_id : null,
                );
            }

            $this->syncPurchaseOrderStatus($validated['purchase_order_id'] ?? null);
        });

        // Old: return redirect()->route('admin.goods-receipt-notes.index')->with('success', 'GRN saved and stock quantities updated.');
        return redirect()
            ->route('admin.goods-receipt-notes.show', $grn)
            ->with('success', 'GRN saved and stock quantities updated.');
    }

    /* ---------- Supplier purchase invoices ---------- */

    public function purchaseInvoicesIndex(Request $request): View
    {
        $perPage = $this->resolvePerPage($request);
        $invoices = SupplierPurchaseInvoice::query()
            ->with('supplier')
            ->where('invoice_status', 'Confirmed')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.purchases.purchase-invoices-index', [
            'invoices' => $invoices,
            'datatableConfig' => app(DatatablePaginationService::class)->config('supplier-purchase-invoices'),
        ]);
    }

    public function purchaseInvoicesShow(SupplierPurchaseInvoice $invoice): View
    {
        $invoice->load(['supplier', 'store', 'items', 'purchaseOrder']);
        $recordNav = $this->recordNav(
            SupplierPurchaseInvoice::query()->where('invoice_status', 'Confirmed')->orderByDesc('id'),
            $invoice->id,
            'admin.supplier-purchase-invoices.show',
        );

        return view('admin.purchases.purchase-invoice-show', compact('invoice', 'recordNav'));
    }

    public function checkSupplierCreditLimit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'total_amount' => 'required|numeric|min:0',
            'invoice_id' => 'nullable|integer|exists:supplier_purchase_invoices,id',
        ]);

        $supplier = Supplier::findOrFail($validated['supplier_id']);
        $excludeId = ! empty($validated['invoice_id']) ? (int) $validated['invoice_id'] : null;
        $assessment = app(CreditLimitService::class)->assessSupplierCredit(
            $supplier,
            (float) $validated['total_amount'],
            $excludeId
        );

        return response()->json($assessment);
    }

    public function purchaseInvoicesCreate(Request $request): View
    {
        $suppliers = Supplier::query()->where('status', 1)->orderBy('company_name')->get();
        $stores = Store::query()->orderBy('name')->get();
        $purchaseOrders = PurchaseOrder::query()->with('supplier')->orderByDesc('order_date')->limit(200)->get();
        $selectedStoreId = auth()->user()->selected_store_id ?? null;
        $selectedPurchaseOrderId = $request->integer('purchase_order_id') ?: null;

        $holdInvoice = null;
        $holdInvoicePayload = null;
        if ($request->filled('hold')) {
            $holdInvoice = SupplierPurchaseInvoice::with('items')
                ->where('id', $request->integer('hold'))
                ->where('invoice_status', 'Draft')
                ->firstOrFail();
            $holdInvoicePayload = $this->buildHoldPurchaseInvoicePayload($holdInvoice);
            $selectedPurchaseOrderId = $selectedPurchaseOrderId ?: $holdInvoice->purchase_order_id;
        }

        return view('admin.purchases.purchase-invoices-create', compact(
            'suppliers',
            'stores',
            'purchaseOrders',
            'selectedStoreId',
            'selectedPurchaseOrderId',
            'holdInvoice',
            'holdInvoicePayload'
        ));
    }

    /** @return array<string, mixed> */
    private function buildHoldPurchaseInvoicePayload(SupplierPurchaseInvoice $invoice): array
    {
        $lines = [];
        foreach ($invoice->items as $item) {
            $stock = null;
            if ($item->product_id) {
                // Prefer this store, then any store so product details are not blank after a store change.
                // $stock = Stock::query()
                //     ->where('store_id', $invoice->store_id)
                //     ->where('product_id', $item->product_id)
                //     ->first();
                $stock = Stock::query()
                    ->where('store_id', $invoice->store_id)
                    ->where('product_id', $item->product_id)
                    ->first()
                    ?: Stock::query()->where('product_id', $item->product_id)->first();
            }

            $categoryName = '';
            if ($stock && $stock->category_id) {
                $categoryName = (string) (Category::query()->whereKey($stock->category_id)->value('name') ?? '');
            }

            $lineQty = (int) $item->qty;
            $lines[] = [
                'product_id' => $item->product_id,
                'product_name' => (string) ($item->product_name ?? $stock?->product_name ?? ''),
                'sku' => (string) ($stock?->sku ?? ''),
                'category_id' => (int) ($stock?->category_id ?? 0),
                'category_name' => $categoryName,
                'qty' => $lineQty,
                'unit_price' => (float) $item->unit_price,
                'is_taxable' => (bool) ($item->is_taxable ?? true),
                'tax_rate' => (float) ($item->tax_rate ?? 0),
                'default_is_taxable' => (bool) ($stock?->is_taxable ?? true),
                'default_tax_rate' => (float) ($stock?->tax_rate ?? 0),
            ];
        }

        return [
            'invoice_id' => (int) $invoice->id,
            'supplier_id' => (int) $invoice->supplier_id,
            'store_id' => (int) $invoice->store_id,
            'purchase_order_id' => $invoice->purchase_order_id ? (int) $invoice->purchase_order_id : null,
            'internal_invoice_number' => $invoice->internal_invoice_number,
            'supplier_invoice_number' => $invoice->invoice_number,
            'invoice_date' => $invoice->invoice_date?->format('Y-m-d'),
            'due_date' => $invoice->due_date?->format('Y-m-d'),
            'tax_percent' => (float) $invoice->tax_percent,
            'tax_exempt' => (bool) ($invoice->tax_exempt ?? false),
            'notes' => (string) ($invoice->notes ?? ''),
            'credit_limit_blocked' => (bool) $invoice->credit_limit_blocked,
            'draft_reference' => $invoice->draft_reference,
            'lines' => $lines,
        ];
    }

    public function purchaseInvoicesStore(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate(array_merge([
            'supplier_id' => 'required|exists:suppliers,id',
            'store_id' => 'required|exists:stores,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            // Supplier reference — free text, not globally unique (point 15).
            'supplier_invoice_number' => 'nullable|string|max:64',
            // Old: single invoice_number field with global uniqueness.
            // 'invoice_number' => 'nullable|string|max:64|unique:supplier_purchase_invoices,invoice_number',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            // Old global order tax: 'tax_percent' => 'nullable|numeric|min:0|max:100',
            'tax_exempt' => 'nullable|boolean',
            'notes' => 'nullable|string|max:2000',
            'invoice_status' => 'nullable|in:Draft,Confirmed,Cancelled',
        ], $this->invoiceStyleLineValidationRules()));

        $onHold = ($validated['invoice_status'] ?? 'Confirmed') === 'Draft';
        $lines = $this->resolvePurchaseLines($request);
        if (count($lines) === 0) {
            return back()->withErrors(['lines' => 'Add at least one product.'])->withInput();
        }

        // Old: $taxPercent = (float) ($validated['tax_percent'] ?? 0);
        // [$subtotal, $taxAmount, $total] = $this->totalsFromLines($lines, $taxPercent);
        [$subtotal, $taxAmount, $total] = $this->totalsFromPerLineTax($lines);
        $taxExempt = $request->boolean('tax_exempt');

        $creditLimitBlocked = false;
        $supplier = Supplier::find($validated['supplier_id']);
        if ($supplier) {
            $creditMessage = app(CreditLimitService::class)->supplierWouldExceed($supplier, $total);
            if ($creditMessage) {
                $creditLimitBlocked = true;
                if (! $onHold) {
                    return back()
                        ->withErrors(['invoice_status' => $creditMessage])
                        ->withInput();
                }
            }
        }

        $createdInvoice = null;
        DB::transaction(function () use ($validated, $lines, $subtotal, $taxAmount, $total, $taxExempt, $onHold, $creditLimitBlocked, &$createdInvoice) {
            $supplierInvoiceNumber = $this->normalizeSupplierInvoiceNumber($validated['supplier_invoice_number'] ?? null);
            $internalInvoiceNumber = null;
            if (! $onHold) {
                $internalInvoiceNumber = $this->issueInternalPurchaseInvoiceNumber((int) $validated['store_id']);
            }

            $inv = SupplierPurchaseInvoice::create([
                'supplier_id' => $validated['supplier_id'],
                'store_id' => $validated['store_id'],
                'purchase_order_id' => $validated['purchase_order_id'] ?? null,
                'internal_invoice_number' => $internalInvoiceNumber,
                'invoice_number' => $supplierInvoiceNumber,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'] ?? null,
                'subtotal' => $subtotal,
                'tax_percent' => 0,
                'tax_amount' => $taxAmount,
                'tax_exempt' => $taxExempt,
                'total' => $total,
                'paid_amount' => 0,
                'due_amount' => $total,
                'status' => 'unpaid',
                'invoice_status' => $onHold ? 'Draft' : 'Confirmed',
                'credit_limit_blocked' => $creditLimitBlocked,
                'notes' => $validated['notes'] ?? null,
            ]);

            if ($onHold) {
                app(DraftReferenceService::class)->assignForPurchaseInvoice($inv);
                $inv->save();
            }

            foreach ($lines as $line) {
                $inv->items()->create($line);
            }

            if (! $onHold) {
                $this->postPurchaseGl(
                    'supplier_purchase_invoice',
                    (int) $inv->id,
                    $inv->invoice_date->format('Y-m-d'),
                    $inv->internal_invoice_number ?: $inv->invoice_number,
                    'Supplier purchase invoice',
                    (float) $total,
                    $inv->store_id ? (int) $inv->store_id : null,
                );
            }

            $createdInvoice = $inv;
        });

        $this->syncPurchaseOrderStatus($createdInvoice?->purchase_order_id);

        $message = $onHold
            ? 'Purchase invoice saved on hold.'
            : 'Purchase invoice saved.';

        // Old: return redirect()->route($onHold ? 'admin.purchase-invoices-on-hold' : 'admin.supplier-purchase-invoices.index')->with('success', $message);
        return $this->purchaseInvoiceSaveResponse($request, $createdInvoice, $onHold, $message);
    }

    public function purchaseInvoicesUpdate(Request $request, int $invoice): RedirectResponse|JsonResponse
    {
        $inv = SupplierPurchaseInvoice::with('items')->findOrFail($invoice);
        if ($inv->invoice_status !== 'Draft') {
            return back()->withErrors(['invoice' => 'Only draft purchase invoices can be edited here.']);
        }

        $validated = $request->validate(array_merge([
            'supplier_id' => 'required|exists:suppliers,id',
            'store_id' => 'required|exists:stores,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'supplier_invoice_number' => 'nullable|string|max:64',
            // Old: 'invoice_number' => 'nullable|string|max:64|unique:supplier_purchase_invoices,invoice_number,' . $inv->id,
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            // Old global order tax: 'tax_percent' => 'nullable|numeric|min:0|max:100',
            'tax_exempt' => 'nullable|boolean',
            'notes' => 'nullable|string|max:2000',
            'invoice_status' => 'nullable|in:Draft,Confirmed,Cancelled',
        ], $this->invoiceStyleLineValidationRules()));

        $onHold = ($validated['invoice_status'] ?? 'Confirmed') === 'Draft';
        $overrideCredit = $request->boolean('override_credit_limit')
            && app(CreditLimitService::class)->canOverrideCreditLimit();

        $lines = $this->resolvePurchaseLines($request);
        if (count($lines) === 0) {
            return back()->withErrors(['lines' => 'Add at least one product.'])->withInput();
        }

        // Old: $taxPercent = (float) ($validated['tax_percent'] ?? 0);
        // [$subtotal, $taxAmount, $total] = $this->totalsFromLines($lines, $taxPercent);
        [$subtotal, $taxAmount, $total] = $this->totalsFromPerLineTax($lines);
        $taxExempt = $request->boolean('tax_exempt');

        $creditLimitBlocked = (bool) $inv->credit_limit_blocked;
        $supplier = Supplier::find($validated['supplier_id']);
        if ($supplier) {
            $creditMessage = app(CreditLimitService::class)->supplierWouldExceed($supplier, $total, (int) $inv->id);
            if ($creditMessage) {
                $creditLimitBlocked = true;
                if (! $onHold && ! $overrideCredit) {
                    return back()->withErrors(['invoice_status' => $creditMessage])->withInput();
                }
            } elseif ($onHold) {
                $creditLimitBlocked = false;
            }
        }

        if (! $onHold) {
            if ($inv->credit_limit_blocked && ! $overrideCredit) {
                return back()->withErrors(['invoice_status' => 'Override permission required to confirm this invoice.'])->withInput();
            }
            // Old: supplier invoice number was mandatory on confirm.
            // if (empty($validated['invoice_number'])) {
            //     return back()->withErrors(['invoice_number' => 'Supplier invoice number is required when confirming.'])->withInput();
            // }
        }

        DB::transaction(function () use ($inv, $validated, $lines, $subtotal, $taxAmount, $total, $taxExempt, $onHold, $creditLimitBlocked, $overrideCredit) {
            $inv->items()->delete();
            $inv->update([
                'supplier_id' => $validated['supplier_id'],
                'store_id' => $validated['store_id'],
                'purchase_order_id' => $validated['purchase_order_id'] ?? null,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'] ?? null,
                'subtotal' => $subtotal,
                'tax_percent' => 0,
                'tax_amount' => $taxAmount,
                'tax_exempt' => $taxExempt,
                'total' => $total,
                'due_amount' => $total,
                'credit_limit_blocked' => $creditLimitBlocked,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $inv->items()->create($line);
            }

            if ($onHold) {
                $inv->invoice_status = 'Draft';
                $inv->internal_invoice_number = null;
                $inv->invoice_number = $this->normalizeSupplierInvoiceNumber($validated['supplier_invoice_number'] ?? null);
                app(DraftReferenceService::class)->assignForPurchaseInvoice($inv);
                $inv->save();

                return;
            }

            $inv->invoice_status = 'Confirmed';
            $inv->credit_limit_blocked = false;
            $inv->draft_reference = null;
            $inv->internal_invoice_number = $this->issueInternalPurchaseInvoiceNumber((int) $validated['store_id']);
            $inv->invoice_number = $this->normalizeSupplierInvoiceNumber($validated['supplier_invoice_number'] ?? null);
            $inv->save();

            $this->postPurchaseGl(
                'supplier_purchase_invoice',
                (int) $inv->id,
                $inv->invoice_date->format('Y-m-d'),
                $inv->internal_invoice_number ?: ('PINV/' . $inv->id),
                'Supplier purchase invoice (confirmed)',
                (float) $total,
                $inv->store_id ? (int) $inv->store_id : null,
            );
        });

        $this->syncPurchaseOrderStatus($inv->purchase_order_id);

        $message = $onHold
            ? 'Purchase invoice saved on hold.'
            : 'Purchase invoice confirmed.';

        $inv->refresh();

        // Old: return redirect()->route($onHold ? 'admin.purchase-invoices-on-hold' : 'admin.supplier-purchase-invoices.index')->with('success', $message);
        return $this->purchaseInvoiceSaveResponse($request, $inv, $onHold, $message);
    }

    public function purchaseInvoicesOnHold(Request $request): View
    {
        $perPage = $this->resolvePerPage($request);
        $invoices = SupplierPurchaseInvoice::query()
            ->with('supplier')
            ->where('invoice_status', 'Draft')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.purchases.purchase-invoices-on-hold', [
            'canOverrideCreditLimit' => app(CreditLimitService::class)->canOverrideCreditLimit(),
            'invoices' => $invoices,
            'datatableConfig' => app(DatatablePaginationService::class)->config('purchase-invoices-on-hold'),
        ]);
    }

    public function resumePurchaseInvoice(Request $request, int $invoiceId): RedirectResponse|JsonResponse
    {
        $inv = SupplierPurchaseInvoice::with('supplier')->find($invoiceId);
        if (! $inv || $inv->invoice_status !== 'Draft') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Invoice not on hold.'], 404);
            }

            return back()->withErrors(['resume' => 'Invoice not on hold.']);
        }

        $overrideCredit = $request->boolean('override_credit_limit')
            && app(CreditLimitService::class)->canOverrideCreditLimit();

        if ($inv->credit_limit_blocked && ! $overrideCredit) {
            $msg = 'This invoice is on hold because the supplier credit limit was exceeded. Only users allowed to override credit limits can confirm it.';

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $msg, 'credit_limit_blocked' => true], 422)
                : back()->withErrors(['resume' => $msg]);
        }

        $supplier = $inv->supplier;
        if ($supplier && ! $overrideCredit) {
            $creditMessage = app(CreditLimitService::class)->supplierWouldExceed($supplier, (float) $inv->due_amount);
            if ($creditMessage) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $creditMessage], 422);
                }

                return back()->withErrors(['resume' => $creditMessage]);
            }
        }

        $supplierInvoiceNumber = $this->normalizeSupplierInvoiceNumber(
            $request->input('supplier_invoice_number', $request->input('invoice_number'))
        );

        DB::transaction(function () use ($inv, $supplierInvoiceNumber) {
            $inv->internal_invoice_number = $this->issueInternalPurchaseInvoiceNumber((int) $inv->store_id);
            $inv->invoice_number = $supplierInvoiceNumber;
            $inv->invoice_status = 'Confirmed';
            $inv->credit_limit_blocked = false;
            $inv->draft_reference = null;
            $inv->save();

            $this->postPurchaseGl(
                'supplier_purchase_invoice',
                (int) $inv->id,
                $inv->invoice_date->format('Y-m-d'),
                $inv->internal_invoice_number ?: ('PINV/' . $inv->id),
                'Supplier purchase invoice (resumed)',
                (float) $inv->total,
                $inv->store_id ? (int) $inv->store_id : null,
            );
        });

        $this->syncPurchaseOrderStatus($inv->purchase_order_id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Purchase invoice resumed.',
                'redirect_url' => route('admin.supplier-purchase-invoices.show', $inv),
            ]);
        }

        return redirect()
            ->route('admin.supplier-purchase-invoices.show', $inv)
            ->with('success', 'Purchase invoice resumed.');
    }

    public function cancelPurchaseInvoice(Request $request, int $invoiceId): RedirectResponse|JsonResponse
    {
        $inv = SupplierPurchaseInvoice::find($invoiceId);
        if (! $inv || $inv->invoice_status !== 'Draft') {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Invoice not on hold.'], 404)
                : back()->withErrors(['cancel' => 'Invoice not on hold.']);
        }

        if ($inv->credit_limit_blocked && ! app(CreditLimitService::class)->canOverrideCreditLimit()) {
            $msg = 'Only users allowed to override credit limits can cancel this invoice.';

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $msg], 403)
                : back()->withErrors(['cancel' => $msg]);
        }

        $inv->invoice_status = 'Cancelled';
        $inv->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Purchase invoice cancelled.']);
        }

        return redirect()->route('admin.purchase-invoices-on-hold')->with('success', 'Purchase invoice cancelled.');
    }

    /** Point 15 — supplier reference (free text, optional). */
    private function normalizeSupplierInvoiceNumber(mixed $value): ?string
    {
        $trimmed = trim((string) ($value ?? ''));

        return $trimmed !== '' ? $trimmed : null;
    }

    /** Point 15 — sequential internal purchase invoice number (always issued on confirm). */
    private function issueInternalPurchaseInvoiceNumber(int $storeId): string
    {
        $issued = app(StoreSequentialNumberService::class)->issueNextForStore(
            $storeId,
            'purchase_invoice_prefix',
            'next_purchase_invoice_number',
            'PINV',
            'supplier_purchase_invoices',
            'internal_invoice_number'
        );

        if ($issued !== null) {
            return $issued;
        }

        // Old fallback when store sequence columns are unavailable.
        return $this->uniqueRef('PINV');
    }

    /*
    // Old: single field mixed supplier + internal sequence.
    private function resolvePurchaseInvoiceNumber(int $storeId, ?string $manualNumber): string { ... }
    */

    private function syncPurchaseOrderStatus(mixed $purchaseOrderId): void
    {
        if (empty($purchaseOrderId)) {
            return;
        }

        $po = PurchaseOrder::find((int) $purchaseOrderId);
        $po?->syncStatusFromDocuments();
    }

    private function postPurchaseGl(
        string $sourceType,
        int $sourceId,
        string $entryDate,
        string $reference,
        string $description,
        float $amount,
        ?int $storeId,
    ): void {
        try {
            app(AccountingService::class)->postPurchase(
                $sourceType,
                $sourceId,
                $entryDate,
                $reference,
                $description,
                $amount,
                $storeId,
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function purchaseInvoicePrintData(SupplierPurchaseInvoice $invoice): JsonResponse
    {
        $invoice->load(['items', 'supplier', 'store']);

        return response()->json([
            'invoice_no' => $invoice->internal_invoice_number ?: ('PINV/' . $invoice->id),
            'supplier_invoice_number' => $invoice->invoice_number ?: '—',
            'supplier_name' => $invoice->supplier->company_name ?? '—',
            'store_name' => $invoice->store->name ?? '—',
            'invoice_date' => format_date($invoice->invoice_date, '—'),
            'due_date' => format_date($invoice->due_date, '—'),
            'sub_total' => (float) $invoice->subtotal,
            'tax_amount' => (float) $invoice->tax_amount,
            'total' => (float) $invoice->total,
            'due_amount' => (float) $invoice->due_amount,
            'paid_amount' => (float) $invoice->paid_amount,
            'tax_exempt' => (bool) $invoice->tax_exempt,
            'items' => $invoice->items->map(fn (SupplierPurchaseInvoiceItem $item) => [
                'product_name' => (string) $item->product_name,
                'qty' => (int) $item->qty,
                'unit_price' => (float) $item->unit_price,
                'line_total' => (float) $item->line_total,
                'tax_rate' => (float) $item->tax_rate,
                'tax_amount' => (float) $item->tax_amount,
                'is_taxable' => (bool) $item->is_taxable,
            ])->values(),
        ]);
    }

    private function purchaseInvoiceSaveResponse(
        Request $request,
        ?SupplierPurchaseInvoice $invoice,
        bool $onHold,
        string $message,
    ): RedirectResponse|JsonResponse {
        if ($request->ajax() || $request->wantsJson()) {
            $payload = [
                'success' => true,
                'message' => $message,
                'open_print' => ! $onHold && $invoice !== null,
            ];

            if ($invoice) {
                $payload['invoice_id'] = (int) $invoice->id;
                $payload['redirect_url'] = route('admin.supplier-purchase-invoices.show', $invoice);
                // Old: redirect to list or on-hold index only.
                // $payload['redirect_url'] = route(
                //     $onHold ? 'admin.purchase-invoices-on-hold' : 'admin.supplier-purchase-invoices.index'
                // );
            }

            return response()->json($payload);
        }

        if ($invoice) {
            return redirect()
                ->route('admin.supplier-purchase-invoices.show', $invoice)
                ->with('success', $message);
        }

        // Old: return redirect()
        //     ->route($onHold ? 'admin.purchase-invoices-on-hold' : 'admin.supplier-purchase-invoices.index')
        //     ->with('success', $message);

        return redirect()
            ->route($onHold ? 'admin.purchase-invoices-on-hold' : 'admin.supplier-purchase-invoices.index')
            ->with('success', $message);
    }

    /* ---------- Batch purchases ---------- */

    public function batchPurchasesIndex(Request $request)
    {
        if ($request->ajax()) {
            $query = BatchPurchase::query()
                ->with('store')
                ->where('status', 'confirmed')
                ->orderByDesc('id');

            return datatables()
                ->eloquent($query)
                ->filter(function ($query) use ($request) {
                    $search = trim((string) $request->input('search.value', ''));
                    if ($search === '') {
                        return;
                    }

                    $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search) . '%';
                    $query->where(function ($q) use ($like) {
                        $q->where('batch_number', 'like', $like)
                            ->orWhereHas('store', fn ($store) => $store->where('name', 'like', $like));
                    });
                }, false)
                ->addColumn('batch_date', fn (BatchPurchase $row) => format_date($row->batch_date, '—'))
                ->addColumn('store_name', fn (BatchPurchase $row) => $row->store->name ?? '—')
                ->addColumn('total_formatted', fn (BatchPurchase $row) => number_format((float) $row->total, 2))
                ->addColumn('actions', fn (BatchPurchase $row) => '<a href="' . e(route('admin.batch-purchases.show', $row)) . '" class="btn btn-sm btn-outline-primary">View</a>')
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.purchases.batch-purchases-index', [
            'datatableConfig' => app(DatatablePaginationService::class)->config('batch-purchases'),
        ]);
    }

    public function batchPurchasesShow(BatchPurchase $batch): View
    {
        $batch->load(['store', 'items', 'productInvoices.supplier', 'chargeInvoices.supplier']);
        $recordNav = $this->recordNav(BatchPurchase::query()->orderByDesc('id'), $batch->id, 'admin.batch-purchases.show');

        return view('admin.purchases.batch-purchase-show', compact('batch', 'recordNav'));
    }

    public function batchPurchasesOnHold(Request $request)
    {
        if ($request->ajax()) {
            $query = BatchPurchase::query()
                ->with('store')
                ->where('status', 'draft')
                ->orderByDesc('id');

            return datatables()
                ->eloquent($query)
                ->filter(function ($query) use ($request) {
                    $search = trim((string) $request->input('search.value', ''));
                    if ($search === '') {
                        return;
                    }

                    $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search) . '%';
                    $query->where(function ($q) use ($like) {
                        $q->where('batch_number', 'like', $like)
                            ->orWhere('draft_reference', 'like', $like)
                            ->orWhereHas('store', fn ($store) => $store->where('name', 'like', $like));
                    });
                }, false)
                ->addColumn('reference', fn (BatchPurchase $row) => $row->draft_reference ?? $row->batch_number)
                ->addColumn('batch_date', fn (BatchPurchase $row) => format_date($row->batch_date, '—'))
                ->addColumn('store_name', fn (BatchPurchase $row) => $row->store->name ?? '—')
                ->addColumn('total_formatted', fn (BatchPurchase $row) => number_format((float) $row->total, 2))
                ->addColumn('action', function (BatchPurchase $row) {
                    $url = route('admin.batch-purchases.create', ['hold' => $row->id]);

                    return '<a href="' . e($url) . '" class="btn btn-sm btn-outline-primary">Continue</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.purchases.batch-purchases-on-hold', [
            'datatableConfig' => app(DatatablePaginationService::class)->config('batch-purchases-on-hold'),
        ]);
    }

    public function batchPurchasesCreate(Request $request): View
    {
        $stores = Store::query()->orderBy('name')->get();
        $selectedStoreId = auth()->user()->selected_store_id ?? null;

        $holdBatch = null;
        $holdBatchPayload = null;
        if ($request->filled('hold')) {
            $holdBatch = BatchPurchase::with(['items', 'productInvoices.supplier', 'chargeInvoices.supplier'])
                ->where('id', $request->integer('hold'))
                ->where('status', 'draft')
                ->firstOrFail();
            $holdBatchPayload = $this->buildHoldBatchPurchasePayload($holdBatch);
        }

        return view('admin.purchases.batch-purchases-create', compact(
            'stores',
            'selectedStoreId',
            'holdBatch',
            'holdBatchPayload'
        ));
    }

    public function searchBatchPurchaseInvoices(Request $request): JsonResponse
    {
        $term = trim((string) $request->input('q', ''));
        $storeId = $request->integer('store_id') ?: null;
        $excludeBatchId = $request->integer('exclude_batch_id') ?: null;

        $query = SupplierPurchaseInvoice::query()
            ->with('supplier')
            ->where('invoice_status', 'Confirmed')
            ->when($storeId, fn ($q) => $q->where('store_id', $storeId))
            ->orderByDesc('invoice_date')
            ->limit(30);

        if ($term !== '') {
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term) . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('internal_invoice_number', 'like', $like)
                    ->orWhere('invoice_number', 'like', $like)
                    ->orWhereHas('supplier', fn ($supplier) => $supplier->where('company_name', 'like', $like));
            });
        }

        $receivedByGrn = $this->purchaseInvoiceIdsWithCompletedGrn();
        $invoices = $query->get()->filter(function (SupplierPurchaseInvoice $invoice) use ($excludeBatchId, $receivedByGrn) {
            // Already received by GRN or by a confirmed batch purchase.
            // return ! $invoice->isAssignedToConfirmedBatchPurchase($excludeBatchId);
            if (in_array((int) $invoice->id, $receivedByGrn, true)) {
                return false;
            }

            return ! $invoice->isAssignedToConfirmedBatchPurchase($excludeBatchId);
        })->values();

        return response()->json($invoices->map(fn (SupplierPurchaseInvoice $invoice) => [
            'id' => (int) $invoice->id,
            'label' => $this->purchaseInvoiceLabel($invoice),
            'supplier_name' => (string) ($invoice->supplier->company_name ?? ''),
            'subtotal' => (float) $invoice->subtotal,
            'display' => $this->purchaseInvoiceLabel($invoice) . ' — ' . ($invoice->supplier->company_name ?? ''),
        ]));
    }

    public function previewBatchPurchase(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'product_invoice_ids' => 'nullable|array',
            'product_invoice_ids.*' => 'integer|exists:supplier_purchase_invoices,id',
            'charge_invoice_ids' => 'nullable|array',
            'charge_invoice_ids.*' => 'integer|exists:supplier_purchase_invoices,id',
            'exclude_batch_id' => 'nullable|integer|exists:batch_purchases,id',
        ]);

        $productIds = array_values(array_unique(array_map('intval', $validated['product_invoice_ids'] ?? [])));
        $chargeIds = array_values(array_unique(array_map('intval', $validated['charge_invoice_ids'] ?? [])));
        $overlap = array_intersect($productIds, $chargeIds);
        if ($overlap !== []) {
            return response()->json(['message' => 'An invoice cannot be both a product invoice and an additional charge invoice.'], 422);
        }

        $excludeBatchId = $validated['exclude_batch_id'] ?? null;
        $locked = SupplierPurchaseInvoice::idsLockedByConfirmedBatchPurchase(array_merge($productIds, $chargeIds), $excludeBatchId);
        if ($locked !== []) {
            return response()->json(['message' => 'One or more selected invoices are already part of a confirmed batch purchase.'], 422);
        }

        $storeId = (int) $validated['store_id'];
        $lines = $this->consolidateProductInvoiceLines($productIds, $storeId);
        $chargeSubtotal = $this->chargeInvoicesSubtotal($chargeIds);
        $lines = $this->allocateAdditionalChargesToLines($lines, $chargeSubtotal);

        return response()->json([
            'lines' => $lines,
            'charge_invoices_subtotal' => $chargeSubtotal,
            'product_invoice_count' => count($productIds),
            'charge_invoice_count' => count($chargeIds),
        ]);
    }

    public function batchPurchasesStore(Request $request): RedirectResponse|JsonResponse
    {
        return $this->saveBatchPurchase($request);
    }

    public function batchPurchasesUpdate(Request $request, int $batch): RedirectResponse|JsonResponse
    {
        $existing = BatchPurchase::query()->where('id', $batch)->where('status', 'draft')->firstOrFail();

        return $this->saveBatchPurchase($request, $existing);
    }

    private function saveBatchPurchase(Request $request, ?BatchPurchase $existing = null): RedirectResponse|JsonResponse
    {
        $validated = $request->validate(array_merge([
            'store_id' => 'required|exists:stores,id',
            'batch_date' => 'required|date',
            'notes' => 'nullable|string|max:2000',
            'tax_exempt' => 'nullable|boolean',
            'batch_status' => 'nullable|in:draft,confirmed',
            'product_invoice_ids' => 'nullable|array',
            'product_invoice_ids.*' => 'integer|exists:supplier_purchase_invoices,id',
            'charge_invoice_ids' => 'nullable|array',
            'charge_invoice_ids.*' => 'integer|exists:supplier_purchase_invoices,id',
            'additional_charges' => 'sometimes|array',
            'additional_charges.*' => 'nullable|numeric|min:0',
            'final_unit' => 'sometimes|array',
            'final_unit.*' => 'nullable|numeric|min:0',
            'sku' => 'sometimes|array',
            'sku.*' => 'nullable|string|max:120',
            'category_id' => 'sometimes|array',
            'category_id.*' => 'nullable',
            'category' => 'sometimes|array',
            'category.*' => 'nullable|string|max:255',
        ], $this->invoiceStyleLineValidationRules()));

        $onHold = ($validated['batch_status'] ?? 'confirmed') === 'draft';
        $productInvoiceIds = array_values(array_unique(array_map('intval', $validated['product_invoice_ids'] ?? [])));
        $chargeInvoiceIds = array_values(array_unique(array_map('intval', $validated['charge_invoice_ids'] ?? [])));

        if (array_intersect($productInvoiceIds, $chargeInvoiceIds) !== []) {
            return $this->batchPurchaseErrorResponse($request, [
                'product_invoice_ids' => 'An invoice cannot be both a product and additional charge invoice.',
            ]);
        }

        $lines = $this->resolveBatchPurchaseLines($request);
        if (count($lines) === 0) {
            return $this->batchPurchaseErrorResponse($request, ['lines' => 'Add at least one product line.']);
        }

        if (count($productInvoiceIds) === 0) {
            return $this->batchPurchaseErrorResponse($request, [
                'product_invoice_ids' => 'Select at least one product invoice.',
            ]);
        }

        $chargeSubtotal = $this->chargeInvoicesSubtotal($chargeInvoiceIds);
        $allocatedCharges = round(array_sum(array_column($lines, 'additional_charges')), 2);

        if (! $onHold && $chargeInvoiceIds !== [] && abs($allocatedCharges - $chargeSubtotal) > 0.01) {
            return $this->batchPurchaseErrorResponse($request, [
                'additional_charges' => 'Allocated additional charges (' . number_format($allocatedCharges, 2) . ') must equal the total of additional charge invoices (' . number_format($chargeSubtotal, 2) . ').',
            ]);
        }

        $locked = SupplierPurchaseInvoice::idsLockedByConfirmedBatchPurchase(
            array_merge($productInvoiceIds, $chargeInvoiceIds),
            $existing?->id
        );
        if ($locked !== []) {
            return $this->batchPurchaseErrorResponse($request, [
                'product_invoice_ids' => 'One or more invoices are already part of another confirmed batch purchase.',
            ]);
        }

        $alreadyReceived = array_values(array_intersect(
            array_merge($productInvoiceIds, $chargeInvoiceIds),
            $this->purchaseInvoiceIdsWithCompletedGrn()
        ));
        if ($alreadyReceived !== []) {
            return $this->batchPurchaseErrorResponse($request, [
                'product_invoice_ids' => 'One or more invoices have already been received via a goods receipt note.',
            ]);
        }

        [$subtotal, $taxAmount, $total] = $this->totalsFromPerLineTax($lines);
        // Old total was product subtotal + tax and left out allocated additional charges.
        // $total = round($subtotal + $taxAmount, 2);
        $grandTotalsSum = round((float) array_sum(array_column($lines, 'grand_total')), 2);
        $total = round($grandTotalsSum + $taxAmount, 2);
        $taxExempt = $request->boolean('tax_exempt');

        $batch = null;
        DB::transaction(function () use (
            $request, $validated, $lines, $subtotal, $taxAmount, $total, $taxExempt, $onHold,
            $productInvoiceIds, $chargeInvoiceIds, $chargeSubtotal, $allocatedCharges, $existing, &$batch
        ) {
            if ($existing) {
                $existing->items()->delete();
                $existing->productInvoices()->detach();
                $existing->chargeInvoices()->detach();
                $batch = $existing;
            } else {
                $batch = new BatchPurchase;
            }

            // batch_number is NOT NULL — assign before the first insert.
            if ($onHold) {
                if (! $batch->batch_number) {
                    $batch->batch_number = 'HOLD-PENDING-' . uniqid('', true);
                }
            } elseif (! $batch->batch_number || str_starts_with((string) $batch->batch_number, 'HOLD-')) {
                $batch->batch_number = $this->issueBatchPurchaseNumber((int) $validated['store_id']);
            }

            $batch->fill([
                'store_id' => $validated['store_id'],
                'batch_date' => $validated['batch_date'],
                'status' => $onHold ? 'draft' : 'confirmed',
                'tax_exempt' => $taxExempt,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'charge_invoices_subtotal' => $chargeSubtotal,
                'allocated_additional_charges' => $allocatedCharges,
                'notes' => $validated['notes'] ?? null,
            ]);
            $batch->save();

            if ($onHold) {
                if (! $batch->draft_reference) {
                    app(DraftReferenceService::class)->assignForBatchPurchase($batch);
                }
                if (str_starts_with((string) $batch->batch_number, 'HOLD-PENDING-')) {
                    $batch->batch_number = 'HOLD-' . str_pad((string) $batch->id, 6, '0', STR_PAD_LEFT);
                    $batch->save();
                }
            }

            // Old: batch_number was set after first save(), causing SQL error (no default value).
            // if ($onHold) { ... $batch->batch_number = 'HOLD-...'; $batch->save(); }
            // elseif (! $batch->batch_number || str_starts_with(...)) { ... }

            foreach ($lines as $line) {
                $batch->items()->create($line);
            }

            $batch->productInvoices()->sync($productInvoiceIds);
            $batch->chargeInvoices()->sync($chargeInvoiceIds);

            if (! $onHold) {
                $stockLines = array_map(fn (array $line) => [
                    'product_id' => $line['product_id'],
                    'product_name' => $line['product_name'],
                    'qty' => $line['qty'],
                    'unit_price' => $line['final_unit'],
                    'line_total' => round($line['qty'] * $line['final_unit'], 2),
                    'is_taxable' => $line['is_taxable'],
                    'tax_rate' => $line['tax_rate'],
                    'tax_amount' => $line['tax_amount'],
                ], $lines);

                $this->receiveLinesIntoStock(
                    (int) $validated['store_id'],
                    $stockLines,
                    $request,
                    'Batch ' . $batch->batch_number,
                    $validated['batch_date'],
                );
            }
        });

        $message = $onHold ? 'Batch purchase saved on hold.' : 'Batch purchase confirmed and stock updated.';
        $redirectUrl = $onHold
            ? route('admin.batch-purchases-on-hold')
            : route('admin.batch-purchases.show', $batch);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect_url' => $redirectUrl,
            ]);
        }

        return redirect()->to($redirectUrl)->with('success', $message);
    }

    /** @return array<string, mixed> */
    private function buildHoldBatchPurchasePayload(BatchPurchase $batch): array
    {
        return [
            'batch_id' => (int) $batch->id,
            'store_id' => (int) $batch->store_id,
            'batch_date' => $batch->batch_date?->format('Y-m-d'),
            'notes' => (string) ($batch->notes ?? ''),
            'tax_exempt' => (bool) $batch->tax_exempt,
            'draft_reference' => $batch->draft_reference,
            'product_invoice_ids' => $batch->productInvoices->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
            'charge_invoice_ids' => $batch->chargeInvoices->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
            'product_invoices' => $batch->productInvoices->map(fn (SupplierPurchaseInvoice $inv) => [
                'id' => (int) $inv->id,
                'display' => $this->purchaseInvoiceLabel($inv) . ' — ' . ($inv->supplier->company_name ?? ''),
            ])->values()->all(),
            'charge_invoices' => $batch->chargeInvoices->map(fn (SupplierPurchaseInvoice $inv) => [
                'id' => (int) $inv->id,
                'display' => $this->purchaseInvoiceLabel($inv) . ' — ' . ($inv->supplier->company_name ?? ''),
                'subtotal' => (float) $inv->subtotal,
            ])->values()->all(),
            'charge_invoices_subtotal' => (float) $batch->charge_invoices_subtotal,
            'lines' => $batch->items->map(fn (BatchPurchaseItem $item) => [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'sku' => $item->sku,
                'category_id' => $item->category_id,
                'category_name' => $item->category_name,
                'qty' => (int) $item->qty,
                'unit_price' => (float) $item->unit_price,
                'amount' => (float) $item->amount,
                'additional_charges' => (float) $item->additional_charges,
                'grand_total' => (float) $item->grand_total,
                'final_unit' => (float) $item->final_unit,
                'is_taxable' => (bool) $item->is_taxable,
                'tax_rate' => (float) $item->tax_rate,
            ])->values()->all(),
        ];
    }

    /** @param  array<int, int>  $invoiceIds
     * @return array<int, array<string, mixed>>
     */
    private function consolidateProductInvoiceLines(array $invoiceIds, int $storeId): array
    {
        if ($invoiceIds === []) {
            return [];
        }

        $invoices = SupplierPurchaseInvoice::query()
            ->with('items')
            ->whereIn('id', $invoiceIds)
            ->where('invoice_status', 'Confirmed')
            ->get();

        $merged = [];
        foreach ($invoices as $invoice) {
            foreach ($invoice->items as $item) {
                if (empty($item->product_id)) {
                    continue;
                }

                $key = (string) $item->product_id;
                if (! isset($merged[$key])) {
                    $mapped = $this->mapDocumentItemsForPicker([$item], $storeId, 'qty', 'unit_price')[0] ?? null;
                    $merged[$key] = [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'sku' => $mapped['sku'] ?? '',
                        'category' => $mapped['category'] ?? '',
                        'category_id' => $mapped['category_id'] ?? '',
                        'qty' => 0,
                        'amount' => 0.0,
                        'is_taxable' => (bool) ($item->is_taxable ?? true),
                        'tax_rate' => (float) ($item->tax_rate ?? 0),
                    ];
                }

                $lineAmount = round((int) $item->qty * (float) $item->unit_price, 2);
                $merged[$key]['qty'] += (int) $item->qty;
                $merged[$key]['amount'] = round($merged[$key]['amount'] + $lineAmount, 2);
            }
        }

        $lines = [];
        foreach ($merged as $row) {
            $qty = max(1, (int) $row['qty']);
            $amount = round((float) $row['amount'], 2);
            $unitPrice = $qty > 0 ? round($amount / $qty, 2) : 0.0;
            $lines[] = [
                'product_id' => $row['product_id'],
                'product_name' => $row['product_name'],
                'sku' => $row['sku'],
                'category' => $row['category'],
                'category_id' => $row['category_id'],
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'amount' => $amount,
                'additional_charges' => 0,
                'grand_total' => $amount,
                'final_unit' => $qty > 0 ? round($amount / $qty, 4) : 0,
                'is_taxable' => $row['is_taxable'],
                'tax_rate' => $row['tax_rate'],
            ];
        }

        return $lines;
    }

    /** @param  array<int, array<string, mixed>>  $lines */
    private function allocateAdditionalChargesToLines(array $lines, float $chargeSubtotal): array
    {
        if ($chargeSubtotal <= 0 || $lines === []) {
            return $lines;
        }

        $totalAmount = round(array_sum(array_map(
            fn (array $line) => (float) ($line['amount'] ?? 0),
            $lines
        )), 2);

        $allocated = 0.0;
        $lastIndex = count($lines) - 1;

        foreach ($lines as $index => &$line) {
            if ($index === $lastIndex) {
                $extra = round($chargeSubtotal - $allocated, 2);
            } elseif ($totalAmount > 0) {
                $extra = round($chargeSubtotal * ((float) ($line['amount'] ?? 0) / $totalAmount), 2);
                $allocated += $extra;
            } else {
                $extra = round($chargeSubtotal / count($lines), 2);
                $allocated += $extra;
            }

            $line['additional_charges'] = $extra;
            $grandTotal = round((float) ($line['amount'] ?? 0) + $extra, 2);
            $qty = max(1, (int) ($line['qty'] ?? 1));
            $line['grand_total'] = $grandTotal;
            $line['final_unit'] = round($grandTotal / $qty, 4);
        }
        unset($line);

        return $lines;
    }

    /** @param  array<string, string|array<int, string>>  $errors */
    private function batchPurchaseErrorResponse(Request $request, array $errors): RedirectResponse|JsonResponse
    {
        if ($request->ajax() || $request->wantsJson()) {
            $flat = collect($errors)->flatten()->filter()->values();

            return response()->json([
                'message' => (string) ($flat->first() ?? 'Could not save batch purchase.'),
                'errors' => $errors,
            ], 422);
        }

        return back()->withErrors($errors)->withInput();
    }

    /** @param  array<int, int>  $invoiceIds */
    private function chargeInvoicesSubtotal(array $invoiceIds): float
    {
        if ($invoiceIds === []) {
            return 0.0;
        }

        return round((float) SupplierPurchaseInvoice::query()
            ->whereIn('id', $invoiceIds)
            ->where('invoice_status', 'Confirmed')
            ->sum('subtotal'), 2);
    }

    /** @return array<int, array<string, mixed>> */
    private function resolveBatchPurchaseLines(Request $request): array
    {
        $names = $request->input('product_name', []);
        $qtys = $request->input('qty', []);
        $prices = $request->input('price', []);
        $ids = $request->input('product_id', []);
        $additionalCharges = $request->input('additional_charges', []);
        $finalUnits = $request->input('final_unit', []);
        $skus = $request->input('sku', []);
        $categoryIds = $request->input('category_id', []);
        $categories = $request->input('category', []);
        $taxExempt = $request->boolean('tax_exempt');
        $lineIsTaxable = $request->input('line_is_taxable', []);
        $lineTaxRates = $request->input('line_tax_rate', []);
        $storeId = (int) $request->input('store_id', 0);

        $out = [];
        foreach ($names as $i => $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }

            $qty = max(1, (int) ($qtys[$i] ?? 1));
            $unitPrice = round((float) ($prices[$i] ?? 0), 2);
            $amount = round($qty * $unitPrice, 2);
            $extra = round((float) ($additionalCharges[$i] ?? 0), 2);
            $grandTotal = round($amount + $extra, 2);
            $finalUnit = isset($finalUnits[$i]) && $finalUnits[$i] !== ''
                ? round((float) $finalUnits[$i], 4)
                : ($qty > 0 ? round($grandTotal / $qty, 4) : 0);

            $isTaxable = false;
            $rate = 0.0;
            if (! $taxExempt) {
                if (array_key_exists($i, $lineIsTaxable)) {
                    $isTaxable = $this->parseLineTaxableFlag($lineIsTaxable[$i]);
                } else {
                    $isTaxable = true;
                }
                $rate = $isTaxable ? (float) ($lineTaxRates[$i] ?? 0) : 0.0;
            }

            $taxAmount = $isTaxable ? round($amount * $rate / 100, 2) : 0.0;

            $out[] = [
                'product_id' => isset($ids[$i]) && $ids[$i] !== '' ? (string) $ids[$i] : null,
                'product_name' => $name,
                'sku' => isset($skus[$i]) ? (string) $skus[$i] : '',
                'category_id' => ! empty($categoryIds[$i]) ? (int) $categoryIds[$i] : null,
                'category_name' => isset($categories[$i]) ? (string) $categories[$i] : '',
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'amount' => $amount,
                'additional_charges' => $extra,
                'grand_total' => $grandTotal,
                'final_unit' => $finalUnit,
                'is_taxable' => $isTaxable,
                'tax_rate' => $rate,
                'tax_amount' => $taxAmount,
            ];
        }

        return $out;
    }

    private function issueBatchPurchaseNumber(int $storeId): string
    {
        $issued = app(StoreSequentialNumberService::class)->issueNextForStore(
            $storeId,
            'batch_purchase_prefix',
            'next_batch_purchase_number',
            'BP',
            'batch_purchases',
            'batch_number'
        );

        return $issued ?? $this->uniqueRef('BP');
    }

    /**
     * @return array{previous: ?string, next: ?string, position: int, total: int}
     */
    private function recordNav($query, int|string $currentId, string $route): array
    {
        return RecordNavigator::links($query, $currentId, fn ($id) => route($route, $id));
    }
}

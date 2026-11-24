<?php

namespace App\Http\Controllers\admin;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\AccountingService;
use App\Services\CreditLimitService;
use App\Services\DraftReferenceService;
use App\Services\ProductUnitCostService;
use App\Services\ProductCatalogService;
use App\Services\SaleInvoiceNumberService;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\CustomerPayment;
use App\Models\GlAccount;
use App\Models\JournalEntry;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTerm;
use App\Models\Store;
use App\Models\Stock;
use App\Models\User;
use App\Services\DatatablePaginationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    private const CASH_SALES_OPTION_ID = 1;
    private const CREDIT_SALES_OPTION_ID = 2;
    private const ESTIMATE_OPTION_ID = 3;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $storeId = session('selected_store_id');  // Make sure you're saving it with this key
        $discount_percentage = User::where('role_id', 5)->value('max_discount');
        if (!$storeId) {
            return redirect()->back()->with('error', 'Please select a store first.');
        }
        $customers = Customer::get();

        // Same catalog on every store. Quantity is this store's on-hand amount.
        $catalog = DB::table('stocks')
            ->selectRaw('
                stocks.product_id,
                MAX(stocks.product_name) as product_name,
                MAX(stocks.sku) as sku,
                MAX(stocks.category_id) as category_id,
                MAX(stocks.images) as images,
                COALESCE(MAX(CASE WHEN stocks.store_id = ? THEN stocks.price END), MAX(stocks.price)) as price,
                COALESCE(SUM(CASE WHEN stocks.store_id = ? THEN stocks.quantity ELSE 0 END), 0) as quantity
            ', [$storeId, $storeId])
            ->groupBy('stocks.product_id')
            ->orderBy('product_name')
            ->get()
            ->groupBy('category_id');

        $categories = Category::query()
            ->whereIn('id', $catalog->keys()->filter())
            ->orderBy('name')
            ->get();
        $categoryProductCounts = [];
        $categoryProducts = [];
        foreach ($categories as $category) {
            $items = $catalog->get($category->id, collect());
            $categoryProductCounts[$category->id] = $items->count();
            $categoryProducts[$category->id] = $items;
        }
        return view('admin.orders.pos-terminal', compact('categories', 'categoryProductCounts', 'categoryProducts', 'customers', 'discount_percentage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Incoming Order Data:', $request->all());
        // Validate incoming data
        $request->validate([
            'store_from' => 'required|exists:stores,id',
            'store_to' => 'required|exists:stores,id|different:store_from',
            'rperson_from' => 'required|string',
            'rperson_to' => 'required|string',
            'product_id.*' => 'required|exists:stocks,id',
            'product_name.*' => 'required|string',
            'sku.*' => 'required|string',
            'qty.*' => 'required|integer|min:1',
            'category_id.*' => 'nullable|exists:categories,id',
        ]);

        // Proceed to store order after validation
        DB::beginTransaction();  // Start transaction to ensure data consistency

        try {
            // Step 1: Create Order
            $order = Order::create([
                'store_from' => $request->store_from,
                'store_to' => $request->store_to,
                'rperson_from' => $request->rperson_from,
                'rperson_to' => $request->rperson_to,
                'reference_no' => time(),  // Unique reference number
                'notes' => $request->notes,
            ]);

            // Step 2: Add Order Items
            foreach ($request->product_id as $key => $productId) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'product_name' => $request->product_name[$key],
                    'sku' => $request->sku[$key],
                    'qty' => $request->qty[$key],
                    'category_id' => $request->category_id[$key],
                ]);

                // Step 3: Update Stock for Source Store
                $stock = Stock::where('store_id', $request->store_from)
                    ->where('product_id', $productId)
                    ->first();

                if ($stock) {
                    $stock->decrement('quantity', $request->qty[$key]);
                }

                // Step 4: Update or Create Stock in Destination Store
                $destinationStock = Stock::where('store_id', $request->store_to)
                    ->where('product_id', $productId)
                    ->first();

                if ($destinationStock) {
                    $destinationStock->increment('quantity', $request->qty[$key]);
                } else {
                    Stock::create([
                        'store_id' => $request->store_to,
                        'product_id' => $productId,
                        'product_name' => $request->product_name[$key],
                        'sku' => $request->sku[$key],
                        'category_id' => $request->category_id[$key],
                        'quantity' => $request->qty[$key],
                    ]);
                }
            }

            DB::commit();  // Commit transaction
            return response()->json(['success' => true, 'message' => 'Order placed successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();  // Rollback on error
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getStores()
    {
        $stores = Store::where('status', 1)->get();
        return response()->json($stores);
    }

    public function getProductsByStore(Request $request)
    {
        $storeId = $request->store_id;
        $query = $request->input('query', '');
        if (!$storeId) {
            return response()->json(['error' => 'Store ID is required!'], 400);
        }
        $products = app(ProductCatalogService::class)->search((int) $storeId, (string) $query);

        return response()->json($products);
    }

    // /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function addToCart(Request $request)
    {
        $cart = session()->get('cart', []);

        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;  // Default quantity is 1

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'product_name' => $request->product_name,
                'price' => $request->price,
                'image' => $request->image,
                'quantity' => $quantity,
                'sku' => $request->sku,
                'category_id' => $request->category_id,
                // 'total_price' => $quantity * $request->price,
            ];
        }

        session()->put('cart', $cart);
        return response()->json(['success' => true, 'cart' => $cart]);
    }

    // ////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function getCart()
    {
        $cart = Session::get('cart', []);
        return response()->json(['cart' => $cart]);
    }

    // /////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function updateProduct(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'product_name' => 'required|string|max:255',
            'tax' => 'required|numeric',
            'discount' => 'required|numeric',
            'sale_unit' => 'required|string|max:255',
        ]);
        $product = Product::findOrFail($request->product_id);

        $product->update([
            'product_name' => $request->product_name,
            'tax' => $request->tax,
            'discount' => $request->discount,
            'sale_unit' => $request->sale_unit,
        ]);
        return response()->json(['success' => true, 'product' => $product]);
    }

    public function removeFromCart(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
        }

        return response()->json(['success' => true]);
    }

    public function clearCart()
    {
        session()->forget('cart');  // Clear cart session
        return response()->json(['message' => 'Cart cleared successfully']);
    }

    // public function placeOrder(Request $request)
    // {
    //     $request->validate([
    //         'store_id' => 'nullable|integer',
    //         'customer_id' => 'required|integer',
    //         'customer_name' => 'required|string',
    //         'subtotal_price' => 'required|numeric',
    //         'total_price' => 'required|numeric',
    //         'payment_method' => 'required|string',
    //         'items' => 'required|array|min:1',
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         $storeId = session('selected_store_id');

    //         //  Step 1: Validate stock availability
    //         foreach ($request->items as $item) {
    //             $stock = Stock::where('store_id', $storeId)
    //                 ->where('product_id', $item['product_id'])
    //                 ->first();

    //             if (!$stock || $stock->quantity < $item['qty']) {
    //                 DB::rollBack();
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => "Insufficient stock for product: {$item['product_name']} (SKU: {$item['sku']})",
    //                 ], 400);
    //             }
    //         }

    //         //  Step 2: Create Order
    //         $order = Order::create([
    //             'store_id' => $storeId,
    //             'customer_id' => $request->customer_id,
    //             'customer_name' => $request->customer_name,
    //             'subtotal_price' => $request->subtotal_price,
    //             'total_price' => $request->total_price,
    //             'tax' => $request->tax ?? 0,
    //             'discount' => $request->discount ?? 0,
    //             'shipping' => $request->shipping ?? 0,
    //             'payment_method' => $request->payment_method,
    //             'notes' => $request->notes ?? null,
    //             'type' => $request->type ?? 'online',
    //         ]);

    //         //  Step 3: Insert Order Items and update stock
    //         foreach ($request->items as $item) {
    //             OrderItem::create([
    //                 'order_id' => $order->id,
    //                 'product_id' => $item['product_id'],
    //                 'product_name' => $item['product_name'],
    //                 'sku' => $item['sku'],
    //                 'qty' => $item['qty'],
    //                 'category_id' => $item['category_id'],
    //                 'price' => $item['price'],
    //             ]);

    //             // Reduce stock
    //             Stock::where('store_id', $storeId)
    //                 ->where('product_id', $item['product_id'])
    //                 ->decrement('quantity', $item['qty']);
    //         }

    //         DB::commit();
    //         session()->forget('cart');
    //         // NOTIFICATION

    //         $userId = auth()->id();
    //         $message = 'New Order has been created.';  // Custom message
    //         $userName = auth()->user()->name;
    //         $port = env('NOTIFICATION_PORT');
    //         $url = "http://localhost:$port/notify";
    //         $response = Http::post($url, [
    //             'userId' => $userId,
    //             'message' => $message,
    //             'user_name' => $userName,
    //         ]);
    //         NotificationHelper::add(auth()->id(), $message);

    //         return response()->json(['success' => true, 'message' => 'Order placed successfully!']);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Order placement failed!',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
    public function placeOrder(Request $request)
    {
        // if (auth()->user()->role_id != 5) {
        if (!hasPermission('create', 'order')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        if ($request->filled('order_id')) {
            return $this->updateDraftSaleInvoice($request);
        }

        $request->validate([
            'store_id' => 'nullable|integer',
            'customer_id' => 'required|integer',
            'customer_name' => 'required|string',
            'subtotal_price' => 'required|numeric',
            'total_price' => 'required|numeric',
            'payment_method' => 'nullable|string',
            // Old code:
            // 'invoice_type' => 'required_if:type,offline|in:1,2,3',
            // Only required for invoice flow (type=offline)
            'invoice_type' => 'required_if:type,offline|exists:invoice_options,id',
            'items' => 'required|array|min:1',
            // Old code:
            // 'due_date' => 'nullable|date',
            // 'payment_terms_days' => 'nullable|integer|min:0|max:3650',
            'payment_terms_days' => [
                'nullable',
                Rule::exists('payment_terms', 'id')->where(fn($q) => $q->where('is_active', 1)),
            ],
            'invoice_status' => 'nullable|in:Draft,Confirmed,Cancelled',
        ]);

        if ($this->isCreditSaleInvoiceType($request->invoice_type) && !$request->filled('payment_terms_days')) {
            return response()->json([
                'success' => false,
                'message' => 'Payment terms are required for Credit Sales.',
            ], 422);
        }
        if ((int) $request->invoice_type === self::CASH_SALES_OPTION_ID && !$request->filled('payment_method')) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method is required for Cash Sales.',
            ], 422);
        }

        $storeId = (int) ($request->input('store_id') ?: $request->input('store_from') ?: session('selected_store_id'));
        $isOfflineInvoice = ($request->type ?? 'online') === 'offline';
        $onHold = $this->isInvoiceOnHold($request->input('invoice_status'));

        try {
            $resolvedItems = $this->resolveOrderItemsAgainstStock($storeId, $request->items);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        $taxExempt = $isOfflineInvoice && $request->boolean('tax_exempt');
        if ($isOfflineInvoice) {
            $computed = $this->computeInvoiceTotalsWithLineTax(
                $resolvedItems,
                (float) ($request->discount ?? 0),
                (float) ($request->shipping ?? 0),
                $taxExempt
            );
            $resolvedItems = $computed['items'];
            $subtotalPrice = $computed['subtotal'];
            $totalPrice = $computed['total'];
            $taxAmount = $computed['tax_amount'];
        } else {
            $subtotalPrice = (float) $request->subtotal_price;
            $totalPrice = (float) $request->total_price;
            $taxAmount = 0.0;
        }

        [$paidAmount, $dueAmount, $dueDate, $paymentTermsDays] = $this->buildReceivableFields($request, $totalPrice);

        $creditLimitBlocked = false;
        $creditMessage = null;
        if ($isOfflineInvoice && $this->isCreditSaleInvoiceType($request->invoice_type)) {
            $customer = Customer::find($request->customer_id);
            if ($customer) {
                $excludeOrderId = $request->filled('order_id') ? (int) $request->order_id : null;
                $creditMessage = app(CreditLimitService::class)->customerWouldExceed($customer, $dueAmount, $excludeOrderId);
                if ($creditMessage) {
                    $creditLimitBlocked = true;
                    if (! $onHold) {
                        return response()->json([
                            'success' => false,
                            'message' => $creditMessage,
                            'credit_limit_exceeded' => true,
                        ], 422);
                    }
                }
            }
        }

        DB::beginTransaction();

        try {

            // 1. Validate stock availability (skip for on-hold drafts)
            if (! $onHold) {
            foreach ($resolvedItems as $item) {
                $stock = Stock::where('store_id', $storeId)
                    ->where('product_id', $item['product_id'])
                    ->first();

                if (!$stock || $stock->quantity < $item['qty']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for {$item['product_name']} (SKU {$item['sku']})",
                    ], 400);
                }
            }
            }

            // 2. Create Order
            $orderDate = $request->filled('order_date')
                ? parse_date_input($request->order_date)
                : now();

            $order = Order::create([
                'store_id' => $storeId,
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'subtotal_price' => $subtotalPrice,
                'total_price' => $totalPrice,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'tax' => $isOfflineInvoice ? 0 : ($request->tax ?? 0),
                'tax_exempt' => $taxExempt,
                'tax_amount' => $taxAmount,
                'discount' => $request->discount ?? 0,
                'shipping' => $request->shipping ?? 0,
                'payment_method' => $request->payment_method,
                'invoice_type' => $request->invoice_type ?? null,
                'due_date' => $dueDate,
                'payment_terms_days' => $paymentTermsDays,
                'status' => $this->resolveOrderStatus($request->invoice_type, $dueAmount, $paidAmount),
                'notes' => $request->notes ?? null,
                'type' => $request->type ?? 'online',
                'created_at' => $orderDate,
            ]);

            if ($creditLimitBlocked && Schema::hasColumn('orders', 'credit_limit_blocked')) {
                $order->credit_limit_blocked = true;
                $order->save();
            }

            $this->assignInvoiceDocumentFields(
                $order,
                (int) ($request->invoice_type ?? 0),
                $storeId,
                $request->input('invoice_status')
            );

            // 3. Insert Order Items and update stock
            $orderItems = [];
            $newStocks = [];

            $shouldDecrementStock = (int) $order->status !== 3 && ! $onHold;

            foreach ($resolvedItems as $item) {
                $orderItems[] = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'sku' => $item['sku'],
                    'qty' => $item['qty'],
                    'category_id' => $item['category_id'],
                    'price' => $item['price'],
                    'is_taxable' => (bool) ($item['is_taxable'] ?? false),
                    'tax_rate' => (float) ($item['tax_rate'] ?? 0),
                    'tax_amount' => (float) ($item['tax_amount'] ?? 0),
                ])->toArray();

                if ($shouldDecrementStock) {
                    Stock::where('store_id', $storeId)
                        ->where('product_id', $item['product_id'])
                        ->decrement('quantity', $item['qty']);

                    $movedStock = Stock::where('store_id', $storeId)
                        ->where('product_id', $item['product_id'])
                        ->first();

                    if ($movedStock) {
                        app(ProductUnitCostService::class)->syncStock($movedStock);
                    }

                    $newStocks[$item['product_id']] = $movedStock
                        ? $movedStock->fresh()->toArray()
                        : [];
                }
            }

            // Track receivable invoice in customer ledger (not for estimates or on-hold).
            if (! $onHold && (int) $order->status !== 3 && (float) $order->due_amount > 0) {
                $this->createCustomerLedgerEntry(
                    customerId: (int) $order->customer_id,
                    orderId: (int) $order->id,
                    paymentId: null,
                    entryType: 'invoice',
                    description: 'Invoice ' . ($order->invoice_number ?: ('INV/' . $order->id)) . ' posted',
                    debit: (float) $order->total_price,
                    credit: 0,
                    meta: [
                        'invoice_type' => $order->invoice_type,
                        'due_date' => optional($order->due_date)->toDateString(),
                    ]
                );
            }

            // Automatic double-entry GL (not for on-hold drafts).
            if (! $onHold) {
                try {
                    app(AccountingService::class)->postSaleOrder($order);
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            // 4. Store in history (only new)
            storeHistory(
                'pos_order_form',
                'created',
                'order',
                $order->id,
                [
                    'order' => $order->toArray(),
                    'items' => $orderItems,
                    // 'stock_after' => $newStocks,
                ],
                null
            );

            DB::commit();
            session()->forget('cart');

            // 5. Notification only for POS/online orders (not when creating an invoice)
            $isInvoice = ($request->type ?? 'online') === 'offline';
            if (! $isInvoice) {
                $userId = auth()->id();
                $message = 'New order has been created.';
                NotificationHelper::add($userId, $message);
                NotificationHelper::pushRealtime($userId, $message);
            }

            $message = $onHold && $isOfflineInvoice
                ? 'Invoice saved on hold.'
                : 'Order placed successfully!';

            // Old: return response()->json(['success' => true, 'message' => $message]);
            return $this->saleInvoicePlacementResponse(
                $order,
                $message,
                $isOfflineInvoice && ! $onHold
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Order placement failed!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkCustomerCreditLimit(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'due_amount' => 'required|numeric|min:0',
            'order_id' => 'nullable|integer|exists:orders,id',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $excludeOrderId = ! empty($validated['order_id']) ? (int) $validated['order_id'] : null;
        $assessment = app(CreditLimitService::class)->assessCustomerCredit(
            $customer,
            (float) $validated['due_amount'],
            $excludeOrderId
        );

        return response()->json($assessment);
    }

    public function salesInvoicesOnHold(Request $request)
    {
        if ($request->ajax()) {
            $ordersTable = (new Order)->getTable();
            $query = Order::query()
                ->select("{$ordersTable}.*")
                ->leftJoin('stores as sort_stores', "{$ordersTable}.store_id", '=', 'sort_stores.id')
                ->with('store')
                ->where("{$ordersTable}.type", 'offline')
                ->where("{$ordersTable}.invoice_status", 'Draft');

            // Old default sort blocked user-selected column sorting after store join.
            // ->orderByDesc('id');

            $datatable = datatables()
                ->eloquent($query)
                ->addColumn('invoice_no', fn ($row) => e($row->draft_reference ?: ($row->invoice_number ?: ('#' . $row->id))))
                ->addColumn('customer_name', fn ($row) => e($row->customer_name ?? '-'))
                ->addColumn('store_name', fn ($row) => $row->store->name ?? '-')
                ->addColumn('created_at', fn ($row) => format_date($row->created_at, '-'))
                ->addColumn('total_price', fn ($row) => '$' . number_format((float) $row->total_price, 2))
                ->addColumn('limit_flag', function ($row) {
                    if (! $row->credit_limit_blocked) {
                        return '<span class="text-muted">—</span>';
                    }

                    return '<span class="badge bg-warning text-dark">Over credit limit</span>';
                })
                ->addColumn('actions', function ($row) {
                    $viewUrl = route('admin.sales-invoices.show', $row->id);
                    $editUrl = route('admin.generate-invoice', ['hold_order' => $row->id]);
                    $html = '<div class="d-flex flex-wrap gap-1">';
                    $html .= '<a href="' . e($viewUrl) . '" class="btn btn-sm btn-outline-secondary">View</a>';
                    $html .= '<a href="' . e($editUrl) . '" class="btn btn-sm btn-primary">Continue</a>';
                    $html .= '</div>';

                    return $html;
                })
                ->rawColumns(['limit_flag', 'actions']);

            // $datatable->orderColumn('invoice_no', 'draft_reference $1');
            $datatable->orderColumn('invoice_no', "{$ordersTable}.draft_reference $1");
            $datatable->orderColumn('customer_name', "{$ordersTable}.customer_name $1");
            $datatable->orderColumn('created_at', "{$ordersTable}.created_at $1");
            $datatable->orderColumn('sort_stores.name', 'sort_stores.name $1');
            $datatable->orderColumn('id', "{$ordersTable}.id $1");

            $datatable->filterColumn('invoice_no', function ($query, $keyword) use ($ordersTable) {
                $query->where(function ($q) use ($keyword, $ordersTable) {
                    $q->where("{$ordersTable}.draft_reference", 'like', "%{$keyword}%")
                        ->orWhere("{$ordersTable}.invoice_number", 'like', "%{$keyword}%")
                        ->orWhere("{$ordersTable}.id", 'like', "%{$keyword}%");
                });
            });

            return $datatable->make(true);
        }

        return view('admin.sales-invoices-on-hold', [
            'canOverrideCreditLimit' => app(CreditLimitService::class)->canOverrideCreditLimit(),
            'datatableConfig' => app(DatatablePaginationService::class)->config('sales-invoices-on-hold'),
        ]);
    }

    public function resumeSaleInvoice(Request $request, int $orderId)
    {
        if (! hasPermission('update', 'order')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $order = Order::with('orderItems')->find($orderId);
        if (! $order || $order->invoice_status !== 'Draft' || $order->type !== 'offline') {
            return response()->json(['success' => false, 'message' => 'Invoice not found or not on hold.'], 404);
        }

        $overrideCredit = $request->boolean('override_credit_limit')
            && app(CreditLimitService::class)->canOverrideCreditLimit();

        if ($order->credit_limit_blocked && ! $overrideCredit) {
            return response()->json([
                'success' => false,
                'message' => 'This invoice is on hold because the customer credit limit was exceeded. Only users allowed to override credit limits can confirm it.',
                'credit_limit_blocked' => true,
            ], 422);
        }

        DB::beginTransaction();
        try {
            $this->finalizeSaleInvoice($order, $overrideCredit);
            DB::commit();

            return $this->saleInvoicePlacementResponse(
                $order,
                'Invoice resumed and confirmed. Number: ' . ($order->fresh()->invoice_number ?? $order->id),
                true
            );
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json(['success' => false, 'message' => 'Failed to resume invoice: ' . $e->getMessage()], 500);
        }
    }

    private function updateDraftSaleInvoice(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'store_id' => 'nullable|integer',
            'customer_id' => 'required|integer',
            'customer_name' => 'required|string',
            'subtotal_price' => 'required|numeric',
            'total_price' => 'required|numeric',
            'payment_method' => 'nullable|string',
            'invoice_type' => 'required|exists:invoice_options,id',
            'items' => 'required|array|min:1',
            'payment_terms_days' => [
                'nullable',
                Rule::exists('payment_terms', 'id')->where(fn ($q) => $q->where('is_active', 1)),
            ],
            'invoice_status' => 'nullable|in:Draft,Confirmed,Cancelled',
        ]);

        $order = Order::with('orderItems')->findOrFail($request->order_id);
        if ($order->invoice_status !== 'Draft' || $order->type !== 'offline') {
            return response()->json(['success' => false, 'message' => 'Only draft invoices can be edited from this screen.'], 404);
        }

        if ($this->isCreditSaleInvoiceType($request->invoice_type) && ! $request->filled('payment_terms_days')) {
            return response()->json(['success' => false, 'message' => 'Payment terms are required for Credit Sales.'], 422);
        }
        if ((int) $request->invoice_type === self::CASH_SALES_OPTION_ID && ! $request->filled('payment_method')) {
            return response()->json(['success' => false, 'message' => 'Payment method is required for Cash Sales.'], 422);
        }

        $storeId = (int) ($request->input('store_id') ?: $request->input('store_from') ?: $order->store_id);
        $onHold = $this->isInvoiceOnHold($request->input('invoice_status'));
        $overrideCredit = $request->boolean('override_credit_limit')
            && app(CreditLimitService::class)->canOverrideCreditLimit();

        try {
            $resolvedItems = $this->resolveOrderItemsAgainstStock($storeId, $request->items);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        $taxExempt = $request->boolean('tax_exempt');
        $computed = $this->computeInvoiceTotalsWithLineTax(
            $resolvedItems,
            (float) ($request->discount ?? 0),
            (float) ($request->shipping ?? 0),
            $taxExempt
        );
        $resolvedItems = $computed['items'];

        [$paidAmount, $dueAmount, $dueDate, $paymentTermsDays] = $this->buildReceivableFields($request, $computed['total']);

        $creditLimitBlocked = (bool) $order->credit_limit_blocked;
        if ($this->isCreditSaleInvoiceType($request->invoice_type)) {
            $customer = Customer::find($request->customer_id);
            if ($customer) {
                $creditMessage = app(CreditLimitService::class)->customerWouldExceed($customer, $dueAmount, (int) $order->id);
                if ($creditMessage) {
                    $creditLimitBlocked = true;
                    if (! $onHold && ! $overrideCredit) {
                        return response()->json([
                            'success' => false,
                            'message' => $creditMessage,
                            'credit_limit_exceeded' => true,
                        ], 422);
                    }
                } elseif ($onHold) {
                    $creditLimitBlocked = false;
                }
            }
        }

        if (! $onHold && $order->credit_limit_blocked && ! $overrideCredit) {
            return response()->json([
                'success' => false,
                'message' => 'This invoice is on hold because the customer credit limit was exceeded. Use Place Invoice on Hold, or confirm with override permission.',
                'credit_limit_blocked' => true,
            ], 422);
        }

        DB::beginTransaction();
        try {
            OrderItem::where('order_id', $order->id)->delete();

            foreach ($resolvedItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'sku' => $item['sku'],
                    'category_id' => $item['category_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'is_taxable' => (bool) ($item['is_taxable'] ?? false),
                    'tax_rate' => (float) ($item['tax_rate'] ?? 0),
                    'tax_amount' => (float) ($item['tax_amount'] ?? 0),
                ]);
            }

            $order->update([
                'store_id' => $storeId,
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'subtotal_price' => $computed['subtotal'],
                'total_price' => $computed['total'],
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'tax' => 0,
                'tax_exempt' => $taxExempt,
                'tax_amount' => $computed['tax_amount'],
                'discount' => $request->discount ?? 0,
                'shipping' => $request->shipping ?? 0,
                'payment_method' => $request->payment_method,
                'invoice_type' => $request->invoice_type,
                'due_date' => $dueDate,
                'payment_terms_days' => $paymentTermsDays,
                'status' => $this->resolveOrderStatus($request->invoice_type, $dueAmount, $paidAmount),
                'notes' => $request->notes ?? $order->notes,
                'credit_limit_blocked' => $creditLimitBlocked,
            ]);

            if ($onHold) {
                $this->assignInvoiceDocumentFields(
                    $order,
                    (int) $request->invoice_type,
                    $storeId,
                    'Draft'
                );
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Invoice saved on hold (' . ($order->fresh()->draft_reference ?? 'draft') . ').',
                ]);
            }

            $order->load('orderItems');
            $this->finalizeSaleInvoice($order, $overrideCredit);
            DB::commit();

            return $this->saleInvoicePlacementResponse(
                $order,
                'Invoice confirmed. Number: ' . ($order->fresh()->invoice_number ?? $order->id),
                true
            );
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json(['success' => false, 'message' => 'Failed to save invoice: ' . $e->getMessage()], 500);
        }
    }

    public function cancelSaleInvoice(Request $request, int $orderId)
    {
        if (! hasPermission('update', 'order')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $order = Order::find($orderId);
        if (! $order || $order->invoice_status !== 'Draft' || $order->type !== 'offline') {
            return response()->json(['success' => false, 'message' => 'Invoice not found or not on hold.'], 404);
        }

        if ($order->credit_limit_blocked && ! app(CreditLimitService::class)->canOverrideCreditLimit()) {
            return response()->json([
                'success' => false,
                'message' => 'Only users allowed to override credit limits can cancel this invoice.',
            ], 403);
        }

        $order->invoice_status = 'Cancelled';
        $order->save();

        return response()->json(['success' => true, 'message' => 'Sale invoice cancelled.']);
    }

    // ///////////////////////////////////////////////////////////////////////////////////////////////////////
    public function manageTransactions()
    {
        $storeId = session('selected_store_id');

        $transactions = Order::with('orderItems')
            ->where('store_id', $storeId)
            ->where('type', 'online')
            ->latest()
            ->get();

        return view('admin.transaction-manage', compact('transactions'));
    }

    public function getOrderItems($id)
    {
        $order = Order::with('orderItems')->find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $subTotal = floatval($order->subtotal_price);
        $discount = floatval($order->discount);
        $shipping = floatval($order->shipping);
        $tax = floatval($order->tax);

        // Calculate discount and tax amounts and shipping amount
        $discountAmount = ($discount / 100) * $subTotal;
        if ($order->type === 'offline') {
            $taxAmount = ($order->tax_exempt ?? false)
                ? 0.0
                : round((float) ($order->tax_amount ?? 0), 2);
            $shippingAmount = round($shipping, 2);
            $totalBill = round((float) $order->total_price, 2);
            $totalPayable = $totalBill;
        } else {
            // Old global tax / shipping percent logic for online POS orders:
            $taxAmount = ($tax / 100) * $subTotal;
            $shippingAmount = ($shipping / 100) * $subTotal;
            $totalBill = $subTotal - $discountAmount + $shippingAmount + $taxAmount;
            $totalPayable = $totalBill;
        }

        $payments = CustomerPayment::where('order_id', $order->id)
            ->orderBy('payment_date')
            ->orderBy('id')
            ->get();

        // Build modal ledger from posted GL journals (same source as Accounting > Journals),
        // limited to A/R movement so invoice/payment due math remains intuitive.
        $paymentIds = CustomerPayment::query()
            ->where('order_id', $order->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $arCode = (string) config('accounting.accounts.accounts_receivable', '1100');
        $arAccountId = (int) (GlAccount::query()->where('code', $arCode)->value('id') ?? 0);

        $journalEntries = JournalEntry::query()
            ->with('lines')
            ->where('status', 'posted')
            ->where(function ($q) use ($order, $paymentIds) {
                $q->where(function ($sq) use ($order) {
                    $sq->where('source_type', 'order')->where('source_id', $order->id);
                });
                if (!empty($paymentIds)) {
                    $q->orWhere(function ($sq) use ($paymentIds) {
                        $sq->where('source_type', 'customer_payment')->whereIn('source_id', $paymentIds);
                    });
                }
            })
            ->orderBy('entry_date')
            ->orderBy('id')
            ->get();

        $ledgerEntries = [];
        $runningOrderBalance = 0.0;
        foreach ($journalEntries as $journal) {
            $debit = 0.0;
            $credit = 0.0;
            foreach ($journal->lines as $line) {
                if ($arAccountId > 0 && (int) $line->gl_account_id !== $arAccountId) {
                    continue;
                }
                $debit += (float) $line->debit;
                $credit += (float) $line->credit;
            }

            // Skip entries with no A/R impact (keeps modal focused on customer due ledger).
            if (round($debit, 2) == 0.0 && round($credit, 2) == 0.0) {
                continue;
            }

            $runningOrderBalance = round($runningOrderBalance + $debit - $credit, 2);
            $ledgerEntries[] = [
                'entry_date' => format_datetime_ampm($journal->posted_at ?? $journal->entry_date, '-'),
                'entry_type' => $journal->source_type === 'customer_payment' ? 'Payment' : 'Invoice',
                'description' => $journal->description ?: '-',
                'debit' => round($debit, 2),
                'credit' => round($credit, 2),
                'balance_after' => $runningOrderBalance,
            ];
        }

        return response()->json([
            'invoice_no' => $order->invoice_number ?: ('INV/' . $order->id),
            'sub_total' => $subTotal,
            'discount' => $discount,
            'discount_amount' => $discountAmount,
            'shipping' => $shipping,
            'shipping_amount' => $shippingAmount,
            'tax' => $tax,
            'tax_amount' => $taxAmount,
            'total_bill' => $totalBill,
            'total_payable' => $totalPayable,
            'due_amount' => floatval($order->due_amount ?? 0),
            'paid_amount' => floatval($order->paid_amount ?? 0),
            'due_date' => $order->due_date ? format_date($order->due_date) : null,
            'items' => $order->orderItems->map(function ($item) {
                return [
                    'product_name' => $item->product_name,
                    'price' => floatval($item->price),
                    'qty' => intval($item->qty),
                ];
            }),
            'payments' => $payments->map(function ($payment) {
                return [
                    'payment_date' => format_date($payment->payment_date, '-'),
                    'amount' => (float) $payment->amount,
                    'payment_method' => $payment->payment_method ?: '-',
                    'reference_no' => $payment->reference_no ?: '-',
                ];
            }),
            /*
            Legacy source (kept for reference): customer_ledger_entries
            'ledger_entries' => $ledgerEntries->map(function ($entry) { ... });
            */
            'ledger_entries' => $ledgerEntries,
        ]);
    }

    public function invoiceOrderCreate(Request $request)
    {
         //dd($request->all());
        // if (auth()->user()->role_id != 5) {
        if (!hasPermission('create', 'order')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }
        // }
        $storeId = (int) ($request->input('store_from') ?: session('selected_store_id'));

        // Validate invoice-level required inputs
        $request->validate([
            'customer_id' => 'required|integer',
            'payment_method' => 'nullable|string',
            // Old code:
            // 'invoice_type' => 'required|in:1,2,3',
            'invoice_type' => 'required|exists:invoice_options,id',
            // Old code:
            // 'due_date' => 'nullable|date',
            // 'payment_terms_days' => 'nullable|integer|min:0|max:3650',
            'payment_terms_days' => [
                'nullable',
                Rule::exists('payment_terms', 'id')->where(fn($q) => $q->where('is_active', 1)),
            ],
            'invoice_status' => 'nullable|in:Draft,Confirmed,Cancelled',
        ]);

        if ($this->isCreditSaleInvoiceType($request->invoice_type) && !$request->filled('payment_terms_days')) {
            return response()->json(['success' => false, 'message' => 'Payment terms are required for Credit Sales.'], 422);
        }
        if ((int) $request->invoice_type === self::CASH_SALES_OPTION_ID && !$request->filled('payment_method')) {
            return response()->json(['success' => false, 'message' => 'Payment method is required for Cash Sales.'], 422);
        }

        // Extract arrays from the request
        $productIds = $request->input('product_id', []);
        $productNames = $request->input('product_name', []);
        $skus = $request->input('sku', []);
        $categories = $request->input('category_id', []);
        $quantities = $request->input('qty', []);
        $prices = $request->input('price', []);
        $lineIsTaxable = $request->input('line_is_taxable', []);
        $lineTaxRates = $request->input('line_tax_rate', []);
        $taxExempt = $request->boolean('tax_exempt');

        $items = [];
        for ($i = 0; $i < count($productIds); $i++) {
            $items[] = [
                'product_id' => $productIds[$i],
                'product_name' => $productNames[$i],
                'sku' => $skus[$i],
                'category_id' => $categories[$i],
                'qty' => (float) $quantities[$i],
                'price' => (float) $prices[$i],
                'is_taxable' => $taxExempt ? false : $this->parseLineTaxableFlag($lineIsTaxable[$i] ?? 0),
                'tax_rate' => $taxExempt ? 0 : (float) ($lineTaxRates[$i] ?? 0),
            ];
        }

        $discount = (float) $request->input('discount', 0);
        $shipping = (float) $request->input('shipping', 0);

        // Old global tax calculation:
        // $tax = (float) $request->input('tax', 0);
        // $total = $subtotal + ($subtotal * $tax / 100) - ($subtotal * $discount / 100) + $shipping;

        $computed = $this->computeInvoiceTotalsWithLineTax($items, $discount, $shipping, $taxExempt);

        // Prepare data to pass to placeOrder
        $orderData = new Request(array_filter([
            'order_id' => $request->input('order_id'),
            'store_id' => $storeId,
            'store_from' => $storeId,
            'customer_id' => $request->customer_id,
            'customer_name' => Customer::find($request->customer_id)?->customer_name,
            'subtotal_price' => $computed['subtotal'],
            'total_price' => $computed['total'],
            'tax' => 0,
            'tax_exempt' => $taxExempt,
            'tax_amount' => $computed['tax_amount'],
            'discount' => $discount,
            'shipping' => $shipping,
            'items' => $computed['items'],
            'payment_method' => $request->payment_method,
            'invoice_type' => $request->invoice_type,
            'payment_terms_days' => $request->payment_terms_days,
            'invoice_status' => $request->invoice_status,
            'override_credit_limit' => $request->input('override_credit_limit'),
            'order_date' => $request->input('order_date'),
            'type' => 'offline',
        ], fn ($v) => $v !== null && $v !== ''));

        // storeHistory(
        //     'invoice_order_form',  // key
        //     'created',  // event
        //     'invoice_order',  // entity type
        //     null,  // entity id (none yet)
        //     $orderData->all(),  // new values
        //     null  // no old values
        // );

        return $this->placeOrder($orderData);
    }

    // public function invoiceOrderUpdate(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $orderId = $request->input('order_id');
    //         $order = Order::with('orderItems')->findOrFail($orderId);

    //         // Reverse previous stock changes
    //         foreach ($order->orderItems as $oldItem) {
    //             Stock::where('store_id', $order->store_id)
    //                 ->where('product_id', $oldItem->product_id)
    //                 ->increment('quantity', $oldItem->qty);
    //         }

    //         // Delete old items
    //         OrderItem::where('order_id', $orderId)->delete();

    //         // Recalculate items and subtotal
    //         $items = [];
    //         $subtotal = 0;

    //         $productIds = $request->input('product_id', []);
    //         $productNames = $request->input('product_name', []);
    //         $skus = $request->input('sku', []);
    //         $quantities = $request->input('qty', []);
    //         $prices = $request->input('price', []);
    //         $categories = $request->input('category_id', []);

    //         for ($i = 0; $i < count($productIds); $i++) {
    //             $qty = (float) $quantities[$i];
    //             $price = (float) $prices[$i];
    //             $lineTotal = $qty * $price;

    //             $items[] = [
    //                 'order_id' => $orderId,
    //                 'product_id' => $productIds[$i],
    //                 'product_name' => $productNames[$i],
    //                 'sku' => $skus[$i],
    //                 'category_id' => $categories[$i],
    //                 'qty' => $qty,
    //                 'price' => $price,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ];

    //             $subtotal += $lineTotal;

    //             // Decrease stock
    //             Stock::where('store_id', $order->store_id)
    //                 ->where('product_id', $productIds[$i])
    //                 ->decrement('quantity', $qty);
    //         }

    //         OrderItem::insert($items);

    //         // Tax, discount, and total calculation
    //         $tax = (float) $request->input('tax', 0);
    //         $discount = (float) $request->input('discount', 0);
    //         $shipping = 0;

    //         $total = $subtotal + ($subtotal * $tax / 100) - ($subtotal * $discount / 100) + $shipping;

    //         $order->update([
    //             'customer_id' => $request->input('customer_id'),
    //             'subtotal_price' => round($subtotal, 2),
    //             'total_price' => round($total, 2),
    //             'tax' => $tax,
    //             'discount' => $discount,
    //             'payment_method' => $request->input('payment_method'),
    //         ]);

    //         DB::commit();

    //         return response()->json(['success' => true, 'message' => 'Invoice updated successfully.']);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()]);
    //     }
    // }
    public function invoiceOrderUpdate(Request $request)
    {
        // if (auth()->user()->role_id != 5) {
        if (!hasPermission('update', 'order')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }
        // }
        DB::beginTransaction();

        try {
            /* ────── 1  Fetch order and old snapshots ────── */
            $orderId = $request->input('order_id');
            $order = Order::with('orderItems')->findOrFail($orderId);

            $productIds = $request->input('product_id', []);
            $quantities = $request->input('qty', []);
            // Old: trusted client `price` on invoice edit (could be forged via DevTools).
            // $prices = $request->input('price', []);

            $lines = [];
            for ($i = 0; $i < count($productIds); $i++) {
                $lines[] = [
                    'product_id' => $productIds[$i],
                    'qty' => $quantities[$i] ?? 0,
                ];
            }

            try {
                $resolvedItems = $this->resolveInvoiceEditLines((int) $order->store_id, $lines, $order->orderItems);
            } catch (\InvalidArgumentException $e) {
                DB::rollBack();

                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }

            $oldOrder = $order->toArray();
            $oldItems = $order->orderItems->keyBy('product_id')->toArray();

            /* ────── 2  Reverse previous stock changes ────── */
            foreach ($order->orderItems as $oldItem) {
                Stock::where('store_id', $order->store_id)
                    ->where('product_id', $oldItem->product_id)
                    ->increment('quantity', $oldItem->qty);
            }

            /* ────── 3  Delete old items ────── */
            OrderItem::where('order_id', $orderId)->delete();

            /* ────── 4  Rebuild items & adjust stock ────── */
            $items = [];
            $subtotal = 0;
            $newStocks = [];

            foreach ($resolvedItems as $item) {
                $qty = $item['qty'];
                $price = $item['price'];
                $subtotal += $qty * $price;

                $items[] = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'sku' => $item['sku'],
                    'category_id' => $item['category_id'],
                    'qty' => $qty,
                    'price' => $price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                Stock::where('store_id', $order->store_id)
                    ->where('product_id', $item['product_id'])
                    ->decrement('quantity', $qty);

                // snapshot stock after decrement
                $newStocks[$item['product_id']] = Stock::where('store_id', $order->store_id)
                    ->where('product_id', $item['product_id'])
                    ->first()
                    ->toArray();
            }

            OrderItem::insert($items);

            /* ────── 5  Recompute totals & update order ────── */
            $tax = (float) $request->input('tax', 0);
            $discount = (float) $request->input('discount', 0);
            $shipping = 0;

            $total = $subtotal + ($subtotal * $tax / 100) - ($subtotal * $discount / 100) + $shipping;
            $currentPaidAmount = (float) ($order->paid_amount ?? 0);
            $newDueAmount = max(0, round($total - $currentPaidAmount, 2));

            $order->update([
                'customer_id' => $request->input('customer_id'),
                'subtotal_price' => round($subtotal, 2),
                'total_price' => round($total, 2),
                'due_amount' => $newDueAmount,
                'status' => $this->resolveOrderStatus($order->invoice_type, $newDueAmount, $currentPaidAmount),
                'tax' => $tax,
                'discount' => $discount,
                'payment_method' => $request->input('payment_method'),
            ]);

            $this->assignInvoiceDocumentFields(
                $order,
                (int) $order->invoice_type,
                (int) $order->store_id,
                $request->input('invoice_status')
            );

            $orderTotalDiff = round(((float) $order->total_price) - ((float) ($oldOrder['total_price'] ?? 0)), 2);
            if (abs($orderTotalDiff) > 0.0001 && !empty($order->customer_id)) {
                $this->createCustomerLedgerEntry(
                    customerId: (int) $order->customer_id,
                    orderId: (int) $order->id,
                    paymentId: null,
                    entryType: 'adjustment',
                    description: 'Invoice ' . ($order->invoice_number ?: ('INV/' . $order->id)) . ' updated (value adjustment)',
                    debit: $orderTotalDiff > 0 ? abs($orderTotalDiff) : 0,
                    credit: $orderTotalDiff < 0 ? abs($orderTotalDiff) : 0,
                    meta: ['reason' => 'invoice_update']
                );
            }

            /* ────── 6  New snapshots ────── */
            $newOrder = $order->fresh()->toArray();
            $newItems = OrderItem::where('order_id', $orderId)->get()->keyBy('product_id')->toArray();

            /* ────── 7  Audit trail ────── */
            $masterNew = array_diff_assoc_recursive($newOrder, $oldOrder);
            $masterOld = array_diff_assoc_recursive($oldOrder, $newOrder);

            $itemsNew = array_diff_assoc_recursive($newItems, $oldItems);
            $itemsOld = array_diff_assoc_recursive($oldItems, $newItems);

            storeHistory(
                'pos_order_form',
                'updated',
                'order',
                $orderId,
                ['order' => $masterNew, 'items' => $itemsNew, 'stock_after' => $newStocks],
                ['order' => $masterOld, 'items' => $itemsOld]
            );

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Invoice updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Resolve invoice/POS lines against `stocks` for the active store.
     * Client-submitted product_name, sku, and category are ignored so DevTools cannot forge them.
     *
     * @param  array<int, array<string, mixed>>  $items  each entry needs product_id, qty, price
     * @return array<int, array{product_id: mixed, product_name: mixed, sku: mixed, category_id: mixed, qty: float, price: float}>
     */
    /**
     * Invoice edit: unit price must come from the existing order line if the product was already
     * on the invoice; otherwise use current `stocks.price` (new line added in the editor).
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\OrderItem>  $originalOrderItems  snapshot before this edit
     */
    private function resolveInvoiceEditLines(int $storeId, array $lines, $originalOrderItems): array
    {
        if ($storeId < 1) {
            throw new \InvalidArgumentException('Please select a store first.');
        }

        $resolved = [];

        foreach ($lines as $index => $line) {
            $productId = $line['product_id'] ?? null;
            if ($productId === null || $productId === '') {
                throw new \InvalidArgumentException('Invalid product on line ' . ($index + 1) . '.');
            }

            $stock = Stock::where('store_id', $storeId)
                ->where('product_id', $productId)
                ->first();

            if (! $stock) {
                throw new \InvalidArgumentException(
                    'Invalid product reference on line ' . ($index + 1) . '. Refresh the page and choose products from the list only.'
                );
            }

            $qty = $line['qty'] ?? null;
            if (! is_numeric($qty) || (float) $qty <= 0) {
                throw new \InvalidArgumentException('Invalid quantity on line ' . ($index + 1) . '.');
            }

            $oldItem = $originalOrderItems->firstWhere('product_id', $productId);
            $unitPrice = $oldItem !== null
                ? (float) $oldItem->price
                : (float) $stock->price;

            $resolved[] = [
                'product_id' => $stock->product_id,
                'product_name' => $stock->product_name,
                'sku' => $stock->sku,
                'category_id' => $stock->category_id,
                'qty' => (float) $qty,
                'price' => $unitPrice,
            ];
        }

        return $resolved;
    }

    private function resolveOrderItemsAgainstStock(int $storeId, array $items): array
    {
        if ($storeId < 1) {
            throw new \InvalidArgumentException('Please select a store first.');
        }

        $resolved = [];

        foreach ($items as $index => $item) {
            $productId = trim((string) ($item['product_id'] ?? ''));
            if ($productId === '') {
                throw new \InvalidArgumentException('Invalid product on line ' . ($index + 1) . '.');
            }

            $stock = Stock::where('store_id', $storeId)
                ->where('product_id', $productId)
                ->first();

            // Legacy drafts may have stored stocks.id instead of stocks.product_id.
            if (! $stock && ctype_digit($productId)) {
                $stock = Stock::where('store_id', $storeId)
                    ->where('id', (int) $productId)
                    ->first();
            }

            if (! $stock) {
                throw new \InvalidArgumentException(
                    'Invalid product reference on line ' . ($index + 1) . '. Refresh the page and choose products from the list only.'
                );
            }

            $qty = $item['qty'] ?? null;
            if (! is_numeric($qty) || (float) $qty <= 0) {
                throw new \InvalidArgumentException('Invalid quantity on line ' . ($index + 1) . '.');
            }

            $price = $item['price'] ?? null;
            if (! is_numeric($price) || (float) $price < 0) {
                throw new \InvalidArgumentException('Invalid price on line ' . ($index + 1) . '.');
            }

            $lineTaxable = array_key_exists('is_taxable', $item)
                ? (bool) $item['is_taxable']
                : (bool) ($stock->is_taxable ?? true);
            $lineTaxRate = array_key_exists('tax_rate', $item)
                ? (float) $item['tax_rate']
                : (float) ($stock->tax_rate ?? 0);

            $resolved[] = [
                'product_id' => $stock->product_id,
                'product_name' => $stock->product_name,
                'sku' => $stock->sku,
                'category_id' => $stock->category_id,
                'qty' => (float) $qty,
                'price' => (float) $price,
                'is_taxable' => $lineTaxable,
                'tax_rate' => $lineTaxRate,
            ];
        }

        return $resolved;
    }

    private function parseLineTaxableFlag(mixed $value): bool
    {
        if (is_array($value)) {
            $value = end($value);
        }

        return in_array((string) $value, ['1', 'true', 'on'], true) || $value === 1 || $value === true;
    }

    /**
     * Points 13–14: per-line tax with optional invoice-level tax exempt override.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array{subtotal: float, tax_amount: float, discount_amount: float, total: float, items: array<int, array<string, mixed>>}
     */
    private function computeInvoiceTotalsWithLineTax(
        array $items,
        float $discountPercent,
        float $shipping,
        bool $taxExempt
    ): array {
        $subtotal = 0.0;
        $taxAmount = 0.0;

        foreach ($items as &$item) {
            $lineSubtotal = round((float) $item['qty'] * (float) $item['price'], 2);
            $subtotal += $lineSubtotal;

            $isTaxable = ! $taxExempt && (bool) ($item['is_taxable'] ?? false);
            $rate = $isTaxable ? (float) ($item['tax_rate'] ?? 0) : 0.0;
            $lineTax = round($lineSubtotal * $rate / 100, 2);

            $item['is_taxable'] = $isTaxable;
            $item['tax_rate'] = $rate;
            $item['tax_amount'] = $lineTax;
            $taxAmount += $lineTax;
        }
        unset($item);

        $subtotal = round($subtotal, 2);
        $taxAmount = round($taxAmount, 2);
        $discountAmount = round($subtotal * $discountPercent / 100, 2);
        $total = round($subtotal + $taxAmount - $discountAmount + $shipping, 2);

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'items' => $items,
        ];
    }

    private function buildReceivableFields(Request $request, float $totalAmount): array
    {
        $invoiceTypeId = (int) ($request->input('invoice_type') ?? 0);
        $isCreditSale = $invoiceTypeId === self::CREDIT_SALES_OPTION_ID;
        $isEstimate = $invoiceTypeId === self::ESTIMATE_OPTION_ID;
        $isCashSale = $invoiceTypeId === self::CASH_SALES_OPTION_ID;

        // Immediate-payment invoices are treated as fully paid by default.
        $paidAmount = $isCreditSale ? 0.0 : round($totalAmount, 2);
        if ($isEstimate) {
            // Estimate is not an actual receivable posting.
            $paidAmount = 0.0;
        }
        $dueAmount = max(0, round($totalAmount - $paidAmount, 2));

        // Save only selected term ID (1..4) in orders.payment_terms_days.
        $paymentTermsDays = $isCreditSale && $request->filled('payment_terms_days')
            ? (int) $request->input('payment_terms_days')
            : null;

        $dueDate = null;
        if ($isCreditSale && $paymentTermsDays !== null) {
            // Old code:
            // $dueDate = match ($paymentTermsDays) { ... };
            $term = PaymentTerm::where('id', $paymentTermsDays)
                ->where('is_active', 1)
                ->first();
            if ($term) {
                $dueDateBase = now();
                if (!empty($term->due_days)) {
                    $dueDateBase = $dueDateBase->addDays((int) $term->due_days);
                }
                if (!empty($term->due_months)) {
                    $dueDateBase = $dueDateBase->addMonths((int) $term->due_months);
                }
                $dueDate = $dueDateBase->toDateString();
            }
        } elseif ($isCashSale) {
            // Cash sales are due on invoice date.
            $dueDate = now()->toDateString();
        }

        return [$paidAmount, $dueAmount, $dueDate, $paymentTermsDays];
    }

    private function assignInvoiceDocumentFields(
        Order $order,
        int $invoiceTypeId,
        int $storeId,
        ?string $requestedInvoiceStatus = null
    ): void {
        if (! Schema::hasColumn('orders', 'invoice_status') || ! Schema::hasColumn('orders', 'invoice_number')) {
            return;
        }

        $allowedStatuses = ['Draft', 'Confirmed', 'Cancelled'];
        if ($requestedInvoiceStatus && in_array($requestedInvoiceStatus, $allowedStatuses, true)) {
            $order->invoice_status = $requestedInvoiceStatus;
        } else {
            $order->invoice_status = $invoiceTypeId === self::ESTIMATE_OPTION_ID ? 'Draft' : 'Confirmed';
        }

        if ($order->invoice_status === 'Draft' && $order->id) {
            $order->invoice_number = null;
            if (Schema::hasColumn('orders', 'draft_reference')) {
                app(DraftReferenceService::class)->assignForOrder($order);
            }
        } elseif ($order->invoice_status !== 'Draft' && empty($order->invoice_number) && $order->id) {
            $this->assignInvoiceNumber($order, $storeId);
        }

        $order->save();
    }

    private function assignInvoiceNumber(Order $order, int $storeId): void
    {
        if (
            $storeId > 0
            && Schema::hasColumn('stores', 'next_sale_invoice_number')
            && Schema::hasColumn('stores', 'current_sale_invoice_sequence')
        ) {
            $order->invoice_number = app(SaleInvoiceNumberService::class)->issueNextForStore($storeId);

            return;
        }

        $storePrefix = 'INV/';
        if ($storeId > 0 && Schema::hasColumn('stores', 'invoice_prefix')) {
            $storePrefix = Store::whereKey($storeId)->value('invoice_prefix') ?: $storePrefix;
        }
        $order->invoice_number = $storePrefix . $order->id;
    }

    private function isInvoiceOnHold(?string $invoiceStatus): bool
    {
        return $invoiceStatus === 'Draft';
    }

    private function finalizeSaleInvoice(Order $order, bool $overrideCreditLimit = false): void
    {
        if ($order->invoice_status !== 'Draft') {
            throw new \InvalidArgumentException('Only on-hold invoices can be resumed.');
        }

        $customer = Customer::find($order->customer_id);
        if ($customer && ! $overrideCreditLimit) {
            $creditMessage = app(CreditLimitService::class)->customerWouldExceed($customer, (float) $order->due_amount, (int) $order->id);
            if ($creditMessage) {
                throw new \InvalidArgumentException($creditMessage);
            }
        }

        foreach ($order->orderItems as $item) {
            $stock = Stock::where('store_id', $order->store_id)
                ->where('product_id', $item->product_id)
                ->first();

            if (! $stock || $stock->quantity < $item->qty) {
                throw new \InvalidArgumentException(
                    "Insufficient stock for {$item->product_name} (SKU {$item->sku})"
                );
            }
        }

        foreach ($order->orderItems as $item) {
            Stock::where('store_id', $order->store_id)
                ->where('product_id', $item->product_id)
                ->decrement('quantity', $item->qty);

            $movedStock = Stock::where('store_id', $order->store_id)
                ->where('product_id', $item->product_id)
                ->first();

            if ($movedStock) {
                app(ProductUnitCostService::class)->syncStock($movedStock);
            }
        }

        $order->invoice_status = 'Confirmed';
        $order->credit_limit_blocked = false;
        $order->draft_reference = null;
        $this->assignInvoiceNumber($order, (int) $order->store_id);
        $order->save();

        if ((int) $order->status !== 3 && (float) $order->due_amount > 0) {
            $this->createCustomerLedgerEntry(
                customerId: (int) $order->customer_id,
                orderId: (int) $order->id,
                paymentId: null,
                entryType: 'invoice',
                description: 'Invoice ' . ($order->invoice_number ?: ('INV/' . $order->id)) . ' posted',
                debit: (float) $order->total_price,
                credit: 0,
                meta: [
                    'invoice_type' => $order->invoice_type,
                    'due_date' => optional($order->due_date)->toDateString(),
                ]
            );
        }

        try {
            app(AccountingService::class)->postSaleOrder($order->fresh());
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function resolveOrderStatus($invoiceTypeId, float $dueAmount, float $paidAmount): int
    {
        if ((int) $invoiceTypeId === self::ESTIMATE_OPTION_ID) {
            return 3; // Estimate
        }

        if ($dueAmount <= 0) {
            return 1; // Paid
        }

        if ($paidAmount > 0 && $dueAmount > 0) {
            return 2; // Partial
        }

        return 0; // Unpaid
    }

    private function isCreditSaleInvoiceType($invoiceTypeId): bool
    {
        return (int) $invoiceTypeId === self::CREDIT_SALES_OPTION_ID;
    }

    private function saleInvoicePlacementResponse(Order $order, string $message, bool $openPrint = false): \Illuminate\Http\JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if ($openPrint) {
            $order->refresh();
            $payload['order_id'] = (int) $order->id;
            $payload['open_print'] = true;
            $payload['invoice_number'] = $order->invoice_number ?: ('INV/' . $order->id);
            $payload['customer_id'] = $order->customer_id;
            $payload['customer_name'] = $order->customer_name;
            // $payload['order_date'] = optional($order->created_at)->format('d.m.Y') ?? now()->format('d.m.Y');
            $payload['order_date'] = format_date($order->created_at ?? now());
            $payload['due_date'] = $order->due_date
                ? format_date($order->due_date)
                : '-';
            $payload['total'] = '$' . number_format((float) $order->total_price, 2);
        }

        return response()->json($payload);
    }

    private function createCustomerLedgerEntry(
        int $customerId,
        ?int $orderId,
        ?int $paymentId,
        string $entryType,
        string $description,
        float $debit,
        float $credit,
        array $meta = []
    ): CustomerLedgerEntry {
        // Old code:
        // $latestBalance = (float) (CustomerLedgerEntry::where('customer_id', $customerId)->latest('id')->value('balance_after') ?? 0);
        // For order-linked entries we keep running balance per order.
        $balanceQuery = CustomerLedgerEntry::where('customer_id', $customerId);
        if (!empty($orderId)) {
            $balanceQuery->where('order_id', $orderId);
        } else {
            $balanceQuery->whereNull('order_id');
        }
        $latestBalance = (float) ($balanceQuery->latest('id')->value('balance_after') ?? 0);
        $newBalance = round($latestBalance + $debit - $credit, 2);

        return CustomerLedgerEntry::create([
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'payment_id' => $paymentId,
            'entry_type' => $entryType,
            'entry_date' => now(),
            'description' => $description,
            'debit' => $debit,
            'credit' => $credit,
            'balance_after' => $newBalance,
            'meta' => $meta,
        ]);
    }

    /**
     * Update unit price for an invoice line before the invoice is generated.
     * Saves into `stocks.price` so the updated price will be used in OrderItem creation.
     */
    public function invoiceItemPriceUpdate(Request $request)
    {
        // if (auth()->user()->role_id != 5) {
        if (!hasPermission('update', 'order')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }
        // }

        $request->validate([
            'store_id' => 'required|integer|exists:stores,id',
            'product_id' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        $storeId = (int) $request->input('store_id');
        $productId = $request->input('product_id');
        $price = (float) $request->input('price');

        $stock = Stock::where('store_id', $storeId)
            ->where('product_id', $productId)
            ->first();

        if (!$stock) {
            return response()->json(['success' => false, 'message' => 'Stock item not found for this store/product.'], 404);
        }

        $stock->price = $price;
        $stock->save();
     

        return response()->json([
            'success' => true,
            'message' => 'Unit price updated successfully.',
            'price' => $stock->price,
        ]);
    }


}


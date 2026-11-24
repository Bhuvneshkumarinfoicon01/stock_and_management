<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\AccountingService;
use App\Services\DatatablePaginationService;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\InvoiceOption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTerm;
use App\Models\Stock;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    private const CASH_SALES_OPTION_ID = 1;
    private const CREDIT_SALES_OPTION_ID = 2;
    private const ESTIMATE_OPTION_ID = 3;

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

    private function creditInvoiceTypeIds(): array
    {
        return [self::CREDIT_SALES_OPTION_ID];
    }

    private function salesInvoiceTypeIds(): array
    {
        return [self::CASH_SALES_OPTION_ID, self::CREDIT_SALES_OPTION_ID];
    }

    /** @return array<int, string> */
    private function salesInvoiceScopeLabels(): array
    {
        return [
            'sales-all' => 'All Sales Invoices',
            'cash-sales' => 'Cash Sales',
            'all' => 'All Credit Invoices',
            'paid' => 'Paid Credit Invoices',
            'unpaid' => 'Unpaid Credit Invoices',
            'partial' => 'Partial Credit Invoices',
            'overdue' => 'Overdue Credit Invoices',
            //'estimates' => 'Estimates',
        ];
    }

    private function allowedSalesInvoiceScopes(): array
    {
        return array_keys($this->salesInvoiceScopeLabels());
    }

    private function applySalesInvoiceScope($query, string $scope): void
    {
        $ordersTable = $query->getModel()->getTable();

        if ($scope === 'sales-all') {
            // All cash + credit sales invoices; exclude estimates.
            $query->whereIn("{$ordersTable}.invoice_type", $this->salesInvoiceTypeIds())
                ->where("{$ordersTable}.status", '!=', self::ESTIMATE_OPTION_ID);

            // Old filter used invoice_status and incorrectly included estimates (Point — All Sales Invoices).
            // $query->where(function ($q) {
            //     $q->where('invoice_status', 'Confirmed')
            //         ->orWhereNull('invoice_status')
            //         ->orWhere('status', self::ESTIMATE_OPTION_ID);
            // });

            return;
        }

        if (! in_array($scope, ['estimates', 'cash-sales'], true)) {
            $query->whereIn("{$ordersTable}.invoice_type", $this->creditInvoiceTypeIds());
        }

        match ($scope) {
            'paid' => $query->where("{$ordersTable}.status", 1),
            'unpaid' => $query->where("{$ordersTable}.status", 0),
            'partial' => $query->where("{$ordersTable}.status", 2),
            'overdue' => $query->whereDate("{$ordersTable}.due_date", '<', now()->toDateString())
                ->where("{$ordersTable}.due_amount", '>', 0),
            'estimates' => $query->where("{$ordersTable}.status", self::ESTIMATE_OPTION_ID),
            'cash-sales' => $query->where("{$ordersTable}.invoice_type", self::CASH_SALES_OPTION_ID),
            default => null,
        };
    }

    private function salesInvoiceActionHtml($row, string $scope): string
    {
        $viewUrl = route('admin.sales-invoices.show', $row->id);
        $isEstimate = (int) $row->status === self::ESTIMATE_OPTION_ID;
        if ($scope === 'estimates' || ($scope === 'sales-all' && $isEstimate)) {
            return '
                <div class="edit-delete-action">
                    <a class="me-2 p-2" href="' . e($viewUrl) . '" title="View invoice">
                        <i data-feather="eye" class="feather-eye"></i>
                    </a>
                    <a class="btn btn-sm btn-outline-primary convert-estimate-invoice" href="javascript:void(0);" data-id="' . $row->id . '" title="Change Invoice Type">
                        Change Invoice Type
                    </a>
                </div>
            ';
        }

        if ((float) ($row->due_amount ?? 0) <= 0) {
            return '
                <div class="edit-delete-action">
                    <a class="me-2 p-2" href="' . e($viewUrl) . '" title="View invoice">
                        <i data-feather="eye" class="feather-eye"></i>
                    </a>
                </div>
            ';
        }

        return '
            <div class="edit-delete-action">
                <a class="me-2 p-2" href="' . e($viewUrl) . '" title="View invoice">
                    <i data-feather="eye" class="feather-eye"></i>
                </a>
                <a class="me-2 p-2 add-order-payment" href="javascript:void(0);" data-id="' . $row->id . '" data-customer-id="' . (int) $row->customer_id . '" data-due="' . (float) $row->due_amount . '" title="Receive Payment">
                    <i data-feather="dollar-sign" class="feather-dollar-sign"></i>
                </a>
            </div>
        ';
    }

    private function salesInvoicesDataTable(Request $request, string $scope)
    {
        $ordersTable = (new Order)->getTable();
        $query = Order::query()
            ->select("{$ordersTable}.*")
            ->leftJoin('stores as sort_stores', "{$ordersTable}.store_id", '=', 'sort_stores.id')
            ->with(['store'])
            ->where("{$ordersTable}.type", 'offline');
        $this->applySalesInvoiceScope($query, $scope);

        // Old default sort forced orders.id DESC before user sort, so clicking Store Name only
        // re-sorted within the same id order and appeared to do nothing.
        // $query->orderByDesc("{$ordersTable}.id");

        $invoiceTypeMap = InvoiceOption::orderBy('id')->pluck('name', 'id')->toArray();

        $datatable = datatables()
            ->eloquent($query)
            ->filter(function ($query) use ($request) {
                $search = strtolower(trim((string) $request->input('search.value', '')));
                if ($search === '') {
                    return;
                }

                $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search) . '%';
                $query->where(function ($q) use ($like) {
                    $q->whereRaw('LOWER(orders.id) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(orders.customer_name) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(orders.invoice_number) LIKE ?', [$like])
                        ->orWhereHas('store', function ($storeQuery) use ($like) {
                            $storeQuery->whereRaw('LOWER(name) LIKE ?', [$like]);
                        });
                });
            }, false)
            ->addColumn('invoice_no', fn ($row) => $row->invoice_number ?: ('INV/' . $row->id))
            ->addColumn('invoice_type_label', fn ($row) => e($invoiceTypeMap[(int) $row->invoice_type] ?? '-'))
            ->addColumn('customer_name', fn ($row) => e($row->customer_name ?? '-'))
            ->addColumn('store_name', fn ($row) => $row->store->name ?? '-')
            ->addColumn('created_at', fn ($row) => format_date($row->created_at, '-'))
            ->addColumn('total_price', fn ($row) => '$' . number_format((float) $row->total_price, 2, '.', ','))
            ->addColumn('paid_amount', fn ($row) => '$' . number_format((float) ($row->paid_amount ?? 0), 2, '.', ','))
            ->addColumn('due_amount', fn ($row) => '$' . number_format((float) ($row->due_amount ?? 0), 2, '.', ','))
            ->addColumn('due_date', fn ($row) => $row->due_date ? format_date($row->due_date, '-') : '-')
            ->addColumn('overdue', function ($row) {
                $isOverdue = ! empty($row->due_date)
                    && Carbon::parse($row->due_date)->lt(now())
                    && (float) ($row->due_amount ?? 0) > 0;

                return $isOverdue
                    ? '<span class="badge badge-linedanger">Overdue</span>'
                    : '<span class="badge badge-linesuccess">No</span>';
            })
            ->addColumn('status', function ($row) {
                return match ((int) $row->status) {
                    1 => '<span class="badge badge-linesuccess">Paid</span>',
                    2 => '<span class="badge bg-warning text-dark">Partial</span>',
                    3 => '<span class="badge badge-lineinfo">Estimate</span>',
                    default => '<span class="badge badge-linedanger">Unpaid</span>',
                };
            })
            ->addColumn('action', fn ($row) => $this->salesInvoiceActionHtml($row, $scope))
            ->rawColumns(['status', 'overdue', 'action']);

        // Default sort is set client-side (invoice id DESC). orderColumn keeps joins unambiguous.
        $datatable->orderColumn('id', "{$ordersTable}.id $1");
        $datatable->orderColumn('sort_stores.name', 'sort_stores.name $1');

        return $datatable->make(true);
    }

    public function allSalesInvoices(Request $request, string $scope = 'sales-all')
    {
        if (! in_array($scope, $this->allowedSalesInvoiceScopes(), true)) {
            abort(404);
        }

        if ($request->ajax()) {
            return $this->salesInvoicesDataTable($request, $scope);
        }

        $paymentTerms = PaymentTerm::where('is_active', 1)->orderBy('sort_order')->get(['id', 'label']);
        $invoiceOptions = InvoiceOption::orderBy('id')->get(['id', 'name']);

        return view('admin.sales-invoices', [
            'scope' => $scope,
            'scopeLabels' => $this->salesInvoiceScopeLabels(),
            'paymentTerms' => $paymentTerms,
            'invoiceOptions' => $invoiceOptions,
            'datatableConfig' => app(DatatablePaginationService::class)->config('sales-invoices'),
        ]);
    }

    public function salesInvoicesShow(Order $order)
    {
        if ($order->type !== 'offline') {
            abort(404);
        }

        $order->load([
            'store',
            'customer',
            'orderItems',
            'payments' => fn ($query) => $query->latest('created_at'),
        ]);

        return view('admin.sales-invoice-show', [
            'invoice' => $order,
        ]);
    }

    public function index(Request $request)
    {
        // Old code:
        // $invoice_options = [1 => 'Cash Sales', 2 => 'Credit Sales', 3 => 'Estimates'];
        // $invoice_options = InvoiceOption::orderBy('id')->pluck('name', 'id');
        $invoice_options = InvoiceOption::ensureDefaults();
        $paymentTerms = PaymentTerm::where('is_active', 1)->orderBy('sort_order')->get(['id', 'label']);
        $customers = Customer::where('status', 1)->get();
        $discount_percentage = User::where('role_id', 5)->value('max_discount');
        if ($request->ajax()) {
            $invoiceTypeMap = $invoice_options->toArray();
            $ordersTable = (new Order)->getTable();
            $query = Order::query()
                ->select("{$ordersTable}.*")
                ->leftJoin('stores as sort_stores', "{$ordersTable}.store_id", '=', 'sort_stores.id')
                ->with(['orderItems', 'store'])
                ->where("{$ordersTable}.type", 'offline');

            if ($request->has('store') && $request->store !== 'choose') {
                $query->where("{$ordersTable}.store_id", $request->store);
            }
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween("{$ordersTable}.created_at", [
                    Carbon::parse($request->date_from)->startOfDay(),
                    Carbon::parse($request->date_to)->endOfDay(),
                ]);
            }

            // Old manual sort blocked DataTables column sorting after store join.
            // if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
            //     $query->orderBy('created_at', $request->order_type);
            // } else {
            //     $query->orderByDesc('id');
            // }

            $datatable = datatables()
                ->eloquent($query)
                ->addColumn('order_id', fn($row) => $row->id)
                ->addColumn('invoice_no', fn($row) => $row->invoice_number ?: ('INV/' . $row->id))
                ->addColumn('customer_name', fn($row) => e($row->customer_name ?? '-'))
                ->addColumn('store_name', fn($row) => $row->store->name ?? '-')
                ->addColumn('created_at', fn($row) => format_date($row->created_at, '-'))
                ->addColumn('invoice_type', function ($row) use ($invoiceTypeMap) {
                    return $invoiceTypeMap[(int) $row->invoice_type] ?? '-';
                })
                // Old code:
                // ->addColumn('total_price', fn($row) => '$' . $row->total_price)
                ->addColumn('total_price', fn($row) => '$' . number_format((float) $row->total_price, 2, '.', ','))
                // Old code:
                // ->addColumn('paid_price', fn($row) => '$' . $row->total_price)
                ->addColumn('paid_price', fn($row) => '$' . number_format((float) ($row->paid_amount ?? 0), 2, '.', ','))
                ->addColumn('due', fn($row) => '$' . number_format((float) ($row->due_amount ?? 0), 2, '.', ','))
                ->addColumn('due_date', fn($row) => $row->due_date ? format_date($row->due_date, '-') : '-')
                ->addColumn('overdue', function ($row) {
                    $isOverdue = !empty($row->due_date)
                        && Carbon::parse($row->due_date)->lt(now())
                        && (float) ($row->due_amount ?? 0) > 0;

                    return $isOverdue
                        ? '<span class="badge badge-linedanger">Overdue</span>'
                        : '<span class="badge badge-linesuccess">No</span>';
                })
                ->addColumn('payment_status', function ($order) {
                    if ((int) $order->status === 3) {
                        return '<span class="badge badge-lineinfo">Estimate</span>';
                    }
                    if ((int) $order->status === 1) {
                        return '<span class="badge badge-linesuccess">Paid</span>';
                    }
                    if ((int) $order->status === 2) {
                        return '<span class="badge bg-warning text-dark">Partial</span>';
                    }
                    if ((int) $order->status === 0) {
                        return '<span class="badge badge-linedanger">Unpaid</span>';
                    }
                    return '<span class="badge badge-linedanger">Unpaid</span>';
                })
                ->addColumn('actions', function ($order) {
                    $actions = '
        <div class="edit-delete-action">
            <a class="me-2 p-2 view-customer-payments"
                href="javascript:void(0);"
                data-id="' . $order->id . '"
                title="View Customer & Payment Info"
            >
                <i data-feather="user-check" class="feather-user-check"></i>
            </a>
            <a class="me-2 p-2 print-receipt"
                href="javascript:void(0);"
                data-bs-toggle="modal"
                data-bs-target="#print-receipt"
                data-id="' . $order->id . '"
                data-customer="' . $order->customer_name . '"
                data-customerid="' . $order->customer_id . '"
                data-date="' . format_date($order->created_at, '-') . '"
                data-total="$' . number_format((float) $order->total_price, 2, '.', ',') . '"
                data-invoice="' . ($order->invoice_number ?: ('INV/' . $order->id)) . '"
                data-duedate="' . ($order->due_date ? format_date($order->due_date, '-') : '-') . '"
            >
                <i data-feather="printer" class="feather-edit"></i>
            </a>
    ';

                    if (hasPermission('update', 'order')) {
                        if ((int) $order->status === self::ESTIMATE_OPTION_ID) {
                            $actions .= '
            <a class="me-2 p-2 convert-estimate-invoice-main"
                href="javascript:void(0);"
                data-id="' . $order->id . '"
                title="Change Invoice Type"
            >
                <i data-feather="repeat" class="feather-repeat"></i>
            </a>
        ';
                        }

                        $actions .= '
            <a class="me-2 p-2 edit-order"
                href="javascript:void(0);"
                data-id="' . $order->id . '"
            >
                <i data-feather="edit" class="feather-edit"></i>
            </a>
        ';
                    }

                    $actions .= '</div>';

                    return $actions;
                })
                ->rawColumns(['actions', 'payment_status', 'overdue']);

            $datatable->orderColumn('id', "{$ordersTable}.id $1");
            $datatable->orderColumn('invoice_no', "{$ordersTable}.invoice_number $1");
            $datatable->orderColumn('customer_name', "{$ordersTable}.customer_name $1");
            $datatable->orderColumn('sort_stores.name', 'sort_stores.name $1');
            $datatable->orderColumn('created_at', "{$ordersTable}.created_at $1");
            $datatable->orderColumn('invoice_type', "{$ordersTable}.invoice_type $1");

            return $datatable->make(true);
        }

        return view('admin.invoice', [
            'customers' => $customers,
            'discount_percentage' => $discount_percentage,
            'invoice_options' => $invoice_options,
            'paymentTerms' => $paymentTerms,
            'datatableConfig' => app(DatatablePaginationService::class)->config('invoice-management'),
        ]);
    }

    public function generateInvoice(Request $request)
    {
        // Reuse the same data contract as invoice.blade.php "Generate Invoice" section.
        // $invoice_options = InvoiceOption::orderBy('id')->pluck('name', 'id');
        $invoice_options = InvoiceOption::ensureDefaults();
        $paymentTerms = PaymentTerm::where('is_active', 1)->orderBy('sort_order')->get(['id', 'label']);
        $customers = Customer::where('status', 1)->get();
        $discount_percentage = User::where('role_id', 5)->value('max_discount');
        $stores = Store::orderBy('name')->get();

        $holdOrder = null;
        $holdOrderPayload = null;
        if ($request->filled('hold_order')) {
            $holdOrder = Order::with(['orderItems', 'store'])
                ->where('id', $request->integer('hold_order'))
                ->where('type', 'offline')
                ->where('invoice_status', 'Draft')
                ->firstOrFail();
            $holdOrderPayload = $this->buildHoldSaleOrderPayload($holdOrder);
        }

        return view('admin.generate-invoice', compact(
            'customers',
            'discount_percentage',
            'invoice_options',
            'paymentTerms',
            'stores',
            'holdOrder',
            'holdOrderPayload'
        ));
    }

    /** @return array<string, mixed> */
    private function buildHoldSaleOrderPayload(Order $order): array
    {
        $categoryNames = Category::query()
            ->whereIn('id', $order->orderItems->pluck('category_id')->filter())
            ->pluck('name', 'id');

        $lines = [];
        foreach ($order->orderItems as $item) {
            $stock = Stock::query()
                ->where('store_id', $order->store_id)
                ->where('product_id', $item->product_id)
                ->first();

            $lineQty = (float) $item->qty;
            $availableQty = $stock ? (int) $stock->quantity : max(1, (int) ceil($lineQty));

            $lines[] = [
                'product_id' => (string) $item->product_id,
                'product_name' => (string) $item->product_name,
                'sku' => (string) ($item->sku ?? ''),
                'category_id' => (int) ($item->category_id ?? 0),
                'category_name' => (string) ($categoryNames[$item->category_id] ?? ''),
                'qty' => $lineQty,
                'price' => (float) $item->price,
                'available_qty' => max($availableQty, (int) ceil($lineQty)),
                'is_taxable' => (bool) ($item->is_taxable ?? true),
                'tax_rate' => (float) ($item->tax_rate ?? 0),
                'default_is_taxable' => (bool) ($stock->is_taxable ?? true),
                'default_tax_rate' => (float) ($stock->tax_rate ?? 0),
            ];
        }

        return [
            'order_id' => (int) $order->id,
            'store_id' => (int) $order->store_id,
            'customer_id' => (int) $order->customer_id,
            'invoice_type' => (int) $order->invoice_type,
            'payment_terms_days' => $order->payment_terms_days,
            'tax' => (float) $order->tax,
            'tax_exempt' => (bool) ($order->tax_exempt ?? false),
            'tax_amount' => (float) ($order->tax_amount ?? 0),
            'discount' => (float) $order->discount,
            'shipping' => (float) ($order->shipping ?? 0),
            'payment_method' => $order->payment_method,
            'credit_limit_blocked' => (bool) $order->credit_limit_blocked,
            'draft_reference' => $order->draft_reference,
            'lines' => $lines,
        ];
    }

    public function creditInvoices(Request $request, string $scope = 'all')
    {
        $allowedScopes = ['all', 'paid', 'unpaid', 'partial', 'overdue', 'estimates', 'cash-sales'];
        if (!in_array($scope, $allowedScopes, true)) {
            abort(404);
        }

        if ($request->ajax()) {
            return $this->salesInvoicesDataTable($request, $scope);
        }

        $paymentTerms = PaymentTerm::where('is_active', 1)->orderBy('sort_order')->get(['id', 'label']);
        $invoiceOptions = InvoiceOption::orderBy('id')->get(['id', 'name']);
        return view('admin.credit-invoices', [
            'scope' => $scope,
            'paymentTerms' => $paymentTerms,
            'invoiceOptions' => $invoiceOptions,
            'datatableConfig' => app(DatatablePaginationService::class)->config('credit-invoices'),
        ]);
    }

    public function convertEstimateInvoice(Request $request, Order $order)
    {
        $request->validate([
            'invoice_type' => 'required|in:' . self::CASH_SALES_OPTION_ID . ',' . self::CREDIT_SALES_OPTION_ID,
            'payment_terms_days' => 'nullable|exists:payment_terms,id',
        ]);

        if ((int) $order->status !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Only estimate invoices can be converted.',
            ], 422);
        }

        $targetInvoiceTypeId = (int) $request->input('invoice_type');
        $isCreditSale = $targetInvoiceTypeId === self::CREDIT_SALES_OPTION_ID;
        if ($isCreditSale && !$request->filled('payment_terms_days')) {
            return response()->json([
                'success' => false,
                'message' => 'Payment terms are required for Credit Sales.',
            ], 422);
        }

        $paymentTermId = $request->filled('payment_terms_days') ? (int) $request->input('payment_terms_days') : null;
        $computedDueDate = null;
        if ($isCreditSale && $paymentTermId) {
            $term = PaymentTerm::where('id', $paymentTermId)->where('is_active', 1)->first();
            if (!$term) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected payment term is invalid or inactive.',
                ], 422);
            }

            $dueDateBase = now();
            if (!empty($term->due_days)) {
                $dueDateBase = $dueDateBase->addDays((int) $term->due_days);
            }
            if (!empty($term->due_months)) {
                $dueDateBase = $dueDateBase->addMonths((int) $term->due_months);
            }
            $computedDueDate = $dueDateBase->toDateString();
        }

        $total = (float) $order->total_price;
        $paid = $isCreditSale ? 0.0 : $total;
        $due = max(0, round($total - $paid, 2));
        $status = $due <= 0 ? 1 : ($paid > 0 ? 2 : 0);

        DB::beginTransaction();
        try {
            // Estimate creation does not decrement stock, so decrement now on conversion.
            $orderItems = OrderItem::where('order_id', $order->id)->get(['product_id', 'qty']);
            foreach ($orderItems as $item) {
                $stock = Stock::where('store_id', $order->store_id)
                    ->where('product_id', $item->product_id)
                    ->first();

                if (!$stock || (float) $stock->quantity < (float) $item->qty) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock for conversion. Product ' . $item->product_id . ' is not available in required quantity.',
                    ], 422);
                }
            }

            foreach ($orderItems as $item) {
                Stock::where('store_id', $order->store_id)
                    ->where('product_id', $item->product_id)
                    ->decrement('quantity', (float) $item->qty);
            }

            $order->update([
                'invoice_type' => (int) $targetInvoiceTypeId,
                'paid_amount' => round($paid, 2),
                'due_amount' => $due,
                'status' => $status,
                'invoice_status' => (int) $targetInvoiceTypeId === self::ESTIMATE_OPTION_ID ? 'Draft' : 'Confirmed',
                'due_date' => $isCreditSale ? $computedDueDate : optional($order->created_at)->toDateString(),
                'payment_terms_days' => $isCreditSale ? $paymentTermId : null,
            ]);

            if (
                $isCreditSale
                && (float) $order->due_amount > 0
                && !empty($order->customer_id)
            ) {
                $this->createCustomerLedgerEntry(
                    customerId: (int) $order->customer_id,
                    orderId: (int) $order->id,
                    paymentId: null,
                    entryType: 'invoice',
                    description: 'Converted estimate to credit invoice ' . ($order->invoice_number ?: ('INV/' . $order->id)),
                    debit: (float) $order->due_amount,
                    credit: 0,
                    meta: [
                        'source' => 'estimate_conversion',
                        'invoice_type_id' => $targetInvoiceTypeId,
                        'due_date' => $order->due_date,
                    ]
                );
            }

            try {
                app(AccountingService::class)->postSaleOrder($order->fresh());
            } catch (\Throwable $e) {
                report($e);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to convert estimate invoice.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Estimate converted successfully.',
            'order' => $order->fresh(),
        ]);
    }

    public function invoicePaymentInfo($orderId)
    {
        $order = Order::with(['customer', 'payments'])->find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $order->id,
                'invoice_no' => $order->invoice_number ?: ('INV/' . $order->id),
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer->email ?? '-',
                'customer_phone' => $order->customer->phone ?? '-',
                'total_amount' => (float) $order->total_price,
                'paid_amount' => (float) ($order->paid_amount ?? 0),
                'due_amount' => (float) ($order->due_amount ?? 0),
                'due_date' => $order->due_date ? format_date($order->due_date, '-') : '-',
                'payments' => $order->payments->map(function ($payment) {
                    return [
                        'payment_date' => $payment->payment_date ? format_date($payment->payment_date, '-') : '-',
                        'amount' => (float) $payment->amount,
                        'payment_method' => $payment->payment_method ?: '-',
                        'reference_no' => $payment->reference_no ?: '-',
                        'notes' => $payment->notes ?: '-',
                    ];
                })->values(),
            ],
        ]);
    }

    public function sales(Request $request)
    {
        $storeId = session('selected_store_id');
        if ($request->ajax()) {
            $query = Order::with(['orderItems', 'store', 'customer'])
                ->where('store_id', $storeId);
            // ->orderBy('created_at', 'desc');  // latest first
            if ($request->has('from_date') && $request->from_date) {
                $query->whereDate('created_at', '>=', parse_date_input_to_db($request->from_date));
            }

            if ($request->has('to_date') && $request->to_date) {
                $query->whereDate('created_at', '<=', parse_date_input_to_db($request->to_date));
            }
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            }
            if ($search = $request->input('search.value')) {
                $query->where(function ($q) use ($search) {
                    $q
                        ->where('orders.id', 'like', "%{$search}%")
                        ->orWhereHas('store', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('customer', fn($q3) => $q3->where('customer_name', 'like', "%{$search}%"));
                });
            }

            $orders = $query->get();
            return datatables()
                ->eloquent($query)
                ->addColumn('order_id', fn($row) => $row->id)
                ->addColumn('date', fn($row) => format_date($row->created_at, '-'))
                ->addColumn('store_name', fn($row) => $row->store->name)
                ->addColumn('customer_name', fn($row) => $row->customer->customer_name)
                ->addColumn('quantity', fn($row) => $row->quantity)
                ->addColumn('total_price', fn($row) => $row->total_price)
                ->addColumn('due', fn($row) => '$0.00')
                ->addColumn('status', function ($order) {
                    if ($order->status == 0) {
                        return '<span class="badge badge-linedanger">Unpaid</span>';
                    } else {
                        return '<span class="badge badge-linesuccess">Paid</span>';
                    }
                })
                ->addColumn('payment_type', fn($row) => $row->payment_method)
                ->addColumn('action', function ($order) {
                    return '
                            <a class="me-2 edit-icon p-2 viewDetails" href="#" data-id="' . $order->id . '"
                                data-bs-toggle="modal" data-bs-target="#sales-details-new">
                                <i data-feather="eye" class="feather-eye"></i>
                            </a>
                    ';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('storemanager.sales');
    }

    public function viewInvoice(Request $request)  // VIEW EMPLOYEE FUNCTION
    {
        $order = Order::find($request->invoiceId);
        $store = Store::find($order->store_id);
        if ($order) {
            return response()->json([
                'success' => true,
                'data' => $order,
                'store_name' => $store->name,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found!',
            ]);
        }
    }

    public function filter(Request $request)
    {
        $query = Order::with('store');

        if ($request->store_id) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', parse_date_input_to_db($request->date_from));
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', parse_date_input_to_db($request->date_to));
        }

        $orders = $query->get();
        // Render only the table rows to return
        $view = view('admin.partials.invoice_table_rows', compact('orders'))->render();

        return response()->json([
            'success' => true,
            'html' => $view
        ]);
    }

    public function showSalesDetail(Request $request)  // FOR STOREMANAGER SIDEBAR SALES TRANSACTION
    {
        $orderId = $request->invoiceId;
        $order = Order::with('orderItems', 'customer')->find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found!'
            ]);
        }

        $tax = $order->tax;  // Replace with actual tax value
        $discount = $order->discount;  // Replace with actual discount value
        $totalPrice = $order->total_price;  // You might have a method to calculate the total price

        $stocks = Stock::where('store_id', $order->store_id)
            ->whereIn('product_id', $order->orderItems->pluck('product_id'))
            ->get();

        return response()->json([
            'success' => true,
            'data' => $order,
            'stocks' => $stocks,
            'store_name' => $order->store->name
        ]);
    }

    public function customerReportIndex(Request $request)
    {
        $storeId = session('selected_store_id');
        $payment_method = config('payment_methods');
        if ($request->ajax()) {
            $query = Order::with(['customer', 'orderItems.stock'])
                ->where('store_id', $storeId);

            // Ensure the latest orders are displayed first by default
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            } else {
                // Default to descending order if no order_type is provided
                $query->orderBy('created_at', 'desc');
            }

            if ($request->method) {
                $query->where('payment_method', $request->method);
            }

            return datatables()
                ->of($query)
                ->editColumn('customer_id', fn($row) => $row->customer_id)
                ->editColumn('customer_name', fn($row) => $row->customer_name)
                ->editColumn('amount', fn($row) => '$' . $row->total_price)
                ->editColumn('paid', fn($row) => '$' . $row->total_price)
                ->editColumn('due', fn($row) => '$0.00')
                ->editColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge badge-linedanger">Unpaid</span>';
                    } else {
                        return '<span class="badge badge-linesuccess">Paid</span>';
                    }
                })
                ->editColumn('payment_method', fn($row) => ucfirst($row->payment_method))
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('storemanager.customer-report', compact('payment_method'));
    }

    public function soldProductReportIndex(Request $request)
    {
        $storeId = session('selected_store_id');
        $payment_method = config('payment_methods');

        if ($request->ajax()) {
            $query = OrderItem::with(['order.customer', 'stock'])
                ->whereHas('order', function ($q) use ($storeId, $request) {
                    $q->where('store_id', $storeId);
                });

            // Sort dynamically based on the request 'order_type'
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);  // correct
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Return the DataTable with the queried data
            return datatables()
                ->of($query)
                ->addColumn('sold_amount', fn($row) => '$' . number_format($row->price * $row->qty, 2))
                ->addColumn('sold_qty', fn($row) => $row->qty ?? '-')
                ->addColumn('instock_qty', fn($row) => $row->stock->quantity ?? '-')
                ->addColumn('product_info', function ($row) {
                    $stock = $row->stock;
                    $imagePath = $stock && $stock->images
                        ? asset('storage/' . $stock->images)
                        : asset('admin/assets/img/avatar/default-product.png');

                    $productName = $stock->product_name ?? '-';

                    return '
                    <div class="productimgname d-flex align-items-center mb-1">
                        <div class="view-product me-2">
                            <a href="javascript:void(0);">
                                <img src="' . $imagePath . '" alt="product" width="40">
                            </a>
                        </div>
                        <a href="javascript:void(0);">' . htmlspecialchars($productName) . '</a>
                    </div>
                ';
                })
                ->rawColumns(['product_info'])
                ->make(true);
        }
    }

    public function exportCsv(Request $request)
    {
        $query = Order::query();

        // Filter only 'offline' orders
        $query->where('type', 'offline');

        if ($request->has('store') && $request->store !== 'choose') {
            $query->where('store_id', $request->store);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->date_from)->startOfDay(),
                Carbon::parse($request->date_to)->endOfDay()
            ]);
        }

        $orders = $query->with('store')->get();

        $filename = 'orders_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = [
            'Order ID',
            // 'Type',
            'Store Name',
            'Date',
            'Total Price',
        ];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    // $order->type,
                    $order->store ? $order->store->name : '',
                    $order->created_at ? $order->created_at->format('Y-m-d') : '',
                    $order->total_price,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getProductsByStore(Request $request)
    {
        $storeId = $request->store_id;
        $query = $request->input('query', '');

        if (!$storeId) {
            return response()->json(['error' => 'Store ID is required!'], 400);
        }

        try {
            // Products are a catalog, not owned by one store. Quantity is this store's on-hand amount.
            // Old query only returned stock rows for the selected store, so switching stores showed an empty list.
            // $products = DB::table('stocks')
            //     ->where('stocks.store_id', $storeId)
            //     ->when($query, function ($q) use ($query) {
            //         $q->where(function ($subQ) use ($query) {
            //             $subQ
            //                 ->where('stocks.product_name', 'LIKE', "%{$query}%")
            //                 ->orWhere('stocks.sku', 'LIKE', "%{$query}%");
            //         });
            //     })
            //     ->join('categories', 'stocks.category_id', '=', 'categories.id')
            //     ->selectRaw('
            //     stocks.id,
            //     stocks.product_id,
            //     stocks.product_name,
            //     stocks.sku,
            //     stocks.price,
            //     stocks.category_id,
            //     stocks.is_taxable,
            //     stocks.tax_rate,
            //     categories.name as category_name,
            //     SUM(stocks.quantity) as total_quantity
            // ')
            //     ->groupBy(
            //         'stocks.id',
            //         'stocks.product_name',
            //         'stocks.sku',
            //         'stocks.price',
            //         'stocks.category_id',
            //         'stocks.is_taxable',
            //         'stocks.tax_rate',
            //         'categories.name',
            //         'stocks.product_id'
            //     )
            //     ->orderBy('stocks.product_name')
            //     ->get();

            $products = DB::table('stocks')
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($subQ) use ($query) {
                        $subQ
                            ->where('stocks.product_name', 'LIKE', "%{$query}%")
                            ->orWhere('stocks.sku', 'LIKE', "%{$query}%");
                    });
                })
                ->join('categories', 'stocks.category_id', '=', 'categories.id')
                ->selectRaw('
                MIN(stocks.id) as id,
                stocks.product_id,
                MAX(stocks.product_name) as product_name,
                MAX(stocks.sku) as sku,
                MAX(stocks.price) as price,
                MAX(stocks.category_id) as category_id,
                MAX(stocks.is_taxable) as is_taxable,
                MAX(stocks.tax_rate) as tax_rate,
                MAX(categories.name) as category_name,
                COALESCE(SUM(CASE WHEN stocks.store_id = ? THEN stocks.quantity ELSE 0 END), 0) as total_quantity
            ', [$storeId])
                ->groupBy('stocks.product_id')
                ->orderBy('product_name')
                ->get();

            return response()->json($products);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch products!'], 500);
        }
    }

    public function editInvoice(Request $request)
    {
        $id = $request->id;
        $order = Order::with('orderItems')->find($id);

        if (!$order) {
            return response()->json(['success' => false]);
        }

        $items = [];

        foreach ($order->orderItems as $item) {
            $stock = Stock::where('store_id', $order->store_id)
                ->where('product_id', $item->product_id)
                ->first();

            $itemData = $item->toArray();  // Convert to array
            $itemData['available_qty'] = $stock ? $stock->quantity : 0;
            $itemData['category_name'] = $stock && $stock->category ? $stock->category->name : null;

            $items[] = $itemData;
        }

        $orderData = $order->toArray();  // Convert order
        $orderData['order_items'] = $items;  // Override with updated array

        return response()->json([
            'success' => true,
            'data' => $orderData,
        ]);
    }
}

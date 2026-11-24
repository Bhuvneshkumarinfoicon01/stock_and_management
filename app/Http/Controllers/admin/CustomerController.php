<?php

namespace App\Http\Controllers\admin;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Services\AccountingService;
use App\Services\DatatablePaginationService;
use App\Services\RecordNavigator;
use App\Services\StoreSequentialNumberService;
use App\Models\Customer;
use App\Models\CustomerLedgerEntry;
use App\Models\CustomerPayment;
use App\Models\InvoiceOption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTerm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            // 'email' => 'required|email|max:255|unique:customers,email',
            // 'phone' => 'required|unique:customers,phone',
            // 'country' => 'required|string|max:100',
            // 'city' => 'required|string|max:100',
            // 'address' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'phone' => 'nullable|string|max:20|unique:customers,phone',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:60',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        try {
            $customer = Customer::create([
                'customer_name' => $request->input('customer_name'),
                'email' => $request->input('email') ?: null,
                'phone' => $request->input('phone') ?: null,
                'country' => $request->input('country') ?: null,
                'city' => $request->input('city') ?: null,
                'address' => $request->input('address') ?: null,
                'description' => $request->input('description'),
                'credit_limit' => (float) ($request->input('credit_limit', 0) ?: 0),
            ]);
            storeHistory(
                'customer_form',
                'created',
                'customer',
                $customer->id,
                $customer->toArray(),
                []
            );
            $userId = auth()->id();
            $message = 'New Customer has been Added.';
            NotificationHelper::add($userId, $message);
            NotificationHelper::pushRealtime($userId, $message);

            return response()->json([
                'success' => true,
                'message' => 'Customer added successfully!',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add customer!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $countryList = config('countries');
        if ($request->ajax()) {
            $query = Customer::query();

            if ($request->has('country') && $request->country !== 'choose') {
                $query->where('country', $request->country);
            }
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            }

            //$customer = $query->get();
            return datatables()
                ->eloquent($query)
                ->editColumn('customer_name', fn($row) => $row->customer_name)
                ->editColumn('code', fn($row) => $row->id)
                ->editColumn('email', fn($row) => $row->email)
                ->editColumn('phone', fn($row) => $row->phone)
                ->editColumn('city', fn($row) => $row->city)
                ->editColumn('country', fn($row) => $row->country)
                ->addColumn('status', function ($customer) {
                    $checked = $customer->status == 1 ? 'checked' : '';
                    return '
                        <div class="status-toggle modal-status d-flex justify-content-between align-items-center">
                            <input type="checkbox" class="check customer-status-toggle" id="customer_status_' . $customer->id . '" data-id="' . $customer->id . '" ' . $checked . '>
                            <label for="customer_status_' . $customer->id . '" class="checktoggle"></label>
                        </div>
                    ';
                })
                ->addColumn('action', function ($customer) {
                    return '
                            <div class="edit-delete-action">
                                <a class="me-2 p-2" href="' . route('admin.customer-orders', $customer->id) . '" title="View Orders">
                                  <i data-feather="shopping-cart" class="feather-eye"></i>
                                </a>
                                <a class="me-2 p-2 viewCustomer" href="#" data-id="' . $customer->id . '" 
                                  data-bs-toggle="modal" data-bs-target="#view-customer">         
                                  <i data-feather="eye" class="feather-eye"></i>
                                </a>                 
                                <a class="me-2 p-2 editCustomer" href="#" data-id="' . $customer->id . '"
                                   data-bs-toggle="modal" data-bs-target="#edit-units">         
                                    <i data-feather="edit" class="feather-edit"></i>
                                 </a>'                   
                                /*<a class="confirm-text p-2 deleteCustomer" href="javascript:void(0);"
                                    data-id="' . $customer->id . '">        
                                    <i data-feather="trash-2" class="feather-trash-2" ></i >    
                                 </a>  */                
                            .'</div>                    
                        ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('storemanager.customer', [
            'countryList' => $countryList,
            'datatableConfig' => app(DatatablePaginationService::class)->config('all-customers'),
        ]);
    }

    public function viewCustomer(Request $request)  // VIEW EMPLOYEE FUNCTION
    {
        $customer = Customer::find($request->empId);

        if ($customer) {
            return response()->json([
                'success' => true,
                'data' => $customer,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found!',
            ]);
        }
    }

    public function editCustomer(Request $request)  // EDIT EMPLOYEE FUNCTION
    {
        $customer = Customer::find($request->empId);

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Employee not found!']);
        }
        return response()->json([
            'success' => true,
            'data' => $customer,
        ]);
    }

    public function updateCustomer(Request $request)  // UPDATE EMPLOYEE FUNCTION
    {
        // dd($request->all());
        $customer = Customer::find($request->id);
        // print_r($request->id);
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found!',
            ], 404);
        }
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:15|unique:customers,phone,' . $customer->id,
            'credit_limit' => 'nullable|numeric|min:0',
        ]);
        $oldPayload = $customer->toArray();
        $customer->customer_name = $request->input('customer_name');
        $customer->address = $request->input('address');
        $customer->country = $request->input('country');
        $customer->city = $request->input('city');
        $customer->description = $request->input('description');
        if ($request->has('credit_limit')) {
            $customer->credit_limit = (float) ($request->input('credit_limit', 0) ?: 0);
        }
        $customer->save();
        $newPayload = $customer->toArray();
        storeHistory(
            'customer_form',  // key
            'updated',  // event
            'customer',  // entity type
            $customer->id,  // entity id
            $newPayload,  // new values
            $oldPayload  // old values
        );
        $userId = auth()->id();
        $message = 'Customer (' . $customer->customer_name . ') has been Updated.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);
        return response()->json(['success' => true, 'message' => 'Customer updated successfully.']);
    }

    public function deleteCustomer(Request $request)
    {
        $customer = Customer::findOrFail($request->empId);
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found!']);
        }

        $oldPayload = $customer->toArray();

        DB::transaction(function () use ($customer) {
            // Delete customer order details first, then orders, then customer.
            $orderIds = Order::where('customer_id', $customer->id)->pluck('id');
            if ($orderIds->isNotEmpty()) {
                OrderItem::whereIn('order_id', $orderIds)->delete();
                Order::whereIn('id', $orderIds)->delete();
            }

            if (!empty($customer->profile_image)) {
                Storage::disk('public')->delete($customer->profile_image);
            }

            $customer->delete();
        });

        storeHistory(
            'customer_form',  // key
            'deleted',  // event
            'customer',  // entity type
            $request->empId,  // entity ID
            [],  // new data (empty for delete)
            $oldPayload  // old data
        );
        $userId = auth()->id();
        $message = 'Customer (' . $customer->customer_name . ') has been Deleted.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);
        return response()->json(['success' => true, 'message' => 'Employee deleted successfully!']);
    }

    public function changeStatusCustomer(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:customers,id',
            'status' => 'required|in:0,1',
        ]);

        $customer = Customer::findOrFail($request->id);
        $customer->status = (int) $request->status;
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer status updated successfully!'
        ]);
    }

    public function customerOrders(Customer $customer)
    {
        $invoiceOptions = InvoiceOption::ensureDefaults();
        PaymentTerm::ensureDefaults();
        $paymentTerms = PaymentTerm::where('is_active', 1)->orderBy('sort_order')->get(['id', 'label']);
        $recordNav = RecordNavigator::links(
            Customer::query()->orderBy('customer_name')->orderBy('id'),
            $customer->id,
            fn ($id) => route('admin.customer-orders', $id),
        );

        return view('admin.customer-orders', compact('customer', 'invoiceOptions', 'paymentTerms', 'recordNav'));
    }

    public function customerOrdersData(Request $request, Customer $customer)
    {
        $invoiceTypeMap = InvoiceOption::orderBy('id')->pluck('name', 'id')->toArray();

        $query = Order::with(['store', 'orderItems'])
            ->where('customer_id', $customer->id)
            ->where('type', 'offline');

        if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
            $query->orderBy('created_at', $request->order_type);
        } else {
            $query->latest('id');
        }

        return datatables()
            ->eloquent($query)
            ->addColumn('order_id', fn($row) => 'INV/' . $row->id)
            ->addColumn('store_name', fn($row) => $row->store->name ?? '-')
            ->addColumn('created_at', fn($row) => format_date($row->created_at, '-'))
            ->addColumn('invoice_type', function ($row) use ($invoiceTypeMap) {
                return $invoiceTypeMap[(int) $row->invoice_type] ?? '-';
            })
            ->addColumn('items', fn($row) => $row->orderItems->sum('qty'))
            ->addColumn('total_price', fn($row) => '$' . number_format((float) $row->total_price, 2, '.', ','))
            ->addColumn('paid_amount', fn($row) => '$' . number_format((float) ($row->paid_amount ?? 0), 2, '.', ','))
            ->addColumn('due_amount', fn($row) => '$' . number_format((float) ($row->due_amount ?? 0), 2, '.', ','))
            ->addColumn('due_date', fn($row) => $row->due_date ? format_date($row->due_date, '-') : '-')
            ->addColumn('payment_method', fn($row) => ucfirst($row->payment_method))
            ->addColumn('status', function ($row) {
                if ((int) $row->status === 3) {
                    return '<span class="badge badge-lineinfo">Estimate</span>';
                }

                if ((float) ($row->due_amount ?? 0) <= 0) {
                    return '<span class="badge badge-linesuccess">Paid</span>';
                }

                if ((float) ($row->paid_amount ?? 0) > 0) {
                    // Old code:
                    // return '<span class="badge badge-linewarning">Partial</span>';
                    return '<span class="badge bg-warning text-dark">Partial</span>';
                }

                if (!empty($row->due_date) && Carbon::parse($row->due_date)->isPast()) {
                    return '<span class="badge badge-linedanger">Overdue</span>';
                }

                return '<span class="badge badge-linedanger">Unpaid</span>';
            })
            ->addColumn('action', function ($row) {
                $payButton = '';
                $changeTypeButton = '';

                if ((int) ($row->status ?? 0) === 3) {
                    $changeTypeButton = '
                        <a class="me-2 p-2 change-order-invoice-type" href="javascript:void(0);" data-id="' . $row->id . '" title="Change Invoice Type">
                            <i data-feather="repeat" class="feather-repeat"></i>
                        </a>
                    ';
                }

                if ((int) ($row->invoice_type ?? 0) === 2 && (float) ($row->due_amount ?? 0) > 0) {
                    $payButton = '
                        <a class="me-2 p-2 add-order-payment" href="javascript:void(0);" data-id="' . $row->id . '" data-due="' . (float) $row->due_amount . '" title="Receive Payment">
                            <i data-feather="dollar-sign" class="feather-dollar-sign"></i>
                        </a>
                    ';
                }

                return '
                    <div class="edit-delete-action">
                        <a class="me-2 p-2 view-order-details" href="javascript:void(0);" data-id="' . $row->id . '" title="View Order Details">
                            <i data-feather="eye" class="feather-eye"></i>
                        </a>
                        ' . $changeTypeButton . '
                        ' . $payButton . '
                    </div>
                ';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function storeOrderPayment(Request $request, Customer $customer, Order $order)
    {
        if ((int) $order->customer_id !== (int) $customer->id) {
            return response()->json(['success' => false, 'message' => 'Order does not belong to selected customer.'], 422);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:100',
            'payment_date' => 'nullable|date',
            'reference_no' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $amount = (float) $request->amount;
        $dueAmount = (float) ($order->due_amount ?? 0);
        if ($amount > $dueAmount) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount cannot be greater than due amount.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $referenceNo = trim((string) ($request->reference_no ?? ''));
            if ($referenceNo === '') {
                $referenceNo = app(StoreSequentialNumberService::class)->issueNextForStore(
                    (int) ($order->store_id ?? 0),
                    'receipt_prefix',
                    'next_receipt_number',
                    'RCPT',
                    'customer_payments',
                    'reference_no'
                );
                // Old fallback when store sequence is unavailable.
                if ($referenceNo === null) {
                    $referenceNo = 'RCPT' . time();
                }
            }

            $payment = CustomerPayment::create([
                'customer_id' => $customer->id,
                'order_id' => $order->id,
                'amount' => $amount,
                'payment_method' => $request->payment_method ?: $order->payment_method,
                'payment_date' => $request->payment_date ?: now()->toDateString(),
                'reference_no' => $referenceNo,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            $newPaidAmount = round((float) ($order->paid_amount ?? 0) + $amount, 2);
            $newDueAmount = max(0, round((float) $order->total_price - $newPaidAmount, 2));

            $order->update([
                'paid_amount' => $newPaidAmount,
                'due_amount' => $newDueAmount,
                'last_payment_at' => now(),
                'status' => $newDueAmount <= 0 ? 1 : ($newPaidAmount > 0 ? 2 : 0),
            ]);

            $this->createCustomerLedgerEntry(
                customerId: (int) $customer->id,
                orderId: (int) $order->id,
                paymentId: (int) $payment->id,
                entryType: 'payment',
                description: 'Payment received for INV/' . $order->id,
                debit: 0,
                credit: $amount,
                meta: [
                    'payment_method' => $payment->payment_method,
                    'reference_no' => $payment->reference_no,
                ]
            );

            try {
                app(AccountingService::class)->postCustomerPayment($payment);
            } catch (\Throwable $e) {
                report($e);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully.',
                'order' => $order->fresh(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function customerLedgerData(Customer $customer)
    {
        $entries = CustomerLedgerEntry::where('customer_id', $customer->id)
            ->orderBy('entry_date')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'entries' => $entries,
            'summary' => [
                'outstanding' => (float) Order::where('customer_id', $customer->id)->sum('due_amount'),
                'invoiced_total' => (float) Order::where('customer_id', $customer->id)->sum('total_price'),
                'paid_total' => (float) Order::where('customer_id', $customer->id)->sum('paid_amount'),
            ],
        ]);
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
        // For order-linked entries keep order-wise running balance.
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
}

<?php

namespace App\Http\Controllers\admin;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Store;
use App\Models\StoreEmployee;
use App\Services\DatatablePaginationService;
use App\Services\SaleInvoiceNumberService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        
        $roles = Role::all();
        $userCount = StoreEmployee::where('store_id', session('selected_store_id'))->count();
        $permissions = Permission::all()->groupBy('module');
        if ($request->ajax()) {
            $query = StoreEmployee::where('store_id', session('selected_store_id'));

            if ($request->has('status') && in_array($request->status, ['0', '1'])) {
                $query->where('status', $request->status);
            }

            // Apply order sorting if present
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            }
            return datatables()
                ->of($query)
                ->addColumn('staff_info', function ($staff) {
                    $imagePath = $staff->profile_image
                        ? asset('storage/' . $staff->profile_image)
                        : asset('admin/assets/img/avatar/default-avatar.png');

                    $name = htmlspecialchars($staff->name);
                    $role = htmlspecialchars($staff->role->name ?? '-');

                    return '
                        <div class="userimgname">
                            <a href="javascript:void(0);" class="product-img">
                                <img src="' . $imagePath . '" alt="profile" />
                            </a>
                            <div>
                                <a href="javascript:void(0);">' . $name . '</a>
                                <span class="emp-team">' . $role . '</span>
                            </div>
                        </div>
                    ';
                })
                ->addColumn('emp_code', fn($row) => $row->id)
                ->addColumn('email', fn($row) => $row->email)
                ->addColumn('phone', fn($row) => $row->phone)
                ->addColumn('status_badge', function ($staff) {
                    $statusText = $staff->status == 1 ? 'Active' : 'Inactive';
                    $badgeClass = $staff->status == 1 ? 'badge-linesuccess' : 'badge-linedanger';
                    return '<span class="badge ' . $badgeClass . '">' . $statusText . '</span>';
                })
                ->addColumn('actions', function ($staff) {
                    $checked = $staff->status == 1 ? 'checked' : '';
                    $actions = '<div class="edit-delete-action">';

                    $actions .= '
            <a class="me-2 p-2 viewEmployee" href="#" data-id="' . $staff->id . '" data-bs-toggle="modal" data-bs-target="#employee-view">
                <i data-feather="eye" class="feather-eye"></i>
            </a>';

                    if (hasPermission('update', 'user')) {
                        $actions .= '
            <a class="me-2 p-2 editEmployee" href="javascript:void(0);" data-id="' . $staff->id . '" data-bs-toggle="modal" data-bs-target="#employee-edit">
                <i data-feather="edit" class="feather-edit"></i>
            </a>';
                    }
                    //         if (hasPermission('create', 'permission')) {
                    //             $actions .= '
                    // <a class="me-2 p-2" href="javascript:void(0);" data-id="' . $staff->id . '" data-bs-toggle="modal" data-bs-target="#permissionModal">
                    //     <i data-feather="shield" class="feather-shield"></i>
                    // </a>';
                    // }

                    if (hasPermission('delete', 'user')) {
                        $actions .= '
            <a class="confirm-text p-2 deleteStaff" href="javascript:void(0);" data-id="' . $staff->id . '">
                <i data-feather="trash-2" class="feather-trash-2"></i>
            </a>';
                    }

                    if (hasPermission('update', 'user')) {
                        $actions .= '
            <label class="switch">
                <input type="checkbox" class="status-toggle" data-id="' . $staff->id . '" ' . $checked . '>
                <span class="slider round"></span>
            </label>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['staff_info', 'status_badge', 'actions'])
                ->make(true);
        }
        return view('storemanager.employee-list', compact('roles', 'userCount', 'permissions'));
    }

    public function allStores(Request $request)
    {
        $sortOrder = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $query = Store::query();
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        $query->orderBy('created_at', $sortOrder);
        $stores = $query->paginate(10);
        return view('admin.store-list', compact('stores', 'sortOrder'));
    }

    // public function superadmminstorelist_test(Request $request){
   
    //     $data = Store::paginate(50);
    //     return view('testcode.Superadmin.store-list', compact('data'));
    // }

    public function superAdminStoreList(Request $request)
    {
      
        // dd([
        //     'all' => $request->all(),
        //     'query' => $request->query(),
        //     'post' => $request->post(),
        //     'method' => $request->method(),
        //     'is_ajax' => $request->ajax(),
        //     'wants_json' => $request->wantsJson(),
        // ]);

        // dd($request->all());
        // dd($request->query());
        // if (! $request->ajax()) {
        //     return response()->json(['error' => 'Not an AJAX request']);
        // }

        // if ($request->ajax()) {
        // Some live/proxy environments may not preserve X-Requested-With.
        // DataTables always sends "draw", so allow that as a server-side signal.
        if ($request->ajax() || $request->has('draw')) {
        //if ($request->has('draw')) {
            $query = Store::query();

            if ($request->has('status') && $request->status !== 'choose') {
                $query->where('status', $request->status);
            }
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            }
            //$stores = $query->get();
            $stores = $query;
            return datatables()
                ->of($query)
                ->filter(function ($query) use ($request) {
                    $search = trim((string) $request->input('search.value', ''));
                    if ($search === '') {
                        return;
                    }

                    $searchLower = strtolower($search);
                    $query->where(function ($q) use ($search, $searchLower) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%")
                            ->orWhere('state', 'like', "%{$search}%")
                            ->orWhere('zipcode', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");

                        // Also allow searching by status text.
                        if (in_array($searchLower, ['active', 'inactive'], true)) {
                            $q->orWhere('status', $searchLower === 'active' ? 1 : 0);
                        }
                    });
                })
                //->setDraw($request->input('draw')) 
                ->addColumn('store_name', fn($row) => $row->name)
                ->addColumn('address', fn($row) => $row->address)
                ->addColumn('city', fn($row) => $row->city)
                ->addColumn('state', fn($row) => $row->state)
                ->addColumn('zipcode', fn($row) => $row->zipcode)
                ->addColumn('phone', fn($row) => $row->phone)
                ->addColumn('email', fn($row) => $row->email)
                ->addColumn('invoice_prefix', fn($row) => $row->invoice_prefix)
                ->addColumn('sale_invoice_start_number', fn($row) => $row->next_sale_invoice_number)
                ->addColumn('status_badge', function ($store) {
                    if ($store->status == 1) {
                        return '<span class="badge badge-linesuccess">Active</span>';
                    } else {
                        return '<span class="badge badge-linedanger">Inactive</span>';
                    }
                })
                ->addColumn('actions', function ($store) {
                    $actions = '<div class="edit-delete-action">';

                    // Edit store
                    if (hasPermission('edit', 'store')) {
                        $actions .= '
            <a
                class="me-2 p-2 edit-store-btn"
                href="javascript:void(0);"
                data-id="' . $store->id . '"
                data-bs-toggle="modal"
                data-bs-target="#edit-stores"
            >
                <i data-feather="edit" class="feather-edit"></i>
            </a>
        ';
                    }

                    // Delete store
                    if (hasPermission('delete', 'store')) {
                        $actions .= '
            <a
                href="javascript:void(0);"
                class="confirm-text p-2 delete-store-btn"
                data-id="' . $store->id . '"
            >
                <i data-feather="trash-2" class="feather-trash-2"></i>
            </a>
        ';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }

        // Non-AJAX request: only show the page shell.
        // DataTables will fetch paginated rows via AJAX (server-side pagination).
        //
        // NOTE: The direct `$stores = $query->get()` implementation was added earlier to
        // avoid the loader, but it loads all rows at once. Keeping it commented to
        // preserve old code while preventing million-row fetches.
        /*
        $sortOrder = in_array($request->input('order_type'), ['asc', 'desc']) ? $request->input('order_type') : 'desc';
        $query = Store::query();

        if ($request->has('status') && $request->status !== 'choose' && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $query->orderBy('created_at', $sortOrder);
        $stores = $query->get();

        return view('SuperAdmin.store-list', compact('stores'));
        */
        return view('SuperAdmin.store-list', [
            'datatableConfig' => app(DatatablePaginationService::class)->config('store-list'),
        ]);
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            // State and zip code are optional.
            // 'state' => 'required|string|max:100',
            // 'zipcode' => 'required|regex:/^\d{3,10}$/',
            'state' => 'nullable|string|max:100',
            'zipcode' => 'nullable|regex:/^\d{3,10}$/',
            // 'phone' => 'required|string|max:20',
            'phone' => 'required|regex:/^\+?[0-9]{7,20}$/',
            'email' => 'required|email|max:255|unique:stores',
            'status' => 'required|boolean',
            'invoice_prefix' => 'nullable|string|max:32',
            'sale_invoice_start_number' => 'nullable|integer|min:1',
            // Point 11 - document-specific sequences per store.
            'purchase_invoice_prefix' => 'nullable|string|max:32',
            'purchase_invoice_start_number' => 'nullable|integer|min:1',
            'purchase_order_prefix' => 'nullable|string|max:32',
            'purchase_order_start_number' => 'nullable|integer|min:1',
            'purchase_return_prefix' => 'nullable|string|max:32',
            'purchase_return_start_number' => 'nullable|integer|min:1',
            'sale_return_prefix' => 'nullable|string|max:32',
            'sale_return_start_number' => 'nullable|integer|min:1',
            'grn_prefix' => 'nullable|string|max:32',
            'grn_start_number' => 'nullable|integer|min:1',
            // Not fully wired to DB generators yet (kept for future parity with point 11).
            'credit_note_prefix' => 'nullable|string|max:32',
            'credit_note_start_number' => 'nullable|integer|min:1',
            'debit_note_prefix' => 'nullable|string|max:32',
            'debit_note_start_number' => 'nullable|integer|min:1',
            // Applied to payment reference_no when missing.
            'receipt_prefix' => 'nullable|string|max:32',
            'receipt_start_number' => 'nullable|integer|min:1',
            // Point 19 — batch purchase document sequence per store.
            'batch_purchase_prefix' => 'nullable|string|max:32',
            'batch_purchase_start_number' => 'nullable|integer|min:1',
        ]);

        $store = Store::create([
            'name' => $request->name,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zipcode' => $request->zipcode,
            'phone' => $request->phone,
            'email' => $request->email,
            'status' => $request->status ? 1 : 0,
            'invoice_prefix' => $this->normalizeInvoicePrefix($request->input('invoice_prefix')),
            'current_sale_invoice_sequence' => SaleInvoiceNumberService::NUMERIC_SEQUENCE_FORMAT,
            // Internal counter — not shown on create form; set from "Sale Invoice Number Sequence" start value.
            'next_sale_invoice_number' => (int) ($request->input('sale_invoice_start_number') ?: 1),

            'purchase_invoice_prefix' => $this->normalizeOptionalPrefix($request->input('purchase_invoice_prefix'), 'PINV'),
            'next_purchase_invoice_number' => (int) ($request->input('purchase_invoice_start_number') ?: 1),
            'purchase_order_prefix' => $this->normalizeOptionalPrefix($request->input('purchase_order_prefix'), 'PO'),
            'next_purchase_order_number' => (int) ($request->input('purchase_order_start_number') ?: 1),
            'purchase_return_prefix' => $this->normalizeOptionalPrefix($request->input('purchase_return_prefix'), 'PR'),
            'next_purchase_return_number' => (int) ($request->input('purchase_return_start_number') ?: 1),
            'sale_return_prefix' => $this->normalizeOptionalPrefix($request->input('sale_return_prefix'), 'SR'),
            'next_sale_return_number' => (int) ($request->input('sale_return_start_number') ?: 1),
            'grn_prefix' => $this->normalizeOptionalPrefix($request->input('grn_prefix'), 'GRN'),
            'next_grn_number' => (int) ($request->input('grn_start_number') ?: 1),

            'credit_note_prefix' => $this->normalizeOptionalPrefix($request->input('credit_note_prefix'), 'CN'),
            'next_credit_note_number' => (int) ($request->input('credit_note_start_number') ?: 1),
            'debit_note_prefix' => $this->normalizeOptionalPrefix($request->input('debit_note_prefix'), 'DN'),
            'next_debit_note_number' => (int) ($request->input('debit_note_start_number') ?: 1),

            'receipt_prefix' => $this->normalizeOptionalPrefix($request->input('receipt_prefix'), 'RCPT'),
            'next_receipt_number' => (int) ($request->input('receipt_start_number') ?: 1),

            'batch_purchase_prefix' => $this->normalizeOptionalPrefix($request->input('batch_purchase_prefix'), 'BP'),
            'next_batch_purchase_number' => (int) ($request->input('batch_purchase_start_number') ?: 1),
        ]);
        storeHistory(
            'store_form',  // key
            'created',  // event
            'store',  // entity type
            $store->id,  // entity ID
            $store->toArray(),  // new data
            []  // old data
        );

        // Notification
        $userId = auth()->id();
        $message = 'Store (' . $request->name . ') has been successfully created.';
        $userName = auth()->user()->name;
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);
        return response()->json([
            'status' => 'success',
            'message' => 'Store created successfully!',
            'store' => $store
        ]);
    }

    public function editStore($id)
    {
        $store = Store::find($id);
        if ($store) {
            return response()->json([
                'success' => true,
                'data' => $store,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Store not found!',
            ]);
        }
    }

    public function updateStore(Request $request, $id)
    {
        $store = Store::find($id);
        if (!$store) {
            return response()->json(['success' => false, 'message' => 'Store not found.']);
        }
        $oldData = $store->toArray();  // Store old data for history

        $updateData = [
            'name' => $request->storeName,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zipcode' => $request->zipcode,
            'phone' => $request->phone,
            'email' => $request->email,
            'status' => $request->status,
        ];

        if ($request->filled('invoice_prefix')) {
            $updateData['invoice_prefix'] = $this->normalizeInvoicePrefix($request->input('invoice_prefix'));
            $updateData['current_sale_invoice_sequence'] = SaleInvoiceNumberService::NUMERIC_SEQUENCE_FORMAT;
        }

        // UI "Sale Invoice Number Sequence" maps to internal next_sale_invoice_number.
        if ($request->filled('sale_invoice_start_number')) {
            $updateData['next_sale_invoice_number'] = (int) $request->input('sale_invoice_start_number');
        }
        // Old code: explicit next_sale_invoice_number field (hidden from UI, kept for internal/API use).
        // if ($request->filled('next_sale_invoice_number')) {
        //     $updateData['next_sale_invoice_number'] = (int) $request->input('next_sale_invoice_number');
        // }

        // Point 11 - store-specific document sequences (if provided).
        if ($request->filled('purchase_invoice_prefix')) {
            $updateData['purchase_invoice_prefix'] = $this->normalizeOptionalPrefix(
                $request->input('purchase_invoice_prefix'),
                'PINV'
            );
        }
        if ($request->filled('purchase_invoice_start_number')) {
            $updateData['next_purchase_invoice_number'] = (int) $request->input('purchase_invoice_start_number');
        }

        if ($request->filled('purchase_order_prefix')) {
            $updateData['purchase_order_prefix'] = $this->normalizeOptionalPrefix(
                $request->input('purchase_order_prefix'),
                'PO'
            );
        }
        if ($request->filled('purchase_order_start_number')) {
            $updateData['next_purchase_order_number'] = (int) $request->input('purchase_order_start_number');
        }

        if ($request->filled('purchase_return_prefix')) {
            $updateData['purchase_return_prefix'] = $this->normalizeOptionalPrefix(
                $request->input('purchase_return_prefix'),
                'PR'
            );
        }
        if ($request->filled('purchase_return_start_number')) {
            $updateData['next_purchase_return_number'] = (int) $request->input('purchase_return_start_number');
        }

        if ($request->filled('sale_return_prefix')) {
            $updateData['sale_return_prefix'] = $this->normalizeOptionalPrefix(
                $request->input('sale_return_prefix'),
                'SR'
            );
        }
        if ($request->filled('sale_return_start_number')) {
            $updateData['next_sale_return_number'] = (int) $request->input('sale_return_start_number');
        }

        if ($request->filled('grn_prefix')) {
            $updateData['grn_prefix'] = $this->normalizeOptionalPrefix($request->input('grn_prefix'), 'GRN');
        }
        if ($request->filled('grn_start_number')) {
            $updateData['next_grn_number'] = (int) $request->input('grn_start_number');
        }

        if ($request->filled('credit_note_prefix')) {
            $updateData['credit_note_prefix'] = $this->normalizeOptionalPrefix(
                $request->input('credit_note_prefix'),
                'CN'
            );
        }
        if ($request->filled('credit_note_start_number')) {
            $updateData['next_credit_note_number'] = (int) $request->input('credit_note_start_number');
        }

        if ($request->filled('debit_note_prefix')) {
            $updateData['debit_note_prefix'] = $this->normalizeOptionalPrefix(
                $request->input('debit_note_prefix'),
                'DN'
            );
        }
        if ($request->filled('debit_note_start_number')) {
            $updateData['next_debit_note_number'] = (int) $request->input('debit_note_start_number');
        }

        if ($request->filled('receipt_prefix')) {
            $updateData['receipt_prefix'] = $this->normalizeOptionalPrefix(
                $request->input('receipt_prefix'),
                'RCPT'
            );
        }
        if ($request->filled('receipt_start_number')) {
            $updateData['next_receipt_number'] = (int) $request->input('receipt_start_number');
        }

        // Point 19 — batch purchase document sequence.
        if ($request->filled('batch_purchase_prefix')) {
            $updateData['batch_purchase_prefix'] = $this->normalizeOptionalPrefix(
                $request->input('batch_purchase_prefix'),
                'BP'
            );
        }
        if ($request->filled('batch_purchase_start_number')) {
            $updateData['next_batch_purchase_number'] = (int) $request->input('batch_purchase_start_number');
        }

        $store->update($updateData);

        storeHistory(
            'store_form',
            'updated',
            'store',
            $store->id,
            $store->toArray(),  // new values
            $oldData  // old values
        );

        $userId = auth()->id();
        $message = 'Store (' . $request->storeName . ') has been successfully updated.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);

        return response()->json(['success' => true, 'message' => 'Store updated successfully.']);
    }

    public function destroy($id)
    {
        $store = Store::find($id);
        if (!$store) {
            return response()->json(['success' => false, 'message' => 'Store not found.']);
        }
        $oldData = $store->toArray();
        storeHistory(
            'store_form',
            'deleted',
            'store',
            $id,
            null,  // No new data on deletion
            $oldData  // Only old data
        );
        $store->delete();
        $userId = auth()->id();
        $message = 'Store (' . $store->name . ') has been successfully deleted.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);
        return response()->json(['success' => true, 'message' => 'Store deleted successfully.']);
    }

    public function selectStore(Request $request)  // NEWLY MADE FOR SELECT STORE IN THE HEADER
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
        ]);
        $storeId = $request->store_id;
        session(['selected_store_id' => $storeId]);
        $user = Auth::user();
        $user->selected_store_id = $storeId;
        $user->save();
        session()->forget('cart');
        return response()->json(['message' => 'Store selected successfully.']);
    }

    public function addEmployee(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:store_employees,email',
                'username' => 'required|string|max:255|unique:store_employees,username',
                'phone' => 'required|regex:/^\+?[0-9]{10,15}$/|unique:store_employees,phone',
                'emp_code' => 'required|string|max:50|unique:store_employees,emp_code',
                'joining_date' => 'required|date|before_or_equal:today',
            ], [
                'phone.regex' => 'Phone number must be 10-15 digits only'
            ]);
            $user = new StoreEmployee();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->username = $request->username;
            $user->email = $request->email;
            // $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            $user->emp_code = $request->emp_code;
            $user->dob = $request->dob;
            $user->gender = $request->gender;
            $user->nationality = $request->nationality;
            $user->role_id = $request->role_id;
            $user->store_id = session('selected_store_id');
            $user->joining_date = $request->joining_date;
            $user->emergency_no1 = $request->emergency_no1;
            $user->emergency_no2 = $request->emergency_no2;
            $user->address = $request->address;
            $user->country = $request->country;
            $user->state = $request->state;
            $user->city = $request->city;
            $user->zipcode = $request->zipcode;
            $user->name = trim($request->first_name . ' ' . $request->last_name);
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('uploads', 'public');
                $user->profile_image = $path;
            } else {
                $user->profile_image = null;
            }
            $user->save();
            storeHistory(
                'store_employee_form',
                'created',
                'store_employees',
                $user->id,
                $user->toArray(),
                null
            );
            // Notification
            $employeeName = trim($request->first_name . ' ' . $request->last_name);
            $userId = auth()->id();
            $message = 'New Employee (' . $employeeName . ') has been successfully added.';
            NotificationHelper::add($userId, $message);
            NotificationHelper::pushRealtime($userId, $message);
            return response()->json([
                'success' => true,
                'message' => 'Employee added successfully!',
            ]);
        } catch (Exception $e) {
            \Log::error('Error adding employee: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding employee!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function viewEmployee(Request $request)  // VIEW EMPLOYEE FUNCTION
    {
        $staff = StoreEmployee::find($request->empId);
        if ($staff) {
            return response()->json([
                'success' => true,
                'data' => $staff,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found!',
            ]);
        }
    }

    public function editEmployee(Request $request)  // EDIT EMPLOYEE FUNCTION
    {
        $staff = StoreEmployee::find($request->empId);

        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Employee not found!']);
        }

        $roles = Role::all();

        return response()->json([
            'success' => true,
            'data' => $staff,
            'roles' => $roles,
        ]);
    }

    public function updateEmployee(Request $request)  // UPDATE EMPLOYEE FUNCTION
    {
        $staff = StoreEmployee::findOrFail($request->id);
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:store_employees,email,' . $staff->id,
            'username' => 'required|string|unique:store_employees,username,' . $staff->id,
            'phone' => 'required|regex:/^\+?[0-9]{10,15}$/|unique:store_employees,phone,' . $staff->id,
            'emp_code' => 'required|string|max:50|unique:store_employees,emp_code, ' . $staff->id,
            'joining_date' => 'required|date|before_or_equal:today',
        ], [
            'phone.regex' => 'Phone number must be 10-15 digits only'
        ]);
        $oldData = $staff->toArray();  // before update
        $staff->last_name = $request->input('last_name');
        $staff->first_name = $request->input('first_name');
        $staff->email = $request->input('email');
        // if (!empty($request->password)) {
        //     $staff->password = Hash::make($request->password);
        // }
        $staff->username = $request->input('username');
        $staff->phone = $request->input('phone');
        $staff->emp_code = $request->input('emp_code');
        $staff->role_id = $request->input('role_id');
        $staff->gender = $request->input('gender');
        $staff->nationality = $request->input('nationality');
        $staff->dob = $request->input('dob');
        $staff->address = $request->input('address');
        $staff->country = $request->input('country');
        $staff->city = $request->input('city');
        $staff->state = $request->input('state');
        $staff->zipcode = $request->input('zipcode');
        $staff->joining_date = $request->input('joining_date');
        $staff->emergency_no1 = $request->input('emergency_no1');
        $staff->emergency_no2 = $request->input('emergency_no2');
        $staff->name = trim($request->first_name . ' ' . $request->last_name);
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads', 'public');
            $staff->profile_image = $path;
        } elseif ($request->input('existing_profile_image')) {
            $staff->profile_image = $staff->profile_image;
        } else {
            $staff->profile_image = null;
        }
        $staff->save();
        storeHistory(
            'store_employee_form',
            'updated',
            'store_employees',
            $staff->id,
            $staff->toArray(),  // new data
            $oldData  // old data
        );
        $userId = auth()->id();
        $message = 'Employee (' . $staff->name . ') has been successfully updated.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);
        return response()->json(['success' => true, 'message' => 'Employee updated successfully.']);
    }

    public function deleteEmployee(Request $request)
    {
        
        $staff = StoreEmployee::findOrFail($request->empId);
        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Employee not found!']);
        }
        $oldPayload = $staff->toArray();
        $employeeName = $staff->name ?? ($staff->first_name . ' ' . $staff->last_name);
        if ($staff->profile_image) {
            Storage::disk('public')->delete($staff->profile_image);
        }
        $staff->delete();
        storeHistory(
            'store_employee_form',  // key
            'deleted',  // event
            'store_employees',  // entity type
            $request->empId,  // id
            null,  // no new data
            $oldPayload  // old data
        );
        $userId = auth()->id();
        $message = "Employee ({$employeeName}) has been successfully deleted.";
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);
        return response()->json(['success' => true, 'message' => 'Employee deleted successfully!']);
    }

    // ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ////////////////////////Functions for admin/superadmins to add store employee with select store field////////////////////////////
    public function indexStoreEmployee(Request $request)
    {
        $roles = Role::all();
        $countryList = config('countries');
        $authUser = auth()->user();
        $permissions = Permission::all()->groupBy('module');
        $userCount = StoreEmployee::where('store_id', session('selected_store_id'))->count();
        if ($request->ajax()) {
            $query = StoreEmployee::where('store_id', session('selected_store_id'));

            if ($request->has('status') && $request->status !== 'choose') {
                $query->where('status', $request->status);
            }
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            }
            return datatables()
                ->of($query)
                ->addColumn('user_profile', function ($query) {
                    $image = $query->profile_image
                        ? asset('storage/' . $query->profile_image)
                        : asset('admin/assets/img/avatar/default-avatar.png');

                    return '
                    <div class="userimgname">
                        <a href="javascript:void(0);" class="product-img">
                            <img src="' . $image . '" alt="product" />
                        </a>
                        <div>
                            <a href="javascript:void(0);">' . e($query->name) . '</a>
                            <span class="emp-team">' . e($query->role->name ?? '-') . '</span>
                        </div>
                    </div>
                ';
                })
                ->addColumn('user_id', fn($row) => $row->id)
                ->addColumn('email', fn($row) => $row->email)
                ->addColumn('phone', fn($row) => $row->phone)
                ->addColumn('status_badge', function ($query) {
                    $status = $query->status == 1 ? 'Active' : 'Inactive';
                    $class = $query->status == 1 ? 'badge-linesuccess' : 'badge-linedanger';

                    return '<span class="badge ' . $class . '">' . $status . '</span>';
                })
                ->addColumn('actions', function ($user) use ($authUser) {
                    $checked = $user->status == 1 ? 'checked' : '';

                    $actions = '<div class="edit-delete-action">';
                    $actions .= '<a href="javascript:void(0);" data-id="' . $user->id . '" class="me-2 p-2 viewEmployee" data-bs-toggle="modal" data-bs-target="#employee-view">
                        <i data-feather="eye" class="feather-eye"></i>
                    </a>';
                    $actions .= ' <a class="me-2 p-2 editEmployee" href="javascript:void(0);" data-id="' . $user->id . '" data-bs-toggle="modal" data-bs-target="#employee-edit">
                        <i data-feather="edit" class="feather-edit"></i>
                    </a>';
                    //         if (hasPermission('create', 'permission')) {
                    //             $actions .= '
                    // <a class="me-2 p-2" href="javascript:void(0);" data-id="' . $user->id . '" data-bs-toggle="modal" data-bs-target="#permissionModal">
                    //     <i data-feather="shield" class="feather-shield"></i>
                    // </a>';
                    //         }
                    $actions .= '<label class="switch">
                        <input type="checkbox" class="status-toggle" data-id="' . $user->id . '" ' . $checked . '>
                        <span class="slider round"></span>
                    </label>';

                    if ($authUser->role_id == 1) {
                        $actions .= '
                    <a class="confirm-text p-2 deleteStoreEmployee" href="javascript:void(0);" data-id="' . $user->id . '">
                        <i data-feather="trash-2" class="feather-trash-2"></i>
                    </a>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['user_profile', 'status_badge', 'actions'])
                ->make(true);
        }

        return view('admin.store-employee', compact('userCount', 'roles', 'countryList', 'permissions'));
    }

    public function addEmployeeAdmin(Request $request)
    {
       
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:store_employees,email',
                'username' => 'required|string|max:255|unique:store_employees,username',
                'store' => 'required',
                'phone' => 'required|regex:/^\+?[0-9]{10,15}$/|unique:store_employees,phone',
                'emp_code' => 'required|string|max:50|unique:store_employees,emp_code',
                'joining_date' => 'required|date|before_or_equal:today',
            ], [
                'store.required' => 'Select store you want to assign the employee',
                'phone.regex' => 'Phone number must be 10-15 digits only'
            ]);
            $user = new StoreEmployee();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->username = $request->username;
            $user->email = $request->email;
            // $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            $user->emp_code = $request->emp_code;
            $user->dob = $request->dob;
            $user->gender = $request->gender;
            $user->nationality = $request->nationality;
            $user->role_id = $request->role_id;
            $user->store_id = $request->store;
            $user->joining_date = $request->joining_date;
            $user->emergency_no1 = $request->emergency_no1;
            $user->emergency_no2 = $request->emergency_no2;
            $user->address = $request->address;
            $user->country = $request->country;
            $user->state = $request->state;
            $user->city = $request->city;
            $user->zipcode = $request->zipcode;
            $user->name = trim($request->first_name . ' ' . $request->last_name);
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('uploads', 'public');
                $user->profile_image = $path;
            } else {
                $user->profile_image = null;
            }
            $user->save();

            storeHistory(
                'employee_form',
                'created',
                'store_employees',
                $user->id,
                $user->toArray(),  // new data
                null  // no old data
            );
            // Notification
            $employeeName = trim($request->first_name . ' ' . $request->last_name);
            $store = Store::find($request->store);
            $storeName = $store ? $store->name : 'Unknown';
            $userId = auth()->id();
            $message = 'New Employee (' . $employeeName . ') has been successfully added to store (' . $storeName . ').';
            NotificationHelper::add($userId, $message);
            NotificationHelper::pushRealtime($userId, $message);
            return response()->json([
                'success' => true,
                'message' => 'Employee added successfully!',
            ]);
        } catch (Exception $e) {
            \Log::error('Error adding employee: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding employee!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function viewEmployeeAdmin(Request $request)  // VIEW EMPLOYEE FUNCTION
    {
        $staff = StoreEmployee::find($request->empId);
        if ($staff) {
            return response()->json([
                'success' => true,
                'data' => $staff,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found!',
            ]);
        }
    }

    public function editEmployeeAdmin(Request $request)  // EDIT EMPLOYEE FUNCTION
    {
        $staff = StoreEmployee::find($request->empId);

        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Employee not found!']);
        }
        $stores = Store::all();
        $roles = Role::all();

        return response()->json([
            'success' => true,
            'data' => $staff,
            'roles' => $roles,
            'stores' => $stores,
        ]);
    }

    public function updateEmployeeAdmin(Request $request)  // UPDATE EMPLOYEE FUNCTION
    {
        
        $staff = StoreEmployee::findOrFail($request->id);
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:store_employees,email,' . $staff->id,
            'username' => 'required|string|unique:store_employees,username,' . $staff->id,
            'phone' => 'required|regex:/^\+?[0-9]{10,15}$/|unique:store_employees,phone,' . $staff->id,
            'emp_code' => 'required|string|max:50|unique:store_employees,emp_code, ' . $staff->id,
            'joining_date' => 'required|date|before_or_equal:today',
            'store' => 'required',
        ], [
            'phone.regex' => 'Phone number must be 10-15 digits only'
        ]);
        $oldData = $staff->toArray();
        $staff->last_name = $request->input('last_name');
        $staff->first_name = $request->input('first_name');
        $staff->email = $request->input('email');
        // if (!empty($request->password)) {
        //     $staff->password = Hash::make($request->password);
        // }
        $staff->phone = $request->input('phone');
        $staff->emp_code = $request->input('emp_code');
        $staff->role_id = $request->input('role_id');
        $staff->store_id = $request->input('store');
        $staff->gender = $request->input('gender');
        $staff->nationality = $request->input('nationality');
        $staff->dob = $request->input('dob');
        $staff->address = $request->input('address');
        $staff->country = $request->input('country');
        $staff->city = $request->input('city');
        $staff->state = $request->input('state');
        $staff->zipcode = $request->input('zipcode');
        $staff->joining_date = $request->input('joining_date');
        $staff->emergency_no1 = $request->input('emergency_no1');
        $staff->emergency_no2 = $request->input('emergency_no2');
        $staff->name = trim($request->first_name . ' ' . $request->last_name);
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads', 'public');
            $staff->profile_image = $path;
        } elseif ($request->input('existing_profile_image')) {
            $staff->profile_image = $staff->profile_image;
        } else {
            $staff->profile_image = null;
        }
        $staff->save();
        storeHistory(
            'employee_form',
            'updated',
            'store_employees',
            $staff->id,
            $staff->toArray(),  // new data
            $oldData  // old data
        );
        $userId = auth()->id();
        $message = 'Employee (' . trim($request->first_name . ' ' . $request->last_name) . ') has been successfully updated.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);
        return response()->json(['success' => true, 'message' => 'Employee updated successfully.']);
    }

    public function deleteEmployeeAdmin(Request $request)
    {
        
        $staff = StoreEmployee::findOrFail($request->empId);
        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Employee not found!']);
        }
        $oldData = $staff->toArray();
        if ($staff->profile_image) {
            Storage::disk('public')->delete($staff->profile_image);
        }
        $staff->delete();
        storeHistory(
            'employee_form',
            'deleted',
            'store_employees',
            $staff->id,
            null,  // no new data after deletion
            $oldData  // old data before deletion
        );
        $userId = auth()->id();
        $message = 'Employee has been successfully deleted.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);
        return response()->json(['success' => true, 'message' => 'Employee deleted successfully!']);
    }

    public function changeStatusStoreEmployee(Request $request)
    {
        $user = StoreEmployee::findOrFail($request->id);
        $user->status = $request->status;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Employee status updated successfully!'
        ]);
    }

    private function normalizeInvoicePrefix(?string $prefix): string
    {
        $prefix = trim((string) $prefix);

        return $prefix !== '' ? $prefix : 'INV';
    }

    private function normalizeOptionalPrefix(?string $prefix, string $default): string
    {
        $prefix = trim((string) $prefix);

        return $prefix !== '' ? $prefix : $default;
    }
}

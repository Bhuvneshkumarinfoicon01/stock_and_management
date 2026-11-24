<?php

namespace App\Http\Controllers\admin;

use App\Helpers\NotificationHelper;
use App\Services\DatatablePaginationService;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\GlAccount;
use App\Models\ProductStoreMarkup;
use App\Services\ProductUnitCostService;
use App\Services\RecordNavigator;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StockController extends Controller
{

    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {

    //         $query = Stock::with(['store', 'category', 'unit']);

    //         $user = auth()->user();
    //         $sessionStoreId = session('selected_store_id');

    //         // 🔹 Store filter
    //         if (in_array($user->role_id, [1, 2, 3, 5])) {

    //             if ($request->filled('store')) {
    //                 if ($request->store === 'all' || $request->store === 'choose') {
    //                     // Products are catalog-wide. "Choose Store" no longer hides other stores.
    //                     // } elseif ($request->store !== 'choose') {
    //                     //     $query->where('store_id', $request->store);
    //                     // } elseif ($sessionStoreId) {
    //                     //     $query->where('store_id', $sessionStoreId);
    //                 } else {
    //                     $query->where('store_id', $request->store);
    //                 }
    //             }
    //             // } elseif ($sessionStoreId) {
    //             //     $query->where('store_id', $sessionStoreId);
    //             // }
    //         }

    //         // 🔹 Category filter
    //         if ($request->filled('category') && $request->category !== 'choose') {
    //             $query->where('category_id', $request->category);
    //         }

    //         return datatables()
    //             ->eloquent($query)

    //             // 🔍 Global search
    //             ->filter(function ($query) use ($request) {
    //                 if ($request->has('search') && $request->search['value']) {

    //                     $search = $request->search['value'];

    //                     $query->where(function ($q) use ($search) {
    //                         $q->where('product_name', 'like', "%{$search}%")
    //                         ->orWhere('sku', 'like', "%{$search}%")
    //                         ->orWhereHas('store', function ($q) use ($search) {
    //                             $q->where('name', 'like', "%{$search}%");
    //                         })
    //                         ->orWhereHas('category', function ($q) use ($search) {
    //                             $q->where('name', 'like', "%{$search}%");
    //                         });
    //                     });
    //                 }
    //             })

    //             // 🔹 Columns
    //             ->addColumn('store_name', fn($row) => $row->store->name ?? '')

    //             ->addColumn('product', function ($row) {

    //                 $imagePath = $row->images;
    //                 $image = asset('admin/assets/img/avatar/default-product.png');

    //                 if (! empty($imagePath) && Str::startsWith($imagePath, 'products/')) {
    //                     $image = asset('storage/' . $imagePath);
    //                 }

    //                 return '
    //                 <div class="productimgname">
    //                     <a href="javascript:void(0);" class="product-img stock-img">
    //                         <img src="' . $image . '" alt="' . e($row->product_name) . '" />
    //                     </a>
    //                     <a href="javascript:void(0);">' . e($row->product_name) . '</a>
    //                 </div>';
    //             })

    //             ->addColumn('sku', fn($row) => $row->sku)
    //             ->addColumn('category', fn($row) => $row->category->name ?? '')
    //             ->addColumn('price', fn($row) => $row->price)
    //             ->addColumn('unit', fn($row) => $row->unit->name ?? '')
    //             ->addColumn('quantity', fn($row) => $row->quantity)

    //             ->addColumn('actions', function ($stock) {

    //                 $user = auth()->user();
    //                 if (!$user) return '';

    //                 $sessionStoreId = session('selected_store_id');
    //                 $canActOnAllStores = (int) $user->role_id === 1;

    //                 $canActOnThisStock = !$sessionStoreId
    //                     || $canActOnAllStores
    //                     || (int) $stock->store_id === (int) $sessionStoreId;

    //                 $editBtn = '';
    //                 $deleteBtn = '';

    //                 if ($canActOnThisStock && hasPermission('update', 'stock')) {
    //                     $editUrl = route('admin.stock.edit', $stock->id);
    //                     $editBtn = '
    //                     <a class="p-2" href="' . $editUrl . '">
    //                         <i data-feather="edit" class="feather-edit"></i>
    //                     </a>';
    //                 }

    //                 if ($canActOnThisStock && hasPermission('delete', 'stock')) {
    //                     $deleteBtn = '
    //                     <a href="javascript:void(0);" class="delete-stock confirm-text p-2" data-id="' . $stock->id . '">
    //                         <i data-feather="trash-2" class="feather-trash-2"></i>
    //                     </a>';
    //                 }

    //                 if ($editBtn || $deleteBtn) {
    //                     return '<div class="edit-delete-action">' . $editBtn . $deleteBtn . '</div>';
    //                 }

    //                 return '';
    //             })

    //             ->rawColumns(['product', 'actions'])
    //             ->make(true);
    //     }

    //     // 🔹 Only load these for page view (not AJAX)
    //     $units = Unit::all();
    //     $categories = Category::all();
    //     $stores = Store::all();

    //     return view('admin.stock-list', [
    //         'categories' => $categories,
    //         'stores' => $stores,
    //         'units' => $units,
    //         'datatableConfig' => app(DatatablePaginationService::class)->config('stock-list', [
    //             'fixedHeader' => true,
    //             'fixedHeaderInScrollMode' => true,
    //         ]),
    //     ]);
    // }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $user = auth()->user();
            $query = Stock::with(['category', 'unit']);

            $selectedStore = $request->input('store');
            $sessionStoreId = session('selected_store_id');
            $canPickStore = in_array((int) $user->role_id, [1, 2], true);

            // Products are not owned by a store. This id only chooses which quantity on hand to show.
            $qohStoreId = null;
            if ($canPickStore && $selectedStore === 'all') {
                $qohStoreId = null;
            } elseif ($canPickStore && $selectedStore && !in_array($selectedStore, ['all', 'choose'], true)) {
                $qohStoreId = (int) $selectedStore;
            } elseif ($sessionStoreId) {
                $qohStoreId = (int) $sessionStoreId;
            }

            // Category filter
            if ($request->filled('category') && $request->category !== 'choose') {
                $query->where('category_id', $request->category);
            }

            // Search filter
            if ($request->filled('search.value')) {

                $search = $request->input('search.value');

                $query->where(function ($q) use ($search) {

                    $q->where('product_name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhereHas('store', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('category', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // One row per product. Quantity is the selected store's on-hand amount (or the combined amount).
            $idSql = 'MIN(id)';
            $storeSql = 'MIN(store_id)';
            $quantitySql = 'COALESCE(SUM(quantity), 0)';
            $priceSql = 'CASE WHEN SUM(CASE WHEN quantity > 0 THEN quantity ELSE 0 END) > 0 THEN SUM(CASE WHEN quantity > 0 THEN price * quantity ELSE 0 END) / SUM(CASE WHEN quantity > 0 THEN quantity ELSE 0 END) ELSE MAX(price) END';
            $bindings = [];

            if ($qohStoreId) {
                $idSql = 'COALESCE(MIN(CASE WHEN store_id = ? THEN id END), MIN(id))';
                $storeSql = 'COALESCE(MIN(CASE WHEN store_id = ? THEN store_id END), MIN(store_id))';
                $quantitySql = 'COALESCE(SUM(CASE WHEN store_id = ? THEN quantity ELSE 0 END), 0)';
                $priceSql = 'COALESCE(MAX(CASE WHEN store_id = ? THEN price END), MAX(price))';
                $bindings = [$qohStoreId, $qohStoreId, $qohStoreId, $qohStoreId];
            }

            $query->selectRaw("
                {$idSql} as id,
                {$storeSql} as store_id,
                product_id,
                MIN(product_name) as product_name,
                MIN(sku) as sku,
                MIN(category_id) as category_id,
                MIN(unit_id) as unit_id,
                {$quantitySql} as quantity,
                {$priceSql} as price,
                MIN(images) as images
            ", $bindings)
                ->groupBy('product_id');

            return datatables()
                ->eloquent($query)

                ->addColumn('store_name', function ($row) {
                    return $row->store->name ?? 'All Stores';
                })

                ->addColumn('product', function ($row) {

                    $imagePath = $row->images;

                    $image = asset('admin/assets/img/avatar/default-product.png');

                    if (!empty($imagePath) && Str::startsWith($imagePath, 'products/')) {
                        $image = asset('storage/' . $imagePath);
                    }

                    return '
                        <div class="productimgname">
                            <a href="javascript:void(0);" class="product-img stock-img">
                                <img src="' . $image . '" alt="' . e($row->product_name) . '" />
                            </a>
                            <a href="javascript:void(0);">' . e($row->product_name) . '</a>
                        </div>';
                })

                ->addColumn('sku', fn($row) => $row->sku)

                ->addColumn('category', fn($row) => $row->category->name ?? '')

                ->addColumn('price', fn($row) => $row->price)

                ->addColumn('unit', fn($row) => $row->unit->name ?? '')

                ->addColumn('quantity', fn($row) => $row->quantity)

                ->addColumn('actions', function ($stock) {

                    $user = auth()->user();

                    if (!$user) {
                        return '';
                    }

                    $sessionStoreId = session('selected_store_id');

                    $canActOnAllStores = (int) $user->role_id === 1;

                    $canActOnThisStock = !$sessionStoreId
                        || $canActOnAllStores
                        || (int) $stock->store_id === (int) $sessionStoreId;

                    $editBtn = '';
                    $deleteBtn = '';

                    if ($canActOnThisStock && hasPermission('update', 'stock')) {

                        $editUrl = route('admin.stock.edit', $stock->id);

                        $editBtn = '
                            <a class="p-2" href="' . $editUrl . '">
                                <i data-feather="edit" class="feather-edit"></i>
                            </a>';
                    }

                    if ($canActOnThisStock && hasPermission('delete', 'stock')) {

                        $deleteBtn = '
                            <a href="javascript:void(0);" class="delete-stock confirm-text p-2"
                                data-id="' . $stock->id . '">
                                <i data-feather="trash-2" class="feather-trash-2"></i>
                            </a>';
                    }

                    if ($editBtn || $deleteBtn) {
                        return '<div class="edit-delete-action">' . $editBtn . $deleteBtn . '</div>';
                    }

                    return '';
                })

                ->rawColumns(['product', 'actions'])
                ->make(true);
        }

        // Page view
        $units = Unit::all();
        $categories = Category::all();

        $stores = Store::orderBy('name')->get();

        return view('admin.stock-list', [
            'categories' => $categories,
            'stores' => $stores,
            'units' => $units,
            'datatableConfig' => app(DatatablePaginationService::class)->config('stock-list', [
                'fixedHeader' => true,
                'fixedHeaderInScrollMode' => true,
            ]),
        ]);
    }



// commentd on 06-04-2026
 /*  
    public function index(Request $request)
    {
        $units = Unit::all();
        $categories = Category::all();
        $stores = Store::all();

        if ($request->ajax()) {
            $query = Stock::with(['store', 'category', 'unit']);

            $user = auth()->user();
            $sessionStoreId = session('selected_store_id');

            if (in_array($user->role_id, [1, 2, 3, 5])) {
                // Store filter:
                // - "all" => show stocks from all stores
                // - specific store id => show stocks from that store
                // - "choose" => fallback to user's selected_store_id (if available)
                if ($request->has('store')) {
                    if ($request->store === 'all') {
                        // no filter
                    } elseif ($request->store && $request->store !== 'choose') {
                        $query->where('store_id', $request->store);
                    } elseif ($sessionStoreId) {
                        $query->where('store_id', $sessionStoreId);
                    }
                } elseif ($sessionStoreId) {
                    $query->where('store_id', $sessionStoreId);
                }
            }

            if ($request->has('category') && $request->category !== 'choose') {
                $query->where('category_id', $request->category);
            }

            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            }

            //$stocks = $query->get();
            $stocks = $query;
            return datatables()
                ->of($stocks)
                ->addColumn('store_name', fn($row) => $row->store->name ?? '')
                ->addColumn('product', function ($row) {
                    $imagePath = $row->images;  // value from DB
                    $image = asset('admin/assets/img/avatar/default-product.png');  // default

                    if (
                        $imagePath &&  // not null / not empty
                        Str::startsWith($imagePath, 'products/') &&  // real upload, not pending
                        Storage::disk('public')->exists($imagePath)  // file is present
                    ) {
                        $image = asset('storage/' . $imagePath);  // serve the real image
                    }

                    return '
        <div class="productimgname">
            <a href="javascript:void(0);" class="product-img stock-img">
                <img src="' . $image . '" alt="' . e($row->product_name) . '" />
            </a>
            <a href="javascript:void(0);">' . e($row->product_name) . '</a>
        </div>';
                })
                ->addColumn('sku', fn($row) => $row->sku)
                ->addColumn('category', fn($row) => $row->category->name ?? '')
                ->addColumn('price', fn($row) => $row->price)
                ->addColumn('unit', fn($row) => $row->unit->name ?? '')
                ->addColumn('quantity', fn($row) => $row->quantity)
                ->addColumn('actions', function ($stock) {
                    $user = auth()->user();
                    if (!$user)
                        return '';

                    $sessionStoreId = session('selected_store_id');
                    $canActOnAllStores = (int) $user->role_id === 1; // super-admin
                    $canActOnThisStock = !$sessionStoreId
                        || $canActOnAllStores
                        || (int) $stock->store_id === (int) $sessionStoreId;

                    $editBtn = '';
                    $deleteBtn = '';

                    if ($canActOnThisStock && hasPermission('update', 'stock')) {
                        $editUrl = route('admin.stock.edit', $stock->id);
                        $editBtn = '
            <a class="p-2" href="' . $editUrl . '">
                <i data-feather="edit" class="feather-edit"></i>
            </a>
        ';
                    }

                    if ($canActOnThisStock && hasPermission('delete', 'stock')) {
                        $deleteBtn = '
            <a href="javascript:void(0);" class="delete-stock confirm-text p-2" data-id="' . $stock->id . '">
                <i data-feather="trash-2" class="feather-trash-2"></i>
            </a>
        ';
                    }

                    if ($editBtn || $deleteBtn) {
                        return '<div class="edit-delete-action">' . $editBtn . $deleteBtn . '</div>';
                    }

                    return '';
                })
                ->rawColumns(['product', 'actions'])
                ->make(true);
        }

        return view('admin.stock-list', compact('categories', 'stores', 'units'));
    }


   
    public function index(Request $request)
    {
        $units = Unit::all();
        $categories = Category::all();
        $stores = Store::all();

        if ($request->ajax()) {
            $query = Stock::with(['store', 'category', 'unit']);

            $user = auth()->user();
            $sessionStoreId = session('selected_store_id');

            if (in_array($user->role_id, [1, 2, 3, 5])) {
                if ($request->has('store') && $request->store === 'all') {
                } elseif ($sessionStoreId) {
                    $query->where('store_id', $sessionStoreId);
                }
            }

            if ($request->has('category') && $request->category !== 'choose') {
                $query->where('category_id', $request->category);
            }

            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            }

            $stocks = $query; // ❗ removed ->get()

            return datatables()
                ->of($stocks)
                ->addColumn('store_name', fn($row) => $row->store->name ?? '')
                ->addColumn('product', function ($row) {

                    $imagePath = $row->images;
                    $image = asset('admin/assets/img/avatar/default-product.png');

                    if (
                        $imagePath &&
                        Str::startsWith($imagePath, 'products/') &&
                        Storage::disk('public')->exists($imagePath)
                    ) {
                        $image = asset('storage/' . $imagePath);
                    }

                    return '
                    <div class="productimgname">
                        <a href="javascript:void(0);" class="product-img stock-img">
                            <img src="' . $image . '" alt="' . e($row->product_name) . '" />
                        </a>
                        <a href="javascript:void(0);">' . e($row->product_name) . '</a>
                    </div>';
                })
                ->addColumn('sku', fn($row) => $row->sku)
                ->addColumn('category', fn($row) => $row->category->name ?? '')
                ->addColumn('price', fn($row) => $row->price)
                ->addColumn('unit', fn($row) => $row->unit->name ?? '')
                ->addColumn('quantity', fn($row) => $row->quantity)
                ->addColumn('actions', function ($stock) {

                    $user = auth()->user();
                    if (!$user) return '';

                    $editBtn = '';
                    $deleteBtn = '';

                    if (hasPermission('update', 'stock')) {
                        $editUrl = route('admin.stock.edit', $stock->id);

                        $editBtn = '
                        <a class="p-2" href="' . $editUrl . '">
                            <i data-feather="edit" class="feather-edit"></i>
                        </a>';
                    }

                    if (hasPermission('delete', 'stock')) {

                        $deleteBtn = '
                        <a href="javascript:void(0);" class="delete-stock confirm-text p-2" data-id="' . $stock->id . '">
                            <i data-feather="trash-2" class="feather-trash-2"></i>
                        </a>';
                    }

                    if ($editBtn || $deleteBtn) {
                        return '<div class="edit-delete-action">' . $editBtn . $deleteBtn . '</div>';
                    }

                    return '';
                })
                ->rawColumns(['product', 'actions'])
                ->make(true);
        }

        return view('admin.stock-list', compact('categories', 'stores', 'units'));
    }
    */

    public function create()
    {
        // Inactive categories and units stay off the product form dropdowns.
        // $categories = Category::get();
        // $units = Unit::all();
        $categories = Category::query()->where('status', 1)->orderBy('name')->get(); 
        
        $units = Unit::query()->where('status', 1)->orderBy('name')->get();
        $stores = Store::orderBy('name')->get();
        $glAccounts = GlAccount::query()
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('code')
            ->get();
        // $stock_last_insert_id = Stock::latest()->value('id');
        // Get the last inserted Stock ID
        $lastStock = Stock::orderBy('id', 'desc')->first();
        $lastId = $lastStock ? $lastStock->id : 0;

        // Generate formatted stock ID with "p_" prefix and zero-padding (e.g., p_001, p_002)
        $stock_last_insert_id = 'P_' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);

        return view('admin.add-stock', compact(
            'categories',
            'units',
            'stores',
            'stock_last_insert_id',
            'glAccounts'
        ));
    }

    public function store(Request $request)
    {
        //dd('Working');
        if (!hasPermission('create', 'stock')) {
            return view('admin.partials.authorisation');
        }
        $product_id = $request->product_id ?? ('P_' . str_pad(Stock::count() + 1, 3, '0', STR_PAD_LEFT));

        // Store comes from header selection (session), not the product form dropdown.
        // $storeId = (int) $request->store_id;
        $storeId = (int) (session('selected_store_id') ?? $request->store_id);
        if (! $storeId) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['store_id' => 'Please select a store from the header first.']);
        }
        $request->merge(['store_id' => $storeId]);

        $request->validate([
            //'store_id' => 'required|exists:stores,id',
            'store_id' => 'required|exists:stores,id',
            'product_id' => 'required|string|unique:stocks,product_id,NULL,id,store_id,' . $storeId,
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|in:service,inventory_stock,non_inventory_stock',
            'sku' => 'required|string|unique:stocks,sku',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'debit_account_id' => 'required|exists:gl_accounts,id',
            'credit_account_id' => 'required|exists:gl_accounts,id|different:debit_account_id',
            // 'supplier_name' => 'nullable|string',
            // 'purchase_date' => 'required|date_format:d-m-Y',
            // Legacy product form used manual opening quantity entry.
            // 'qty' => 'required|integer|min:1|max:999999999',
            // Point 18 — cost is system-calculated from stock transactions (not manual entry).
            // 'cost' => 'required|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'store_markups' => 'nullable|array',
            'store_markups.*' => 'nullable|numeric|min:0|max:1000',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'is_taxable' => 'required|boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100|required_if:is_taxable,1',
            'manufactured_date' => 'nullable|date',
            'expiry_on' => 'nullable|date|after:manufactured_date',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('products', 'public')
            : null;

        // Point 18 — cost is derived from purchases and stock movements, not the form.
        // $cost = round((float) $request->cost, 2);
        $cost = 0.0;
        //$storeId = (int) $request->store_id;
        // $storeId = session('selected_store_id') ?? 1;
        $markups = $this->normalizeStoreMarkups($request->input('store_markups', []));
        $sellingPrice = $this->sellingPriceForStore($cost, $markups, $storeId);

        $stock = Stock::create([
            'store_id' => $storeId,
            'unit_id' => $request->unit_id,
            'product_id' => $product_id,
            'product_name' => $request->product_name,
            'product_type' => $request->product_type,
            'sku' => $request->sku,
            'category_id' => $request->category_id,
            'debit_account_id' => $request->debit_account_id,
            'credit_account_id' => $request->credit_account_id,
            // 'supplier_name' => $request->supplier_name,
            // 'purchase_date' => Carbon::createFromFormat('d-m-Y', $request->purchase_date)->format('Y-m-d'),
            'description' => $request->description,
            // New products no longer accept manual quantity; stock is computed from transactions.
            // 'quantity' => $request->qty,
            'quantity' => 0,
            'cost' => $cost,
            'price' => $sellingPrice,
            'selling_price' => $sellingPrice,
            'average_cost' => 0,
            'discount' => $request->discount,
            'tax' => $request->tax,
            'is_taxable' => (bool) $request->boolean('is_taxable'),
            'tax_rate' => $request->boolean('is_taxable') ? $request->tax_rate : null,
            'manufactured_date' => $request->manufactured_date ? parse_date_input_to_db($request->manufactured_date) : null,
            'expiry_date' => $request->expiry_on ? parse_date_input_to_db($request->expiry_on) : null,
            'images' => $imagePath
        ]);

        $this->syncProductStoreMarkups($product_id, $markups);

        storeHistory(
            'stock_form',
            'created',
            'stock',
            $stock->id,
            $stock->toArray(),  // new values
            null  // no old values on creation
        );

        $userId = auth()->id();
        $storeName = \App\Models\Store::find($storeId)?->name;
        $message = 'New Stock has been added to store (' . $storeName . ')';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);

        return redirect()->back()->with('success', 'Stock added successfully!');
    }

    public function edit(Stock $stock)
    {
        // Inactive categories and units stay off the product form dropdowns.
        // Keep the product's current category/unit visible if it was later set Inactive.
        // $categories = Category::all();
        // $units = Unit::all();
        $categories = Category::query()
            ->where(function ($q) use ($stock) {
                $q->where('status', 1);
                if ($stock->category_id) {
                    $q->orWhere('id', $stock->category_id);
                }
            })
            ->orderBy('name')
            ->get();
        $stores = Store::orderBy('name')->get();
        $units = Unit::query()
            ->where(function ($q) use ($stock) {
                $q->where('status', 1);
                if ($stock->unit_id) {
                    $q->orWhere('id', $stock->unit_id);
                }
            })
            ->orderBy('name')
            ->get();
        $glAccounts = GlAccount::query()
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('code')
            ->get();

        $storeMarkups = ProductStoreMarkup::query()
            ->where('product_id', $stock->product_id)
            ->pluck('markup_percentage', 'store_id')
            ->map(fn ($value) => (float) $value)
            ->all();

        app(ProductUnitCostService::class)->syncStock($stock);
        $stock->refresh();
        $recordNav = RecordNavigator::forProduct($stock);

        return view('admin.add-stock', compact(
            'stock',
            'categories',
            'stores',
            'units',
            'glAccounts',
            'storeMarkups',
            'recordNav'
        ));
    }

    // public function update(Request $request, $id)
    // {
    //     // dd($request->all());
    //     $stock = Stock::findOrFail($id);
    //     if ($request->remove_image == '1') {
    //         if ($stock->images) {
    //             Storage::delete('public/' . $stock->images);
    //             $imagePath = null;
    //         }
    //     } else {
    //         $imagePath = $stock->images;
    //     }

    //     if ($request->hasFile('image')) {
    //         if ($stock->images) {
    //             Storage::delete('public/' . $stock->images);
    //         }
    //         $imagePath = $request->file('image')->store('products', 'public');
    //     }

    //     $product_id = $request->product_id ?? $stock->product_id ?? ('P_' . str_pad(Stock::count() + 1, 3, '0', STR_PAD_LEFT));

    //     $request->validate([
    //         'store_id' => 'required|exists:stores,id',
    //         'unit_id' => 'required|exists:units,id',
    //         'product_id' => 'required|string|unique:stocks,product_id,' . $stock->id . ',id,store_id,' . $request->store_id,
    //         'product_name' => 'required|string|max:255',
    //         'sku' => [
    //             'required',
    //             Rule::unique('stocks')->where(function ($query) use ($request, $stock) {
    //                 return $query
    //                     ->where('store_id', $request->store_id)
    //                     ->where('sku', $request->sku)
    //                     ->where('product_id', $request->product_id);
    //             })->ignore($stock->id),
    //         ],
    //         'category_id' => 'required|exists:categories,id',
    //         'supplier_name' => 'nullable|string',
    //         'purchase_date' => 'required|date_format:d-m-Y',
    //         'description' => 'nullable|string|max:255',
    //         'qty' => 'required|integer|min:1|max:999999999',
    //         'price' => 'required|numeric|min:0',
    //         'discount' => 'nullable|numeric|min:0',
    //         'tax' => 'nullable|numeric|min:0',
    //         'manufactured_date' => 'nullable|date',
    //         'expiry_on' => 'nullable|date|after:manufactured_date',
    //         // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif'
    //     ], [
    //         'qty.max' => 'Quantity limit exceed (Max Qty: 999999999)'
    //     ]);

    //     $stock->update([
    //         'store_id' => $request->store_id,
    //         'unit_id' => $request->unit_id,
    //         'product_name' => $request->product_name,
    //         'product_id' => $product_id,
    //         'sku' => $request->sku,
    //         'category_id' => $request->category_id,
    //         'supplier_name' => $request->supplier_name,
    //         'purchase_date' => Carbon::createFromFormat('d-m-Y', $request->purchase_date)->format('Y-m-d'),
    //         'description' => $request->description,
    //         'quantity' => $request->qty,
    //         'price' => $request->price,
    //         'discount' => $request->discount ?? 0,
    //         'tax' => $request->tax ?? 0,
    //         'manufactured_date' => $request->manufactured_date ? Carbon::createFromFormat('d-m-Y', $request->manufactured_date)->format('Y-m-d') : null,
    //         'expiry_date' => $request->expiry_on ? Carbon::createFromFormat('d-m-Y', $request->expiry_on)->format('Y-m-d') : null,
    //         'images' => $imagePath
    //     ]);
    //     $userId = auth()->id();
    //     $stockName = $request->product_name;
    //     $storeName = \App\Models\Store::find($request->store_id)?->name;
    //     $message = 'Stock (' . $stockName . ') has been updated in store (' . $storeName . ')';
    //     $userName = auth()->user()->name;
    //     $port = env('NOTIFICATION_PORT');
    //     $url = "http://localhost:$port/notify";
    //     $response = Http::post($url, [
    //         'userId' => $userId,
    //         'message' => $message,
    //         'user_name' => $userName,  // Send the user's name
    //     ]);
    //     NotificationHelper::add(auth()->id(), $message);
    //     return redirect()->route('admin.stock.list')->with('success', 'Stock updated successfully!');
    // }
    public function update(Request $request, $id)
    {
        if (!hasPermission('update', 'stock')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }
        $stock = Stock::findOrFail($id);

        // Store-level protection: allow edit only for user's selected store (non-superadmin).
        $user = auth()->user();
        $sessionStoreId = session('selected_store_id');
        $restrictByStore = $user && (int) $user->role_id !== 1 && $sessionStoreId;
        if ($restrictByStore && (int) $stock->store_id !== (int) $sessionStoreId) {
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access for this store.'], 403);
            }

            return redirect()->back()->withErrors(['stock' => 'Unauthorized access for this store.']);
        }

        $oldData = $stock->toArray();  // 🟡 Step 1: Capture old data

        // Image logic (no change)

        if ($request->remove_image == '1') {
            if ($stock->images) {
                Storage::delete('public/' . $stock->images);
                $imagePath = null;
            }
        } else {
            $imagePath = $stock->images;
        }

        if ($request->hasFile('image')) {
            if ($stock->images) {
                Storage::delete('public/' . $stock->images);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product_id = $request->product_id ?? $stock->product_id ?? ('P_' . str_pad(Stock::count() + 1, 3, '0', STR_PAD_LEFT));

        // Store comes from header selection (session), not the product form dropdown.
        // $storeId = (int) $request->store_id;
        $storeId = (int) (session('selected_store_id') ?? $request->store_id ?? $stock->store_id);
        if (! $storeId) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['store_id' => 'Please select a store from the header first.']);
        }
        $request->merge(['store_id' => $storeId]);

        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'unit_id' => 'required|exists:units,id',
            'product_id' => 'required|string|unique:stocks,product_id,' . $stock->id . ',id,store_id,' . $storeId,
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|in:service,inventory_stock,non_inventory_stock',
            'debit_account_id' => 'required|exists:gl_accounts,id',
            'credit_account_id' => 'required|exists:gl_accounts,id|different:debit_account_id',
            'sku' => [
                'required',
                Rule::unique('stocks')->where(function ($query) use ($request, $stock, $storeId) {
                    return $query
                        ->where('store_id', $storeId)
                        ->where('sku', $request->sku)
                        ->where('product_id', $request->product_id);
                })->ignore($stock->id),
            ],
            'category_id' => 'required|exists:categories,id',
            'supplier_name' => 'nullable|string',
            // Legacy edit flow required purchase_date even though the field is no longer shown.
            // 'purchase_date' => 'nullable|date_format:d-m-Y',
            'purchase_date' => 'nullable|app_date',
            'description' => 'nullable|string|max:255',
            // Legacy product edit used manual quantity entry.
            // 'qty' => 'required|integer|min:1|max:999999999',
            // Point 18 — cost is system-calculated from stock transactions (not manual entry).
            // 'cost' => 'required|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'store_markups' => 'nullable|array',
            'store_markups.*' => 'nullable|numeric|min:0|max:1000',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'is_taxable' => 'required|boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100|required_if:is_taxable,1',
            'manufactured_date' => 'nullable|date',
            'expiry_on' => 'nullable|date|after:manufactured_date',
        ]);

        // Point 18 — cost is derived from purchases and stock movements, not the form.
        // $cost = round((float) $request->cost, 2);
        // $storeId = (int) $request->store_id;
        $markups = $this->normalizeStoreMarkups($request->input('store_markups', []));
        $unitCostService = app(ProductUnitCostService::class);
        $cost = $unitCostService->unitCostForStock($stock);
        $sellingPrice = $this->sellingPriceForStore($cost, $markups, $storeId);

        $updateData = [
            'store_id' => $storeId,
            'unit_id' => $request->unit_id,
            'product_name' => $request->product_name,
            'product_id' => $product_id,
            'product_type' => $request->product_type,
            'sku' => $request->sku,
            'category_id' => $request->category_id,
            'debit_account_id' => $request->debit_account_id,
            'credit_account_id' => $request->credit_account_id,
            'supplier_name' => $request->supplier_name,
            'purchase_date' => $request->purchase_date
                ? parse_date_input_to_db($request->purchase_date)
                : null,
            'description' => $request->description,
            // Quantity is transaction-derived and is no longer edited on the product form.
            // 'quantity' => $request->qty,
            // 'cost' => $cost,
            'price' => $sellingPrice,
            'selling_price' => $sellingPrice,
            'discount' => $request->discount ?? 0,
            'tax' => $request->tax ?? 0,
            'is_taxable' => (bool) $request->boolean('is_taxable'),
            'tax_rate' => $request->boolean('is_taxable') ? $request->tax_rate : null,
            'manufactured_date' => $request->manufactured_date
                ? parse_date_input_to_db($request->manufactured_date)
                : null,
            'expiry_date' => $request->expiry_on
                ? parse_date_input_to_db($request->expiry_on)
                : null,
            'images' => $imagePath,
        ];

        // 🟡 Step 3: Compare differences
        $new = array_diff_assoc_recursive($updateData, $oldData);
        $old = array_diff_assoc_recursive($oldData, $updateData);

        // 🟢 Step 4: Save history only if there are changes
        if ($new || $old) {
            storeHistory(
                'stock_form',
                'updated',
                'stock',
                $stock->id,
                $new,
                $old
            );
        }

        $stock->update($updateData);

        $unitCostService->syncStock($stock->fresh());
        $this->syncProductStoreMarkups($product_id, $markups);

        // Notification logic
        $userId = auth()->id();
        $stockName = $request->product_name;
        $storeName = \App\Models\Store::find($storeId)?->name;
        $message = 'Stock (' . $stockName . ') has been updated in store (' . $storeName . ')';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);

        return redirect()->route('admin.stock.list')->with('success', 'Stock updated successfully!');
    }

    // public function destroy(Request $request, $id): JsonResponse
    // {
    //     try {
    //         $stock = Stock::findOrFail($id);
    //         $productName = $stock->product_name ?? 'Unknown Product';
    //         $storeName = $stock->store?->name ?? 'Unknown Store';
    //         $message = 'Stock (' . $productName . ') has been deleted from store (' . $storeName . ')';
    //         $stock->delete();
    //         $port = env('NOTIFICATION_PORT');
    //         $url = "http://localhost:$port/notify";
    //         $response = Http::post($url, [
    //             'userId' => auth()->id(),
    //             'message' => $message,
    //             'user_name' => auth()->user()->name,  // Send the user's name
    //         ]);
    //         if ($response->failed()) {
    //             \Log::error('Notification failed: ' . $response->body());
    //         }
    //         NotificationHelper::add(auth()->id(), $message);
    //         return response()->json(['success' => true, 'message' => 'Stock deleted successfully.']);
    //     } catch (\Exception $e) {
    //         \Log::error('Failed to delete stock: ' . $e->getMessage());
    //         return response()->json(['success' => false, 'message' => 'Failed to delete stock.']);
    //     }
    // }

    public function destroy(Request $request, $id): JsonResponse
    {
        if (!hasPermission('delete', 'stock')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }
        try {
            $stock = Stock::findOrFail($id);

            // Store-level protection: allow delete only for user's selected store (non-superadmin).
            $user = auth()->user();
            $sessionStoreId = session('selected_store_id');
            $restrictByStore = $user && (int) $user->role_id !== 1 && $sessionStoreId;
            if ($restrictByStore && (int) $stock->store_id !== (int) $sessionStoreId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access for this store.'], 403);
            }

            $oldData = $stock->toArray();  // 🔍 Capture data before deletion

            $productName = $stock->product_name ?? 'Unknown Product';
            $storeName = $stock->store?->name ?? 'Unknown Store';
            $message = 'Stock (' . $productName . ') has been deleted from store (' . $storeName . ')';

            // 🔐 Store delete history
            storeHistory(
                'stock_form',
                'deleted',
                'stock',
                $stock->id,
                null,  // no new values on delete
                $oldData  // full old data before deletion
            );

            $stock->delete();

            NotificationHelper::add(auth()->id(), $message);
            NotificationHelper::pushRealtime(auth()->id(), $message);

            return response()->json(['success' => true, 'message' => 'Stock deleted successfully.']);
        } catch (\Exception $e) {
            \Log::error('Failed to delete stock: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete stock.']);
        }
    }

    public function export()
    {
        $stocks = Stock::select(
            'stocks.product_name',
            'stocks.product_id',
            'stores.name as store_name',
            'stocks.sku',
            'categories.name as category_name',
            'units.name as unit_name',
            'stocks.supplier_name',
            'stocks.purchase_date',
            'stocks.quantity',
            'stocks.price',
            'stocks.manufactured_date',
            'stocks.expiry_date'
        )
            ->leftJoin('stores', 'stocks.store_id', '=', 'stores.id')
            ->leftJoin('categories', 'stocks.category_id', '=', 'categories.id')
            ->leftJoin('units', 'stocks.unit_id', '=', 'units.id')
            ->get();

        $filename = 'stocks_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = [
            'Product Name',
            'Product ID',
            'Store Name',
            'SKU',
            'Category Name',
            'Unit Name',
            'Supplier Name',
            'Purchase Date',
            'Quantity',
            'Price',
            'Manufactured Date',
            'Expiry Date'
        ];

        $callback = function () use ($stocks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($stocks as $stock) {
                fputcsv($file, [
                    $stock->product_name,
                    $stock->product_id,
                    $stock->store_name,
                    $stock->sku,
                    $stock->category_name,
                    $stock->unit_name,
                    $stock->supplier_name,
                    $stock->purchase_date,
                    $stock->quantity,
                    $stock->price,
                    $stock->manufactured_date,
                    $stock->expiry_date,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // public function import(Request $request)
    // {
    //     /* 1. Validate inputs */
    //     $request->validate([
    //         'csv_file' => 'required|mimes:csv,txt',
    //         'images.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,bmp,gif,svg,jfif'
    //     ]);

    //     /* 2. Index uploaded images by original filename */
    //     $imageMap = [];
    //     $hasImageFolder = $request->hasFile('images');  // flag for later
    //     if ($hasImageFolder) {
    //         foreach ($request->file('images') as $img) {
    //             $imageMap[$img->getClientOriginalName()] = $img;
    //         }
    //     }

    //     /* 3. Open CSV & verify header */
    //     $file = fopen($request->file('csv_file')->getRealPath(), 'r');
    //     $header = array_map('trim', fgetcsv($file));
    //     $expected = [
    //         'Product Name', 'Product ID', 'Store Name', 'SKU', 'Category Name',
    //         'Unit Name', 'Supplier Name', 'Purchase Date', 'Quantity', 'Price',
    //         'Manufactured Date', 'Expiry Date', 'Image'
    //     ];
    //     if ($header !== $expected) {
    //         fclose($file);
    //         return back()->withErrors(['csv_file' => 'Header mismatch. Download a fresh dummy CSV.']);
    //     }

    //     /* 4. Prep helpers */
    //     $baseCount = Stock::count();
    //     $newCounter = 1;
    //     $pad = 3;
    //     $parseDate = fn($d) => $d ? Carbon::parse($d)->format('Y-m-d') : null;

    //     $rowsImported = 0;
    //     $conflicts = [];
    //     $missingImgs = [];
    //     $storeErrors = [];

    //     DB::beginTransaction();
    //     $rowNumber = 1;

    //     /* 5. Row loop */
    //     while (($row = fgetcsv($file)) !== false) {
    //         $rowNumber++;
    //         $row = array_map('trim', $row);

    //         /* Skip blank lines */
    //         if (!array_filter($row))
    //             continue;

    //         [
    //             $productName, $productIdCsv, $storeName, $sku, $categoryName,
    //             $unitName, $supplier, $purchase, $qty, $price,
    //             $mfgDate, $expDate, $imageFile
    //         ] = $row;

    //         /* Store must pre-exist (case-insensitive) */
    //         $store = Store::whereRaw('LOWER(name) = ?', [strtolower($storeName)])->first();
    //         if (!$store) {
    //             $storeErrors[] = "Row $rowNumber: store '$storeName' does not exist";
    //             continue;
    //         }

    //         /* Category & Unit */
    //         $category = Category::firstOrCreate(['name' => $categoryName]);
    //         $unit = $unitName ? Unit::firstOrCreate(['name' => $unitName]) : null;

    //         /* Duplicate-SKU check */
    //         if (Stock::where('store_id', $store->id)->where('sku', $sku)->exists()) {
    //             $conflicts[] = "Row $rowNumber: SKU '$sku' already exists in store '$storeName'";
    //             continue;
    //         }

    //         /* Product-ID */
    //         $productId = 'P_' . str_pad($baseCount + $newCounter, $pad, '0', STR_PAD_LEFT);
    //         $newCounter++;

    //         /* Image handling */
    //         $imagePath = null;
    //         if ($imageFile) {
    //             if ($hasImageFolder && isset($imageMap[$imageFile])) {
    //                 $imagePath = $imageMap[$imageFile]->storeAs('products', $imageFile, 'public');
    //             } elseif ($hasImageFolder) {
    //                 // Only warn if user actually uploaded a folder
    //                 $missingImgs[] = "Row $rowNumber: image '$imageFile' not found in uploaded folder";
    //             }
    //             // If no image folder uploaded, silently ignore
    //         }

    //         /* Insert stock */
    //         Stock::create([
    //             'store_id' => $store->id,
    //             'product_id' => $productId,
    //             'product_name' => $productName,
    //             'sku' => $sku,
    //             'category_id' => $category->id,
    //             'unit_id' => $unit?->id,
    //             'supplier_name' => $supplier,
    //             'purchase_date' => $parseDate($purchase),
    //             'manufactured_date' => $parseDate($mfgDate),
    //             'expiry_date' => $parseDate($expDate),
    //             'quantity' => is_numeric($qty) ? (int) $qty : 0,
    //             'price' => is_numeric($price) ? (float) $price : 0,
    //             'discount' => 0,
    //             'tax' => 0,
    //             'status' => 1,
    //             'images' => $imagePath,
    //         ]);

    //         $rowsImported++;
    //     }
    //     fclose($file);

    //     /* 6. Abort if any unknown store */
    //     if ($storeErrors) {
    //         DB::rollBack();
    //         return back()->with(['storeErrors' => $storeErrors]);
    //     }

    //     DB::commit();

    //     /* 7. Build flash messages */
    //     $message = "$rowsImported new stocks imported.";
    //     if ($conflicts)
    //         $message .= ' Some rows skipped (duplicate SKUs).';
    //     if ($missingImgs)
    //         $message .= ' Some images were not found.';

    //     return back()->with([
    //         'success' => $message,
    //         'conflicts' => $conflicts,
    //         'missing' => $missingImgs
    //     ]);
    // }

    public function import(Request $request)
    {
        if (!hasPermission('create', 'stock')) {
            return view('admin.partials.authorisation');
        }
        /* 1. Validate inputs */
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
            'images.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,bmp,gif,svg,jfif'
        ]);

        /* 2. Index uploaded images by original filename */
        $hasImageFolder = $request->hasFile('images');
        $imageMap = [];
        if ($hasImageFolder) {
            foreach ($request->file('images') as $img) {
                $imageMap[$img->getClientOriginalName()] = $img;
            }
        }

        /* 3. Verify CSV header */
        $file = fopen($request->file('csv_file')->getRealPath(), 'r');
        $header = array_map('trim', fgetcsv($file));
        $expected = [
            'Product Name', 'Product ID', 'Store Name', 'SKU', 'Category Name',
            'Unit Name', 'Supplier Name', 'Purchase Date', 'Quantity', 'Price',
            'Manufactured Date', 'Expiry Date', 'Image'
        ];
        if ($header !== $expected) {
            fclose($file);
            return back()->withErrors(['csv_file' => 'Header mismatch. Download a fresh dummy CSV.']);
        }

        /* 4. Helpers */
        $baseCount = Stock::count();
        $newCounter = 1;
        $pad = 3;
        $parseDate = fn($d) => $d ? Carbon::parse($d)->format('Y-m-d') : null;

        $rowsImported = 0;
        $conflicts = [];
        $missingImgs = [];
        $storeErrors = [];

        DB::beginTransaction();
        $rowNumber = 1;

        /* 5. Row loop */
        while (($row = fgetcsv($file)) !== false) {
            $rowNumber++;
            $row = array_map('trim', $row);
            if (!array_filter($row))
                continue;  // skip blank rows

            [
                $productName, $productIdCsv, $storeName, $sku, $categoryName,
                $unitName, $supplier, $purchase, $qty, $price,
                $mfgDate, $expDate, $imageFile
            ] = $row;

            /* store must pre‑exist */
            $store = Store::whereRaw('LOWER(name)=?', [strtolower($storeName)])->first();
            if (!$store) {
                $storeErrors[] = "Row $rowNumber: store '$storeName' does not exist";
                continue;
            }

            /* category & unit */
            $category = Category::firstOrCreate(['name' => $categoryName]);
            $unit = $unitName ? Unit::firstOrCreate(['name' => $unitName]) : null;

            /* duplicate SKU */
            if (Stock::where('store_id', $store->id)->where('sku', $sku)->exists()) {
                $conflicts[] = "Row $rowNumber: SKU '$sku' already exists in store '$storeName'";
                continue;
            }

            /* product id */
            $productId = 'P_' . str_pad($baseCount + $newCounter, $pad, '0', STR_PAD_LEFT);
            $newCounter++;

            /* image logic */
            $imagePath = null;
            if ($imageFile) {
                if ($hasImageFolder && isset($imageMap[$imageFile])) {
                    // image provided now
                    $imagePath = $imageMap[$imageFile]->storeAs('products', $imageFile, 'public');
                } else {
                    // image missing or folder not uploaded → mark as pending (raw filename)
                    $imagePath = $imageFile;
                    if ($hasImageFolder) {
                        $missingImgs[] = "Row $rowNumber: image '$imageFile' not found in uploaded folder";
                    }
                }
            }

            /* insert */
            $created = Stock::create([
                'store_id' => $store->id,
                'product_id' => $productId,
                'product_name' => $productName,
                'sku' => $sku,
                'category_id' => $category->id,
                'unit_id' => $unit?->id,
                'supplier_name' => $supplier,
                'purchase_date' => $parseDate($purchase),
                'manufactured_date' => $parseDate($mfgDate),
                'expiry_date' => $parseDate($expDate),
                'quantity' => is_numeric($qty) ? (int) $qty : 0,
                'price' => is_numeric($price) ? (float) $price : 0,
                'discount' => 0,
                'tax' => 0,
                'status' => 1,
                'images' => $imagePath,
            ]);

            // ✅ Store history
            storeHistory(
                'stock_import',
                'created',
                'stock',
                $created->id,
                $created->toArray(),
                null
            );

            $rowsImported++;
        }
        fclose($file);

        /* abort on unknown store */
        if ($storeErrors) {
            DB::rollBack();
            return back()->with(['storeErrors' => $storeErrors]);
        }
        DB::commit();

        /* summary */
        $message = "$rowsImported products imported.";
        if ($conflicts)
            $message .= ' Some rows skipped (duplicate SKUs).';
        if ($missingImgs)
            $message .= ' Some images were not found.';

        return back()->with([
            'success' => $message,
            'conflicts' => $conflicts,
            'missing' => $missingImgs,
        ]);
    }

    // public function uploadImages(Request $request)
    // {
    //     if (!hasPermission('create', 'stock')) {
    //         return view('admin.partials.authorisation');
    //     }
    //     // 1. Ensure at least one file was selected
    //     if (!$request->hasFile('images')) {
    //         return back()->withErrors(['images' => 'No images were uploaded.']);
    //     }

    //     // 2. Validate
    //     $request->validate([
    //         'images.*' => 'file|mimes:jpg,jpeg,png,webp,bmp,gif,svg,jfif'
    //     ]);

    //     $updated = 0;
    //     $notMatched = [];

    //     foreach ($request->file('images') as $image) {
    //         $filename = $image->getClientOriginalName();  // e.g. airpods.jpg

    //         // 3. Find stock where images column is either raw filename OR already prefixed
    //         $stock = Stock::where('images', $filename)
    //             ->orWhere('images', 'products/' . $filename)
    //             ->first();

    //         if (!$stock) {
    //             $notMatched[] = $filename;
    //             continue;
    //         }

    //         // 4. Store image to public/products/
    //         $storedPath = $image->storeAs('products', $filename, 'public');

    //         // 5. Update DB only if prefix missing OR file previously absent
    //         if (!Str::startsWith($stock->images, 'products/') ||
    //                 !Storage::disk('public')->exists($stock->images)) {
    //             $stock->update(['images' => $storedPath]);
    //         }

    //         $updated++;
    //     }

    //     /* 6. Build flash message */
    //     $msg = "$updated image(s) processed.";
    //     if ($notMatched) {
    //         return back()->with([
    //             'success' => $msg,
    //             'unmatched' => $notMatched  // show via SweetAlert
    //         ]);
    //     }

    //     return back()->with('success', $msg);
    // }
    public function uploadImages(Request $request)
    {
        if (!hasPermission('create', 'stock')) {
            return view('admin.partials.authorisation');
        }

        if (!$request->hasFile('images')) {
            return back()->withErrors(['images' => 'No images were uploaded.']);
        }

        $request->validate([
            'images.*' => 'file|mimes:jpg,jpeg,png,webp,bmp,gif,svg,jfif'
        ]);

        $updated = 0;
        $notMatched = [];

        foreach ($request->file('images') as $image) {
            $filename = $image->getClientOriginalName();  // e.g. wm.jpg

            // 🔹 Only match stocks where images == raw filename (NOT starting with products/)
            $stock = Stock::where('images', $filename)
                ->where(function ($q) {
                    $q
                        ->whereNull('images')
                        ->orWhereRaw("images NOT LIKE 'products/%'");
                })
                ->first();

            if (!$stock) {
                $notMatched[] = $filename;
                continue;
            }

            // Save file into /products
            $storedPath = $image->storeAs('products', $filename, 'public');

            // Update DB with prefixed path
            $stock->update(['images' => $storedPath]);

            $updated++;
        }

        $msg = "$updated image(s) processed.";
        if ($notMatched) {
            return back()->with([
                'success' => $msg,
                'unmatched' => $notMatched
            ]);
        }

        return back()->with('success', $msg);
    }

    public function downloadDummy()
    {
        $filename = 'dummy_stock_import.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = [
            'Product Name',
            'Product ID',
            'Store Name',
            'SKU',
            'Category Name',
            'Unit Name',
            'Supplier Name',
            'Purchase Date',
            'Quantity',
            'Price',
            'Manufactured Date',
            'Expiry Date',
            'Image'
        ];

        $exampleRows = [
            ['T-Shirt', '', 'Main Store', 'SKU-001', 'Apparel', 'Pcs', 'Supplier A', '15-06-2024', '100', '250.00', '10-06-2024', '10-06-2025', 'tshirt.jpg'],
            ['Jeans', '', 'Main Store', 'SKU-002', 'Apparel', 'Pcs', 'Supplier B', '10-06-2024', '50', '800.00', '01-06-2024', '01-06-2026', 'jeans.png'],
            ['Bluetooth Speaker', '', 'Electronics Hub', 'SKU-003', 'Electronics', 'Box', 'Supplier C', '20-06-2024', '30', '1500.00', '05-06-2024', '05-06-2027', 'product1.png'],
            ['Notebook', '', 'Main Store', 'SKU-004', 'Stationery', 'Piece', 'Supplier D', '12-06-2024', '200', '50.00', '', '', 'product2.png'],
            ['Coffee Mug', '', 'Cafe Shop', 'SKU-005', 'Kitchenware', 'Piece', 'Supplier E', '18-06-2024', '80', '120.00', '15-06-2024', '15-06-2025', 'product3.png'],
        ];

        $callback = function () use ($columns, $exampleRows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($exampleRows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function indexStoreManager(Request $request)
    {
        $units = Unit::all();
        $categories = Category::all();
        $sessionStoreId = session('selected_store_id');
        if ($request->ajax()) {
            $query = Stock::with(['category', 'unit']);

            if ($request->has('category') && $request->category !== 'choose') {
                $query->where('category_id', $request->category);
            }

            $storeId = (int) $sessionStoreId;
            $stocks = $query->selectRaw('
                    COALESCE(MIN(CASE WHEN store_id = ? THEN id END), MIN(id)) as id,
                    COALESCE(MIN(CASE WHEN store_id = ? THEN store_id END), MIN(store_id)) as store_id,
                    product_id,
                    MIN(product_name) as product_name,
                    MIN(sku) as sku,
                    MIN(category_id) as category_id,
                    MIN(unit_id) as unit_id,
                    COALESCE(SUM(CASE WHEN store_id = ? THEN quantity ELSE 0 END), 0) as quantity,
                    COALESCE(MAX(CASE WHEN store_id = ? THEN price END), MAX(price)) as price,
                    MIN(images) as images
                ', [$storeId, $storeId, $storeId, $storeId])
                ->groupBy('product_id')
                ->get();
            return datatables()
                ->of($stocks)
                ->addColumn('store_name', fn($row) => $row->store->name ?? '')
                ->addColumn('product', function ($row) {
                    $image = $row->images
                        ? asset('storage/' . $row->images)
                        : asset('admin/assets/img/avatar/default-product.png');

                    return '
                    <div class="productimgname">
                        <a href="javascript:void(0);" class="product-img stock-img">
                            <img src="' . $image . '" alt="product" />
                        </a>
                        <a href="javascript:void(0);">' . e($row->product_name) . '</a>
                    </div>';
                })
                ->addColumn('sku', fn($row) => $row->sku)
                ->addColumn('category', fn($row) => $row->category->name ?? '')
                ->addColumn('price', fn($row) => $row->price)
                ->addColumn('unit', fn($row) => $row->unit->name ?? '')
                ->addColumn('quantity', fn($row) => $row->quantity)
                ->addColumn('actions', function ($stock) {
                    $user = auth()->user();
                    if ($user) {
                        $editUrl = route('admin.stock.edit', $stock->id);
                        $editBtn = '
                            <a class=" p-2" href="' . $editUrl . '">
                                <i data-feather="edit" class="feather-edit"></i>
                            </a>
                        ';
                        $deleteBtn = '
                            <a href="javascript:void(0);" class="delete-stock confirm-text p-2" data-id="' . $stock->id . '">
                                <i data-feather="trash-2" class="feather-trash-2"></i>
                            </a>
                        ';

                        // Admin: show both edit and delete
                        return '<div class="edit-delete-action">' . $editBtn . $deleteBtn . '</div>';
                    }
                    return '';
                })
                ->rawColumns(['product', 'actions'])
                ->make(true);
        }

        return view('storemanager.stock-list', compact('categories', 'units'));
    }

    public function stockByProduct(Request $request)
    {
        if ($request->ajax()) {
            return $this->stockByProductData($request);
        }

        $categories = Category::orderBy('name')->get();
        $stores = Store::orderBy('name')->get();

        return view('admin.stock-by-product', [
            'categories' => $categories,
            'stores' => $stores,
            'datatableConfig' => app(DatatablePaginationService::class)->config('stock-by-product', [
                'fixedHeader' => false,
            ]),
        ]);
    }

    protected function resolveStockListStores(Request $request)
    {
        $user = auth()->user();
        $sessionStoreId = session('selected_store_id');
        $storesQuery = Store::query()->orderBy('name');

        if ($user && in_array((int) $user->role_id, [1, 2, 3, 5], true)) {
            if ($request->filled('store')) {
                if ($request->store === 'all') {
                    // show all stores as columns
                } elseif ($request->store !== 'choose') {
                    $storesQuery->where('id', $request->store);
                } elseif ($sessionStoreId) {
                    $storesQuery->where('id', $sessionStoreId);
                }
            } elseif ($sessionStoreId) {
                $storesQuery->where('id', $sessionStoreId);
            }
        } elseif ($sessionStoreId) {
            $storesQuery->where('id', $sessionStoreId);
        }

        return $storesQuery->get(['id', 'name']);
    }

    protected function stockByProductData(Request $request): JsonResponse
    {
        $stores = $this->resolveStockListStores($request);

        // Keep every product, even when the selected store has no stock row for it.
        $stockQuery = Stock::query()->with(['category', 'unit']);

        if ($request->filled('category') && $request->category !== 'choose') {
            $stockQuery->where('category_id', $request->category);
        }

        $search = trim((string) $request->input('search.value', ''));
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

            foreach ($stores as $store) {
                $stock = $items->firstWhere('store_id', $store->id);
                if ($stock) {
                    $qty = (int) $stock->quantity;
                    $storeData[(string) $store->id] = [
                        'qty' => $qty,
                        'price' => (float) $stock->price,
                    ];
                    $combinedQty += $qty;
                } else {
                    $storeData[(string) $store->id] = [
                        'qty' => 0,
                        'price' => null,
                    ];
                }
            }

            $rows[] = [
                'product_id' => $productId,
                'product_name' => $first->product_name,
                'sku' => $first->sku,
                'category' => $first->category->name ?? '',
                'unit' => $first->unit->name ?? '',
                'combined_qty' => $combinedQty,
                'stores' => $storeData,
            ];
        }

        $orderColumn = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $sortKeys = ['product_name', 'sku', 'category', 'unit', 'combined_qty'];
        $sortKey = $sortKeys[$orderColumn] ?? 'product_name';

        usort($rows, function ($a, $b) use ($sortKey, $orderDir) {
            if ($sortKey === 'combined_qty') {
                $cmp = ($a['combined_qty'] ?? 0) <=> ($b['combined_qty'] ?? 0);
            } else {
                $cmp = strnatcasecmp((string) ($a[$sortKey] ?? ''), (string) ($b[$sortKey] ?? ''));
            }

            return $orderDir === 'desc' ? -$cmp : $cmp;
        });

        $total = count($rows);
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 25);
        $pageRows = $length < 0
            ? $rows
            : array_slice($rows, $start, $length > 0 ? $length : $total);

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'stores' => $stores->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values(),
            'data' => array_values($pageRows),
        ]);
    }

    private function normalizeStoreMarkups(array $markups): array
    {
        $normalized = [];
        foreach ($markups as $storeId => $markup) {
            if ($markup === null || $markup === '') {
                continue;
            }
            $normalized[(int) $storeId] = round((float) $markup, 2);
        }

        return $normalized;
    }

    private function sellingPriceForStore(float $cost, array $markups, int $storeId): float
    {
        $markup = $markups[$storeId] ?? 0.0;

        return ProductStoreMarkup::priceFromCost($cost, $markup);
    }

    private function syncProductStoreMarkups(string $productId, array $markups): void
    {
        ProductStoreMarkup::syncForProduct($productId, $markups);
    }
}

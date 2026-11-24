<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyOperationsController extends Controller
{
    


    public function index(Request $request)
    {
        $sessionStoreId = session('selected_store_id');
        $sort = $request->get('sort', 'newest');

        // DataTables may omit X-Requested-With on some proxies; `draw` is always present server-side.
        // if ($request->ajax()) {
        if ($request->ajax() || $request->has('draw')) {
            $filterStoreId = $request->filled('store_id') ? (int) $request->input('store_id') : null;
            $storeFrom = $request->input('store_from');
            $storeTo = $request->input('store_to');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $sort = $request->get('sort', 'newest');
            $since = now()->subDay();

            // Old: whereHas on stores — extra subqueries. stock_transfers.store_from / store_to are FKs to stores.id.
            // $query->where(function ($q) use ($storeId) {
            //     $q->whereHas('storeFrom', fn ($q) => $q->where('id', $storeId))
            //         ->orWhereHas('storeTo', fn ($q) => $q->where('id', $storeId));
            // });

            // Old: wrong eager-load key — items link by reference_no, not stock_transfer_id.
            // 'items:id,stock_transfer_id,product_name,qty'

            $query = StockTransfer::query()
                ->select(['id', 'store_from', 'store_to', 'reference_no', 'created_at'])
                ->with([
                    'storeFrom:id,name',
                    'storeTo:id,name',
                    'items:id,reference_no,product_name,qty',
                ])
                ->when($filterStoreId, function ($q) use ($filterStoreId) {
                    $q->where(function ($q) use ($filterStoreId) {
                        $q->where('store_from', $filterStoreId)
                            ->orWhere('store_to', $filterStoreId);
                    });
                })
                ->when($storeFrom && $storeFrom !== 'choose', function ($q) use ($storeFrom) {
                    $q->where('store_from', (int) $storeFrom);
                })
                ->when($storeTo && $storeTo !== 'choose', function ($q) use ($storeTo) {
                    $q->where('store_to', (int) $storeTo);
                });

            if (!empty($dateFrom) && !empty($dateTo)) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            } elseif (!empty($dateFrom)) {
                $query->whereDate('created_at', '>=', Carbon::parse($dateFrom)->toDateString());
            } elseif (!empty($dateTo)) {
                $query->whereDate('created_at', '<=', Carbon::parse($dateTo)->toDateString());
            } else {
                $query->where('created_at', '>=', $since);
            }

            if ($request->filled('order_type') && in_array($request->order_type, ['asc', 'desc'], true)) {
                $query->orderBy('created_at', $request->order_type);
            } else {
                $query->orderBy('created_at', $sort === 'newest' ? 'desc' : 'asc');
            }

            $searchRaw = $request->input('search.value');
            $searchLike = ($searchRaw !== null && $searchRaw !== '')
                ? '%' . addcslashes((string) $searchRaw, '%_\\') . '%'
                : null;

            return datatables()
                ->eloquent($query)
                ->addColumn('store_from', fn ($row) => $row->storeFrom->name ?? '-')
                ->addColumn('store_to', fn ($row) => $row->storeTo->name ?? '-')
                ->addColumn('item_name', fn ($row) => $row->items->pluck('product_name')->filter()->implode(', '))
                ->addColumn('item_qty', fn ($row) => $row->items->pluck('qty')->implode(', '))
                ->filter(function ($query) use ($searchLike) {
                    if ($searchLike === null) {
                        return;
                    }
                    $query->where(function ($q) use ($searchLike) {
                        $q->whereHas('storeFrom', fn ($q) => $q->where('name', 'like', $searchLike))
                            ->orWhereHas('storeTo', fn ($q) => $q->where('name', 'like', $searchLike))
                            ->orWhereHas('items', function ($q) use ($searchLike) {
                                // Old code (removed): include SKU from stock_transfer_items.
                                // $q->where('product_name', 'like', $searchLike)->orWhere('sku', 'like', $searchLike);
                                $q->where('product_name', 'like', $searchLike);
                            });
                    });
                })
                ->make(true);
        }

        $categoryData = Category::query()
            ->select(['id', 'name'])
            ->withCount([
                'stocks as product_count' => function ($query) use ($sessionStoreId) {
                    $query
                        ->when($sessionStoreId, fn ($q) => $q->where('store_id', $sessionStoreId))
                        ->where('quantity', '>', 0);
                },
            ])
            ->get()
            ->map(fn ($category) => [
                'name' => $category->name,
                'count' => $category->product_count,
            ]);

        $stores = Store::where('status', 1)->select('id', 'name')->get();
        return view('SuperAdmin.daily-operations-overview', compact('categoryData', 'stores'));
    }

    public function dailyoperations(Request $request)
    {
        $sessionStoreId = session('selected_store_id');

        // if ($request->ajax()) {
        if ($request->ajax() || $request->has('draw')) {
            $storeFrom = $request->input('store_from');
            $storeTo = $request->input('store_to');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');

            $query = StockTransfer::query()
                ->select(['id', 'store_from', 'store_to', 'reference_no', 'created_at'])
                ->with([
                    'storeFrom:id,name',
                    'storeTo:id,name',
                    // Old code used stock_transfer_id; relationship is keyed by reference_no.
                    // 'items:id,stock_transfer_id,product_name,qty'
                    'items:id,reference_no,product_name,qty',
                ])
                ->when($sessionStoreId, function ($q) use ($sessionStoreId) {
                    $q->where(function ($q) use ($sessionStoreId) {
                        // Old code:
                        // $q->where('store_from_id', $sessionStoreId)->orWhere('store_to_id', $sessionStoreId);
                        $q->where('store_from', $sessionStoreId)
                            ->orWhere('store_to', $sessionStoreId);
                    });
                })
                ->when($storeFrom && $storeFrom !== 'choose', function ($q) use ($storeFrom) {
                    $q->where('store_from', (int) $storeFrom);
                })
                ->when($storeTo && $storeTo !== 'choose', function ($q) use ($storeTo) {
                    $q->where('store_to', (int) $storeTo);
                });

            if (!empty($dateFrom) && !empty($dateTo)) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            } elseif (!empty($dateFrom)) {
                $query->whereDate('created_at', '>=', Carbon::parse($dateFrom)->toDateString());
            } elseif (!empty($dateTo)) {
                $query->whereDate('created_at', '<=', Carbon::parse($dateTo)->toDateString());
            } else {
                // Keep old default when date filters are not provided.
                $query->where('created_at', '>=', now()->subDay());
            }

            // Apply ordering (custom OR default)
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            return datatables()
                ->eloquent($query) // ✅ IMPORTANT for server-side
                ->addColumn('store_from', function ($row) {
                    return $row->storeFrom->name ?? '-';
                })
                ->addColumn('store_to', function ($row) {
                    return $row->storeTo->name ?? '-';
                })
                ->addColumn('item_name', function ($row) {
                    return $row->items->pluck('product_name')->implode(', ');
                })
                ->addColumn('item_qty', function ($row) {
                    return $row->items->pluck('qty')->implode(', ');
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->input('search.value')) {
                        $query->where(function ($q) use ($search) {
                            $q->whereHas('storeFrom', fn($q) => $q->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('storeTo', fn($q) => $q->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('items', function ($q) use ($search) {
                                // Old code (removed): include SKU from stock_transfer_items.
                                // $q->where('product_name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%");
                                $q->where('product_name', 'like', "%{$search}%");
                            });
                        });
                    }
                })
                ->make(true);
        }

        // Category Data (optimized)
        $categoryData = Category::query()
            ->select('id', 'name')
            ->withCount([
                'stocks as product_count' => function ($query) use ($sessionStoreId) {
                    $query->where('store_id', $sessionStoreId)
                        ->where('quantity', '>', 0);
                }
            ])
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'count' => $category->product_count,
                ];
            });

        $stores = Store::where('status', 1)->select('id', 'name')->get();

        return view('admin.daily-operations-overview', compact('categoryData', 'stores'));
    }


    public function dailySalesOverview(Request $request)
    {
        $storeId = session('selected_store_id') ? (int) session('selected_store_id') : null;
        $since = now()->subDay();
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // DataTables + some proxies omit X-Requested-With; `draw` is always sent server-side.
        // if ($request->ajax()) {
        if ($request->ajax() || $request->has('draw')) {
            // Old: eager-loaded `order` and `stock` but DataTable columns only use `order_items` columns.
            // $salesData = OrderItem::with(['order', 'stock' => ...])->whereHas('order', ...);

            $query = OrderItem::query()
                ->select([
                    'order_items.id',
                    'order_items.order_id',
                    'order_items.product_id',
                    'order_items.sku',
                    'order_items.product_name',
                    'order_items.price',
                    'order_items.qty',
                    'order_items.created_at',
                ])
                ->join('orders', 'orders.id', '=', 'order_items.order_id');

            if (!empty($dateFrom) && !empty($dateTo)) {
                $query->whereBetween('order_items.created_at', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            } elseif (!empty($dateFrom)) {
                $query->whereDate('order_items.created_at', '>=', Carbon::parse($dateFrom)->toDateString());
            } elseif (!empty($dateTo)) {
                $query->whereDate('order_items.created_at', '<=', Carbon::parse($dateTo)->toDateString());
            } else {
                // Old behavior if date filters are not selected.
                $query->where('order_items.created_at', '>=', $since);
            }

            if ($storeId) {
                $query->where('orders.store_id', $storeId);
            } else {
                $query->whereRaw('0 = 1');
            }

            if ($request->filled('order_type') && in_array($request->order_type, ['asc', 'desc'], true)) {
                $query->orderBy('order_items.created_at', $request->order_type);
            } else {
                $query->orderBy('order_items.created_at', 'desc');
            }

            return datatables()
                ->eloquent($query)
                ->filter(function ($query) use ($request) {
                    $search = $request->input('search.value');
                    if ($search === null || $search === '') {
                        return;
                    }

                    $searchLike = '%' . addcslashes((string) $search, '%_\\') . '%';
                    // Day Sales Overview search targets order_items (SKU + product name).
                    $query->where(function ($q) use ($searchLike) {
                        $q->where('order_items.sku', 'like', $searchLike)
                            ->orWhere('order_items.product_name', 'like', $searchLike);
                    });
                })
                ->addColumn('sku', fn ($row) => $row->sku ?: '-')
                ->addColumn('product_name', fn ($row) => $row->product_name ?: '-')
                ->addColumn('price', function ($row) {
                    return $row->price === null
                        ? '-'
                        : '$' . number_format((float) $row->price, 2, '.', ',');
                })
                ->addColumn('qty', fn ($row) => $row->qty ?? '-')
                ->addColumn('totalPrice', function ($row) {
                    if ($row->price === null || $row->qty === null) {
                        return '-';
                    }

                    return '$' . number_format((float) $row->qty * (float) $row->price, 2, '.', ',');
                })
                ->make(true);
        }

        return response()->json(['message' => 'Invalid request'], 400);
    }


    //Commentd on 14-04-2026
/*
    public function index(Request $request)
    {
        $storeId = session('selected_store_id');
        //dd($storeId);
        $sort = $request->get('sort', 'newest');
        if ($request->ajax()) {
            $storeId = $request->store_id;
            $sort = $request->sort ?? 'newest';

            $query = StockTransfer::with('storeFrom', 'storeTo', 'items');

            if ($storeId) {
                $query->where(function ($q) use ($storeId) {
                    $q
                        ->whereHas('storeFrom', fn($q) => $q->where('id', $storeId))
                        ->orWhereHas('storeTo', fn($q) => $q->where('id', $storeId));
                });
            }
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            }
            // Filter for records created within the last 24 hours
            $query->where('created_at', '>=', Carbon::now()->subDay());

            $query->orderBy('created_at', $sort === 'newest' ? 'desc' : 'asc');
            $orderItems = $query->get();
            return datatables()
                ->of($orderItems)
                ->addColumn('store_from', fn($row) => $row->storeFrom->name ?? '-')
                ->addColumn('store_to', fn($row) => $row->storeTo->name ?? '-')
                ->addColumn('item_name', fn($row) => $row->items->pluck('product_name')->implode(', '))
                ->addColumn('item_qty', fn($row) => $row->items->pluck('qty')->implode(', '))
                ->make(true);
        }

        $categoryData = Category::withCount(['stocks as product_count' => function ($query) use ($storeId) {
            $query
                ->where('store_id', $storeId)
                ->where('quantity', '>', 0);  // Exclude stocks with 0 quantity
        }])
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'count' => $category->product_count,  // Use the alias
                ];
            });

        return view('SuperAdmin.daily-operations-overview', compact('categoryData'));
    }


    // public function dailyoperations(Request $request)
    // {
    //     $storeId = session('selected_store_id');
    //     $sort = $request->get('sort', 'newest');
    //     if ($request->ajax()) {
    //         $storeId = $request->store_id;
    //         $sort = $request->sort ?? 'newest';

    //         $query = StockTransfer::with('storeFrom', 'storeTo', 'items');

    //         if ($storeId) {
    //             $query->where(function ($q) use ($storeId) {
    //                 $q
    //                     ->whereHas('storeFrom', fn($q) => $q->where('id', $storeId))
    //                     ->orWhereHas('storeTo', fn($q) => $q->where('id', $storeId));
    //             });
    //         }
    //         if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
    //             $query->orderBy('created_at', $request->order_type);
    //         }
    //         // Filter for records created within the last 24 hours
    //         $query->where('created_at', '>=', Carbon::now()->subDay());

    //         $query->orderBy('created_at', $sort === 'newest' ? 'desc' : 'asc');
    //         $orderItems = $query->get();
    //         return datatables()
    //             ->of($orderItems)
    //             ->addColumn('store_from', fn($row) => $row->storeFrom->name ?? '-')
    //             ->addColumn('store_to', fn($row) => $row->storeTo->name ?? '-')
    //             ->addColumn('item_name', fn($row) => $row->items->pluck('product_name')->implode(', '))
    //             ->addColumn('item_qty', fn($row) => $row->items->pluck('qty')->implode(', '))
    //             ->make(true);
    //     }

    //     $categoryData = Category::withCount(['stocks as product_count' => function ($query) use ($storeId) {
    //         $query
    //             ->where('store_id', $storeId)
    //             ->where('quantity', '>', 0);  // Exclude stocks with 0 quantity
    //     }])
    //         ->get()
    //         ->map(function ($category) {
    //             return [
    //                 'name' => $category->name,
    //                 'count' => $category->product_count,  // Use the alias
    //             ];
    //         });

    //     return view('admin.daily-operations-overview', compact('categoryData'));
    // }

    

    public function dailySalesOverview(Request $request)
    {
        $storeId = session('selected_store_id');

        if ($request->ajax()) {
            $salesData = OrderItem::with(['order',
                    'stock' => function ($query) use ($storeId) {
                        $query->where('store_id', $storeId);
                    }])
                ->whereHas('order', function ($query) use ($storeId) {
                    $query->where('store_id', $storeId);
                });
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $salesData->orderBy('created_at', $request->order_type);
            }
            $salesData->where('created_at', '>=', Carbon::now()->subDay());
            return datatables()
                ->eloquent($salesData)
                ->addColumn('sku', fn($row) => $row->sku ?? '-')
                ->addColumn('product_name', fn($row) => $row->product_name ?? '-')
                ->addColumn('price', fn($row) => '$' . number_format($row->price, 2) ?? '-')
                ->addColumn('qty', fn($row) => $row->qty ?? '-')
                ->addColumn('totalPrice', fn($row) => '$' . number_format($row->qty * $row->price, 2) ?? '-')
                ->make(true);
        }
    }
*/

}

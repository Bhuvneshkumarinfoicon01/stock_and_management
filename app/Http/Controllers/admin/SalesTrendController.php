<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesTrendController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $storeId = ($request->store_id && $request->store_id !== 'choose') ? $request->store_id : null;

        if ($request->ajax()) {
            $stocks = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->leftJoin('stocks', function ($join) use ($storeId) {
                    $join->on('order_items.product_id', '=', 'stocks.product_id');
                    if ($storeId) {
                        $join->where('stocks.store_id', '=', $storeId);
                    }
                })
                ->leftJoin('categories', 'order_items.category_id', '=', 'categories.id')
                ->select(
                    'order_items.product_id',
                    'order_items.product_name',
                    'order_items.sku',
                    'categories.name as category',
                    DB::raw('SUM(order_items.qty) as sold_qty'),
                    DB::raw('SUM(order_items.price * order_items.qty) as sold_amount'),
                    DB::raw('COALESCE(stocks.quantity, 0) as instock_qty')
                )
                ->when($storeId, fn($q) => $q->where('orders.store_id', $storeId));

            // Apply filters
            if ($request->has('category_id') && $request->category_id != '') {
                $stocks->where('order_items.category_id', $request->category_id);
            }

            if ($request->has('date_from') && $request->date_from != '') {
                $dateFrom = Carbon::parse($request->date_from)->startOfDay();
                $stocks->where('order_items.created_at', '>=', $dateFrom);
            }

            if ($request->has('date_to') && $request->date_to != '') {
                $dateTo = Carbon::parse($request->date_to)->endOfDay();
                $stocks->where('order_items.created_at', '<=', $dateTo);
            }

            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                // $stocks->orderBy('order_items.created_at', $request->order_type);
                $stocks->selectRaw('MAX(order_items.created_at) as latest_order');
                $stocks->orderBy('latest_order', $request->order_type);
                $stocks->groupBy(
                    'order_items.product_id',
                    'order_items.product_name',
                    'order_items.sku',
                    'categories.name',
                    'stocks.quantity'
                );
            }

            $stocks->groupBy(
                'order_items.product_id',
                'order_items.product_name',
                'order_items.sku',
                'categories.name',
                'stocks.quantity'
            );

            return datatables()
                ->of($stocks)
                ->addColumn('product_name', fn($row) => $row->product_name)
                ->addColumn('sku', fn($row) => $row->sku)
                ->addColumn('category', fn($row) => $row->category)
                ->addColumn('sold_qty', fn($row) => $row->sold_qty)
                ->addColumn('sold_amount', fn($row) => '$' . number_format($row->sold_amount, 2))
                ->addColumn('instock_qty', fn($row) => $row->instock_qty)
                ->make(true);
        }

        return view('admin.sales-trend', compact('categories'));
    }

    public function storemgrSalesReport(Request $request)
    {
        $categories = Category::all();
        $storeId = session('selected_store_id');

        if ($request->ajax()) {
            $storeId = session('selected_store_id');
            $stocks = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->leftJoin('stocks', function ($join) use ($storeId) {
                    $join->on('order_items.product_id', '=', 'stocks.product_id');
                    $join->where('stocks.store_id', '=', $storeId);
                })
                ->leftJoin('categories', 'order_items.category_id', '=', 'categories.id')
                ->select(
                    'order_items.product_id',
                    'order_items.product_name',
                    'order_items.sku',
                    'categories.name as category',
                    DB::raw('SUM(order_items.qty) as sold_qty'),
                    DB::raw('SUM(order_items.price * order_items.qty) as sold_amount'),
                    DB::raw('COALESCE(stocks.quantity, 0) as instock_qty'),
                    DB::raw('MAX(order_items.created_at) as latest_order')
                )
                ->where('orders.store_id', $storeId);

            // Apply filters if they exist
            if ($request->has('category_id') && $request->category_id != '') {
                $stocks->where('order_items.category_id', $request->category_id);
            }

            if ($request->has('date_from') && $request->date_from != '') {
                $dateFrom = Carbon::parse($request->date_from)->startOfDay();  // Start of the day
                $stocks->where('order_items.created_at', '>=', $dateFrom);
            }

            if ($request->has('date_to') && $request->date_to != '') {
                $dateTo = Carbon::parse($request->date_to)->endOfDay();  // End of the day
                $stocks->where('order_items.created_at', '<=', $dateTo);
            }
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $stocks->orderBy('latest_order', $request->order_type);
            }

            // Group and finalize the query
            $stocks->groupBy(
                'order_items.product_id',
                'order_items.product_name',
                'order_items.sku',
                'categories.name',
                'stocks.quantity'
            );

            return datatables()
                ->of($stocks)
                ->addColumn('product_name', fn($row) => $row->product_name)
                ->addColumn('sku', fn($row) => $row->sku)
                ->addColumn('category', fn($row) => $row->category)
                ->addColumn('sold_qty', fn($row) => $row->sold_qty)
                ->addColumn('sold_amount', fn($row) => '$' . number_format($row->sold_amount, 2))
                ->addColumn('instock_qty', fn($row) => $row->instock_qty)
                ->make(true);
        }

        return view('storemanager.sales-trend', compact('categories'));
    }

    public function fetchSalesData(Request $request)
    {
        // Use store_id from the session if not provided in the request
        $storeId = $request->store_id ?? session('selected_store_id');

        // Chart Data (Sales by Month)
        $chart = DB::table('orders')
            ->selectRaw("DATE_FORMAT(created_at, '%b') as month, SUM(total_price) as total")
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->groupByRaw('month')
            ->orderByRaw('MIN(created_at)')
            ->get();

        // Table Data (Product Performance)
        $tableData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('stocks', function ($join) use ($storeId) {
                $join->on('order_items.product_id', '=', 'stocks.product_id');
                $join->where('stocks.store_id', '=', $storeId);
            })
            ->leftJoin('categories', 'order_items.category_id', '=', 'categories.id')
            ->select(
                'order_items.product_id',
                'order_items.product_name',
                'order_items.sku',
                'categories.name as category',
                DB::raw('SUM(order_items.qty) as sold_qty'),
                DB::raw('SUM(order_items.price*order_items.qty) as sold_amount'),
                DB::raw('COALESCE(stocks.quantity, 0) as instock_qty')
            )
            ->when($storeId, fn($q) => $q->where('orders.store_id', $storeId))
            ->groupBy(
                'order_items.product_id',
                'order_items.product_name',
                'order_items.sku',
                'categories.name',
                'stocks.quantity'
            )
            ->get();

        $allMonths = [
            'Jan' => 0,
            'Feb' => 0,
            'Mar' => 0,
            'Apr' => 0,
            'May' => 0,
            'Jun' => 0,
            'Jul' => 0,
            'Aug' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Dec' => 0,
        ];

        // Fill totals from DB
        foreach ($chart as $row) {
            $allMonths[$row->month] = round($row->total / 1000, 2);  // optional division
        }

        return response()->json([
            'tableData' => $tableData ?? [],
            'chartData' => [
                'labels' => array_keys($allMonths),
                'values' => array_values($allMonths),
            ]
        ]);
    }

    public function filter(Request $request)
    {
        // Use store_id from the request or session
        // $storeId = $request->store_id ?? session('selected_store_id');
        $storeId = ($request->store_id && $request->store_id !== 'choose') ? $request->store_id : null;
        $categoryId = $request->category_id;
        $from = $request->date_from ? parse_date_input_to_db($request->date_from) : null;
        $to = $request->date_to ? parse_date_input_to_db($request->date_to) : null;

        // Query for the table data
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('stocks', function ($join) use ($storeId) {
                $join->on('order_items.product_id', '=', 'stocks.product_id');
                if ($storeId) {
                    $join->where('stocks.store_id', '=', $storeId);
                }
            })
            ->leftJoin('categories', 'order_items.category_id', '=', 'categories.id')
            ->select(
                'order_items.product_id',
                'order_items.product_name',
                'order_items.sku',
                'categories.name as category',
                DB::raw('SUM(order_items.qty) as sold_qty'),
                DB::raw('SUM(order_items.price * order_items.qty) as sold_amount'),
                DB::raw('COALESCE(stocks.quantity, 0) as instock_qty')
            )
            ->when($storeId, fn($q) => $q->where('orders.store_id', $storeId))
            ->when($categoryId, fn($q) => $q->where('order_items.category_id', $categoryId))
            ->when($from, fn($q) => $q->whereDate('order_items.created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('order_items.created_at', '<=', $to))
            ->groupBy(
                'order_items.product_id',
                'order_items.product_name',
                'order_items.sku',
                'categories.name',
                'stocks.quantity'
            )
            ->get();

        // Sales chart data with price * qty instead of total_price from orders
        $chart = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->selectRaw("DATE_FORMAT(order_items.created_at, '%b') as month, SUM(order_items.price * order_items.qty) as total")
            ->when($storeId, fn($q) => $q->where('orders.store_id', $storeId))
            ->when($categoryId, fn($q) => $q->where('order_items.category_id', $categoryId))
            ->when($from, fn($q) => $q->whereDate('order_items.created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('order_items.created_at', '<=', $to))
            ->groupByRaw('month')
            ->orderByRaw('MIN(order_items.created_at)')
            ->get();

        $allMonths = [
            'Jan' => 0,
            'Feb' => 0,
            'Mar' => 0,
            'Apr' => 0,
            'May' => 0,
            'Jun' => 0,
            'Jul' => 0,
            'Aug' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Dec' => 0,
        ];

        // Fill totals from DB
        foreach ($chart as $row) {
            $allMonths[$row->month] = round($row->total / 1000, 2);  // optional division
        }

        return response()->json([
            'tableData' => $query ?? [],
            'chartData' => [
                'labels' => array_keys($allMonths),
                'values' => array_values($allMonths),
            ]
        ]);
    }
}

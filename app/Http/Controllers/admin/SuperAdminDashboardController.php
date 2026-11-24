<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminDashboardController extends Controller
{
    public function superadminDashboard(Request $request)
    {
        $selectedStoreId = session('selected_store_id');

        // Stocks (no year filter)
        $stocks = Stock::when($selectedStoreId, function ($query) use ($selectedStoreId) {
            return $query->where('store_id', $selectedStoreId);
        })->get();

        // Top Stores (no year filter) — exclude estimate orders (status 3)
        $topStores = DB::table('stores')
            ->select('stores.id', 'stores.name', DB::raw('SUM(order_items.price * order_items.qty) as total_sales'))
            ->join('orders', 'stores.id', '=', 'orders.store_id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '<>', 3)
            ->groupBy('stores.id', 'stores.name')
            ->orderByDesc('total_sales')
            ->take(5)
            ->get();

        // High Demand Products (no year filter)
        $highDemandProductsRaw = DB::table('order_items')
            ->select('product_id', 'product_name', 'price', DB::raw('SUM(qty) as total_sold'))
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '<>', 3)
            ->when($selectedStoreId, function ($query) use ($selectedStoreId) {
                return $query->where('orders.store_id', $selectedStoreId);
            })
            ->groupBy('product_id', 'product_name', 'price')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $highDemandProducts = $highDemandProductsRaw->map(function ($product) use ($selectedStoreId) {
            $product->stocks = Stock::where('product_id', $product->product_id)
                ->when($selectedStoreId, function ($query) use ($selectedStoreId) {
                    return $query->where('store_id', $selectedStoreId);
                })
                ->get();
            return $product;
        });

        // Low Stock (no year filter)
        $alertQty = setting('qty_alert');
        $lowStockProducts = Stock::where('quantity', '<=', $alertQty)
            ->when($selectedStoreId, function ($query) use ($selectedStoreId) {
                return $query->where('store_id', $selectedStoreId);
            })
            ->orderBy('quantity')
            ->take(5)
            ->get();

        $lowStockProductsCount = Stock::where('quantity', '<=', $alertQty)
            ->when($selectedStoreId, function ($query) use ($selectedStoreId) {
                return $query->where('store_id', $selectedStoreId);
            })
            ->count();
        // Year filters for charts only
        $availableYears = DB::table('orders')
            ->when($selectedStoreId, function ($query) use ($selectedStoreId) {
                return $query->where('store_id', $selectedStoreId);
            })
            ->selectRaw('YEAR(created_at) as year')
            ->groupBy('year')
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();

        $defaultYear = $request->input('year', $availableYears[0] ?? now()->year);

        // Monthly Sales (line totals; excludes estimate orders)
        $monthlySales = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '<>', 3)
            ->whereYear('orders.created_at', $defaultYear)
            ->when($selectedStoreId, function ($query) use ($selectedStoreId) {
                return $query->where('orders.store_id', $selectedStoreId);
            })
            ->selectRaw('MONTH(orders.created_at) as month, SUM(order_items.price * order_items.qty) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $salesByMonth = [];
        for ($i = 1; $i <= 12; $i++) {
            $salesByMonth[] = $monthlySales[$i] ?? 0;
        }

        // Total Sales (same basis as monthly chart; excludes estimates)
        $rawSales = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '<>', 3)
            ->whereYear('orders.created_at', $defaultYear)
            ->when($selectedStoreId, function ($query) use ($selectedStoreId) {
                return $query->where('orders.store_id', $selectedStoreId);
            })
            ->sum(DB::raw('order_items.price * order_items.qty'));

        $totalSales = round((float) $rawSales, 2);

        // Total Stores (no year filter)
        $totalStores = DB::table('stores')->count();

        // Bar chart: line sales vs stock adjustment value (not formal accounting P&L)
        $monthlyProfit = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->selectRaw('MONTH(orders.created_at) as month, SUM(order_items.price * order_items.qty) as profit')
            ->where('orders.status', '<>', 3)
            ->whereYear('orders.created_at', $defaultYear)
            ->when($selectedStoreId, function ($query) use ($selectedStoreId) {
                return $query->where('orders.store_id', $selectedStoreId);
            })
            ->groupBy('month')
            ->pluck('profit', 'month')
            ->toArray();

        // Loss Chart
        $monthlyLoss = DB::table('stock_adjustments as sa')
            ->join('stocks as s', function ($join) {
                $join
                    ->on('sa.product_id', '=', 's.product_id')
                    ->on('sa.store_id', '=', 's.store_id');
            })
            ->selectRaw('MONTH(sa.adjust_date) as month, SUM(s.price * sa.adjust_quantity) as loss')
            ->whereYear('sa.adjust_date', $defaultYear)
            ->when($selectedStoreId, function ($query) use ($selectedStoreId) {
                return $query->where('sa.store_id', $selectedStoreId);
            })
            ->where('sa.adjustment_type', 'remove')
            ->groupBy('month')
            ->pluck('loss', 'month')
            ->toArray();

        $profitByMonth = [];
        $lossByMonth = [];
        for ($i = 1; $i <= 12; $i++) {
            $profitByMonth[] = round(($monthlyProfit[$i] ?? 0) / 1000, 2);
            $lossByMonth[] = round(($monthlyLoss[$i] ?? 0) / 1000, 2);
        }

        /*
        $revenue = DB::table('orders')->where('orders.store_id', $selectedStoreId)->selectRaw('SUM(...)')->first();
        */
        $rawRevenue = DB::table('orders')
            ->whereYear('created_at', $defaultYear)
            ->where('status', '<>', 3)
            ->when($selectedStoreId, function ($q) use ($selectedStoreId) {
                return $q->where('store_id', $selectedStoreId);
            })
            ->sum('total_price');
        $revenueAmount = round((float) $rawRevenue, 2);

        $monthlyRevenue = DB::table('orders')
            ->selectRaw('MONTH(created_at) as month, SUM(total_price) as revenue')
            ->whereYear('created_at', $defaultYear)
            ->where('status', '<>', 3)
            ->when($selectedStoreId, function ($q) use ($selectedStoreId) {
                return $q->where('store_id', $selectedStoreId);
            })
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('revenue', 'month')
            ->toArray();

        // Fill in all 12 months with zero if missing
        $monthlyRevenueData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyRevenueData[] = round(($monthlyRevenue[$m] ?? 0) / 1000, 1);  // In K
        }
        return view('SuperAdmin.index', compact(
            'topStores',
            'highDemandProducts',
            'lowStockProducts',
            'stocks',
            'availableYears',
            'defaultYear',
            'salesByMonth',
            'totalSales',
            'totalStores',
            'profitByMonth',
            'lossByMonth',
            'revenueAmount',
            'lowStockProductsCount',
            'monthlyRevenueData'
        ));
    }

    public function lowStockProducts()
    {
        $storeId = session('selected_store_id');
        $alertQty = setting('qty_alert');
        $lowStock = Stock::where('store_id', $storeId)
            ->where('quantity', '<=', $alertQty)
            ->orderBy('quantity')
            ->paginate(10);

        return view('admin.viewLowStockProduct', compact('lowStock'));
    }
}

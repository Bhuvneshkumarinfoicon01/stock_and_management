<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreManagerDashboardController extends Controller
{
    public function storemanagerDashboard(Request $request)
    {
        $users = auth()->user();
        $storeId = session('selected_store_id');
        $currentDate = now();
        $previousMonth = $currentDate->copy()->subMonth();
        $twoMonthsAgo = $previousMonth->copy()->subMonth();

        // Revenue for previous month
        $previousMonthEarning = DB::table('orders')
            ->where('store_id', $storeId)
            ->whereYear('created_at', $previousMonth->year)
            ->whereMonth('created_at', $previousMonth->month)
            ->selectRaw('
            SUM(
                subtotal_price 
                - (subtotal_price * discount / 100) 
                + (subtotal_price * tax / 100) 
                + (subtotal_price * shipping / 100)
            ) as revenue
        ')
            ->value('revenue') ?? 0;

        $twoMonthsAgoEarning = DB::table('orders')
            ->where('store_id', $storeId)
            ->whereYear('created_at', $twoMonthsAgo->year)
            ->whereMonth('created_at', $twoMonthsAgo->month)
            ->selectRaw('
            SUM(
                subtotal_price 
                - (subtotal_price * discount / 100) 
                + (subtotal_price * tax / 100) 
                + (subtotal_price * shipping / 100)
            ) as revenue
        ')
            ->value('revenue') ?? 0;

        $monthlyEarning = round($previousMonthEarning, 2);

        $percentageChange = $twoMonthsAgoEarning > 0
            ? round((($previousMonthEarning - $twoMonthsAgoEarning) / $twoMonthsAgoEarning) * 100, 2)
            : null;

        $countOrder = DB::table('orders')
            ->where('store_id', $storeId)
            ->count();

        $availableYears = DB::table('orders')
            ->where('store_id', $storeId)
            ->selectRaw('YEAR(created_at) as year')
            ->groupBy('year')
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
        $selectedYear = $request->input('year', $availableYears[0] ?? now()->year);
        $monthlyRevenue = DB::table('orders')
            ->selectRaw('
            MONTH(created_at) as month,
            SUM(
                subtotal_price 
                - (subtotal_price * discount / 100) 
                + (subtotal_price * tax / 100) 
                + (subtotal_price * shipping / 100)
            ) as revenue
        ')
            ->whereYear('created_at', $selectedYear)
            ->where('store_id', $storeId)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('revenue', 'month')
            ->toArray();

        $monthlyRevenueData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyRevenueData[] = round(($monthlyRevenue[$m] ?? 0) / 1000, 1);
        }
        $bestSellers = DB::table('order_items')
            ->select(
                'order_items.product_name',
                DB::raw('SUM(order_items.qty) as total_sold'),
                DB::raw('SUM(order_items.qty * order_items.price) as total_revenue'),
                'stocks.sku',
                'stocks.quantity as stock_quantity',
                'stocks.images',
                'stocks.price'
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('stocks', function ($join) use ($storeId) {
                $join
                    ->on('order_items.product_id', '=', 'stocks.product_id')
                    ->where('stocks.store_id', '=', $storeId);  // ensure correct store
            })
            ->where('orders.store_id', $storeId)
            ->whereYear('orders.created_at', now()->year)
            ->groupBy('order_items.product_name', 'stocks.sku', 'stocks.quantity', 'stocks.images', 'stocks.price')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // /RECENT TRANSACTIONS
        $transactions = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('stocks', 'order_items.product_id', '=', 'stocks.product_id')
            ->select(
                'orders.id as order_id',
                DB::raw('GROUP_CONCAT(DISTINCT stocks.product_name SEPARATOR ", ") as product_name'),
                DB::raw('SUM(order_items.qty) as qty'),
                'orders.payment_method',
                'orders.status',
                'orders.total_price',
                'orders.created_at',
                DB::raw('MAX(stocks.images) as images')  // pick one image if needed
            )
            ->where('orders.store_id', $storeId)
            ->groupBy('orders.id', 'orders.payment_method', 'orders.status', 'orders.total_price', 'orders.created_at')
            ->orderBy('orders.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('storemanager.index', compact(
            'users',
            'monthlyEarning',
            'percentageChange',
            'countOrder',
            'availableYears',
            'selectedYear',
            'monthlyRevenueData',
            'bestSellers',
            'transactions'
        ));
    }
}

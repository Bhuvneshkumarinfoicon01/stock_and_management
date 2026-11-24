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

class PerformingProductController extends Controller
{
    public function index()
    {
        $storeId = session('selected_store_id');

        $availableYears = \App\Models\Order::where('store_id', $storeId)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');
        // Top Performing Products
        $topPerformingProducts = OrderItem::with(['order', 'stock'])
            ->whereHas('order', fn($q) => $q->where('store_id', $storeId))
            ->select('product_id', 'product_name', 'sku')
            ->selectRaw('SUM(qty) as sold_qty')
            ->selectRaw('SUM(price * qty) as sold_amount')
            ->groupBy('product_id', 'product_name', 'sku')
            ->orderBy('sold_qty', 'desc')
            ->limit(5)
            ->get();

        // Underperforming Products
        $underPerformingProducts = OrderItem::with(['order', 'stock'])
            ->whereHas('order', fn($q) => $q->where('store_id', $storeId))
            ->select('product_id', 'product_name', 'sku')
            ->selectRaw('SUM(qty) as sold_qty')
            ->selectRaw('SUM(price * qty) as sold_amount')
            ->groupBy('product_id', 'product_name', 'sku')
            ->orderBy('sold_qty', 'asc')
            ->limit(5)
            ->get();

        // Top Categories
        $topCategories = \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('categories', 'order_items.category_id', '=', 'categories.id')
            ->where('orders.store_id', $storeId)
            ->select('categories.name')
            ->selectRaw('SUM(order_items.qty * order_items.price) as total_sales')
            ->groupBy('categories.name')
            ->orderBy('total_sales', 'desc')
            ->limit(4)
            ->get();
        $categoryLabels = $topCategories->pluck('name');
        $categorySales = $topCategories->pluck('total_sales');

        // Underperforming Categories
        $underCategories = \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('categories', 'order_items.category_id', '=', 'categories.id')
            ->where('orders.store_id', $storeId)
            ->select('categories.name')
            ->selectRaw('SUM(order_items.qty * order_items.price) as total_sales')
            ->groupBy('categories.name')
            ->orderBy('total_sales', 'asc')
            ->limit(4)
            ->get();
        $underCategoryLabels = $underCategories->pluck('name');
        $underCategorySales = $underCategories->pluck('total_sales');

        return view('admin.top-performing-product', compact('topPerformingProducts', 'underPerformingProducts', 'categoryLabels', 'categorySales', 'underCategoryLabels', 'underCategorySales', 'availableYears'));
    }

    // Controller method for fetching top-performing products data based on the year

    public function getTopProductsData(Request $request)
    {
        $year = $request->year;  // Get the year from the request
        $storeId = session('selected_store_id');  // Get selected store ID from session

        // Fetch Top Performing Categories based on year and store_id
        $topCategories = \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('categories', 'order_items.category_id', '=', 'categories.id')
            ->where('orders.store_id', $storeId)  // Filter by store
            ->whereYear('orders.created_at', $year)  // Filter by year
            ->select('categories.name')
            ->selectRaw('SUM(order_items.qty * order_items.price) as total_sales')
            ->groupBy('categories.name')
            ->orderBy('total_sales', 'desc')
            ->limit(4)
            ->get();

        // Return category labels and sales data
        return response()->json([
            'labels' => $topCategories->pluck('name'),
            'series' => $topCategories->pluck('total_sales')
        ]);
    }

    // Method to fetch Underperforming Categories data based on the selected year
    public function getUnderProductsData(Request $request)
    {
        $year = $request->year;  // Get the year from the request
        $storeId = session('selected_store_id');  // Get selected store ID from session

        // Fetch Underperforming Categories based on year and store_id
        $underCategories = \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('categories', 'order_items.category_id', '=', 'categories.id')
            ->where('orders.store_id', $storeId)  // Filter by store
            ->whereYear('orders.created_at', $year)  // Filter by year
            ->select('categories.name')
            ->selectRaw('SUM(order_items.qty * order_items.price) as total_sales')
            ->groupBy('categories.name')
            ->orderBy('total_sales', 'asc')
            ->limit(4)
            ->get();

        // Return under category labels and sales data
        return response()->json([
            'labels' => $underCategories->pluck('name'),
            'series' => $underCategories->pluck('total_sales')
        ]);
    }

    public function topViewAll()
    {
        $storeId = session('selected_store_id');

        $viewAllTopPerformingProducts = OrderItem::with(['order', 'stock'])
            ->whereHas('order', fn($q) => $q->where('store_id', $storeId))
            ->select('product_id', 'product_name', 'sku')
            ->selectRaw('SUM(qty) as sold_qty')
            ->selectRaw('SUM(price * qty) as sold_amount')
            ->groupBy('product_id', 'product_name', 'sku')
            ->orderBy('sold_qty', 'desc')
            ->paginate(10);

        return view('admin.viewTopPerformingProducts', compact('viewAllTopPerformingProducts'));
    }

    public function underViewAll()
    {
        $storeId = session('selected_store_id');

        $viewAllUnderPerformingProducts = OrderItem::with(['order', 'stock'])
            ->whereHas('order', fn($q) => $q->where('store_id', $storeId))
            ->select('product_id', 'product_name', 'sku')
            ->selectRaw('SUM(qty) as sold_qty')
            ->selectRaw('SUM(price * qty) as sold_amount')
            ->groupBy('product_id', 'product_name', 'sku')
            ->orderBy('sold_qty', 'asc')
            ->paginate(10);

        return view('admin.viewUnderPerformingProducts', compact('viewAllUnderPerformingProducts'));
    }
}

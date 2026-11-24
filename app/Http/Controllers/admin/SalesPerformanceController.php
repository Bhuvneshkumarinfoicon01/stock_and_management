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

class SalesPerformanceController extends Controller
{
    public function index(Request $request)
    {
        $storeId = session('selected_store_id');
        $categories = Category::all();
        $now = Carbon::now();

        if ($request->ajax()) {
            // $orderItems = OrderItem::with(['stock.category', 'order'])
            //     ->whereHas('order', function ($query) use ($storeId) {
            //         $query->where('store_id', $storeId);
            //     })
            //     ->selectRaw('product_id, sku, SUM(qty) as sold_qty, SUM(price * qty) as sold_amount')
            //     ->groupBy('product_id', 'sku');
            // if ($request->has('category_id') && $request->category_id != '') {
            //     $orderItems->where('order_items.category_id', $request->category_id);
            // }

            // if ($request->has('date_from') && $request->date_from != '') {
            //     $dateFrom = Carbon::parse($request->date_from)->startOfDay();  // Start of the day
            //     $orderItems->where('order_items.created_at', '>=', $dateFrom);
            // }

            // if ($request->has('date_to') && $request->date_to != '') {
            //     $dateTo = Carbon::parse($request->date_to)->endOfDay();  // End of the day
            //     $orderItems->where('order_items.created_at', '<=', $dateTo);
            // }
            // if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
            //     $orderItems->orderBy('order_items.created_at', $request->order_type);
            // }

            $orderItems = OrderItem::with(['stock.category', 'order'])
                ->whereHas('order', function ($query) use ($storeId) {
                    $query->where('store_id', $storeId);
                })
                ->selectRaw('product_id, sku, MAX(created_at) as latest_created_at, SUM(qty) as sold_qty, SUM(price * qty) as sold_amount')
                ->groupBy('product_id', 'sku');

            if ($request->has('category_id') && $request->category_id != '') {
                $orderItems->where('order_items.category_id', $request->category_id);
            }

            if ($request->has('date_from') && $request->date_from != '') {
                $dateFrom = Carbon::parse($request->date_from)->startOfDay();
                $orderItems->where('order_items.created_at', '>=', $dateFrom);
            }

            if ($request->has('date_to') && $request->date_to != '') {
                $dateTo = Carbon::parse($request->date_to)->endOfDay();
                $orderItems->where('order_items.created_at', '<=', $dateTo);
            }

            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $orderItems->orderBy('latest_created_at', $request->order_type);
            }

            return datatables()
                ->of($orderItems)
                ->addColumn('product_info', function ($row) {
                    $imagePath = $row->stock && $row->stock->images
                        ? asset('storage/' . $row->stock->images)
                        : asset('admin/assets/img/avatar/default-product.png');

                    $productName = $row->stock->product_name ?? '-';

                    return '
                        <div class="productimgname d-flex align-items-center">
                            <div class="view-product me-2">
                                <a href="javascript:void(0);">
                                    <img src="' . $imagePath . '" alt="product" width="40">
                                </a>
                            </div>
                            <a href="javascript:void(0);">' . htmlspecialchars($productName) . '</a>
                        </div>
                    ';
                })
                ->addColumn('sku', fn($row) => $row->sku)
                ->addColumn('category', fn($row) => $row->stock->category->name ?? '-')
                ->addColumn('sold_qty', fn($row) => $row->sold_qty)
                ->addColumn('sold_amount', fn($row) => $row->sold_amount)
                ->addColumn('quantity', fn($row) => $row->stock->quantity ?? 0)
                ->rawColumns(['product_info'])
                ->make(true);
        }

        // Existing OrderItem logic

        $startOfYear = $now->copy()->startOfYear();
        $startOfQuarter = $now->copy()->firstOfQuarter();
        $startOfMonth = $now->copy()->startOfMonth();

        $annualSales = OrderItem::whereHas('order', function ($query) use ($storeId, $startOfYear, $now) {
            $query
                ->where('store_id', $storeId)
                ->whereBetween('created_at', [$startOfYear, $now]);
        })->sum(\DB::raw('price * qty'));

        $quarterlySales = OrderItem::whereHas('order', function ($query) use ($storeId, $startOfQuarter, $now) {
            $query
                ->where('store_id', $storeId)
                ->whereBetween('created_at', [$startOfQuarter, $now]);
        })->sum(\DB::raw('price * qty'));

        $monthlySales = OrderItem::whereHas('order', function ($query) use ($storeId, $startOfMonth, $now) {
            $query
                ->where('store_id', $storeId)
                ->whereBetween('created_at', [$startOfMonth, $now]);
        })->sum(\DB::raw('price * qty'));

        $availableYears = Order::where('store_id', $storeId)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $selectedYear = $request->get('year', now()->year);

        $monthlySalesData = OrderItem::whereHas('order', function ($query) use ($storeId, $selectedYear) {
            $query
                ->where('store_id', $storeId)
                ->whereYear('created_at', $selectedYear);
        })
            ->selectRaw('MONTH(created_at) as month, SUM(price * qty) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Ensure all 12 months are filled
        $monthlySalesArray = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlySalesArray[] = round($monthlySalesData[$i] ?? 0, 2);
        }

        return view('storemanager.sales-performance', compact(
            // 'orderItems',
            'annualSales',
            'quarterlySales',
            'monthlySales',
            'monthlySalesArray',
            'availableYears',
            'selectedYear',
            'categories',
        ));
    }

    // public function getMonthlySalesData(Request $request)
    // {
    //     // Get filter parameters
    //     $productId = $request->input('product_id');
    //     $categoryId = $request->input('category_id');
    //     $fromDate = $request->input('from_date');
    //     $toDate = $request->input('to_date');

    //     // Query to get filtered sales data
    //     $query = OrderItem::with(['stock', 'stock.category'])
    //         ->join('orders', 'order_items.order_id', '=', 'orders.id')
    //         ->where('orders.status', 'completed');  // Adjust this to match your requirements

    //     // Apply filters based on user input
    //     if ($productId) {
    //         $query->where('order_items.product_id', $productId);
    //     }
    //     if ($categoryId) {
    //         $query->whereHas('stock', function ($q) use ($categoryId) {
    //             $q->where('category_id', $categoryId);
    //         });
    //     }
    //     if ($fromDate) {
    //         $query->whereDate('orders.created_at', '>=', $fromDate);
    //     }
    //     if ($toDate) {
    //         $query->whereDate('orders.created_at', '<=', $toDate);
    //     }

    //     // Get the data (for example, monthly sales)
    //     $salesData = $query
    //         ->selectRaw('MONTH(orders.created_at) as month, SUM(order_items.amount) as total_sales')
    //         ->groupBy('month')
    //         ->get();

    //     // Format the data for the chart
    //     $months = [];
    //     $sales = [];
    //     foreach ($salesData as $data) {
    //         $months[] = date('M', mktime(0, 0, 0, $data->month, 1));  // Convert month number to month name
    //         $sales[] = $data->total_sales;
    //     }

    //     // Return the data to the frontend
    //     return response()->json([
    //         'sales_data' => $sales,
    //         'months' => $months
    //     ]);
    // }
}

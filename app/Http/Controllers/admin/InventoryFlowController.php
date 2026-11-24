<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryFlowController extends Controller
{
    public function index()
    {
        $storeId = session('selected_store_id');
        $categories = Category::all();
        $outofstockProducts = Stock::where('store_id', $storeId)
            ->where('quantity', 0)
            ->get();

        $alertQty = setting('qty_alert');  // no fallback

        $lowStockProducts = Stock::where('store_id', $storeId)
            ->where('quantity', '<=', $alertQty)
            ->where('quantity', '>', 0)
            ->get();

        // Incoming Stock (already added previously)
        $incomingStockData = DB::table('stock_transfer_items')
            ->join('stock_transfers', 'stock_transfer_items.reference_no', '=', 'stock_transfers.reference_no')
            ->selectRaw("DATE_FORMAT(stock_transfers.created_at, '%Y-%m') as month, SUM(stock_transfer_items.qty) as total_quantity")
            ->where('stock_transfers.store_to', $storeId)
            ->where('stock_transfer_items.status', 1)
            ->groupBy('month')
            ->orderByRaw('MIN(stock_transfers.created_at)')
            ->pluck('total_quantity', 'month')
            ->toArray();

        // Outgoing Transfers
        $outgoingTransferData = DB::table('stock_transfer_items')
            ->join('stock_transfers', 'stock_transfer_items.reference_no', '=', 'stock_transfers.reference_no')
            ->selectRaw("DATE_FORMAT(stock_transfers.created_at, '%Y-%m') as month, SUM(stock_transfer_items.qty) as total_quantity")
            ->where('stock_transfers.store_from', $storeId)
            ->where('stock_transfer_items.status', 1)
            ->groupBy('month')
            ->orderByRaw('MIN(stock_transfers.created_at)')
            ->pluck('total_quantity', 'month')
            ->toArray();

        // Sales (from orders)
        $salesData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->selectRaw("DATE_FORMAT(orders.created_at, '%Y-%m') as month, SUM(order_items.qty) as total_quantity")
            ->where('orders.store_id', $storeId)
            ->groupBy('month')
            ->orderByRaw('MIN(orders.created_at)')
            ->pluck('total_quantity', 'month')
            ->toArray();

        $stockAnalysisData = DB::table('stocks')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(quantity * price) as total_value")
            ->where('store_id', $storeId)
            ->groupBy('month')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total_value', 'month')
            ->toArray();

        return view('admin.inventory-flow', compact(
            'lowStockProducts',
            'outofstockProducts',
            'incomingStockData',
            'outgoingTransferData',
            'stockAnalysisData',
            'salesData',
            'categories',
        ));
    }

    public function indexSuperAdmin()
    {
        $storeId = session('selected_store_id');
        $categories = Category::all();
        $outofstockProducts = Stock::where('store_id', $storeId)
            ->where('quantity', 0)
            ->get();

        $alertQty = setting('qty_alert');  // no fallback

        $lowStockProducts = Stock::where('store_id', $storeId)
            ->where('quantity', '<=', $alertQty)
            ->where('quantity', '>', 0)
            ->get();

        // Incoming Stock (already added previously)
        $incomingStockData = DB::table('stock_transfer_items')
            ->join('stock_transfers', 'stock_transfer_items.reference_no', '=', 'stock_transfers.reference_no')
            ->selectRaw("DATE_FORMAT(stock_transfers.created_at, '%Y-%m') as month, SUM(stock_transfer_items.qty) as total_quantity")
            ->where('stock_transfers.store_to', $storeId)
            ->where('stock_transfer_items.status', 1)
            ->groupBy('month')
            ->orderByRaw('MIN(stock_transfers.created_at)')
            ->pluck('total_quantity', 'month')
            ->toArray();

        // Outgoing Transfers
        $outgoingTransferData = DB::table('stock_transfer_items')
            ->join('stock_transfers', 'stock_transfer_items.reference_no', '=', 'stock_transfers.reference_no')
            ->selectRaw("DATE_FORMAT(stock_transfers.created_at, '%Y-%m') as month, SUM(stock_transfer_items.qty) as total_quantity")
            ->where('stock_transfers.store_from', $storeId)
            ->where('stock_transfer_items.status', 1)
            ->groupBy('month')
            ->orderByRaw('MIN(stock_transfers.created_at)')
            ->pluck('total_quantity', 'month')
            ->toArray();

        // Sales (from orders)
        $salesData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->selectRaw("DATE_FORMAT(orders.created_at, '%Y-%m') as month, SUM(order_items.qty) as total_quantity")
            ->where('orders.store_id', $storeId)
            ->groupBy('month')
            ->orderByRaw('MIN(orders.created_at)')
            ->pluck('total_quantity', 'month')
            ->toArray();

        $stockAnalysisData = DB::table('stocks')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(quantity * price) as total_value")
            ->where('store_id', $storeId)
            ->groupBy('month')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total_value', 'month')
            ->toArray();

        return view('SuperAdmin.inventory-flow', compact(
            'lowStockProducts',
            'outofstockProducts',
            'incomingStockData',
            'outgoingTransferData',
            'stockAnalysisData',
            'salesData',
            'categories',
        ));
    }

    // public function filterLowStock(Request $request)
    // {
    //     $storeId = session('selected_store_id');
    //     $categoryId = $request->input('category_id');

    //     $lowStockProducts = Stock::with(['store', 'category'])
    //         ->where('store_id', $storeId)  // Filter by store
    //         ->when($categoryId, function ($query) use ($categoryId) {
    //             $query->where('category_id', $categoryId);
    //         })
    //         ->where('quantity', '<', 5)
    //         ->where('quantity', '>', 0)
    //         ->get();

    //     // return response()->json([
    //     //     'html' => view('admin.partials.storeManagerPartials.low_stock_rows', compact('lowStockProducts'))->render()
    //     // ]);
    // }

    public function filterOutOfStock(Request $request)
    {
        $storeId = session('selected_store_id');
        $categoryId = $request->input('category');  // Corrected to match the key sent in AJAX

        $outofstockProducts = Stock::with(['store', 'category'])
            ->where('store_id', $storeId)  // Filter by store
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);  // Apply category filter if it's present
            })
            ->where('quantity', 0)  // Only out of stock products
            ->get();

        return response()->json([
            'html' => view('admin.partials.storeManagerPartials.out_of_stock_rows', compact('outofstockProducts'))->render()
        ]);
    }
}

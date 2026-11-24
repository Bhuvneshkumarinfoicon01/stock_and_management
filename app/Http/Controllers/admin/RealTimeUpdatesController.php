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

class RealTimeUpdatesController extends Controller
{
    public function index(Request $request)
    {
        $storeId = session('selected_store_id');
        $categories = Category::all();
        $alertQty = setting('qty_alert');
        if ($request->ajax()) {
            $type = $request->input('type');
            $categoryId = $request->input('category_id');
            $sortOrder = $request->input('sort_order');

            $query = Stock::where('store_id', $storeId);

            if ($type === 'low') {
                $query->where('quantity', '<=', $alertQty)->where('quantity', '>', 0);
            } elseif ($type === 'out') {
                $query->where('quantity', 0);
            }

            if (!empty($categoryId)) {
                $query->where('category_id', $categoryId);
            }

            if ($sortOrder === 'asc') {
                $query->orderBy('product_name', 'asc');
            } elseif ($sortOrder === 'desc') {
                $query->orderBy('product_name', 'desc');
            } elseif ($sortOrder === 'qty_asc') {
                $query->orderBy('quantity', 'asc');
            } elseif ($sortOrder === 'qty_desc') {
                $query->orderBy('quantity', 'desc');
            }

            $products = $query->get();

            return datatables()
                ->of($products)
                ->addColumn('store', fn($row) => $row->store->name)
                ->addColumn('product_info', function ($row) {
                    $imagePath = $row->images
                        ? asset('storage/' . $row->images)
                        : asset('admin/assets/img/avatar/default-product.png');
                    return '
                        <div class="productimgname">
                            <a href="javascript:void(0);" class="product-img stock-img">
                                <img src="' . $imagePath . '" alt="product">
                            </a>
                            <a href="javascript:void(0);">' . htmlspecialchars($row->product_name) . '</a>
                        </div>';
                })
                ->addColumn('category', fn($row) => $row->category->name)
                ->addColumn('sku', fn($row) => $row->sku)
                ->addColumn('quantity', fn($row) => $row->quantity)
                ->addColumn('qty_alert', fn($row) => $type === 'low' ? setting('qty_alert') : '0')
                ->rawColumns(['product_info'])
                ->make(true);
        }

        // Incoming Stock (already added previously)
        $incomingStockData = DB::table('stock_transfer_items')
            ->join('stock_transfers', 'stock_transfer_items.reference_no', '=', 'stock_transfers.reference_no')
            ->selectRaw("DATE_FORMAT(stock_transfers.created_at, '%b') as month, SUM(stock_transfer_items.qty) as total_quantity")
            ->where('stock_transfers.store_to', $storeId)
            ->where('stock_transfer_items.status', 1)
            ->groupBy('month')
            ->orderByRaw('MIN(stock_transfers.created_at)')
            ->pluck('total_quantity', 'month')
            ->toArray();

        // Outgoing Transfers
        $outgoingTransferData = DB::table('stock_transfer_items')
            ->join('stock_transfers', 'stock_transfer_items.reference_no', '=', 'stock_transfers.reference_no')
            ->selectRaw("DATE_FORMAT(stock_transfers.created_at, '%b') as month, SUM(stock_transfer_items.qty) as total_quantity")
            ->where('stock_transfers.store_from', $storeId)
            ->where('stock_transfer_items.status', 1)
            ->groupBy('month')
            ->orderByRaw('MIN(stock_transfers.created_at)')
            ->pluck('total_quantity', 'month')
            ->toArray();

        // Sales (from orders)
        $salesData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->selectRaw("DATE_FORMAT(orders.created_at, '%b') as month, SUM(order_items.qty) as total_quantity")
            ->where('orders.store_id', $storeId)
            ->groupBy('month')
            ->orderByRaw('MIN(orders.created_at)')
            ->pluck('total_quantity', 'month')
            ->toArray();

        $stockAnalysisData = DB::table('stocks')
            ->selectRaw("DATE_FORMAT(created_at, '%b') as month, SUM(quantity * price) as total_value")
            ->where('store_id', $storeId)
            ->groupBy('month')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total_value', 'month')
            ->toArray();

        return view('storemanager.real-time-stock', compact(
            // 'lowStockProducts',
            // 'outofstockProducts',
            'incomingStockData',
            'outgoingTransferData',
            'stockAnalysisData',
            'salesData',
            'categories',
        ));
    }
}

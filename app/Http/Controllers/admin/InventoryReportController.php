<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InventoryReportController extends Controller
{
    public function index(Request $request)
    {
        $storeId = session('selected_store_id');
        if ($request->ajax()) {
            $query = StockTransfer::with(['storeFrom', 'items', 'storeTo'])
                ->where('store_to', $storeId)
                ->whereHas('items', function ($q) {
                    $q->where('status', 1);
                });
            if ($request->store_id) {
                $query->where('store_from', $request->store_id);
            }

            if ($request->date_from) {
                $query->whereDate('arrival_date', '>=', Carbon::parse($request->date_from)->startOfDay());
            }

            if ($request->date_to) {
                $query->whereDate('arrival_date', '<=', Carbon::parse($request->date_to)->endOfDay());
            }
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            }

            $transfers = $query->get();
            return datatables()
                ->of($transfers)
                ->addColumn('from_store', fn($row) => $row->storeFrom->name)
                ->addColumn('to_store', fn($row) => $row->storeTo->name)
                ->addColumn('transfer_product_name', fn($row) => $row->items->pluck('product_name')->implode(', '))
                ->addColumn('qty', fn($row) => $row->items->pluck('qty')->implode(', '))
                ->addColumn('reference', fn($row) => $row->items->pluck('reference_no')->implode(', '))
                ->addColumn('date', fn($row) => format_date($row->arrival_date))
                ->make(true);
        }

        $outofstockProducts = Stock::where('store_id', $storeId)
            ->where('quantity', 0)
            ->limit(5)
            ->get();

        $alertQty = setting('qty_alert');  // no fallback

        $lowStockProducts = Stock::where('store_id', $storeId)
            ->where('quantity', '<=', $alertQty)
            ->where('quantity', '>', 0)
            ->limit(5)
            ->get();

        return view('storemanager.inventory-report', compact('outofstockProducts', 'lowStockProducts'));
    }

    public function getOutOfStock(Request $request)
    {
        $storeId = session('selected_store_id');
        if ($request->ajax()) {
            $query = Stock::where('store_id', $storeId)->where('quantity', 0);
            if (!empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('product_name', 'like', '%' . $search . '%');
                });
            }

            return datatables()
                ->of($query)
                ->addColumn('product_info', function ($row) {
                    $imagePath = $row->images
                        ? asset('storage/' . $row->images)
                        : asset('admin/assets/img/avatar/default-product.png');

                    $productName = $row->product_name ?? '-';
                    return '
                <div class="productimgname d-flex align-items-center mb-3">
                    <div class="view-product me-2">
                        <a href="javascript:void(0);">
                            <img src="' . $imagePath . '" alt="product" width="60">
                        </a>
                    </div>
                    <a href="javascript:void(0);">' . htmlspecialchars($productName) . '</a>
                </div>
            ';
                })
                ->addColumn('quantity', fn($row) => $row->quantity)
                ->rawColumns(['product_info'])
                ->make(true);
        }

        return view('storemanager.out-stock-view');
    }

    public function getLowStock(Request $request)
    {
        $alertQty = setting('qty_alert');
        $storeId = session('selected_store_id');
        if ($request->ajax()) {
            $query = Stock::where('store_id', $storeId)->where('quantity', '<=', $alertQty)->where('quantity', '>', 0);
            if (!empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('product_name', 'like', '%' . $search . '%');
                });
            }

            return datatables()
                ->of($query)
                ->addColumn('product_info', function ($row) {
                    $imagePath = $row->images
                        ? asset('storage/' . $row->images)
                        : asset('admin/assets/img/avatar/default-product.png');

                    $productName = $row->product_name ?? '-';
                    return '
                <div class="productimgname d-flex align-items-center mb-2">
                    <div class="view-product me-2">
                        <a href="javascript:void(0);">
                            <img src="' . $imagePath . '" alt="product" width="60">
                        </a>
                    </div>
                    <a href="javascript:void(0);">' . htmlspecialchars($productName) . '</a>
                </div>
            ';
                })
                ->addColumn('quantity', fn($row) => $row->quantity)
                ->rawColumns(['product_info'])
                ->make(true);
        }

        return view('storemanager.low-stock-view');
    }
}

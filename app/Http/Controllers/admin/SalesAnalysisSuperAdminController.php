<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesAnalysisSuperAdminController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        if ($request->ajax()) {
            return response()->json(
                $this->buildMonthlySales($request)
            );
        }

        return view('SuperAdmin.sales-analysis', compact('stores', 'categories'));
    }

    /**
     * Monthly sales from orders (line totals). Excludes estimate orders (status 3).
     *
     * @return array<int, object{month: string, total: float}>
     */
    private function buildMonthlySales(Request $request): array
    {
        $storeId = $request->input('store_id');
        $categoryId = $request->input('category_id');
        $year = (int) ($request->input('year') ?: now()->year);

        if ($categoryId) {
            $query = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.status', '!=', 3)
                ->whereYear('orders.created_at', $year)
                ->where('order_items.category_id', $categoryId)
                ->when($storeId, fn ($q) => $q->where('orders.store_id', $storeId))
                ->selectRaw("DATE_FORMAT(orders.created_at, '%b') as month")
                ->selectRaw('ROUND(SUM(order_items.qty * order_items.price), 2) as total')
                ->groupByRaw("DATE_FORMAT(orders.created_at, '%b')")
                ->orderByRaw('MIN(orders.created_at)');
        } else {
            $query = DB::table('orders')
                ->where('orders.status', '!=', 3)
                ->whereYear('orders.created_at', $year)
                ->when($storeId, fn ($q) => $q->where('orders.store_id', $storeId))
                ->selectRaw("DATE_FORMAT(orders.created_at, '%b') as month")
                ->selectRaw('ROUND(SUM(orders.total_price), 2) as total')
                ->groupByRaw("DATE_FORMAT(orders.created_at, '%b')")
                ->orderByRaw('MIN(orders.created_at)');
        }

        return $query->get()->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Legacy query — inflated totals when category filter used (SUM order total
    | per line row). Kept for reference only.
    |--------------------------------------------------------------------------
    |
    | $query = DB::table('orders')
    |     ->join('order_items', 'orders.id', '=', 'order_items.order_id')
    |     ->join('stocks', function ($join) {
    |         $join->on('stocks.store_id', '=', 'orders.store_id')
    |             ->on('stocks.product_id', '=', 'order_items.product_id');
    |     })
    |     ->where('orders.store_id', $storeId)
    |     ->selectRaw("DATE_FORMAT(orders.created_at, '%b') as month, SUM(orders.total_price) as total")
    |     ->groupByRaw("DATE_FORMAT(orders.created_at, '%b')");
    */
}

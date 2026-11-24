<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Store;
use App\Services\DatatablePaginationService;
use App\Services\InventoryValuationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryValuationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->data($request);
        }

        $valuationService = app(InventoryValuationService::class);

        return view('admin.inventory-valuation', [
            'categories' => Category::orderBy('name')->get(),
            'stores' => Store::orderBy('name')->get(),
            'valuationMethod' => $valuationService->method(),
            'valuationMethodLabel' => $valuationService->methodLabel(),
            'datatableConfig' => app(DatatablePaginationService::class)->config('inventory-valuation', [
                'fixedHeader' => false,
            ]),
        ]);
    }

    protected function data(Request $request): JsonResponse
    {
        $request->validate([
            'as_at' => 'nullable|string',
        ]);

        $asAt = $this->parseAsAtDate($request->input('as_at')) ?? now()->endOfDay();

        $stores = $this->resolveStockListStores($request);
        $search = trim((string) $request->input('search.value', ''));
        $categoryId = $request->filled('category') && $request->category !== 'choose'
            ? (int) $request->category
            : null;

        $rows = app(InventoryValuationService::class)->buildReportRows(
            $stores,
            $asAt,
            $search,
            $categoryId
        );

        $orderColumn = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $sortKeys = ['product_name', 'sku', 'category', 'unit', 'combined_qty', 'combined_value'];
        $sortKey = $sortKeys[$orderColumn] ?? 'product_name';

        usort($rows, function ($a, $b) use ($sortKey, $orderDir) {
            if (in_array($sortKey, ['combined_qty', 'combined_value'], true)) {
                $cmp = ($a[$sortKey] ?? 0) <=> ($b[$sortKey] ?? 0);
            } else {
                $cmp = strnatcasecmp((string) ($a[$sortKey] ?? ''), (string) ($b[$sortKey] ?? ''));
            }

            return $orderDir === 'desc' ? -$cmp : $cmp;
        });

        $total = count($rows);
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 25);
        $pageRows = $length < 0
            ? $rows
            : array_slice($rows, $start, $length > 0 ? $length : $total);

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'valuation_method' => app(InventoryValuationService::class)->methodLabel(),
            'as_at' => $asAt->toDateString(),
            'stores' => $stores->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values(),
            'data' => array_values($pageRows),
        ]);
    }

    protected function resolveStockListStores(Request $request)
    {
        $user = auth()->user();
        $sessionStoreId = session('selected_store_id');
        $storesQuery = Store::query()->orderBy('name');

        if ($user && in_array((int) $user->role_id, [1, 2, 3, 5], true)) {
            if ($request->filled('store')) {
                if ($request->store === 'all') {
                    // all stores
                } elseif ($request->store !== 'choose') {
                    $storesQuery->where('id', $request->store);
                } elseif ($sessionStoreId) {
                    $storesQuery->where('id', $sessionStoreId);
                }
            } elseif ($sessionStoreId) {
                $storesQuery->where('id', $sessionStoreId);
            }
        } elseif ($sessionStoreId) {
            $storesQuery->where('id', $sessionStoreId);
        }

        return $storesQuery->get(['id', 'name']);
    }

    private function parseAsAtDate(?string $date): ?Carbon
    {
        if (! $date || trim($date) === '') {
            return null;
        }

        $date = trim($date);

        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
            return parse_date_input($date)?->endOfDay();
        }

        try {
            return Carbon::parse($date)->endOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}

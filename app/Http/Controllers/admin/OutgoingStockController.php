<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OutgoingStockController extends Controller
{
    // public function index(Request $request)
    // {
    //     // Get the store ID from the session or authenticated user
    //     $storeId = session('selected_store_id');  // Assuming the store ID is stored in the session
    //     if ($request->ajax()) {
    //         $query = StockTransfer::with(['storeTo', 'items'])
    //             ->where('store_from', $storeId);

    //         // status filter
    //         if ($request->has('status') && $request->status !== 'choose') {
    //             $status = $request->status;
    //             $query->whereHas('items', function ($q) use ($status) {
    //                 $q->where('status', $status);
    //             });
    //         }

    //         // sorting
    //         if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
    //             $query->orderBy('created_at', $request->order_type);
    //         }
    //         $transfers = $query->get();
    //         return datatables()
    //             ->of($transfers)
    //             ->addColumn('date', fn($row) => \Carbon\Carbon::parse($row->created_at)->format('d-m-Y'))
    //             ->addColumn('transfer_product_name', fn($row) => $row->items->pluck('product_name')->implode(', '))
    //             ->addColumn('destination_store', fn($row) => $row->storeTo->name)
    //             ->addColumn('qty', fn($row) => $row->items->pluck('qty')->implode(', '))
    //             ->addColumn('status', function ($row) {
    //                 $firstItem = $row->items->first();

    //                 return view('admin.partials.storeManagerPartials.outgoing_status_buttons', [
    //                     'status' => optional($firstItem)->status ?? 'N/A',
    //                     'id' => $row->id,
    //                     'product_id' => optional($firstItem)->product_id,
    //                     'reference_no' => $row->reference_no
    //                 ])->render();
    //             })
    //             ->rawColumns(['status'])
    //             ->make(true);
    //     }
    //     // Fetch transfers where the current store is the "from" store

    //     return view('storemanager.outgoing-stock');
    // }
    public function index(Request $request)
    {
        $storeId = session('selected_store_id');

        if ($request->ajax()) {
            // Query individual items instead of parent transfers
            $query = StockTransferItem::with(['transfer.storeTo'])
                ->whereHas('transfer', function ($q) use ($storeId) {
                    $q->where('store_from', $storeId);
                });

            // Status filter
            if ($request->has('status') && $request->status !== 'choose') {
                $query->where('status', $request->status);
            }

            // Sorting by transfer date
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy(
                    StockTransfer::select('created_at')
                        ->whereColumn('stock_transfers.id', 'stock_transfer_items.stock_transfer_id'),
                    $request->order_type
                );
            }

            $items = $query->get();

            return datatables()
                ->of($items)
                ->addColumn('date', fn($row) =>
                    optional($row->transfer)->created_at
                        ? format_date($row->transfer->created_at)
                        : 'N/A')
                ->addColumn('transfer_product_name', fn($row) => $row->product_name)
                ->addColumn('destination_store', fn($row) =>
                    optional($row->transfer->storeTo)->name ?? 'N/A')
                ->addColumn('qty', fn($row) => $row->qty)
                ->addColumn('status', function ($row) {
                    $normalizedStatus = $row->status;
                    if (!in_array($normalizedStatus, [0, 1], true)) {
                        $normalizedStatus = null;  // Treat anything else as pending
                    }

                    return view('admin.partials.storeManagerPartials.outgoing_status_buttons', [
                        'status' => $normalizedStatus,
                        'id' => optional($row->transfer)->id,
                        'product_id' => $row->product_id,
                        'reference_no' => optional($row->transfer)->reference_no
                    ])->render();
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('storemanager.outgoing-stock');
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Mail\StockTransferStatusNotification;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use DB;

class IncomingStockController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $storeId = session('selected_store_id');
            $query = StockTransfer::with(['storeFrom', 'items'])
                ->where('store_to', $storeId);

            // status filter
            if ($request->has('status') && $request->status !== 'choose') {
                $status = $request->status;
                $query->whereHas('items', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            }

            // sorting
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            }

            $transfers = $query->get();

            return datatables()
                ->of($transfers->flatMap(fn($transfer) =>
                    $transfer->items->map(fn($item) => [
                        'arrival_date' => $transfer->arrival_date,
                        'supplier_name' => $transfer->storeFrom->name,
                        'reference_no' => $transfer->reference_no,
                        'product_name' => $item->product_name,
                        'incoming_qty' => $item->qty,
                        'instock_qty' => $item->stock ? $item->stock->quantity : 'N/A',
                        'status' => $item->status,
                        'transfer_id' => $transfer->id,
                        'product_id' => $item->product_id,
                    ])))
                ->addColumn('status', function ($row) {
                    return view('admin.partials.storeManagerPartials.status_buttons', [
                        'status' => $row['status'],
                        'id' => $row['transfer_id'],
                        'product_id' => $row['product_id'],
                        'reference_no' => $row['reference_no'],
                    ])->render();
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('storemanager.incoming-stock');
    }

    // public function updateTransferStatus(Request $request, $id)
    // {
    //     $request->validate([
    //         'status' => 'required|in:0,1',
    //     ]);

    //     $referenceNo = $request->reference_no;
    //     $productId = $request->product_id;
    //     $status = $request->status;

    //     $transferItem = StockTransferItem::where('reference_no', $referenceNo)
    //         ->where('product_id', $productId)
    //         ->first();

    //     if (!$transferItem) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Transfer item not found.',
    //         ], 404);  // Return an error if the transfer item doesn't exist
    //     }

    //     $previousStatus = $transferItem->status;
    //     $fromStoreId = $transferItem->transfer->store_from;  // Get the "from" store ID
    //     $toStoreId = $transferItem->transfer->store_to;  // Get the "to" store ID
    //     $qty = $transferItem->qty;

    //     DB::beginTransaction();  // Start transaction

    //     try {
    //         if ($request->status == 1 && $previousStatus !== 1) {
    //             // Accepting the transfer (status changes to 1)
    //             $fromStock = Stock::where('store_id', $fromStoreId)
    //                 ->where('product_id', $productId)
    //                 ->first();

    //             if (!$fromStock || $fromStock->quantity < $qty) {
    //                 DB::rollBack();  // Rollback if insufficient stock
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Insufficient stock in the "from" store.',
    //                 ], 400);
    //             }

    //             $fromStock->quantity -= $qty;
    //             $fromStock->save();

    //             $toStock = Stock::where('store_id', $toStoreId)
    //                 ->where('product_id', $productId)
    //                 ->first();

    //             if ($toStock) {
    //                 $toStock->quantity += $qty;
    //                 $toStock->save();
    //             } else {
    //                 Stock::create([
    //                     'store_id' => $toStoreId,
    //                     'product_id' => $productId,
    //                     'product_name' => $transferItem->product_name,
    //                     'quantity' => $qty,
    //                     'price' => $fromStock->price,
    //                     'sku' => $fromStock->sku,
    //                     'category_id' => $fromStock->category_id,
    //                     'description' => $fromStock->description,
    //                     'status' => 1,
    //                     'images' => $fromStock->images,
    //                     'discount' => $fromStock->discount,
    //                     'tax' => $fromStock->tax,
    //                     'manufactured_date' => $fromStock->manufactured_date,
    //                     'expiry_date' => $fromStock->expiry_date,
    //                     'unit_id' => $fromStock->unit_id,
    //                     'supplier_name' => $fromStock->supplier_name,
    //                     'purchase_date' => $fromStock->purchase_date,
    //                 ]);
    //             }
    //         } elseif ($request->status == 0 && $previousStatus == 1) {
    //             // Rejecting the transfer after it was accepted (status changes to 0)
    //             $toStock = Stock::where('store_id', $toStoreId)
    //                 ->where('product_id', $productId)
    //                 ->first();

    //             if ($toStock && $toStock->quantity >= $qty) {
    //                 $toStock->quantity -= $qty;
    //                 $toStock->save();

    //                 $fromStock = Stock::where('store_id', $fromStoreId)
    //                     ->where('product_id', $productId)
    //                     ->first();

    //                 if ($fromStock) {
    //                     $fromStock->quantity += $qty;
    //                     $fromStock->save();
    //                 } else {
    //                     Stock::create([
    //                         'store_id' => $fromStoreId,
    //                         'product_id' => $productId,
    //                         'product_name' => $transferItem->product_name,
    //                         'quantity' => $qty,
    //                         'price' => $toStock->price,
    //                         'sku' => $toStock->sku,
    //                         'category_id' => $toStock->category_id,
    //                         'description' => $toStock->description,
    //                         'status' => 1,
    //                         'images' => $toStock->images,
    //                         'discount' => $toStock->discount,
    //                         'tax' => $toStock->tax,
    //                         'manufactured_date' => $toStock->manufactured_date,
    //                         'expiry_date' => $toStock->expiry_date,
    //                         'unit_id' => $toStock->unit_id,
    //                         'supplier_name' => $toStock->supplier_name,
    //                         'purchase_date' => $toStock->purchase_date,
    //                     ]);
    //                 }
    //             } else {
    //                 DB::rollBack();  // Rollback if there's insufficient stock to reverse
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Insufficient stock in the "to" store to reverse the transfer.',
    //                 ], 400);
    //             }
    //         }

    //         // Update the transfer item status
    //         $transferItem->status = $request->status;
    //         $transferItem->save();

    //         DB::commit();  // Commit the transaction

    //         // Send Email Notification
    //         try {
    //             $transfer = $transferItem->transfer;
    //             $items = StockTransferItem::where('reference_no', $transfer->reference_no)->get();
    //             $storeFrom = Store::find($fromStoreId);
    //             $storeTo = Store::find($toStoreId);

    //             $adminEmail = config('mail.admin_email');  // Add this to mail config
    //             $storeToEmail = $storeTo->email ?? null;

    //             $statusText = $request->status == 1 ? 'Accepted' : 'Rejected';

    //             $recipients = array_filter([$adminEmail, $storeToEmail]);

    //             foreach ($recipients as $recipient) {
    //                 Mail::to($recipient)->send(new \App\Mail\StockTransferStatusNotification(
    //                     $transfer,
    //                     $items,
    //                     $storeFrom,
    //                     $storeTo,
    //                     $statusText
    //                 ));
    //             }
    //         } catch (\Exception $mailEx) {
    //             \Log::error('Stock transfer status mail failed: ' . $mailEx->getMessage());
    //         }
    //         // End Send Email Notification

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Transfer status updated successfully!',
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();  // Rollback if any exception occurs
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Something went wrong. Please try again later.',
    //         ], 500);
    //     }
    // }
    public function updateTransferStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:0,1',
        ]);

        $referenceNo = $request->reference_no;
        $productId = $request->product_id;
        $status = (int) $request->status;

        $transferItem = StockTransferItem::where('reference_no', $referenceNo)
            ->where('product_id', $productId)
            ->first();

        if (!$transferItem) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer item not found.',
            ], 404);
        }

        $fromStoreId = $transferItem->transfer->store_from;
        $toStoreId = $transferItem->transfer->store_to;
        $qty = $transferItem->qty;

        /* ────── snapshots BEFORE change ────── */
        $oldItem = $transferItem->toArray();
        $oldFromStock = Stock::where('store_id', $fromStoreId)
            ->where('product_id', $productId)
            ->first()
            ?->toArray();
        $oldToStock = Stock::where('store_id', $toStoreId)
            ->where('product_id', $productId)
            ->first()
            ?->toArray();

        DB::beginTransaction();

        try {
            /* ────── quantity movements (unchanged logic) ────── */
            if ($status === 1 && $transferItem->status !== 1) {
                // accept transfer
                $fromStock = Stock::where('store_id', $fromStoreId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (!$fromStock || $fromStock->quantity < $qty) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock in the "from" store.',
                    ], 400);
                }

                $fromStock->decrement('quantity', $qty);

                $toStock = Stock::where('store_id', $toStoreId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if ($toStock) {
                    $toStock->increment('quantity', $qty);
                } else {
                    $toStock = Stock::create([
                        'store_id' => $toStoreId,
                        'product_id' => $productId,
                        'product_name' => $transferItem->product_name,
                        'quantity' => $qty,
                        'price' => $fromStock->price,
                        'sku' => $fromStock->sku,
                        'category_id' => $fromStock->category_id,
                        'description' => $fromStock->description,
                        'status' => 1,
                        'images' => $fromStock->images,
                        'discount' => $fromStock->discount,
                        'tax' => $fromStock->tax,
                        'manufactured_date' => $fromStock->manufactured_date,
                        'expiry_date' => $fromStock->expiry_date,
                        'unit_id' => $fromStock->unit_id,
                        'supplier_name' => $fromStock->supplier_name,
                        'purchase_date' => $fromStock->purchase_date,
                    ]);
                }
            } elseif ($status === 0 && $transferItem->status === 1) {
                // reject (rollback) after acceptance
                $toStock = Stock::where('store_id', $toStoreId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (!$toStock || $toStock->quantity < $qty) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock in the "to" store to reverse the transfer.',
                    ], 400);
                }

                $toStock->decrement('quantity', $qty);

                $fromStock = Stock::where('store_id', $fromStoreId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if ($fromStock) {
                    $fromStock->increment('quantity', $qty);
                } else {
                    $fromStock = Stock::create([
                        'store_id' => $fromStoreId,
                        'product_id' => $productId,
                        'product_name' => $transferItem->product_name,
                        'quantity' => $qty,
                        'price' => $toStock->price,
                        'sku' => $toStock->sku,
                        'category_id' => $toStock->category_id,
                        'description' => $toStock->description,
                        'status' => 1,
                        'images' => $toStock->images,
                        'discount' => $toStock->discount,
                        'tax' => $toStock->tax,
                        'manufactured_date' => $toStock->manufactured_date,
                        'expiry_date' => $toStock->expiry_date,
                        'unit_id' => $toStock->unit_id,
                        'supplier_name' => $toStock->supplier_name,
                        'purchase_date' => $toStock->purchase_date,
                    ]);
                }
            }

            /* ────── update item status ────── */
            $transferItem->status = $status;
            $transferItem->save();

            /* ────── snapshots AFTER change ────── */
            $newItem = $transferItem->toArray();
            $newFromStock = Stock::where('store_id', $fromStoreId)
                ->where('product_id', $productId)
                ->first()
                ?->toArray();
            $newToStock = Stock::where('store_id', $toStoreId)
                ->where('product_id', $productId)
                ->first()
                ?->toArray();

            /* ────── audit trail entry ────── */
            storeHistory(
                'stock_transfer_status',  // key
                'updated',  // event
                'stock_transfer_item',  // entity
                $transferItem->id,  // id
                [  // new values
                    'transfer_item' => $newItem,
                    'from_stock' => $newFromStock,
                    'to_stock' => $newToStock
                ],
                [  // old values
                    'transfer_item' => $oldItem,
                    'from_stock' => $oldFromStock,
                    'to_stock' => $oldToStock
                ]
            );

            DB::commit();

            /* ────── email notification block (unchanged) ────── */
            try {
                $transfer = $transferItem->transfer;
                $items = StockTransferItem::where('reference_no', $transfer->reference_no)->get();
                $storeFrom = Store::find($fromStoreId);
                $storeTo = Store::find($toStoreId);
                $statusText = $status == 1 ? 'Accepted' : 'Rejected';

                $recipients = array_filter([config('mail.admin_email'), $storeTo->email ?? null]);

                foreach ($recipients as $recipient) {
                    Mail::to($recipient)->send(
                        new \App\Mail\StockTransferStatusNotification($transfer, $items, $storeFrom, $storeTo, $statusText)
                    );
                }
            } catch (\Exception $mailEx) {
                \Log::error('Stock transfer status mail failed: ' . $mailEx->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Transfer status updated successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }
}

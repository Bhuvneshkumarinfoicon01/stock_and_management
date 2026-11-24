<?php

namespace App\Http\Controllers\admin;

use App\Helpers\NotificationHelper;
use App\Mail\StockTransferNotification;
use App\Mail\StockTransferStoreManagerNotification;
use App\Services\ProductCatalogService;
use App\Services\RecordNavigator;
use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Arr;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::all();  // Assuming you have a Store model
        //if ($request->ajax()) {
            // Some live/proxy environments may not preserve X-Requested-With.
            // DataTables always sends "draw", so allow that as a server-side signal.
        if ($request->ajax() || $request->has('draw')) {
            $query = StockTransferItem::query()
                ->join('stock_transfers', 'stock_transfer_items.reference_no', '=', 'stock_transfers.reference_no')
                ->join('stores as store_from', 'stock_transfers.store_from', '=', 'store_from.id')
                ->join('stores as store_to', 'stock_transfers.store_to', '=', 'store_to.id')
                ->select([
                    'stock_transfer_items.*',
                    // Alias to avoid duplicate column name (created_at) from stock_transfer_items.*
                    'stock_transfers.created_at as transfer_created_at',
                    'stock_transfers.rperson_from',
                    'stock_transfers.rperson_to',
                    'store_from.name as store_from_name',
                    'store_to.name as store_to_name',
                ]);

            if ($request->input('store_from') && $request->store_from !== 'choose') {
                $query->where('stock_transfers.store_from', $request->input('store_from'));
            }

            if ($request->input('store_to') && $request->store_to !== 'choose') {
                $query->where('stock_transfers.store_to', $request->input('store_to'));
            }

            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('stock_transfers.created_at', [
                    Carbon::parse($request->date_from)->startOfDay(),
                    Carbon::parse($request->date_to)->endOfDay()
                ]);
            }
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('stock_transfers.created_at', $request->order_type);
            }

            // Old approach (kept): eager load full dataset, then DataTables paginates in memory.
            // $items = $query->orderBy('stock_transfers.created_at', 'desc')->get();
            // New approach: pass query builder so pagination/search/sort happen at DB level.
            $query->orderBy('stock_transfers.created_at', 'desc');

            return datatables()
                // ->of($items)
                ->of($query)
                ->addColumn('store_from', fn($row) => $row->store_from_name)
                ->addColumn('store_to', fn($row) => $row->store_to_name)
                ->addColumn('product_name', fn($row) => $row->product_name)
                ->addColumn('qty_transfered', fn($row) => $row->qty)
                ->addColumn('reference_no', function ($row) {
                    $url = route('admin.stock-transfer.show', $row->reference_no);

                    return '<a href="' . e($url) . '">' . e($row->reference_no) . '</a>';
                })
                ->addColumn('created_date', function ($row) {
                    // Handle both Carbon instance and raw datetime string safely.
                    $value = $row->transfer_created_at ?? $row->created_at;
                    return $value instanceof \Carbon\Carbon
                        ? format_date($value)
                        : format_date($value);
                })
                ->addColumn('actions', function ($item) {
                    $transfer = StockTransfer::where('reference_no', $item->reference_no)->first();
                    $transferId = $transfer ? $transfer->id : null;
                    $viewUrl = route('admin.stock-transfer.show', $item->reference_no);

                    $buttons = '<a class="p-2" href="' . e($viewUrl) . '" title="View transfer"><i data-feather="eye" class="feather-eye"></i></a>';

                    if (is_null($item->status)) {

                        // Show edit if user has update permission
                        if (hasPermission('update', 'stock_transfer')) {
                            $buttons .= '
                <a class="p-2 edit-btn" href="javascript:void(0);" 
                   data-id="' . $transferId . '" 
                   data-bs-toggle="modal" 
                   data-bs-target="#edit-units">
                    <i data-feather="edit" class="feather-edit"></i>
                </a>';
                        }

                        // Show delete if user has delete permission
                        if (hasPermission('delete', 'stock_transfer')) {
                            $buttons .= '
                <a href="javascript:void(0);" class="confirm-text p-2 delete-transfer" 
                   data-id="' . $item->id . '">
                    <i data-feather="trash-2" class="feather-trash-2"></i>
                </a>';
                        }

                        return '<div class="edit-delete-action">' . $buttons . '</div>';
                    }

                    $statusBadge = '';
                    if ((int) $item->status === 0) {
                        $statusBadge = ' <span class="badge bg-danger">Rejected</span>';
                    } elseif ((int) $item->status === 1) {
                        $statusBadge = ' <span class="badge bg-success">Accepted</span>';
                    }

                    return '<div class="edit-delete-action"><a class="p-2" href="' . e($viewUrl) . '" title="View transfer"><i data-feather="eye" class="feather-eye"></i></a>' . $statusBadge . '</div>';
                })
                ->rawColumns(['actions', 'reference_no'])
                ->make(true);
        }

        return view('admin.stock-transfer.stock-transfer');
    }

    public function show(string $referenceNo)
    {
        $transfer = StockTransfer::query()
            ->with(['items'])
            ->where('reference_no', $referenceNo)
            ->firstOrFail();

        $storeFrom = Store::find($transfer->store_from);
        $storeTo = Store::find($transfer->store_to);
        $transferRefs = StockTransfer::query()
            ->orderByDesc('id')
            ->pluck('reference_no')
            ->unique()
            ->values()
            ->all();
        $recordNav = RecordNavigator::fromList(
            $transferRefs,
            $transfer->reference_no,
            fn ($referenceNo) => route('admin.stock-transfer.show', $referenceNo),
        );

        return view('admin.stock-transfer.stock-transfer-show', compact('transfer', 'storeFrom', 'storeTo', 'recordNav'));
    }

    public function storeTransfer(Request $request)
    {
        if (!hasPermission('create', 'stock_transfer')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        $request->validate([
            'reference_no' => 'required|string',
            'store_from' => 'required|integer',
            'store_to' => 'required|integer|different:store_from',
            'rperson_from' => 'required|string',
            'rperson_to' => 'required|string',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|string',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
            'arrival_date' => 'required|date',
        ]);

        if (count($request->product_id) !== count($request->qty)) {
            return response()->json([
                'success' => false,
                'message' => 'Product and quantity arrays do not match.'
            ], 422);
        }

        DB::beginTransaction();

        try {

            $referenceNo = $request->reference_no;

            /*
            |--------------------------------------------------------------------------
            | CREATE STOCK TRANSFER
            |--------------------------------------------------------------------------
            */

            $transfer = StockTransfer::create([
                'store_from' => $request->store_from,
                'store_to' => $request->store_to,
                'rperson_from' => $request->rperson_from,
                'rperson_to' => $request->rperson_to,
                'reference_no' => $referenceNo,
                'arrival_date' => parse_date_input_to_db($request->arrival_date),
                'notes' => $request->notes,
            ]);

            /*
            |--------------------------------------------------------------------------
            | SAVE TRANSFER HISTORY
            |--------------------------------------------------------------------------
            */

            storeHistory(
                'stock_transfer_form',
                'created',
                'stock_transfer',
                $transfer->id,
                $transfer->toArray(),
                null
            );

            /*
            |--------------------------------------------------------------------------
            | TRANSFER STOCK
            |--------------------------------------------------------------------------
            */

            foreach ($request->product_id as $key => $product_id) {

                $qty = (float) $request->qty[$key];

                if ($qty <= 0) {
                    throw new \Exception(
                        "Invalid quantity for product ID: {$product_id}"
                    );
                }

                /*
                * GET SOURCE STOCK
                */
                $stockFrom = Stock::where('store_id', $request->store_from)
                    ->where('product_id', $product_id)
                    ->lockForUpdate()
                    ->first();

                if (!$stockFrom) {
                    throw new \Exception(
                        "Product ID {$product_id} not found in source store."
                    );
                }

                /*
                * CHECK AVAILABLE STOCK
                */
                if ($stockFrom->quantity < $qty) {
                    throw new \Exception(
                        "Insufficient stock for product ID {$product_id}. "
                        . "Available: {$stockFrom->quantity}, Requested: {$qty}"
                    );
                }

                /*
                * GET DESTINATION STOCK
                */
                $stockTo = Stock::where('store_id', $request->store_to)
                    ->where('product_id', $product_id)
                    ->lockForUpdate()
                    ->first();

                /*
                * DEDUCT FROM SOURCE STORE
                */
                $stockFrom->quantity = $stockFrom->quantity - $qty;
                $stockFrom->save();

                /*
                * ADD TO DESTINATION STORE
                */
                if ($stockTo) {

                    // Destination stock already exists.
                    $stockTo->quantity = $stockTo->quantity + $qty;
                    $stockTo->save();

                } else {

                    // Destination stock doesn't exist.
                    // Create a new stock record using source stock information.

                    $stockTo = $stockFrom->replicate();

                    $stockTo->store_id = $request->store_to;
                    $stockTo->product_id = $product_id;
                    $stockTo->quantity = $qty;

                    $stockTo->save();
                }

                /*
                * CREATE STOCK TRANSFER ITEM
                */
                StockTransferItem::create([
                    'product_id' => $product_id,
                    'qty' => $qty,
                    'reference_no' => $transfer->reference_no,
                    'product_name' => $stockFrom->product_name,
                    'category_id' => $stockFrom->category_id,
                    'unit_id' => $stockFrom->unit_id,
                    'supplier_name' => $stockFrom->supplier_name,
                    'sku' => $stockFrom->sku,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | COMMIT TRANSACTION
            |--------------------------------------------------------------------------
            */

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

            \Log::error('Stock transfer transaction failed', [
                'reference_no' => $request->reference_no,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }


        /*
        |--------------------------------------------------------------------------
        | POST-TRANSFER NOTIFICATIONS AND EMAILS
        |--------------------------------------------------------------------------
        |
        | The stock transaction is already committed.
        | Email failures will not undo the stock transfer.
        |
        */

        $emailErrors = [];

        try {

            /*
            |--------------------------------------------------------------------------
            | NOTIFICATION TO LOGGED-IN USER
            |--------------------------------------------------------------------------
            */

            $userId = auth()->id();

            $message = 'Stock transfer completed successfully!';

            NotificationHelper::add($userId, $message);

            NotificationHelper::pushRealtime($userId, $message);


            /*
            |--------------------------------------------------------------------------
            | FETCH TRANSFER ITEMS
            |--------------------------------------------------------------------------
            */

            $items = StockTransferItem::where(
                'reference_no',
                $transfer->reference_no
            )->get();


            /*
            |--------------------------------------------------------------------------
            | FETCH STORES
            |--------------------------------------------------------------------------
            */

            $storeFrom = Store::find($request->store_from);

            $storeTo = Store::find($request->store_to);


            /*
            |--------------------------------------------------------------------------
            | DEFINE EMAIL RECIPIENTS
            |--------------------------------------------------------------------------
            */

            $adminEmail = config('mail.admin_email');

            $storeToEmail = $storeTo ? $storeTo->email : null;

            $recipients = array_filter([
                $adminEmail,
                $storeToEmail,
            ]);


            /*
            |--------------------------------------------------------------------------
            | FETCH STORE MANAGERS
            |--------------------------------------------------------------------------
            */

            $fromStoreManager = User::where(
                'assigned_store',
                $request->store_from
            )->first();

            $toStoreManager = User::where(
                'assigned_store',
                $request->store_to
            )->first();


            /*
            |--------------------------------------------------------------------------
            | EMAIL TO DESTINATION STORE MANAGER
            |--------------------------------------------------------------------------
            */

            if ($toStoreManager && $toStoreManager->email) {

                try {

                    Mail::to($toStoreManager->email)->send(
                        new StockTransferStoreManagerNotification(
                            $transfer,
                            $items,
                            $storeFrom,
                            $storeTo,
                            'incoming'
                        )
                    );

                } catch (\Throwable $ex) {

                    $emailErrors[] = 'Destination store manager email failed: '
                        . $ex->getMessage();

                    \Log::error('Destination store manager email failed', [
                        'reference_no' => $referenceNo,
                        'error' => $ex->getMessage(),
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | EMAIL TO SOURCE STORE MANAGER
            |--------------------------------------------------------------------------
            */

            if ($fromStoreManager && $fromStoreManager->email) {

                try {

                    Mail::to($fromStoreManager->email)->send(
                        new StockTransferStoreManagerNotification(
                            $transfer,
                            $items,
                            $storeFrom,
                            $storeTo,
                            'outgoing'
                        )
                    );

                } catch (\Throwable $ex) {

                    $emailErrors[] = 'Source store manager email failed: '
                        . $ex->getMessage();

                    \Log::error('Source store manager email failed', [
                        'reference_no' => $referenceNo,
                        'error' => $ex->getMessage(),
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | NOTIFICATION TO DESTINATION STORE MANAGER
            |--------------------------------------------------------------------------
            */

            $userName = auth()->user()->name;

            $toStoreManager = User::where(
                'assigned_store',
                $request->store_to
            )->first();

            if ($toStoreManager) {

                $incomingMessage = 'Incoming stock transfer request from '
                    . ($storeFrom ? $storeFrom->name : 'source store');

                NotificationHelper::add(
                    $toStoreManager->id,
                    $incomingMessage
                );

                NotificationHelper::pushRealtime(
                    $toStoreManager->id,
                    $incomingMessage,
                    $userName
                );
            }


            /*
            |--------------------------------------------------------------------------
            | NOTIFICATION TO SOURCE STORE MANAGER
            |--------------------------------------------------------------------------
            */

            $fromStoreManager = User::where(
                'assigned_store',
                $request->store_from
            )->first();

            if ($fromStoreManager) {

                $outgoingMessage = 'Outgoing stock transfer to '
                    . ($storeTo ? $storeTo->name : 'destination store')
                    . ' has been initiated.';

                NotificationHelper::add(
                    $fromStoreManager->id,
                    $outgoingMessage
                );

                NotificationHelper::pushRealtime(
                    $fromStoreManager->id,
                    $outgoingMessage,
                    $userName
                );
            }


            /*
            |--------------------------------------------------------------------------
            | EMAIL TO ADMIN AND DESTINATION STORE EMAIL
            |--------------------------------------------------------------------------
            */

            foreach ($recipients as $recipient) {

                try {

                    Mail::to($recipient)->send(
                        new StockTransferNotification(
                            $transfer,
                            $items,
                            $storeFrom,
                            $storeTo
                        )
                    );

                } catch (\Throwable $mailEx) {

                    $emailErrors[] = 'Email failed for ' . $recipient
                        . ': ' . $mailEx->getMessage();

                    \Log::error('Stock transfer email failed', [
                        'reference_no' => $referenceNo,
                        'recipient' => $recipient,
                        'error' => $mailEx->getMessage(),
                    ]);
                }
            }

        } catch (\Throwable $notificationException) {

            /*
            * Stock has already been committed.
            * Do not roll back stock because of notification failure.
            */

            \Log::error('Stock transfer notification processing failed', [
                'reference_no' => $referenceNo,
                'error' => $notificationException->getMessage(),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | FINAL RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'message' => !empty($emailErrors)
                ? 'Stock transfer completed, but some emails or notifications failed.'
                : 'Stock transfer completed successfully!',
            'reference_no' => $referenceNo,
            'email_errors' => $emailErrors,
        ]);
    }

    public function store(Request $request)
    {
        if (!hasPermission('create', 'stock_transfer')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }
        $request->validate([
            'store_from' => 'required|integer',
            'store_to' => 'required|integer|different:store_from',
            'rperson_from' => 'required|string',
            'rperson_to' => 'required|string',
            'product_id' => 'required|array',
            'qty' => 'required|array',
            'notes' => 'nullable|string',
        ]);
        $referenceNo = time() . rand(10, 99);
        DB::beginTransaction();
        try {
            $transfer = StockTransfer::create([
                'store_from' => $request->store_from,
                'store_to' => $request->store_to,
                'rperson_from' => $request->rperson_from,
                'rperson_to' => $request->rperson_to,
                'reference_no' => $referenceNo,
                'notes' => $request->notes,
            ]);
            storeHistory(
                'stock_transfer_form',  // key
                'created',  // event
                'stock_transfer',  // entity type
                $transfer->id,  // entity id
                $transfer->toArray(),  // new values
                null  // no old values
            );
            foreach ($request->product_id as $key => $product_id) {
                $qty = (int) $request->qty[$key];
                $stockFrom = Stock::where('store_id', $request->store_from)
                    ->where('product_id', $product_id)
                    ->first();

                if (!$stockFrom || $stockFrom->quantity < $qty) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product ID: $product_id",
                    ]);
                }
                $stockFrom->decrement('quantity', $qty);
                $stockTo = Stock::where('store_id', $request->store_to)
                    ->where('product_id', $product_id)
                    ->first();

                if ($stockTo) {
                    $stockTo->increment('quantity', $qty);
                } else {
                    Stock::create([
                        'store_id' => $request->store_to,
                        'product_id' => $product_id,
                        'quantity' => $qty,
                    ]);
                }
                StockTransferItem::create([
                    'product_id' => $product_id,
                    'qty' => $qty,
                ]);
            }
            DB::commit();
            // NOTIFICATION
            $userId = auth()->id();
            $message = 'Stock transfer completed successfully!';
            NotificationHelper::add($userId, $message);
            NotificationHelper::pushRealtime($userId, $message);
            return response()->json([
                'success' => true,
                'message' => 'Stock transfer completed successfully!',
                'reference_no' => $referenceNo,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error during stock transfer: ' . $e->getMessage(),
            ]);
        }
    }

    public function edit($id)
    {
        $transfer = StockTransfer::with(['items.category'])->find($id);
        if (!$transfer) {
            return response()->json(['success' => false, 'message' => 'Transfer not found']);
        }
        $availableQuantities = [];
        // Loop through items and get available quantities
        foreach ($transfer->items as $item) {
            $availableQuantity = Stock::where('store_id', $transfer->store_from)
                ->where('product_id', $item->product_id)
                ->first();
            $availableQuantities[$item->product_id] = $availableQuantity ? $availableQuantity->quantity : 0;
        }

        // Add category name to each item
        foreach ($transfer->items as $item) {
            $item->category_name = $item->category->name;  // Access category name
        }

        return response()->json([
            'success' => true,
            'data' => $transfer,
            'availableQuantities' => $availableQuantities,
        ]);
    }


    public function updateTransfer(Request $request)
    {
        if (!hasPermission('update', 'stock_transfer')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        $request->validate([
            'reference_no' => 'required|string',
            'store_from' => 'required|integer',
            'store_to' => 'required|integer|different:store_from',
            'rperson_from' => 'required|string',
            'rperson_to' => 'required|string',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required',
            'product_name' => 'required|array',
            'sku' => 'required|array',
            'category_id' => 'required|array',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);

        if (
            count($request->product_id) !== count($request->qty) ||
            count($request->product_id) !== count($request->product_name) ||
            count($request->product_id) !== count($request->sku) ||
            count($request->product_id) !== count($request->category_id)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Product data arrays do not match.'
            ], 422);
        }

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | STEP 1: FETCH TRANSFER
            |--------------------------------------------------------------------------
            */

            $transfer = StockTransfer::where(
                'reference_no',
                $request->reference_no
            )->lockForUpdate()->first();

            if (!$transfer) {
                throw new \Exception('Transfer not found!');
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 2: CAPTURE OLD MASTER AND ITEMS
            |--------------------------------------------------------------------------
            */

            $oldMaster = $transfer->toArray();

            $oldItems = StockTransferItem::where(
                'reference_no',
                $transfer->reference_no
            )->get()->toArray();

            $oldItemsKeyed = collect($oldItems)
                ->keyBy('product_id')
                ->toArray();

            /*
            |--------------------------------------------------------------------------
            | STEP 3: REVERSE OLD STOCK TRANSFER
            |--------------------------------------------------------------------------
            |
            | Return old transferred quantity to the original source store.
            | Deduct the old transferred quantity from the original destination store.
            |--------------------------------------------------------------------------
            */

            foreach ($oldItems as $oldItem) {

                $productId = $oldItem['product_id'];
                $qty = (float) $oldItem['qty'];

                $oldStoreFromId = $transfer->store_from;
                $oldStoreToId = $transfer->store_to;

                /*
                |--------------------------------------------------------------------------
                | FIND OLD DESTINATION STOCK
                |--------------------------------------------------------------------------
                */

                $oldStockTo = Stock::where('store_id', $oldStoreToId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (!$oldStockTo) {
                    throw new \Exception(
                        "Product ID {$productId} not found in old destination store."
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | CHECK OLD DESTINATION STOCK
                |--------------------------------------------------------------------------
                */

                if ($oldStockTo->quantity < $qty) {
                    throw new \Exception(
                        "Insufficient stock in old destination store for product ID {$productId}. "
                        . "Available: {$oldStockTo->quantity}, Required: {$qty}"
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | FIND OLD SOURCE STOCK
                |--------------------------------------------------------------------------
                */

                $oldStockFrom = Stock::where('store_id', $oldStoreFromId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                /*
                |--------------------------------------------------------------------------
                | RETURN STOCK TO OLD SOURCE STORE
                |--------------------------------------------------------------------------
                */

                if ($oldStockFrom) {

                    $oldStockFrom->quantity += $qty;
                    $oldStockFrom->save();

                } else {

                    $oldStockFrom = $oldStockTo->replicate();

                    $oldStockFrom->store_id = $oldStoreFromId;
                    $oldStockFrom->product_id = $productId;
                    $oldStockFrom->quantity = $qty;

                    $oldStockFrom->save();
                }

                /*
                |--------------------------------------------------------------------------
                | DEDUCT STOCK FROM OLD DESTINATION STORE
                |--------------------------------------------------------------------------
                */

                $oldStockTo->quantity -= $qty;
                $oldStockTo->save();
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 4: UPDATE TRANSFER MASTER
            |--------------------------------------------------------------------------
            */

            $transfer->update([
                'store_from' => $request->store_from,
                'store_to' => $request->store_to,
                'rperson_from' => $request->rperson_from,
                'rperson_to' => $request->rperson_to,
                'notes' => $request->notes,
            ]);

            /*
            |--------------------------------------------------------------------------
            | STEP 5: DELETE OLD TRANSFER ITEMS
            |--------------------------------------------------------------------------
            */

            StockTransferItem::where(
                'reference_no',
                $transfer->reference_no
            )->delete();

            /*
            |--------------------------------------------------------------------------
            | STEP 6: APPLY NEW STOCK TRANSFER
            |--------------------------------------------------------------------------
            */

            foreach ($request->product_id as $i => $productId) {

                $qty = (float) $request->qty[$i];

                if ($qty <= 0) {
                    throw new \Exception(
                        "Invalid quantity for product ID {$productId}."
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | FIND NEW SOURCE STOCK
                |--------------------------------------------------------------------------
                */

                $stockFrom = Stock::where('store_id', $request->store_from)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (!$stockFrom) {
                    throw new \Exception(
                        "Product ID {$productId} not found in new source store."
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | CHECK AVAILABLE SOURCE STOCK
                |--------------------------------------------------------------------------
                */

                if ($stockFrom->quantity < $qty) {
                    throw new \Exception(
                        "Insufficient stock for product ID {$productId}. "
                        . "Available: {$stockFrom->quantity}, Requested: {$qty}"
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | FIND NEW DESTINATION STOCK
                |--------------------------------------------------------------------------
                */

                $stockTo = Stock::where('store_id', $request->store_to)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                /*
                |--------------------------------------------------------------------------
                | DEDUCT FROM NEW SOURCE STORE
                |--------------------------------------------------------------------------
                */

                $stockFrom->quantity -= $qty;
                $stockFrom->save();

                /*
                |--------------------------------------------------------------------------
                | ADD TO NEW DESTINATION STORE
                |--------------------------------------------------------------------------
                */

                if ($stockTo) {

                    $stockTo->quantity += $qty;
                    $stockTo->save();

                } else {

                    $stockTo = $stockFrom->replicate();

                    $stockTo->store_id = $request->store_to;
                    $stockTo->product_id = $productId;
                    $stockTo->quantity = $qty;

                    $stockTo->save();
                }

                /*
                |--------------------------------------------------------------------------
                | CREATE NEW TRANSFER ITEM
                |--------------------------------------------------------------------------
                */

                StockTransferItem::create([
                    'reference_no' => $transfer->reference_no,
                    'product_id' => $productId,
                    'product_name' => $request->product_name[$i],
                    'sku' => $request->sku[$i],
                    'category_id' => $request->category_id[$i],
                    'qty' => $qty,
                    'unit_id' => $stockFrom->unit_id,
                    'supplier_name' => $stockFrom->supplier_name,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 7: CAPTURE NEW MASTER AND ITEMS
            |--------------------------------------------------------------------------
            */

            $newMaster = $transfer->fresh()->toArray();

            $newItems = StockTransferItem::where(
                'reference_no',
                $transfer->reference_no
            )->get()->toArray();

            $newItemsKeyed = collect($newItems)
                ->keyBy('product_id')
                ->toArray();

            /*
            |--------------------------------------------------------------------------
            | STEP 8: COMPARE OLD AND NEW DATA
            |--------------------------------------------------------------------------
            */

            $masterNewDiff = array_diff_assoc_recursive(
                $newMaster,
                $oldMaster
            );

            $masterOldDiff = array_diff_assoc_recursive(
                $oldMaster,
                $newMaster
            );

            $itemsNewDiff = array_diff_assoc_recursive(
                $newItemsKeyed,
                $oldItemsKeyed
            );

            $itemsOldDiff = array_diff_assoc_recursive(
                $oldItemsKeyed,
                $newItemsKeyed
            );

            /*
            |--------------------------------------------------------------------------
            | STEP 9: STORE HISTORY
            |--------------------------------------------------------------------------
            */

            if (
                $masterNewDiff ||
                $masterOldDiff ||
                $itemsNewDiff ||
                $itemsOldDiff
            ) {

                storeHistory(
                    'stock_transfer_form',
                    'updated',
                    'stock_transfer',
                    $transfer->id,
                    [
                        'master' => $masterNewDiff,
                        'items' => $itemsNewDiff
                    ],
                    [
                        'master' => $masterOldDiff,
                        'items' => $itemsOldDiff
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 10: COMMIT TRANSACTION
            |--------------------------------------------------------------------------
            */

            DB::commit();

            /*
            |--------------------------------------------------------------------------
            | STEP 11: NOTIFICATION
            |--------------------------------------------------------------------------
            */

            $userId = auth()->id();

            $message = 'Stock transfer updated successfully!';

            NotificationHelper::add($userId, $message);

            NotificationHelper::pushRealtime($userId, $message);

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            \Log::error('Stock transfer update failed', [
                'reference_no' => $request->reference_no,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(Request $request)
    {
        if (!hasPermission('delete', 'stock_transfer')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        $request->validate([
            'id' => 'required|integer'
        ]);

        try {

            DB::transaction(function () use ($request) {

                /*
                |--------------------------------------------------------------------------
                | STEP 1: FIND TRANSFER ITEM
                |--------------------------------------------------------------------------
                */

                $item = StockTransferItem::findOrFail($request->id);

                // Capture old item data before deletion
                $oldItem = $item->toArray();

                /*
                |--------------------------------------------------------------------------
                | STEP 2: FIND STOCK TRANSFER
                |--------------------------------------------------------------------------
                */

                $transfer = StockTransfer::where(
                    'reference_no',
                    $item->reference_no
                )->firstOrFail();

                $productId = $item->product_id;
                $qty = (float) $item->qty;

                $storeFromId = $transfer->store_from;
                $storeToId = $transfer->store_to;

                /*
                |--------------------------------------------------------------------------
                | STEP 3: FIND DESTINATION STOCK
                | Stock from which transferred quantity will be deducted.
                |--------------------------------------------------------------------------
                */

                $stockTo = Stock::where('store_id', $storeToId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (!$stockTo) {
                    throw new \Exception(
                        "Product ID {$productId} not found in destination store."
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | STEP 4: CHECK DESTINATION AVAILABLE STOCK
                |--------------------------------------------------------------------------
                */

                if ($stockTo->quantity < $qty) {
                    throw new \Exception(
                        "Insufficient stock in destination store to reverse transfer. "
                        . "Available: {$stockTo->quantity}, Required: {$qty}"
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | STEP 5: FIND SOURCE STOCK
                |--------------------------------------------------------------------------
                */

                $stockFrom = Stock::where('store_id', $storeFromId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                /*
                |--------------------------------------------------------------------------
                | STEP 6: RETURN QUANTITY TO SOURCE STORE
                |--------------------------------------------------------------------------
                */

                if ($stockFrom) {

                    // Source stock already exists
                    $stockFrom->quantity = $stockFrom->quantity + $qty;
                    $stockFrom->save();

                } else {

                    // Source stock record does not exist
                    // Create it using destination stock information

                    $stockFrom = $stockTo->replicate();

                    $stockFrom->store_id = $storeFromId;
                    $stockFrom->product_id = $productId;
                    $stockFrom->quantity = $qty;

                    $stockFrom->save();
                }

                /*
                |--------------------------------------------------------------------------
                | STEP 7: DEDUCT QUANTITY FROM DESTINATION STORE
                |--------------------------------------------------------------------------
                */

                $stockTo->quantity = $stockTo->quantity - $qty;
                $stockTo->save();

                /*
                |--------------------------------------------------------------------------
                | STEP 8: STORE ITEM DELETION HISTORY
                |--------------------------------------------------------------------------
                */

                storeHistory(
                    'stock_transfer_item',
                    'deleted',
                    'stock_transfer_item',
                    $item->id,
                    null,
                    $oldItem
                );

                /*
                |--------------------------------------------------------------------------
                | STEP 9: DELETE TRANSFER ITEM
                |--------------------------------------------------------------------------
                */

                $item->delete();

            });

            return response()->json([
                'success' => true,
                'message' => 'Stock transfer item deleted successfully. Stock has been returned to the source store.'
            ]);

        } catch (\Throwable $e) {

            \Log::error('Stock transfer item deletion failed', [
                'item_id' => $request->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getStores()
    {
        try {
            $user = auth()->user();

            if (in_array($user->role_id, [1, 2])) {
                // Role 1 & 2 — get all active stores
                $stores = Store::where('status', 1)->get(['id', 'name']);
            } else {
                // Other roles — only their selected store
                $stores = Store::where('status', 1)
                    ->where('id', $user->selected_store_id)
                    ->get(['id', 'name']);
            }

            return response()->json($stores);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch stores!'], 500);
        }
    }

    /**
     * Get products by store ID and query
     */
    public function getProductsByStore(Request $request)
    {
        $storeId = $request->store_id;
        $query = $request->input('query', '');

        if (!$storeId) {
            return response()->json(['error' => 'Store ID is required!'], 400);
        }

        try {
            $products = app(ProductCatalogService::class)->search((int) $storeId, (string) $query);

            return response()->json($products);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch products!'], 500);
        }
    }

    public function exportStockTransferData()
    {
        $data = DB::table('stock_transfer_items as sti')
            ->join('stock_transfers as st', 'sti.reference_no', '=', 'st.reference_no')
            ->leftJoin('stores as source_store', 'st.store_from', '=', 'source_store.id')
            ->leftJoin('stores as dest_store', 'st.store_to', '=', 'dest_store.id')
            ->leftJoin('categories as cat', 'sti.category_id', '=', 'cat.id')
            ->select([
                'sti.product_name',
                'sti.product_id',
                'source_store.name as source_store_name',
                'dest_store.name as destination_store_name',
                'sti.sku',
                'cat.name as category_name',
                'st.rperson_from',
                'st.rperson_to',
                'sti.qty',
                'st.notes',
                'st.arrival_date',
            ])
            ->get();

        $filename = 'stock_transfer_export_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $columns = [
            'Product Name',
            'Product ID',
            'Source Store',
            'Destination Store',
            'SKU',
            'Category',
            'Responsible From',
            'Responsible To',
            'Quantity Transferred',
            'Notes',
            'Arrival Date',
        ];

        $callback = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->product_name,
                    $row->product_id,
                    $row->source_store_name,
                    $row->destination_store_name,
                    $row->sku,
                    $row->category_name,
                    $row->rperson_from,
                    $row->rperson_to,
                    $row->qty,
                    $row->notes,
                    $row->arrival_date,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

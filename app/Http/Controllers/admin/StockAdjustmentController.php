<?php

namespace App\Http\Controllers\admin;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\GlAccount;
use App\Models\Stock;
use App\Models\StockAdjustment;
use App\Models\Store;
use App\Models\User;
use App\Services\AccountingService;
use App\Services\InventoryValuationService;
use App\Services\ProductUnitCostService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class StockAdjustmentController extends Controller
{
    public function showAdjustmentForm(Request $request)
    {
        if ($request->ajax()) {
            $user = User::find(auth()->id());

            if ($user && in_array($user->role_id, [1, 2])) {
                $query = StockAdjustment::with(['stock', 'store'])
                    ->select('stock_adjustments.*');
            } else {
                $query = StockAdjustment::with(['stock', 'store'])
                    ->where('stock_adjustments.store_id', $user->assigned_store)
                    ->select('stock_adjustments.*');
            }
            $sessionStoreId = session('selected_store_id');

            if ($request->has('store') && $request->store !== 'all') {
                $query->where('store_id', $request->store);
            }
            $orderType = $request->get('order_type', 'desc');  // default to desc
            $query->orderBy('adjust_date', in_array($orderType, ['asc', 'desc']) ? $orderType : 'desc');

            return datatables()
                ->eloquent($query)
                ->addColumn('store_name', fn($row) => $row->store->name)
                ->addColumn('product_details', function ($adjustment) {
                    $imageUrl = asset($adjustment->stock->images ? 'storage/' . $adjustment->stock->images : 'admin/assets/img/avatar/default-product.png');
                    $productName = $adjustment->stock->product_name;

                    return '
                        <div class="productimgname">
                            <a href="javascript:void(0);" class="product-img stock-img">
                                <img src="' . $imageUrl . '" alt="product" />
                            </a>
                            <a href="javascript:void(0);">' . $productName . '</a>
                        </div>
                ';
                })
                ->addColumn('adjustment_type', fn($row) => $row->adjustment_type === 'add' ? 'Add' : 'Reduce')
                ->addColumn('adj_qty', fn($row) => $row->adjust_quantity)
                ->addColumn('unit_cost', fn($row) => $row->unit_cost !== null ? number_format((float) $row->unit_cost, 2) : '—')
                ->addColumn('reason', fn($row) => $row->reason)
                // ->addColumn('adjust_date', fn($row) => $row->adjust_date)
                ->addColumn('adjust_date', fn($row) => format_date($row->adjust_date))
                ->addColumn('actions', function ($row) {
                    $buttons = '';

                    // Show edit if user has update permission
                    if (hasPermission('update', 'stock_adjustment')) {
                        $buttons .= '
            <a href="javascript:void(0);" class="edit-btn me-2 p-2" 
               data-id="' . $row->id . '" 
               data-bs-toggle="modal" 
               data-bs-target="#edit-units">
                <i data-feather="edit" class="feather-edit"></i>
            </a>';
                    }

                    // Show delete if user has delete permission
                    if (hasPermission('delete', 'stock_adjustment')) {
                        $buttons .= '
            <a class="confirm-text p-2 delete-adjustment" 
               href="javascript:void(0);" 
               data-id="' . $row->id . '">
                <i data-feather="trash-2" class="feather-trash-2"></i>
            </a>';
                    }

                    return $buttons ? '<div class="edit-delete-action">' . $buttons . '</div>' : '';
                })
                ->rawColumns(['product_details', 'adjustment_type', 'actions'])
                ->make(true);
        }

        $adjustmentAccounts = GlAccount::query()
            ->where('is_active', true)
            ->where('type', 'expense')
            ->orderBy('code')
            ->get();
        $defaultAdjustmentAccountId = $adjustmentAccounts
            ->firstWhere('code', (string) config('accounting.accounts.cogs', '5000'))
            ?->id ?? $adjustmentAccounts->first()?->id;

        return view('admin.stock-adjustment', compact('adjustmentAccounts', 'defaultAdjustmentAccountId'));
    }

    // public function createAdjustment(Request $request)
    // {
    //     $request->validate([
    //         'store_id' => 'required|integer|exists:stores,id',
    //         'product_id' => 'required|string',
    //         'quantity' => 'required|integer|min:1',
    //         'reason' => 'required|string',
    //         'adjustment_date' => 'required|date',
    //     ]);

    //     // Find the stock entry
    //     $stock = Stock::where('store_id', $request->store_id)
    //         ->where('product_id', $request->product_id)
    //         ->first();

    //     if (!$stock) {
    //         return redirect()->back()->with('error', 'Stock not found for the selected product and store.');
    //     }

    //     // Ensure stock is available
    //     if ($stock->quantity < $request->quantity) {
    //         return redirect()->back()->with('error', 'Insufficient stock quantity to adjust.');
    //     }

    //     // Save adjustment record
    //     $adjustment = new StockAdjustment();
    //     $adjustment->store_id = $request->store_id;
    //     $adjustment->product_id = $request->product_id;
    //     $adjustment->adjust_quantity = $request->quantity;
    //     $adjustment->reason = $request->reason;
    //     $adjustment->adjust_date = Carbon::createFromFormat('d-m-Y', $request->adjustment_date)->format('Y-m-d');
    //     $adjustment->save();
    //     // Deduct quantity from stock
    //     $stock->quantity -= $request->quantity;
    //     $stock->save();

    //     $userId = auth()->id();  // The user who performed the action
    //     $message = 'New Stock Adjustment has been made successfully';  // Custom message
    //     $userName = auth()->user()->name;
    //     $port = env('NOTIFICATION_PORT');
    //     $url = "http://localhost:$port/notify";
    //     $response = Http::post($url, [
    //         'userId' => $userId,
    //         'message' => $message,
    //         'user_name' => $userName,  // Send the user's name
    //     ]);
    //     NotificationHelper::add(auth()->id(), $message);

    //     // Email Notification
    //     try {
    //         $store = \App\Models\Store::find($request->store_id);
    //         $adminEmail = config('mail.admin_email');  // .env -> MAIL_ADMIN_ADDRESS
    //         $storeEmail = $store->email ?? null;

    //         $recipients = array_filter([$adminEmail, $storeEmail]);
    //         $userName = auth()->user()->name;

    //         foreach ($recipients as $recipient) {
    //             Mail::to($recipient)->send(new \App\Mail\StockAdjustmentNotification(
    //                 $adjustment,
    //                 $store,
    //                 $stock,
    //                 $userName
    //             ));
    //         }
    //     } catch (\Exception $mailEx) {
    //         \Log::error('Stock adjustment email failed: ' . $mailEx->getMessage());
    //     }

    //     return redirect()->back()->with('success', 'Stock adjustment created and quantity updated!');
    // }
    public function createAdjustment(Request $request)
    {
        if (!hasPermission('create', 'stock_adjustment')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }
        /* ────── validate ────── */
        $request->validate([
            'store_id' => 'required|integer|exists:stores,id',
            'product_id' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'adjustment_type' => 'required|in:add,remove',
            'adjustment_account_id' => [
                'required_if:adjustment_type,remove',
                'nullable',
                'integer',
                Rule::exists('gl_accounts', 'id')->where(
                    fn ($query) => $query->where('is_active', true)->where('type', 'expense')
                ),
            ],
            // 'reason' => 'required_if:adjustment_type,remove|string',
            'reason' => 'nullable|required_if:adjustment_type,remove|string',
            'adjustment_date' => 'required|app_date',
        ]);

        $adjustmentType = $request->input('adjustment_type', StockAdjustment::TYPE_REMOVE);

        /* ────── locate stock ────── */
        $stock = Stock::where('store_id', $request->store_id)
            ->where('product_id', $request->product_id)
            ->first();

        $productTemplate = $stock ?: Stock::where('product_id', $request->product_id)->first();
        if (!$stock && ($adjustmentType !== StockAdjustment::TYPE_ADD || !$productTemplate)) {
            return back()->with('error', 'Stock not found for the selected product and store.');
        }

        $reason = $request->input('reason');
        if ($adjustmentType === StockAdjustment::TYPE_ADD && empty($reason)) {
            $reason = null;
        }

        /* ────── additions do not require sufficient stock ────── */
        if ($adjustmentType === StockAdjustment::TYPE_REMOVE && (int) ($stock->quantity ?? 0) < $request->quantity) {
            return back()->with('error', 'Insufficient stock quantity to adjust.');
        }

        /* ────── capture OLD stock snapshot ────── */
        $oldStock = $stock?->toArray();

        DB::beginTransaction();
        try {
            if (!$stock) {
                $stockAttributes = $productTemplate->getAttributes();
                unset($stockAttributes['id'], $stockAttributes['store_id'], $stockAttributes['quantity'], $stockAttributes['created_at'], $stockAttributes['updated_at']);
                $stockAttributes['store_id'] = $request->store_id;
                $stockAttributes['quantity'] = 0;
                if (array_key_exists('total_stock', $stockAttributes)) {
                    $stockAttributes['total_stock'] = 0;
                }
                $stock = Stock::create($stockAttributes);
            }

            $unitCost = round(app(InventoryValuationService::class)->suggestedAdjustmentUnitCost(
                (int) $request->store_id,
                (string) $request->product_id,
                (float) $request->quantity,
                $adjustmentType,
            ), 2);
            if ($unitCost <= 0) {
                throw new \RuntimeException('A default unit cost could not be determined for this product and store.');
            }

            /* ────── create adjustment record ────── */
            $adjustment = StockAdjustment::create([
                'store_id' => $request->store_id,
                'product_id' => $request->product_id,
                'adjust_quantity' => $request->quantity,
                'unit_cost' => $unitCost,
                'adjustment_account_id' => $adjustmentType === StockAdjustment::TYPE_REMOVE
                    ? $request->input('adjustment_account_id')
                    : null,
                'adjustment_type' => $adjustmentType,
                'reason' => $reason,
                'adjust_date' => parse_date_input_to_db($request->adjustment_date),
            ]);

            /* ────── update stock quantity (add for additions, deduct for reductions) ────── */
            if ($adjustment->adjustment_type === StockAdjustment::TYPE_ADD) {
                $stock->increment('quantity', $adjustment->adjust_quantity);
            } else {
                $stock->decrement('quantity', $adjustment->adjust_quantity);
            }
            app(ProductUnitCostService::class)->syncStock($stock->fresh());

            $journal = app(AccountingService::class)->postStockAdjustment($adjustment->fresh());
            if (! $journal) {
                throw new \RuntimeException('The stock adjustment journal could not be posted.');
            }

            /* ────── capture NEW stock snapshot ────── */
            $newStock = $stock->fresh()->toArray();

            storeHistory(
                'stock_adjustment_form',
                'created',
                'stock_adjustment',
                $adjustment->id,
                [
                    'adjustment' => $adjustment->fresh()->toArray(),
                    'stock' => $newStock,
                ],
                [
                    'stock' => $oldStock,
                ]
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }

        /* ────── realtime notification ────── */
        $userId = auth()->id();
        $message = 'New stock adjustment recorded.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);

        /* ────── email notification (unchanged) ────── */
        try {
            $store = Store::find($request->store_id);
            $adminEmail = config('mail.admin_email');
            $storeEmail = $store->email ?? null;
            $recipients = array_filter([$adminEmail, $storeEmail]);

            foreach ($recipients as $recipient) {
                Mail::to($recipient)->send(new \App\Mail\StockAdjustmentNotification(
                    $adjustment,
                    $store,
                    $stock,
                    $userName
                ));
            }
        } catch (\Exception $mailEx) {
            \Log::error('Stock adjustment email failed: ' . $mailEx->getMessage());
        }

        return back()->with(
            'success',
            $adjustment->adjustment_type === StockAdjustment::TYPE_ADD
                ? 'Stock quantity added successfully!'
                : 'Stock adjustment created and quantity updated!'
        );
    }

    public function edit($id)
    {
        $adjustment = StockAdjustment::findOrFail($id);
        $stores = Store::all(['id', 'name']);
        $products = Stock::where('store_id', $adjustment->store_id)
            ->get(['id', 'product_name']);

        // Return data as JSON
        return response()->json([
            'adjustment' => $adjustment,
            'stores' => $stores,
            'products' => $products,
        ]);
    }

    // public function update(Request $request, $id)
    // {
    //     $adjustment = StockAdjustment::findOrFail($id);
    //     $validatedData = $request->validate([
    //         'store_id' => 'required|integer|exists:stores,id',
    //         'product_id' => 'required|string',
    //         'adjust_quantity' => 'required|integer|min:1',
    //         'reason' => 'required|string',
    //         'adjust_date' => 'required|date',
    //     ]);

    //     $validatedData['adjust_date'] = date('Y-m-d', strtotime($validatedData['adjust_date']));

    //     // Find stock record
    //     $stock = Stock::where('store_id', $request->store_id)
    //         ->where('product_id', $request->product_id)
    //         ->first();

    //     if (!$stock) {
    //         return redirect()->back()->with('error', 'Stock not found for the selected product and store.');
    //     }

    //     // Revert the previous adjustment
    //     $stock->quantity += $adjustment->adjust_quantity;

    //     // Check if new adjustment is valid
    //     if ($stock->quantity < $validatedData['adjust_quantity']) {
    //         return redirect()->back()->with('error', 'Insufficient stock for the new adjustment quantity.');
    //     }

    //     // Apply new adjustment
    //     $stock->quantity -= $validatedData['adjust_quantity'];
    //     $stock->save();

    //     // NOTIFICATION
    //     $userId = auth()->id();
    //     $message = 'Stock Adjustment has been updated';  // Custom message
    //     $userName = auth()->user()->name;
    //     $port = env('NOTIFICATION_PORT');
    //     $url = "http://localhost:$port/notify";
    //     $response = Http::post($url, [
    //         'userId' => $userId,
    //         'message' => $message,
    //         'user_name' => $userName,  // Send the user's name
    //     ]);
    //     NotificationHelper::add(auth()->id(), $message);
    //     // Update adjustment record
    //     $adjustment->update($validatedData);

    //     return redirect()->back()->with('success', 'Stock adjustment and stock quantity updated successfully!');
    // }
    public function update(Request $request, $id)
    {
        if (!hasPermission('update', 'stock_adjustment')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }
        $adjustment = StockAdjustment::findOrFail($id);

        $validatedData = $request->validate([
            'store_id' => 'required|integer|exists:stores,id',
            'product_id' => 'required|string',
            'adjust_quantity' => 'required|integer|min:1',
            'adjustment_type' => 'required|in:add,remove',
            'adjustment_account_id' => [
                'required_if:adjustment_type,remove',
                'nullable',
                'integer',
                Rule::exists('gl_accounts', 'id')->where(
                    fn ($query) => $query->where('is_active', true)->where('type', 'expense')
                ),
            ],
            'reason' => 'nullable|required_if:adjustment_type,remove|string',
            'adjust_date' => 'required|app_date',
        ]);
        $validatedData['adjust_date'] = parse_date_input_to_db($validatedData['adjust_date']);
        $validatedData['adjust_date'] = parse_date_input_to_db($validatedData['adjust_date']);
        $validatedData['adjustment_account_id'] = $validatedData['adjustment_type'] === StockAdjustment::TYPE_REMOVE
            ? $validatedData['adjustment_account_id']
            : null;

        $stock = Stock::where('store_id', $request->store_id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$stock) {
            return back()->with('error', 'Stock not found for the selected product and store.');
        }

        $unitCost = (float) $adjustment->unit_cost > 0
            ? round((float) $adjustment->unit_cost, 2)
            : round(app(InventoryValuationService::class)->suggestedAdjustmentUnitCost(
                (int) $request->store_id,
                (string) $request->product_id,
                (float) $request->adjust_quantity,
                (string) $request->adjustment_type,
            ), 2);
        if ($unitCost <= 0) {
            return back()->with('error', 'A default unit cost could not be determined for this product and store.');
        }
        $validatedData['unit_cost'] = $unitCost;

        $oldAdjustment = $adjustment->toArray();
        $oldStock = $stock->toArray();

        DB::beginTransaction();
        try {
            if ($adjustment->adjustment_type === StockAdjustment::TYPE_ADD) {
                $stock->decrement('quantity', $adjustment->adjust_quantity);
            } else {
                $stock->increment('quantity', $adjustment->adjust_quantity);
            }

            if ($validatedData['adjustment_type'] === StockAdjustment::TYPE_ADD) {
                $stock->increment('quantity', $validatedData['adjust_quantity']);
            } else {
                if ($stock->quantity < $validatedData['adjust_quantity']) {
                    throw new \RuntimeException('Insufficient stock for the new adjustment quantity.');
                }
                $stock->decrement('quantity', $validatedData['adjust_quantity']);
            }
            $stock->save();
            app(ProductUnitCostService::class)->syncStock($stock->fresh());

            $adjustment->update($validatedData);

            $journal = app(AccountingService::class)->postStockAdjustment($adjustment->fresh());
            if (! $journal) {
                throw new \RuntimeException('The stock adjustment journal could not be posted.');
            }

            $newAdjustment = $adjustment->fresh()->toArray();
            $newStock = $stock->fresh()->toArray();

            storeHistory(
                'stock_adjustment_form',
                'updated',
                'stock_adjustment',
                $adjustment->id,
                ['adjustment' => $newAdjustment, 'stock' => $newStock],
                ['adjustment' => $oldAdjustment, 'stock' => $oldStock]
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }

        $userId = auth()->id();
        $message = 'Stock adjustment has been updated';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);

        return back()->with('success', 'Stock adjustment and stock quantity updated successfully!');
    }

    // public function destroy($id)
    // {
    //     $adjustment = StockAdjustment::findOrFail($id);
    //     $adjustment->delete();
    //     $userId = auth()->id();
    //     $message = 'A Stock Adjustment has been Deleted';  // Custom message
    //     $userName = auth()->user()->name;
    //     $port = env('NOTIFICATION_PORT');
    //     $url = "http://localhost:$port/notify";
    //     $response = Http::post($url, [
    //         'userId' => $userId,
    //         'message' => $message,
    //         'user_name' => $userName,  // Send the user's name
    //     ]);
    //     NotificationHelper::add(auth()->id(), $message);
    //     return response()->json(['success' => 'Stock adjustment deleted successfully!']);
    // }
    public function destroy(Request $request, $id)
    {
        if (!hasPermission('delete', 'stock_adjustment')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }
        $adjustment = StockAdjustment::findOrFail($id);

        // Try to find related stock
        $stock = Stock::where('store_id', $adjustment->store_id)
            ->where('product_id', $adjustment->product_id)
            ->first();

        $userId = auth()->id();

        if (!$stock) {
            $oldAdjustment = $adjustment->toArray();

            DB::transaction(function () use ($adjustment, $oldAdjustment) {
                app(AccountingService::class)->deletePosting('stock_adjustment', (int) $adjustment->id);
                storeHistory(
                    'stock_adjustment_form',
                    'deleted',
                    'stock_adjustment',
                    $adjustment->id,
                    null,
                    ['adjustment' => $oldAdjustment, 'stock' => null]
                );
                $adjustment->delete();
            });

            $message = 'Stock adjustment deleted, but related stock item was not found in inventory.';
            NotificationHelper::add($userId, $message);
            NotificationHelper::pushRealtime($userId, $message);

            return response()->json([
                'success' => true,
                'message' => 'Adjustment deleted, but related stock not found.',
                'warning' => true,
            ]);
        }

        // If stock found, proceed as normal
        $oldAdjustment = $adjustment->toArray();
        $oldStock = $stock->toArray();

        DB::transaction(function () use ($adjustment, $stock, $oldAdjustment, $oldStock) {
            if ($adjustment->adjustment_type === StockAdjustment::TYPE_ADD) {
                $stock->decrement('quantity', $adjustment->adjust_quantity);
            } else {
                $stock->increment('quantity', $adjustment->adjust_quantity);
            }
            app(ProductUnitCostService::class)->syncStock($stock->fresh());
            $newStock = $stock->fresh()->toArray();

            app(AccountingService::class)->deletePosting('stock_adjustment', (int) $adjustment->id);
            storeHistory(
                'stock_adjustment_form',
                'deleted',
                'stock_adjustment',
                $adjustment->id,
                ['stock' => $newStock],
                ['adjustment' => $oldAdjustment, 'stock' => $oldStock]
            );
            $adjustment->delete();
        });

        $message = 'Stock adjustment deleted and quantity restored.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);

        return response()->json([
            'success' => true,
            'message' => 'Stock adjustment deleted and quantity restored.',
        ]);
    }

    public function getAdjustmentData(Request $request, $id)
    {
        $id = $id ?: $request->id;

        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'No ID provided!',
            ]);
        }

        $adjustment = StockAdjustment::with(['store', 'stock'])
            ->where('id', $id)
            ->first();

        if (!$adjustment) {
            return response()->json([
                'success' => false,
                'message' => 'Adjustment not found!',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $adjustment->id,
                'store_id' => $adjustment->store_id,
                'store_name' => $adjustment->store->name,
                'product_id' => $adjustment->product_id,
                'product_name' => $adjustment->stock->product_name,
                'adjust_quantity' => $adjustment->adjust_quantity,
                'unit_cost' => $adjustment->unit_cost,
                'adjustment_account_id' => $adjustment->adjustment_account_id,
                'adjustment_type' => $adjustment->adjustment_type,
                'reason' => $adjustment->reason,
                'adjust_date' => $adjustment->adjust_date,
            ],
        ]);
    }

    public function unitCost(Request $request)
    {
        $data = $request->validate([
            'store_id' => 'required|integer|exists:stores,id',
            'product_id' => 'required|string',
            'quantity' => 'nullable|numeric|min:1',
            'adjustment_type' => 'nullable|in:add,remove',
        ]);

        $valuation = app(InventoryValuationService::class);
        $type = $data['adjustment_type'] ?? StockAdjustment::TYPE_REMOVE;
        $unitCost = $valuation->suggestedAdjustmentUnitCost(
            (int) $data['store_id'],
            (string) $data['product_id'],
            (float) ($data['quantity'] ?? 1),
            $type,
        );

        return response()->json([
            'unit_cost' => $unitCost,
            'method' => $valuation->method(),
            'method_label' => $valuation->methodLabel(),
        ]);
    }

    public function exportStockAdjustmentData()
    {
        $data = DB::table('stock_adjustments as sa')
            ->leftJoin('stocks as s', 'sa.product_id', '=', 's.product_id')
            ->leftJoin('stores as st', 'sa.store_id', '=', 'st.id')
            ->leftJoin('categories as cat', 's.category_id', '=', 'cat.id')
            ->leftJoin('units as unit', 's.unit_id', '=', 'unit.id')
            ->select([
                's.product_name',
                'sa.product_id',
                's.sku',
                'cat.name as category_name',
                'unit.name as unit_name',
                'st.name as store_name',
                'sa.adjustment_type',
                'sa.adjust_quantity',
                'sa.unit_cost',
                'sa.reason',
                'sa.adjust_date',
            ])
            ->get();

        $filename = 'stock_adjustments_export_' . date('Ymd_His') . '.csv';

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
            'SKU',
            'Category',
            'Unit',
            'Store',
            'Adjustment Type',
            'Adjusted Quantity',
            'Unit Cost',
            'Reason',
            'Adjustment Date',
        ];

        $callback = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->product_name,
                    $row->product_id,
                    $row->sku,
                    $row->category_name,
                    $row->unit_name,
                    $row->store_name,
                    ucfirst($row->adjustment_type),
                    $row->adjust_quantity,
                    $row->unit_cost,
                    $row->reason,
                    format_date($row->adjust_date),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UnitController extends Controller
{
    // Display Unit List with Search
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Unit::query();
            if ($request->has('status') && $request->status !== 'choose') {
                $query->where('status', $request->status);
            }
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            }
            $unit = $query->get();
            return datatables()
                ->of($query)
                ->addColumn('unit_name', fn($row) => $row->name)
                ->addColumn('status_badge', function ($store) {
                    if ($store->status == 1) {
                        return '<span class="badge badge-linesuccess">Active</span>';
                    } else {
                        return '<span class="badge badge-linedanger">Inactive</span>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    return '
                        
                            <button
                                class="btn btn-sm btn-primary edit-category"
                                data-id="' . $row->id . '"
                            >
                                Edit
                            </button>
                            <button
                                class="btn btn-sm btn-danger delete-category"
                                data-id="' . $row->id . '"
                            >
                            Delete
                            </button>
                        ';
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }

        return view('admin.unit-list');
    }

    // Store a New Unit
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $unit = new Unit();
        $unit->name = $request->name;
        $unit->slug = Str::slug($request->name);
        // Product page does not send status; omitted status used to save Inactive.
        // $unit->status = $request->status ?? 0;
        $unit->status = $request->exists('status') ? (int) $request->input('status') : 1;
        $unit->save();
        $userId = auth()->id();
        $message = 'New Unit (' . $unit->name . ') has been created.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);
        return response()->json(['message' => 'Unit created successfully', 'status' => 'success', 'unit' => $unit]);
    }

    // Get Unit for Editing
    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        return response()->json($unit);
    }

    // Update Existing Unit
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $unit = Unit::findOrFail($id);
        $unit->name = $request->name;
        $unit->slug = Str::slug($request->name);
        $unit->status = $request->status ?? 0;
        $unit->save();
        $userId = auth()->id();
        $message = 'Unit (' . $unit->name . ') has been updated.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);

        return response()->json(['message' => 'Unit updated successfully', 'status' => 'success']);
    }

    // Delete Unit
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();
        $userId = auth()->id();
        $message = 'Unit (' . $unit->name . ') has been deleted.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);

        return response()->json(['message' => 'Unit deleted successfully', 'status' => 'success']);
    }
}

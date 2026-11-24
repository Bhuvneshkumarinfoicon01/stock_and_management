<?php

namespace App\Http\Controllers\admin;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Category::query();

            if ($request->has('status') && $request->status !== 'choose') {
                $query->where('status', $request->status);
            }
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            }

            return datatables()
                ->eloquent($query)
                ->addColumn('category_name', fn($row) => $row->name)
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

        return view('admin.category-list');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        // $category = new Category();
        // $category->name = $request->name;
        // $category->slug = Str::slug($request->name);
        // $category->status = $request->status ?? 0;
        // $category->save();
        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            // Product page does not send status; omitted status used to save Inactive.
            // 'status' => $request->status ?? 0,
            'status' => $request->exists('status') ? (int) $request->input('status') : 1,
        ]);

        /* ─── audit trail ─── */
        storeHistory(
            'category_form',  // key
            'created',  // event
            'category',  // entity type
            $category->id,  // entity id
            $category->toArray(),
            null
        );

        $userId = auth()->id();
        $message = 'New category (' . $category->name . ') has been added.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);

        return response()->json(['message' => 'Category created successfully', 'status' => 'success', 'category' => $category]);
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $category = Category::findOrFail($id);
        $oldPayload = $category->toArray();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->status = $request->status ?? 0;
        $category->save();

        $newPayload = $category->toArray();
        storeHistory(
            'category_form',
            'updated',
            'category',
            $category->id,
            $newPayload,
            $oldPayload
        );

        $userId = auth()->id();
        $message = 'Category (' . $category->name . ') has been updated.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);

        return response()->json(['message' => 'Category updated successfully', 'status' => 'success']);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $oldPayload = $category->toArray();
        $category->delete();

        storeHistory(
            'category_form',
            'deleted',
            'category',
            $id,
            [],
            $oldPayload
        );

        $userId = auth()->id();
        $message = 'Category (' . $category->name . ') has been Deleted.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);

        return response()->json(['message' => 'Category deleted successfully', 'status' => 'success']);
    }
}
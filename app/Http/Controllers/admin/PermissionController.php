<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\StoreEmployee;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function savePermissions(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'permissions' => 'array',
        ]);

        $permissionIds = array_filter($request->permissions ?? [], function ($id) {
            return is_numeric($id) && $id > 0;
        });

        if ($request->filled('user_id')) {
            $request->validate(['user_id' => 'required|exists:users,id']);
            $user = User::findOrFail($request->user_id);
            $user->permissions()->sync($permissionIds);
        } elseif ($request->filled('storeUser_id')) {
            $request->validate(['storeUser_id' => 'required|exists:store_employees,id']);
            $storeUser = StoreEmployee::findOrFail($request->storeUser_id);
            $storeUser->permissions()->sync($permissionIds);
        } else {
            return response()->json(['success' => false, 'message' => 'User ID or Store User ID is required.'], 422);
        }

        return response()->json(['success' => true]);
    }

    public function getPermissions($id)
    {
        $user = User::with('permissions')->findOrFail($id);
        $permissions = Permission::all()->groupBy('module');

        return response()->json([
            'role_id' => $user->role_id,  // send role_id to the frontend
            'permissions' => $permissions,
            'user_permissions' => $user->permissions->pluck('id')->toArray(),
        ]);
    }
}

<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('hasPermission')) {
    function hasPermission($action, $module)
    {
        $user = Auth::user();

        if (!$user || $user->role_id == 1) {
            return true;  // Super Admin has all permissions
        }

        if (!$user->permissions) {
            return false;
        }

        return $user->permissions->contains(function ($permission) use ($action, $module) {
            return $permission->action === $action && $permission->module === $module;
        });
    }
}

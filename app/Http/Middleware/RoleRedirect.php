<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Closure;

class RoleRedirect
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
            // return redirect()->route('login_view');
        }
        $user = Auth::user();

        // Get the intended dashboard route based on role
        $dashboardRoute = match ($user->role_id) {
            1 => route('admin.superadmin_dashboard'),
            2 => route('admin.dashboard'),
            3 => route('admin.storemanager_dashboard'),
            5 => route('admin.clerk_dashboard'),
            default => route('login_view'),
        };

        // ✅ Only redirect if user is NOT already on their dashboard
        if ($request->url() !== $dashboardRoute) {
            return redirect($dashboardRoute);
        }

        return $next($request);
    }
}

<?php
use Illuminate\Support\Facades\Auth;

if (!function_exists('hasSidebarLink')) {
    function hasSidebarLink(string $linkKey): bool
    {
        $user = Auth::user();

        if (!$user || $user->role_id != 5) {
            // Only applies to clerks (role_id = 5)
            return true;
        }

        return $user->sidebarLinks->contains('link_key', $linkKey);
    }
}

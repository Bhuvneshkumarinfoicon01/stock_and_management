<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\SidebarLink;
use Illuminate\Http\Request;

class SidebarLinksController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'clerk_id' => 'required|exists:users,id',
            'links' => 'array'
        ]);

        // Delete old links for this clerk
        SidebarLink::where('clerk_id', $request->clerk_id)->delete();

        // Insert new links
        if (!empty($request->links)) {
            foreach ($request->links as $link) {
                SidebarLink::create([
                    'clerk_id' => $request->clerk_id,
                    'link_key' => $link,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Sidebar links updated successfully'
        ]);
    }

    public function fetch(Request $request)
    {
        $request->validate([
            'clerk_id' => 'required|exists:users,id',
        ]);

        $links = SidebarLink::where('clerk_id', $request->clerk_id)
            ->pluck('link_key')
            ->toArray();

        return response()->json([
            'status' => 'success',
            'links' => $links
        ]);
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Message;  // Import the Message model
use App\Models\Notification;  // Import the Notification model
use App\Models\Store;
use App\Models\User;  // Import the User model
use Illuminate\Http\Request;

class CommunicationController extends Controller
{
    public function communications()
    {
        $roleId = auth()->user()->role_id;  // Get the logged-in user's role ID

        // Determine which users to fetch based on the role
        if ($roleId == 1) {  // Super Admin
            $users = User::whereIn('role_id', [2])  // Fetch Admins
                ->where('id', '!=', auth()->id())  // Exclude the logged-in user
                ->get();
        } elseif ($roleId == 2) {  // Admin
            $users = User::whereIn('role_id', [1, 3, 5])  // Fetch Super Admins and Store Managers
                ->where('id', '!=', auth()->id())  // Exclude the logged-in user
                ->get();
        } elseif ($roleId == 3) {  // Store Manager
            $users = User::whereIn('role_id', [2, 3, 5])  // Fetch Admins and Store Managers
                ->where('id', '!=', auth()->id())  // Exclude the logged-in user
                ->get();
        } elseif ($roleId == 5) {  // Clerk
            $users = User::whereIn('role_id', [2, 3])  // Fetch Admins and Store Managers
                ->where('id', '!=', auth()->id())  // Exclude the logged-in user
                ->get();
        } else {
            $users = collect();  // Empty collection if the role is not recognized
        }

        return view('admin.stor2storecomm', compact('users', 'roleId'));
    }

    public function communicationSuperAdmin()
    {
        $users = User::whereIn('role_id', [2, 3])  // Fetch Admins and Store Managers
            ->where('id', '!=', auth()->id())  // Exclude the logged-in user
            ->get();

        // Old view:
        // return view('SuperAdmin.stor2storecomm', compact('users'));
        // Reuse dynamic chat view with message pagination + infinite scroll.
        return view('admin.stor2storecomm', compact('users'));
    }

    public function communicationStoreManager()
    {
        $users = User::whereIn('role_id', [1, 2])  // Fetch Super Admins and Admins
            ->where('id', '!=', auth()->id())  // Exclude the logged-in user
            ->get();

        // Old view:
        // return view('storemanager.stor2storecomm', compact('users'));
        // Reuse dynamic chat view with message pagination + infinite scroll.
        return view('admin.stor2storecomm', compact('users'));
    }

    public function fetchMessages(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'before_id' => 'nullable|integer',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $limit = $validated['limit'] ?? 30;

        $query = Message::where(function ($query) use ($validated) {
            $query
                ->where('sender_id', auth()->id())
                ->where('receiver_id', $validated['user_id']);
        })->orWhere(function ($query) use ($validated) {
            $query
                ->where('sender_id', $validated['user_id'])
                ->where('receiver_id', auth()->id());
        });

        if (!empty($validated['before_id'])) {
            $query->where('id', '<', (int) $validated['before_id']);
        }

        // Get latest chunk first, then return in ascending order for rendering.
        $messages = $query
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get()
            ->sortBy('id')
            ->values();

        $hasMore = false;
        if ($messages->isNotEmpty()) {
            $oldestId = $messages->first()->id;
            $hasMore = Message::where(function ($query) use ($validated) {
                $query
                    ->where('sender_id', auth()->id())
                    ->where('receiver_id', $validated['user_id']);
            })->orWhere(function ($query) use ($validated) {
                $query
                    ->where('sender_id', $validated['user_id'])
                    ->where('receiver_id', auth()->id());
            })->where('id', '<', $oldestId)->exists();
        }

        return response()->json([
            'messages' => $messages,
            'has_more' => $hasMore,
        ]);
    }

    public function storeMessage(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        // Save the message in the messages table
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'content' => $validated['message'],
        ]);

        return response()->json(['status' => 'success', 'message' => $message]);
    }

    public function fetchUnreadMessages()
    {
        $unreadMessages = Message::select('sender_id', \DB::raw('COUNT(*) as unread_count'))
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->groupBy('sender_id')
            ->get();

        return response()->json(['unreadMessages' => $unreadMessages]);
    }

    public function markMessagesAsRead(Request $request)
    {
        Message::where('receiver_id', auth()->id())
            ->where('sender_id', $request->user_id)
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }
}

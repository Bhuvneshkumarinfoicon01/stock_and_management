<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        $userId = $request->input('userId');
        $message = $request->input('message');
        \App\Helpers\NotificationHelper::pushRealtime($userId, $message);

        return response()->json(['status' => 'Notification sent']);
    }

    public function fetchUserNotifications()
    {
        $userId = auth()->id();

        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                return [
                    'user_name' => auth()->user()->name,  // Or store in DB if needed
                    'notification_text' => $notification->message,
                    'time_ago' => Carbon::parse($notification->created_at)->diffForHumans(),
                ];
            });

        return response()->json($notifications);
    }

    public function clear()
    {
        $userId = auth()->id();

        Notification::where('user_id', $userId)->delete();

        return response()->json(['status' => 'success', 'message' => 'Notifications cleared.']);
    }
}

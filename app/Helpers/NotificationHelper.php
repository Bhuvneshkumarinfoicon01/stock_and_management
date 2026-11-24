<?php

namespace App\Helpers;

use App\Models\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationHelper
{
    /**
     * Store notification in database (in-app).
     */
    public static function add($userId, $message)
    {
        Notification::create([
            'user_id' => $userId,
            'message' => $message,
        ]);
    }

    /**
     * Send real-time push to Node server (Socket.IO). Bypasses silently if server is down.
     */
    public static function pushRealtime($userId, $message, $userName = null): void
    {
        try {
            $userName = $userName ?? auth()->user()?->name ?? 'System';
            Http::timeout(2)->post(config('services.notification_url') . '/notify', [
                'userId' => $userId,
                'message' => $message,
                'user_name' => $userName,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Realtime notify failed (notification server may be down): ' . $e->getMessage());
        }
    }
}
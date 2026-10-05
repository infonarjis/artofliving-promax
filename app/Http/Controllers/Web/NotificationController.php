<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MemberNotification;

class NotificationController extends Controller
{
    public function markRead($id)
    {
        $memberId = auth()->guard('web')->id();

        $notification = MemberNotification::active()
            ->where('id', $id)
            ->where('receiver_member_id', $memberId)
            ->first();

        if ($notification && !$notification->is_read) {
            $notification->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);
        }

        $unreadCount = MemberNotification::active()
            ->unread()
            ->where('receiver_member_id', $memberId)
            ->count();

        return response()->json([
            'status'       => true,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAllRead()
    {
        $memberId = auth()->guard('web')->id();
        MemberNotification::active()
            ->unread()
            ->where('receiver_member_id', $memberId)
            ->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);

        return response()->json([
            'status'       => true,
            'unread_count' => 0,
        ]);
    }
}

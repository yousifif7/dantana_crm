<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function create(User $user, string $type, string $title, string $message, ?array $data = null)
    {
        return DB::table('notifications')->insert([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data ? json_encode($data) : null,
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function markAsRead(int $notificationId)
    {
        return DB::table('notifications')
            ->where('id', $notificationId)
            ->update([
                'is_read' => true,
                'read_at' => now(),
                'updated_at' => now(),
            ]);
    }

    public function getUserNotifications(User $user, bool $unreadOnly = false)
    {
        $query = DB::table('notifications')
            ->where('user_id', $user->id);

        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        return $query->latest()->get();
    }

    public function getUnreadCount(User $user): int
    {
        return DB::table('notifications')
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }
}
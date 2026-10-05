<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class AdminNotification extends Model
{
    use SoftDeletes;

    protected $table = 'admin_notifications';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public const ADMIN_TYPE_ADMIN     = 'admin';
    public const ADMIN_TYPE_STAFF     = 'staff';
    public const ADMIN_TYPE_FRANCHISE = 'franchise';

    protected $fillable = [
        'admin_id',
        'admin_type',
        'member_id',
        'matri_id',
        'title',
        'message',
        'action',
        'image',
        'is_read',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        $clearCache = function (self $notification) {

            if (!empty($notification->admin_type) && !empty($notification->admin_id)) {
                self::clearNotificationCache(
                    $notification->admin_type,
                    $notification->admin_id
                );
            }
        };

        static::created($clearCache);
        static::updated($clearCache);
        static::deleted($clearCache);
    }

    /**
     * Clear notification cache for a specific user.
     */
    public static function clearNotificationCache(string $userType, int $userId): void
    {
        Cache::forget("admin_notification_unread_count_{$userType}_{$userId}");
        Cache::forget("admin_notification_list_{$userType}_{$userId}");
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeUnread($query)
    {
        return $query->where('is_read', 0);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', 1);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeForUser($query, string $userType, int $userId)
    {
        return $query->where('admin_type', $userType)
            ->where('admin_id', $userId);
    }
}

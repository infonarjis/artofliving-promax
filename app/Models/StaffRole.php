<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class StaffRole extends Model
{
    use SoftDeletes;

    protected $table = 'staff_role';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'role_name',
        'view_member',
        'add_member',
        'edit_member',
        'delete_member',
        'view_profile',
        'approve_member',
        'match_making',
        'unapprove_member',
        'send_bulk_email',
        'send_bulk_notification',
        'horoscope_approval',
        'photo_approval',
        'expired_member',
        'add_comment',
        'view_comment',
        'add_lead_generation',
        'edit_lead_generation',
        'view_lead_generation',
        'delete_lead_generation',
        'lead_generation_add_comment',
        'lead_generation_view_comment',
        'selfie_photo_approval',
        'selfie_photo_delete',
        'photo_delete',
        'horoscope_delete',
        'id_proof_approval',
        'id_proof_delete',
        'suspend_member',
        'personalized_member',
        'personalized_chat',
        'lead_generation_convert_member',
        'lead_import',
        'active_to_paid_member',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Cache Prefixes
    |--------------------------------------------------------------------------
    */

    public const STAFF_ROLE_CACHE_PREFIX     = 'staff_roles_permissions';
    public const FRANCHISE_ROLE_CACHE_PREFIX = 'franchise_roles_permissions';

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::saved(function (StaffRole $model) {
            self::clearRelatedCache();
        });

        static::deleted(function (StaffRole $model) {
            self::clearRelatedCache();
        });

        static::restored(function (StaffRole $model) {
            self::clearRelatedCache();
        });

        static::forceDeleted(function (StaffRole $model) {
            self::clearRelatedCache();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Clear All Related Cache
    |--------------------------------------------------------------------------
    */

    protected static function clearRelatedCache(): void
    {
        $cacheKeys = [
            self::STAFF_ROLE_CACHE_PREFIX,
            self::FRANCHISE_ROLE_CACHE_PREFIX,
        ];

        foreach ($cacheKeys as $cacheKey) {
            self::forgetCacheFromAllStores($cacheKey);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Forget Cache From Redis + File + Default Store
    |--------------------------------------------------------------------------
    */

    protected static function forgetCacheFromAllStores(string $key): void
    {
        $stores = [
            config('cache.default'),
            'redis',
            'file',
        ];

        foreach (array_unique($stores) as $store) {
            try {
                Cache::store($store)->forget($key);
            } catch (\Throwable $e) {
                // Ignore unavailable cache stores
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Cache Key Helpers
    |--------------------------------------------------------------------------
    */

    public static function staffRoleCacheKey(): string
    {
        return self::STAFF_ROLE_CACHE_PREFIX;
    }

    public static function franchiseRoleCacheKey(): string
    {
        return self::FRANCHISE_ROLE_CACHE_PREFIX;
    }

    /*
    |--------------------------------------------------------------------------
    | Common Enum Values
    |--------------------------------------------------------------------------
    */

    public const SCOPE_ALL  = 'All Members';
    public const SCOPE_OWN  = 'Own Members';
    public const SCOPE_NONE = 'No';

    public const YES = 'Yes';
    public const NO  = 'No';

    /*
    |--------------------------------------------------------------------------
    | Permission Helpers
    |--------------------------------------------------------------------------
    */

    public function canAll(string $column): bool
    {
        return $this->{$column} === self::SCOPE_ALL;
    }

    public function canOwn(string $column): bool
    {
        return $this->{$column} === self::SCOPE_OWN;
    }

    public function isNo(string $column): bool
    {
        return $this->{$column} === self::SCOPE_NONE;
    }

    public function isYes(string $column): bool
    {
        return $this->{$column} === self::YES;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class MemberDefaultPlaceholder extends Model
{
    use SoftDeletes;

    protected $table = 'member_default_placeholders';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'male_public_image',
        'female_public_image',
        'male_protected_image',
        'female_protected_image',
        'status',
    ];

    const CACHE_KEY = 'member_default_placeholders';

    protected static function booted()
    {
        static::saving(fn() => Cache::forget(self::CACHE_KEY));
        static::deleting(fn() => Cache::forget(self::CACHE_KEY));
        static::restoring(fn() => Cache::forget(self::CACHE_KEY));
    }

    /**
     * Clear cache
     */
    public static function clearApprovedCache()
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get cached single row
     */
    public static function getCached()
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return self::approved()->first();
        });
    }

    /**
     * Scope Approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }
}

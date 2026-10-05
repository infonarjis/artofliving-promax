<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class MemberCartDesignLayout extends Model
{
    use SoftDeletes;
    protected $table = 'member_cart_design_layouts';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'preview_image',
        'status'
    ];

    protected $attributes = [
        'status'     => 'UNAPPROVED'
    ];

    const CACHE_KEY_APPROVED = 'member_cart_design_layout_approved';

    /**
     * Auto clear cache on any change
     */
    protected static function booted()
    {
        static::created(fn() => self::clearApprovedCache());
        static::updated(fn() => self::clearApprovedCache());
    }

    /**
     * Scope
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    /**
     * Get the only approved layout (cached)
     */
    public static function getApprovedCached()
    {
        return Cache::rememberForever(self::CACHE_KEY_APPROVED, function () {
            return self::active()->first();
        });
    }

    /**
     * Clear cache
     */
    public static function clearApprovedCache()
    {
        Cache::forget(self::CACHE_KEY_APPROVED);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class AdvertisementMaster extends Model
{
    use SoftDeletes;

    protected $table = 'advertisement_master';

    protected $fillable = [
        'adv_type',
        'link',
        'banner',
        'level',
        'google_adsense',
        'status',
    ];

    /*
    |---------------------------------------------------------
    | Cache Key
    |---------------------------------------------------------
    */
    public static function cacheKey($level)
    {
        return "advertisement_level_" . str_replace(' ', '_', strtolower($level));
    }

    /*
    |---------------------------------------------------------
    | Get Advertisement With Cache
    |---------------------------------------------------------
    */
    public static function getByLevel(string $level)
    {
        $cacheKey = self::cacheKey($level);

        if (!$cacheKey) {
            return [];
        }

        return Cache::rememberForever(
            $cacheKey,
            function () use ($level) {
                return self::query()
                    ->where('level', $level)
                    ->where('status', 'APPROVED')
                    ->get();
            }
        );
    }

    /*
    |---------------------------------------------------------
    | Clear Cache
    |---------------------------------------------------------
    */
    public static function clearLevelCache($level)
    {
        Cache::forget(self::cacheKey($level));
    }

    /*
    |---------------------------------------------------------
    | Auto clear cache on save/delete
    |---------------------------------------------------------
    */
    protected static function booted()
    {
        static::saved(function ($ad) {
            self::clearLevelCache($ad->level);
        });

        static::deleted(function ($ad) {
            self::clearLevelCache($ad->level);
        });
    }
}
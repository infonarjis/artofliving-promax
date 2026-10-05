<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarriedSisMaster extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'married_sis_masters';
    protected $primaryKey = 'id';

    protected $fillable = [
        'married_sis_name',
        'lang_code',
        'lang_id',
        'status'
    ];

    protected $casts = [
        'lang_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public $timestamps = true;

    /*
    |--------------------------------------------------------------------------
    | AUTO CACHE CLEAR (VERY IMPORTANT FIX)
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::saved(function ($model) {
            self::clearDropdownCache();
        });

        static::updated(function ($model) {
            self::clearDropdownCache();
        });
    }

    public static function clearDropdownCache()
    {
        Cache::forget('married_sis_dropdown');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeByLang($query, string $lang = 'en')
    {
        return $query->where('lang_code', $lang);
    }

    /*
    |--------------------------------------------------------------------------
    | Dropdown (PERFECT MULTI LANG + ORDERING)
    |--------------------------------------------------------------------------
    */
    public static function getDropdown($langCode = '')
    {
        $defaultLang = _getDefaultLanguage();
        $langCode = blank($langCode) ? $defaultLang : $langCode;

        $cacheKey = 'married_sis_dropdown_' . $langCode;

        return Cache::rememberForever($cacheKey, function () use ($langCode, $defaultLang) {

            // Step 1: Get base records sorted
            $base = self::active()
                ->where('lang_code', $defaultLang)
                ->orderBy('married_sis_name')
                ->pluck('married_sis_name', 'id');

            if ($langCode === $defaultLang) {
                return $base->toArray();
            }

            // Step 2: Get translations
            $translations = self::active()
                ->where('lang_code', $langCode)
                ->whereNotNull('lang_id')
                ->pluck('married_sis_name', 'lang_id');

            // Step 3: Merge with fallback
            $result = [];
            foreach ($base as $id => $name) {
                $result[$id] = $translations[$id] ?? $name;
            }

            // Step 4: Sort after translation applied
            asort($result);

            return $result;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Get name by language
    |--------------------------------------------------------------------------
    */
    public function getNameByLang($langCode = '')
    {
        $defaultLang = _getDefaultLanguage();

        if (blank($langCode) || $langCode === $defaultLang) {
            return $this->married_sis_name;
        }

        return self::active()
            ->where('lang_id', $this->id)
            ->where('lang_code', $langCode)
            ->value('married_sis_name')
            ?? $this->married_sis_name;
    }

    public function getTranslatedNameAttribute()
    {
        return $this->getNameByLang(app()->getLocale());
    }
}

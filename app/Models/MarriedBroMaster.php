<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarriedBroMaster extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'married_bro_masters';
    protected $primaryKey = 'id';

    protected $fillable = [
        'married_bro_name',
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
    | CACHE VERSIONING (Fixes multilingual cache bug permanently)
    |--------------------------------------------------------------------------
    */

    protected static function cacheVersion()
    {
        return Cache::get('married_bro_dropdown_version', 1);
    }

    protected static function bumpCacheVersion()
    {
        Cache::increment('married_bro_dropdown_version');
    }

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::saved(function () {
            self::bumpCacheVersion();
        });
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

    public function scopeByLang($query, string $lang)
    {
        return $query->where('lang_code', $lang);
    }

    /*
    |--------------------------------------------------------------------------
    | Dropdown Helper (Multilingual + Cached + Correct)
    |--------------------------------------------------------------------------
    */

    public static function getDropdown($langCode = '')
    {
        $defaultLang = _getDefaultLanguage();
        $langCode = blank($langCode) ? $defaultLang : $langCode;

        $version = self::cacheVersion();
        $cacheKey = "married_bro_dropdown_{$langCode}_v{$version}";

        return Cache::rememberForever($cacheKey, function () use ($langCode, $defaultLang) {

            // Default language base records
            $base = self::active()
                ->where('lang_code', $defaultLang)
                ->pluck('married_bro_name', 'id');

            // If same language, no need to map
            if ($langCode === $defaultLang) {
                return $base->sort()->toArray();
            }

            // Translations mapped by lang_id
            $translations = self::active()
                ->where('lang_code', $langCode)
                ->whereNotNull('lang_id')
                ->pluck('married_bro_name', 'lang_id');

            $result = [];

            foreach ($base as $id => $name) {
                $result[$id] = $translations[$id] ?? $name;
            }

            asort($result);

            return $result;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Get Name By Language (Cached, No N+1)
    |--------------------------------------------------------------------------
    */

    public function getNameByLang($langCode = '')
    {
        $defaultLang = _getDefaultLanguage();

        if (blank($langCode) || $langCode === $defaultLang) {
            return $this->married_bro_name;
        }

        $cacheKey = "married_bro_name_{$this->id}_{$langCode}";

        return Cache::rememberForever($cacheKey, function () use ($langCode) {
            return self::where([
                'lang_id'    => $this->id,
                'lang_code'  => $langCode,
                'status'     => 'APPROVED',
            ])->value('married_bro_name') ?? $this->married_bro_name;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor for translated name
    |--------------------------------------------------------------------------
    */

    public function getTranslatedNameAttribute()
    {
        return $this->getNameByLang(app()->getLocale());
    }
}
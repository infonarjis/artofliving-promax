<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class CityMaster extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'city_master';
    public $timestamps = false;

    protected $fillable = [
        'status', 'city_name', 'country_id', 'state_id', 'lang_code', 'lang_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Auto Cache Clear (Bulletproof)
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::saved(fn ($m) => self::clearDropdownCache($m));
        static::deleted(fn ($m) => self::clearDropdownCache($m));
    }

    private static function clearDropdownCache($model): void
    {
        $defaultLang = _getDefaultLanguage();

        // Clear for both default and translated language
        foreach ([$defaultLang, $model->lang_code] as $lang) {
            Cache::forget(self::dropdownCacheKey($model->state_id, $lang));
        }
    }

    private static function dropdownCacheKey($stateId, string $lang): string
    {
        if (is_array($stateId)) {
            sort($stateId);
            $stateId = implode('-', $stateId);
        }

        return "city_dropdown_{$stateId}_{$lang}";
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($q)
    {
        return $q->where('status', 'APPROVED');
    }

    public function scopeByState($q, $stateId)
    {
        return is_array($stateId)
            ? $q->whereIn('state_id', $stateId)
            : $q->where('state_id', $stateId);
    }

    public function scopeByCountry($q, $countryId)
    {
        return is_array($countryId)
            ? $q->whereIn('country_id', $countryId)
            : $q->where('country_id', $countryId);
    }

    public function scopeByLang($q, string $lang)
    {
        return $q->where('lang_code', $lang);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function country(): BelongsTo
    {
        return $this->belongsTo(CountryMaster::class, 'country_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(StateMaster::class, 'state_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Dropdown (Ultra Optimized + Correct Cache)
    |--------------------------------------------------------------------------
    */

    public static function getDropdown($stateId, $langCode = '')
    {
        $defaultLang = _getDefaultLanguage();
        $langCode = blank($langCode) ? $defaultLang : $langCode;

        $cacheKey = self::dropdownCacheKey($stateId, $langCode);

        return Cache::rememberForever($cacheKey, function () use ($stateId, $langCode, $defaultLang) {

            $cities = self::active()
                ->byState($stateId)
                ->whereIn('lang_code', [$defaultLang, $langCode])
                ->orderBy('city_name')
                ->get([
                    'id', 'city_name', 'lang_code', 'lang_id'
                ]);

            $base = [];
            $translations = [];

            foreach ($cities as $city) {
                if ($city->lang_code === $defaultLang) {
                    $base[$city->id] = $city->city_name;
                } elseif ($city->lang_id) {
                    $translations[$city->lang_id] = $city->city_name;
                }
            }

            foreach ($base as $id => $name) {
                $base[$id] = $translations[$id] ?? $name;
            }

            return $base;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Translation Helpers
    |--------------------------------------------------------------------------
    */

    public function getNameByLang($langCode = '')
    {
        $defaultLang = _getDefaultLanguage();

        if (blank($langCode) || $langCode === $defaultLang) {
            return $this->city_name;
        }

        return self::where([
            'lang_id'    => $this->id,
            'lang_code'  => $langCode,
            'status'     => 'APPROVED',
        ])->value('city_name') ?: $this->city_name;
    }

    public function getTranslatedNameAttribute()
    {
        return $this->getNameByLang(app()->getLocale());
    }
}
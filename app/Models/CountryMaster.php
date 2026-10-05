<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class CountryMaster extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'country_master';

    public $timestamps = false;

    protected $fillable = [
        'status',
        'country_name',
        'country_code',
        'lang_code',
        'is_top_country',
        'lang_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Events (Auto Clear Cache)
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::saved(
            fn($model) =>
            self::clearCacheKey('country_dropdown_' . $model->lang_code)
        );

        static::deleted(
            fn($model) =>
            self::clearCacheKey('country_dropdown_' . $model->lang_code)
        );
    }

    private static function clearCacheKey(string $key): void
    {
        foreach (['redis', 'file'] as $store) {
            try {
                Cache::store($store)->forget($key);
            } catch (\Throwable $e) {
                // ignore missing store
            }
        }

        Cache::forget($key);
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeByLang($query, string $lang = 'en')
    {
        return $query->where('lang_code', $lang);
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function states(): HasMany
    {
        return $this->hasMany(StateMaster::class, 'country_id');
    }

    public function cities(): HasMany
    {
        return $this->hasMany(CityMaster::class, 'country_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Register::class, 'country_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */
    public static function getDropdown($langCode = '')
    {
        $defaultLang = _getDefaultLanguage();

        if (blank($langCode)) {
            $langCode = $defaultLang;
        }

        $cacheKey = 'country_dropdown_' . $langCode;

        return Cache::rememberForever($cacheKey, function () use ($langCode, $defaultLang) {

            // Base records
            $base = self::active()
                ->where('lang_code', $defaultLang)
                ->select(
                    'id',
                    'country_name',
                    'is_top_country'
                )
                ->get()
                ->keyBy('id');

            if ($langCode === $defaultLang) {

                return $base
                    ->sortByDesc('is_top_country')
                    ->sortBy('country_name')
                    ->values();
            }

            // Translations
            $translations = self::active()
                ->where('lang_code', $langCode)
                ->whereNotNull('lang_id')
                ->pluck('country_name', 'lang_id');

            $result = [];

            foreach ($base as $id => $row) {

                $result[] = [
                    'id' => $id,

                    'country_name' =>
                    $translations[$id] ?? $row->country_name,

                    'is_top_country' =>
                    (int) $row->is_top_country,
                ];
            }

            return collect($result)
                ->sortByDesc('is_top_country')
                ->sortBy('country_name')
                ->values();
        });
    }

    public function getNameByLang($langCode = '')
    {
        $defaultLang = _getDefaultLanguage();

        if (blank($langCode) || $langCode === $defaultLang) {
            return $this->country_name;
        }

        $translated = self::where([
            'lang_id'   => $this->id,
            'lang_code' => $langCode,
            'status'    => 'APPROVED',
        ])->value('country_name');

        return $translated ?: $this->country_name;
    }

    public function getTranslatedNameAttribute()
    {
        $langCode = app()->getLocale(); // or session('lang_code');

        return $this->getNameByLang($langCode);
    }
}

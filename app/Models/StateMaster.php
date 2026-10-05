<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class StateMaster extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'state_master';

    public $timestamps = false;

    protected $fillable = [
        'status',
        'country_id',
        'state_name',
        'lang_code',
        'lang_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Events (Auto Clear Cache)
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget('state_dropdown_' . $model->country_id . '_' . $model->lang_code);
        });

        static::deleted(function ($model) {
            Cache::forget('state_dropdown_' . $model->country_id . '_' . $model->lang_code);
        });
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeByCountry($query, $countryId)
    {
        if (is_array($countryId)) {
            return $query->whereIn('country_id', $countryId);
        }

        return $query->where('country_id', $countryId);
    }

    public function scopeByLang($query, string $lang = 'en')
    {
        return $query->where('lang_code', $lang);
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function country(): BelongsTo
    {
        return $this->belongsTo(CountryMaster::class, 'country_id');
    }

    public function cities(): HasMany
    {
        return $this->hasMany(CityMaster::class, 'state_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Register::class, 'state_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public static function getDropdown(array $countryId, $langCode = '')
    {
        $defaultLang = _getDefaultLanguage();
        $langCode = blank($langCode) ? $defaultLang : $langCode;

        sort($countryId); // important for stable cache key
        $countryKey = implode('_', $countryId);

        $cacheKey = "state_dropdown_{$countryKey}_{$langCode}";

        return Cache::rememberForever($cacheKey, function () use ($countryId, $langCode, $defaultLang) {

            // Base (default language)
            $base = self::query()
                ->active()
                ->byCountry($countryId)
                ->where('lang_code', $defaultLang)
                ->pluck('state_name', 'id')
                ->toArray();

            if ($langCode === $defaultLang) {
                asort($base);
                return $base;
            }

            // Translations
            $translations = self::query()
                ->active()
                ->byCountry($countryId)
                ->where('lang_code', $langCode)
                ->whereNotNull('lang_id')
                ->pluck('state_name', 'lang_id')
                ->toArray();

            foreach ($base as $id => $name) {
                $base[$id] = $translations[$id] ?? $name;
            }

            asort($base);

            return $base;
        });
    }

    public function getNameByLang($langCode = '')
    {
        $defaultLang = _getDefaultLanguage();

        if (blank($langCode) || $langCode === $defaultLang) {
            return $this->state_name;
        }

        $translated = self::where([
            'lang_id'   => $this->id,
            'lang_code' => $langCode,
            'status'    => 'APPROVED',
        ])->value('state_name');

        return $translated ?: $this->state_name;
    }

    public function getTranslatedNameAttribute()
    {
        $langCode = app()->getLocale(); // or session('lang_code');

        return $this->getNameByLang($langCode);
    }
}

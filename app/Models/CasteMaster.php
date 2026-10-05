<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class CasteMaster extends Model
{
    use SoftDeletes;
    
    protected $table = 'caste_master';
    
    public $timestamps = false;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'status',
        'religion_id',
        'caste_name',
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
            self::clearDropdownCache($model->religion_id);
        });

        static::deleted(function ($model) {
            self::clearDropdownCache($model->religion_id);
        });
    }

    protected static function clearDropdownCache($religionId)
    {
        $languages = Cache::rememberForever('all_languages_codes', function () {
            return LanguageMaster::pluck('lang_code')->toArray();
        });

        foreach ($languages as $code) {
            Cache::forget("caste_dropdown_{$religionId}_{$code}");
        }
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }
    
    public function scopeByReligion($query, $religionId)
    {
        if (is_array($religionId)) {
            return $query->whereIn('religion_id', $religionId);
        }

        return $query->where('religion_id', $religionId);
    }

    public function scopeByLang($query, string $lang = 'en')
    {
        return $query->where('lang_code', $lang);
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function religion(): BelongsTo
    {
        return $this->belongsTo(ReligionMaster::class, 'religion_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Register::class, 'caste');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */
    public static function getDropdown(array $religionIds, $langCode = '')
    {
        $defaultLang = _getDefaultLanguage();
        $langCode = blank($langCode) ? $defaultLang : $langCode;

        sort($religionIds); // important for stable cache key
        $religionKey = implode('_', $religionIds);

        $cacheKey = "caste_dropdown_{$religionKey}_{$langCode}";

        return Cache::rememberForever($cacheKey, function () use ($religionIds, $langCode, $defaultLang) {

            // Base (default language)
            $base = self::query()
                ->active()
                ->whereIn('religion_id', $religionIds)
                ->where('lang_code', $defaultLang)
                ->pluck('caste_name', 'id')
                ->toArray();

            if ($langCode === $defaultLang) {
                asort($base);
                return $base;
            }
            
            // Translations
            $translations = self::query()
                ->active()
                ->whereIn('religion_id', $religionIds)
                ->where('lang_code', $langCode)
                ->whereNotNull('lang_id')
                ->pluck('caste_name', 'lang_id')
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
            return $this->caste_name;
        }

        $translated = self::where([
            'lang_id'   => $this->id,
            'lang_code' => $langCode,
            'status'    => 'APPROVED',
        ])->value('caste_name');

        return $translated ?: $this->caste_name;
    }

    public function getTranslatedNameAttribute()
    {
        $langCode = app()->getLocale(); // or session('lang_code');

        return $this->getNameByLang($langCode);
    }
}

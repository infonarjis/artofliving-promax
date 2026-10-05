<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class LanguageMaster extends Model
{
    use SoftDeletes;
    protected $table = 'language_master';

    protected $fillable = [
        'lang_name',
        'lang_code',
        'status',
        'is_default'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * =========================================
     * Global Scope (hide deleted languages)
     * =========================================
     */
    protected static function booted(): void
    {
        /**
         * Ensure ONLY one default language
         */
        static::saving(function ($model) {
            if ($model->is_default === 'Yes') {
                self::where('id', '!=', $model->id)
                    ->update(['is_default' => 'No']);
            }
        });

        /**
         * Auto clear cache when language changes
         */
        static::saved(function () {
            self::clearLanguageCache();
        });

        static::deleted(function () {
            self::clearLanguageCache();
        });
    }

    /**
     * =========================================
     * Query Scopes
     * =========================================
     */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', 'Yes');
    }

    /**
     * =========================================
     * Helper Methods
     * =========================================
     */

    public static function getActiveLanguages($limit = 10)
    {
        return Cache::remember('active_languages', 3600, function () use ($limit) {
            return self::active()
                ->orderByDesc('is_default')
                ->orderBy('lang_name')
                ->select('lang_name', 'lang_code', 'is_default')
                ->limit($limit)
                ->get();
        });
    }

    public static function getDefaultLanguageCode(): string
    {
        return Cache::remember('default_language', 3600, function () {
            $default = self::active()->default()->first();
            return $default?->lang_code ?? config('app.locale');
        });
    }

    public static function getByCode(string $code): ?self
    {
        return self::active()
            ->where('lang_code', $code)
            ->first();
    }

    /**
     * =========================================
     * Cache Clear Helper
     * =========================================
     */
    public static function clearLanguageCache(): void
    {
        Cache::forget('active_languages');
        Cache::forget('default_language');
    }
}
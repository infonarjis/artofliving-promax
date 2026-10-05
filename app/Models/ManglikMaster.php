<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManglikMaster extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'manglik_masters';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'manglik_name',
        'lang_code',
        'lang_id',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'lang_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Events (Auto Clear Cache)
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget('manglik_dropdown_' . $model->lang_code);
        });

        static::deleted(function ($model) {
            Cache::forget('manglik_dropdown_' . $model->lang_code);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeByLang(Builder $query, $langCode): Builder
    {
        return $query->where('lang_code', $langCode);
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
        $cacheKey = 'manglik_dropdown_' . $langCode;
        return Cache::rememberForever($cacheKey, function () use ($langCode, $defaultLang) {
            // Base records (default language IDs)
            $base = self::active()
                ->where('lang_code', $defaultLang)
                ->select('id', 'manglik_name')
                ->get()
                ->keyBy('id');
            if ($langCode === $defaultLang) {
                // Simple case
                $result = $base
                    ->pluck('manglik_name', 'id')
                    ->toArray();

                // Alphabetical ascending order
                asort($result, SORT_NATURAL | SORT_FLAG_CASE);

                return $result;
            }
            // Translations for requested language
            $translations = self::active()
                ->where('lang_code', $langCode)
                ->whereNotNull('lang_id')
                ->pluck('manglik_name', 'lang_id'); // key = base id
            $result = [];
            foreach ($base as $id => $row) {
                $result[$id] = $translations[$id] ?? $row->manglik_name;
            }
            asort($result); // keep alphabetical order by name
            return $result;
        });
    }

    public function getNameByLang($langCode = '')
    {
        $defaultLang = _getDefaultLanguage();

        if (blank($langCode) || $langCode === $defaultLang) {
            return $this->manglik_name;
        }

        $translated = self::where([
            'lang_id'   => $this->id,
            'lang_code' => $langCode,
            'status'    => 'APPROVED',
        ])->value('manglik_name');

        return $translated ?: $this->manglik_name;
    }

    public function getTranslatedNameAttribute()
    {
        $langCode = app()->getLocale(); // or session('lang_code');

        return $this->getNameByLang($langCode);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaritalStatusMaster extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'marital_status_masters';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'marital_status_name',
        'lang_code',
        'lang_id',
        'status'
    ];

    protected $casts = [
        'id' => 'integer',
        'lang_id' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget('marital_status_dropdown_' . $model->lang_code);
        });

        static::deleted(function ($model) {
            Cache::forget('marital_status_dropdown_' . $model->lang_code);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
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
        $cacheKey = 'marital_status_dropdown_' . $langCode;
        return Cache::rememberForever($cacheKey, function () use ($langCode, $defaultLang) {
            // Base records (default language IDs)
            $base = self::active()
                ->where('lang_code', $defaultLang)
                ->select('id', 'marital_status_name')
                ->get()
                ->keyBy('id');
            if ($langCode === $defaultLang) {
                // Simple case
                $result = $base
                    ->pluck('marital_status_name', 'id')
                    ->toArray();

                // Alphabetical ascending order
                asort($result, SORT_NATURAL | SORT_FLAG_CASE);

                return $result;
            }
            // Translations for requested language
            $translations = self::active()
                ->where('lang_code', $langCode)
                ->whereNotNull('lang_id')
                ->pluck('marital_status_name', 'lang_id'); // key = base id
            $result = [];
            foreach ($base as $id => $row) {
                $result[$id] = $translations[$id] ?? $row->marital_status_name;
            }
            asort($result); // keep alphabetical order by name
            return $result;
        });
    }

    public function getNameByLang($langCode = '')
    {
        $defaultLang = _getDefaultLanguage();

        if (blank($langCode) || $langCode === $defaultLang) {
            return $this->marital_status_name;
        }

        $translated = self::where([
            'lang_id'   => $this->id,
            'lang_code' => $langCode,
            'status'    => 'APPROVED',
        ])->value('marital_status_name');

        return $translated ?: $this->marital_status_name;
    }

    public function getTranslatedNameAttribute()
    {
        $langCode = app()->getLocale(); // or session('lang_code');

        return $this->getNameByLang($langCode);
    }
}

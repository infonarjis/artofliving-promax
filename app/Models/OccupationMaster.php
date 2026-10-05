<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class OccupationMaster extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'occupation_master';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'status',
        'occupation_name',
        'lang_code',
        'lang_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'lang_id' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Events (Auto Clear Cache)
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget('occupation_dropdown_'.$model->lang_code);
            Cache::forget('occupation_name_'.$model->id);
        });

        static::deleted(function ($model) {
            Cache::forget('occupation_dropdown_'.$model->lang_code);
            Cache::forget('occupation_name_'.$model->id);
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

    public function scopeByLang($query, string $lang = 'en')
    {
        return $query->where('lang_code', $lang);
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
        $cacheKey = 'occupation_dropdown_' . $langCode;
        return Cache::rememberForever($cacheKey, function () use ($langCode, $defaultLang) {
            // Base records (default language IDs)
            $base = self::active()
                ->where('lang_code', $defaultLang)
                ->select('id', 'occupation_name')
                ->get()
                ->keyBy('id');
            if ($langCode === $defaultLang) {
                // Simple case
                $result = $base
                    ->pluck('occupation_name', 'id')
                    ->toArray();

                // Alphabetical ascending order
                asort($result, SORT_NATURAL | SORT_FLAG_CASE);

                return $result;
            }
            // Translations for requested language
            $translations = self::active()
                ->where('lang_code', $langCode)
                ->whereNotNull('lang_id')
                ->pluck('occupation_name', 'lang_id'); // key = base id
            $result = [];
            foreach ($base as $id => $row) {
                $result[$id] = $translations[$id] ?? $row->occupation_name;
            }
            asort($result); // keep alphabetical order by name
            return $result;
        });
    }

    public function getNameByLang($langCode = '')
    {
        $defaultLang = _getDefaultLanguage();

        if (blank($langCode) || $langCode === $defaultLang) {
            return $this->occupation_name;
        }

        $translated = self::where([
            'lang_id'   => $this->id,
            'lang_code' => $langCode,
            'status'    => 'APPROVED',
        ])->value('occupation_name');

        return $translated ?: $this->occupation_name;
    }

    public function getTranslatedNameAttribute()
    {
        $langCode = app()->getLocale(); // or session('lang_code');

        return $this->getNameByLang($langCode);
    }
}
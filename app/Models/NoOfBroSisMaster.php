<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoOfBroSisMaster extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'no_of_bro_sis_masters';

    protected $primaryKey = 'id';

    protected $fillable = [
        'no_of_bro_sis_name',
        'lang_code',
        'lang_id',
        'status'
    ];

    protected $casts = [
        'lang_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $timestamps = true;

    /*
    |--------------------------------------------------------------------------
    | Model Events (Auto Clear Cache)
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget('bro_sis_dropdown_'.$model->lang_code);
        });

        static::deleted(function ($model) {
            Cache::forget('bro_sis_dropdown_'.$model->lang_code);
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
        $cacheKey = 'bro_sis_dropdown_' . $langCode;
        return Cache::rememberForever($cacheKey, function () use ($langCode, $defaultLang) {
            // Base records (default language IDs)
            $base = self::active()
                ->where('lang_code', $defaultLang)
                ->select('id', 'no_of_bro_sis_name')
                ->get()
                ->keyBy('id');
            if ($langCode === $defaultLang) {
                // Simple case
                $result = $base
                    ->pluck('no_of_bro_sis_name', 'id')
                    ->toArray();

                // Alphabetical ascending order
                asort($result, SORT_NATURAL | SORT_FLAG_CASE);

                return $result;
            }
            // Translations for requested language
            $translations = self::active()
                ->where('lang_code', $langCode)
                ->whereNotNull('lang_id')
                ->pluck('no_of_bro_sis_name', 'lang_id'); // key = base id
            $result = [];
            foreach ($base as $id => $row) {
                $result[$id] = $translations[$id] ?? $row->no_of_bro_sis_name;
            }
            asort($result); // keep alphabetical order by name
            return $result;
        });
    }

    public function getNameByLang($langCode = '')
    {
        $defaultLang = _getDefaultLanguage();

        if (blank($langCode) || $langCode === $defaultLang) {
            return $this->no_of_bro_sis_name;
        }

        $translated = self::where([
            'lang_id'   => $this->id,
            'lang_code' => $langCode,
            'status'    => 'APPROVED',
        ])->value('no_of_bro_sis_name');

        return $translated ?: $this->no_of_bro_sis_name;
    }

    public function getTranslatedNameAttribute()
    {
        $langCode = app()->getLocale(); // or session('lang_code');

        return $this->getNameByLang($langCode);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class StarMaster extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'star_master';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'star_name',
        'status',
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
            Cache::forget('star_dropdown_' . $model->lang_code);
        });

        static::deleted(function ($model) {
            Cache::forget('star_dropdown_' . $model->lang_code);
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

        $cacheKey = 'star_dropdown_' . $langCode;

        return Cache::rememberForever($cacheKey, function () use ($langCode, $defaultLang) {

            $base = self::where([
                'lang_code'  => $defaultLang,
                'status'     => 'APPROVED'
            ])
                ->select('id', 'star_name')
                ->get()
                ->keyBy('id');

            if ($langCode === $defaultLang) {
                // Simple case
                $result = $base
                    ->pluck('star_name', 'id')
                    ->toArray();

                // Alphabetical ascending order
                asort($result, SORT_NATURAL | SORT_FLAG_CASE);

                return $result;
            }

            $translations = self::where('lang_code', $langCode)
                ->whereNotNull('lang_id')
                ->pluck('star_name', 'lang_id');

            $result = [];

            foreach ($base as $id => $row) {
                $result[$id] = $translations[$id] ?? $row->star_name;
            }

            asort($result);

            return $result;
        });
    }

    public function getNameByLang($langCode = '')
    {
        $defaultLang = _getDefaultLanguage();

        if (blank($langCode) || $langCode === $defaultLang) {
            return $this->star_name;
        }

        $translated = self::where([
            'lang_id'   => $this->id,
            'lang_code' => $langCode,
            'status'    => 'APPROVED',
        ])->value('star_name');

        return $translated ?: $this->star_name;
    }

    public function getTranslatedNameAttribute()
    {
        $langCode = app()->getLocale(); // or session('lang_code');

        return $this->getNameByLang($langCode);
    }
}

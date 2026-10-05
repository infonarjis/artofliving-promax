<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseDetailMaster extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'course_detail_masters';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'course_name',
        'lang_code',
        'lang_id',
        'status',
    ];

    protected $casts = [
        'id'      => 'integer',
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
            Cache::forget('course_detail_dropdown_' . $model->lang_code);
        });

        static::deleted(function ($model) {
            Cache::forget('course_detail_dropdown_' . $model->lang_code);
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

        $cacheKey = 'course_detail_dropdown_' . $langCode;

        return Cache::rememberForever($cacheKey, function () use ($langCode, $defaultLang) {
            // Base records (default language IDs)
            $base = self::active()
                ->where('lang_code', $defaultLang)
                ->select('id', 'course_name')
                ->get()
                ->keyBy('id');

            if ($langCode === $defaultLang) {
                $result = $base->pluck('course_name', 'id')->toArray();
                asort($result, SORT_NATURAL | SORT_FLAG_CASE);
                return $result;
            }

            // Translations for requested language
            $translations = self::active()
                ->where('lang_code', $langCode)
                ->whereNotNull('lang_id')
                ->pluck('course_name', 'lang_id'); // key = base id

            $result = [];
            foreach ($base as $id => $row) {
                $result[$id] = $translations[$id] ?? $row->course_name;
            }

            asort($result, SORT_NATURAL | SORT_FLAG_CASE);
            return $result;
        });
    }

    public function getNameByLang($langCode = '')
    {
        $defaultLang = _getDefaultLanguage();

        if (blank($langCode) || $langCode === $defaultLang) {
            return $this->course_name;
        }

        $translated = self::where([
            'lang_id'   => $this->id,
            'lang_code' => $langCode,
            'status'    => 'APPROVED',
        ])->value('course_name');

        return $translated ?: $this->course_name;
    }

    public function getTranslatedNameAttribute()
    {
        return $this->getNameByLang(app()->getLocale());
    }
}
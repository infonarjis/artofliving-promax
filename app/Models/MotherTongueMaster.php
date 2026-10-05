<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class MotherTongueMaster extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'mothertongue_master';

    protected $primaryKey = 'id';

    protected $fillable = [
        'status',
        'mtongue_name',
        'lang_code',
        'lang_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'lang_id' => 'integer',
    ];

    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Model Events (Auto Clear Cache)
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget('mother_tongue_dropdown_' . $model->lang_code);
        });

        static::deleted(function ($model) {
            Cache::forget('mother_tongue_dropdown_' . $model->lang_code);
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

    public function scopeByLang(Builder $query, string $langCode): Builder
    {
        return $query->where('lang_code', $langCode);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedNameAttribute(): ?string
    {
        return $this->mtongue_name
            ? ucfirst($this->mtongue_name)
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function members(): HasMany
    {
        return $this->hasMany(Register::class, 'religion');
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
        $cacheKey = 'mother_tongue_dropdown_' . $langCode;
        return Cache::rememberForever($cacheKey, function () use ($langCode, $defaultLang) {
            // Base records (default language IDs)
            $base = self::active()
                ->where('lang_code', $defaultLang)
                ->select('id', 'mtongue_name')
                ->get()
                ->keyBy('id');
            if ($langCode === $defaultLang) {
                // Simple case
                $result = $base
                    ->pluck('mtongue_name', 'id')
                    ->toArray();

                // Alphabetical ascending order
                asort($result, SORT_NATURAL | SORT_FLAG_CASE);

                return $result;
            }
            // Translations for requested language
            $translations = self::active()
                ->where('lang_code', $langCode)
                ->whereNotNull('lang_id')
                ->pluck('mtongue_name', 'lang_id'); // key = base id
            $result = [];
            foreach ($base as $id => $row) {
                $result[$id] = $translations[$id] ?? $row->mtongue_name;
            }
            asort($result); // keep alphabetical order by name
            return $result;
        });
    }

    public function getNameByLang($langCode = '')
    {
        $defaultLang = _getDefaultLanguage();

        if (blank($langCode) || $langCode === $defaultLang) {
            return $this->mtongue_name;
        }

        $translated = self::where([
            'lang_id'   => $this->id,
            'lang_code' => $langCode,
            'status'    => 'APPROVED',
        ])->value('mtongue_name');

        return $translated ?: $this->mtongue_name;
    }

    public function getTranslatedNameAttribute()
    {
        $langCode = app()->getLocale(); // or session('lang_code');

        return $this->getNameByLang($langCode);
    }
}

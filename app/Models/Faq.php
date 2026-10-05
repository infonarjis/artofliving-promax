<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'faq_master';

    protected $fillable = [
        'status',
        'question',
        'answer',
        'lang_code',
        'lang_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot Method (Auto Cache Clear)
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        // Clear cache whenever a FAQ is created, updated, or deleted
        static::saved(function () {
            self::clearFaqCache();
        });

        static::deleted(function () {
            self::clearFaqCache();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Clear Redis Cache
    |--------------------------------------------------------------------------
    */
    public static function clearFaqCache()
    {
        // Get all available languages (from config or DB)
        $languages = _getActiveLanguage();

        foreach ($languages as $lang) {
            // Clear cache for each language
            Cache::forget("faq_page_{$lang->lang_code}");
        }
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

    public function scopeDefaultLang($query, $lang)
    {
        return $query->where('lang_code', $lang);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function translations()
    {
        return $this->hasMany(self::class, 'lang_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsPage extends Model
{
    use SoftDeletes;
    protected $table = 'cms_pages';

    protected $fillable = [
        'status',
        'page_title',
        'page_content',
        'page_url',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'lang_code',
        'lang_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function translations()
    {
        return $this->hasMany(self::class, 'lang_id');
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

    public function scopeBySlug($query, $slug)
    {
        return $query->where('page_url', $slug);
    }

    public function scopeByLanguage($query, $langCode)
    {
        return $query->where('lang_code', $langCode);
    }

    /*
    |--------------------------------------------------------------------------
    | Cache Clear Logic
    |--------------------------------------------------------------------------
    */

    public function clearCache()
    {
        $defaultLanguage = _getDefaultLanguage();

        // Clear default language
        Cache::forget("cms_page_footer_{$defaultLanguage}");
        Cache::forget("cms_page_{$this->page_url}_{$defaultLanguage}");

        // Clear this language
        Cache::forget("cms_page_footer_{$this->lang_code}");
        Cache::forget("cms_page_{$this->page_url}_{$this->lang_code}");

        // Clear all translations
        $languages = _getActiveLanguage();

        foreach ($languages as $language) {
            Cache::forget("cms_page_footer_{$language->lang_code}");
            Cache::forget("cms_page_{$this->page_url}_{$language->lang_code}");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Auto Clear Cache On Save/Delete
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::saved(function ($cmsPage) {
            $cmsPage->clearCache();
        });

        static::deleted(function ($cmsPage) {
            $cmsPage->clearCache();
        });
    }
}
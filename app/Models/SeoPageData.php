<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeoPageData extends Model
{
    use SoftDeletes;

    protected $table = 'seo_page_data';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // protected $fillable = ['*'];
    protected $guarded = [];

    protected $casts = [
        'schema_json' => 'array'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function language()
    {
        return $this->belongsTo(LanguageMaster::class, 'lang_id');
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

    public function scopeSlug($query, $slug)
    {
        return $query->where('page_slug', $slug);
    }

    public function scopeLang($query, $langCode)
    {
        return $query->where('lang_code', $langCode);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    // Get SEO row by slug + language
    public static function getSeo($slug, $langCode = 'en')
    {
        return self::active()
            ->slug($slug)
            ->lang($langCode)
            ->first();
    }
}
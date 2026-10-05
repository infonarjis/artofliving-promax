<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class SiteContent extends Model
{
    use SoftDeletes;
    protected $table = 'site_contents';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'about_us_image',
        'about_us_title',
        'about_us_sub_title',
        'about_us_small_desc',
        'about_us_desc',
        'about_us_brow_sec1',
        'about_us_brow_sec2',
        'about_us_brow_sec3',
        'lang_code',
        'lang_id',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        $clear = function (self $model) {
            // Clear cache for this language
            Cache::forget('cms_about_us_' . $model->lang_code);

            // If this is a translation, also clear parent language cache
            if ($model->parent) {
                Cache::forget('cms_about_us_' . $model->parent->lang_code);
            }

            // Clear all translations cache
            foreach ($model->translations as $translation) {
                Cache::forget('cms_about_us_' . $translation->lang_code);
            }
        };

        static::created($clear);
        static::updated($clear);
        static::deleted($clear);
        static::restored($clear);
    }


    /*
    |--------------------------------------------------------------------------
    | Scope: Active
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    /*
    |--------------------------------------------------------------------------
    | Scope: Language
    |--------------------------------------------------------------------------
    */

    public function scopeLang($query, $lang = 'en')
    {
        return $query->where('lang_code', $lang);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationship: Translations
    |--------------------------------------------------------------------------
    */

    public function translations()
    {
        return $this->hasMany(self::class, 'lang_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Parent Language Record
    |--------------------------------------------------------------------------
    */

    public function parent()
    {
        return $this->belongsTo(self::class, 'lang_id');
    }
}

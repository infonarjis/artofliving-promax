<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateHomePage extends Model
{
    use SoftDeletes;
    
    protected $table = 'affiliate_home_page';

    protected $primaryKey = 'id';

    public $timestamps = false; // using datetime, not Laravel timestamps

    protected $fillable = [
        'banner_img',
        'banner_title',
        'banner_subtitle',

        'how_it_works_title',
        'how_it_works_subtitle',
        'how_it_works_section1_title',
        'how_it_works_section1_subtitle',
        'how_it_works_section2_title',
        'how_it_works_section2_subtitle',
        'how_it_works_section3_title',
        'how_it_works_section3_subtitle',

        'whychooseus_title',
        'whychooseus_subtitle',
        'whychooseus_sec1_title',
        'whychooseus_sec1_subtitle',
        'whychooseus_sec2_title',
        'whychooseus_sec2_subtitle',
        'whychooseus_sec3_title',
        'whychooseus_sec3_subtitle',
        'whychooseus_sec4_title',
        'whychooseus_sec4_subtitle',
        'whychooseus_sec5_title',
        'whychooseus_sec5_subtitle',
        'whychooseus_sec6_title',
        'whychooseus_sec6_subtitle',
        'whychooseus_sec7_title',
        'whychooseus_sec7_subtitle',

        'why_affiliate_title',
        'why_affiliate_subtitle',
        'why_affiliate_sec1_title',
        'why_affiliate_sec1_subtitle',
        'why_affiliate_sec2_title',
        'why_affiliate_sec2_subtitle',
        'why_affiliate_sec3_title',
        'why_affiliate_sec3_subtitle',
        'why_affiliate_banner',

        'affiliate_feature_title',
        'affiliate_feature_subtitle',
        'affiliate_feature_sec1_title',
        'affiliate_feature_sec1_subtitle',
        'affiliate_feature_sec2_title',
        'affiliate_feature_sec2_subtitle',
        'affiliate_feature_sec3_title',
        'affiliate_feature_sec3_subtitle',
        'affiliate_feature_sec4_title',
        'affiliate_feature_sec4_subtitle',
        'affiliate_feature_sec5_title',
        'affiliate_feature_sec5_subtitle',
        'affiliate_feature_sec6_title',
        'affiliate_feature_sec6_subtitle',

        'affiliate_testimonial_title',
        'affiliate_testimonial_subtitle',

        'lang_code',
        'lang_id',
        'status',
        'created_at',
        'updated_at'
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes (VERY IMPORTANT for CMS usage)
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeLang($query, $langCode)
    {
        return $query->where('lang_code', $langCode);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper to get single row safely
    |--------------------------------------------------------------------------
    */

    public static function getContent($langCode = 'en')
    {
        return self::active()
            ->lang($langCode)
            ->first();
    }
}
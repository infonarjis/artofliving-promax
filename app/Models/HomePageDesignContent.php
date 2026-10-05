<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageDesignContent extends Model
{
    protected $table = 'home_page_design_contents';

    protected $fillable = [
        'design_id',
        'lang_id',
        'lang_code',
        'data',
        'status',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function design()
    {
        return $this->belongsTo(HomePageDesign::class, 'design_id');
    }

    /**
     * Fetch the row for $langCode, falling back to the design's
     * default-language row (lang_id NULL) if no translation exists yet —
     * same fallback behaviour as HomePageSection::getSectionWithFallback().
     */
    public static function forDesignAndLanguage(int $designId, string $langCode, string $defaultLangCode)
    {
        if ($langCode === $defaultLangCode) {
            return static::where('design_id', $designId)->whereNull('lang_id')->first();
        }

        $row = static::where('design_id', $designId)->where('lang_code', $langCode)->first();

        if ($row) {
            return $row;
        }

        return static::where('design_id', $designId)->whereNull('lang_id')->first();
    }
}

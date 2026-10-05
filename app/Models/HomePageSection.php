<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class HomePageSection extends Model
{
    use SoftDeletes;
    protected $table = 'home_page_section';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'banner_section_image',
        'banner_section_title',
        'banner_section_subtitle',
        'banner_section_story_count',
        'banner_section_story_text',

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

        'aboutus_image',
        'aboutus_title',
        'aboutus_subtitle',
        'aboutus_description',
        'aboutus_sec_title1',
        'aboutus_sec_subtitle1',
        'aboutus_sec_title2',
        'aboutus_sec_subtitle2',
        'aboutus_sec_title3',
        'aboutus_sec_subtitle3',
        'aboutus_sec_title4',
        'aboutus_sec_subtitle4',

        'mobile_banner',
        'mobile_section_title',
        'mobile_section_subtext',
        'mobile_tag_line',

        'personalize_logo',
        'personalize_image',
        'personalize_title',
        'personalize_text1',
        'personalize_text2',
        'personalize_text3',

        'last_profile_title',
        'last_profile_subtitle',
        'success_story_title',
        'success_story_subtitle',
        'browse_matrimony_title',
        'browse_matrimony_subtitle',

        'lang_code',
        'lang_id',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope: Only approved records
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    /**
     * Scope: By language code
     */
    public function scopeLang(Builder $query, string $langCode): Builder
    {
        return $query->where('lang_code', $langCode);
    }

    public static function getSectionWithFallback(string $currentLang, string $defaultLang = 'en')
    {
        $instance = new self();
        $table = $instance->getTable();
        $fillable = $instance->getFillable();

        $baseQuery = self::withoutGlobalScope(SoftDeletingScope::class)
            ->from("$table as base")
            ->whereNull('base.deleted_at') // manually apply soft delete
            ->where('base.status', 'APPROVED')
            ->where('base.lang_code', $defaultLang);

        // If same language, return normally
        if ($currentLang === $defaultLang) {
            return $baseQuery->select('base.*')->first();
        }

        $baseQuery->leftJoin("$table as tr", function ($join) use ($currentLang) {
            $join->on('base.id', '=', 'tr.lang_id')
                ->where('tr.lang_code', $currentLang)
                ->whereNull('tr.deleted_at'); // important
        });

        $selects = ['base.id'];

        foreach ($fillable as $column) {

            if (in_array($column, ['lang_code', 'lang_id', 'status', 'deleted_at'])) {
                continue;
            }

            if (str_contains($column, 'image') || str_contains($column, 'banner') || str_contains($column, 'logo') || str_contains($column, 'img')) {
                $selects[] = DB::raw("COALESCE(tr.$column, base.$column) as $column");
            } else {
                $selects[] = DB::raw("COALESCE(NULLIF(tr.$column, ''), base.$column) as $column");
            }
        }

        return $baseQuery->select($selects)->first();
    }
}

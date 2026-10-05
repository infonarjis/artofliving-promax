<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class PersonalizeHomePage extends Model
{
    use SoftDeletes;
    protected $table = 'personalize_home_page';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'banner_section_image',
        'banner_section_heading',
        'banner_section_title',
        'banner_section_title2',
        'banner_section_subtitle',
        'feature_1_text',
        'feature_2_text',
        'inquiry_title',
        'inquiry_subtitle',

        'curation_section_banner',
        'curation_section_heading',
        'curation_section_title1',
        'curation_section_subtitle1',
        'curation_section_title2',
        'curation_section_subtitle2',
        'curation_schedule_text',

        'system_advantages_section_title',
        'system_advantages_section_subtitle',
        'advantage_1_title',
        'advantage_1_subtitle',
        'advantage_2_title',
        'advantage_2_subtitle',
        'advantage_3_title',
        'advantage_3_subtitle',
        'advantage_4_title',
        'advantage_4_subtitle',
        
        'success_story_title',
        'assisted_service_title',
        'assisted_service_subtitle',
        'cta_title',
        'cta_subtitle',

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

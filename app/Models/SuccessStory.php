<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SuccessStory extends Model
{
    use SoftDeletes;
    protected $table = 'success_story';

    protected $fillable = [
        'story_type',
        'bridename',
        'brideid',
        'groomname',
        'groomid',
        'wedding_photo',
        'wedding_video_file',
        'wedding_video_thumbnail',
        'marriagedate',
        'video_link',
        'successmessage',
        'slug',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'status',
        'lang_code',
        'lang_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'lang_id'    => 'integer',
    ];

    // ─── Scopes ────────────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeByLang($query, string $lang = 'en')
    {
        return $query->where('lang_code', $lang);
    }

    public function scopeByLanguage($query, $langCode)
    {
        return $query->where('lang_code', $langCode);
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function bride(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'brideid', 'matri_id');
    }

    public function groom(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'groomid', 'matri_id');
    }

    public function scopeLanguageFallback($query, string $defaultLang, string $currentLang)
    {
        $table = $this->getTable();
        $columns = DB::getSchemaBuilder()->getColumnListing($table);

        // ❗ VERY IMPORTANT: remove soft delete global scope
        $query->withoutGlobalScopes()
            ->from("$table as base")
            ->whereNull('base.deleted_at') // apply manually
            ->where('base.lang_code', $defaultLang)
            ->where('base.status', 'APPROVED');

        // If same language → no join :
        if ($defaultLang === $currentLang) {
            return $query->select('base.*');
        }

        // Join translation
        $query->leftJoin("$table as tr", function ($join) use ($currentLang) {
            $join->on('base.id', '=', 'tr.lang_id')
                ->where('tr.lang_code', $currentLang)
                ->whereNull('tr.deleted_at'); // also needed
        });

        $skip = ['id', 'lang_id', 'lang_code', 'created_at', 'updated_at', 'deleted_at'];
        $selects = ['base.id'];

        foreach ($columns as $col) {
            if (in_array($col, $skip)) {
                $selects[] = "base.$col";
            } else {
                $selects[] = DB::raw("COALESCE(tr.$col, base.$col) as $col");
            }
        }

        return $query->select($selects);
    }
}

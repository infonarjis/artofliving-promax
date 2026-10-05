<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogMaster extends Model
{
    use SoftDeletes;
    protected $table = 'blog_master';

    protected $primaryKey = 'id';

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'blog_image',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'lang_code',
        'lang_id',
        'view_count',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeUnapproved(Builder $query): Builder
    {
        return $query->where('status', 'UNAPPROVED');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeByLang(Builder $query, $langCode): Builder
    {
        return $query->where('lang_code', $langCode);
    }

    /*
    |--------------------------------------------------------------------------
    | Language Fallback Scope
    |--------------------------------------------------------------------------
    */
    public function scopeLanguageFallback($query, $defaultLanguage, $currentLanguage)
    {
        $table = $this->getTable();
        $columns = DB::getSchemaBuilder()->getColumnListing($table);

        // ❗ remove soft delete global scope
        $query->withoutGlobalScopes();

        $query->from("$table as base")
            ->whereNull('base.deleted_at')   // ✅ manually apply soft delete
            ->where('base.lang_code', $defaultLanguage)
            ->where('base.status', 'APPROVED');

        // ✅ If same language → NO JOIN
        if ($defaultLanguage === $currentLanguage) {
            return $query->select('base.*');
        }

        // ✅ Join translations
        $query->leftJoin("$table as tr", function ($join) use ($currentLanguage) {
            $join->on('base.id', '=', 'tr.lang_id')
                ->where('tr.lang_code', $currentLanguage)
                ->whereNull('tr.deleted_at'); // ✅ soft delete for tr
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

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getShortContentAttribute(): string
    {
        return Str::limit(strip_tags($this->content), 150);
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function translations()
    {
        return $this->hasMany(self::class, 'lang_id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | Boot Method (Slug Generation)
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $slug = Str::slug($model->title);
                $count = self::where('slug', 'LIKE', "{$slug}%")->count();
                $model->slug = $count ? "{$slug}-{$count}" : $slug;
            }
        });
    }
}

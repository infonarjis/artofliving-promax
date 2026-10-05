<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class MemberFieldCheck extends Model
{
    use SoftDeletes;
    protected $table = 'member_field_check';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['section_name', 'field_name', 'field_value', 'status', 'created_at', 'updated_at'];
    protected $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];

    public const PAGE_OPTIONS = [
        'register'          => 'Register',
        'edit_profile'      => 'Edit Profile',
        'my_profile_list'   => 'My Profile List',
        'user_profile_list' => 'User Profile List',
    ];

    public const SEARCH_OPTIONS = [
        'quick_search'      => 'Quick Search',
        'advance_search'    => 'Advance Search',
        'search_result'     => 'Search Result',
        'ai_match_search'   => 'AI Match Search',
    ];

    protected static function booted()
    {
        static::saved(fn() => Cache::forget('member_field_check_map'));
    }

    public function scopeActive(Builder $query): Builder { return $query->where('status', 'APPROVED'); }
    public function scopeSection(Builder $query, string $section): Builder { return $query->where('section_name', $section); }
    public function scopeField(Builder $query, string $field): Builder { return $query->where('field_name', $field); }
    public function scopeForPage(Builder $query, string $page): Builder { return $query->whereRaw("FIND_IN_SET(?, field_value)", [$page]); }

    public static function getCachedMap(): array
    {
        return Cache::rememberForever('member_field_check_map', function () {
            return self::active()->get()
                ->groupBy('field_name')
                ->map(fn($rows) => $rows->pluck('field_value')
                    ->map(fn($v) => explode(',', $v))
                    ->flatten()->unique()->values()->toArray())
                ->toArray();
        });
    }

    public static function isFieldEnabled(string $fieldName, string $page): bool
    {
        $map = self::getCachedMap();
        return isset($map[$fieldName]) && in_array($page, $map[$fieldName]);
    }

    public static function getSectionFields(string $section, string $page): array
    {
        return self::active()->section($section)->forPage($page)->pluck('field_name')->toArray();
    }

    /**
     * Tokens a locked (field_disable = Yes) field is always given:
     * every page, plus every search context if the field is searchable.
     */
    public static function forcedTokens(bool $searchable): array
    {
        $tokens = array_keys(self::PAGE_OPTIONS);

        if ($searchable) {
            $tokens = array_merge($tokens, array_keys(self::SEARCH_OPTIONS));
        }

        return $tokens;
    }

    /**
     * Insert any field defined in config but missing from the DB.
     * Locked fields are seeded already-forced-on; everything else
     * starts fully off until an admin turns it on.
     */
    public static function syncFromConfig(): void
    {
        $config = config('member_field_settings', []);
        if (empty($config)) {
            return;
        }

        $existing = self::query()
            ->select('section_name', 'field_name')
            ->get()
            ->map(fn($row) => $row->section_name . '|' . $row->field_name)
            ->flip();

        foreach ($config as $sectionName => $fields) {
            foreach ($fields as $fieldName => $fieldConfig) {
                $key = $sectionName . '|' . $fieldName;
                if (isset($existing[$key])) {
                    continue;
                }

                $locked     = strtolower($fieldConfig['field_disable'] ?? 'No') === 'yes';
                $searchable = strtolower($fieldConfig['field_show_in_search'] ?? 'Yes') === 'yes';

                self::create([
                    'section_name' => $sectionName,
                    'field_name'   => $fieldName,
                    'field_value'  => $locked ? implode(',', self::forcedTokens($searchable)) : '',
                    'status'       => 'APPROVED',
                ]);
            }
        }
    }

    public static function label(string $fieldName): string
    {
        foreach (config('member_field_settings', []) as $fields) {
            if (isset($fields[$fieldName]['label'])) {
                return $fields[$fieldName]['label'];
            }
        }

        return \Illuminate\Support\Str::title(str_replace('_', ' ', $fieldName));
    }
}
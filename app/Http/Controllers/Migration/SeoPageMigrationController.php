<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SeoPageMigrationController extends Controller
{
    /**
     * The old table has no slug: pages are known by their page_title ('Success Story', 'Id Search' ...).
     * The new slug is the page_title as a slug ('success-story', 'id-search'), which matches the slugs
     * already used by the new table.
     *
     * Old titles whose slug differs from the page in the new table:  slug from the title => new page_slug
     */
    private const SLUG_ALIASES = [
        'register-now' => 'register',        // old 'Register Now'  -> new 'Register'
        'upgrade'      => 'membership-plan', // old 'Upgrade'       -> new 'Membership Plan'
    ];

    /**
     * Old `seo_page_data` (mysql_old, latin1)  ->  new `seo_page_data` (utf8mb4)
     *
     * By default the new table is NOT emptied: it already holds rows (home, about-us, contact-us ...) that
     * do not exist in the old table. Rows are matched on page_slug:
     *   - slug already in the new table  -> the SEO texts and og image are replaced with the old ones,
     *                                       every new-only column (robots, schema_json ...) is kept
     *   - slug not in the new table      -> a new row is inserted
     *
     * GET ...?truncate=1  empties the new table first, so every old page is inserted as a new row.
     */
    public function seoPages(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'status'   => true,
                'message'  => 'SEO page data migrated successfully.',
                'migrated' => $this->migrate($request->boolean('truncate')),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    private function migrate(bool $truncate): array
    {
        if ($truncate) {
            // TRUNCATE causes an implicit commit, so it runs before the transaction
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::table('seo_page_data')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        /**
         * latin1 -> utf8mb4 repair on every text column
         * (same technique as the other masters; og_image is a plain file name, so it needs none)
         */
        $olds = DB::connection('mysql_old')
            ->table('seo_page_data')
            ->select('seo_page_data.*')
            ->selectRaw($this->fixLatin1('page_title') . ' AS page_title_fixed')
            ->selectRaw($this->fixLatin1('seo_title') . ' AS seo_title_fixed')
            ->selectRaw($this->fixLatin1('seo_description') . ' AS seo_description_fixed')
            ->selectRaw($this->fixLatin1('seo_keywords') . ' AS seo_keywords_fixed')
            ->selectRaw($this->fixLatin1('og_title') . ' AS og_title_fixed')
            ->selectRaw($this->fixLatin1('og_description') . ' AS og_description_fixed')
            ->orderBy('id')
            ->get();

        $now       = now();
        $existing  = DB::table('seo_page_data')->pluck('id', 'page_slug')->all(); // page_slug => id
        $seenSlugs = [];

        $inserted        = 0;
        $updated         = 0;
        $skippedDeleted  = [];  // deleted old page, but the slug is already live in the new table
        $skippedNoTitle  = 0;
        $duplicateSlugs  = [];  // two old pages gave the same slug: the later one got "-<old id>"
        $addedSlugs      = [];  // old pages that are not in the new table yet
        $updatedSlugs    = [];

        DB::transaction(function () use (
            $olds, $now, $existing, &$seenSlugs, &$inserted, &$updated, &$skippedDeleted,
            &$skippedNoTitle, &$duplicateSlugs, &$addedSlugs, &$updatedSlugs
        ) {
            foreach ($olds as $o) {
                $title = $this->nz($o->page_title_fixed);
                if ($title === null) {
                    $skippedNoTitle++;
                    continue;
                }

                $slug = Str::slug($title);
                $slug = self::SLUG_ALIASES[$slug] ?? $slug;
                if ($slug === '') {
                    $skippedNoTitle++;
                    continue;
                }

                if (isset($seenSlugs[$slug])) {
                    $duplicateSlugs[] = "{$slug} (old id {$o->id})";
                    $slug = "{$slug}-{$o->id}";
                }
                $seenSlugs[$slug] = true;

                $isDeleted = $o->is_deleted === 'Yes';

                $content = [
                    'status'          => $o->status === 'UNAPPROVED' ? 'UNAPPROVED' : 'APPROVED',
                    'seo_title'       => $this->cut($o->seo_title_fixed, 255),
                    'seo_description' => $this->nz($o->seo_description_fixed),
                    'seo_keywords'    => $this->nz($o->seo_keywords_fixed),
                    'og_title'        => $this->cut($o->og_title_fixed, 255),
                    'og_description'  => $this->nz($o->og_description_fixed),
                    'og_image'        => $this->cut($o->og_image, 255),
                ];

                if (isset($existing[$slug])) {
                    // never overwrite a live page with a page that was deleted in the old system
                    if ($isDeleted) {
                        $skippedDeleted[] = $slug;
                        continue;
                    }

                    DB::table('seo_page_data')
                        ->where('id', $existing[$slug])
                        ->update($content + ['updated_at' => $now]);

                    $updated++;
                    $updatedSlugs[] = $slug;
                    continue;
                }

                DB::table('seo_page_data')->insert($content + [
                    'page_slug'  => $slug,
                    'page_title' => $this->cut($title, 255),
                    // meta_robots, og_type, sitemap_* use the column defaults of the new table
                    'lang_code'  => 'en',
                    'lang_id'    => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                    // old is_deleted = 'Yes' -> soft delete
                    'deleted_at' => $isDeleted ? $now : null,
                ]);

                $inserted++;
                $addedSlugs[] = $slug;
            }
        });

        $oldTotal = $olds->count();

        $result = [
            'old_total'       => $oldTotal,
            'updated'         => $updated,
            'inserted'        => $inserted,
            'skipped'         => count($skippedDeleted) + $skippedNoTitle,
            'updated_pages'   => $updatedSlugs,
            'new_pages_added' => $addedSlugs,
        ];

        if ($skippedDeleted) {
            $result['skipped_deleted_old_pages'] = $skippedDeleted;
        }

        if ($skippedNoTitle) {
            $result['skipped_no_title'] = $skippedNoTitle;
        }

        if ($duplicateSlugs) {
            $result['duplicate_slugs'] = $duplicateSlugs;
        }

        if ($updated + $inserted + count($skippedDeleted) + $skippedNoTitle !== $oldTotal) {
            $result['warning'] = 'updated + inserted + skipped does not match the old table count.';
        }

        return $result;
    }

    /** SQL that repairs text stored as UTF-8 bytes inside a latin1 column */
    private function fixLatin1(string $column): string
    {
        return "CONVERT(CAST(CONVERT({$column} USING latin1) AS BINARY) USING utf8mb4)";
    }

    /** '' / whitespace / null -> null */
    private function nz($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function cut($value, int $length): ?string
    {
        $value = $this->nz($value);

        return $value === null ? null : mb_substr($value, 0, $length);
    }
}
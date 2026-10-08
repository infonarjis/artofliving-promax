<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LeadCommentMigrationController extends Controller
{
    private const CHUNK = 200;

    /**
     * Old `comments_of_lead_generation` (mysql_old, latin1)
     *   ->  new `comments_of_lead_generation` (utf8mb4)
     *
     * Run it after the lead generation table is migrated, because lead_generation_id
     * keeps the old index_id value.
     */
    public function leadComments(): JsonResponse
    {
        try {
            return response()->json([
                'status'   => true,
                'message'  => 'Lead generation comments migrated successfully.',
                'migrated' => $this->migrate(),
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

    private function migrate(): array
    {
        // TRUNCATE causes an implicit commit, so it runs before the insert transaction
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('comments_of_lead_generation')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $oldTotal = DB::connection('mysql_old')->table('comments_of_lead_generation')->count();
        $now      = now();

        $withId           = [];  // old id > 0 and not used before -> keep the id
        $withoutId        = [];  // id 0 / duplicate               -> new table assigns one
        $usedIds          = [];
        $dateFallback     = 0;   // next_followup_date empty / unreadable -> created date used
        $closedNotKept    = 0;   // is_closed = 'Yes' (no column for it in the new table)
        $skippedNoComment = 0;

        /**
         * One pass over EVERY old row (no id based paging, so id 0 / duplicate ids are not lost).
         * `comment` is latin1 in the old table, so it gets the same latin1 -> utf8mb4 repair
         * used for the other tables.
         */
        $olds = DB::connection('mysql_old')
            ->table('comments_of_lead_generation')
            ->select('comments_of_lead_generation.*')
            ->selectRaw($this->fixLatin1('comment') . ' AS comment_fixed')
            ->orderBy('id')
            ->cursor();

        foreach ($olds as $o) {
            $comment = $this->nz($o->comment_fixed);

            // new column is NOT NULL: rows with no text at all are not worth a record
            if ($comment === null) {
                $skippedNoComment++;
                continue;
            }

            $createdAt = $this->clean($o->created_on) ?? $now->format('Y-m-d H:i:s');

            [$followupDate, $followupTime, $usedFallback] = $this->followup($o->next_followup_date, $createdAt);
            if ($usedFallback) {
                $dateFallback++;
            }

            if ($o->is_closed === 'Yes') {
                $closedNotKept++;
            }

            $row = [
                'lead_generation_id' => (int) $o->index_id,
                'posted_user_type'   => in_array($o->posted_user_type, ['admin', 'staff', 'franchise'], true)
                    ? $o->posted_user_type
                    : 'admin',
                'posted_by'          => (int) $o->posted_by,
                'comment'            => $comment,
                'next_followup_date' => $followupDate,
                'next_followup_time' => $followupTime,
                'follow_up_status'   => $o->follow_up_status === '1' ? 1 : 0,
                'created_at'         => $createdAt,
                'updated_at'         => $createdAt,
                // old is_deleted = 'Yes' -> soft delete
                'deleted_at'         => $o->is_deleted === 'Yes' ? $now : null,
            ];

            $id = (int) $o->id;
            if ($id > 0 && !isset($usedIds[$id])) {
                $usedIds[$id] = true;
                $withId[]     = ['id' => $id] + $row;
            } else {
                $withoutId[] = $row;
            }
        }

        DB::transaction(function () use ($withId, $withoutId) {
            foreach (array_chunk($withId, self::CHUNK) as $chunk) {
                DB::table('comments_of_lead_generation')->insert($chunk);
            }
            // inserted last so auto-increment ids continue after the highest kept id
            foreach (array_chunk($withoutId, self::CHUNK) as $chunk) {
                DB::table('comments_of_lead_generation')->insert($chunk);
            }
        });

        $migrated = count($withId) + count($withoutId);

        $result = [
            'old_total'      => $oldTotal,
            'migrated'       => $migrated,
            'ids_reassigned' => count($withoutId),
            'skipped'        => $skippedNoComment,
        ];

        if ($dateFallback) {
            $result['followup_date_from_created_on'] = $dateFallback;
        }

        if ($closedNotKept) {
            $result['closed_comments_not_flagged'] = $closedNotKept;
        }

        if ($migrated + $skippedNoComment !== $oldTotal) {
            $result['warning'] = 'migrated + skipped does not match the old table count.';
        }

        return $result;
    }

    /**
     * Old next_followup_date is free text (varchar). The new column is a NOT NULL datetime plus a separate time.
     *   '2019-11-27'            -> ['2019-11-27 00:00:00', null]
     *   '2019-11-27 15:30'      -> ['2019-11-27 00:00:00', '15:30:00']
     *   '27-11-2019' / '27/11/2019' (day first)
     * Empty or unreadable values use the comment's created date, and are counted.
     *
     * @return array{0:string,1:?string,2:bool}  [date, time, usedFallback]
     */
    private function followup($value, string $createdAt): array
    {
        $value = $this->clean($value);
        $date  = null;

        if ($value !== null) {
            if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})/', $value, $m)) {
                $date = [(int) $m[1], (int) $m[2], (int) $m[3]];
            } elseif (preg_match('/^(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{4})/', $value, $m)) {
                $date = [(int) $m[3], (int) $m[2], (int) $m[1]];
            }
        }

        if ($date === null || !checkdate($date[1], $date[2], $date[0])) {
            return [substr($createdAt, 0, 10) . ' 00:00:00', null, true];
        }

        $time = null;
        if (preg_match('/(\d{1,2}):(\d{2})(?::(\d{2}))?\s*([ap]m)?/i', substr($value, 10) ?: '', $t)) {
            $hour = (int) $t[1];
            if (!empty($t[4])) {
                $isPm = strtolower($t[4]) === 'pm';
                $hour = ($hour % 12) + ($isPm ? 12 : 0);
            }
            if ($hour < 24 && (int) $t[2] < 60) {
                $time = sprintf('%02d:%02d:%02d', $hour, (int) $t[2], (int) ($t[3] ?? 0));
            }
        }

        return [sprintf('%04d-%02d-%02d 00:00:00', $date[0], $date[1], $date[2]), $time, false];
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

    /** MySQL zero dates ('0000-00-00') and empty values -> null */
    private function clean($value): ?string
    {
        $value = $this->nz($value);

        return ($value === null || str_starts_with($value, '0000')) ? null : $value;
    }
}
<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CommentMasterMigrationController extends Controller
{
    private const CHUNK = 500;

    /**
     * Old `comment_master` (mysql_old, latin1)  ->  new `comment_master` (utf8mb4)
     *
     * old index_id  ->  new member_id  (the member's registers.id; e.g. 'Match sent AOLM347727' is
     * written on member 345432 and the other way round, so index_id is the numeric part of matri_id)
     *
     * Run it after the registers migration.
     */
    public function commentMaster(): JsonResponse
    {
        set_time_limit(0);

        try {
            return response()->json([
                'status'   => true,
                'message'  => 'Comment master migrated successfully.',
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
        DB::table('comment_master')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $oldTotal = DB::connection('mysql_old')->table('comment_master')->count();
        $now      = now();

        // ids of the members that exist in the new registers table (to report comments of unknown members)
        $memberIds = DB::table('registers')->pluck('id')->flip()->all();

        $usedIds         = [];
        $batch           = [];  // rows with their old id, inserted as the table is read
        $withoutId       = [];  // old id 0 / duplicate -> inserted last, the new table assigns an id
        $migrated        = 0;
        $skippedNoText   = 0;
        $dateNotReadable = 0;   // next_followup_date had text that is not a date -> stored as NULL
        $closedNotKept   = 0;   // is_closed = 'Yes' (no column in the new table)
        $unknownMembers  = [];  // member_id not found in registers (the comment is still migrated)

        /**
         * One pass over EVERY old row (no id based paging, so id 0 / duplicate ids are not lost).
         * `comment` is latin1 in the old table, so it gets the same latin1 -> utf8mb4 repair
         * used for the other tables.
         */
        $olds = DB::connection('mysql_old')
            ->table('comment_master')
            ->select('comment_master.*')
            ->selectRaw($this->fixLatin1('comment') . ' AS comment_fixed')
            ->orderBy('id')
            ->cursor();

        DB::beginTransaction();

        try {
            foreach ($olds as $o) {
                $comment = $this->nz($o->comment_fixed);

                // the new column is NOT NULL: a row without any text is not worth a record
                if ($comment === null) {
                    $skippedNoText++;
                    continue;
                }

                $createdAt = $this->clean($o->created_on) ?? $now->format('Y-m-d H:i:s');

                [$followup, $readable] = $this->followup($o->next_followup_date);
                if (!$readable) {
                    $dateNotReadable++;
                }

                if ($o->is_closed === 'Yes') {
                    $closedNotKept++;
                }

                $memberId = (int) $o->index_id;
                if (!isset($memberIds[$memberId])) {
                    $unknownMembers[$memberId] = true;
                }

                $row = [
                    'member_id'          => $memberId,
                    'posted_user_type'   => in_array($o->posted_user_type, ['admin', 'staff', 'franchise'], true)
                        ? $o->posted_user_type
                        : 'admin',
                    'posted_by'          => (int) $o->posted_by,
                    'comment'            => $comment,
                    'next_followup_date' => $followup,
                    'follow_up_status'   => $o->follow_up_status === '1' ? 1 : 0,
                    'is_closed'          => $o->is_closed ?? 'No',
                    'created_at'         => $createdAt,
                    'updated_at'         => $createdAt,
                    // old is_deleted = 'Yes' -> soft delete
                    'deleted_at'         => $o->is_deleted === 'Yes' ? $now : null,
                ];

                $id = (int) $o->id;
                if ($id > 0 && !isset($usedIds[$id])) {
                    $usedIds[$id] = true;
                    $batch[]      = ['id' => $id] + $row;

                    if (count($batch) >= self::CHUNK) {
                        DB::table('comment_master')->insert($batch);
                        $migrated += count($batch);
                        $batch = [];
                    }
                } else {
                    $withoutId[] = $row;
                }
            }

            if ($batch) {
                DB::table('comment_master')->insert($batch);
                $migrated += count($batch);
            }

            // inserted last so auto-increment ids continue after the highest kept id
            foreach (array_chunk($withoutId, self::CHUNK) as $chunk) {
                DB::table('comment_master')->insert($chunk);
                $migrated += count($chunk);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $result = [
            'old_total'      => $oldTotal,
            'migrated'       => $migrated,
            'ids_reassigned' => count($withoutId),
            'skipped'        => $skippedNoText,
        ];

        if ($dateNotReadable) {
            $result['followup_date_not_readable'] = $dateNotReadable;
        }

        if ($closedNotKept) {
            $result['closed_comments_not_flagged'] = $closedNotKept;
        }

        if ($unknownMembers) {
            $result['members_not_in_registers'] = [
                'count'            => count($unknownMembers),
                'member_id_sample' => array_slice(array_keys($unknownMembers), 0, 50),
            ];
        }

        if ($migrated + $skippedNoText !== $oldTotal) {
            $result['warning'] = 'migrated + skipped does not match the old table count.';
        }

        return $result;
    }

    /**
     * Old next_followup_date is free text (varchar, often NULL). The new column is a nullable datetime.
     *   NULL / '' / '0000-00-00'  -> [null, true]
     *   '2026-10-08'              -> ['2026-10-08 00:00:00', true]
     *   '2026-10-08 15:30'        -> ['2026-10-08 15:30:00', true]  (also '03:30 PM')
     *   '08-10-2026' / '08/10/2026' (day first)
     *   anything else             -> [null, false]  (counted as not readable)
     *
     * @return array{0:?string,1:bool}  [datetime, readable]
     */
    private function followup($value): array
    {
        $value = $this->clean($value);
        if ($value === null) {
            return [null, true];
        }

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})/', $value, $m)) {
            $date = [(int) $m[1], (int) $m[2], (int) $m[3]];
            $rest = substr($value, strlen($m[0]));
        } elseif (preg_match('/^(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{4})/', $value, $m)) {
            $date = [(int) $m[3], (int) $m[2], (int) $m[1]];
            $rest = substr($value, strlen($m[0]));
        } else {
            return [null, false];
        }

        if (!checkdate($date[1], $date[2], $date[0])) {
            return [null, false];
        }

        $hour = $minute = $second = 0;
        if (preg_match('/(\d{1,2}):(\d{2})(?::(\d{2}))?\s*([ap]m)?/i', $rest, $t)) {
            $h = (int) $t[1];
            if (!empty($t[4])) {
                $h = ($h % 12) + (strtolower($t[4]) === 'pm' ? 12 : 0);
            }
            if ($h < 24 && (int) $t[2] < 60) {
                [$hour, $minute, $second] = [$h, (int) $t[2], (int) ($t[3] ?? 0)];
            }
        }

        return [sprintf('%04d-%02d-%02d %02d:%02d:%02d', $date[0], $date[1], $date[2], $hour, $minute, $second), true];
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

<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminNotificationMigrationController extends Controller
{
    private const CHUNK = 500;

    /**
     * The old rows have an empty `action`. The action is filled from the notification TITLE:
     *   title (exact text)  =>  action
     * Add every title listed under "titles_without_action" in the response here.
     */
    private const ACTION_BY_TITLE = [
        'New Photo Uploaded' => 'photo_upload',
    ];

    /**
     * Old `admin_notification_list` (mysql_old)  ->  new `admin_notifications`
     *
     * member_id (new column) is found through registers.matri_id, so run the registers migration first.
     * Both tables are utf8mb4, so no latin1 repair is applied (it would break emoji).
     */
    public function adminNotifications(): JsonResponse
    {
        set_time_limit(0);

        try {
            return response()->json([
                'status'   => true,
                'message'  => 'Admin notifications migrated successfully.',
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
        // matri_id (lower case) => registers.id
        $members = DB::table('registers')
            ->whereNotNull('matri_id')
            ->pluck('id', 'matri_id')
            ->mapWithKeys(fn($id, $matri) => [mb_strtolower(trim((string) $matri)) => (int) $id])
            ->all();

        if (!$members) {
            throw new Exception('The new `registers` table is empty. Run the registers migration first.');
        }

        // TRUNCATE causes an implicit commit, so it runs before the transaction
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('admin_notifications')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $oldTotal = DB::connection('mysql_old')->table('admin_notification_list')->count();
        $now      = now();

        $usedIds          = [];
        $batch            = [];  // rows with their old id, inserted as the table is read
        $withoutId        = [];  // old id 0 / duplicate -> inserted last, the new table assigns an id
        $migrated         = 0;
        $memberFromDigits = 0;   // matri_id not in registers -> member_id taken from the number in the matri_id
        $memberNotFound   = [];  // no member at all -> member_id 0
        $titlesNoAction   = [];  // title => rows with no action (title not in ACTION_BY_TITLE and no old action)

        // one pass over EVERY old row (no id based paging, so id 0 / duplicate ids are not lost)
        $olds = DB::connection('mysql_old')
            ->table('admin_notification_list')
            ->orderBy('id')
            ->cursor();

        DB::beginTransaction();

        try {
            foreach ($olds as $o) {
                $matriId = trim((string) $o->matri_id);
                $key     = mb_strtolower($matriId);

                if (isset($members[$key])) {
                    $memberId = $members[$key];
                } elseif (preg_match('/(\d+)$/', $matriId, $m)) {
                    // 'AOLM347421' -> 347421 (member ids are the number part of the matri id)
                    $memberId = (int) $m[1];
                    $memberFromDigits++;
                } else {
                    $memberId = 0;
                    $memberNotFound[$matriId === '' ? '(empty)' : $matriId] = true;
                }

                // action from the title; an action that already exists in the old row is kept for other titles
                $title  = (string) $o->title;
                $action = self::ACTION_BY_TITLE[trim($title)] ?? trim((string) $o->action);
                if ($action === '') {
                    $label = trim($title) === '' ? '(empty title)' : trim($title);
                    $titlesNoAction[$label] = ($titlesNoAction[$label] ?? 0) + 1;
                }

                $row = [
                    'admin_id'   => null,   // not stored in the old table
                    'admin_type' => 'admin',
                    'member_id'  => $memberId,
                    'matri_id'   => $matriId,
                    'title'      => $title,
                    'message'    => (string) $o->message,
                    'action'     => $action,
                    'image'      => $this->nz($o->image),
                    'is_read'    => (int) $o->is_read === 1 ? 1 : 0,
                    'status'     => $o->status === 'UNAPPROVED' ? 'UNAPPROVED' : 'APPROVED',
                    'created_at' => $this->clean($o->created_at),
                    'updated_at' => $this->clean($o->updated_at),
                    // old is_deleted = 'Yes' -> soft delete
                    'deleted_at' => $o->is_deleted === 'Yes' ? $now : null,
                ];

                $id = (int) $o->id;
                if ($id > 0 && !isset($usedIds[$id])) {
                    $usedIds[$id] = true;
                    $batch[]      = ['id' => $id] + $row;

                    if (count($batch) >= self::CHUNK) {
                        DB::table('admin_notifications')->insert($batch);
                        $migrated += count($batch);
                        $batch = [];
                    }
                } else {
                    $withoutId[] = $row;
                }
            }

            if ($batch) {
                DB::table('admin_notifications')->insert($batch);
                $migrated += count($batch);
            }

            // inserted last so auto-increment ids continue after the highest kept id
            foreach (array_chunk($withoutId, self::CHUNK) as $chunk) {
                DB::table('admin_notifications')->insert($chunk);
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
        ];

        if ($memberFromDigits) {
            $result['member_id_from_matri_id_number'] = $memberFromDigits;
        }

        if ($memberNotFound) {
            $result['member_not_found'] = [
                'count'           => count($memberNotFound),
                'matri_id_sample' => array_slice(array_keys($memberNotFound), 0, 50),
            ];
        }

        if ($titlesNoAction) {
            arsort($titlesNoAction);
            $result['titles_without_action'] = $titlesNoAction;
        }

        if ($migrated !== $oldTotal) {
            $result['warning'] = 'migrated does not match the old table count.';
        }

        return $result;
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

    /** '' / null / MySQL zero date -> null */
    private function clean($value): ?string
    {
        $value = $this->nz($value);

        return ($value === null || str_starts_with($value, '0000')) ? null : $value;
    }
}
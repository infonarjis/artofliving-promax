<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NotificationMigrationController extends Controller
{
    private const CHUNK = 500;

    /**
     * Old `member_notification` (mysql_old, utf8mb4)  ->  new `member_notification` (utf8mb4)
     *
     * Both tables are already utf8mb4, so no latin1 repair is applied (it would break emoji).
     * Make sure the `mysql_old` connection in config/database.php has 'charset' => 'utf8mb4'.
     */
    public function memberNotifications(): JsonResponse
    {
        set_time_limit(0);

        try {
            return response()->json([
                'status'   => true,
                'message'  => 'Member notifications migrated successfully.',
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
        // TRUNCATE causes an implicit commit, so it runs before the transaction
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('member_notification')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $oldTotal = DB::connection('mysql_old')->table('member_notification')->count();
        $now      = now();

        $usedIds   = [];
        $batch     = [];  // rows with their old id, inserted as the table is read
        $withoutId = [];  // old id 0 / duplicate -> inserted last, the new table assigns an id
        $migrated  = 0;
        $adminRows = 0;

        // one pass over EVERY old row (no id based paging, so id 0 / duplicate ids are not lost)
        $olds = DB::connection('mysql_old')
            ->table('member_notification')
            ->orderBy('id')
            ->cursor();

        DB::beginTransaction();

        try {
            foreach ($olds as $o) {
                $isRead    = (int) $o->is_read === 1;
                $updatedAt = $this->clean($o->updated_at);

                // no sender (member id 0) = sent by the admin, otherwise by a member
                $byAdmin = (int) $o->sender_member_id === 0;
                if ($byAdmin) {
                    $adminRows++;
                }

                $row = [
                    'sender_member_id'   => (int) $o->sender_member_id,
                    'receiver_member_id' => (int) $o->receiver_member_id,
                    'sender_matri_id'    => $this->nz($o->sender_matri_id),
                    'receiver_matri_id'  => $this->nz($o->receiver_matri_id),
                    'title'              => (string) $o->title,
                    'message'            => (string) $o->message,
                    'action'             => $this->nz($o->action),
                    'notification_by'    => $byAdmin ? 1 : 0,
                    'image'              => $this->nz($o->image),
                    'is_read'            => $isRead ? 1 : 0,
                    // the old table has no read time: the last update is the closest value
                    'read_at'            => $isRead ? $updatedAt : null,
                    'status'             => (int) $o->status === 1 ? 1 : 0,
                    'created_at'         => $this->clean($o->created_at),
                    'updated_at'         => $updatedAt,
                    // old is_deleted = 1 -> soft delete
                    'deleted_at'         => (int) $o->is_deleted === 1 ? $now : null,
                ];

                $id = (int) $o->id;
                if ($id > 0 && !isset($usedIds[$id])) {
                    $usedIds[$id] = true;
                    $batch[]      = ['id' => $id] + $row;

                    if (count($batch) >= self::CHUNK) {
                        DB::table('member_notification')->insert($batch);
                        $migrated += count($batch);
                        $batch = [];
                    }
                } else {
                    $withoutId[] = $row;
                }
            }

            if ($batch) {
                DB::table('member_notification')->insert($batch);
                $migrated += count($batch);
            }

            // inserted last so auto-increment ids continue after the highest kept id
            foreach (array_chunk($withoutId, self::CHUNK) as $chunk) {
                DB::table('member_notification')->insert($chunk);
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
            'sent_by_admin'  => $adminRows,
        ];

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
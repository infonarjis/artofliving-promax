<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeleteProfileMigrationController extends Controller
{
    /**
     * Old DB = mysql_old
     * New DB = mysql
     *
     * Migrates `delete_profile` (old) → `member_delete_profile` (new).
     */
    public function migrate()
    {
        $result = [];

        try {

            $count = $this->migrateDeleteProfile();

            $result = [
                'status'    => 'success',
                'old_table' => 'delete_profile',
                'new_table' => 'member_delete_profile',
                'count'     => $count,
            ];

        } catch (Throwable $e) {

            $result = [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }

        return response()->json([
            'status'  => true,
            'message' => 'Delete-profile migration completed.',
            'result'  => $result,
        ]);
    }


    /**
     * Clear + migrate member_delete_profile
     */
    private function migrateDeleteProfile(): int
    {
        $source = DB::connection('mysql_old');
        $target = DB::connection('mysql');

        $oldTable = 'delete_profile';
        $newTable = 'member_delete_profile';

        /*
        |--------------------------------------------------------------------------
        | Column mapping (old => new)
        |--------------------------------------------------------------------------
        */

        $columns = [
            'id'            => 'id',
            'sender'        => 'sender',
            'reason'        => 'reason',
            'is_pause_plan' => 'is_pause_plan',
        ];

        /*
        |--------------------------------------------------------------------------
        | Defaults / computed columns
        |--------------------------------------------------------------------------
        */

        $defaults = [
            // sent_on may contain zero dates
            'sent_on' => function ($row) {
                $raw = trim((string) ($row->sent_on ?? ''));
                if ($raw === '' || str_starts_with($raw, '0000-00-00')) {
                    return null;
                }
                try {
                    return \Carbon\Carbon::parse($raw)->toDateTimeString();
                } catch (\Throwable $e) {
                    return null;
                }
            },

            // not present in old table
            'admin_action_status' => 0,
            'rejected_on'         => null,
            'deleted_on'          => null,

            // is_deleted = 'Yes' → soft deleted (now)
            // is_deleted = 'No'  → active (null)
            'deleted_at' => function ($row) {
                return ($row->is_deleted === 'Yes') ? now() : null;
            },
        ];

        $total = 0;

        /*
        |--------------------------------------------------------------------------
        | TRUNCATE TARGET TABLE BEFORE MIGRATION
        |--------------------------------------------------------------------------
        */

        $target->statement('SET FOREIGN_KEY_CHECKS=0');
        $target->table($newTable)->truncate();
        $target->statement('SET FOREIGN_KEY_CHECKS=1');

        /*
        |--------------------------------------------------------------------------
        | CHUNKED MIGRATION
        |--------------------------------------------------------------------------
        */

        $source->table($oldTable)
            ->orderBy('id')
            ->chunk(500, function ($rows) use (
                $target,
                $columns,
                $defaults,
                $newTable,
                &$total
            ) {

                $insertData = [];

                foreach ($rows as $row) {

                    $data = [];

                    // Column mapping
                    foreach ($columns as $oldColumn => $newColumn) {
                        $data[$newColumn] = $row->{$oldColumn} ?? null;
                    }

                    // Defaults (static OR closure)
                    foreach ($defaults as $column => $value) {
                        $data[$column] = ($value instanceof Closure)
                            ? $value($row)
                            : $value;
                    }

                    $insertData[] = $data;
                }

                if (!empty($insertData)) {

                    $target->table($newTable)->upsert(
                        $insertData,
                        ['id'],
                        array_keys($insertData[0])
                    );

                    $total += count($insertData);
                }
            });

        return $total;
    }
}
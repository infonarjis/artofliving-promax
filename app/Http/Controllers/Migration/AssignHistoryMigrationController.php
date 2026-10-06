<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Support\Facades\DB;
use Throwable;

class AssignHistoryMigrationController extends Controller
{
    /**
     * Old DB = mysql_old
     * New DB = mysql
     *
     * Migrates `assign_history` (old) → `assign_history` (new).
     */
    public function migrate()
    {
        $result = [];

        try {

            $count = $this->migrateAssignHistory();

            $result = [
                'status'    => 'success',
                'old_table' => 'assign_history',
                'new_table' => 'assign_history',
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
            'message' => 'Assign-history migration completed.',
            'result'  => $result,
        ]);
    }


    /**
     * Clear + migrate assign_history
     */
    private function migrateAssignHistory(): int
    {
        $source = DB::connection('mysql_old');
        $target = DB::connection('mysql');

        $oldTable = 'assign_history';
        $newTable = 'assign_history';

        /*
        |--------------------------------------------------------------------------
        | Column mapping (old => new) — all identical names
        |--------------------------------------------------------------------------
        */

        $columns = [
            'id'                 => 'id',
            'assign_by'          => 'assign_by',
            'assign_by_email'    => 'assign_by_email',
            'assign_to'          => 'assign_to',
            'user_type'          => 'user_type',
            'member_id'          => 'member_id',
            'lead_generation_id' => 'lead_generation_id',
            'action'             => 'action',
        ];

        /*
        |--------------------------------------------------------------------------
        | Defaults / computed columns
        |--------------------------------------------------------------------------
        */

        $defaults = [
            // assign_date may contain zero dates
            'assign_date' => function ($row) {
                $raw = trim((string) ($row->assign_date ?? ''));
                if ($raw === '' || str_starts_with($raw, '0000-00-00')) {
                    return null;
                }
                try {
                    return \Carbon\Carbon::parse($raw)->toDateTimeString();
                } catch (\Throwable $e) {
                    return null;
                }
            },

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
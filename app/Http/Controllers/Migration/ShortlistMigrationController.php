<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Support\Facades\DB;
use Throwable;

class ShortlistMigrationController extends Controller
{
    /**
     * Old DB = mysql_old
     * New DB = mysql
     */
    public function migrate()
    {
        $result = [];

        try {

            $count = $this->migrateShortlist();

            $result = [
                'status'    => 'success',
                'old_table' => 'shortlisted',
                'new_table' => 'shortlist_profile',
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
            'message' => 'Shortlist migration completed.',
            'result'  => $result,
        ]);
    }


    /**
     * Clear + migrate shortlist_profile
     */
    private function migrateShortlist(): int
    {
        $source = DB::connection('mysql_old');
        $target = DB::connection('mysql');

        $oldTable = 'shortlisted';
        $newTable = 'shortlist_profile';

        /*
        |--------------------------------------------------------------------------
        | Column mapping (old => new)
        |--------------------------------------------------------------------------
        */

        $columns = [
            'id'              => 'id',
            'my_member_id'    => 'sender_member_id',
            'other_member_id' => 'receiver_member_id',
            'status'          => 'status',
        ];

        /*
        |--------------------------------------------------------------------------
        | Defaults / computed columns
        |--------------------------------------------------------------------------
        */

        $defaults = [
            // sent_date (may contain zero dates) → created_at
            'created_at' => function ($row) {
                $raw = trim((string) ($row->sent_date ?? ''));
                if ($raw === '' || str_starts_with($raw, '0000-00-00')) {
                    return now();
                }
                try {
                    return \Carbon\Carbon::parse($raw)->toDateTimeString();
                } catch (\Throwable $e) {
                    return now();
                }
            },

            'updated_at' => now(),

            // ✅ Populate matri_ids by looking up old `registers` table
            'sender_matri_id' => function ($row) {
                return DB::connection('mysql_old')
                    ->table('registers')
                    ->where('id', $row->my_member_id)
                    ->value('matri_id') ?? '';
            },
            'receiver_matri_id' => function ($row) {
                return DB::connection('mysql_old')
                    ->table('registers')
                    ->where('id', $row->other_member_id)
                    ->value('matri_id') ?? '';
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
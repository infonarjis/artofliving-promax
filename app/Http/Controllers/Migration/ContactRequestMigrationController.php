<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Support\Facades\DB;
use Throwable;

class ContactRequestMigrationController extends Controller
{
    /**
     * Old DB = mysql_old
     * New DB = mysql
     *
     * Migrates `contact_request` (old) → `view_contact_details` (new).
     */
    public function migrate()
    {
        $result = [];

        try {

            $count = $this->migrateContactRequest();

            $result = [
                'status'    => 'success',
                'old_table' => 'contact_request',
                'new_table' => 'view_contact_details',
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
            'message' => 'Contact-request migration completed.',
            'result'  => $result,
        ]);
    }


    /**
     * Clear + migrate view_contact_details
     */
    private function migrateContactRequest(): int
    {
        $source = DB::connection('mysql_old');
        $target = DB::connection('mysql');

        $oldTable = 'contact_request';
        $newTable = 'view_contact_details';

        /*
        |--------------------------------------------------------------------------
        | Column mapping (old => new)
        |--------------------------------------------------------------------------
        */

        $columns = [
            'id'                 => 'id',
            'sender_matri_id'    => 'sender_matri_id',
            'receiver_matri_id'  => 'receiver_matri_id',
            'status'             => 'status',
        ];

        /*
        |--------------------------------------------------------------------------
        | Defaults / computed columns
        |--------------------------------------------------------------------------
        */

        $defaults = [
            // send_date → created_at (safe parse)
            'created_at' => function ($row) {
                $raw = trim((string) ($row->send_date ?? ''));
                if ($raw === '' || str_starts_with($raw, '0000-00-00')) {
                    return now();
                }
                try {
                    return \Carbon\Carbon::parse($raw)->toDateTimeString();
                } catch (\Throwable $e) {
                    return now();
                }
            },

            // updated_at — safe parse if present, else now()
            'updated_at' => function ($row) {
                $raw = trim((string) ($row->updated_at ?? ''));
                if ($raw === '' || str_starts_with($raw, '0000-00-00')) {
                    return now();
                }
                try {
                    return \Carbon\Carbon::parse($raw)->toDateTimeString();
                } catch (\Throwable $e) {
                    return now();
                }
            },

            // Look up numeric member IDs from old `registers` using matri_id
            'sender_member_id' => function ($row) {
                if (empty($row->sender_matri_id)) {
                    return 0;
                }
                return (int) (DB::connection('mysql_old')
                    ->table('registers')
                    ->where('matri_id', $row->sender_matri_id)
                    ->value('id') ?? 0);
            },
            'receiver_member_id' => function ($row) {
                if (empty($row->receiver_matri_id)) {
                    return 0;
                }
                return (int) (DB::connection('mysql_old')
                    ->table('registers')
                    ->where('matri_id', $row->receiver_matri_id)
                    ->value('id') ?? 0);
            },

            // is_deleted = 'Yes' → soft-deleted, 'No' → active
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
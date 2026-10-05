<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DataCommonMigrationController extends Controller
{
    /**
     * Old `religion` (mysql_old, latin1)  ->  new `religion_master` (utf8mb4)
     */
    public function religonMaster(): JsonResponse
    {
        return $this->migrateSimpleMaster(
            oldTable: 'religion',
            newTable: 'religion_master',
            nameColumn: 'religion_name',
            label: 'Religion master'
        );
    }

    public function occupationMaster(): JsonResponse
    {
        return $this->migrateSimpleMaster(
            oldTable: 'occupation',
            newTable: 'occupation_master',
            nameColumn: 'occupation_name',
            label: 'Occupation master'
        );
    }
    
    private function migrateSimpleMaster(
        string $oldTable,
        string $newTable,
        string $nameColumn,
        string $label,
        bool $truncate = true
    ): JsonResponse {
        try {

            /**
             * STEP 0: CLEAR NEW TABLE
             * (TRUNCATE causes an implicit commit, so it runs before the transaction)
             */
            if ($truncate) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                DB::table($newTable)->truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }

            /**
             * STEP 1: FETCH ALL OLD DATA FIRST
             * (latin1 -> utf8mb4 fix)
             */
            $oldRows = DB::connection('mysql_old')
                ->table($oldTable)
                ->selectRaw("
                    id,
                    status,
                    is_deleted,
                    CONVERT(
                        CAST(CONVERT({$nameColumn} USING latin1) AS BINARY)
                        USING utf8mb4
                    ) AS name_fixed
                ")
                ->orderBy('id')
                ->get();

            /**
             * STEP 2: PREPARE ROWS
             */
            $now     = now();
            $rows    = [];
            $skipped = 0;

            foreach ($oldRows as $value) {
                $name = trim((string) $value->name_fixed);

                // skip rows with empty name (e.g. old row: status '', name NULL)
                if ($name === '') {
                    $skipped++;
                    continue;
                }

                $rows[] = [
                    'id'         => $value->id,
                    'status'     => $value->status === 'UNAPPROVED' ? 'UNAPPROVED' : 'APPROVED',
                    $nameColumn  => $name,
                    'lang_code'  => 'en',
                    'lang_id'    => 1,
                    // old is_deleted = 'Yes'  ->  soft delete timestamp
                    'deleted_at' => $value->is_deleted === 'Yes' ? $now : null,
                ];
            }

            /**
             * STEP 3: INSERT (ENGLISH) INSIDE A TRANSACTION
             */
            DB::transaction(function () use ($newTable, $rows) {
                foreach (array_chunk($rows, 500) as $chunk) {
                    DB::table($newTable)->insert($chunk);
                }
            });

            return response()->json([
                'status'   => true,
                'message'  => "{$label} migrated successfully.",
                'migrated' => count($rows),
                'skipped'  => $skipped,
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
    
}

<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RequestCallBackMigrationController extends Controller
{
    private const CHUNK = 500;

    /**
     * Old `request_call_back` (mysql_old, utf8mb4)  ->  new `request_call_back`
     *
     * Create the new table first (php artisan migrate). The old table only has the matri id, so
     * member_id is found through registers.matri_id (run the registers migration first). Rows without a
     * matri id (visitors) get member_id = null. Both tables are utf8mb4, so no latin1 repair is applied.
     */
    public function requestCallBack(): JsonResponse
    {
        set_time_limit(0);

        try {
            return response()->json([
                'status'   => true,
                'message'  => 'Request call back migrated successfully.',
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
        DB::table('request_call_back')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $oldTotal = DB::connection('mysql_old')->table('request_call_back')->count();
        $now      = now();

        $usedIds    = [];
        $batch      = [];  // rows with their old id, inserted as the table is read
        $withoutId  = [];  // old id 0 / duplicate -> inserted last, the new table assigns an id
        $migrated   = 0;
        $visitors   = 0;   // no matri id -> member_id null
        $unknownMatri = []; // matri id given but not in registers -> member_id null

        // one pass over EVERY old row (no id based paging, so id 0 / duplicate ids are not lost)
        $olds = DB::connection('mysql_old')
            ->table('request_call_back')
            ->orderBy('id')
            ->cursor();

        DB::beginTransaction();

        try {
            foreach ($olds as $o) {
                $matri    = $this->nz($o->matri_id);
                $memberId = null;

                if ($matri === null) {
                    $visitors++;
                } elseif (isset($members[mb_strtolower($matri)])) {
                    $memberId = $members[mb_strtolower($matri)];
                } else {
                    $unknownMatri[$matri] = true;
                }

                $createdAt = $this->clean($o->created_at);

                $row = [
                    'member_id'  => $memberId,
                    'matri_id'   => $this->cut($matri, 50),
                    'name'       => $this->cut($o->name, 255),
                    'email'      => $this->cut($o->email, 255),
                    'mobile'     => $this->cut($o->mobile, 20),
                    'status'     => $o->status === 'UNAPPROVED' ? 'UNAPPROVED' : 'APPROVED',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    // old is_deleted = 'Yes' -> soft delete
                    'deleted_at' => $o->is_deleted === 'Yes' ? $now : null,
                ];

                $id = (int) $o->id;
                if ($id > 0 && !isset($usedIds[$id])) {
                    $usedIds[$id] = true;
                    $batch[]      = ['id' => $id] + $row;

                    if (count($batch) >= self::CHUNK) {
                        DB::table('request_call_back')->insert($batch);
                        $migrated += count($batch);
                        $batch = [];
                    }
                } else {
                    $withoutId[] = $row;
                }
            }

            if ($batch) {
                DB::table('request_call_back')->insert($batch);
                $migrated += count($batch);
            }

            // inserted last so auto-increment ids continue after the highest kept id
            foreach (array_chunk($withoutId, self::CHUNK) as $chunk) {
                DB::table('request_call_back')->insert($chunk);
                $migrated += count($chunk);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $result = [
            'old_total'          => $oldTotal,
            'migrated'           => $migrated,
            'ids_reassigned'     => count($withoutId),
            'visitors_no_member' => $visitors,
        ];

        if ($unknownMatri) {
            $result['matri_ids_not_in_registers'] = [
                'count'  => count($unknownMatri),
                'sample' => array_slice(array_keys($unknownMatri), 0, 50),
            ];
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

    private function cut($value, int $length): ?string
    {
        $value = $this->nz($value);

        return $value === null ? null : mb_substr($value, 0, $length);
    }

    /** '' / null / MySQL zero date -> null */
    private function clean($value): ?string
    {
        $value = $this->nz($value);

        return ($value === null || str_starts_with($value, '0000')) ? null : $value;
    }
}
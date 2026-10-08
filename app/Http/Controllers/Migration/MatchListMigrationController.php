<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MatchListMigrationController extends Controller
{
    private const CHUNK = 500;

    /**
     * Old `match_list` (mysql_old)  ->  new `match_list`
     *
     * The old table stores the two members as text ('305069' or 'AOLM305069' in my_matri_id /
     * other_matri_id). The new table stores member ids, so every value is turned into registers.id.
     * Run the registers migration first.
     */
    public function matchList(): JsonResponse
    {
        set_time_limit(0);

        try {
            return response()->json([
                'status'   => true,
                'message'  => 'Match list migrated successfully.',
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
        $registers = DB::table('registers')->select('id', 'matri_id')->get();
        if ($registers->isEmpty()) {
            throw new Exception('The new `registers` table is empty. Run the registers migration first.');
        }

        $memberIds = $registers->pluck('id')->flip()->all();                      // registers.id => index
        $byMatri   = $registers->filter(fn($r) => $r->matri_id !== null)
            ->mapWithKeys(fn($r) => [mb_strtolower(trim((string) $r->matri_id)) => (int) $r->id])
            ->all();                                                              // matri_id => registers.id

        // TRUNCATE causes an implicit commit, so it runs before the transaction
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('match_list')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $oldTotal = DB::connection('mysql_old')->table('match_list')->count();
        $now      = now();

        $usedIds        = [];
        $batch          = [];  // rows with their old id, inserted as the table is read
        $withoutId      = [];  // old id 0 / duplicate -> inserted last, the new table assigns an id
        $migrated       = 0;
        $skipped        = 0;   // a member id could not be worked out
        $unknownMembers = [];  // member id not in registers (the row is still migrated)

        // one pass over EVERY old row (no id based paging, so id 0 / duplicate ids are not lost)
        $olds = DB::connection('mysql_old')
            ->table('match_list')
            ->orderBy('id')
            ->cursor();

        DB::beginTransaction();

        try {
            foreach ($olds as $o) {
                $sender   = $this->memberId($o->my_matri_id, $memberIds, $byMatri);
                $receiver = $this->memberId($o->other_matri_id, $memberIds, $byMatri);

                if ($sender === 0 || $receiver === 0) {
                    $skipped++;
                    continue;
                }

                foreach ([$sender, $receiver] as $memberId) {
                    if (!isset($memberIds[$memberId])) {
                        $unknownMembers[$memberId] = true;
                    }
                }

                $createdAt = $this->clean($o->created_at);

                $row = [
                    'sender_member_id'   => $sender,
                    'receiver_member_id' => $receiver,
                    'is_notify'          => $o->is_notify === '1' ? '1' : '0',
                    // '1' Manual Match, '2' Auto Match: the old table cannot tell, so these are manual matches
                    'sent_type'          => '1',
                    // 1 admin, 2 staff, 3 franchise: not stored in the old table
                    'sent_by'            => 1,
                    'sent_by_id'         => 0,
                    // 0 Pending, 1 Accept, 2 Reject: not stored in the old table
                    'response'           => 0,
                    'updated_at'         => $createdAt,
                    'created_at'         => $createdAt,
                    // old is_deleted = 'Yes' -> soft delete
                    'deleted_at'         => $o->is_deleted === 'Yes' ? $now : null,
                ];

                $id = (int) $o->id;
                if ($id > 0 && !isset($usedIds[$id])) {
                    $usedIds[$id] = true;
                    $batch[]      = ['id' => $id] + $row;

                    if (count($batch) >= self::CHUNK) {
                        DB::table('match_list')->insert($batch);
                        $migrated += count($batch);
                        $batch = [];
                    }
                } else {
                    $withoutId[] = $row;
                }
            }

            if ($batch) {
                DB::table('match_list')->insert($batch);
                $migrated += count($batch);
            }

            // inserted last so auto-increment ids continue after the highest kept id
            foreach (array_chunk($withoutId, self::CHUNK) as $chunk) {
                DB::table('match_list')->insert($chunk);
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
            'skipped'        => $skipped,
        ];

        if ($unknownMembers) {
            $result['members_not_in_registers'] = [
                'count'            => count($unknownMembers),
                'member_id_sample' => array_slice(array_keys($unknownMembers), 0, 50),
            ];
        }

        if ($migrated + $skipped !== $oldTotal) {
            $result['warning'] = 'migrated + skipped does not match the old table count.';
        }

        return $result;
    }

    /**
     * '305069'      -> 305069
     * 'AOLM305069'  -> registers.id found by matri_id, otherwise the number at the end (305069)
     * anything else -> 0
     */
    private function memberId($value, array $memberIds, array $byMatri): int
    {
        $value = trim((string) $value);
        if ($value === '') {
            return 0;
        }

        if (ctype_digit($value)) {
            return (int) $value;
        }

        if (isset($byMatri[mb_strtolower($value)])) {
            return $byMatri[mb_strtolower($value)];
        }

        return preg_match('/(\d+)$/', $value, $m) ? (int) $m[1] : 0;
    }

    /** '' / null / MySQL zero date -> null */
    private function clean($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);

        return ($value === '' || str_starts_with($value, '0000')) ? null : $value;
    }
}
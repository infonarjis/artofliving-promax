<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ExpressInterestMigrationController extends Controller
{
    private const CHUNK = 500;

    /**
     * Old `expressinterest` (mysql_old, utf8mb4)  ->  new `express_interest`
     *
     * The old table only has matri ids ('AOLM338710'); the new table also needs member ids.
     * They are found through registers.matri_id, so run the registers migration first.
     * Both tables are utf8mb4, so no latin1 repair is applied.
     *
     * Run it BEFORE the chat conversations: custom_chat_conversation.expressinterest_id points to these ids.
     */
    public function expressInterest(): JsonResponse
    {
        set_time_limit(0);

        try {
            return response()->json([
                'status'   => true,
                'message'  => 'Express interest migrated successfully.',
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
        DB::table('express_interest')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $oldTotal = DB::connection('mysql_old')->table('expressinterest')->count();
        $now      = now();

        $usedIds          = [];
        $batch            = [];  // rows with their old id, inserted as the table is read
        $withoutId        = [];  // old id 0 / duplicate -> inserted last, the new table assigns an id
        $migrated         = 0;
        $skipped          = 0;   // sender or receiver member id could not be worked out
        $idFromMatriDigit = 0;   // matri_id not in registers -> member id taken from the number in it
        $unknownMatri     = [];  // matri ids that are not in registers (the row is still migrated)
        $rejectRemarks    = 0;   // rows that had a reject remark (no column for it in the new table)

        // one pass over EVERY old row (no id based paging, so id 0 / duplicate ids are not lost)
        $olds = DB::connection('mysql_old')
            ->table('expressinterest')
            ->orderBy('id')
            ->cursor();

        DB::beginTransaction();

        try {
            foreach ($olds as $o) {
                $senderMatri   = trim((string) $o->sender);
                $receiverMatri = trim((string) $o->receiver);

                $senderId   = $this->memberId($senderMatri, $members, $fromDigits1);
                $receiverId = $this->memberId($receiverMatri, $members, $fromDigits2);

                if ($senderId === 0 || $receiverId === 0) {
                    $skipped++;
                    continue;
                }

                foreach ([[$fromDigits1, $senderMatri], [$fromDigits2, $receiverMatri]] as [$usedDigits, $matri]) {
                    if ($usedDigits) {
                        $idFromMatriDigit++;
                        $unknownMatri[$matri] = true;
                    }
                }

                if ($this->nz($o->reject_remark) !== null) {
                    $rejectRemarks++;
                }

                $sentDate = $this->clean($o->sent_date);

                $row = [
                    'sender_member_id'   => $senderId,
                    'receiver_member_id' => $receiverId,
                    'sender_matri_id'    => $senderMatri,
                    'receiver_matri_id'  => $receiverMatri,
                    'receiver_response'  => in_array($o->receiver_response, ['Accepted', 'Rejected'], true)
                        ? $o->receiver_response
                        : 'Pending',
                    'reminder_count'     => (int) $o->reminder_count,
                    'is_read_sender'     => $o->is_read_sender === '1' ? '1' : '0',
                    'is_read_receiver'   => $o->is_read_receiver === '1' ? '1' : '0',
                    'is_notify'          => $o->is_notify === '1' ? '1' : '0',
                    // AI / Manual / Reminder: the old table does not say, so every interest is a manual one
                    'send_type'          => 'Manual',
                    'status'             => $o->status === 'UNAPPROVED' ? 'UNAPPROVED' : 'APPROVED',
                    'created_at'         => $sentDate,
                    'updated_at'         => $this->clean($o->updated_at) ?? $sentDate,
                    // old is_deleted = 'Yes' -> soft delete
                    'deleted_at'         => $o->is_deleted === 'Yes' ? $now : null,
                ];

                $id = (int) $o->id;
                if ($id > 0 && !isset($usedIds[$id])) {
                    $usedIds[$id] = true;
                    $batch[]      = ['id' => $id] + $row;

                    if (count($batch) >= self::CHUNK) {
                        DB::table('express_interest')->insert($batch);
                        $migrated += count($batch);
                        $batch = [];
                    }
                } else {
                    $withoutId[] = $row;
                }
            }

            if ($batch) {
                DB::table('express_interest')->insert($batch);
                $migrated += count($batch);
            }

            // inserted last so auto-increment ids continue after the highest kept id
            foreach (array_chunk($withoutId, self::CHUNK) as $chunk) {
                DB::table('express_interest')->insert($chunk);
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

        if ($unknownMatri) {
            $result['matri_ids_not_in_registers'] = [
                'count'  => count($unknownMatri),
                'sample' => array_slice(array_keys($unknownMatri), 0, 50),
            ];
        }

        if ($rejectRemarks) {
            $result['reject_remarks_not_migrated'] = $rejectRemarks;
        }

        if ($migrated + $skipped !== $oldTotal) {
            $result['warning'] = 'migrated + skipped does not match the old table count.';
        }

        return $result;
    }

    /**
     * 'AOLM338710' -> registers.id found by matri_id; if the matri id is not in registers, the number at the end
     * (338710) is used and $fromDigits is set to true. Anything else -> 0.
     */
    private function memberId(string $matriId, array $members, &$fromDigits): int
    {
        $fromDigits = false;

        if ($matriId === '') {
            return 0;
        }

        $key = mb_strtolower($matriId);
        if (isset($members[$key])) {
            return $members[$key];
        }

        if (preg_match('/(\d+)$/', $matriId, $m)) {
            $fromDigits = true;

            return (int) $m[1];
        }

        return 0;
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
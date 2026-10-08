<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ChatMigrationController extends Controller
{
    private const CHUNK = 500;

    private const CONVERSATIONS = 'custom_chat_conversation';
    private const MESSAGES      = 'custom_chat_conversation_message';

    /* ----------------------------------------------------------------
     |  PUBLIC ENDPOINTS
     * ---------------------------------------------------------------- */

    /** Old `custom_chat_conversation` -> new `custom_chat_conversation` */
    public function conversations(): JsonResponse
    {
        return $this->run(fn() => ['conversations' => $this->migrateConversations()]);
    }

    /** Old `custom_chat_conversation_message` -> new `custom_chat_conversation_message` (run conversations first) */
    public function messages(): JsonResponse
    {
        return $this->run(fn() => ['messages' => $this->migrateMessages()]);
    }

    /** Both, in the right order */
    public function all(): JsonResponse
    {
        return $this->run(fn() => [
            'conversations' => $this->migrateConversations(),
            'messages'      => $this->migrateMessages(),
        ]);
    }

    private function run(callable $job): JsonResponse
    {
        set_time_limit(0);

        try {
            return response()->json([
                'status'   => true,
                'message'  => 'Chat migrated successfully.',
                'migrated' => $job(),
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

    /* ----------------------------------------------------------------
     |  CONVERSATIONS
     * ---------------------------------------------------------------- */

    private function migrateConversations(): array
    {
        $now = now();

        return $this->copyTable(self::CONVERSATIONS, self::CONVERSATIONS, function (object $o) use ($now) {
            // old Accepted / Rejected / Pending  ->  new accepted / rejected / pending
            $status = match ($o->expressinterest_status) {
                'Accepted' => 'accepted',
                'Rejected' => 'rejected',
                default    => 'pending',
            };

            $updatedAt = $this->clean($o->updated_at);

            return [
                'member1_id'        => (int) $o->member1_id,
                'member1_matri_id'  => $o->member1_matri_id,
                'member2_id'        => (int) $o->member2_id,
                'member2_matri_id'  => $o->member2_matri_id,
                'request_status'    => $status,
                // the chat request is sent by member1 (the member who expressed the interest)
                'requested_by'      => (int) $o->member1_id > 0 ? (int) $o->member1_id : null,
                // no answer time in the old table: the last update is the closest value
                'responded_at'      => $status === 'pending' ? null : $updatedAt,
                'member1_block'     => (int) $o->member1_block === 1 ? 1 : 0,
                'member2_block'     => (int) $o->member2_block === 1 ? 1 : 0,
                'last_message_date' => $this->clean($o->last_message_date),
                'ai_suggestions'    => null,
                'created_at'        => $this->clean($o->created_at),
                'updated_at'        => $updatedAt,
                // old is_deleted = 'Yes' -> soft delete
                'deleted_at'        => $o->is_deleted === 'Yes' ? $now : null,
            ];
        });
    }

    /* ----------------------------------------------------------------
     |  MESSAGES
     * ---------------------------------------------------------------- */

    private function migrateMessages(): array
    {
        $now = now();

        // conversations that exist in the new table (messages of unknown conversations are still migrated, but reported)
        $conversationIds = DB::table(self::CONVERSATIONS)->pluck('id')->flip()->all();
        if (!$conversationIds) {
            throw new Exception('The new `custom_chat_conversation` table is empty. Run the conversations migration first.');
        }

        $orphans = [];

        $result = $this->copyTable(self::MESSAGES, self::MESSAGES, function (object $o) use ($now, $conversationIds, &$orphans) {
            $conversationId = (int) $o->conversation_id;
            if (!isset($conversationIds[$conversationId])) {
                $orphans[$conversationId] = true;
            }

            $isRead = $o->is_read === 'Yes';

            return [
                'conversation_id'          => $conversationId,
                'sender_member_id'         => (int) $o->sender_member_id,
                'sender_member_matri_id'   => $o->sender_member_matri_id,
                'receiver_member_id'       => (int) $o->receiver_member_id,
                'receiver_member_matri_id' => $o->receiver_member_matri_id,
                'type'                     => '0',            // 0 = chat (the old table only has text messages)
                'message'                  => (string) $o->message,
                'is_read'                  => $isRead ? 'Yes' : 'No',
                'chat_status'              => $isRead ? 2 : 0, // 2 = seen, 0 = sent
                'blocked_member_id'        => (int) $o->blocked_member_id,
                'send_on'                  => $this->clean($o->send_on),
                'deleted_at'               => $o->is_deleted === 'Yes' ? $now : null,
            ];
        });

        if ($orphans) {
            $result['conversations_not_found'] = [
                'count'               => count($orphans),
                'conversation_sample' => array_slice(array_keys($orphans), 0, 50),
            ];
        }

        return $result;
    }

    /* ----------------------------------------------------------------
     |  COMMON COPY
     * ---------------------------------------------------------------- */

    /**
     * Truncates $newTable and copies every row of $oldTable (mysql_old) into it.
     *
     * - one pass over the whole old table (no id based paging, so id 0 / duplicate ids are not lost)
     * - rows are inserted in batches as they are read; rows with id 0 / a duplicate id are inserted last
     *   and get a new id
     * - everything runs in one transaction, so a failure rolls the whole table back
     * - the old tables are already utf8mb4, so no latin1 repair is applied (it would break emoji)
     *
     * @param callable(object):?array $mapper  returns the new row (without id), or null to skip the old row
     */
    private function copyTable(string $oldTable, string $newTable, callable $mapper): array
    {
        // TRUNCATE causes an implicit commit, so it runs before the transaction
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table($newTable)->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $oldTotal  = DB::connection('mysql_old')->table($oldTable)->count();
        $usedIds   = [];
        $batch     = [];
        $withoutId = [];
        $migrated  = 0;
        $skipped   = 0;

        $olds = DB::connection('mysql_old')->table($oldTable)->orderBy('id')->cursor();

        DB::beginTransaction();

        try {
            foreach ($olds as $o) {
                $row = $mapper($o);
                if ($row === null) {
                    $skipped++;
                    continue;
                }

                $id = (int) $o->id;
                if ($id > 0 && !isset($usedIds[$id])) {
                    $usedIds[$id] = true;
                    $batch[]      = ['id' => $id] + $row;

                    if (count($batch) >= self::CHUNK) {
                        DB::table($newTable)->insert($batch);
                        $migrated += count($batch);
                        $batch = [];
                    }
                } else {
                    $withoutId[] = $row;
                }
            }

            if ($batch) {
                DB::table($newTable)->insert($batch);
                $migrated += count($batch);
            }

            // inserted last so auto-increment ids continue after the highest kept id
            foreach (array_chunk($withoutId, self::CHUNK) as $chunk) {
                DB::table($newTable)->insert($chunk);
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

        if ($migrated + $skipped !== $oldTotal) {
            $result['warning'] = 'migrated + skipped does not match the old table count.';
        }

        return $result;
    }

    /** '' / whitespace / null / MySQL zero date -> null */
    private function clean($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);

        return ($value === '' || str_starts_with($value, '0000')) ? null : $value;
    }
}
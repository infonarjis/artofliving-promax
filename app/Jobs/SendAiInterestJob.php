<?php

namespace App\Jobs;

use App\Models\AiMatchQueue;
use App\Models\ExpressInterest;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Throwable;

class SendAiInterestJob implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    public $queueId;

    public function __construct($queueId)
    {
        $this->queueId = $queueId;
    }

    public function handle(): void
    {
        $match = AiMatchQueue::query()
            ->with(['member', 'matchedMember'])
            ->find($this->queueId);

        if (!$match) {
            return;
        }

        DB::beginTransaction();

        try {

            $member = $match->member;

            $receiver = $match->matchedMember;

            if (!$member || !$receiver) {

                $match->update([
                    'queue_status' => 2
                ]);

                DB::commit();

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Daily Limit
            |--------------------------------------------------------------------------
            */
            $sentToday = ExpressInterest::query()

                ->where('sender_member_id', $member->id)

                ->whereDate('created_at', today())

                ->count();

            if (
                $sentToday >=
                $member->daily_interest_limit
            ) {

                $match->update([
                    'queue_status' => 3
                ]);

                DB::commit();

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Duplicate Check
            |--------------------------------------------------------------------------
            */
            $exists = ExpressInterest::query()

                ->where(function ($q) use ($member, $receiver) {

                    $q->where(
                        'sender_member_id',
                        $member->id
                    )

                        ->where(
                            'receiver_member_id',
                            $receiver->id
                        );
                })

                ->orWhere(function ($q) use ($member, $receiver) {

                    $q->where(
                        'sender_member_id',
                        $receiver->id
                    )

                        ->where(
                            'receiver_member_id',
                            $member->id
                        );
                })

                ->exists();

            if ($exists) {

                $match->update([
                    'queue_status' => 4
                ]);

                DB::commit();

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Create Interest
            |--------------------------------------------------------------------------
            */
            ExpressInterest::create([
                'sender_member_id' => $member->id,
                'sender_matri_id' => $member->matri_id,
                'receiver_member_id' => $receiver->id,
                'receiver_matri_id' => $receiver->matri_id,
                'receiver_response' => 'Pending',
                'reminder_count' => 0,
                'is_read_sender' => '1',
                'is_read_receiver' => '0',
                'is_notify' => '1',
                'send_type' => 'AI',
                'status' => 'APPROVED'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Mark Success
            |--------------------------------------------------------------------------
            */
            $match->update([
                'queue_status' => 1
            ]);

            DB::commit();
        } catch (Throwable $e) {

            DB::rollBack();

            $match->update([
                'queue_status' => 5
            ]);

            logger()->error(
                'AI Interest Failed',
                [
                    'queue_id' => $this->queueId,
                    'message' => $e->getMessage()
                ]
            );
        }
    }
}

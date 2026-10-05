<?php

namespace App\Services;

use App\Models\MatchSchedule;
use App\Models\Register;
use App\Models\SendMatchesSms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class AutoMatchService
{
    ## How many members to process per scheduler invocation :
    private const BATCH_SIZE = 100;
    ## How many members can receive emails per scheduler invocation :
    private const EMAIL_BATCH_SIZE = 50;
    ## How many members can receive SMS per scheduler invocation :
    private const SMS_BATCH_SIZE = 50;
    ## Safety valve for pending SMS/email records :
    private const MAX_PENDING_QUEUE = 200;

    ## Run auto matchmaking scheduler :
    public function run(PartnerPreferenceService $prefService): void
    {
        $today = now()->toDateString();

        ## Get latest schedule for today :
        $schedule = MatchSchedule::where('schedule_date', $today)->latest('id')->first();

        ## No schedule configured for today :
        if (!$schedule) {
            return;
        }

        ##  Already completed or failed :
        if (in_array($schedule->status, ['completed', 'failed'], true)) {
            return;
        }

        ## Don't create additional queue backlog :
        $pendingCount = SendMatchesSms::query()
            ->where('match_schedule_id', $schedule->id)
            ->where(function ($query) use ($schedule) {
                if ($this->shouldSendEmail($schedule)) {
                    $query->orWhere('email_sent_status', 'No');
                }
                if ($this->shouldSendSms($schedule)) {
                    $query->orWhere('sms_sent_status', 'No');
                }
            })
            ->count();

        if ($pendingCount > self::MAX_PENDING_QUEUE) {
            if ($schedule->processed_members >= $schedule->total_members) {
                $this->processPendingMessages($schedule);
            }
            return;
        }

        try {
            $this->processBatch($schedule, $prefService, $today);
        } catch (Throwable $e) {
            $schedule->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Process one member batch.
     */
    private function processBatch(MatchSchedule $schedule, PartnerPreferenceService $prefService, string $today): void
    {
        ## First execution of this schedule :
        if ($schedule->status === 'pending') {
            $totalMembers = Register::query()
                ->where('status', 'APPROVED')
                ->whereNotNull('mobile')
                ->count();

            $schedule->update([
                'status'        => 'in_progress',
                'total_members' => $totalMembers,
                'started_at'    => now(),
            ]);
        }

        ## Get last processed member ID :
        $lastId = (int) $schedule->last_processed_id;

        ## Get next batch :
        $users = Register::active()->whereNotNull('mobile')->where('id', '>', $lastId)->orderBy('id')->limit(self::BATCH_SIZE)
            ->get(['id', 'gender', 'mobile', 'email']);

        ## We still need to process pending email/SMS :
        if ($users->isEmpty()) {
            $this->processPendingMessages($schedule);
            ## Check whether anything is still pending :
            if ($this->hasPendingMessages($schedule)) {
                return;
            }
            ## Everything has been processed and sent.
            $schedule->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);
            return;
        }

        $matchesCreatedThisBatch = 0;

        ## Process members :
        foreach ($users as $user) {
            $partnerPref = $user->partnerPreference;
            if ($partnerPref) {
                $partnerQuery = Register::active()->where('gender', '!=', $user->gender);
                ## Apply partner preferences :
                $prefService->apply(
                    $partnerQuery,
                    $partnerPref
                );

                ## Exclude already matched pairs :
                $partnerQuery->whereNotExists(function ($q) use ($user) {
                    $q->select(DB::raw(1))->from('send_matches_sms as s')->whereColumn('s.other_id', 'registers.id')->where('s.my_id', $user->id);
                });

                ## Get matches according to schedule limit :
                $matches = $partnerQuery->limit($schedule->send_total_match)->get(['id',]);

                ## Create match records :
                foreach ($matches as $match) {
                    SendMatchesSms::create([
                        'match_schedule_id' => $schedule->id,
                        'sms_mobile'        => $user->mobile,
                        'email'             => $user->email,
                        'my_id'             => $user->id,
                        'other_id'          => $match->id,
                        ## Separate channel status :
                        'email_sent_status' => $this->shouldSendEmail($schedule) ? 'No' : 'Yes',
                        'sms_sent_status' => $this->shouldSendSms($schedule) ? 'No' : 'Yes',
                    ]);
                    $matchesCreatedThisBatch++;
                }
            }

            ## Move cursor :
            $lastId = $user->id;
        }

        ## Save progress :
        $schedule->update([
            'last_processed_id' => $lastId,
            'processed_members' => $schedule->processed_members + $users->count(),
            'matches_sent'      => $schedule->matches_sent + $matchesCreatedThisBatch,
        ]);

        ## Send pending email/SMS for this schedule :
        $this->processPendingMessages($schedule);
    }

    ## Process pending email and SMS messages :
    private function processPendingMessages(MatchSchedule $schedule): void
    {
        ## Send Email :
        if ($this->shouldSendEmail($schedule)) {
            $emailsSent = $this->sendEmails($schedule);
            if ($emailsSent > 0) {
                $schedule->increment('emails_sent', $emailsSent);
            }
        }
        ## Send SMS :
        if ($this->shouldSendSms($schedule)) {
            $smsSent = $this->sendSms($schedule);
            if ($smsSent > 0) {
                $schedule->increment('sms_sent', $smsSent);
            }
        }
    }

    ## Check if email should be sent :
    private function shouldSendEmail(MatchSchedule $schedule): bool
    {
        return in_array(
            $schedule->match_sending_mode,
            ['email', 'both'],
            true
        );
    }

    ## Check if SMS should be sent :
    private function shouldSendSms(MatchSchedule $schedule): bool
    {
        return in_array(
            $schedule->match_sending_mode,
            ['sms', 'both'],
            true
        );
    }

    ## Check if schedule still has pending messages :
    private function hasPendingMessages(MatchSchedule $schedule): bool
    {
        $query = SendMatchesSms::query()->where('match_schedule_id', $schedule->id);
        if ($this->shouldSendEmail($schedule)) {
            $query->where(function ($q) use ($schedule) {
                $q->where('email_sent_status', 'No');
                if ($this->shouldSendSms($schedule)) {
                    $q->orWhere('sms_sent_status', 'No');
                }
            });
        } elseif ($this->shouldSendSms($schedule)) {
            $query->where('sms_sent_status', 'No');
        }
        return $query->exists();
    }

    ## Send emails for this schedule :
    private function sendEmails(MatchSchedule $schedule): int
    {
        $memberIds = SendMatchesSms::query()
            ->where('match_schedule_id', $schedule->id)
            ->where('email_sent_status', 'No')
            ->distinct()
            ->orderBy('my_id')
            ->limit(self::EMAIL_BATCH_SIZE)
            ->pluck('my_id');

        if ($memberIds->isEmpty()) {
            return 0;
        }

        $pending = SendMatchesSms::query()
            ->where('match_schedule_id', $schedule->id)
            ->where('email_sent_status', 'No')
            ->whereIn('my_id', $memberIds)
            ->get()
            ->groupBy('my_id');

        $emailedCount = 0;
        foreach ($pending as $myId => $rows) {
            $user = Register::find($myId);
            if (!$user) {
                SendMatchesSms::whereIn('id', $rows->pluck('id'))->update([
                    'email_sent_status' => 'Yes',
                ]);
                continue;
            }

            ## Send email.
            $this->sendMatchEmail($user, $rows);

            ## Mark email as sent :
            SendMatchesSms::whereIn('id', $rows->pluck('id'))->update([
                'email_sent_status' => 'Yes',
                'sent_date'         => now()->toDateString(),
            ]);
            $emailedCount++;
        }

        return $emailedCount;
    }

    ## Send SMS for this schedule :
    private function sendSms(MatchSchedule $schedule): int
    {
        $memberIds = SendMatchesSms::query()
            ->where('match_schedule_id', $schedule->id)
            ->where('sms_sent_status', 'No')
            ->distinct()
            ->orderBy('my_id')
            ->limit(self::SMS_BATCH_SIZE)
            ->pluck('my_id');
        if ($memberIds->isEmpty()) {
            return 0;
        }

        $sentCount = 0;
        foreach ($memberIds as $memberId) {
            $user = Register::find($memberId);
            if (!$user) {
                SendMatchesSms::query()
                    ->where('match_schedule_id', $schedule->id)
                    ->where('my_id', $memberId)
                    ->where('sms_sent_status', 'No')
                    ->update([
                        'sms_sent_status' => 'Yes',
                    ]);
                continue;
            }

            ## Send SMS
            $this->sendMatchSms($user);

            ## Mark SMS as sent :
            SendMatchesSms::query()
                ->where('match_schedule_id', $schedule->id)
                ->where('my_id', $memberId)
                ->where('sms_sent_status', 'No')
                ->update([
                    'sms_sent_status' => 'Yes',
                    'sent_date'       => now()->toDateString(),
                ]);
            $sentCount++;
        }

        return $sentCount;
    }

    ## Send match email :
    private function sendMatchEmail($user, $rows): void
    {
        $replaceArr = [
            'user_name'        => $user->fullname,
            'user_matri_id'    => $user->matri_id,
            'user_email'      => $user->email,
            'member_data_html' =>
            AdminCommonActionModel::emailMemberDataHtml($user),
        ];
        app(EmailSendService::class)->send('Auto Match Send', $user->email, $replaceArr);
    }

    ## Send match SMS :
    private function sendMatchSms($user): void
    {
        app(SmsSendService::class)->sendTemplate('Auto Match Send', $user, []);
    }
}

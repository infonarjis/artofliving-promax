<?php

namespace App\Services;

use App\Models\AffiliateMemberIncome;
use App\Models\AffiliateMemberIncomeTransaction;
use App\Models\Payment;
use App\Models\Register;
use App\Models\SendBulkEmail;
use App\Models\SendBulkNotification;
use App\Models\SiteSetting;
use App\Services\Api\ApiCommonActionModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Str;

class CronService
{
    ## Update Plan Expired Members:
    public function checkExpiredMember(): void
    {
        $currentDate = Carbon::now()->format('Y-m-d');

        DB::transaction(function () use ($currentDate): void {

            ## Get members about to expire (before updating them) :
            $expiredMembers = Register::select(ApiCommonActionModel::MEMBER_COLUMNS)
                ->whereDate('plan_expired_on', '<', $currentDate)
                ->where('plan_status', 'Paid')
                ->get();

            ## Expire Members :
            Register::whereDate('plan_expired_on', '<', $currentDate)
                ->where('plan_status', 'Paid')
                ->update([
                    'plan_status' => 'Expired',
                    'fstatus' => 'Unfeatured'
                ]);

            ## Update Payments :
            Payment::whereDate('plan_expiry_date', '<', $currentDate)
                ->update([
                    'current_plan' => 'No'
                ]);

            foreach ($expiredMembers as $member) {
                ## Send Notification To Each Member Whose Plan Has Expired :
                app(NotificationService::class)->sendNotification(
                    $member,
                    $member,
                    'plan_expired'
                );
                ## Send SMS Message:
                app(SmsSendService::class)->sendTemplate('Membership Expired', $member, []);
            }

            ## Update Cron Date :
            SiteSetting::query()->update([
                'current_date_crone' => $currentDate
            ]);
            SiteSetting::clearCache();
        });
    }

    ## Upcoming Expired Member in 7 Days Send Email
    public function sendExpiryReminder()
    {
        $targetDate = Carbon::today()->addDays(7);

        $members = Register::query()
            ->select(['id', 'matri_id', 'email', 'fullname', 'plan_expired_on'])
            ->whereDate('plan_expired_on', $targetDate)
            ->where('plan_status', 'Paid')
            ->get();

        foreach ($members as $member) {
            ## Send Email :
            app(EmailSendService::class)->send('Upcoming Expired Member', $member->email, [
                'user_name'     => $member->fullname,
                'user_matri_id' => $member->matri_id,
                'user_email'    => $member->email,
                'plan_name'    => $member->plan_name,
                'plan_expired_on'    => $member->plan_expired_on,
                'renew_plan_url'    => route('web.membershipPlan.index'),
            ], ['memberData' => $member]);
        }
    }

    ## Send Bulk email :
    public function sendBulkEmail()
    {
        DB::transaction(function () {

            // Lock 50 rows for this process only
            $emails = SendBulkEmail::where('is_processing', 'No')
                ->limit(50)
                ->lockForUpdate()
                ->get();

            if ($emails->isEmpty()) {
                return;
            }

            // Mark as processing immediately (so other cron can't pick them)
            SendBulkEmail::whereIn('id', $emails->pluck('id'))->update(['is_processing' => 'Yes']);
            foreach ($emails as $email) {
                try {
                    ## Send Email :
                    $replaceArr = [
                        'email_subject'  => $email->email_subject,
                        'email_content' => $email->email_content
                    ];
                    app(EmailSendService::class)->send('Bulk Email', $email->email, $replaceArr);

                    // Hard delete ONLY after success
                    $email->delete();
                } catch (Throwable $e) {
                    //  Failed — release back to queue
                    $email->update(['is_processing' => 'No']);
                    // Optional: log error
                    Log::error('Bulk Email Failed: ' . $e->getMessage(), [
                        'id' => $email->id,
                        'email' => $email->email,
                    ]);
                }
            }
        });
    }

    ## Send Bulk Notification :
    public function sendBulkNotification(): int
    {
        $jobs = DB::transaction(function () {
            $jobs = SendBulkNotification::where('status', 'Pending')
                ->orderBy('id')
                ->limit(20)
                ->lockForUpdate()
                ->get();

            if ($jobs->isNotEmpty()) {
                SendBulkNotification::whereIn('id', $jobs->pluck('id'))
                    ->update(['status' => 'Processing', 'started_at' => now()]);
            }

            return $jobs;
        });

        foreach ($jobs as $job) {
            try {
                $this->processNotificationJob($job);
                $job->update(['status' => 'Completed', 'completed_at' => now()]);
            } catch (Throwable $e) {
                Log::error('Bulk notification job failed', [
                    'job_id' => $job->id,
                    'target_type' => $job->target_type,
                    'error' => $e->getMessage(),
                ]);

                $attempts = $job->attempts + 1;
                $job->update([
                    'attempts' => $attempts,
                    'status' => $attempts >= 3 ? 'Failed' : 'Pending',
                    'error' => Str::limit($e->getMessage(), 1000),
                ]);
            }
        }

        return $jobs->count();
    }

    protected function processNotificationJob(SendBulkNotification $job): void
    {
        $service = app(NotificationService::class);

        match ($job->target_type) {
            'Single' => $this->sendToSelectedMembers($job, $service),
            'All'    => $this->sendToAllTopics($job, $service),
            default  => throw new \RuntimeException(
                "Unhandled target_type '{$job->target_type}' for job {$job->id}"
            ),
        };
    }

    protected function sendToSelectedMembers(SendBulkNotification $job, NotificationService $service): void
    {
        $memberIds = json_decode($job->target_value, true);

        if (!is_array($memberIds) || empty($memberIds)) {
            throw new \RuntimeException("Invalid target_value for job {$job->id}");
        }

        $memberIds = array_values(array_unique(array_filter(array_map('intval', $memberIds))));
        $found = 0;
        $failed = 0;

        Register::whereIn('id', $memberIds)
            ->chunkById(200, function ($members) use ($job, $service, &$found, &$failed) {
                foreach ($members as $member) {
                    $found++;

                    try {
                        $service->sendToMember($member, $job->title, $job->message);
                    } catch (Throwable $e) {
                        $failed++;
                        Log::warning('Failed to notify member', [
                            'job_id' => $job->id,
                            'member_id' => $member->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            });

        if ($found === 0) {
            throw new \RuntimeException("No matching members for job {$job->id}");
        }

        if ($failed === $found) {
            throw new \RuntimeException("All recipients failed for job {$job->id}");
        }
    }

    protected function sendToAllTopics(SendBulkNotification $job, NotificationService $service): void
    {
        $topics = array_filter([
            _getConstant('topic_notification.BULK_NOTIFICATION_TOPIC_ANDROID'),
            _getConstant('topic_notification.BULK_NOTIFICATION_TOPIC_IOS'),
            _getConstant('topic_notification.BULK_NOTIFICATION_TOPIC_WEB'),
        ]);

        if (empty($topics)) {
            throw new \RuntimeException("No topics configured for job {$job->id}");
        }

        $failed = [];

        foreach ($topics as $topic) {
            try {
                $service->sendToTopic($topic, $job->title, $job->message, ['type' => 'bulk']);
            } catch (Throwable $e) {
                $failed[] = $topic;
                Log::warning('Failed to send to topic', [
                    'job_id' => $job->id,
                    'topic' => $topic,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (count($failed) === count($topics)) {
            throw new \RuntimeException("All topics failed for job {$job->id}");
        }
    }

    ## Generate Affiliate Income :
    public function generateIncomeAffiliate()
    {
        DB::transaction(function () {

            // Lock rows to prevent double processing :
            $incomeGroups = AffiliateMemberIncome::where('is_transfered', 0)
                ->selectRaw('affiliate_member_id, SUM(amount) as total_amount')
                ->groupBy('affiliate_member_id')
                ->lockForUpdate()
                ->get();

            foreach ($incomeGroups as $group) {

                // Insert into transaction table :
                AffiliateMemberIncomeTransaction::create([
                    'affiliate_member_id' => $group->affiliate_member_id,
                    'amount'              => $group->total_amount,
                    'is_transfered'       => 0,
                    'status'              => 1,
                ]);

                // Mark original incomes as transferred :
                AffiliateMemberIncome::where('affiliate_member_id', $group->affiliate_member_id)
                    ->where('is_transfered', 0)
                    ->update([
                        'is_transfered' => 1,
                    ]);
            }
        });
    }
}

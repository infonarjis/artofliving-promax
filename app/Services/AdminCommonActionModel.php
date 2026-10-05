<?php

namespace App\Services;

use App\Models\AdminAlert;
use App\Models\AdminNotification;
use App\Models\Franchise;
use App\Models\FranchiseActivity;
use App\Models\Payment;
use App\Models\PersonalizeAdminChatList;
use App\Models\Register;
use App\Models\SiteSetting;
use App\Models\Staff;
use App\Models\StaffActivity;
use App\Services\Api\ApiCommonActionModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminCommonActionModel
{
    public function __construct()
    {
        $this->checkExpiredMember();
    }

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

    ## Add Personalize Member to Chat :
    public function addPersonalizeMemberChat($memberId)
    {
        $personalizeMember = PersonalizeAdminChatList::where('member_id', $memberId)->first();
        if (blank($personalizeMember)) {
            $memberData = Register::where('id', $memberId)->select(['id', 'matri_id', 'user_type'])->first();
            if ($memberData->user_type == 1) {
                $insertArr = [
                    'member_id' => $memberId,
                    'matri_id' => $memberData->matri_id,
                    'admin_unread_count' => 0,
                    'web_unread_count' => 0,
                    'created_at' => _getCurrentDate(),
                ];
                PersonalizeAdminChatList::create($insertArr);
            }
        }
    }

    ## Send Admin Notification :
    public static function sendAdminNotification($memberId, $matriId, $actionType, $type = 'Admin', $adminId = 0)
    {
        $title = '';
        $message = '';

        switch ($actionType) {
            case 'new_registration':
                $title = 'New User Registration';
                $message = $matriId . ' has registered successfully.';
                break;
            case 'horoscope_upload':
                $title = 'Horoscope Uploaded';
                $message = 'New horoscope uploaded by ' . $matriId . '.';
                break;
            case 'profile_delete_request':
                $title = 'Profile Deletion Request';
                $message = 'Profile deletion request received from ' . $matriId . '.';
                break;
            case 'new_affiliate_registration':
                $title = 'New Affiliate Registration';
                $message = $matriId . ' has registered as an affiliate successfully.';
                break;
            case 'photo_upload':
                $title = 'Photo Uploaded';
                $message = 'New photo uploaded by ' . $matriId . '.';
                break;
            case 'new_id_proof_upload':
                $title = 'ID Proof Uploaded';
                $message = 'New ID proof uploaded by ' . $matriId . '.';
                break;
            case 'personalized_match':
                $title = 'New Personalized Match Request';
                $message = 'New personalized match request received from ' . $matriId . '.';
                break;
            case 'lead_assign':
                $staff = Staff::where('id', $adminId)->select('id', 'username')->first();
                if (!$staff) {
                    return false; // currently falls through with empty title/message on failure
                }
                $title = 'New Lead Assigned';
                $message = $staff->username . ', you have been assigned a new lead.';
                break;
            case 'member_assign':
                $memberData = Register::select('id', 'matri_id')->find($memberId);
                if (!$memberData) {
                    return false;
                }
                if ($type === 'Staff') {
                    $user = Staff::select('id', 'username')->find($adminId);
                    if (!$user) {
                        return false;
                    }
                    $title = 'New Member Assigned';
                    $message = $user->username . ', you have been assigned a new member: ' . $memberData->matri_id . '.';
                } elseif ($type === 'Franchise') {
                    $user = Franchise::select('id', 'username')->find($adminId);
                    if (!$user) {
                        return false;
                    }
                    $title = 'New Member Assigned';
                    $message = $user->username . ', you have been assigned a new member: ' . $memberData->matri_id . '.';
                }
                break;
            default:
                $title = 'New Notification';
                $message = 'You have received a new notification.';
                break;
        }

        $insertDataArr = [
            'admin_id'     => $adminId ?: null,
            'admin_type'   => strtolower($type), // admin, staff, franchise
            'member_id'   => $memberId ?? 0,
            'matri_id'    => $matriId ?? '',
            'title'       => $title,
            'message'     => $message,
            'action'      => $actionType,
            'status'     => 'APPROVED',
            'is_read'    => 0,
            'created_at' => _getCurrentDate(),
        ];

        AdminNotification::create($insertDataArr);

        AdminNotification::clearNotificationCache($type, $adminId);

        return true;
    }

    ## Add Staff Actvity :
    public static function addStaffFranchiseActivity(int $memberId, int $userTypeId, string $activity, string $type = 'Staff', string $desc = ''): bool
    {
        if ($type === 'Staff') {
            $exists = StaffActivity::where([
                'member_id'     => $memberId,
                'staff_id'      => $userTypeId,
                'activity_type' => $activity,
            ])->exists();

            if ($exists) {
                return false;
            }

            StaffActivity::create([
                'member_id'     => $memberId,
                'staff_id'      => $userTypeId,
                'activity_type' => $activity,
                'description'   => $desc,
            ]);

            return true;
        } else {

            $exists = FranchiseActivity::where([
                'member_id'     => $memberId,
                'franchise_id'  => $userTypeId,
                'activity_type' => $activity,
            ])->exists();

            if ($exists) {
                return false;
            }

            FranchiseActivity::create([
                'member_id'     => $memberId,
                'franchise_id'  => $userTypeId,
                'activity_type' => $activity,
                'description'   => $desc,
            ]);

            return true;
        }
    }

    ## Update Admin Alert Messages:
    public static function adminAlertUpdate(string $alertType, string $readType = AdminAlert::STATUS_UNREAD): void
    {
        $adminId = Auth::id();

        if (!$adminId || !in_array($readType, [AdminAlert::STATUS_READ, AdminAlert::STATUS_UNREAD], true)) {
            return;
        }

        $allowedColumns = _getStaticArr('adminAlertType');

        if (!in_array($alertType, $allowedColumns, true)) {
            return;
        }

        AdminAlert::updateOrCreate(
            ['admin_id' => $adminId],
            [
                $alertType   => $readType,
                'admin_type' => AdminAlert::TYPE_ADMIN,
            ]
        );

        $adminId = Auth::user()->id;
        // $cacheKey = 'admin_unread_alerts_' . $adminId;
        // Cache::forget($cacheKey);
        Cache::increment("admin_alerts_v_{$adminId}") ?: Cache::forever("admin_alerts_v_{$adminId}", 1);
    }

    public static function emailMemberDataHtml($memberData)
    {
        // $profileImage = _getMemberProfileImage($memberData);
        $profileImage = 'https://pro.matrimonialscriptphp.com/storage/assets/memberPhotos/photo1_hwr0e1sy.webp';
        $profileTitle = e(_profileTitle($memberData));
        $memberDetail = e(_displayNotAvailable(_profileSubTitle($memberData)));
        $profileLink  = route('web.userProfile.index', _encrypt($memberData->id));

        return '
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="91%" align="center" style="margin: 0 auto 16px auto;">
            <tr>
                <td style="background-color: #ffffff; border: 1px solid #FCE7EC; border-radius: 20px; padding: 20px 22px;">

                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>

                            <!-- Profile Image -->
                            <td width="90" valign="top" style="padding-right: 18px;">
                                <img
                                    src="' . $profileImage . '"
                                    alt="' . $profileTitle . '"
                                    width="90"
                                    height="110"
                                    style="border-radius: 14px; height: 110px; width: 90px; object-fit: cover; border: 2px solid #ffffff; display: block;">
                            </td>

                            <!-- Profile Details -->
                            <td valign="top">

                                <h5
                                    style="margin: 0; font-family: \'Outfit\', Arial, sans-serif; font-size: 17px; line-height: 22px; color: #0F172A; font-weight: 700;">
                                    ' . $profileTitle . '
                                </h5>

                                <p
                                    style="margin: 6px 0 14px 0; font-family: \'Outfit\', Arial, sans-serif; font-size: 13px; font-weight: 400; color: #475569; line-height: 19px;">
                                    ' . $memberDetail . '
                                </p>

                                <!-- View Profile Button -->
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td
                                            bgcolor="#DE3B68"
                                            style="background-color: #DE3B68; border-radius: 30px;">
                                            <a
                                                href="' . $profileLink . '"
                                                target="_blank"
                                                style="display: inline-block; color: #ffffff; padding: 9px 22px; border-radius: 30px; font-family: \'Outfit\', Arial, sans-serif; font-size: 13px; font-weight: 700; line-height: 18px; text-decoration: none;">
                                                View Profile
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>';
    }
}

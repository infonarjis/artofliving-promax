<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ViewedProfile;
use App\Models\ViewContactDetail;
use App\Models\PhotoRequest;
use App\Models\ShortlistProfile;
use App\Models\BlockProfile;
use App\Models\CustomChatConversation;
use App\Models\ExpressInterest;
use App\Models\Payment;
use App\Models\Register;
use App\Models\VideoCallHistory;
use App\Services\Api\ApiCommonActionModel;
use App\Services\ProfileCompletionService;

class SidebarComposer
{
    public function compose(View $view): void
    {
        if (auth()->guard('web')->check()) {
            $authUser = Auth::guard('web')->user();
            $authMemberId = $authUser->id;
            $blockedIds = BlockProfile::getBlockedMemberIds($authMemberId);

            // ================= Featured & Paid (shared logic) =================
            $getProfiles = function ($featured = false) use ($authUser, $authMemberId, $blockedIds) {
                $query = Register::query()
                    ->select(ApiCommonActionModel::MEMBER_COLUMNS)
                    ->whereNotIn('id', $blockedIds)
                    ->active()
                    ->paid()
                    ->where('gender', '!=', $authUser->gender);

                if ($featured) {
                    $query->where('fstatus', 'Featured');
                }

                if ($authUser && isset($authUser->user_type) && $authUser->user_type == 0) {
                    $query->where('user_type', 0);
                }

                $profiles = $query->orderby('id', 'DESC')->limit(5)->get();

                $receiverIds = $profiles->pluck('id');

                $accepted = PhotoRequest::where('sender_member_id', $authMemberId)
                    ->whereIn('receiver_member_id', $receiverIds)
                    ->where('status', 'Accepted')
                    ->pluck('receiver_member_id')
                    ->toArray();

                foreach ($profiles as $p) {
                    $p->hasPhotoRequestAccess = in_array($p->id, $accepted);
                }

                return $profiles;
            };

            $featuredProfiles = $getProfiles(true);
            $paidProfiles     = $getProfiles(false);

            ## Check Plan Exists:
            $today = _getCurrentDate('Y-m-d');
            $currentPlan = Payment::where([
                'member_id'   => $authMemberId,
                'current_plan' => 'Yes',
            ])->whereDate('plan_expiry_date', '>=', $today)->first();

            $view->with([
                'iViewedProfileCount'       => ViewedProfile::active()->where('sender_member_id', $authMemberId)->count(),
                'whoViewedProfileCount'     => ViewedProfile::active()->where('receiver_member_id', $authMemberId)->count(),
                'iViewedContactCount'       => ViewContactDetail::active()->where('sender_member_id', $authMemberId)->count(),
                'whoViewedContactCount'     => ViewContactDetail::active()->where('receiver_member_id', $authMemberId)->count(),
                'photoRequestSentCount'     => PhotoRequest::active()->where('sender_member_id', $authMemberId)->count(),
                'photoRequestReceiveCount'  => PhotoRequest::active()->where('receiver_member_id', $authMemberId)->count(),
                'interestSentCount'         => ExpressInterest::active()->where('sender_member_id', $authMemberId)->count(),
                'interestReceiveCount'      => ExpressInterest::active()->where('receiver_member_id', $authMemberId)->count(),
                'shortlistCount'            => ShortlistProfile::active()->where('sender_member_id', $authMemberId)->count(),
                'blocklistCount'            => BlockProfile::active()->where('sender_member_id', $authMemberId)->count(),
                'callHistoryCount'          => VideoCallHistory::active()->where(function ($q) use ($authMemberId) {
                                                    $q->where(function ($q1) use ($authMemberId) {
                                                        $q1->where('sender_member_id', $authMemberId);
                                                    })->orWhere(function ($q2) use ($authMemberId) {
                                                        $q2->where('receiver_member_id', $authMemberId);
                                                    });
                                                })->count(),
                'completionPercent'         => ProfileCompletionService::calculate($authUser),
                'unreadChatMessage'         => CustomChatConversation::where(function ($q) use ($authMemberId) {
                    $q->where('member1_id', $authMemberId)
                        ->orWhere('member2_id', $authMemberId);
                })->count(),
                'featuredProfiles'          => $featuredProfiles,
                'paidProfiles'              => $paidProfiles,
                'currentPlan'               => $currentPlan,
                'biodataUrl'                => route('web.myProfile.downloadBiodataPdf', [$authMemberId])
            ]);
        } else {
            // ================= Featured & Paid (shared logic) =================
            $getProfiles = function ($featured = false) {
                $query = Register::query()
                    ->select(ApiCommonActionModel::MEMBER_COLUMNS)
                    ->active()
                    ->paid();

                if ($featured) {
                    $query->where('fstatus', 'Featured');
                }
                $profiles = $query->orderby('id', 'DESC')->limit(5)->get();
                foreach ($profiles as $p) {
                    $p->hasPhotoRequestAccess = '';
                }
                return $profiles;
            };

            $featuredProfiles = $getProfiles(true);
            $paidProfiles     = $getProfiles(false);

            $view->with([
                'featuredProfiles'        => $featuredProfiles,
                'paidProfiles'            => $paidProfiles,
            ]);
        }
    }
}

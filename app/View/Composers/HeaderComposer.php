<?php

namespace App\View\Composers;

use App\Models\MatchMemberMeeting;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\MemberNotification;
use App\Models\PhotoRequest;
use App\Services\Api\ApiCommonActionModel;

class HeaderComposer
{
    public function compose(View $view): void
    {
        $authUser = Auth::guard('web')->user();
        if (!$authUser) {
            return;
        }
        $currentMemberId = $authUser->id;

        ## Notification List :
        $notifications = MemberNotification::active()->with(ApiCommonActionModel::relation('sender'))->where('receiver_member_id', $currentMemberId)->latest()->limit(6)->get();
        // Get all receiver ids in one go
        $receiverIds = $notifications->pluck('receiver_member_id')->toArray();
        // Fetch accepted photo requests via model method
        $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($currentMemberId, $receiverIds);

        // Attach flag
        foreach ($notifications as $item) {
            $item->hasPhotoRequestAccess = in_array($item->receiver_member_id, $acceptedRequests);
        }

        ## Unread Count :
        $unreadCount = MemberNotification::active()->where('receiver_member_id', $currentMemberId)->unread()->count();

        ## Check Online User Fix Meetings:
        $fixMeetingCount = MatchMemberMeeting::query()
            ->where(function ($q) use ($currentMemberId) {
                $q->where('member1_id', $currentMemberId)
                    ->orWhere('member2_id', $currentMemberId);
            })
            ->count();
        

        $savedCount = 0;

        $view->with([
            'navNotifications'  => $notifications,
            'navUnreadCount'    => $unreadCount,
            'fixMeetingCount'   => $fixMeetingCount,
            'savedCount' => $savedCount
        ]);
    }
}
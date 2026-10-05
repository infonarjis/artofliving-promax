<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\ExpressInterest;
use App\Models\MatchMemberMeeting;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Models\RegisterPartner;
use App\Models\ShortlistProfile;
use App\Services\EmailSendService;
use App\Services\MatchMakingService;
use App\Services\PartnerPreferenceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    private PartnerPreferenceService $prefService;
    private MatchMakingService $matchService;

    public function __construct(
        PartnerPreferenceService $prefService,
        MatchMakingService $matchService
    ) {
        $this->prefService  = $prefService;
        $this->matchService = $matchService;
    }

    public function index()
    {
        $authUser = auth()->guard('web')->user();

        $featuredMember        = $this->getMatches('featured_member', 5);
        $recentlyJoinedMember  = $this->getMatches('new_joined', 5);
        $recentlyLoginMember   = $this->getMatches('login_member', 5);
        $recommedMatchesMember = $this->getMatches('recommended', 5);
        $premiumMatchesMember  = $this->getMatches('premium', 5);

        ## Check Today Meetings:
        $currentMemberId = $authUser->id;
        $currentDate = now()->toDateString();

        $todayMeeting = MatchMemberMeeting::query()
            ->where(function ($q) use ($currentMemberId) {
                $q->where('member1_id', $currentMemberId)
                    ->orWhere('member2_id', $currentMemberId);
            })->whereDate('date_time', $currentDate)->where('meeting_status', 1)->count();

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.dashboard.index', [
            'authUser'              => $authUser,
            'todayMeeting'          => $todayMeeting,
            'featuredMember'        => $featuredMember,
            'recentlyJoinedMember'  => $recentlyJoinedMember,
            'recentlyLoginMember'   => $recentlyLoginMember,
            'recommedMatchesMember' => $recommedMatchesMember,
            'premiumMatchesMember'  => $premiumMatchesMember
        ]);
    }

    private function getMatches(string $type, int $limit = 5)
    {
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        $blockedIds  = BlockProfile::getBlockedMemberIds($memberId);
        $partnerPref = RegisterPartner::where('member_id', $memberId)->first();
        $this->matchService->setPreference($partnerPref);

        $query = Register::active()
            ->common($authUser)
            ->select([
                'id',
                'matri_id',
                'gender',
                'religion',
                'caste',
                'mother_tongue',
                'income',
                'diet',
                'occupation',
                'manglik',
                'marital_status',
                'education_level',
                'birthdate',
                'height',
                'country_id',
                'state_id',
                'city',
                'last_activity',
                'created_at',
                'last_login',
                'photo1',
                'photo1_status',
                'photo2',
                'photo2_status',
                'photo3',
                'photo3_status',
                'photo4',
                'photo4_status',
                'photo_visibility',
                'plan_status',
                'plan_name'
            ])
            ->where('id', '!=', $memberId)
            ->whereNotIn('id', $blockedIds)
            ->where('gender', '!=', $authUser->gender);

        // Apply partner preference early
        if (in_array($type, ['recommended', 'premium'])) {
            if ($type === 'premium') {
                $query->where('plan_status', 'Paid');
            }
            $this->prefService->apply($query, $partnerPref);
        }
        // Featured Member :
        if ($type === 'featured_member') {
            $query->where('plan_status', 'Paid');
            $query->where('fstatus', 'Featured');
        }

        // Sorting
        if ($type === 'new_joined') {
            $query->orderByDesc('created_at');
        } elseif ($type === 'login_member') {
            $query->orderByDesc('last_login');
        }

        $resultArr = $query->limit($limit)->get();

        // Batch fetch statuses
        $receiverIds = $resultArr->pluck('id')->toArray();

        $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

        $shortlistedIds = ShortlistProfile::where('sender_member_id', $memberId)
            ->whereIn('receiver_member_id', $receiverIds)
            ->pluck('receiver_member_id')
            ->toArray();

        $interestIds = ExpressInterest::where('sender_member_id', $memberId)
            ->whereIn('receiver_member_id', $receiverIds)
            ->pluck('receiver_member_id')
            ->toArray();

        $blockedMap = BlockProfile::getEitherBlockedMap($memberId, $receiverIds);

        foreach ($resultArr as $item) {
            $item->hasPhotoRequestAccess = in_array($item->id, $acceptedRequests);
            $item->is_shortlisted       = in_array($item->id, $shortlistedIds);
            $item->is_interest          = in_array($item->id, $interestIds);
            $item->isBlocked            = isset($blockedMap[$item->id]);

            ## Get Profile Match % :
            $item->matchPercent = $this->matchService->percent($item);
        }

        return $resultArr;
    }


    public function sendConfirmationEmail(Request $request)
    {
        /** @var \App\Models\Register|null $authUser */
        $authUser = auth()->guard('web')->user();

        if ($authUser->email_verify_status == 'Verify') {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_your_email_is_already_verified')
            ]);
        }

        ## Send Confirmation Email :
        $plainToken = Str::random(64);
        $authUser->update([
            'email_verification_token' => hash('sha256', $plainToken), // store hashed
            'email_verification_token_expires_at' => Carbon::now()->addDays(3),
        ]);
        $confirmLink = route('web.confirm.email', ['token' => $plainToken]);

        $replaceArr = [
            'user_name'  => $authUser->fullname,
            'user_matri_id' => $authUser->matri_id,
            'user_email' => $authUser->email,
            'confirmation_url' => $confirmLink
        ];
        app(EmailSendService::class)->send('Email Confirmation', $authUser->email, $replaceArr, ['memberData' => $authUser]);

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_verification_email_sent_successfully')
        ]);
    }
}

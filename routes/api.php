<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
date_default_timezone_set("Asia/Calcutta");

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\{
    CommonRequestController,
    LoginController,
    ForgotPasswordController,
    RegisterController,
    MyProfileController,
    MobileVerificationController,
    DashboardController,
    SearchController,
    SavedSearchController,
    NotificationController,
    ShortListController,
    BlockListController,
    ExpressInterestController,
    PhotoRequestController,
    ViewedProfileController,
    ViewedContactController,
    UserProfileController,
    MatchesController,
    ReceiveAdminMatchController,
    FixMeetingsController,
    PersonalizeChatController,
    MembershipPlanController,
    CurrentPlanController,
    PrivacySettingController,
    VideoVoiceCallController,
    ChatController,
    CmsPagesController,
    ContactUsController,
    ReportProfileController,
    SuccessStoryController,
    AiAutoInterestController,
    AiMatchMakingController
};

## Common-Request :
Route::post('common-request/get-token', [CommonRequestController::class, 'getToken']);
Route::get('common-request/get-common-dropdown-list', [CommonRequestController::class, 'getCommonDropdownList']);

Route::post('common-request/get-dropdown-value', [CommonRequestController::class, 'getDropdownValue']);
Route::post('common-request/get-dependency-dropdown', [CommonRequestController::class, 'getDependencyList']);

Route::post('common-request/dynamic/fields', [CommonRequestController::class, 'dynamicFields']);

## Login :
Route::post('check-login', [LoginController::class, 'authenticate']);
Route::post('send-otp', [LoginController::class, 'sendOtp']);
Route::post('resend-otp', [LoginController::class, 'resendOtp']);
Route::post('verify-otp', [LoginController::class, 'verifyOtp']);

## Forgot Password :
Route::post('forgot-password', [ForgotPasswordController::class, 'index']);

## Register:
Route::post('register', [RegisterController::class, 'store']);
Route::post('register/resend-otp', [RegisterController::class, 'resendOtp']);
Route::post('register/verify-otp', [RegisterController::class, 'verifyOtp']);
Route::post('register/next-step', [RegisterController::class, 'submitSteps']);
Route::post('register/generate-about-me', [RegisterController::class, 'generateAiAboutMe']);

## Cms Pages:
Route::post('cms/page', [CmsPagesController::class, 'index']);

## After Login :
Route::group(['middleware' => ['auth:sanctum', 'app.check.deleted']], function () {
    ## Logout :
    Route::post('logout', [LoginController::class, 'logout']);

    ## Dashboard :
    Route::get('dashboard', [DashboardController::class, 'index']);

    ## My Profile :
    Route::get('my-profile', [MyProfileController::class, 'index']);
    Route::post('my-profile/edit', [MyProfileController::class, 'editProfile']);
    Route::get('my-profile/generate-about-me', [MyProfileController::class, 'generateAiAboutMe']);
    ## Delete Photos, Id Proof & Horoscope :
    Route::post('my-profile/remove-photo', [MyProfileController::class, 'removePhoto']);
    Route::post('my-profile/remove-id-proof', [MyProfileController::class, 'removeIdProof']);
    Route::get('my-profile/remove-horoscope', [MyProfileController::class, 'removeHoroscope']);
    ## Delete Profile :
    Route::post('my-profile/delete-request', [MyProfileController::class, 'deleteRequest']);

    ## Update Lat & Long :
    Route::post('my-profile/update-lat-long', [MyProfileController::class, 'updateLatLog']);

    ## Send Confirmation Email :
    Route::get('my-profile/verify-email', [MyProfileController::class, 'sendConfirmationEmail']);

    ## Verify Mobile Number :
    Route::get('mobile-verification/generate', [MobileVerificationController::class, 'generateOtp']);
    Route::get('mobile-verification/resend', [MobileVerificationController::class, 'resendOtp']);
    Route::post('mobile-verification/verify', [MobileVerificationController::class, 'verifyOtp']);

    ## User Profile :
    Route::post('user-profile', [UserProfileController::class, 'index']);
    Route::post('view-contact', [UserProfileController::class, 'viewContact']);

    ## Search :
    Route::post('search-result', [SearchController::class, 'searchResult']);

    ## Saved Search :
    Route::post('saved-search/list', [SavedSearchController::class, 'index']);
    Route::post('saved-search/save', [SavedSearchController::class, 'savedSearch']);
    Route::post('saved-search/delete', [SavedSearchController::class, 'destroy']);

    ## Notification :
    Route::post('notification/list', [NotificationController::class, 'index']);

    ## Shortlist Profile :
    Route::post('shortlist', [ShortListController::class, 'index']);
    Route::post('shortlist/add-remove', [ShortListController::class, 'addRemove']);

    ## Blocklist Profile :
    Route::post('blocklist', [BlockListController::class, 'index']);
    Route::post('blocklist/add-remove', [BlockListController::class, 'addRemove']);

    ## Photo Request :
    Route::post('photo-request', [PhotoRequestController::class, 'index']);
    Route::post('photo-request/send', [PhotoRequestController::class, 'send']);
    Route::post('photo-request/accept-reject', [PhotoRequestController::class, 'acceptReject']);
    Route::post('photo-request/remove', [PhotoRequestController::class, 'remove']);

    ## Express Interest :
    Route::post('express-interest', [ExpressInterestController::class, 'index']);
    Route::post('express-interest/send', [ExpressInterestController::class, 'send']);
    Route::post('express-interest/accept-reject', [ExpressInterestController::class, 'acceptReject']);
    Route::post('express-interest/remove', [ExpressInterestController::class, 'remove']);

    ## Viewed Profile :
    Route::post('viewed-profile', [ViewedProfileController::class, 'index']);
    ## Viewed Contact :
    Route::post('viewed-contact', [ViewedContactController::class, 'index']);

    ## Matches, Premium & Suggested Matches. :
    Route::post('matches/recommended', [MatchesController::class, 'recommended']);
    Route::post('matches/premium', [MatchesController::class, 'premium']);
    Route::post('matches/near-by-me', [MatchesController::class, 'nearByMe']);
    Route::post('matches/suggested', [MatchesController::class, 'suggested']);

    ## Recently Joined & Login :
    Route::post('matches/recenly-joined', [MatchesController::class, 'recentlyJoined']);
    Route::post('matches/recenly-login', [MatchesController::class, 'recentlyLogin']);
    Route::post('matches/featured-member', [MatchesController::class, 'featuredMember']);

    ## Receive Admin Matches :
    Route::post('receive-admin-matches', [ReceiveAdminMatchController::class, 'index']);
    Route::post('receive-admin-matches/accept-reject', [ReceiveAdminMatchController::class, 'acceptReject']);

    ## Fix Meetings :
    Route::post('my-meeting', [FixMeetingsController::class, 'index']);
    Route::post('my-meeting/accept-reject', [FixMeetingsController::class, 'acceptReject']);

    ## Personalize Chat :
    Route::post('personalize-chat/get-messages', [PersonalizeChatController::class, 'getMessages']);
    Route::post('personalize-chat/send-message', [PersonalizeChatController::class, 'sendMessage']);

    ## Membership Plan :
    Route::get('membership-plan', [MembershipPlanController::class, 'index']);
    Route::get('membership-plan/add-on-package-list', [MembershipPlanController::class, 'addOnPackageList']);
    Route::post('membership-plan/checkout', [MembershipPlanController::class, 'checkout']);
    Route::post('membership-plan/calculate', [MembershipPlanController::class, 'calculatePrice']);
    ## Assign Membership Plan :
    Route::post('membership-plan/assign-plan', [MembershipPlanController::class, 'assignPlan']);

    ## Current & Plan History :
    Route::get('membership-plan/current-plan', [CurrentPlanController::class, 'index']);
    Route::post('membership-plan/plan-history', [CurrentPlanController::class, 'history']);

    ## Video Voice Call History :
    Route::post('call-history', [VideoVoiceCallController::class, 'index']);
    Route::post('call-history/add-call-minutes', [VideoVoiceCallController::class, 'addCallMinutes']);

    ## Privacy Setting List :
    Route::get('privacy-settings', [PrivacySettingController::class, 'index']);
    Route::post('privacy-settings/update', [PrivacySettingController::class, 'updatePrivacySetting']);
    Route::post('privacy-settings/change-password', [PrivacySettingController::class, 'changePassword']);
    Route::post('privacy-settings/update-alerts', [PrivacySettingController::class, 'updateAlertSetting']);
    
    ## Custom Chat :
    Route::post('chat/list', [ChatController::class, 'index']);
    Route::post('chat/conversation-list', [ChatController::class, 'chatConversation']);
    Route::post('chat/send-message', [ChatController::class, 'sendMessage']);
    
    Route::post('chat/request', [ChatController::class, 'chatRequest']);
    
    ## Block / Unblock in Chat :
    Route::post('chat/block-unblock', [ChatController::class, 'blockUnblockMember']);

    ## Contact Us:
    Route::post('contact-us', [ContactUsController::class, 'submit']);

    ## Report Profile:
    Route::post('report-profile', [ReportProfileController::class, 'submit']);

    ## Success Story :
    Route::post('success-story', [SuccessStoryController::class, 'index']);
    Route::post('success-story/details', [SuccessStoryController::class, 'details']);
    Route::post('success-story/add-story', [SuccessStoryController::class, 'submit']);

    ## AI Interst :
    Route::get('ai-interest/list', [AiAutoInterestController::class, 'index']);
    Route::post('ai-interest/update-settings', [AiAutoInterestController::class, 'updateSettings']);

    ## AI Match :
    Route::post('ai-match-making/list', [AiMatchMakingController::class, 'index']);
    Route::get('ai-match-making/best-matches-today', [AiMatchMakingController::class, 'bestMatchesToday']);
});

<?php

use App\Http\Controllers\Migration\DatabaseMigrationController;
use App\Http\Controllers\Migration\ShortlistMigrationController;
use App\Http\Controllers\Migration\DeleteProfileMigrationController;
use App\Http\Controllers\Migration\ContactRequestMigrationController;
use App\Http\Controllers\Migration\AssignHistoryMigrationController;
use App\Http\Controllers\Migration\UserMigrationController;
use App\Http\Controllers\Migration\StaticMasterMigrationController;
use App\Http\Controllers\Migration\PaymentMigrationController;
use App\Http\Controllers\Migration\MembershipPlanMigrationController;
use App\Http\Controllers\Migration\CommentMasterMigrationController;
use App\Http\Controllers\Migration\LeadCommentMigrationController;
use App\Http\Controllers\Migration\LeadGenerationMigrationController;
use App\Http\Controllers\Migration\ChatMigrationController;
use App\Http\Controllers\Migration\SeoPageMigrationController;
use App\Http\Controllers\Migration\NotificationMigrationController;
use App\Http\Controllers\Migration\AdminNotificationMigrationController;
use App\Http\Controllers\Migration\MatchListMigrationController;
use App\Http\Controllers\Migration\StaffMigrationController;
use App\Http\Controllers\Migration\StaffRoleMigrationController;
use App\Http\Controllers\Migration\ExpressInterestMigrationController;
use App\Http\Controllers\Migration\RequestCallBackMigrationController;
use App\Http\Controllers\Migration\SiteConfigMigrationController;
use App\Http\Controllers\Migration\EducationMasterMigrationController;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\{
    HomeController,
    PersonalizeHomeController,
    CmsPagesController,
    FAQController,
    AboutUsController,
    CommonRequestController,
    LoginController,
    RegisterController,
    ForgotPasswordController,
    ResetPasswordController,
    ContactUsController,
    EventsController,
    WeddingVendorsController,
    MatrimonyPagesController,
    AdvertisementController,
    NewsletterController,
    SuccessStoryController,
    BlogController,
    SearchController,
    LanguageController,
    DashboardController,
    ShortListController,
    BlockListController,
    ChatController,
    ViewedProfileController,
    ViewedContactController,
    SavedSearchController,
    MyProfileController,
    DeleteProfileController,
    InviteLinksController,
    PhotoRequestController,
    UserProfileController,
    VideoVoiceCallController,
    ExpressInterestController,
    MembershipPlanController,
    CurrentPlanController,
    ReportProfileController,
    MatchesController,
    PrivacySettingController,
    PaymentController,
    ReceiveAdminMatchController,
    NotificationController,
    MobileVerificationController,
    FixMeetingsController,
    PersonalizeChatController,
    FcmController,
    AiAutoInterestController,
    AiMatchMakingController,
    RequestCallBackController
};
use Illuminate\Support\Facades\Artisan;

Route::get('/run-migration', function () {
    // abort_if(!app()->environment('local'), 403);
    Artisan::call('migrate', ['--force' => true]);
    return "Migrations have been run!";
});

## For Storage Link:
Route::get('/link-storage/{key}', function ($key) {
    if ($key !== 'rozer@2550') {
        abort(403, 'Unauthorized');
    }
    Artisan::call('storage:link');
    return response('<h2>✔ Laravel storage:link successfully.</h2>');
});


## Cache Clear:
Route::get('/clear-optimize/{key}', function ($key) {
    if ($key !== 'rozer@2550') {
        abort(403, 'Unauthorized');
    }

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('clear-compiled');
    Artisan::call('optimize:clear');

    // Re-cache config and routes if needed
    Artisan::call('config:cache');
    Artisan::call('route:cache');

    return response('<h2>✔ Laravel cache cleared and optimized successfully.</h2>');
});

Route::group(['prefix' => ''], function (): void {

    Route::get('/theme.css', function () {
        return response(\App\Services\ThemeService::cachedCss(), 200)
            ->header('Content-Type', 'text/css');
    })->name('theme.css');

    ## Download Biodata :
    Route::get('/member/{member}/biodata/pdf', [MyProfileController::class, 'downloadBiodataPdf'])->name('web.myProfile.downloadBiodataPdf');

    ## Language Changes :
    Route::get('language/{locale}', [LanguageController::class, 'changeLanguage'])->name('language.change');

    ## Cms Pages :
    Route::get('/pages/{slug}', [CmsPagesController::class, 'index'])->name('web.cmsPages.index');
    ## Faqs Pages :
    Route::get('/faq', [FAQController::class, 'index'])->name('web.faq.index');

    ## About Us:
    Route::get('/about-us', [AboutUsController::class, 'index'])->name('web.aboutUs.index');

    ## Contact Us :
    Route::get('/contact-us', [ContactUsController::class, 'index'])->name('web.contactUs.index');
    Route::post('/contact-inqury', [ContactUsController::class, 'submit'])->name('web.contactUs.submit');

    ## Event:
    Route::prefix('events')->name('web.event.')->group(function () {
        Route::get('/',                         [EventsController::class, 'index'])->name('index');
        Route::get('/show/{id}',                [EventsController::class, 'show'])->name('show');
        Route::get('/checkout/{id}',            [EventsController::class, 'checkout'])->name('checkout');
        Route::post('/checkout/{id}/store',     [EventsController::class, 'storeCheckout'])->name('checkout.store');
        Route::get('/paynow/{id}',              [EventsController::class, 'paynow'])->name('paynow');
        Route::match(['GET', 'POST'], '/payment/handle', [EventsController::class, 'handlePayment'])->name('payment.handle');
        Route::get('/success',                  [EventsController::class, 'success'])->name('success');
        Route::get('/failed',                   [EventsController::class, 'failed'])->name('failed');
        Route::get('/invoice/download/{registrationId}', [EventsController::class, 'downloadInvoice'])->name('invoice.download');
    });
    Route::post('/payment/create-order', [EventsController::class, 'createOrder'])->name('web.event.createOrder');

    ## Wedding Vendors:
    Route::get('/vendor-categories', [WeddingVendorsController::class, 'index'])->name('web.weddingVendors.index');
    Route::get('/wedding-vendors/captcha', [WeddingVendorsController::class, 'refreshCaptcha'])->name('web.weddingVendor.captcha');
    Route::post('/wedding-vendors/book-vendor', [WeddingVendorsController::class, 'bookVenue'])->name('web.weddingVendor.bookVenue');
    Route::post('/wedding-vendors/add-review', [WeddingVendorsController::class, 'addVendorReview'])->name('web.weddingVendor.addVendorReview');
    Route::get('/vendors/{vendor}/reviews', [WeddingVendorsController::class, 'getVendorReviews'])->name('web.weddingVendor.getVendorReviews');
    Route::get('/wedding-vendors/{category:id?}', [WeddingVendorsController::class, 'vendorList'])->name('web.weddingVendors.vendorList');
    Route::get('/wedding-vendors/{category:id}/details/{vendor:id}', [WeddingVendorsController::class, 'vendorDetails'])->name('web.weddingVendors.vendorDetails');

    ## Advertisement with us:
    Route::get('/advertisement', [AdvertisementController::class, 'index'])->name('web.advertisement.index');
    Route::post('/advertisement/submit', [AdvertisementController::class, 'submitInquiry'])->name('web.advertisement.submitInquiry');
    Route::get('/advertisement/captcha', [AdvertisementController::class, 'refreshCaptcha'])->name('web.advertisement.captcha');

    ## News Letter Subscribe :
    Route::post('/newsletter-subscribe', [NewsletterController::class, 'subscribe'])->name('web.newsletter.subscribe');

    ## Common requests :
    Route::get('/search-cities', [CommonRequestController::class, 'searchCities'])->name('web.searchCities');
    Route::post('/get-dependency-dropdown-data', [CommonRequestController::class, 'getDependencyData'])->name('get.location.getDependencyData');

    ## Success Story :
    Route::get('/success-story', [SuccessStoryController::class, 'index'])->name('web.successStory.index');
    Route::get('/success-story/details/{id}', [SuccessStoryController::class, 'details'])->name('web.successStory.details');
    Route::post('/success-story/add-story', [SuccessStoryController::class, 'submit'])->name('web.successStory.submit');

    ## Blog:
    Route::prefix('blog')->name('web.blog.')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('/{slug}', [BlogController::class, 'details'])->name('details');
    });

    ## Membership Plan :
    Route::get('/membership-plan', [MembershipPlanController::class, 'index'])->name('web.membershipPlan.index');
    ## Download Invoice:
    Route::get('membership-plan/download-invoice/{id}', [CurrentPlanController::class, 'downloadInvoice'])->name('web.currentPlan.downloadInvoice');

    ## FCM Firebase Token Update :
    Route::post('/fcm/subscribe-topic', [FcmController::class, 'subscribeTopic'])->name('web.fcm.subscribe.topic');

    ## Verify Email:
    Route::get('confirm-email/{token}', [LoginController::class, 'verifyEmail'])->name('web.confirm.email');

    Route::get('/personalize', [PersonalizeHomeController::class, 'index'])->name('web.personalize.index');
    Route::post('/personalized-enquiry', [PersonalizeHomeController::class, 'storeEnquiry'])->name('web.personalize.storeEnquiry');

    Route::post('/request-call-bacl', [RequestCallBackController::class, 'store'])->name('web.requestCallBack.submit');

    Route::group(['middleware' => 'web.guest'], function (): void {
        Route::get('/', [HomeController::class, 'index'])->name('web.home.index');

        ## Login With Email, Matri Id and Otp :
        Route::prefix('login')->name('web.login.')->group(function () {
            Route::get('/', [LoginController::class, 'index'])->name('index');
            Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
            ## OTP :
            Route::post('/check-mobile', [LoginController::class, 'checkMobile'])->name('checkMobile');
            Route::post('/send-otp', [LoginController::class, 'sendOtp'])->name('sendOtp');
            Route::post('/verify-otp', [LoginController::class, 'verifyOtp'])->name('verifyOtp');
            Route::post('/resend-otp', [LoginController::class, 'resendOtp'])->name('resendOtp');
            ## OTP - Firebase :
            Route::post('/verify-firebase-otp', [LoginController::class, 'verifyFirebaseOtp'])->name('verifyFirebaseOtp');
            ## Captcha :
            Route::get('/captcha', [LoginController::class, 'refreshCaptcha'])->name('captcha');
        });

        ## Register :
        Route::get('/register', [RegisterController::class, 'index'])->name('web.register.index');
        Route::get('/register/{type}/ref/{code}', [RegisterController::class, 'index'])->where('type', 'franchise|affiliate')->name('web.register.referral');
        Route::post('/register/store', [RegisterController::class, 'store'])->name('web.register.store');
        Route::get('/register/next', [RegisterController::class, 'nextStep'])->name('web.register.nextStep');
        Route::post('/register/steps/save', [RegisterController::class, 'submitSteps'])->name('web.register.submitSteps');
        Route::get('/register/steps/success', [RegisterController::class, 'successPage'])->name('web.register.success');
        Route::post('register/generate-about-me', [RegisterController::class, 'generateAiAboutMe'])->name('web.register.generateAiAboutMe');
        ## Captcha :
        Route::get('/register/captcha', [RegisterController::class, 'refreshCaptcha'])->name('web.register.captcha');

        Route::get('forgot-password', [ForgotPasswordController::class, 'index'])->name('web.forgotPassword.index');
        Route::post('forgot-password/send', [ForgotPasswordController::class, 'sendResetLink'])->name('web.forgotPassword.send');
        Route::get('forgot-password/captcha', [ForgotPasswordController::class, 'refreshCaptcha'])->name('web.forgotPassword.captcha');

        Route::get('reset-password/{token}',  [ResetPasswordController::class, 'index'])->name('web.resetPassword.index');
        Route::post('reset-password/reset',   [ResetPasswordController::class, 'resetPassword'])->name('web.resetPassword.reset');
    });

    Route::group(['middleware' => ['web.auth', 'web.check.deleted', 'web.suspicious.check']], function (): void {
        Route::post('logout', [LoginController::class, 'logout'])->name('web.logout');

        ## Dashboard :
        Route::get('dashboard', [DashboardController::class, 'index'])->name('web.dashboard.index');
        Route::post('/send-confirmation-email', [DashboardController::class, 'sendConfirmationEmail'])->name('web.dashboard.sendConfirmationEmail');

        ## Verify Mobile Number :
        Route::post('/generate-mobile-otp', [MobileVerificationController::class, 'generateOtp'])->name('web.mobile.generateOtp');
        Route::post('/resend-mobile-otp', [MobileVerificationController::class, 'resendOtp'])->name('web.mobile.resendOtp');
        Route::post('/verify-mobile-otp', [MobileVerificationController::class, 'verifyOtp'])->name('web.mobile.verifyOtp');
        Route::post('mobile/verify-firebase-otp', [MobileVerificationController::class, 'verifyFirebaseOtp'])->name('web.mobile.verifyFirebaseOtp');

        ## My Profiles :
        Route::get('my-profile', [MyProfileController::class, 'index'])->name('web.myProfile.index');
        Route::get('my-profile/edit-profile/{id}', [MyProfileController::class, 'editProfile'])->name('web.myProfile.editProfile');
        Route::post('my-profile/update-profile', [MyProfileController::class, 'updateProfile'])->name('web.myProfile.updateProfile');
        Route::post('my-profile/remove-photo', [MyProfileController::class, 'removePhoto'])->name('web.myProfile.removePhoto');
        Route::post('my-profile/remove-id-proof', [MyProfileController::class, 'removeIdProof'])->name('web.myProfile.removeIdProof');
        Route::post('my-profile/remove-horoscope', [MyProfileController::class, 'removeHoroscope'])->name('web.myProfile.removeHoroscope');
        Route::post('my-profile/generate-about-me', [MyProfileController::class, 'generateAiAboutMe'])->name('web.myProfile.generateAiAboutMe');

        ## Shortlist Profile :
        Route::prefix('shortlist')->name('web.shortlist.')->group(function () {
            Route::get('/', [ShortListController::class, 'index'])->name('index');
            Route::post('/add-remove', [ShortListController::class, 'addRemove'])->name('addRemove');
            Route::post('/remove/{id}', [ShortListController::class, 'remove'])->name('remove');
        });

        ## Blocklist Profile :
        Route::prefix('blocklist')->name('web.blocklist.')->group(function () {
            Route::get('/', [BlockListController::class, 'index'])->name('index');
            Route::post('/action', [BlockListController::class, 'addRemove'])->name('addRemove');
            Route::post('/remove/{id}', [BlockListController::class, 'remove'])->name('remove');
        });

        ## Viewed Profile :
        Route::prefix('viewed-profile')->name('web.viewedProfile.')->group(function () {
            Route::get('/{type}', [ViewedProfileController::class, 'index'])->name('index');
        });
        ## Viewed Contact :
        Route::prefix('viewed-contact')->name('web.viewedContact.')->group(function () {
            Route::get('/{type}', [ViewedContactController::class, 'index'])->name('index');
        });

        ## Photo Requests :
        Route::prefix('photo-request')->name('web.photoRequest.')->group(function () {
            Route::get('/{type}', [PhotoRequestController::class, 'index'])->name('index');
            Route::post('/send', [PhotoRequestController::class, 'send'])->name('send');
            Route::post('/remove/{id}', [PhotoRequestController::class, 'remove'])->name('remove');
            Route::post('/accept/{id}', [PhotoRequestController::class, 'accept'])->name('accept');
            Route::post('/reject/{id}', [PhotoRequestController::class, 'reject'])->name('reject');
        });

        ## Express Interest :
        Route::prefix('express-interest')->name('web.expressInterest.')->group(function () {
            Route::get('/', [ExpressInterestController::class, 'index'])->name('index');
            Route::post('/send', [ExpressInterestController::class, 'send'])->name('send');
            Route::post('/remove/{id}', [ExpressInterestController::class, 'remove'])->name('remove');
            Route::post('/accept/{id}', [ExpressInterestController::class, 'accept'])->name('accept');
            Route::post('/reject/{id}', [ExpressInterestController::class, 'reject'])->name('reject');
        });

        ## Search :
        Route::get('/search', [SearchController::class, 'searchResult'])->name('web.search.searchResult');
        Route::get('/search/{type}', [SearchController::class, 'index'])
            ->where('type', 'quick-search|advance-search|keyword-search|id-search')->name('web.search.type');

        ## Saved Search :
        Route::prefix('saved-search')->name('web.savedSearch.')->group(function () {
            Route::get('/', [SavedSearchController::class, 'index'])->name('index');

            Route::delete('/delete/{id}', [SavedSearchController::class, 'destroy'])->name('delete');
            Route::get('/apply/{id}', [SavedSearchController::class, 'apply'])->name('apply');
            Route::post('/save', [SavedSearchController::class, 'savedSearch'])->name('save');
        });

        ## Delete Profile :
        Route::prefix('delete-profile')->name('web.deleteProfile.')->group(function () {
            Route::get('/', [DeleteProfileController::class, 'index'])->name('index');
            Route::post('request', [DeleteProfileController::class, 'deleteProfile'])->name('request');
        });

        ## InviteLinks :
        Route::prefix('invite-links')->name('web.inviteLinks.')->group(function () {
            Route::get('/', [InviteLinksController::class, 'index'])->name('index');
        });

        ## User Profile :
        Route::prefix('user-profile')->name('web.userProfile.')->group(function () {
            Route::get('/{id}', [UserProfileController::class, 'index'])->name('index');
            Route::post('view-contact', [UserProfileController::class, 'viewContact'])->name('viewContact');
        });

        ## Report Profile:
        Route::post('report-profile', [ReportProfileController::class, 'submit'])->name('web.reportProfile.submit');

        ## Video Voice Call History :
        Route::prefix('call-history')->name('web.videoVoiceCall.')->group(function () {
            Route::get('/', [VideoVoiceCallController::class, 'index'])->name('index');
            Route::get('/initite-video-call/{id}', [VideoVoiceCallController::class, 'inititeVideoCall'])->name('inititeVideoCall');
            Route::get('/initite-voice-call/{id}', [VideoVoiceCallController::class, 'inititeVoiceCall'])->name('inititeVoiceCall');
            Route::post('/add-call-minutes', [VideoVoiceCallController::class, 'addCallMinutes'])->name('addCallMinutes');
        });

        ## Ai Interest Send:
        Route::prefix('ai-auto-interest')->name('web.aiAutoInterest.')->group(function () {
            Route::get('/', [AiAutoInterestController::class, 'index'])->name('index');
            Route::post('/toggle', [AiAutoInterestController::class, 'toggleAutoInterest'])->name('toggle');
            Route::post('/update-settings', [AiAutoInterestController::class, 'updateSettings'])->name('updateSettings');
        });

        ## Ai Matchmaking:
        Route::prefix('ai-match-making')->name('web.aiMatchMaking.')->group(function () {
            Route::get('/', [AiMatchMakingController::class, 'index'])->name('index');
            Route::get('/get-matches', [AiMatchMakingController::class, 'getMatches'])->name('getMatches');
            Route::get('/best-matches-today', [AiMatchMakingController::class, 'bestMatchesToday'])->name('bestMatchesToday');
        });

        ## Matches, Premium & Suggested Matches. :
        Route::prefix('matches')->name('web.matches.')->group(function () {
            Route::get('/recommended', [MatchesController::class, 'recommended'])->name('recommended');
            Route::get('/premium', [MatchesController::class, 'premium'])->name('premium');
            Route::get('/near-by-me', [MatchesController::class, 'nearByMe'])->name('nearByMe');
            Route::get('/suggested', [MatchesController::class, 'suggested'])->name('suggested');
        });

        ## Receive Admin Matches :
        Route::prefix('receive-admin-matches')->name('web.receiveAdminMatch.')->group(function () {
            Route::get('/', [ReceiveAdminMatchController::class, 'index'])->name('index');
            Route::post('/accept-reject', [ReceiveAdminMatchController::class, 'acceptReject'])->name('acceptReject');
        });
        ## Fix Meetings :
        Route::prefix('my-meeting')->name('web.fixMeetings.')->group(function () {
            Route::get('/', [FixMeetingsController::class, 'index'])->name('index');
            Route::post('/accept-reject', [FixMeetingsController::class, 'acceptReject'])->name('acceptReject');
        });

        ## personalize chat :
        Route::prefix('personalize-chat')->name('web.personalizeChat.')->group(function () {
            Route::get('/', [PersonalizeChatController::class, 'index'])->name('index');
            Route::post('get-messages', [PersonalizeChatController::class, 'getMessages'])->name('getMessages');
            Route::post('send-message', [PersonalizeChatController::class, 'sendMessage'])->name('sendMessage');
        });

        Route::prefix('membership-plan')->name('web.membershipPlan.')->group(function () {
            Route::get('/checkout/{id}', [MembershipPlanController::class, 'checkout'])->name('checkout');
            Route::post('/calculate-price', [MembershipPlanController::class, 'calculatePrice'])->name('calculatePrice');
            Route::post('/calculate-add-on-price', [MembershipPlanController::class, 'calculateAddOnPreview'])->name('calculateAddOnPreview');
        });

        Route::post('membership/pay', [PaymentController::class, 'createOrder'])->name('web.membership.pay');
        Route::match(['GET', 'POST'], 'membership/verify/razorpay', [PaymentController::class, 'razorpaySuccess'])->name('web.membership.razorpaySuccess');
        Route::get('membership/success/{payment}', [PaymentController::class, 'success'])->name('web.membership.success');
        Route::get('membership/failed', [PaymentController::class, 'failed'])->name('web.membership.failed');
        Route::match(['GET', 'POST'], 'membership/addon/pay', [PaymentController::class, 'createAddOnOrder'])->name('web.membership.addonPay');
        Route::get('membership/paypal-cancel', [PaymentController::class, 'paypalCancel'])->name('web.membership.paypalCancel');
        Route::get('membership/paypal-success', [PaymentController::class, 'paypalSuccess'])->name('web.membership.paypalSuccess');
        // Stripe, Cashfree, and PhonePe:
        Route::match(['GET', 'POST'], 'membership/verify/stripe', [PaymentController::class, 'stripeSuccess'])->name('web.membership.stripeSuccess');
        Route::match(['GET', 'POST'], 'membership/verify/cashfree', [PaymentController::class, 'cashfreeSuccess'])->name('web.membership.cashfreeSuccess');
        Route::match(['GET', 'POST'], 'membership/verify/phonepe', [PaymentController::class, 'phonepeSuccess'])->name('web.membership.phonepeSuccess');

        ## Current Plan & Recent Plan :
        Route::prefix('membership-plan')->name('web.currentPlan.')->group(function () {
            Route::get('/current-plan', [CurrentPlanController::class, 'index'])->name('index');
            Route::get('/plan-history', [CurrentPlanController::class, 'history'])->name('history');
            Route::get('/view-invoice/{id}', [CurrentPlanController::class, 'viewInvoice'])->name('viewInvoice');
        });

        ## Privacy Settings :
        Route::prefix('privacy-settings')->name('web.privacySettings.')->group(function () {
            Route::get('/', [PrivacySettingController::class, 'index'])->name('index');
            Route::post('/update-privacy', [PrivacySettingController::class, 'updatePrivacySetting'])->name('update');
            Route::post('/change-password', [PrivacySettingController::class, 'changePassword'])->name('changePassword');
            Route::post('/update-alerts', [PrivacySettingController::class, 'updateAlertSetting'])->name('updateAlertSetting');
        });

        ## Notification :
        Route::post('/notification/mark-read/{id}', [NotificationController::class, 'markRead'])->name('notification.markRead');
        Route::post('/notification/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notification.markAllRead');

        ## Chat :
        // Page Chat:
        Route::get('/chat', [ChatController::class, 'index'])->name('web.chat.index');
        Route::get('/chat/conversation/{id}', [ChatController::class, 'chatConversation'])->name('web.chat.chatConversation');
        Route::get('/chat/list', [ChatController::class, 'getChatList'])->name('web.chat.getChatList');
        Route::get('/chat/online-members', [ChatController::class, 'onlineMembers'])->name('web.chat.onlineMembers');
        Route::get('/chat/messages/{id}', [ChatController::class, 'getMessages'])->name('web.chat.getMessages');
        Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('web.chat.sendMessage');
        Route::post('/chat/create', [ChatController::class, 'createConversation'])->name('web.chat.createConversation');
        ## Chat Requests :
        Route::post('request/send', [ChatController::class, 'sendChatRequest'])->name('web.chat.sendChatRequest');
        Route::post('request/cancel', [ChatController::class, 'cancelChatRequest'])->name('web.chat.cancelChatRequest');
        Route::post('request/respond', [ChatController::class, 'respondChatRequest'])->name('web.chat.respondChatRequest');
        ## Block / Unblock in Chat :
        Route::post('block-unblock', [ChatController::class, 'blockUnblockMember'])->name('web.chat.blockUnblockMember');
    });

    ## Suspicous Activity:
    Route::get('/account-suspended', function () {
        return view('web.suspiciousActivity.suspended');
    })->name('web.member.suspended');

    Route::get('/matrimony/more-details/{type}', [MatrimonyPagesController::class, 'moreDetails'])->name('web.matrimony.moreDetails');
    Route::get('/matrimony/{slug}/members', [MatrimonyPagesController::class, 'members'])->where('slug', '[a-z0-9-]+')->name('web.matrimony.members');
    Route::get('/matrimony/{slug}', [MatrimonyPagesController::class, 'index'])->where('slug', '[a-z0-9-]+')->name('web.matrimony.index');


    ## Database Migration Master Tables:
    Route::prefix('migrate')->group(function () {
        ## Old DB -> new DB :
        Route::get('/database/migrate', [DatabaseMigrationController::class, 'migrate']);
        Route::get('/database/shortlist/migrate',  [ShortlistMigrationController::class, 'migrate']);
        Route::get('/database/delete-profile/migrate', [DeleteProfileMigrationController::class, 'migrate']);
        Route::get('/database/contact-request/migrate', [ContactRequestMigrationController::class, 'migrate']);
        Route::get('/database/assign-history/migrate', [AssignHistoryMigrationController::class, 'migrate']);

        ## Static arrays -> tables :
        Route::get('database/static-masters',    [StaticMasterMigrationController::class, 'databaseMIgration']);

        ## Register Migration Old Data to new database :
        Route::get('database/registers',          [UserMigrationController::class, 'registers']);
        Route::get('database/register-partners',  [UserMigrationController::class, 'registerPartners']);
        Route::get('database/users-all',          [UserMigrationController::class, 'all']); // both, in order
        Route::get('database/partners-not-exists-data',          [UserMigrationController::class, 'partnerNotExistData']);
        // Route::get('migrate/fill-missing-partners', [UserMigrationController::class, 'fillMissingPartners']);

        Route::get('database/payments', [PaymentMigrationController::class, 'payments']);


        Route::get('lead-generations', [LeadGenerationMigrationController::class, 'leadGenerations']);
        ## Comment Of lead generation :
        Route::get('comment-master', [CommentMasterMigrationController::class, 'commentMaster']);
        Route::get('lead-comments', [LeadCommentMigrationController::class, 'leadComments']);

        ## Chat Migrations :
        Route::get('chat-conversations', [ChatMigrationController::class, 'conversations']);
        Route::get('chat-messages',      [ChatMigrationController::class, 'messages']);
        Route::get('chat-all',           [ChatMigrationController::class, 'all']); // both, in order

        Route::get('seo-pages', [SeoPageMigrationController::class, 'seoPages']);

        Route::get('member-notifications', [NotificationMigrationController::class, 'memberNotifications']);
        Route::get('admin-notifications', [AdminNotificationMigrationController::class, 'adminNotifications']);

        Route::get('match-list', [MatchListMigrationController::class, 'matchList']);

        Route::get('staff', [StaffMigrationController::class, 'staff']);

        Route::get('staff-roles', [StaffRoleMigrationController::class, 'staffRoles']);

        Route::get('express-interest', [ExpressInterestMigrationController::class, 'expressInterest']);
        Route::get('request-call-back', [RequestCallBackMigrationController::class, 'requestCallBack']);
        
        Route::get('education-master', [EducationMasterMigrationController::class, 'educationMaster']);
        
        ## Not required now :
        // Route::get('site-config', [SiteConfigMigrationController::class, 'siteConfig']);
        // Route::get('/database/membership-plan', [MembershipPlanMigrationController::class, 'databaseMigration']);
    });
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    AdminLoginController,
    StaffLoginController,
    AdminDashboardController,
    SiteSettingController,
    CurrencyManagementController,
    ReligionController,
    CasteController,
    CountryController,
    StateController,
    CityController,
    CommonRequestController,
    OccupationController,
    MotherTongueController,
    StarController,
    MoonsignController,
    FaqListController,
    MemberDesignLayoutController,
    PaymentOptionsController,
    MembershipPlanController,
    StaffController,
    StaffRoleController,
    CouponCodeController,
    BlogController,
    CmsPagesController,
    SmsTemplatesController,
    MemberController,
    SuccessStoryController,
    EducationMasterController,
    DesignationMasterController,
    SalesReportsController,
    PhotosApprovalController,
    IdProofController,
    HoroscopeApprovalController,
    PhotoRequestController,
    ExpressInterestController,
    ViewContactController,
    DeleteProfileController,
    ExpiredMemberController,
    PaidActiveMemberController,
    MatchMakingMemberController,
    StaffAssignHistoryController,
    StaffUnassignHistoryController,
    FollowedUpReportController,
    LeadFollowedUpReportController,
    AnnualIncomeController,
    DownloadBackupController,
    ContactInquiryController,
    HomePageSectionController,
    FeaturedMemberController,
    EmployeeMasterController,
    EmailSubscribeController,
    ProfileReportSpamController,
    AnnoucementBannerController,
    BulkNotificationController,
    AdvertisementController,
    AdvertisementInquiryController,
    MatrimonyDataController,
    BulkEmailController,
    SeoManagementController,
    LeadGenerationController,
    LeadGenerationReportController,
    UserLoginHistoryController,
    EventController,
    EventReportController,
    WeddingVendorsController,
    VendorsCategoryController,
    FranchiseController,
    AutoMatchMakingController,
    AutoMatchHistoryController,
    FranchiseMemberController,
    FranchiseSalesReportsController,
    FranchiseAssignHistoryController,
    FranchiseUnassignHistoryController,
    FranchiseLeadAssignHistoryController,
    FranchiseLeadUnAssignHistoryController,
    StaffLeadAssignHistoryController,
    StaffLeadUnAssignHistoryController,
    FranchiseLoginController,
    WhatsappConfigurationController,
    TicketManagementController,
    VendorsReviewController,
    AddOnPackageController,
    AdminLeaveManagementController,
    VendorInquiryController,
    PersonalizeMemberController,
    PersonalizeReportController,
    PersonalizeMatchMakingController,
    PersonalizeMeetingController,
    PersonalizeChatController,
    LanguageMasterController,
    LanguageTemplatesController,
    NotificationTemplatesController,
    AdminNotificationListController,
    AdminReimbursementsController,
    StaffDashboardController,
    StaffLoginHistoryController,
    FranchiseDashboardController,
    FranchiseLoginHistoryController,
    AffiliateMemberloginHistoryController,
    AutoSessionExpiredController,
    AffiliateMemberIncomeController,
    AffiliateMemberPaymentController,
    AffiliateMemberAssignController,
    AffiliateMemberUnassignController,
    AffiliateMemberController,
    AffiliateHomepageController,
    ManualMatchMakingController,
    ManglikController,
    HoroscopeController,
    ComplexionController,
    BloodGroupController,
    BodytypeController,
    DrinkHabitController,
    EatingHabitController,
    EmailTemplatesController,
    SmokeHabitController,
    FamilyStatusController,
    FamilyTypeController,
    MaritalStatusController,
    TotalChildController,
    StatusChildController,
    MarriedBrotherController,
    MarriedSisterController,
    NoOfBrotherSisterController,
    ProfileByController,
    ResidenceController,
    OfflineGatewayController,
    AffiliateTestimonialController,
    MemberFieldCheckController,
    MemberPlaceholderController,
    PersonalizeEnquiryController,
    PersonalizeHomepageController,
    SeoSettingsController,
    ThirdPartySettingController,
    OtherWebsiteLayoutController,
    CallyzerApiSettingController,
    EmployeeAnalysisCallyzerController,
    EmployeeDetailCallyzerController,
    EmployeeSummaryCallyzerController,
    CallLogHistoryCallyzerController,
    NdaAndOtherDocsController,
    StaffAttendanceController,
    StaffCommissionController,
    StaffHolidayMasterController,
    StaffLeaderBoardController,
    StaffLeaveManagementController,
    StaffpayheadsController,
    StaffReimbursementController,
    StaffSalarySlipController,
    StaffScoreCardController,
    WebThemeSettingController,
    OnlineMemberController,
    SelfieApprovalController,
    AppThemeSettingController,
    SetupChecklistController,
    HomePageDesignController
};

Route::get('/admin', function () {
    return redirect()->route('admin.login');
});

Route::group(['prefix' => 'admin'], function (): void {
    Route::group(['middleware' => 'admin.guest:admin'], function (): void {
        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');

        ## New Login With Otp Routes :
        // Route::get('/login/firebase-config', [AdminLoginController::class, 'firebaseConfig'])->name('admin.login.firebaseConfig');
        // Route::post('/login/verify-firebase-otp', [AdminLoginController::class, 'verifyFirebaseOtp'])->name('admin.login.verifyFirebaseOtp');
        // Route::post('/login/check-blocked', [AdminLoginController::class, 'checkBlockedNumber'])->name('admin.login.checkBlocked');
    });

    Route::group(['middleware' => 'admin.guest:staff'], function (): void {
        Route::get('/staff/login', [StaffLoginController::class, 'index'])->name('staff.login');
        Route::post('/staff/authenticate', [StaffLoginController::class, 'authenticate'])->name('staff.authenticate');
    });

    Route::group(['middleware' => 'admin.guest:franchise'], function (): void {
        Route::get('/franchise/login', [FranchiseLoginController::class, 'index'])->name('franchise.login');
        Route::post('/franchise/authenticate', [FranchiseLoginController::class, 'authenticate'])->name('franchise.authenticate');
    });

    Route::group(['middleware' => ['admin.auth', 'admin.check.deleted']], function (): void {
        Route::get('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

        ## Staff Logout:
        Route::get('/staff/logout', [StaffLoginController::class, 'logout'])->name('staff.logout');

        // ## Franchise Logout:
        Route::get('/franchise/logout', [FranchiseLoginController::class, 'logout'])->name('franchise.logout');

        ## Common Request :
        Route::get('/search-cities', [CommonRequestController::class, 'searchCities'])->name('admin.searchCities');
        Route::post('/commonRequest/getList', [CommonRequestController::class, 'getList'])->name('admin.admin.getList');

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::post('/dashboard-data', [AdminDashboardController::class, 'dashboardData'])->name('admin.dashboard.dashboardData');
        Route::post('/get-graph-data', [AdminDashboardController::class, 'getGraphData'])->name('admin.dashboard.getGraphData');
        Route::post('/notification-read', [AdminDashboardController::class, 'notificationUnread'])->name('admin.dashboard.notificationUnread');
        ## For Clear All Cache:
        Route::post('/clear-cache', [AdminDashboardController::class, 'clearCache'])->name('admin.dashboard.clearCache');

        ## Site Settings
        ## basic Site Settings
        Route::get('/basic-site-setting', [SiteSettingController::class, 'index'])->name('admin.siteSetting');
        Route::post('/basic-site-settings-add', [SiteSettingController::class, 'basicSiteSettingsAddEdit'])->name('admin.basicSiteSettingsAddEdit');
        ## Check Admin Authentication:
        Route::post('/check-authentication', [SiteSettingController::class, 'checkAuthentication'])->name('admin.checkAuthentication');
        ## Analytics Code
        Route::post('/analytics-code-setting-add', [SiteSettingController::class, 'analyticsCodeAddEdit'])->name('admin.analyticsCodeAddEdit');
        ## Matri Prefix
        Route::post('/matri-prefix-add', [SiteSettingController::class, 'matriPrefixAddEdit'])->name('admin.matriPrefixAddEdit');
        ## Logo Favicon
        Route::post('/logo-favicon-add', [SiteSettingController::class, 'logoFaviconAddEdit'])->name('admin.logoFaviconAddEdit');

        ## Change Password
        Route::get('/change-password', [SiteSettingController::class, 'changePasswordAddEditForm'])->name('admin.changePasswordAddEditForm');
        Route::post('/change-password-add', [SiteSettingController::class, 'changePasswordAddEdit'])->name('admin.changePasswordAddEdit');

        ## App Link
        Route::get('/app-link', [SiteSettingController::class, 'appLinkAddEditForm'])->name('admin.appLinkAddEditForm');
        Route::post('/app-link-add', [SiteSettingController::class, 'appLinkAddEdit'])->name('admin.appLinkAddEdit');

        ## Social Media Link
        Route::get('/social-site-setting', [SiteSettingController::class, 'socialSiteSettingAddEditForm'])->name('admin.socialSiteSettingAddEditForm');
        Route::post('/social-site-setting-add', [SiteSettingController::class, 'socialSiteSettingAddEdit'])->name('admin.socialSiteSettingAddEdit');

        ## Third Party Settings :
        Route::get('/third-party-setting', [ThirdPartySettingController::class, 'index'])->name('admin.thirdPartySetting.index');
        Route::post('/firebase-setting-add', [ThirdPartySettingController::class, 'firebaseAddEdit'])->name('admin.firebaseAddEdit');
        Route::post('/email-add', [ThirdPartySettingController::class, 'emailAddEdit'])->name('admin.emailAddEdit');
        Route::post('/email/send-test-email', [ThirdPartySettingController::class, 'sendTestEmail'])->name('admin.sendTestEmail');
        Route::post('/zego-cloud-setting-add', [ThirdPartySettingController::class, 'zegoCloudSettingAddEdit'])->name('admin.zegoCloudSettingAddEdit');
        Route::post('/api-key-setting-add', [ThirdPartySettingController::class, 'aiApiKeysSettingAddEdit'])->name('admin.aiApiKeysSettingAddEdit');

        ## Homepage Management
        Route::get('/homepage-section', [HomePageSectionController::class, 'index'])->name('admin.homePageSection.index');
        Route::post('/homepage-section-update', [HomePageSectionController::class, 'update'])->name('admin.homePageSection.update');
        ## Home Page For Lang Data :
        Route::post('/home-page/getLangData', [HomePageSectionController::class, 'getLangData'])->name('admin.homePageSection.getLangData');

        ## Add New Details :
        ## Annoucement Banner :
        Route::get('/annoucement-banner', [AnnoucementBannerController::class, 'index'])->name('admin.annoucementBanner.index');
        Route::post('/annoucement-banner/getAjaxPaginationData', [AnnoucementBannerController::class, 'getAjaxPaginationData'])->name('admin.annoucementBanner.getAjaxPaginationData');
        Route::get('/annoucement-banner/add', [AnnoucementBannerController::class, 'addEditForm'])->name('admin.annoucementBanner.addForm');
        Route::get('/annoucement-banner/edit/{id}', [AnnoucementBannerController::class, 'addEditForm'])->name('admin.annoucementBanner.editForm');
        Route::post('/annoucement-banner/addEdit', [AnnoucementBannerController::class, 'addEdit'])->name('admin.annoucementBanner.addEdit');
        Route::post('/annoucement-banner/changeStatus', [AnnoucementBannerController::class, 'changeStatus'])->name('admin.annoucementBanner.changeStatus');
        Route::post('/annoucement-banner/getLangData', [AnnoucementBannerController::class, 'getLangData'])->name('admin.annoucementBanner.getLangData');

        ## Currency Management:
        Route::get('/currency-management', [CurrencyManagementController::class, 'index'])->name('admin.currency.index');
        Route::post('/currency-management/getAjaxPaginationData', [CurrencyManagementController::class, 'getAjaxPaginationData'])->name('admin.currency.getAjaxPaginationData');
        Route::get('/currency-management/add', [CurrencyManagementController::class, 'addEditForm'])->name('admin.currency.addForm');
        Route::get('/currency-management/edit/{id}', [CurrencyManagementController::class, 'addEditForm'])->name('admin.currency.editForm');
        Route::post('/currency-management/addEdit', [CurrencyManagementController::class, 'addEdit'])->name('admin.currency.addEdit');
        Route::post('/currency-management/changeStatus', [CurrencyManagementController::class, 'changeStatus'])->name('admin.currency.changeStatus');
        Route::post('/currency-management/get-language', [CurrencyManagementController::class, 'getLangData'])->name('admin.currency.getLangData');

        ## Religion
        Route::get('/religion', [ReligionController::class, 'index'])->name('admin.religion.index');
        Route::post('/religion/getAjaxPaginationData', [ReligionController::class, 'getAjaxPaginationData'])->name('admin.religion.getAjaxPaginationData');
        Route::get('/religion/add', [ReligionController::class, 'addEditForm'])->name('admin.religion.addForm');
        Route::get('/religion/edit/{id}', [ReligionController::class, 'addEditForm'])->name('admin.religion.editForm');
        Route::post('/religion/addEdit', [ReligionController::class, 'addEdit'])->name('admin.religion.addEdit');
        Route::post('/religion/changeStatus', [ReligionController::class, 'changeStatus'])->name('admin.religion.changeStatus');
        Route::post('/religion/get-language', [ReligionController::class, 'getLangData'])->name('admin.religion.getLangData');

        ## Caste
        Route::get('/caste', [CasteController::class, 'index'])->name('admin.caste.index');
        Route::post('/caste/getAjaxPaginationData', [CasteController::class, 'getAjaxPaginationData'])->name('admin.caste.getAjaxPaginationData');
        Route::get('/caste/add', [CasteController::class, 'addEditForm'])->name('admin.caste.addForm');
        Route::get('/caste/edit/{id}', [CasteController::class, 'addEditForm'])->name('admin.caste.editForm');
        Route::post('/caste/addEdit', [CasteController::class, 'addEdit'])->name('admin.caste.addEdit');
        Route::post('/caste/changeStatus', [CasteController::class, 'changeStatus'])->name('admin.caste.changeStatus');
        Route::post('/caste/get-language', [CasteController::class, 'getLangData'])->name('admin.caste.getLangData');

        ## Country
        Route::get('/country', [CountryController::class, 'index'])->name('admin.country.index');
        Route::post('/country/getAjaxPaginationData', [CountryController::class, 'getAjaxPaginationData'])->name('admin.country.getAjaxPaginationData');
        Route::get('/country/add', [CountryController::class, 'addEditForm'])->name('admin.country.addForm');
        Route::get('/country/edit/{id}', [CountryController::class, 'addEditForm'])->name('admin.country.editForm');
        Route::post('/country/addEdit', [CountryController::class, 'addEdit'])->name('admin.country.addEdit');
        Route::post('/country/changeStatus', [CountryController::class, 'changeStatus'])->name('admin.country.changeStatus');
        Route::post('/country/get-language', [CountryController::class, 'getLangData'])->name('admin.country.getLangData');

        ## State
        Route::get('/state', [StateController::class, 'index'])->name('admin.state.index');
        Route::post('/state/getAjaxPaginationData', [StateController::class, 'getAjaxPaginationData'])->name('admin.state.getAjaxPaginationData');
        Route::get('/state/add', [StateController::class, 'addEditForm'])->name('admin.state.addForm');
        Route::get('/state/edit/{id}', [StateController::class, 'addEditForm'])->name('admin.state.editForm');
        Route::post('/state/addEdit', [StateController::class, 'addEdit'])->name('admin.state.addEdit');
        Route::post('/state/changeStatus', [StateController::class, 'changeStatus'])->name('admin.state.changeStatus');
        Route::post('/state/get-language', [StateController::class, 'getLangData'])->name('admin.state.getLangData');

        ## City
        Route::get('/city', [CityController::class, 'index'])->name('admin.city.index');
        Route::post('/city/getAjaxPaginationData', [CityController::class, 'getAjaxPaginationData'])->name('admin.city.getAjaxPaginationData');
        Route::get('/city/add', [CityController::class, 'addEditForm'])->name('admin.city.addForm');
        Route::get('/city/edit/{id}', [CityController::class, 'addEditForm'])->name('admin.city.editForm');
        Route::post('/city/addEdit', [CityController::class, 'addEdit'])->name('admin.city.addEdit');
        Route::post('/city/changeStatus', [CityController::class, 'changeStatus'])->name('admin.city.changeStatus');
        Route::post('/city/get-language', [CityController::class, 'getLangData'])->name('admin.city.getLangData');

        ## Occupation
        Route::get('/occupation', [OccupationController::class, 'index'])->name('admin.occupation.index');
        Route::post('/occupation/getAjaxPaginationData', [OccupationController::class, 'getAjaxPaginationData'])->name('admin.occupation.getAjaxPaginationData');
        Route::get('/occupation/add', [OccupationController::class, 'addEditForm'])->name('admin.occupation.addForm');
        Route::get('/occupation/edit/{id}', [OccupationController::class, 'addEditForm'])->name('admin.occupation.editForm');
        Route::post('/occupation/addEdit', [OccupationController::class, 'addEdit'])->name('admin.occupation.addEdit');
        Route::post('/occupation/changeStatus', [OccupationController::class, 'changeStatus'])->name('admin.occupation.changeStatus');
        Route::post('/occupation/get-language', [OccupationController::class, 'getLangData'])->name('admin.occupation.getLangData');

        ## Education
        Route::get('/education', [EducationMasterController::class, 'index'])->name('admin.educationMaster.index');
        Route::post('/education/getAjaxPaginationData', [EducationMasterController::class, 'getAjaxPaginationData'])->name('admin.educationMaster.getAjaxPaginationData');
        Route::get('/education/add', [EducationMasterController::class, 'addEditForm'])->name('admin.educationMaster.addForm');
        Route::get('/education/edit/{id}', [EducationMasterController::class, 'addEditForm'])->name('admin.educationMaster.editForm');
        Route::post('/education/addEdit', [EducationMasterController::class, 'addEdit'])->name('admin.educationMaster.addEdit');
        Route::post('/education/changeStatus', [EducationMasterController::class, 'changeStatus'])->name('admin.educationMaster.changeStatus');
        Route::post('/education/get-language', [EducationMasterController::class, 'getLangData'])->name('admin.educationMaster.getLangData');

        ## For designation :
        Route::get('/designation', [DesignationMasterController::class, 'index'])->name('admin.designationMaster.index');
        Route::post('/designation/getAjaxPaginationData', [DesignationMasterController::class, 'getAjaxPaginationData'])->name('admin.designationMaster.getAjaxPaginationData');
        Route::get('/designation/add', [DesignationMasterController::class, 'addEditForm'])->name('admin.designationMaster.addForm');
        Route::get('/designation/edit/{id}', [DesignationMasterController::class, 'addEditForm'])->name('admin.designationMaster.editForm');
        Route::post('/designation/addEdit', [DesignationMasterController::class, 'addEdit'])->name('admin.designationMaster.addEdit');
        Route::post('/designation/changeStatus', [DesignationMasterController::class, 'changeStatus'])->name('admin.designationMaster.changeStatus');
        Route::post('/designation/get-language', [DesignationMasterController::class, 'getLangData'])->name('admin.designationMaster.getLangData');

        ## Employee In :
        Route::get('/employee', [EmployeeMasterController::class, 'index'])->name('admin.employeeMaster.index');
        Route::post('/employee/getAjaxPaginationData', [EmployeeMasterController::class, 'getAjaxPaginationData'])->name('admin.employeeMaster.getAjaxPaginationData');
        Route::get('/employee/add', [EmployeeMasterController::class, 'addEditForm'])->name('admin.employeeMaster.addForm');
        Route::get('/employee/edit/{id}', [EmployeeMasterController::class, 'addEditForm'])->name('admin.employeeMaster.editForm');
        Route::post('/employee/addEdit', [EmployeeMasterController::class, 'addEdit'])->name('admin.employeeMaster.addEdit');
        Route::post('/employee/changeStatus', [EmployeeMasterController::class, 'changeStatus'])->name('admin.employeeMaster.changeStatus');
        Route::post('/employee/get-language', [EmployeeMasterController::class, 'getLangData'])->name('admin.employeeMaster.getLangData');

        ## Mother Tongue
        Route::get('/mother-tongue', [MotherTongueController::class, 'index'])->name('admin.motherTongue.index');
        Route::post('/mother-tongue/getAjaxPaginationData', [MotherTongueController::class, 'getAjaxPaginationData'])->name('admin.motherTongue.getAjaxPaginationData');
        Route::get('/mother-tongue/add', [MotherTongueController::class, 'addEditForm'])->name('admin.motherTongue.addForm');
        Route::get('/mother-tongue/edit/{id}', [MotherTongueController::class, 'addEditForm'])->name('admin.motherTongue.editForm');
        Route::post('/mother-tongue/addEdit', [MotherTongueController::class, 'addEdit'])->name('admin.motherTongue.addEdit');
        Route::post('/mother-tongue/changeStatus', [MotherTongueController::class, 'changeStatus'])->name('admin.motherTongue.changeStatus');
        Route::post('/mother-tongue/get-language', [MotherTongueController::class, 'getLangData'])->name('admin.motherTongue.getLangData');

        ## Star
        Route::get('/star', [StarController::class, 'index'])->name('admin.star.index');
        Route::post('/star/getAjaxPaginationData', [StarController::class, 'getAjaxPaginationData'])->name('admin.star.getAjaxPaginationData');
        Route::get('/star/add', [StarController::class, 'addEditForm'])->name('admin.star.addForm');
        Route::get('/star/edit/{id}', [StarController::class, 'addEditForm'])->name('admin.star.editForm');
        Route::post('/star/addEdit', [StarController::class, 'addEdit'])->name('admin.star.addEdit');
        Route::post('/star/changeStatus', [StarController::class, 'changeStatus'])->name('admin.star.changeStatus');
        Route::post('/star/get-language', [StarController::class, 'getLangData'])->name('admin.star.getLangData');

        ## Moonsign
        Route::get('/moonsign', [MoonsignController::class, 'index'])->name('admin.moonsign.index');
        Route::post('/moonsign/getAjaxPaginationData', [MoonsignController::class, 'getAjaxPaginationData'])->name('admin.moonsign.getAjaxPaginationData');
        Route::get('/moonsign/add', [MoonsignController::class, 'addEditForm'])->name('admin.moonsign.addForm');
        Route::get('/moonsign/edit/{id}', [MoonsignController::class, 'addEditForm'])->name('admin.moonsign.editForm');
        Route::post('/moonsign/addEdit', [MoonsignController::class, 'addEdit'])->name('admin.moonsign.addEdit');
        Route::post('/moonsign/changeStatus', [MoonsignController::class, 'changeStatus'])->name('admin.moonsign.changeStatus');
        Route::post('/moonsign/get-language', [MoonsignController::class, 'getLangData'])->name('admin.moonsign.getLangData');

        ## Annual Income
        Route::get('/annual-income', [AnnualIncomeController::class, 'index'])->name('admin.annualIncome.index');
        Route::post('/annual-income/getAjaxPaginationData', [AnnualIncomeController::class, 'getAjaxPaginationData'])->name('admin.annualIncome.getAjaxPaginationData');
        Route::get('/annual-income/add', [AnnualIncomeController::class, 'addEditForm'])->name('admin.annualIncome.addForm');
        Route::get('/annual-income/edit/{id}', [AnnualIncomeController::class, 'addEditForm'])->name('admin.annualIncome.editForm');
        Route::post('/annual-income/addEdit', [AnnualIncomeController::class, 'addEdit'])->name('admin.annualIncome.addEdit');
        Route::post('/annual-income/changeStatus', [AnnualIncomeController::class, 'changeStatus'])->name('admin.annualIncome.changeStatus');
        Route::post('/annual-income/get-language', [AnnualIncomeController::class, 'getLangData'])->name('admin.annualIncome.getLangData');

        ## Faq :
        Route::resource('faq-list', FaqListController::class)->parameters(['faq-list' => 'faq'])->names('admin.faqList');
        Route::post('faq-list/ajax-pagination', [FaqListController::class, 'ajaxPagination'])->name('admin.faqList.ajaxPagination');
        Route::post('faq-list/change-status', [FaqListController::class, 'changeStatus'])->name('admin.faqList.changeStatus');
        Route::post('faq-list/lang-data', [FaqListController::class, 'getLangData'])->name('admin.faqList.getLangData');

        ## Membership Plan :
        Route::get('/membership-plan', [MembershipPlanController::class, 'index'])->name('admin.membershipPlan.index');
        Route::post('/membership-plan/getAjaxPaginationData', [MembershipPlanController::class, 'getAjaxPaginationData'])->name('admin.membershipPlan.getAjaxPaginationData');
        Route::get('/membership-plan/add', [MembershipPlanController::class, 'addEditForm'])->name('admin.membershipPlan.addForm');
        Route::get('/membership-plan/edit/{id}', [MembershipPlanController::class, 'addEditForm'])->name('admin.membershipPlan.editForm');
        Route::post('/membership-plan/addEdit', [MembershipPlanController::class, 'addEdit'])->name('admin.membershipPlan.addEdit');
        Route::post('/membership-plan/changeStatus', [MembershipPlanController::class, 'changeStatus'])->name('admin.membershipPlan.changeStatus');
        Route::get('/membership-plan/view/{id}', [MembershipPlanController::class, 'viewDetails'])->name('admin.membershipPlan.viewDetails');

        Route::get('/offline-plan', [OfflineGatewayController::class, 'offlinePaymentAddEditForm'])->name('admin.offlinePaymentAddEditForm');
        Route::post('/offline-plan-add', [OfflineGatewayController::class, 'offlinePaymentAddEdit'])->name('admin.offlinePaymentAddEdit');

        ## Payment Options :
        Route::get('/payment-options', [PaymentOptionsController::class, 'index'])->name('admin.paymentOptions.index');
        Route::post('/payment-options/getAjaxPaginationData', [PaymentOptionsController::class, 'getAjaxPaginationData'])->name('admin.paymentOptions.getAjaxPaginationData');
        Route::get('/payment-options/add', [PaymentOptionsController::class, 'addEditForm'])->name('admin.paymentOptions.addForm');
        Route::get('/payment-options/edit/{id}', [PaymentOptionsController::class, 'addEditForm'])->name('admin.paymentOptions.editForm');
        Route::post('/payment-options/addEdit', [PaymentOptionsController::class, 'addEdit'])->name('admin.paymentOptions.addEdit');
        Route::get('/payment-options/view/{id}', [PaymentOptionsController::class, 'viewDetails'])->name('admin.paymentOptions.viewDetails');
        Route::post('/payment-options/changeStatus', [PaymentOptionsController::class, 'changeStatus'])->name('admin.paymentOptions.changeStatus');

        ## Add On Management :
        Route::get('/add-on-package', [AddOnPackageController::class, 'index'])->name('admin.addOnPackage.index');
        Route::post('/add-on-package/getAjaxPaginationData', [AddOnPackageController::class, 'getAjaxPaginationData'])->name('admin.addOnPackage.getAjaxPaginationData');
        Route::get('/add-on-package/add', [AddOnPackageController::class, 'addEditForm'])->name('admin.addOnPackage.addForm');
        Route::get('/add-on-package/edit/{id}', [AddOnPackageController::class, 'addEditForm'])->name('admin.addOnPackage.editForm');
        Route::post('/add-on-package/addEdit', [AddOnPackageController::class, 'addEdit'])->name('admin.addOnPackage.addEdit');
        Route::post('/add-on-package/changeStatus', [AddOnPackageController::class, 'changeStatus'])->name('admin.addOnPackage.changeStatus');
        Route::get('/add-on-package/view/{id}', [AddOnPackageController::class, 'viewDetails'])->name('admin.addOnPackage.viewDetails');

        ## Coupon Code :
        Route::get('/coupon-code', [CouponCodeController::class, 'index'])->name('admin.couponCode.index');
        Route::post('/coupon-code/getAjaxPaginationData', [CouponCodeController::class, 'getAjaxPaginationData'])->name('admin.couponCode.getAjaxPaginationData');
        Route::get('/coupon-code/add', [CouponCodeController::class, 'addEditForm'])->name('admin.couponCode.addForm');
        Route::get('/coupon-code/edit/{id}', [CouponCodeController::class, 'addEditForm'])->name('admin.couponCode.editForm');
        Route::post('/coupon-code/addEdit', [CouponCodeController::class, 'addEdit'])->name('admin.couponCode.addEdit');
        Route::post('/coupon-code/changeStatus', [CouponCodeController::class, 'changeStatus'])->name('admin.couponCode.changeStatus');

        ## Blog Management :
        Route::get('/blog', [BlogController::class, 'index'])->name('admin.blog.index');
        Route::post('/blog/getAjaxPaginationData', [BlogController::class, 'getAjaxPaginationData'])->name('admin.blog.getAjaxPaginationData');
        Route::get('/blog/add', [BlogController::class, 'addEditForm'])->name('admin.blog.addForm');
        Route::get('/blog/edit/{id}', [BlogController::class, 'addEditForm'])->name('admin.blog.editForm');
        Route::post('/blog/addEdit', [BlogController::class, 'addEdit'])->name('admin.blog.addEdit');
        Route::post('/blog/changeStatus', [BlogController::class, 'changeStatus'])->name('admin.blog.changeStatus');
        Route::get('/blog/view/{id}', [BlogController::class, 'viewDetails'])->name('admin.blog.viewDetails');
        Route::post('/blog/getLangData', [BlogController::class, 'getLangData'])->name('admin.blog.getLangData');

        ## CMS Management :
        Route::get('/cms-pages', [CmsPagesController::class, 'index'])->name('admin.cmsPages.index');
        Route::post('/cms-pages/getAjaxPaginationData', [CmsPagesController::class, 'getAjaxPaginationData'])->name('admin.cmsPages.getAjaxPaginationData');
        Route::get('/cms-pages/add', [CmsPagesController::class, 'addEditForm'])->name('admin.cmsPages.addForm');
        Route::get('/cms-pages/edit/{id}', [CmsPagesController::class, 'addEditForm'])->name('admin.cmsPages.editForm');
        Route::post('/cms-pages/addEdit', [CmsPagesController::class, 'addEdit'])->name('admin.cmsPages.addEdit');
        Route::post('/cms-pages/changeStatus', [CmsPagesController::class, 'changeStatus'])->name('admin.cmsPages.changeStatus');
        Route::get('/cms-pages/view/{id}', [CmsPagesController::class, 'viewDetails'])->name('admin.cmsPages.viewDetails');
        Route::post('/cms-pages/getLangData', [CmsPagesController::class, 'getLangData'])->name('admin.cmsPages.getLangData');

        ## Acout Us :
        Route::get('/about-us-page', [CmsPagesController::class, 'aboutUsPageAddEditForm'])->name('admin.aboutUsPageAddEditForm');
        Route::post('/about-us-page-add', [CmsPagesController::class, 'aboutUsPageAddEdit'])->name('admin.aboutUsPageAddEdit');
        Route::post('/cms-pages/getLangDataAboutUs', [CmsPagesController::class, 'getLangDataAboutUs'])->name('admin.getLangDataAboutUs');

        ## Email Templates :
        Route::get('/email-templates', [EmailTemplatesController::class, 'index'])->name('admin.emailTemplates.index');
        Route::post('/email-templates/getAjaxPaginationData', [EmailTemplatesController::class, 'getAjaxPaginationData'])->name('admin.emailTemplates.getAjaxPaginationData');
        Route::get('/email-templates/add', [EmailTemplatesController::class, 'addEditForm'])->name('admin.emailTemplates.addForm');
        Route::get('/email-templates/edit/{id}', [EmailTemplatesController::class, 'addEditForm'])->name('admin.emailTemplates.editForm');
        Route::post('/email-templates/addEdit', [EmailTemplatesController::class, 'addEdit'])->name('admin.emailTemplates.addEdit');
        Route::post('/email-templates/changeStatus', [EmailTemplatesController::class, 'changeStatus'])->name('admin.emailTemplates.changeStatus');
        Route::get('/email-templates/view/{id}', [EmailTemplatesController::class, 'viewDetails'])->name('admin.emailTemplates.viewDetails');

        ## Notification Templates :
        Route::get('/notification-templates', [NotificationTemplatesController::class, 'index'])->name('admin.notificationTemplates.index');
        Route::post('/notification-templates/getAjaxPaginationData', [NotificationTemplatesController::class, 'getAjaxPaginationData'])->name('admin.notificationTemplates.getAjaxPaginationData');
        Route::post('/notification-templates/changeStatus', [NotificationTemplatesController::class, 'changeStatus'])->name('admin.notificationTemplates.changeStatus');

        ## SMS Templates :
        Route::get('/sms-templates', [SmsTemplatesController::class, 'index'])->name('admin.smsTemplates.index');
        Route::post('/sms-templates/getAjaxPaginationData', [SmsTemplatesController::class, 'getAjaxPaginationData'])->name('admin.smsTemplates.getAjaxPaginationData');
        Route::get('/sms-templates/add', [SmsTemplatesController::class, 'addEditForm'])->name('admin.smsTemplates.addForm');
        Route::get('/sms-templates/edit/{id}', [SmsTemplatesController::class, 'addEditForm'])->name('admin.smsTemplates.editForm');
        Route::post('/sms-templates/addEdit', [SmsTemplatesController::class, 'addEdit'])->name('admin.smsTemplates.addEdit');
        Route::post('/sms-templates/changeStatus', [SmsTemplatesController::class, 'changeStatus'])->name('admin.smsTemplates.changeStatus');

        ## SMS Api Configuration :
        Route::get('/sms-configuration', [SmsTemplatesController::class, 'smsConfigurationAddEditForm'])->name('admin.smsConfigurationAddEditForm');
        Route::post('/sms-configuration-add', [SmsTemplatesController::class, 'smsConfigurationAddEdit'])->name('admin.smsConfigurationAddEdit');

        ## Success Story:
        Route::get('/success-story', [SuccessStoryController::class, 'index'])->name('admin.successStory.index');
        Route::post('/success-story/getAjaxPaginationData', [SuccessStoryController::class, 'getAjaxPaginationData'])->name('admin.successStory.getAjaxPaginationData');
        Route::get('/success-story/add', [SuccessStoryController::class, 'addEditForm'])->name('admin.successStory.addForm');
        Route::get('/success-story/edit/{id}', [SuccessStoryController::class, 'addEditForm'])->name('admin.successStory.editForm');
        Route::post('/success-story/addEdit', [SuccessStoryController::class, 'addEdit'])->name('admin.successStory.addEdit');
        Route::post('/success-story/changeStatus', [SuccessStoryController::class, 'changeStatus'])->name('admin.successStory.changeStatus');
        Route::get('/success-story/view/{id}', [SuccessStoryController::class, 'viewDetails'])->name('admin.successStory.viewDetails');
        Route::post('/success-story/getLangData', [SuccessStoryController::class, 'getLangData'])->name('admin.successStory.getLangData');

        ## Member :
        Route::get('/member', [MemberController::class, 'index'])->name('admin.member.index');
        Route::post('/member/getAjaxPaginationData', [MemberController::class, 'getAjaxPaginationData'])->name('admin.member.getAjaxPaginationData');
        Route::get('/member/edit/{id}', [MemberController::class, 'addEditForm'])->name('admin.member.editForm');
        Route::get('/member/add', [MemberController::class, 'addEditForm'])->name('admin.member.addForm');
        Route::get('/member/view/{id}', [MemberController::class, 'viewDetails'])->name('admin.member.viewDetails');
        Route::post('/member/addEdit', [MemberController::class, 'addEdit'])->name('admin.member.addEdit');
        Route::post('/member/changeStatus', [MemberController::class, 'changeStatus'])->name('admin.member.changeStatus');
        Route::post('/member/assign-member', [MemberController::class, 'assignMember'])->name('admin.member.assignMember');
        Route::post('/member/unassign-member', [MemberController::class, 'unAssignMember'])->name('admin.member.unAssignMember');
        Route::get('/member/edit-plan/{id}', [MemberController::class, 'editPlan'])->name('admin.member.editPlan');
        Route::post('/member/get-plan-data', [MemberController::class, 'getPlanData'])->name('admin.member.getPlanData');
        Route::post('/member/edit-plan-data', [MemberController::class, 'editPlanUpdate'])->name('admin.member.editPlanUpdate');
        Route::get('/member/current-plan/{id}', [MemberController::class, 'currentPlan'])->name('admin.member.currentPlan');
        Route::post('/member/view-comment', [MemberController::class, 'viewComment'])->name('admin.member.viewComment');
        Route::post('/member/add-comment', [MemberController::class, 'addComment'])->name('admin.member.addComment');
        Route::post('/member/save-comment', [MemberController::class, 'saveComment'])->name('admin.member.saveComment');
        Route::post('/member/get-filter', [MemberController::class, 'getFilter'])->name('admin.member.getFilter');
        Route::get('/member/pdf/{id}', [MemberController::class, 'downloadBiodataPdf'])->name('admin.member.downloadBiodataPdf');
        Route::post('/member/confirmation-email', [MemberController::class, 'sendConfirmationEmail'])->name('admin.member.sendConfirmationEmail');
        Route::post('/member/download-report', [MemberController::class, 'downloadReport'])->name('admin.member.downloadReport');
        Route::post('/member/verify-mobile-email', [MemberController::class, 'verifyMobileEmail'])->name('admin.member.verifyMobileEmail');
        Route::post('/member/generate-about-me', [MemberController::class, 'generateAiAboutMe'])->name('admin.member.generateAiAboutMe');

        ## Member Sales Repors:
        Route::get('/member/sales-reports', [SalesReportsController::class, 'index'])->name('admin.salesReports.index');
        Route::post('/member/sales-reports/getAjaxPaginationData', [SalesReportsController::class, 'getAjaxPaginationData'])->name('admin.salesReports.getAjaxPaginationData');
        Route::get('/member/sales-reports/view-invoice/{id}', [SalesReportsController::class, 'viewInvoice'])->name('admin.salesReports.viewInvoice');
        Route::get('/member/sales-reports/download-invoice/{id}', [SalesReportsController::class, 'downloadInvoice'])->name('admin.salesReports.downloadInvoice');
        Route::post('/member/sales-reports/get-filter', [SalesReportsController::class, 'getFilter'])->name('admin.salesReports.getFilter');

        ## Approval :
        ## Photo 1 :
        Route::get('/photos-approval', [PhotosApprovalController::class, 'index'])->name('admin.photosApproval.index');
        Route::post('/photos-approval/getAjaxPaginationData', [PhotosApprovalController::class, 'getAjaxPaginationData'])->name('admin.photosApproval.getAjaxPaginationData');
        Route::get('/photos-approval/add', [PhotosApprovalController::class, 'addEditForm'])->name('admin.photosApproval.addForm');
        Route::get('/photos-approval/edit/{id}', [PhotosApprovalController::class, 'addEditForm'])->name('admin.photosApproval.editForm');
        Route::post('/photos-approval/addEdit', [PhotosApprovalController::class, 'addEdit'])->name('admin.photosApproval.addEdit');
        Route::post('/photos-approval/changeStatus', [PhotosApprovalController::class, 'changeStatus'])->name('admin.photosApproval.changeStatus');

        ## Id Proof :
        Route::get('/id-proof', [IdProofController::class, 'index'])->name('admin.idProof.index');
        Route::post('/id-proof/getAjaxPaginationData', [IdProofController::class, 'getAjaxPaginationData'])->name('admin.idProof.getAjaxPaginationData');
        Route::get('/id-proof/add', [IdProofController::class, 'addEditForm'])->name('admin.idProof.addForm');
        Route::get('/id-proof/edit/{id}', [IdProofController::class, 'addEditForm'])->name('admin.idProof.editForm');
        Route::post('/id-proof/addEdit', [IdProofController::class, 'addEdit'])->name('admin.idProof.addEdit');
        Route::post('/id-proof/changeStatus', [IdProofController::class, 'changeStatus'])->name('admin.idProof.changeStatus');

        ## Horoscope Approval :
        Route::get('/horoscope-approval', [HoroscopeApprovalController::class, 'index'])->name('admin.approvehoroscope.index');
        Route::post('/horoscope-approval/getAjaxPaginationData', [HoroscopeApprovalController::class, 'getAjaxPaginationData'])->name('admin.approvehoroscope.getAjaxPaginationData');
        Route::get('/horoscope-approval/add', [HoroscopeApprovalController::class, 'addEditForm'])->name('admin.approvehoroscope.addForm');
        Route::get('/horoscope-approval/edit/{id}', [HoroscopeApprovalController::class, 'addEditForm'])->name('admin.approvehoroscope.editForm');
        Route::post('/horoscope-approval/addEdit', [HoroscopeApprovalController::class, 'addEdit'])->name('admin.approvehoroscope.addEdit');
        Route::post('/horoscope-approval/changeStatus', [HoroscopeApprovalController::class, 'changeStatus'])->name('admin.approvehoroscope.changeStatus');

        ## Photo Request Approval :
        Route::get('/selfie-photo-approval', [SelfieApprovalController::class, 'index'])->name('admin.selfiePhotoApproval.index');
        Route::post('/selfie-photo-approval/getAjaxPaginationData', [SelfieApprovalController::class, 'getAjaxPaginationData'])->name('admin.selfiePhotoApproval.getAjaxPaginationData');
        Route::get('/selfie-photo-approval/add', [SelfieApprovalController::class, 'addEditForm'])->name('admin.selfiePhotoApproval.addForm');
        Route::get('/selfie-photo-approval/edit/{id}', [SelfieApprovalController::class, 'addEditForm'])->name('admin.selfiePhotoApproval.editForm');
        Route::post('/selfie-photo-approval/addEdit', [SelfieApprovalController::class, 'addEdit'])->name('admin.selfiePhotoApproval.addEdit');
        Route::post('/selfie-photo-approval/changeStatus', [SelfieApprovalController::class, 'changeStatus'])->name('admin.selfiePhotoApproval.changeStatus');

        ## Photo Request :
        Route::get('/photo-request', [PhotoRequestController::class, 'index'])->name('admin.photoRequest.index');
        Route::post('/photo-request/getAjaxPaginationData', [PhotoRequestController::class, 'getAjaxPaginationData'])->name('admin.photoRequest.getAjaxPaginationData');
        Route::get('/photo-request/{id}', [PhotoRequestController::class, 'index'])->name('admin.photoRequest.memberIndex');
        Route::post('/photo-request/download-report', [PhotoRequestController::class, 'downloadReport'])->name('admin.photoRequest.downloadReport');
        Route::post('/photo-request/get-filter', [PhotoRequestController::class, 'getFilter'])->name('admin.photoRequest.getFilter');

        ## View Contact Details :
        Route::get('/view-contacts', [ViewContactController::class, 'index'])->name('admin.viewContact.index');
        Route::post('/view-contacts/getAjaxPaginationData', [ViewContactController::class, 'getAjaxPaginationData'])->name('admin.viewContact.getAjaxPaginationData');
        Route::get('/view-contacts/{id}', [ViewContactController::class, 'index'])->name('admin.viewContact.memberIndex');
        Route::post('/view-contacts/get-filter', [ViewContactController::class, 'getFilter'])->name('admin.viewContact.getFilter');
        Route::post('/view-contacts/download-report', [ViewContactController::class, 'downloadReport'])->name('admin.viewContact.downloadReport');

        ## Express Interest :
        Route::get('/express-interest', [ExpressInterestController::class, 'index'])->name('admin.expressInterest.index');
        Route::post('/express-interest/getAjaxPaginationData', [ExpressInterestController::class, 'getAjaxPaginationData'])->name('admin.expressInterest.getAjaxPaginationData');
        Route::get('/express-interest/{id}', [ExpressInterestController::class, 'index'])->name('admin.expressInterest.memberIndex');
        Route::post('/express-interest/get-filter', [ExpressInterestController::class, 'getFilter'])->name('admin.expressInterest.getFilter');
        Route::post('/express-interest/download-report', [ExpressInterestController::class, 'downloadReport'])->name('admin.expressInterest.downloadReport');

        ## Delete Profile Reports :
        Route::get('/delete-profile', [DeleteProfileController::class, 'index'])->name('admin.deleteProfile.index');
        Route::post('/delete-profile/getAjaxPaginationData', [DeleteProfileController::class, 'getAjaxPaginationData'])->name('admin.deleteProfile.getAjaxPaginationData');
        Route::post('/delete-profile/changeStatus', [DeleteProfileController::class, 'changeStatus'])->name('admin.deleteProfile.changeStatus');
        Route::get('/delete-profile/{id}', [DeleteProfileController::class, 'index'])->name('admin.deleteProfile.memberIndex');
        Route::post('/delete-profile/recover-profile', [DeleteProfileController::class, 'recoverDeleteProfile'])->name('admin.deleteProfile.recoverDeleteProfile');

        ## Online Member :
        Route::get('/online-member', [OnlineMemberController::class, 'index'])->name('admin.onlineMember.index');
        Route::post('/online-member/getAjaxPaginationData', [OnlineMemberController::class, 'getAjaxPaginationData'])->name('admin.onlineMember.getAjaxPaginationData');
        ## Featured Member :
        Route::get('/featured-member', [FeaturedMemberController::class, 'index'])->name('admin.featuredMember.index');
        Route::post('/featured-member/getAjaxPaginationData', [FeaturedMemberController::class, 'getAjaxPaginationData'])->name('admin.featuredMember.getAjaxPaginationData');

        ## Expired Member :
        Route::get('/expired-member', [ExpiredMemberController::class, 'index'])->name('admin.expiredMember.index');
        Route::post('/expired-member/getAjaxPaginationData', [ExpiredMemberController::class, 'getAjaxPaginationData'])->name('admin.expiredMember.getAjaxPaginationData');

        ## Renewal Member :
        Route::get('/active-paid-member', [PaidActiveMemberController::class, 'index'])->name('admin.paidActiveMember.index');
        Route::post('/active-paid-member/getAjaxPaginationData', [PaidActiveMemberController::class, 'getAjaxPaginationData'])->name('admin.paidActiveMember.getAjaxPaginationData');

        ## Match Making Member :
        Route::post('/match-making-member/getAjaxPaginationData', [MatchMakingMemberController::class, 'getAjaxPaginationData'])->name('admin.matchMakingMember.getAjaxPaginationData');
        Route::post('/member/send-matches', [MatchMakingMemberController::class, 'sendManualMatch'])->name('admin.matchMakingMember.sendManualMatch');
        Route::post('/member/send-match-manually', [MatchMakingMemberController::class, 'sendMatchManually'])->name('admin.member.sendMatchManually');
        Route::get('match-making-member/get-search-Member', [MatchMakingMemberController::class, 'getMemberData'])->name('admin.matchMakingMember.getMemberData');
        Route::get('/match-making-member/{id}', [MatchMakingMemberController::class, 'index'])->name('admin.matchMakingMember.index');

        ## Featured Member :
        Route::get('/manual-match-making', [ManualMatchMakingController::class, 'index'])->name('admin.manualMatchMaking.index');
        Route::post('/manual-match-making/getAjaxPaginationData', [ManualMatchMakingController::class, 'getAjaxPaginationData'])->name('admin.manualMatchMaking.getAjaxPaginationData');

        ## Followed Up System:
        Route::get('/follow-up-report', [FollowedUpReportController::class, 'index'])->name('admin.followedUpReport.index');
        Route::post('/follow-up-report/getAjaxPaginationData', [FollowedUpReportController::class, 'getAjaxPaginationData'])->name('admin.followedUpReport.getAjaxPaginationData');
        Route::post('/follow-up-report/assign-member', [FollowedUpReportController::class, 'assignMember'])->name('admin.followedUpReport.assignMember');
        Route::post('/follow-up-report/unassign-member', [FollowedUpReportController::class, 'unAssignMember'])->name('admin.followedUpReport.unAssignMember');

        ## Lead Followed Up System:
        Route::get('/lead-follow-up-report', [LeadFollowedUpReportController::class, 'index'])->name('admin.leadFollowedUpReport.index');
        Route::post('/lead-follow-up-report/getAjaxPaginationData', [LeadFollowedUpReportController::class, 'getAjaxPaginationData'])->name('admin.leadFollowedUpReport.getAjaxPaginationData');

        ## DownloadBackup :
        Route::get('/download-database', [DownloadBackupController::class, 'downloadDatabase'])->name('admin.downloadBackup.downloadDatabase');

        ## ContactInquiry :
        Route::get('/contact-inquiry', [ContactInquiryController::class, 'index'])->name('admin.contactInquiry.index');
        Route::post('/contact-inquiry/getAjaxPaginationData', [ContactInquiryController::class, 'getAjaxPaginationData'])->name('admin.contactInquiry.getAjaxPaginationData');
        Route::post('/contact-inquiry/changeStatus', [ContactInquiryController::class, 'changeStatus'])->name('admin.contactInquiry.changeStatus');

        ## Email Subscribed :
        Route::get('/email-subscribe', [EmailSubscribeController::class, 'index'])->name('admin.emailSubscribe.index');
        Route::post('/email-subscribe/getAjaxPaginationData', [EmailSubscribeController::class, 'getAjaxPaginationData'])->name('admin.emailSubscribe.getAjaxPaginationData');

        ## Profile Report Spam :
        Route::get('/report-spam', [ProfileReportSpamController::class, 'index'])->name('admin.profileReportSpam.index');
        Route::post('/report-spam/getAjaxPaginationData', [ProfileReportSpamController::class, 'getAjaxPaginationData'])->name('admin.profileReportSpam.getAjaxPaginationData');
        Route::post('/report-spam/download-report', [ProfileReportSpamController::class, 'downloadReport'])->name('admin.profileReportSpam.downloadReport');


        ## Bulk Notification :
        Route::get('/bulk-notification', [BulkNotificationController::class, 'index'])->name('admin.bulkNotification.index');
        Route::post('/member-list-bulk-notification', [BulkNotificationController::class, 'getMemberListBulkNotification'])->name('admin.bulkNotification.getMemberListBulkNotification');
        Route::post('/send-bulk-notification', [BulkNotificationController::class, 'sendBulkNotification'])->name('admin.bulkNotification.sendBulkNotification');

        ## Bulk Email :
        Route::get('/bulk-email', [BulkEmailController::class, 'index'])->name('admin.bulkEmail.index');
        Route::post('/member-list-bulk-email', [BulkEmailController::class, 'getMemberListBulkEmail'])->name('admin.bulkEmail.getMemberListBulkEmail');
        Route::post('/send-bulk-email', [BulkEmailController::class, 'sendBulkEmail'])->name('admin.bulkEmail.sendBulkEmail');

        ## Advertisement :
        Route::get('/advertisement', [AdvertisementController::class, 'index'])->name('admin.advertisement.index');
        Route::post('/advertisement/getAjaxPaginationData', [AdvertisementController::class, 'getAjaxPaginationData'])->name('admin.advertisement.getAjaxPaginationData');
        Route::get('/advertisement/add', [AdvertisementController::class, 'addEditForm'])->name('admin.advertisement.addForm');
        Route::get('/advertisement/edit/{id}', [AdvertisementController::class, 'addEditForm'])->name('admin.advertisement.editForm');
        Route::post('/advertisement/addEdit', [AdvertisementController::class, 'addEdit'])->name('admin.advertisement.addEdit');
        Route::post('/advertisement/changeStatus', [AdvertisementController::class, 'changeStatus'])->name('admin.advertisement.changeStatus');

        ## Advertisement Inquiry :
        Route::get('/advertisement-inquiry', [AdvertisementInquiryController::class, 'index'])->name('admin.advertisementInquiry.index');
        Route::post('/advertisement-inquiry/getAjaxPaginationData', [AdvertisementInquiryController::class, 'getAjaxPaginationData'])->name('admin.advertisementInquiry.getAjaxPaginationData');
        Route::post('/advertisement-inquiry/changeStatus', [AdvertisementInquiryController::class, 'changeStatus'])->name('admin.advertisementInquiry.changeStatus');

        ## Matrimony Data :
        Route::get('/matrimony-data', [MatrimonyDataController::class, 'index'])->name('admin.matrimonyData.index');
        Route::post('/matrimony-data/getAjaxPaginationData', [MatrimonyDataController::class, 'getAjaxPaginationData'])->name('admin.matrimonyData.getAjaxPaginationData');
        Route::get('/matrimony-data/add', [MatrimonyDataController::class, 'addEditForm'])->name('admin.matrimonyData.addForm');
        Route::get('/matrimony-data/edit/{id}', [MatrimonyDataController::class, 'addEditForm'])->name('admin.matrimonyData.editForm');
        Route::post('/matrimony-data/addEdit', [MatrimonyDataController::class, 'addEdit'])->name('admin.matrimonyData.addEdit');
        Route::post('/matrimony-data/changeStatus', [MatrimonyDataController::class, 'changeStatus'])->name('admin.matrimonyData.changeStatus');
        Route::get('/matrimony-data/view/{id}', [MatrimonyDataController::class, 'viewDetails'])->name('admin.matrimonyData.viewDetails');
        Route::post('/matrimony-data/getLangData', [MatrimonyDataController::class, 'getLangData'])->name('admin.matrimonyData.getLangData');
        Route::post('matrimony-data/generate-auto-pages', [MatrimonyDataController::class, 'generatePages'])->name('admin.matrimonyData.generatePages');
        Route::post('matrimony-data/get-matrimony-list', [MatrimonyDataController::class, 'getMatrimonyList'])->name('admin.matrimonyData.getMatrimonyList');

        ## SEO Management :
        Route::get('/seo-management', [SeoManagementController::class, 'index'])->name('admin.seoManagement.index');
        Route::post('/seo-management/getAjaxPaginationData', [SeoManagementController::class, 'getAjaxPaginationData'])->name('admin.seoManagement.getAjaxPaginationData');
        Route::get('/seo-management/add', [SeoManagementController::class, 'addEditForm'])->name('admin.seoManagement.addForm');
        Route::get('/seo-management/edit/{id}', [SeoManagementController::class, 'addEditForm'])->name('admin.seoManagement.editForm');
        Route::post('/seo-management/addEdit', [SeoManagementController::class, 'addEdit'])->name('admin.seoManagement.addEdit');
        Route::post('/seo-management/changeStatus', [SeoManagementController::class, 'changeStatus'])->name('admin.seoManagement.changeStatus');
        Route::get('/seo-management/view/{id}', [SeoManagementController::class, 'viewDetails'])->name('admin.seoManagement.viewDetails');
        Route::post('/seo-management/getLangData', [SeoManagementController::class, 'getLangData'])->name('admin.seoManagement.getLangData');
        Route::post('seo-management/generate-auto-seo', [SeoManagementController::class, 'generateSeo'])->name('admin.seoManagement.generateSeo');

        ## Seo Section:
        Route::get('/seo-settings', [SeoSettingsController::class, 'index'])->name('admin.seoSettings.index');
        Route::post('/seo-settings/robots-update', [SeoSettingsController::class, 'updateRobots'])->name('admin.seoSettings.updateRobots');
        Route::post('/seo-settings-update', [SeoSettingsController::class, 'update'])->name('admin.seoSettings.update');
        Route::post('/seo-settings/sitemap-generate', [SeoSettingsController::class, 'generateSiteMap'])->name('admin.seoSettings.generateSiteMap');

        ## Lead Generation :
        Route::get('/lead-generation', [LeadGenerationController::class, 'index'])->name('admin.leadGeneration.index');
        Route::post('/lead-generation/getAjaxPaginationData', [LeadGenerationController::class, 'getAjaxPaginationData'])->name('admin.leadGeneration.getAjaxPaginationData');
        Route::get('/lead-generation/add', [LeadGenerationController::class, 'addEditForm'])->name('admin.leadGeneration.addForm');
        Route::get('/lead-generation/edit/{id}', [LeadGenerationController::class, 'addEditForm'])->name('admin.leadGeneration.editForm');
        Route::post('/lead-generation/addEdit', [LeadGenerationController::class, 'addEdit'])->name('admin.leadGeneration.addEdit');
        Route::post('/lead-generation/changeStatus', [LeadGenerationController::class, 'changeStatus'])->name('admin.leadGeneration.changeStatus');
        Route::post('/lead-generation/assign-member', [LeadGenerationController::class, 'assignMember'])->name('admin.leadGeneration.assignMember');
        Route::post('/lead-generation/unassign-member', [LeadGenerationController::class, 'unAssignMember'])->name('admin.leadGeneration.unAssignMember');
        Route::post('/lead-generation/change-interest', [LeadGenerationController::class, 'changeInterest'])->name('admin.leadGeneration.changeInterest');
        Route::post('/lead-generation/view-comment', [LeadGenerationController::class, 'viewComment'])->name('admin.leadGeneration.viewComment');
        Route::post('/lead-generation/add-comment', [LeadGenerationController::class, 'addComment'])->name('admin.leadGeneration.addComment');
        Route::post('/lead-generation/save-comment', [LeadGenerationController::class, 'saveComment'])->name('admin.leadGeneration.saveComment');
        Route::get('/lead-generation/convert-member/{id}', [LeadGenerationController::class, 'convertMember'])->name('admin.leadGeneration.convertMember');
        Route::post('/lead-generation/get-filter', [LeadGenerationController::class, 'getFilter'])->name('admin.leadGeneration.getFilter');
        Route::post('/lead-generation/download-report', [LeadGenerationController::class, 'downloadReport'])->name('admin.leadGeneration.downloadReport');
        Route::get('/lead-import', [LeadGenerationController::class, 'importLead'])->name('admin.leadGeneration.importLead');
        Route::get('/download-sample-csv', [LeadGenerationController::class, 'downloadSampleCsv'])->name('admin.leadGeneration.downloadSampleCsv');
        Route::post('/import-lead-data', [LeadGenerationController::class, 'importLeadData'])->name('admin.leadGeneration.importLeadData');
        Route::get('/lead-generation/fresh-follow-up', [LeadGenerationController::class, 'freshFollowUp'])->name('admin.leadGeneration.freshFollowUp');
        Route::post('/lead-generation/fresh-follow-up/getAjaxPaginationData', [LeadGenerationController::class, 'getAjaxPaginationDataFresh'])->name('admin.leadGeneration.getAjaxPaginationDataFresh');
        Route::get('/lead-generation/repeated-follow-up', [LeadGenerationController::class, 'repeatedFollowUp'])->name('admin.leadGeneration.repeatedFollowUp');
        Route::post('/lead-generation/repeated-follow-up/getAjaxPaginationData', [LeadGenerationController::class, 'getAjaxPaginationDataRepeated'])->name('admin.leadGeneration.getAjaxPaginationDataRepeated');
        Route::get('/lead-generation/closed-leads', [LeadGenerationController::class, 'closedLeads'])->name('admin.leadGeneration.closedLeads');
        Route::post('/lead-generation/closed-leads/getAjaxPaginationData', [LeadGenerationController::class, 'getAjaxPaginationDataClosed'])->name('admin.leadGeneration.getAjaxPaginationDataClosed');
        Route::get('/lead-generation/staff-wise-report', [LeadGenerationController::class, 'staffWiseReport'])->name('admin.leadGeneration.staffWiseReport');

        ## Lead Generation Report:
        Route::get('/lead-generation-report', [LeadGenerationReportController::class, 'index'])->name('admin.leadGenerationReport.index');
        Route::post('/lead-generation-report/getAjaxPaginationData', [LeadGenerationReportController::class, 'getAjaxPaginationData'])->name('admin.leadGenerationReport.getAjaxPaginationData');

        ## User Login History:
        Route::get('/user-login-history', [UserLoginHistoryController::class, 'index'])->name('admin.userLoginHistory.index');
        Route::post('/user-login-history/getAjaxPaginationData', [UserLoginHistoryController::class, 'getAjaxPaginationData'])->name('admin.userLoginHistory.getAjaxPaginationData');
        Route::post('/user-login-history/changeStatus', [UserLoginHistoryController::class, 'changeStatus'])->name('admin.userLoginHistory.changeStatus');
        Route::get('/user-login-history/{id}', [UserLoginHistoryController::class, 'index'])->name('admin.userLoginHistory.memberIndex');

        ## Event Management :
        Route::get('/event', [EventController::class, 'index'])->name('admin.event.index');
        Route::post('/event/getAjaxPaginationData', [EventController::class, 'getAjaxPaginationData'])->name('admin.event.getAjaxPaginationData');
        Route::get('/event/add', [EventController::class, 'addEditForm'])->name('admin.event.addForm');
        Route::get('/event/edit/{id}', [EventController::class, 'addEditForm'])->name('admin.event.editForm');
        Route::post('/event/addEdit', [EventController::class, 'addEdit'])->name('admin.event.addEdit');
        Route::post('/event/changeStatus', [EventController::class, 'changeStatus'])->name('admin.event.changeStatus');
        Route::get('/event/view/{id}', [EventController::class, 'viewDetails'])->name('admin.event.viewDetails');

        ## Event Reports:
        Route::get('/event-reports', [EventReportController::class, 'index'])->name('admin.eventReport.index');
        Route::post('/event-reports/getAjaxPaginationData', [EventReportController::class, 'getAjaxPaginationData'])->name('admin.eventReport.getAjaxPaginationData');

        Route::post('/event-reports/get-filter', [EventReportController::class, 'getFilter'])->name('admin.eventReport.getFilter');
        Route::post('/event-reports/download-report', [EventReportController::class, 'downloadReport'])->name('admin.eventReport.downloadReport');
        Route::get('/event-reports/{id}', [EventReportController::class, 'index'])->name('admin.eventReport.memberIndex');

        ## Wedding Vendor Category :
        Route::get('/wedding-vendors', [WeddingVendorsController::class, 'index'])->name('admin.weddingVendors.index');
        Route::post('/wedding-vendors/getAjaxPaginationData', [WeddingVendorsController::class, 'getAjaxPaginationData'])->name('admin.weddingVendors.getAjaxPaginationData');
        Route::get('/wedding-vendors/add', [WeddingVendorsController::class, 'addEditForm'])->name('admin.weddingVendors.addForm');
        Route::get('/wedding-vendors/edit/{id}', [WeddingVendorsController::class, 'addEditForm'])->name('admin.weddingVendors.editForm');
        Route::post('/wedding-vendors/addEdit', [WeddingVendorsController::class, 'addEdit'])->name('admin.weddingVendors.addEdit');
        Route::post('/wedding-vendors/changeStatus', [WeddingVendorsController::class, 'changeStatus'])->name('admin.weddingVendors.changeStatus');
        Route::get('/wedding-vendors/view/{id}', [WeddingVendorsController::class, 'viewDetails'])->name('admin.weddingVendors.viewDetails');

        ## Wedding Vendor Category :
        Route::get('/vendors-category', [VendorsCategoryController::class, 'index'])->name('admin.vendorsCategory.index');
        Route::post('/vendors-category/getAjaxPaginationData', [VendorsCategoryController::class, 'getAjaxPaginationData'])->name('admin.vendorsCategory.getAjaxPaginationData');
        Route::get('/vendors-category/add', [VendorsCategoryController::class, 'addEditForm'])->name('admin.vendorsCategory.addForm');
        Route::get('/vendors-category/edit/{id}', [VendorsCategoryController::class, 'addEditForm'])->name('admin.vendorsCategory.editForm');
        Route::post('/vendors-category/addEdit', [VendorsCategoryController::class, 'addEdit'])->name('admin.vendorsCategory.addEdit');
        Route::post('/vendors-category/changeStatus', [VendorsCategoryController::class, 'changeStatus'])->name('admin.vendorsCategory.changeStatus');

        ## Wedding Vendor Review :
        Route::get('/vendors-review', [VendorsReviewController::class, 'index'])->name('admin.vendorsReview.index');
        Route::post('/vendors-review/getAjaxPaginationData', [VendorsReviewController::class, 'getAjaxPaginationData'])->name('admin.vendorsReview.getAjaxPaginationData');
        Route::post('/vendors-review/changeStatus', [VendorsReviewController::class, 'changeStatus'])->name('admin.vendorsReview.changeStatus');

        ## Vendor Enquiry :
        Route::get('/vendor-inquiry', [VendorInquiryController::class, 'index'])->name('admin.vendorInquiry.index');
        Route::post('/vendor-inquiry/getAjaxPaginationData', [VendorInquiryController::class, 'getAjaxPaginationData'])->name('admin.vendorInquiry.getAjaxPaginationData');
        Route::post('/vendor-inquiry/changeStatus', [VendorInquiryController::class, 'changeStatus'])->name('admin.vendorInquiry.changeStatus');
        Route::get('/vendor-inquiry/view/{id}', [VendorInquiryController::class, 'viewDetails'])->name('admin.vendorInquiry.viewDetails');

        ## Franchise:
        Route::get('/franchise', [FranchiseController::class, 'index'])->name('admin.franchise.index');
        Route::post('/franchise/getAjaxPaginationData', [FranchiseController::class, 'getAjaxPaginationData'])->name('admin.franchise.getAjaxPaginationData');
        Route::get('/franchise/add', [FranchiseController::class, 'addEditForm'])->name('admin.franchise.addForm');
        Route::get('/franchise/edit/{id}', [FranchiseController::class, 'addEditForm'])->name('admin.franchise.editForm');
        Route::post('/franchise/addEdit', [FranchiseController::class, 'addEdit'])->name('admin.franchise.addEdit');
        Route::post('/franchise/changeStatus', [FranchiseController::class, 'changeStatus'])->name('admin.franchise.changeStatus');
        Route::get('/franchise/view/{id}', [FranchiseController::class, 'viewDetails'])->name('admin.franchise.viewDetails');

        ## Auto Match Making :
        Route::get('/auto-match-making', [AutoMatchMakingController::class, 'index'])->name('admin.autoMatchMaking.index');
        Route::post('/send-match-making', [AutoMatchMakingController::class, 'sendAutoMatchMaking'])->name('admin.autoMatchMaking.sendAutoMatchMaking');
        ## Auto Match Schedular History :
        Route::get('/auto-match-making-history', [AutoMatchHistoryController::class, 'index'])->name('admin.autoMatchSchedularHistory.index');
        Route::post('/auto-match-making-history/getAjaxPaginationData', [AutoMatchHistoryController::class, 'getAjaxPaginationData'])->name('admin.autoMatchSchedularHistory.getAjaxPaginationData');

        ## Franchise Member:
        Route::get('/franchise-member', [FranchiseMemberController::class, 'index'])->name('admin.franchiseMember.index');
        Route::post('/franchise-member/getAjaxPaginationData', [FranchiseMemberController::class, 'getAjaxPaginationData'])->name('admin.franchiseMember.getAjaxPaginationData');
        Route::get('/franchise-member/{id}', [FranchiseMemberController::class, 'index'])->name('admin.franchiseMember.memberIndex');

        ## Franchise Assign Member History:
        Route::get('/franchise-assign-history', [FranchiseAssignHistoryController::class, 'index'])->name('admin.franchiseAssignHistory.index');
        Route::post('/franchise-assign-history/getAjaxPaginationData', [FranchiseAssignHistoryController::class, 'getAjaxPaginationData'])->name('admin.franchiseAssignHistory.getAjaxPaginationData');
        Route::post('/franchise-assign-history/get-filter', [FranchiseAssignHistoryController::class, 'getFilter'])->name('admin.franchiseAssignHistory.getFilter');
        Route::post('/franchise-assign-history/download-report', [FranchiseAssignHistoryController::class, 'downloadReport'])->name('admin.franchiseAssignHistory.downloadReport');

        ## Franchise UnAssign Member History:
        Route::get('/franchise-unassign-history', [FranchiseUnassignHistoryController::class, 'index'])->name('admin.franchiseUnassignHistory.index');
        Route::post('/franchise-unassign-history/getAjaxPaginationData', [FranchiseUnassignHistoryController::class, 'getAjaxPaginationData'])->name('admin.franchiseUnassignHistory.getAjaxPaginationData');

        ## Franchise Sales Repors:
        Route::get('/franchise-sales-reports', [FranchiseSalesReportsController::class, 'index'])->name('admin.franchiseSalesReports.index');
        Route::post('/franchise-sales-reports/getAjaxPaginationData', [FranchiseSalesReportsController::class, 'getAjaxPaginationData'])->name('admin.franchiseSalesReports.getAjaxPaginationData');
        Route::post('/franchise-sales-reports/sales-reports/get-filter', [FranchiseSalesReportsController::class, 'getFilter'])->name('admin.franchiseSalesReports.getFilter');
        Route::get('/franchise-sales-reports/view-invoice/{id}', [FranchiseSalesReportsController::class, 'viewInvoice'])->name('admin.franchiseSalesReports.viewInvoice');

        ## Franchise Lead Assign Member History:
        Route::get('/franchise-lead-assign-history', [FranchiseLeadAssignHistoryController::class, 'index'])->name('admin.franchiseLeadAssignHistory.index');
        Route::post('/franchise-lead-assign-history/getAjaxPaginationData', [FranchiseLeadAssignHistoryController::class, 'getAjaxPaginationData'])->name('admin.franchiseLeadAssignHistory.getAjaxPaginationData');
        Route::post('/franchise-lead-assign-history/get-filter', [FranchiseLeadAssignHistoryController::class, 'getFilter'])->name('admin.franchiseLeadAssignHistory.getFilter');
        Route::post('/franchise-lead-assign-history/download-report', [FranchiseLeadAssignHistoryController::class, 'downloadReport'])->name('admin.franchiseLeadAssignHistory.downloadReport');

        ## Franchise Lead Assign Member History:
        Route::get('/franchise-lead-unassign-history', [FranchiseLeadUnAssignHistoryController::class, 'index'])->name('admin.franchiseLeadUnAssignHistory.index');
        Route::post('/franchise-lead-unassign-history/getAjaxPaginationData', [FranchiseLeadUnAssignHistoryController::class, 'getAjaxPaginationData'])->name('admin.franchiseLeadUnAssignHistory.getAjaxPaginationData');
        ## Whatsapp Api Configuration :
        Route::get('/whatsapp-configuration', [WhatsappConfigurationController::class, 'whatsappConfigurationAddEditForm'])->name('admin.whatsappConfigurationAddEditForm');
        Route::post('/whatsapp-configuration-add', [WhatsappConfigurationController::class, 'whatsappConfigurationAddEdit'])->name('admin.whatsappConfigurationAddEdit');

        ## Ticket Management :
        Route::get('/ticket-management', [TicketManagementController::class, 'index'])->name('admin.ticketManagement.index');
        Route::post('/ticket-management/getAjaxPaginationData', [TicketManagementController::class, 'getAjaxPaginationData'])->name('admin.ticketManagement.getAjaxPaginationData');
        Route::get('/ticket-management/add', [TicketManagementController::class, 'addEditForm'])->name('admin.ticketManagement.addForm');
        Route::get('/ticket-management/edit/{id}', [TicketManagementController::class, 'addEditForm'])->name('admin.ticketManagement.editForm');
        Route::post('/ticket-management/addEdit', [TicketManagementController::class, 'addEdit'])->name('admin.ticketManagement.addEdit');
        Route::post('/ticket-management/changeStatus', [TicketManagementController::class, 'changeStatus'])->name('admin.ticketManagement.changeStatus');
        Route::get('/ticket-management/view/{id}', [TicketManagementController::class, 'viewDetails'])->name('admin.ticketManagement.viewDetails');
        Route::post('/ticket-management/view-comment', [TicketManagementController::class, 'viewComment'])->name('admin.ticketManagement.viewComment');
        Route::post('/ticket-management/add-comment', [TicketManagementController::class, 'addComment'])->name('admin.ticketManagement.addComment');
        Route::post('/ticket-management/save-comment', [TicketManagementController::class, 'saveComment'])->name('admin.ticketManagement.saveComment');

        ## Personlize Member :
        Route::get('/personalize-member', [PersonalizeMemberController::class, 'index'])->name('admin.personalizeMember.index');
        Route::post('/personalize-member/getAjaxPaginationData', [PersonalizeMemberController::class, 'getAjaxPaginationData'])->name('admin.personalizeMember.getAjaxPaginationData');

        ## Personalize Report :
        Route::get('/personalize-report', [PersonalizeReportController::class, 'index'])->name('admin.personalizeReport.index');
        Route::post('/personalize-report/getAjaxPaginationData', [PersonalizeReportController::class, 'getAjaxPaginationData'])->name('admin.personalizeReport.getAjaxPaginationData');
        Route::post('/personalize-report/changeStatus', [PersonalizeReportController::class, 'changeStatus'])->name('admin.personalizeReport.changeStatus');

        ## Personalize Report :
        Route::get('/personalize-enquiry', [PersonalizeEnquiryController::class, 'index'])->name('admin.personalizeEnquiry.index');
        Route::post('/personalize-enquiry/getAjaxPaginationData', [PersonalizeEnquiryController::class, 'getAjaxPaginationData'])->name('admin.personalizeEnquiry.getAjaxPaginationData');

        ## Personalize Report :
        Route::get('/personalize-match-making', [PersonalizeMatchMakingController::class, 'index'])->name('admin.personalizeMatchMaking.index');
        Route::post('/personalize-match-making/getAjaxPaginationData', [PersonalizeMatchMakingController::class, 'getAjaxPaginationData'])->name('admin.personalizeMatchMaking.getAjaxPaginationData');
        Route::post('/personalize-match-making/changeStatus', [PersonalizeMatchMakingController::class, 'changeStatus'])->name('admin.personalizeMatchMaking.changeStatus');

        ## Personalize Meeting :
        Route::get('/personalize-meeting', [PersonalizeMeetingController::class, 'index'])->name('admin.personalizeMeeting.index');
        Route::get('/personalize-meeting/{id}', [PersonalizeMeetingController::class, 'index'])->name('admin.personalizeMeeting.memberIndex');
        Route::post('/personalize-meeting/getAjaxPaginationData', [PersonalizeMeetingController::class, 'getAjaxPaginationData'])->name('admin.personalizeMeeting.getAjaxPaginationData');
        Route::get('/personalize-meeting/add/{id}', [PersonalizeMeetingController::class, 'addEditForm'])->name('admin.personalizeMeeting.addForm');
        Route::get('/personalize-meeting/edit/{id}', [PersonalizeMeetingController::class, 'addEditForm'])->name('admin.personalizeMeeting.editForm');
        Route::post('/personalize-meeting/addEdit', [PersonalizeMeetingController::class, 'addEdit'])->name('admin.personalizeMeeting.addEdit');
        Route::post('/personalize-meeting/changeStatus', [PersonalizeMeetingController::class, 'changeStatus'])->name('admin.personalizeMeeting.changeStatus');
        Route::get('/personalize-meeting/view/{id}', [PersonalizeMeetingController::class, 'viewDetails'])->name('admin.personalizeMeeting.viewDetails');
        Route::post('/personalize-meeting/remark/{id}', [PersonalizeMeetingController::class, 'updateRemark'])->name('admin.personalizeMeeting.remark');

        ## Personalize Chat :
        Route::prefix('personalize-chat')->name('admin.personalizeChat.')->group(function () {
            Route::get('/support-chat', [PersonalizeChatController::class, 'index'])->name('index');
            Route::post('/custom-chat-list', [PersonalizeChatController::class, 'chatMemberList'])->name('chatMemberList');
            Route::post('/custom-chat-messages', [PersonalizeChatController::class, 'chatMessages'])->name('chatMessages');
            Route::post('/send-message', [PersonalizeChatController::class, 'sendMessage'])->name('sendMessage');
            Route::post('/more-message', [PersonalizeChatController::class, 'moreMessages'])->name('moreMessages');
        });

        ## Language Master :
        Route::get('/language-master', [LanguageMasterController::class, 'index'])->name('admin.languageMaster.index');
        Route::post('/language-master/getAjaxPaginationData', [LanguageMasterController::class, 'getAjaxPaginationData'])->name('admin.languageMaster.getAjaxPaginationData');
        Route::get('/language-master/add', [LanguageMasterController::class, 'addEditForm'])->name('admin.languageMaster.addForm');
        Route::get('/language-master/edit/{id}', [LanguageMasterController::class, 'addEditForm'])->name('admin.languageMaster.editForm');
        Route::post('/language-master/addEdit', [LanguageMasterController::class, 'addEdit'])->name('admin.languageMaster.addEdit');
        Route::post('/language-master/changeStatus', [LanguageMasterController::class, 'changeStatus'])->name('admin.languageMaster.changeStatus');
        Route::get('/language-master/sample-csv-download/{id}', [LanguageMasterController::class, 'langSampleCSVDownload'])->name('admin.languageMaster.langSampleCSVDownload');

        Route::get('/language-templates/{id}', [LanguageTemplatesController::class, 'index'])->name('admin.languageTemplates.index');
        Route::post('/language-templates/getAjaxPaginationData', [LanguageTemplatesController::class, 'getAjaxPaginationData'])->name('admin.languageTemplates.getAjaxPaginationData');
        Route::post('/language-templates/get-lang-data', [LanguageTemplatesController::class, 'getLangData'])->name('admin.languageTemplates.getLangData');
        Route::post('/language-templates/addEdit', [LanguageTemplatesController::class, 'addEdit'])->name('admin.languageTemplates.addEdit');
        Route::post('/language-templates/changeStatus', [LanguageTemplatesController::class, 'changeStatus'])->name('admin.languageTemplates.changeStatus');

        ## notification list:
        Route::get('/admin-notification-list', [AdminNotificationListController::class, 'index'])->name('admin.adminNotificationList.index');
        Route::post('/admin-notification-list/getAjaxPaginationData', [AdminNotificationListController::class, 'getAjaxPaginationData'])->name('admin.adminNotificationList.getAjaxPaginationData');
        Route::get('/admin-notification-list/{id}', [AdminNotificationListController::class, 'index'])->name('admin.adminNotificationList.memberIndex');

        ## Franchise Dashboard:
        Route::get('/franchise/dashboard', [FranchiseDashboardController::class, 'index'])->name('admin.franchiseDashboard');
        Route::post('/franchise/dashboard-data', [FranchiseDashboardController::class, 'dashboardData'])->name('admin.franchiseDashboard.dashboardData');
        Route::post('/franchise/get-graph-data', [FranchiseDashboardController::class, 'getGraphData'])->name('admin.franchiseDashboard.getGraphData');
        ## Franchise Login History:
        Route::get('/franchise-login-history', [FranchiseLoginHistoryController::class, 'index'])->name('admin.franchiseLoginHistory.index');
        Route::post('/franchise-login-history/getAjaxPaginationData', [FranchiseLoginHistoryController::class, 'getAjaxPaginationData'])->name('admin.franchiseLoginHistory.getAjaxPaginationData');
        Route::post('/franchise-login-history/changeStatus', [FranchiseLoginHistoryController::class, 'changeStatus'])->name('admin.franchiseLoginHistory.changeStatus');
        Route::get('/franchise-login-history/{id}', [FranchiseLoginHistoryController::class, 'index'])->name('admin.franchiseLoginHistory.memberIndex');

        ## Check Session :
        Route::get('/check-session', [AutoSessionExpiredController::class, 'index'])->name('admin.autoSessionExpired.index');
        Route::get('/extend-session', [AutoSessionExpiredController::class, 'extendSession'])->name('admin.autoSessionExpired.extendSession');

        ## Affiliate Sections :
        Route::get('/affiliate-member', [AffiliateMemberController::class, 'index'])->name('admin.affiliateMember.index');
        Route::post('/affiliate-member/getAjaxPaginationData', [AffiliateMemberController::class, 'getAjaxPaginationData'])->name('admin.affiliateMember.getAjaxPaginationData');
        Route::get('/affiliate-member/add', [AffiliateMemberController::class, 'addEditForm'])->name('admin.affiliateMember.addForm');
        Route::get('/affiliate-member/edit/{id}', [AffiliateMemberController::class, 'addEditForm'])->name('admin.affiliateMember.editForm');
        Route::post('/affiliate-member/addEdit', [AffiliateMemberController::class, 'addEdit'])->name('admin.affiliateMember.addEdit');
        Route::post('/affiliate-member/changeStatus', [AffiliateMemberController::class, 'changeStatus'])->name('admin.affiliateMember.changeStatus');
        Route::get('/affiliate-member/view/{id}', [AffiliateMemberController::class, 'viewDetails'])->name('admin.affiliateMember.viewDetails');

        Route::get('/affiliate-member-assign', [AffiliateMemberAssignController::class, 'index'])->name('admin.affiliateMemberAssign.index');
        Route::post('/affiliate-member-assign/getAjaxPaginationData', [AffiliateMemberAssignController::class, 'getAjaxPaginationData'])->name('admin.affiliateMemberAssign.getAjaxPaginationData');

        Route::get('/affiliate-member-unassign', [AffiliateMemberUnassignController::class, 'index'])->name('admin.affiliateMemberUnassign.index');
        Route::post('/affiliate-member-unassign/getAjaxPaginationData', [AffiliateMemberUnassignController::class, 'getAjaxPaginationData'])->name('admin.affiliateMemberUnassign.getAjaxPaginationData');

        Route::get('/affiliate-member-income', [AffiliateMemberIncomeController::class, 'index'])->name('admin.affiliateMemberIncome.index');
        Route::post('/affiliate-member-income/getAjaxPaginationData', [AffiliateMemberIncomeController::class, 'getAjaxPaginationData'])->name('admin.affiliateMemberIncome.getAjaxPaginationData');

        Route::get('/affiliate-member-payment', [AffiliateMemberPaymentController::class, 'index'])->name('admin.affiliateMemberPayment.index');
        Route::post('/affiliate-member-payment/getAjaxPaginationData', [AffiliateMemberPaymentController::class, 'getAjaxPaginationData'])->name('admin.affiliateMemberPayment.getAjaxPaginationData');
        Route::post('/affiliate-member-payment/submit-remark', [AffiliateMemberPaymentController::class, 'addAdminRemarks'])->name('admin.affiliateMemberPayment.addAdminRemarks');

        ## Affiliate Member Login History:
        Route::get('/affiliate-member-login-history', [AffiliateMemberloginHistoryController::class, 'index'])->name('admin.affiliateMemberloginHistory.index');
        Route::post('/affiliate-member-login-history/getAjaxPaginationData', [AffiliateMemberloginHistoryController::class, 'getAjaxPaginationData'])->name('admin.affiliateMemberloginHistory.getAjaxPaginationData');
        Route::post('/affiliate-member-login-history/changeStatus', [AffiliateMemberloginHistoryController::class, 'changeStatus'])->name('admin.affiliateMemberloginHistory.changeStatus');
        Route::get('/affiliate-member-login-history/{id}', [AffiliateMemberloginHistoryController::class, 'index'])->name('admin.affiliateMemberloginHistory.memberIndex');

        ## Affiliate Homepage
        Route::get('/affiliate-homepage', [AffiliateHomepageController::class, 'index'])->name('admin.affiliateHomepage.index');
        Route::post('/affiliate-homepage-update', [AffiliateHomepageController::class, 'updateAffiliateHomepage'])->name('admin.affiliateHomepage.update');
        ## Home Page For Lang Data :
        Route::post('/affiliate-homepage/getLangData', [AffiliateHomepageController::class, 'getLangData'])->name('admin.affiliateHomepage.getLangData');

        ## Religion
        Route::get('/affiliate-testimonial', [AffiliateTestimonialController::class, 'index'])->name('admin.affiliateTestimonial.index');
        Route::post('/affiliate-testimonial/getAjaxPaginationData', [AffiliateTestimonialController::class, 'getAjaxPaginationData'])->name('admin.affiliateTestimonial.getAjaxPaginationData');
        Route::get('/affiliate-testimonial/add', [AffiliateTestimonialController::class, 'addEditForm'])->name('admin.affiliateTestimonial.addForm');
        Route::get('/affiliate-testimonial/edit/{id}', [AffiliateTestimonialController::class, 'addEditForm'])->name('admin.affiliateTestimonial.editForm');
        Route::post('/affiliate-testimonial/addEdit', [AffiliateTestimonialController::class, 'addEdit'])->name('admin.affiliateTestimonial.addEdit');
        Route::post('/affiliate-testimonial/changeStatus', [AffiliateTestimonialController::class, 'changeStatus'])->name('admin.affiliateTestimonial.changeStatus');

        ## Staff Dashboard:
        Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('admin.staffDashboard');
        Route::post('/staff/dashboard-data', [StaffDashboardController::class, 'dashboardData'])->name('admin.staffDashboard.dashboardData');
        Route::post('/staff/get-graph-data', [StaffDashboardController::class, 'getGraphData'])->name('admin.staffDashboard.getGraphData');

        ## Staff :
        Route::get('/staff', [StaffController::class, 'index'])->name('admin.staff.index');
        Route::post('/staff/getAjaxPaginationData', [StaffController::class, 'getAjaxPaginationData'])->name('admin.staff.getAjaxPaginationData');
        Route::get('/staff/add', [StaffController::class, 'addEditForm'])->name('admin.staff.addForm');
        Route::get('/staff/edit/{id}', [StaffController::class, 'addEditForm'])->name('admin.staff.editForm');
        Route::post('/staff/addEdit', [StaffController::class, 'addEdit'])->name('admin.staff.addEdit');
        Route::post('/staff/changeStatus', [StaffController::class, 'changeStatus'])->name('admin.staff.changeStatus');
        Route::get('/staff/view/{id}', [StaffController::class, 'viewDetails'])->name('admin.staff.viewDetails');
        Route::get('/staff/pay-slip/{id?}', [StaffController::class, 'paySlip'])->name('admin.staff.paySlip');
        Route::post('/staff/pay-slip-ajax/{id?}', [StaffController::class, 'paySlipAjax'])->name('admin.staff.paySlipAjax');
        Route::post('/staff/save-salary-slip', [StaffController::class, 'saveSalarySlip'])->name('admin.staff.saveSalarySlip');
        Route::post('/staff/update-salary-slip', [StaffController::class, 'updateSalarySlip'])->name('admin.staff.updateSalarySlip');
        Route::get('/staff/download-salary-slip/{id}', [StaffController::class, 'downloadSalarySlip'])->name('admin.staff.downloadSalarySlip');

        ## Staff Lead Assign Member History:
        Route::get('/staff/lead-assign-history', [StaffLeadAssignHistoryController::class, 'index'])->name('admin.staffLeadAssignHistory.index');
        Route::post('/staff/lead-assign-history/getAjaxPaginationData', [StaffLeadAssignHistoryController::class, 'getAjaxPaginationData'])->name('admin.staffLeadAssignHistory.getAjaxPaginationData');
        Route::post('/staff/lead-assign-history/get-filter', [StaffLeadAssignHistoryController::class, 'getFilter'])->name('admin.staffLeadAssignHistory.getFilter');
        Route::post('/staff/lead-assign-history/download-report', [StaffLeadAssignHistoryController::class, 'downloadReport'])->name('admin.staffLeadAssignHistory.downloadReport');

        ## Staff Lead Assign Member History:
        Route::get('/staff/lead-unassign-history', [StaffLeadUnAssignHistoryController::class, 'index'])->name('admin.staffLeadUnAssignHistory.index');
        Route::post('/staff/lead-unassign-history/getAjaxPaginationData', [StaffLeadUnAssignHistoryController::class, 'getAjaxPaginationData'])->name('admin.staffLeadUnAssignHistory.getAjaxPaginationData');

        ## Staff Assignment Report:
        ## Staff Assign Member History:
        Route::get('/staff/assign-history', [StaffAssignHistoryController::class, 'index'])->name('admin.staffAssignHistory.index');
        Route::post('/staff/assign-history/getAjaxPaginationData', [StaffAssignHistoryController::class, 'getAjaxPaginationData'])->name('admin.staffAssignHistory.getAjaxPaginationData');
        Route::post('/staff/assign-history/changeStatus', [StaffAssignHistoryController::class, 'changeStatus'])->name('admin.staffAssignHistory.changeStatus');
        Route::post('/staff/assign-history/get-filter', [StaffAssignHistoryController::class, 'getFilter'])->name('admin.staffAssignHistory.getFilter');
        Route::post('/staff/assign-history/download-report', [StaffAssignHistoryController::class, 'downloadReport'])->name('admin.staffAssignHistory.downloadReport');

        ## Staff UnAssign Member History:
        Route::get('/staff/unassign-history', [StaffUnassignHistoryController::class, 'index'])->name('admin.staffUnassignHistory.index');
        Route::post('/staff/unassign-history/getAjaxPaginationData', [StaffUnassignHistoryController::class, 'getAjaxPaginationData'])->name('admin.staffUnassignHistory.getAjaxPaginationData');
        Route::post('/staff/unassign-history/changeStatus', [StaffUnassignHistoryController::class, 'changeStatus'])->name('admin.staffUnassignHistory.changeStatus');

        ## Staff Role:
        Route::get('/staff/role', [StaffRoleController::class, 'index'])->name('admin.staffRole.index');
        Route::post('/staff/role/getAjaxPaginationData', [StaffRoleController::class, 'getAjaxPaginationData'])->name('admin.staffRole.getAjaxPaginationData');
        Route::get('/staff/role/add', [StaffRoleController::class, 'addEditForm'])->name('admin.staffRole.addForm');
        Route::get('/staff/role/edit/{id}', [StaffRoleController::class, 'addEditForm'])->name('admin.staffRole.editForm');
        Route::post('/staff/role/addEdit', [StaffRoleController::class, 'addEdit'])->name('admin.staffRole.addEdit');
        Route::post('/staff/role/changeStatus', [StaffRoleController::class, 'changeStatus'])->name('admin.staffRole.changeStatus');

        // ## Staff Login History:
        Route::get('/staff/login-history', [StaffLoginHistoryController::class, 'index'])->name('admin.staffLoginHistory.index');
        Route::post('/staff/login-history/getAjaxPaginationData', [StaffLoginHistoryController::class, 'getAjaxPaginationData'])->name('admin.staffLoginHistory.getAjaxPaginationData');
        Route::post('/staff/login-history/changeStatus', [StaffLoginHistoryController::class, 'changeStatus'])->name('admin.staffLoginHistory.changeStatus');
        Route::get('/staff/login-history/{id}', [StaffLoginHistoryController::class, 'index'])->name('admin.staffLoginHistory.memberIndex');

        ## Manglik :
        Route::get('/manglik', [ManglikController::class, 'index'])->name('admin.manglik.index');
        Route::post('/manglik/getAjaxPaginationData', [ManglikController::class, 'getAjaxPaginationData'])->name('admin.manglik.getAjaxPaginationData');
        Route::get('/manglik/add', [ManglikController::class, 'addEditForm'])->name('admin.manglik.addForm');
        Route::get('/manglik/edit/{id}', [ManglikController::class, 'addEditForm'])->name('admin.manglik.editForm');
        Route::post('/manglik/addEdit', [ManglikController::class, 'addEdit'])->name('admin.manglik.addEdit');
        Route::post('/manglik/changeStatus', [ManglikController::class, 'changeStatus'])->name('admin.manglik.changeStatus');
        Route::post('/manglik/get-language', [ManglikController::class, 'getLangData'])->name('admin.manglik.getLangData');

        ## Horoscope :
        Route::get('/horoscope', [HoroscopeController::class, 'index'])->name('admin.horoscope.index');
        Route::post('/horoscope/getAjaxPaginationData', [HoroscopeController::class, 'getAjaxPaginationData'])->name('admin.horoscope.getAjaxPaginationData');
        Route::get('/horoscope/add', [HoroscopeController::class, 'addEditForm'])->name('admin.horoscope.addForm');
        Route::get('/horoscope/edit/{id}', [HoroscopeController::class, 'addEditForm'])->name('admin.horoscope.editForm');
        Route::post('/horoscope/addEdit', [HoroscopeController::class, 'addEdit'])->name('admin.horoscope.addEdit');
        Route::post('/horoscope/changeStatus', [HoroscopeController::class, 'changeStatus'])->name('admin.horoscope.changeStatus');
        Route::post('/horoscope/get-language', [HoroscopeController::class, 'getLangData'])->name('admin.horoscope.getLangData');

        ## Complexion :
        Route::get('/complexion', [ComplexionController::class, 'index'])->name('admin.complexion.index');
        Route::post('/complexion/getAjaxPaginationData', [ComplexionController::class, 'getAjaxPaginationData'])->name('admin.complexion.getAjaxPaginationData');
        Route::get('/complexion/add', [ComplexionController::class, 'addEditForm'])->name('admin.complexion.addForm');
        Route::get('/complexion/edit/{id}', [ComplexionController::class, 'addEditForm'])->name('admin.complexion.editForm');
        Route::post('/complexion/addEdit', [ComplexionController::class, 'addEdit'])->name('admin.complexion.addEdit');
        Route::post('/complexion/changeStatus', [ComplexionController::class, 'changeStatus'])->name('admin.complexion.changeStatus');
        Route::post('/complexion/get-language', [ComplexionController::class, 'getLangData'])->name('admin.complexion.getLangData');

        ## Blood Group :
        Route::get('/blood-group', [BloodGroupController::class, 'index'])->name('admin.blood.index');
        Route::post('/blood-group/getAjaxPaginationData', [BloodGroupController::class, 'getAjaxPaginationData'])->name('admin.blood.getAjaxPaginationData');
        Route::get('/blood-group/add', [BloodGroupController::class, 'addEditForm'])->name('admin.blood.addForm');
        Route::get('/blood-group/edit/{id}', [BloodGroupController::class, 'addEditForm'])->name('admin.blood.editForm');
        Route::post('/blood-group/addEdit', [BloodGroupController::class, 'addEdit'])->name('admin.blood.addEdit');
        Route::post('/blood-group/changeStatus', [BloodGroupController::class, 'changeStatus'])->name('admin.blood.changeStatus');
        Route::post('/blood-group/get-language', [BloodGroupController::class, 'getLangData'])->name('admin.blood.getLangData');

        ## Body Type :
        Route::get('/body-type', [BodytypeController::class, 'index'])->name('admin.bodytype.index');
        Route::post('/body-type/getAjaxPaginationData', [BodytypeController::class, 'getAjaxPaginationData'])->name('admin.bodytype.getAjaxPaginationData');
        Route::get('/body-type/add', [BodytypeController::class, 'addEditForm'])->name('admin.bodytype.addForm');
        Route::get('/body-type/edit/{id}', [BodytypeController::class, 'addEditForm'])->name('admin.bodytype.editForm');
        Route::post('/body-type/addEdit', [BodytypeController::class, 'addEdit'])->name('admin.bodytype.addEdit');
        Route::post('/body-type/changeStatus', [BodytypeController::class, 'changeStatus'])->name('admin.bodytype.changeStatus');
        Route::post('/body-type/get-language', [BodytypeController::class, 'getLangData'])->name('admin.bodytype.getLangData');

        ## Drink habit :
        Route::get('/drinking-habit', [DrinkHabitController::class, 'index'])->name('admin.drinkHabit.index');
        Route::post('/drinking-habit/getAjaxPaginationData', [DrinkHabitController::class, 'getAjaxPaginationData'])->name('admin.drinkHabit.getAjaxPaginationData');
        Route::get('/drinking-habit/add', [DrinkHabitController::class, 'addEditForm'])->name('admin.drinkHabit.addForm');
        Route::get('/drinking-habit/edit/{id}', [DrinkHabitController::class, 'addEditForm'])->name('admin.drinkHabit.editForm');
        Route::post('/drinking-habit/addEdit', [DrinkHabitController::class, 'addEdit'])->name('admin.drinkHabit.addEdit');
        Route::post('/drinking-habit/changeStatus', [DrinkHabitController::class, 'changeStatus'])->name('admin.drinkHabit.changeStatus');
        Route::post('/drinking-habit/get-language', [DrinkHabitController::class, 'getLangData'])->name('admin.drinkHabit.getLangData');

        ## Eating habit :
        Route::get('/eating-habit', [EatingHabitController::class, 'index'])->name('admin.eatingHabit.index');
        Route::post('/eating-habit/getAjaxPaginationData', [EatingHabitController::class, 'getAjaxPaginationData'])->name('admin.eatingHabit.getAjaxPaginationData');
        Route::get('/eating-habit/add', [EatingHabitController::class, 'addEditForm'])->name('admin.eatingHabit.addForm');
        Route::get('/eating-habit/edit/{id}', [EatingHabitController::class, 'addEditForm'])->name('admin.eatingHabit.editForm');
        Route::post('/eating-habit/addEdit', [EatingHabitController::class, 'addEdit'])->name('admin.eatingHabit.addEdit');
        Route::post('/eating-habit/changeStatus', [EatingHabitController::class, 'changeStatus'])->name('admin.eatingHabit.changeStatus');
        Route::post('/eating-habit/get-language', [EatingHabitController::class, 'getLangData'])->name('admin.eatingHabit.getLangData');

        ## Smoking :
        Route::get('/smoking-habit', [SmokeHabitController::class, 'index'])->name('admin.smokeHabit.index');
        Route::post('/smoking-habit/getAjaxPaginationData', [SmokeHabitController::class, 'getAjaxPaginationData'])->name('admin.smokeHabit.getAjaxPaginationData');
        Route::get('/smoking-habit/add', [SmokeHabitController::class, 'addEditForm'])->name('admin.smokeHabit.addForm');
        Route::get('/smoking-habit/edit/{id}', [SmokeHabitController::class, 'addEditForm'])->name('admin.smokeHabit.editForm');
        Route::post('/smoking-habit/addEdit', [SmokeHabitController::class, 'addEdit'])->name('admin.smokeHabit.addEdit');
        Route::post('/smoking-habit/changeStatus', [SmokeHabitController::class, 'changeStatus'])->name('admin.smokeHabit.changeStatus');
        Route::post('/smoking-habit/get-language', [SmokeHabitController::class, 'getLangData'])->name('admin.smokeHabit.getLangData');

        ## Family Status :
        Route::get('/family-status', [FamilyStatusController::class, 'index'])->name('admin.familystatus.index');
        Route::post('/family-status/getAjaxPaginationData', [FamilyStatusController::class, 'getAjaxPaginationData'])->name('admin.familystatus.getAjaxPaginationData');
        Route::get('/family-status/add', [FamilyStatusController::class, 'addEditForm'])->name('admin.familystatus.addForm');
        Route::get('/family-status/edit/{id}', [FamilyStatusController::class, 'addEditForm'])->name('admin.familystatus.editForm');
        Route::post('/family-status/addEdit', [FamilyStatusController::class, 'addEdit'])->name('admin.familystatus.addEdit');
        Route::post('/family-status/changeStatus', [FamilyStatusController::class, 'changeStatus'])->name('admin.familystatus.changeStatus');
        Route::post('/family-status/get-language', [FamilyStatusController::class, 'getLangData'])->name('admin.familystatus.getLangData');

        ## Family Type :
        Route::get('/family-type', [FamilyTypeController::class, 'index'])->name('admin.familytype.index');
        Route::post('/family-type/getAjaxPaginationData', [FamilyTypeController::class, 'getAjaxPaginationData'])->name('admin.familytype.getAjaxPaginationData');
        Route::get('/family-type/add', [FamilyTypeController::class, 'addEditForm'])->name('admin.familytype.addForm');
        Route::get('/family-type/edit/{id}', [FamilyTypeController::class, 'addEditForm'])->name('admin.familytype.editForm');
        Route::post('/family-type/addEdit', [FamilyTypeController::class, 'addEdit'])->name('admin.familytype.addEdit');
        Route::post('/family-type/changeStatus', [FamilyTypeController::class, 'changeStatus'])->name('admin.familytype.changeStatus');
        Route::post('/family-type/get-language', [FamilyTypeController::class, 'getLangData'])->name('admin.familytype.getLangData');

        ## Marital Status :
        Route::get('/marital-status', [MaritalStatusController::class, 'index'])->name('admin.maritalStatus.index');
        Route::post('/marital-status/getAjaxPaginationData', [MaritalStatusController::class, 'getAjaxPaginationData'])->name('admin.maritalStatus.getAjaxPaginationData');
        Route::get('/marital-status/add', [MaritalStatusController::class, 'addEditForm'])->name('admin.maritalStatus.addForm');
        Route::get('/marital-status/edit/{id}', [MaritalStatusController::class, 'addEditForm'])->name('admin.maritalStatus.editForm');
        Route::post('/marital-status/addEdit', [MaritalStatusController::class, 'addEdit'])->name('admin.maritalStatus.addEdit');
        Route::post('/marital-status/changeStatus', [MaritalStatusController::class, 'changeStatus'])->name('admin.maritalStatus.changeStatus');
        Route::post('/marital-status/get-language', [MaritalStatusController::class, 'getLangData'])->name('admin.maritalStatus.getLangData');

        ## Total Child :
        Route::get('/total-children', [TotalChildController::class, 'index'])->name('admin.totalchild.index');
        Route::post('/total-children/getAjaxPaginationData', [TotalChildController::class, 'getAjaxPaginationData'])->name('admin.totalchild.getAjaxPaginationData');
        Route::get('/total-children/add', [TotalChildController::class, 'addEditForm'])->name('admin.totalchild.addForm');
        Route::get('/total-children/edit/{id}', [TotalChildController::class, 'addEditForm'])->name('admin.totalchild.editForm');
        Route::post('/total-children/addEdit', [TotalChildController::class, 'addEdit'])->name('admin.totalchild.addEdit');
        Route::post('/total-children/changeStatus', [TotalChildController::class, 'changeStatus'])->name('admin.totalchild.changeStatus');
        Route::post('/total-children/get-language', [TotalChildController::class, 'getLangData'])->name('admin.totalchild.getLangData');

        ## Status Child :
        Route::get('/status-children', [StatusChildController::class, 'index'])->name('admin.statuschild.index');
        Route::post('/status-children/getAjaxPaginationData', [StatusChildController::class, 'getAjaxPaginationData'])->name('admin.statuschild.getAjaxPaginationData');
        Route::get('/status-children/add', [StatusChildController::class, 'addEditForm'])->name('admin.statuschild.addForm');
        Route::get('/status-children/edit/{id}', [StatusChildController::class, 'addEditForm'])->name('admin.statuschild.editForm');
        Route::post('/status-children/addEdit', [StatusChildController::class, 'addEdit'])->name('admin.statuschild.addEdit');
        Route::post('/status-children/changeStatus', [StatusChildController::class, 'changeStatus'])->name('admin.statuschild.changeStatus');
        Route::post('/status-children/get-language', [StatusChildController::class, 'getLangData'])->name('admin.statuschild.getLangData');

        ## Married Brother :
        Route::get('/married-brother', [MarriedBrotherController::class, 'index'])->name('admin.marriedBrother.index');
        Route::post('/married-brother/getAjaxPaginationData', [MarriedBrotherController::class, 'getAjaxPaginationData'])->name('admin.marriedBrother.getAjaxPaginationData');
        Route::get('/married-brother/add', [MarriedBrotherController::class, 'addEditForm'])->name('admin.marriedBrother.addForm');
        Route::get('/married-brother/edit/{id}', [MarriedBrotherController::class, 'addEditForm'])->name('admin.marriedBrother.editForm');
        Route::post('/married-brother/addEdit', [MarriedBrotherController::class, 'addEdit'])->name('admin.marriedBrother.addEdit');
        Route::post('/married-brother/changeStatus', [MarriedBrotherController::class, 'changeStatus'])->name('admin.marriedBrother.changeStatus');
        Route::post('/married-brother/get-language', [MarriedBrotherController::class, 'getLangData'])->name('admin.marriedBrother.getLangData');

        ## Married Sister :
        Route::get('/married-sister', [MarriedSisterController::class, 'index'])->name('admin.marriedSister.index');
        Route::post('/married-sister/getAjaxPaginationData', [MarriedSisterController::class, 'getAjaxPaginationData'])->name('admin.marriedSister.getAjaxPaginationData');
        Route::get('/married-sister/add', [MarriedSisterController::class, 'addEditForm'])->name('admin.marriedSister.addForm');
        Route::get('/married-sister/edit/{id}', [MarriedSisterController::class, 'addEditForm'])->name('admin.marriedSister.editForm');
        Route::post('/married-sister/addEdit', [MarriedSisterController::class, 'addEdit'])->name('admin.marriedSister.addEdit');
        Route::post('/married-sister/changeStatus', [MarriedSisterController::class, 'changeStatus'])->name('admin.marriedSister.changeStatus');
        Route::post('/married-sister/get-language', [MarriedSisterController::class, 'getLangData'])->name('admin.marriedSister.getLangData');

        ## No Of Brother / Sister :
        Route::get('/no-of-bro-sis', [NoOfBrotherSisterController::class, 'index'])->name('admin.noOfBrotherSister.index');
        Route::post('/no-of-bro-sis/getAjaxPaginationData', [NoOfBrotherSisterController::class, 'getAjaxPaginationData'])->name('admin.noOfBrotherSister.getAjaxPaginationData');
        Route::get('/no-of-bro-sis/add', [NoOfBrotherSisterController::class, 'addEditForm'])->name('admin.noOfBrotherSister.addForm');
        Route::get('/no-of-bro-sis/edit/{id}', [NoOfBrotherSisterController::class, 'addEditForm'])->name('admin.noOfBrotherSister.editForm');
        Route::post('/no-of-bro-sis/addEdit', [NoOfBrotherSisterController::class, 'addEdit'])->name('admin.noOfBrotherSister.addEdit');
        Route::post('/no-of-bro-sis/changeStatus', [NoOfBrotherSisterController::class, 'changeStatus'])->name('admin.noOfBrotherSister.changeStatus');
        Route::post('/no-of-bro-sis/get-language', [NoOfBrotherSisterController::class, 'getLangData'])->name('admin.noOfBrotherSister.getLangData');

        ## Profile By :
        Route::get('/profile-by', [ProfileByController::class, 'index'])->name('admin.profileby.index');
        Route::post('/profile-by/getAjaxPaginationData', [ProfileByController::class, 'getAjaxPaginationData'])->name('admin.profileby.getAjaxPaginationData');
        Route::get('/profile-by/add', [ProfileByController::class, 'addEditForm'])->name('admin.profileby.addForm');
        Route::get('/profile-by/edit/{id}', [ProfileByController::class, 'addEditForm'])->name('admin.profileby.editForm');
        Route::post('/profile-by/addEdit', [ProfileByController::class, 'addEdit'])->name('admin.profileby.addEdit');
        Route::post('/profile-by/changeStatus', [ProfileByController::class, 'changeStatus'])->name('admin.profileby.changeStatus');
        Route::post('/profile-by/get-language', [ProfileByController::class, 'getLangData'])->name('admin.profileby.getLangData');

        ## Residence :
        Route::get('/residence', [ResidenceController::class, 'index'])->name('admin.residence.index');
        Route::post('/residence/getAjaxPaginationData', [ResidenceController::class, 'getAjaxPaginationData'])->name('admin.residence.getAjaxPaginationData');
        Route::get('/residence/add', [ResidenceController::class, 'addEditForm'])->name('admin.residence.addForm');
        Route::get('/residence/edit/{id}', [ResidenceController::class, 'addEditForm'])->name('admin.residence.editForm');
        Route::post('/residence/addEdit', [ResidenceController::class, 'addEdit'])->name('admin.residence.addEdit');
        Route::post('/residence/changeStatus', [ResidenceController::class, 'changeStatus'])->name('admin.residence.changeStatus');
        Route::post('/residence/get-language', [ResidenceController::class, 'getLangData'])->name('admin.residence.getLangData');

        ## Member Field Section:
        Route::get('/member-field-section', [MemberFieldCheckController::class, 'index'])->name('admin.memberFieldAddEditForm');
        Route::post('/member-field-section-add', [MemberFieldCheckController::class, 'memberFieldAddEdit'])->name('admin.memberFieldAddEdit');

        ## Member Design Layout
        Route::get('/member-design-layouts', [MemberDesignLayoutController::class, 'index'])->name('admin.memberLayoutsDesign.index');
        Route::post('/member-design-layouts/getAjaxPaginationData', [MemberDesignLayoutController::class, 'getAjaxPaginationData'])->name('admin.memberLayoutsDesign.getAjaxPaginationData');
        Route::get('/member-design-layouts/add', [MemberDesignLayoutController::class, 'addEditForm'])->name('admin.memberLayoutsDesign.addForm');
        Route::get('/member-design-layouts/edit/{id}', [MemberDesignLayoutController::class, 'addEditForm'])->name('admin.memberLayoutsDesign.editForm');
        Route::post('/member-design-layouts/addEdit', [MemberDesignLayoutController::class, 'addEdit'])->name('admin.memberLayoutsDesign.addEdit');
        Route::post('/member-design-layouts/changeStatus', [MemberDesignLayoutController::class, 'changeStatus'])->name('admin.memberLayoutsDesign.changeStatus');

        ## Member Place Holders:
        Route::get('/member-placeholders', [MemberPlaceholderController::class, 'index'])->name('admin.memberPlaceholder.index');
        Route::post('/member-placeholders/getAjaxPaginationData', [MemberPlaceholderController::class, 'getAjaxPaginationData'])->name('admin.memberPlaceholder.getAjaxPaginationData');
        Route::get('/member-placeholders/add', [MemberPlaceholderController::class, 'addEditForm'])->name('admin.memberPlaceholder.addForm');
        Route::get('/member-placeholders/edit/{id}', [MemberPlaceholderController::class, 'addEditForm'])->name('admin.memberPlaceholder.editForm');
        Route::post('/member-placeholders/addEdit', [MemberPlaceholderController::class, 'addEdit'])->name('admin.memberPlaceholder.addEdit');
        Route::post('/member-placeholders/changeStatus', [MemberPlaceholderController::class, 'changeStatus'])->name('admin.memberPlaceholder.changeStatus');

        ## Personalize Homepage
        Route::get('/personalize-homepage', [PersonalizeHomepageController::class, 'index'])->name('admin.personalizeHomepage.index');
        Route::post('/personalize-homepage-update', [PersonalizeHomepageController::class, 'update'])->name('admin.personalizeHomepage.update');
        ## Home Page For Lang Data :
        Route::post('/personalize-homepage/getLangData', [PersonalizeHomepageController::class, 'getLangData'])->name('admin.personalizeHomepage.getLangData');

        ## Other Website Layouts:
        Route::get('/other-website-layouts', [OtherWebsiteLayoutController::class, 'index'])->name('admin.otherWebsiteLayout.index');
        Route::post('/other-website-layouts-update', [OtherWebsiteLayoutController::class, 'update'])->name('admin.otherWebsiteLayout.update');

        ## Callyzer Api Settings:
        Route::get('/callyzer-settings', [CallyzerApiSettingController::class, 'addEditForm'])->name('admin.callyzerApiSetting.index');
        Route::post('/callyzer-settings/addEdit', [CallyzerApiSettingController::class, 'addEdit'])->name('admin.callyzerApiSetting.addEdit');
        ## Callyzer Analysis Reports :
        Route::get('/callyzer/analysis', [EmployeeAnalysisCallyzerController::class, 'index'])->name('admin.employeeAnalysisCallyzer.index');
        Route::post('/callyzer/analysis/getAjaxPaginationData', [EmployeeAnalysisCallyzerController::class, 'getAjaxPaginationData'])->name('admin.employeeAnalysisCallyzer.getAjaxPaginationData');
        ## Employee Details:
        Route::get('/callyzer/details', [EmployeeDetailCallyzerController::class, 'index'])->name('admin.employeeDetailCallyzer.index');
        Route::post('/callyzer/details/getAjaxPaginationData', [EmployeeDetailCallyzerController::class, 'getAjaxPaginationData'])->name('admin.employeeDetailCallyzer.getAjaxPaginationData');
        ## Employee Summary Reports:
        Route::get('/callyzer/summary', [EmployeeSummaryCallyzerController::class, 'index'])->name('admin.employeeSummaryCallyzer.index');
        Route::post('/callyzer/summary/getAjaxPaginationData', [EmployeeSummaryCallyzerController::class, 'getAjaxPaginationData'])->name('admin.employeeSummaryCallyzer.getAjaxPaginationData');
        ## Emploeyer Coll log History :
        Route::get('/callyzer/call-log/history', [CallLogHistoryCallyzerController::class, 'index'])->name('admin.callLogHistoryCallyzer.index');
        Route::post('/callyzer/call-log/history/getAjaxPaginationData', [CallLogHistoryCallyzerController::class, 'getAjaxPaginationData'])->name('admin.callLogHistoryCallyzer.getAjaxPaginationData');

        ## Staff Payheads
        Route::get('/staff-pay-heads', [StaffpayheadsController::class, 'index'])->name('admin.staffPayHeads.index');
        Route::post('/staff-pay-heads/getAjaxPaginationData', [StaffpayheadsController::class, 'getAjaxPaginationData'])->name('admin.staffPayHeads.getAjaxPaginationData');
        Route::get('/staff-pay-heads/add', [StaffpayheadsController::class, 'addEditForm'])->name('admin.staffPayHeads.addForm');
        Route::get('/staff-pay-heads/edit/{id}', [StaffpayheadsController::class, 'addEditForm'])->name('admin.staffPayHeads.editForm');
        Route::post('/staff-pay-heads/addEdit', [StaffpayheadsController::class, 'addEdit'])->name('admin.staffPayHeads.addEdit');
        Route::post('/staff-pay-heads/changeStatus', [StaffpayheadsController::class, 'changeStatus'])->name('admin.staffPayHeads.changeStatus');
        Route::post('/staff-pay-heads/get-language', [StaffpayheadsController::class, 'getLangData'])->name('admin.staffPayHeads.getLangData');

        ## Staff Holydays
        Route::get('/staff-holidays', [StaffHolidayMasterController::class, 'index'])->name('admin.staffHolidays.index');
        Route::post('/staff-holidays/getAjaxPaginationData', [StaffHolidayMasterController::class, 'getAjaxPaginationData'])->name('admin.staffHolidays.getAjaxPaginationData');
        Route::get('/staff-holidays/add', [StaffHolidayMasterController::class, 'addEditForm'])->name('admin.staffHolidays.addForm');
        Route::get('/staff-holidays/edit/{id}', [StaffHolidayMasterController::class, 'addEditForm'])->name('admin.staffHolidays.editForm');
        Route::post('/staff-holidays/addEdit', [StaffHolidayMasterController::class, 'addEdit'])->name('admin.staffHolidays.addEdit');
        Route::post('/staff-holidays/changeStatus', [StaffHolidayMasterController::class, 'changeStatus'])->name('admin.staffHolidays.changeStatus');
        Route::post('/staff-holidays/get-language', [StaffHolidayMasterController::class, 'getLangData'])->name('admin.staffHolidays.getLangData');

        ## Staff Leave Management:
        Route::get('/staff-leave', [StaffLeaveManagementController::class, 'index'])->name('admin.staffLeaveManagement.index');
        Route::post('/staff-leave/getAjaxPaginationData', [StaffLeaveManagementController::class, 'getAjaxPaginationData'])->name('admin.staffLeaveManagement.getAjaxPaginationData');
        Route::get('/staff-leave/add', [StaffLeaveManagementController::class, 'addEditForm'])->name('admin.staffLeaveManagement.addForm');
        Route::post('/staff-leave/addEdit', [StaffLeaveManagementController::class, 'addEdit'])->name('admin.staffLeaveManagement.addEdit');

        ## Admin Leave Management:
        Route::get('/admin-leave', [AdminLeaveManagementController::class, 'index'])->name('admin.adminLeaveManagement.index');
        Route::post('/admin-leave/getAjaxPaginationData', [AdminLeaveManagementController::class, 'getAjaxPaginationData'])->name('admin.adminLeaveManagement.getAjaxPaginationData');
        Route::post('/admin-leave/get-filter', [AdminLeaveManagementController::class, 'getFilter'])->name('admin.adminLeaveManagement.getFilter');
        Route::get('/admin-leave/download-csv', [AdminLeaveManagementController::class, 'downloadCsv'])->name('admin.adminLeaveManagement.downloadCsv');
        Route::get('/admin-leave/download-pdf', [AdminLeaveManagementController::class, 'downloadPdf'])->name('admin.adminLeaveManagement.downloadPdf');
        Route::post('/admin-leave/changeStatus', [AdminLeaveManagementController::class, 'changeStatus'])->name('admin.adminLeaveManagement.changeStatus');

        ## Staff Reimbursements:
        Route::get('/staff-reimbursements', [StaffReimbursementController::class, 'index'])->name('admin.staffReimbursements.index');
        Route::post('/staff-reimbursements/getAjaxPaginationData', [StaffReimbursementController::class, 'getAjaxPaginationData'])->name('admin.staffReimbursements.getAjaxPaginationData');
        Route::get('/staff-reimbursements/add', [StaffReimbursementController::class, 'addEditForm'])->name('admin.staffReimbursements.addForm');
        Route::post('/staff-reimbursements/addEdit', [StaffReimbursementController::class, 'addEdit'])->name('admin.staffReimbursements.addEdit');

        ## Admin Reimbursements:
        Route::get('/admin-reimbursements', [AdminReimbursementsController::class, 'index'])->name('admin.adminReimbursements.index');
        Route::post('/admin-reimbursements/getAjaxPaginationData', [AdminReimbursementsController::class, 'getAjaxPaginationData'])->name('admin.adminReimbursements.getAjaxPaginationData');
        Route::post('/admin-reimbursements/get-filter', [AdminReimbursementsController::class, 'getFilter'])->name('admin.adminReimbursements.getFilter');
        Route::post('/admin-reimbursements/changeStatus', [AdminReimbursementsController::class, 'changeStatus'])->name('admin.adminReimbursements.changeStatus');

        ## Staff NDA & Other Docs:
        Route::get('/staff-documents', [NdaAndOtherDocsController::class, 'index'])->name('admin.ndaAndOtherDocs.index');
        Route::post('/staff-documents/getAjaxPaginationData', [NdaAndOtherDocsController::class, 'getAjaxPaginationData'])->name('admin.ndaAndOtherDocs.getAjaxPaginationData');
        Route::get('/staff-documents/add', [NdaAndOtherDocsController::class, 'addEditForm'])->name('admin.ndaAndOtherDocs.addForm');
        Route::get('/staff-documents/edit/{id}', [NdaAndOtherDocsController::class, 'addEditForm'])->name('admin.ndaAndOtherDocs.editForm');
        Route::post('/staff-documents/addEdit', [NdaAndOtherDocsController::class, 'addEdit'])->name('admin.ndaAndOtherDocs.addEdit');
        Route::post('/staff-documents/changeStatus', [NdaAndOtherDocsController::class, 'changeStatus'])->name('admin.ndaAndOtherDocs.changeStatus');
        Route::post('/staff-documents/get-filter', [NdaAndOtherDocsController::class, 'getFilter'])->name('admin.ndaAndOtherDocs.getFilter');

        ## Punch In/Out:
        Route::get('/staff-attendance', [StaffAttendanceController::class, 'index'])->name('admin.staffAttendance.index');
        Route::post('/staff-attendance/getAjaxPaginationData', [StaffAttendanceController::class, 'getAjaxPaginationData'])->name('admin.staffAttendance.getAjaxPaginationData');
        Route::post('/staff-attendance/get-filter', [StaffAttendanceController::class, 'getFilter'])->name('admin.staffAttendance.getFilter');
        Route::get('/staff-attendance/download-csv', [StaffAttendanceController::class, 'downloadCsv'])->name('admin.staffAttendance.downloadCsv');
        Route::get('/staff-attendance/download-pdf', [StaffAttendanceController::class, 'downloadPdf'])->name('admin.staffAttendance.downloadPdf');
        Route::post('/staff-attendance/punch-in-out', [StaffAttendanceController::class, 'punchInOut'])->name('admin.staffAttendance.punchInOut');

        ## Staff Commission:
        Route::get('/staff-commission', [StaffCommissionController::class, 'index'])->name('admin.staffCommission.index');
        Route::post('/staff-commission/getAjaxPaginationData', [StaffCommissionController::class, 'getAjaxPaginationData'])->name('admin.staffCommission.getAjaxPaginationData');
        Route::post('/staff-commission/get-filter', [StaffCommissionController::class, 'getFilter'])->name('admin.staffCommission.getFilter');
        Route::get('/staff-commission/download-csv', [StaffCommissionController::class, 'downloadCsv'])->name('admin.staffCommission.downloadCsv');
        Route::get('/staff-commission/download-pdf', [StaffCommissionController::class, 'downloadPdf'])->name('admin.staffCommission.downloadPdf');

        ## Staff Score Cards:
        Route::get('/staff-scorecards', [StaffScoreCardController::class, 'index'])->name('admin.staffScoreCard.index');
        Route::post('/staff-scorecards/getAjaxPaginationData', [StaffScoreCardController::class, 'getAjaxPaginationData'])->name('admin.staffScoreCard.getAjaxPaginationData');
        Route::get('/staff-scorecards/download-pdf/{monthYear?}', [StaffScoreCardController::class, 'downloadPdf'])->name('admin.staffScoreCard.downloadPdf');
        Route::get('/staff-scorecards/download-csv/{monthYear?}', [StaffScoreCardController::class, 'downloadCsv'])->name('admin.staffScoreCard.downloadCsv');

        ## Staff Score Cards:
        Route::get('/staff-leaderboards', [StaffLeaderBoardController::class, 'index'])->name('admin.staffLeaderBoard.index');
        Route::post('/staff-leaderboards/getAjaxPaginationData', [StaffLeaderBoardController::class, 'getAjaxPaginationData'])->name('admin.staffLeaderBoard.getAjaxPaginationData');
        Route::get('/staff-leaderboards/download-pdf/{monthYear?}', [StaffLeaderBoardController::class, 'downloadPdf'])->name('admin.staffLeaderBoard.downloadPdf');
        Route::get('/staff-leaderboards/download-csv/{monthYear?}', [StaffLeaderBoardController::class, 'downloadCsv'])->name('admin.staffLeaderBoard.downloadCsv');

        ## Staff Salary Slip:
        Route::get('/staff-salary-slip', [StaffSalarySlipController::class, 'index'])->name('admin.staffSalarySlip.index');
        Route::post('/staff-salary-slip/getAjaxPaginationData', [StaffSalarySlipController::class, 'getAjaxPaginationData'])->name('admin.staffSalarySlip.getAjaxPaginationData');
        Route::post('/staff-salary-slip/get-filter', [StaffSalarySlipController::class, 'getFilter'])->name('admin.staffSalarySlip.getFilter');
        Route::get('/staff-salary-slip/download-csv', [StaffSalarySlipController::class, 'downloadCsv'])->name('admin.staffSalarySlip.downloadCsv');
        Route::get('/staff-salary-slip/download-pdf', [StaffSalarySlipController::class, 'downloadPdf'])->name('admin.staffSalarySlip.downloadPdf');

        ## Manage dynamic website colors :
        Route::get('theme-settings',        [WebThemeSettingController::class, 'index'])->name('admin.themeSettings.index');
        Route::post('theme-settings',       [WebThemeSettingController::class, 'update'])->name('admin.themeSettings.update');
        Route::post('theme-settings/reset', [WebThemeSettingController::class, 'reset'])->name('admin.themeSettings.reset');
        Route::post('theme-settings/color-preset/apply', [WebThemeSettingController::class, 'applyColorPreset'])->name('admin.themeSettings.applyColorPreset');

        ## App theme Settings:
        Route::get('app-theme-setting', [AppThemeSettingController::class, 'index'])->name('admin.appThemeSetting.index');
        ## One generic save route for every tab :
        Route::post('app-theme-setting/save/{tab}', [AppThemeSettingController::class, 'saveTab'])->where('tab', '[a-z-]+')->name('admin.themeSettingSaveTab');
        Route::post('app-theme-setting/design-option/upload', [AppThemeSettingController::class, 'designOptionUpload'])->name('admin.designOptionUpload');
        Route::post('app-theme-setting/design-option/approve', [AppThemeSettingController::class, 'designOptionApprove'])->name('admin.designOptionApprove');
        Route::post('app-theme-setting/design-option/delete', [AppThemeSettingController::class, 'designOptionDelete'])->name('admin.designOptionDelete');
        Route::post('app-theme-setting/design-option/edit', [AppThemeSettingController::class, 'designOptionEdit'])->name('admin.designOptionEdit');
        Route::post('app-theme-setting/reset/{tab}', [AppThemeSettingController::class, 'resetTab'])->where('tab', '[a-z-]+')->name('admin.themeSettingResetTab');
        Route::post('app-theme-setting/color-preset/apply', [AppThemeSettingController::class, 'applyColorPreset'])->name('admin.applyColorPreset');

        ## Homepage Designs (multi-homepage management)
        Route::get('/homepage-designs', [HomePageDesignController::class, 'index'])->name('admin.homePageDesign.index');
        Route::get('/homepage-designs/create', [HomePageDesignController::class, 'create'])->name('admin.homePageDesign.create');
        Route::post('/homepage-designs/store', [HomePageDesignController::class, 'store'])->name('admin.homePageDesign.store');
        Route::get('/homepage-designs/{id}/edit', [HomePageDesignController::class, 'edit'])->name('admin.homePageDesign.edit');
        Route::post('/homepage-designs/update/{id}', [HomePageDesignController::class, 'update'])->name('admin.homePageDesign.update');
        Route::get('/homepage-designs/{id}/fields', [HomePageDesignController::class, 'manageFields'])->name('admin.homePageDesign.manageFields');
        Route::post('/homepage-designs/{id}/fields', [HomePageDesignController::class, 'updateSchema'])->name('admin.homePageDesign.updateSchema');
        Route::post('/homepage-designs/{id}/activate', [HomePageDesignController::class, 'activate'])->name('admin.homePageDesign.activate');
        // Route::post('home-page-design/activate/{id}', [HomePageDesignController::class, 'activate'])->name('admin.homePageDesign.activate');
        Route::delete('/homepage-designs/{id}', [HomePageDesignController::class, 'destroy'])->name('admin.homePageDesign.destroy');
        Route::post('/homepage-designs/getLangData', [HomePageDesignController::class, 'getLangData'])->name('admin.homePageDesign.getLangData');

        ## Setup / Installation Checklist :
        Route::get('/setup-checklist', [SetupChecklistController::class, 'index'])->name('admin.setupChecklist.index');
        Route::get('/setup-checklist/status-json', [SetupChecklistController::class, 'statusJson'])->name('admin.setupChecklist.statusJson');
        Route::post('/setup-checklist/toggle-manual', [SetupChecklistController::class, 'toggleManualStatus'])->name('admin.setupChecklist.toggleManualStatus');
        Route::post('/setup-checklist/test-queue', [SetupChecklistController::class, 'testQueue'])->name('admin.setupChecklist.testQueue');
    });
});

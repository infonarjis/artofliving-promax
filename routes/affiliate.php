<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Affiliate\{
    AffiliateHomeController,
    AffiliateLoginController,
    AffiliateRegisterController,
    AffiliateDashboardController,
    AffiliateMyProfileController,
    AffiliateVisitorClicksController,
    AffiliateAssignMemberController,
    AffiliateIncomeListController,
    AffiliatePaymentHistoryController,
    ForgotPasswordController,
    ResetPasswordController
};

Route::prefix('affiliate')->name('affiliate.')->group(function () {

    ## Public page
    Route::get('/', [AffiliateHomeController::class, 'index'])->name('home.index');

    ## Guest only (logged-in affiliates get redirected away)
    Route::middleware('affiliate.guest')->group(function () {
        Route::get('/login',          [AffiliateLoginController::class, 'index'])->name('login.index');
        Route::post('/login',         [AffiliateLoginController::class, 'authenticate'])->name('login.authenticate');

        Route::get('/register',       [AffiliateRegisterController::class, 'index'])->name('register.index');
        Route::post('/register',      [AffiliateRegisterController::class, 'store'])->name('register.store');

        Route::get('forgot-password', [ForgotPasswordController::class, 'index'])->name('forgotPassword.index');
        Route::post('forgot-password/send', [ForgotPasswordController::class, 'sendResetLink'])->name('forgotPassword.send');

        Route::get('reset-password/{token}',  [ResetPasswordController::class, 'index'])->name('resetPassword.index');
        Route::post('reset-password/reset',   [ResetPasswordController::class, 'resetPassword'])->name('resetPassword.reset');
    });

    ## Authenticated affiliates only
    Route::middleware('affiliate.auth')->group(function () {
        Route::post('/logout',        [AffiliateLoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard',      [AffiliateDashboardController::class, 'index'])->name('dashboard');
        Route::post('/settlement/request', [AffiliateDashboardController::class, 'requestSettlement'])->name('settlement.request');

        ## My Profile :
        Route::get('/my-profile',      [AffiliateMyProfileController::class, 'index'])->name('myProfile.index');
        Route::post('/my-profile/update', [AffiliateMyProfileController::class, 'updateProfile'])->name('myProfile.update');
        Route::post('/my-profile/change-password', [AffiliateMyProfileController::class, 'changePassword'])->name('myProfile.changePassword');

        ## Visitor Clicks:
        Route::get('/visitor-clicks', [AffiliateVisitorClicksController::class, 'index'])->name('visitorClick.index');

        ## Affiliate Members:
        Route::get('/assign-member', [AffiliateAssignMemberController::class, 'index'])->name('assignMember.index');

        ## Income List:
        Route::get('/income-list', [AffiliateIncomeListController::class, 'index'])->name('incomeList.index');

        ## Payment History :
        Route::get('/payment-history', [AffiliatePaymentHistoryController::class, 'index'])->name('paymentHistory.index');

    });

});
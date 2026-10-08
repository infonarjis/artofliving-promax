{{-- Verification Pending State UI --}}
<style>
    /* ==========================================================================
       PREMIUM VERIFICATION PENDING PAGE - DYNAMIC THEME SYSTEM
       ========================================================================== */

    .verification-pending-section {
        min-height: calc(100vh - 270px);
        display: flex;
        align-items: center;
        position: relative;
        padding: 55px 0 65px;
        background-color: var(--black-color);
        background-image: 
            radial-gradient(circle at 50% 12%, rgba(245, 158, 11, 0.08) 0%, transparent 45%),
            radial-gradient(circle at 85% 75%, rgba(var(--primary-color-rgb, 13, 86, 222), 0.06) 0%, transparent 50%),
            radial-gradient(circle at 15% 80%, rgba(16, 185, 129, 0.05) 0%, transparent 45%);
        overflow: hidden;
    }

    /* Ambient background glowing orbs */
    .verification-pending-section::before,
    .verification-pending-section::after {
        content: "";
        position: absolute;
        width: 380px;
        height: 380px;
        border-radius: 50%;
        filter: blur(95px);
        pointer-events: none;
        opacity: 0.28;
        z-index: 1;
        transition: opacity 0.4s ease;
    }
    .verification-pending-section::before {
        top: -80px;
        left: 8%;
        background: radial-gradient(circle, #f59e0b 0%, rgba(245, 158, 11, 0) 70%);
    }
    .verification-pending-section::after {
        bottom: -80px;
        right: 8%;
        background: radial-gradient(circle, var(--primary-color) 0%, rgba(13, 86, 222, 0) 70%);
    }

    /* Main Status Card */
    .verify-status-card {
        position: relative;
        z-index: 2;
        border-radius: 24px;
        background: var(--black-color-1) !important;
        border: 1px solid var(--common-border);
        box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .light-mode .verify-status-card {
        background: #ffffff !important;
        border: 1px solid rgba(245, 158, 11, 0.2);
        box-shadow: 0 20px 45px -10px rgba(245, 158, 11, 0.12), 0 6px 20px -5px rgba(0, 0, 0, 0.05);
    }

    /* ==========================================================================
       ANIMATED ICON & PULSING BADGE SYSTEM
       ========================================================================== */

    .verify-badge-wrapper {
        position: relative;
        width: 104px;
        height: 104px;
        margin: 6px auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Dual pulsing rings */
    .verify-pulse-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: rgba(245, 158, 11, 0.22);
        animation: verifyPulseRing 2.6s cubic-bezier(0.2, 0.6, 0.35, 1) infinite;
        pointer-events: none;
    }

    .verify-pulse-ring.second {
        animation-delay: 1.3s;
    }

    .light-mode .verify-pulse-ring {
        background: rgba(217, 119, 6, 0.2);
    }

    @keyframes verifyPulseRing {
        0% {
            transform: scale(0.78);
            opacity: 0.85;
        }
        60% {
            transform: scale(1.36);
            opacity: 0.15;
        }
        100% {
            transform: scale(1.45);
            opacity: 0;
        }
    }

    /* Modern Icon Container */
    .verify-icon-box {
        position: relative;
        z-index: 2;
        width: 86px;
        height: 86px;
        border-radius: 50%;
        background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 12px 28px -4px rgba(217, 119, 6, 0.5), inset 0 2px 4px rgba(255, 255, 255, 0.4);
        border: 3px solid rgba(255, 255, 255, 0.25);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .verify-icon-box:hover {
        transform: scale(1.05);
        box-shadow: 0 16px 32px -4px rgba(217, 119, 6, 0.6), inset 0 2px 4px rgba(255, 255, 255, 0.5);
    }

    .light-mode .verify-icon-box {
        background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);
        box-shadow: 0 12px 28px -4px rgba(180, 83, 9, 0.4), inset 0 2px 4px rgba(255, 255, 255, 0.45);
        border: 3px solid #ffffff;
    }

    .verify-main-icon {
        font-size: 48px;
        color: #ffffff;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        animation: floatShield 3.6s ease-in-out infinite alternate;
    }

    @keyframes floatShield {
        0% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-5px) rotate(1.5deg);
        }
        100% {
            transform: translateY(0px) rotate(0deg);
        }
    }

    /* ==========================================================================
       LIVE STATUS CHIP SYSTEM
       ========================================================================== */

    .verify-status-chip {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 7px 18px;
        border-radius: 40px;
        background: rgba(245, 158, 11, 0.14);
        color: #f59e0b;
        font-weight: 700;
        letter-spacing: 0.75px;
        border: 1px solid rgba(245, 158, 11, 0.32);
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.12);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        transition: all 0.25s ease;
    }

    .light-mode .verify-status-chip {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid rgba(217, 119, 6, 0.3);
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.12);
    }

    .pulse-indicator {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: #f59e0b;
        box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.75);
        animation: chipPulse 1.8s infinite;
    }

    .light-mode .pulse-indicator {
        background: #d97706;
        box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.75);
    }

    @keyframes chipPulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.75);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 7px rgba(245, 158, 11, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
        }
    }

    /* Content Typography & Layout */
    .line-height-relaxed {
        line-height: 1.68;
    }

    /* ==========================================================================
       PROGRESS TIMELINE BOX
       ========================================================================== */

    .verify-steps-box {
        background: var(--black-color-3);
        border: 1px solid var(--common-border);
        border-radius: 16px;
        transition: background 0.3s ease, border-color 0.3s ease;
    }

    .light-mode .verify-steps-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .border-bottom-subtle {
        border-bottom: 1px solid var(--common-border);
    }

    .light-mode .border-bottom-subtle {
        border-bottom-color: #e2e8f0;
    }

    .step-check-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        flex-shrink: 0;
        transition: transform 0.25s ease;
    }

    .step-check-icon.complete {
        background: rgba(16, 185, 129, 0.16);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .light-mode .step-check-icon.complete {
        background: #d1fae5;
        color: #059669;
        border-color: rgba(5, 150, 105, 0.25);
    }

    .step-check-icon.in-progress {
        background: rgba(245, 158, 11, 0.18);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.35);
    }

    .light-mode .step-check-icon.in-progress {
        background: #fef3c7;
        color: #d97706;
        border-color: rgba(217, 119, 6, 0.3);
    }

    .step-check-icon.upcoming {
        background: var(--black-color-4);
        color: var(--white-color-70);
    }

    .light-mode .step-check-icon.upcoming {
        background: #e2e8f0;
        color: #64748b;
    }

    /* ==========================================================================
       ASSISTANCE & CONTACT ACTION CARDS
       ========================================================================== */

    .verify-support-container {
        border-top: 1px dashed var(--common-border);
    }

    .light-mode .verify-support-container {
        border-top-color: #e2e8f0;
    }

    .verify-contact-btn {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 16px;
        border-radius: 14px;
        background: var(--black-color-3);
        border: 1px solid var(--common-border);
        color: var(--white-color);
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }

    .light-mode .verify-contact-btn {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #0f1522;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .verify-contact-btn:hover {
        background: var(--primary-light-color);
        border-color: var(--primary-color);
        color: var(--primary-color) !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(13, 86, 222, 0.16);
    }

    .light-mode .verify-contact-btn:hover {
        background: rgba(13, 86, 222, 0.05);
        border-color: var(--primary-color);
        color: var(--primary-color) !important;
    }

    .verify-btn-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        transition: transform 0.25s ease;
    }

    .verify-contact-btn:hover .verify-btn-icon {
        transform: scale(1.08);
    }

    .verify-btn-icon.phone {
        background: rgba(13, 86, 222, 0.14);
        color: var(--primary-color);
    }

    .verify-btn-icon.email {
        background: rgba(16, 185, 129, 0.14);
        color: #10b981;
    }

    .light-mode .verify-btn-icon.phone {
        background: rgba(13, 86, 222, 0.1);
        color: var(--primary-color);
    }

    .light-mode .verify-btn-icon.email {
        background: rgba(5, 150, 105, 0.1);
        color: #059669;
    }

    /* Action Buttons */
    .comman-bg-btn {
        background: var(--primary-color);
        color: #ffffff !important;
        border-radius: 10px;
        border: 1px solid var(--primary-color);
        box-shadow: 0 4px 14px rgba(var(--primary-color-rgb, 13, 86, 222), 0.35);
        transition: all 0.25s ease;
        text-decoration: none;
    }

    .comman-bg-btn:hover {
        opacity: 0.94;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(var(--primary-color-rgb, 13, 86, 222), 0.45);
        color: #ffffff !important;
    }

    .btn-outline-custom {
        background: transparent;
        color: var(--white-color) !important;
        border: 1px solid var(--common-border);
        border-radius: 10px;
        transition: all 0.25s ease;
        text-decoration: none;
    }

    .btn-outline-custom:hover {
        border-color: var(--error-color);
        color: var(--error-color) !important;
        background: rgba(240, 61, 62, 0.08);
        transform: translateY(-2px);
    }

    .light-mode .btn-outline-custom {
        color: #0f1522 !important;
        border-color: #cbd5e1;
    }

    .light-mode .btn-outline-custom:hover {
        border-color: var(--error-color);
        color: var(--error-color) !important;
        background: rgba(240, 61, 62, 0.06);
    }
</style>

<section class="common-section-bg verification-pending-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10">
                <div class="common-bgwhite-main verify-status-card p-4 p-md-5 text-center wow fadeInUp position-relative overflow-hidden" data-wow-duration="0.7s">

                    {{-- Top Status Badge & Animated Icon --}}
                    <div class="verify-badge-wrapper">
                        <div class="verify-pulse-ring"></div>
                        <div class="verify-pulse-ring second"></div>
                        <div class="verify-icon-box">
                            <iconify-icon icon="solar:shield-warning-bold-duotone" class="verify-main-icon"></iconify-icon>
                        </div>
                    </div>

                    {{-- Dynamic Under Review Status Chip --}}
                    <div class="mb-3">
                        <div class="verify-status-chip">
                            <span class="pulse-indicator"></span>
                            <span class="fts-13 fw-6 text-uppercase letter-spacing-1">{{ __('messages.lbl_under_review') }}</span>
                        </div>
                    </div>

                    <h2 class="fts-28 fw-7 white-color-n mb-2">
                        {{ __('messages.lbl_pending_verification') }}
                    </h2>
                    
                    <p class="fts-15 fw-4 white-color70-n mb-4 line-height-relaxed px-md-2">
                        {{ __('messages.lbl_thank_you_signing_up_verification') }}
                    </p>

                    {{-- Step/Process indicators --}}
                    <div class="verify-steps-box p-3 mb-4 text-start">
                        <div class="d-flex align-items-center mb-2 pb-2 border-bottom-subtle">
                            <div class="step-check-icon me-3 complete">
                                <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fts-14 fw-6 white-color-n">{{ __('messages.lbl_registration_submitted') }}</div>
                                <div class="fts-12 white-color70-n">{{ __('messages.lbl_registration_submitted_desc') }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2 pb-2 border-bottom-subtle">
                            <div class="step-check-icon me-3 in-progress">
                                <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fts-14 fw-6 white-color-n">{{ __('messages.lbl_profile_verification') }}</div>
                                <div class="fts-12 white-color70-n">{{ __('messages.lbl_profile_verification_desc') }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="step-check-icon me-3 upcoming">
                                <iconify-icon icon="solar:bell-bing-bold"></iconify-icon>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fts-14 fw-6 white-color-n">{{ __('messages.lbl_access_activation') }}</div>
                                <div class="fts-12 white-color70-n">{{ __('messages.lbl_access_activation_desc') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Assistance Contact Channels --}}
                    <div class="verify-support-container pt-3">

                        <div class="row g-2 justify-content-center">
                            <div class="col-sm-6">
                                <a href="tel:919900038442" class="verify-contact-btn w-100">
                                    <div class="verify-btn-icon phone">
                                        <iconify-icon icon="solar:phone-calling-rounded-bold-duotone"></iconify-icon>
                                    </div>
                                    <div class="text-start">
                                        <div class="fts-11 fw-5 text-uppercase opacity-75">{{ __('messages.lbl_phone_support') }}</div>
                                        <div class="fts-13 fw-6 text-truncate">(+91) 9900038442/2</div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6">
                                <a href="mailto:info@artofliving.org" class="verify-contact-btn w-100">
                                    <div class="verify-btn-icon email">
                                        <iconify-icon icon="solar:letter-bold-duotone"></iconify-icon>
                                    </div>
                                    <div class="text-start">
                                        <div class="fts-11 fw-5 text-uppercase opacity-75">{{ __('messages.lbl_email_support') }}</div>
                                        <div class="fts-13 fw-6 text-truncate">info@artofliving.org</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="mt-4 pt-2 d-flex flex-wrap align-items-center justify-content-center gap-3">
                        <form id="logout-form-verify" action="{{ route('web.logout') }}" method="POST"
                            style="display: none;">
                            @csrf
                        </form>
                        <a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form-verify').submit();" class="btn-outline-custom px-4 py-2 fts-14 fw-6 d-inline-flex align-items-center gap-2">
                            <iconify-icon icon="solar:logout-2-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_logout') }}</span>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

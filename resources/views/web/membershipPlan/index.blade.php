@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    <!-- pricing section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="membership-plans-main">
                    <div class="membership-plan-title text-center mx-auto col-lg-6 col-xl-5">
                        <h1 class="fts-28 fw-7 white-color-n">{{ __('messages.lbl_membership_plans_title') }}</h1>
                        <p class="fts-14 fw-4 white-color70-n mt-1">
                            {{ __('messages.lbl_plans_intro', ['matrimonial' => __('messages.lbl_matrimonial_plan'), 'personalize' => __('messages.lbl_personalize_plan')]) }}
                        </p>
                    </div>
                    <div class="row">
                        @if(!empty($personalizePlans))
                            <div class="col-lg-2 col-12"></div>
                            <div class="col-lg-8 col-12">
                                <div class="common-tabs-design mt-4 mb-3">
                                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                        <li class="nav-item {{ !empty($personalizePlans) ? 'w-50' : 'w-100' }}">
                                            <button class="nav-link fts-14 fw-5 active" id="pills-matrimonial-tab"
                                                data-bs-toggle="pill" data-bs-target="#pills-matrimonial" type="button"
                                                role="tab" aria-controls="pills-matrimonial" aria-selected="true">
                                                <iconify-icon icon="hugeicons:heart-check" width="20" class="me-1"
                                                    height="20"></iconify-icon>
                                                {{ __('messages.lbl_matrimonial_plan') }}
                                            </button>
                                        </li>

                                        @if (!blank($personalizePlans))
                                            <li class="nav-item w-50">
                                                <button class="nav-link fts-14 fw-5" id="pills-personlize-tab"
                                                    data-bs-toggle="pill" data-bs-target="#pills-personlize" type="button"
                                                    role="tab" aria-controls="pills-personlize" aria-selected="false">
                                                    <iconify-icon icon="hugeicons:money-send-01" width="20" class="me-1"
                                                        height="20"></iconify-icon>
                                                    {{ __('messages.lbl_personalize_plan') }}
                                                </button>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="col-lg-2 col-12"></div>

                            </div>
                            <div class="col-lg-2 col-12"></div>
                        @endif
                        </div>
                        @php
                            $voiceApproved = $configArr['zego_voice_call_setting'] === 'APPROVED';
                            $videoApproved = $configArr['zego_video_call_setting'] === 'APPROVED';
                        @endphp
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-matrimonial" role="tabpanel"
                                aria-labelledby="pills-matrimonial-tab">
                                <div class="col-xxl-11 mx-auto">
                                    <div class="row px-1 mt-lg-1 d-flex justify-content-center">
                                        @php
                                            $i = 0;
                                            $planIconArr = ['', 'silver-plan', 'diamond-plan'];
                                        @endphp
                                        @foreach ($standardPlans as $plan)
                                            <div class="col-lg-4 col-md-6 px-2 mt-3">
                                                <div class="single_meber-plans {{ $planIconArr[$i] }} p-3">
                                                    <div class="plan-header-box">
                                                        <div class="plan-title-contents">
                                                            <p class="fts-22 fw-6 white-color-p">{{ $plan->plan_name }}</p>
                                                            <h4 class="fts-15 fw-4 white-color-p opacity-75">
                                                                {{ __('messages.lbl_for_individuals') }}
                                                            </h4>
                                                        </div>
                                                        <div class="plan-pricing fts-28 white-color-p">
                                                            @if ($plan->plan_type == 'FREE')
                                                                <span class="fw-6">{{ __('messages.lbl_free') }}</span>
                                                            @else
                                                                @if (!empty($plan->plan_discount_amount) && $plan->plan_discount_amount > 0 && $plan->plan_discount_amount != $plan->plan_amount)
                                                                    <div class="plan-price-wrapper">
                                                                        {{-- Original Price --}}
                                                                        <div class="plan-original-price">
                                                                            {{ $plan->currency_code }}
                                                                            {{ number_format($plan->plan_amount, 0) }}
                                                                        </div>

                                                                        {{-- Final Price + Validity --}}
                                                                        <div class="plan-final-price">
                                                                            <span class="plan-amount">
                                                                                {{ $plan->currency_code }}
                                                                                {{ number_format($plan->plan_discount_amount, 2) }}
                                                                            </span>

                                                                            <span class="plan-validity">
                                                                                / {{ $plan->validity_days }}
                                                                                {{ __('messages.lbl_days') }}
                                                                            </span>
                                                                        </div>

                                                                    </div>
                                                                @else
                                                                    <span
                                                                        class="fw-6">{{ $plan->currency_code . ' ' . $plan->plan_amount }}</span>
                                                                    <span class="fts-16 fw-4 white-color-p opacity-75">/
                                                                        {{ $plan->validity_days }}
                                                                        {{ __('messages.lbl_days') }}</span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="plans_bottomdivsvaf mt-4 px-1">
                                                        <h4 class="white-color-n fw-6 fts-18">
                                                            {{ __('messages.lbl_key_features') }}</h4>
                                                        <ul class="listmatchis-items pt-1">
                                                            <li
                                                                class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                                                @if ($plan->view_profile_limit > 0)
                                                                    <iconify-icon icon="material-symbols:check"
                                                                        class="text-success fts-20"></iconify-icon>
                                                                @else
                                                                    <iconify-icon icon="material-symbols:close"
                                                                        class="text-danger fts-20"></iconify-icon>
                                                                @endif
                                                                {{ __('messages.lbl_allowed_viewed_profile') }} -
                                                                {{ $plan->view_profile_limit }}
                                                            </li>
                                                            <li
                                                                class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                                                @if ($plan->interests_limit > 0)
                                                                    <iconify-icon icon="material-symbols:check"
                                                                        class="text-success fts-20"></iconify-icon>
                                                                @else
                                                                    <iconify-icon icon="material-symbols:close"
                                                                        class="text-danger fts-20"></iconify-icon>
                                                                @endif
                                                                {{ __('messages.lbl_allowed_interests') }} -
                                                                {{ $plan->interests_limit }}
                                                            </li>
                                                            <li
                                                                class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                                                @if ($plan->contact_views_limit > 0)
                                                                    <iconify-icon icon="material-symbols:check"
                                                                        class="text-success fts-20"></iconify-icon>
                                                                @else
                                                                    <iconify-icon icon="material-symbols:close"
                                                                        class="text-danger fts-20"></iconify-icon>
                                                                @endif
                                                                {{ __('messages.lbl_allowed_contact_views') }} -
                                                                {{ $plan->contact_views_limit }}
                                                            </li>
                                                            <li
                                                                class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                                                @if ($plan->can_chat)
                                                                    <iconify-icon icon="material-symbols:check"
                                                                        class="text-success fts-20"></iconify-icon>
                                                                @else
                                                                    <iconify-icon icon="material-symbols:close"
                                                                        class="text-danger fts-20"></iconify-icon>
                                                                @endif
                                                                {{ __('messages.lbl_live_chat') }}
                                                            </li>
                                                            @if ($voiceApproved)
                                                                <li
                                                                    class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                                                    @if ($plan->audio_minutes_limit > 0)
                                                                        <iconify-icon icon="material-symbols:check"
                                                                            class="text-success fts-20"></iconify-icon>
                                                                        {{ __('messages.lbl_audio_calls') }} -
                                                                        {{ $plan->audio_minutes_limit }} min
                                                                    @else
                                                                        <iconify-icon icon="material-symbols:close"
                                                                            class="text-danger fts-20"></iconify-icon>
                                                                        {{ __('messages.lbl_audio_calls') }}
                                                                    @endif
                                                                </li>
                                                            @endif
                                                            @if ($videoApproved)
                                                                <li
                                                                    class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                                                    @if ($plan->video_minutes_limit > 0)
                                                                        <iconify-icon icon="material-symbols:check"
                                                                            class="text-success fts-20"></iconify-icon>
                                                                        {{ __('messages.lbl_video_calls') }} -
                                                                        {{ $plan->video_minutes_limit }} min
                                                                    @else
                                                                        <iconify-icon icon="material-symbols:close"
                                                                            class="text-danger fts-20"></iconify-icon>
                                                                        {{ __('messages.lbl_video_calls') }}
                                                                    @endif
                                                                </li>
                                                            @endif
                                                            @if (_getConstant('AI_MODE') == 'Enabled')
                                                                <li
                                                                    class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                                                    @if ($plan->ai_interest)
                                                                        <iconify-icon icon="material-symbols:check"
                                                                            class="text-success fts-20"></iconify-icon>
                                                                    @else
                                                                        <iconify-icon icon="material-symbols:close"
                                                                            class="text-danger fts-20"></iconify-icon>
                                                                    @endif
                                                                    {{ __('messages.lbl_send_auto_ai_interest') }}
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                    <hr class="gray2-color-L opacity-100">
                                                    <ul class="mt-2 mt-lg-3">
                                                        @php
                                                            $planDesc = trim($plan->plan_description ?? '');
                                                            $limit = 30;
                                                        @endphp
                                                        @if (strlen($planDesc) > $limit)
                                                            <li
                                                                class="plan-list-style fts-15 fw-4 white-color70-n read-more-box">
                                                                <span class="short-text">
                                                                    {{ \Illuminate\Support\Str::limit($planDesc, $limit) }}
                                                                </span>
                                                                <span class="full-text d-none">{{ $planDesc }}</span>
                                                                <span class="fw-6 primary-color-n read-toggle"
                                                                    style="cursor:pointer;">
                                                                    {{ __('messages.lbl_read_more') }}
                                                                </span>
                                                            </li>
                                                        @else
                                                            <li class="plan-list-style fts-15 fw-4 white-color70-n">
                                                                {{ $planDesc ?? '' }}
                                                            </li>
                                                        @endif
                                                    </ul>
                                                    <div class="plan-next-btndgrg mt-2 mt-lg-4">
                                                        <a href="{{ route('web.membershipPlan.checkout', $plan->id) }}"
                                                            class="plan-primary-btn fts-15">{{ __('messages.lbl_buy_now') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                            @php
                                                $i++;
                                                if ($i == 3) {
                                                    $i = 0;
                                                }
                                            @endphp
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="pills-personlize" role="tabpanel"
                                aria-labelledby="pills-personlize-tab">
                                <div class="col-xxl-11 mx-auto">
                                    <div class="row px-1 mt-lg-1 d-flex justify-content-center">
                                        @php
                                            $i = 0;
                                            $planIconArr = ['', 'silver-plan', 'diamond-plan'];
                                        @endphp
                                        @foreach ($personalizePlans as $plan)
                                            <div class="col-lg-4 col-md-6 px-2 mt-3">
                                                <div class="single_meber-plans {{ $planIconArr[$i] }} p-3">
                                                    <div class="plan-header-box">
                                                        <div class="plan-title-contents">
                                                            <p class="fts-22 fw-6 white-color-p">{{ $plan->plan_name }}
                                                            </p>
                                                            <h4 class="fts-15 fw-4 white-color-p opacity-75">For
                                                                individuals
                                                            </h4>
                                                        </div>
                                                        <div class="plan-pricing fts-28 white-color-p">
                                                            @if ($plan->plan_type == 'FREE')
                                                                <span class="fw-6">{{ __('messages.lbl_free') }}</span>
                                                            @else
                                                                @if (!empty($plan->plan_discount_amount) && $plan->plan_discount_amount > 0 && $plan->plan_discount_amount != $plan->plan_amount)
                                                                    <div class="plan-price-wrapper">
                                                                        {{-- Original Price --}}
                                                                        <div class="plan-original-price">
                                                                            {{ $plan->currency_code }}
                                                                            {{ number_format($plan->plan_amount, 0) }}
                                                                        </div>

                                                                        {{-- Final Price + Validity --}}
                                                                        <div class="plan-final-price">
                                                                            <span class="plan-amount">
                                                                                {{ $plan->currency_code }}
                                                                                {{ number_format($plan->plan_discount_amount, 0) }}
                                                                            </span>

                                                                            <span class="plan-validity">
                                                                                / {{ $plan->validity_days }}
                                                                                {{ __('messages.lbl_days') }}
                                                                            </span>
                                                                        </div>

                                                                    </div>
                                                                @else
                                                                    <span
                                                                        class="fw-6">{{ $plan->currency_code . ' ' . $plan->plan_amount }}</span>
                                                                    <span class="fts-16 fw-4 white-color-p opacity-75">/
                                                                        {{ $plan->validity_days }}
                                                                        {{ __('messages.lbl_days') }}</span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="plans_bottomdivsvaf mt-4 px-1">
                                                        <h4 class="white-color-n fw-6 fts-18">
                                                            {{ __('messages.lbl_key_features') }}</h4>
                                                        <ul class="listmatchis-items pt-1">
                                                            <li
                                                                class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                                                @if ($plan->view_profile_limit > 0)
                                                                    <iconify-icon icon="material-symbols:check"
                                                                        class="text-success fts-20"></iconify-icon>
                                                                @else
                                                                    <iconify-icon icon="material-symbols:close"
                                                                        class="text-danger fts-20"></iconify-icon>
                                                                @endif
                                                                {{ __('messages.lbl_allowed_viewed_profile') }} -
                                                                {{ $plan->view_profile_limit }}
                                                            </li>
                                                            <li
                                                                class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                                                @if ($plan->interests_limit > 0)
                                                                    <iconify-icon icon="material-symbols:check"
                                                                        class="text-success fts-20"></iconify-icon>
                                                                @else
                                                                    <iconify-icon icon="material-symbols:close"
                                                                        class="text-danger fts-20"></iconify-icon>
                                                                @endif
                                                                {{ __('messages.lbl_allowed_interests') }} -
                                                                {{ $plan->interests_limit }}
                                                            </li>
                                                            <li
                                                                class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                                                @if ($plan->contact_views_limit > 0)
                                                                    <iconify-icon icon="material-symbols:check"
                                                                        class="text-success fts-20"></iconify-icon>
                                                                @else
                                                                    <iconify-icon icon="material-symbols:close"
                                                                        class="text-danger fts-20"></iconify-icon>
                                                                @endif
                                                                {{ __('messages.lbl_allowed_contact_views') }} -
                                                                {{ $plan->contact_views_limit }}
                                                            </li>
                                                            <li
                                                                class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                                                @if ($plan->can_chat)
                                                                    <iconify-icon icon="material-symbols:check"
                                                                        class="text-success fts-20"></iconify-icon>
                                                                @else
                                                                    <iconify-icon icon="material-symbols:close"
                                                                        class="text-danger fts-20"></iconify-icon>
                                                                @endif
                                                                {{ __('messages.lbl_live_chat') }}
                                                            </li>
                                                            @if ($videoApproved)
                                                                <li
                                                                    class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                                                    @if ($plan->audio_minutes_limit > 0)
                                                                        <iconify-icon icon="material-symbols:check"
                                                                            class="text-success fts-20"></iconify-icon>
                                                                        {{ __('messages.lbl_audio_calls') }} -
                                                                        {{ $plan->audio_minutes_limit }} min
                                                                    @else
                                                                        <iconify-icon icon="material-symbols:close"
                                                                            class="text-danger fts-20"></iconify-icon>
                                                                        {{ __('messages.lbl_audio_calls') }}
                                                                    @endif
                                                                </li>
                                                            @endif
                                                            @if ($videoApproved)
                                                                <li
                                                                    class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                                                    @if ($plan->video_minutes_limit > 0)
                                                                        <iconify-icon icon="material-symbols:check"
                                                                            class="text-success fts-20"></iconify-icon>
                                                                        {{ __('messages.lbl_video_calls') }} -
                                                                        {{ $plan->video_minutes_limit }} min
                                                                    @else
                                                                        <iconify-icon icon="material-symbols:close"
                                                                            class="text-danger fts-20"></iconify-icon>
                                                                        {{ __('messages.lbl_video_calls') }}
                                                                    @endif
                                                                </li>
                                                            @endif
                                                            @if (_getConstant('AI_MODE') == 'Enabled')
                                                            <li
                                                                class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                                                @if ($plan->ai_interest)
                                                                    <iconify-icon icon="material-symbols:check"
                                                                        class="text-success fts-20"></iconify-icon>
                                                                @else
                                                                    <iconify-icon icon="material-symbols:close"
                                                                        class="text-danger fts-20"></iconify-icon>
                                                                @endif
                                                                {{ __('messages.lbl_send_auto_ai_interest') }}
                                                            </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                    <hr class="gray2-color-L opacity-100">
                                                    <ul class="mt-2 mt-lg-3">
                                                        @php
                                                            $planDesc = trim($plan->plan_description ?? '');
                                                            $limit = 80;
                                                        @endphp
                                                        @if (strlen($planDesc) > $limit)
                                                            <li
                                                                class="plan-list-style fts-15 fw-4 white-color70-n read-more-box">
                                                                <span class="short-text">
                                                                    {{ \Illuminate\Support\Str::limit($planDesc, $limit) }}
                                                                </span>
                                                                <span class="full-text d-none">{{ $planDesc }}</span>
                                                                <span class="fw-6 primary-color-n read-toggle"
                                                                    style="cursor:pointer;">
                                                                    {{ __('messages.lbl_read_more') }}
                                                                </span>
                                                            </li>
                                                        @else
                                                            <li class="plan-list-style fts-15 fw-4 white-color70-n">
                                                                {{ $planDesc ?? '' }}
                                                            </li>
                                                        @endif
                                                    </ul>
                                                    <div class="plan-next-btndgrg mt-2 mt-lg-4">
                                                        <a href="{{ route('web.membershipPlan.checkout', $plan->id) }}"
                                                            class="plan-primary-btn fts-15">{{ __('messages.lbl_buy_now') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                            @php
                                                $i++;
                                                if ($i == 3) {
                                                    $i = 0;
                                                }
                                            @endphp
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @include('web.membershipPlan.callback_box')

                    @if (!empty($offlinePayment))
                        <div
                            class="or-withline-center my-4 position-relative d-flex align-items-center justify-content-center">
                            <span class="fts-14 bg-gradient">{{ __('messages.lbl_payment_options') }}</span>
                        </div>
                        <div class="common-bgwhite-main p-3 p-lg-4 mx-lg-5">
                            <div class="row px-1">
                                <div class="col-lg-6 px-2">
                                    <div class="common-bglight-main p-3 p-lg-4">
                                        <h4 class="fts-18 fw-6 white-color-n">
                                            {{ __('messages.lbl_scan_and_pay_with_any_bhim_upi') }}</h4>
                                        <p class="fts-14 fw-4 white-color70-n mt-1">
                                            {{ __('messages.lbl_qr_code_or_upi') }}</p>
                                        <div class="d-flex align-items-center justify-content-between mt-lg-4 mt-3">
                                            <div class="qr-codegenrets">
                                                @php
                                                    $qrCodeImageUrl = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                                                    if (
                                                        !blank($offlinePayment->qr_code) &&
                                                        _checkStorageFileExists(
                                                            'upload_path.PAYMENT_LOGO_URL',
                                                            $offlinePayment->qr_code,
                                                        )
                                                    ) {
                                                        $qrCodeImageUrl =
                                                            _assetUrl('upload_path.PAYMENT_LOGO_URL') .
                                                            $offlinePayment->qr_code;
                                                    }
                                                @endphp
                                                <img src="{{ $qrCodeImageUrl }}" alt="Qr Codes" class="qr-codes">
                                            </div>
                                            <div class="upi-bhimsdfef pe-lg-5">
                                                @php
                                                    $paymentGatewayImageUrl = _assetUrl(
                                                        'upload_path.WEB_NO_IMAGE_FOUND',
                                                    );
                                                    if (
                                                        !blank($offlinePayment->payment_gateway_img) &&
                                                        _checkStorageFileExists(
                                                            'upload_path.PAYMENT_LOGO_URL',
                                                            $offlinePayment->payment_gateway_img,
                                                        )
                                                    ) {
                                                        $paymentGatewayImageUrl =
                                                            _assetUrl('upload_path.PAYMENT_LOGO_URL') .
                                                            $offlinePayment->payment_gateway_img;
                                                    }
                                                @endphp
                                                <img src="{{ $paymentGatewayImageUrl }}" alt="Payment Gateway"
                                                    class="upibheem">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 px-2 mt-3 mt-lg-0">
                                    <div class="common-bglight-main p-3 p-lg-4">
                                        <h4 class="fts-18 fw-6 white-color-n">
                                            {{ __('messages.lbl_pay_by_bank_transfer') }}</h4>
                                        <div class="bank-details-content mt-3 mt-lg-4">
                                            <p class="fts-14 white-color-n fw-4 mb-2 mb-lg-3">
                                                <span class="fw-5">{{ __('messages.lbl_bank_name') }}</span> :
                                                {{ $offlinePayment->bank_name }}
                                            </p>
                                            <p class="fts-14 white-color-n fw-4 mb-2 mb-lg-3">
                                                <span class="fw-5">{{ __('messages.lbl_account_holder_name') }}</span> :
                                                {{ $offlinePayment->account_holder_number }}
                                            </p>
                                            <p class="fts-14 white-color-n fw-4 mb-2 mb-lg-3">
                                                <span class="fw-5">{{ __('messages.lbl_account_number') }}</span> :
                                                {{ $offlinePayment->account_number }}
                                            </p>
                                            <p class="fts-14 white-color-n fw-4 mb-2 mb-lg-3">
                                                <span class="fw-5">{{ __('messages.lbl_bank_branch') }}</span> :
                                                {{ $offlinePayment->bank_branch }}
                                            </p>
                                            <p class="fts-14 white-color-n fw-4 mb-2 mb-lg-3">
                                                <span class="fw-5">{{ __('messages.lbl_ifsc_code') }}</span> :
                                                {{ $offlinePayment->ifsc_code }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
    </section>
@endsection

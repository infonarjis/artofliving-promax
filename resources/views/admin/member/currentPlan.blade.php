@extends(_getConstant('dir_path.ADMIN_DIR_PATH').'.admin_layout')

@section('admin_content')

<div class="content-wrapper">

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="partner_user_Tabmain">

            {{-- =========================================================
                CURRENT PLAN
            ========================================================== --}}
            @if(isset($resultArr) && !empty($resultArr))

                <div class="plan-section mb-4">

                    {{-- Current Plan Header --}}
                    <div class="plan-section-header">

                        <div class="plan-section-title">

                            <div class="plan-main-icon">
                                <i class='bx bx-grid-alt'></i>
                            </div>

                            <div>
                                <h4>Current Plan</h4>
                                <span>Active membership plan</span>
                            </div>

                        </div>

                        <div class="plan-status active">
                            <i class='bx bxs-circle'></i>
                            Active
                        </div>

                    </div>


                    {{-- Current Plan Body --}}
                    <div class="plan-card">

                        <div class="plan-card-body">

                            <div class="row g-3">

                                @foreach ($currentPlanArr as $key => $planValue)

                                    @php

                                        $planIcons = [

                                            // Plan Information
                                            'plan_name'              => 'bx-crown',
                                            'plan_title'             => 'bx-crown',
                                            'plan_type'              => 'bx-credit-card',
                                            'plan_activated'         => 'bx-calendar-check',
                                            'plan_start_date'        => 'bx-calendar-check',
                                            'plan_expired'           => 'bx-calendar-x',
                                            'plan_end_date'          => 'bx-calendar-x',

                                            // Amount / Currency
                                            'plan_amount'            => 'bx-rupee',
                                            'amount'                 => 'bx-rupee',
                                            'plan_discount'          => 'bx-purchase-tag',
                                            'discount'               => 'bx-purchase-tag',
                                            'plan_currency'          => 'bx-money',
                                            'currency'               => 'bx-money',
                                            'plan_validity'          => 'bx-calendar',

                                            // Payment
                                            'payment_note'           => 'bx-note',
                                            'payment_mode'           => 'bx-wallet',
                                            'payment_method'         => 'bx-wallet',
                                            'payment_source'         => 'bx-credit-card',

                                            // Status
                                            'current_plan'           => 'bx-check-circle',
                                            'plan_status'            => 'bx-check-circle',
                                            'plan_description'       => 'bx-detail',

                                            // Features
                                            'plan_chat'              => 'bx-message-rounded-dots',
                                            'chat'                   => 'bx-message-rounded-dots',

                                            'plan_view_profile_used' => 'bx-show',
                                            'view_profile_used'      => 'bx-show',

                                            'plan_interest_used'     => 'bx-heart',
                                            'interest_used'          => 'bx-heart',

                                            'plan_contact_used'      => 'bx-user',
                                            'contact_used'           => 'bx-user',

                                            'plan_video_call_used'   => 'bx-video',
                                            'video_call_used'        => 'bx-video',

                                            'plan_video_call'        => 'bx-phone-call',
                                            'video_call'             => 'bx-phone-call',

                                            // Coupon / Tax
                                            'coupon_code'            => 'bx-purchase-tag',
                                            'coupon'                 => 'bx-purchase-tag',

                                            'tax_amount'             => 'bx-receipt',
                                            'tax'                    => 'bx-receipt',

                                        ];

                                        $icon = $planIcons[$key] ?? 'bx-info-circle';

                                    @endphp


                                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">

                                        <div class="plan-detail-box">

                                            {{-- Icon Box --}}
                                            <div class="plan-detail-icon">

                                                <i class='bx {{ $icon }}'></i>

                                            </div>


                                            {{-- Content --}}
                                            <div class="plan-detail-content">

                                                <div class="plan-detail-label">
                                                    {{ $planValue['label'] }}
                                                </div>


                                                <div class="plan-detail-value">

                                                    @if ($planValue['type'] == 'str')

                                                        {!! _displayNotAvailable($resultArr->$key) !!}


                                                    @elseif ($planValue['type'] == 'used')

                                                        @php
                                                            $field = $planValue['field'];
                                                        @endphp

                                                        <strong>
                                                            {{ $resultArr->$field }}
                                                        </strong>

                                                        <span class="usage-text">
                                                            available out of
                                                            {{ $resultArr->$key }}
                                                        </span>


                                                    @elseif ($planValue['type'] == 'date')

                                                        <i class='bx bx-calendar'></i>

                                                        {{ _displayDate($resultArr->$key, 'j F, Y') }}


                                                    @elseif ($planValue['type'] == 'per')

                                                        {!! _displayNotAvailable($resultArr->$key) !!}

                                                    @endif

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                @endforeach

                            </div>


                            {{-- =====================================================
                                CURRENT PLAN ADD-ON PACKAGES
                            ====================================================== --}}
                            @if(isset($addOnPlanArr) && !blank($addOnPlanArr))

                                <div class="addon-section">

                                    <div class="addon-heading">

                                        <div class="addon-title-icon">
                                            <i class='bx bx-package'></i>
                                        </div>

                                        <div>
                                            <h5>Add-On Packages</h5>
                                            <span>Additional active packages</span>
                                        </div>

                                    </div>


                                    <div class="row g-3">

                                        @foreach ($addOnPlanArr as $values)

                                            {{-- Package Title --}}
                                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">

                                                <div class="addon-card">

                                                    <div class="addon-icon">
                                                        <i class='bx bx-package'></i>
                                                    </div>

                                                    <div class="addon-content">

                                                        <span class="addon-label">
                                                            Package Title
                                                        </span>

                                                        <strong>
                                                            {{ _displayNotAvailable($values->package_title) }}
                                                        </strong>

                                                    </div>

                                                </div>

                                            </div>


                                            {{-- Package Category --}}
                                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">

                                                <div class="addon-card">

                                                    <div class="addon-icon">
                                                        <i class='bx bx-category'></i>
                                                    </div>

                                                    <div class="addon-content">

                                                        <span class="addon-label">
                                                            Package Category
                                                        </span>

                                                        <strong>
                                                            {{ _displayNotAvailable($values->package_category) }}
                                                        </strong>

                                                    </div>

                                                </div>

                                            </div>


                                            {{-- Package Amount --}}
                                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">

                                                <div class="addon-card">

                                                    <div class="addon-icon">
                                                        <i class='bx bx-rupee'></i>
                                                    </div>

                                                    <div class="addon-content">

                                                        <span class="addon-label">
                                                            Package Amount
                                                        </span>

                                                        <strong>
                                                            {{ _displayNotAvailable($values->package_amount) }}
                                                        </strong>

                                                    </div>

                                                </div>

                                            </div>


                                            {{-- Package Count --}}
                                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">

                                                <div class="addon-card">

                                                    <div class="addon-icon">
                                                        <i class='bx bx-hash'></i>
                                                    </div>

                                                    <div class="addon-content">

                                                        <span class="addon-label">
                                                            Package Count
                                                        </span>

                                                        <strong>
                                                            {{ _displayNotAvailable($values->package_count) }}
                                                        </strong>

                                                    </div>

                                                </div>

                                            </div>

                                        @endforeach

                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

            @endif


            {{-- =========================================================
                RECENT PLANS
            ========================================================== --}}
            @if(isset($recentPlanArr) && !empty($recentPlanArr))

                <div class="plan-section">

                    {{-- Recent Plan Header --}}
                    <div class="plan-section-header">

                        <div class="plan-section-title">

                            <div class="plan-main-icon recent">
                                <i class='bx bx-history'></i>
                            </div>

                            <div>
                                <h4>Recent Plans</h4>
                                <span>Previous membership plans</span>
                            </div>

                        </div>

                        <div class="plan-status recent-status">
                            <i class='bx bx-history'></i>
                            Recent
                        </div>

                    </div>


                    {{-- Recent Plans --}}
                    @foreach ($recentPlanArr as $recentKey => $recentValue)

                        <div class="recent-plan-card mb-4">

                            <div class="recent-plan-header">

                                <div class="recent-plan-name">

                                    <div class="recent-plan-icon">
                                        <i class='bx bx-crown'></i>
                                    </div>

                                    <div>

                                        <h5>
                                            {{ _displayNotAvailable($recentValue->plan_name ?? null) }}
                                        </h5>

                                        @if(isset($recentValue->plan_status))
                                            <span>
                                                {{ $recentValue->plan_status }}
                                            </span>
                                        @endif

                                    </div>

                                </div>

                            </div>


                            <div class="plan-card-body">

                                <div class="row g-3">

                                    @foreach ($currentPlanArr as $key => $planValue)

                                        @php

                                            $planIcons = [

                                                'plan_name'              => 'bx-crown',
                                                'plan_title'             => 'bx-crown',
                                                'plan_type'              => 'bx-credit-card',
                                                'plan_activated'         => 'bx-calendar-check',
                                                'plan_start_date'        => 'bx-calendar-check',
                                                'plan_expired'           => 'bx-calendar-x',
                                                'plan_end_date'          => 'bx-calendar-x',

                                                'plan_amount'            => 'bx-rupee',
                                                'amount'                 => 'bx-rupee',
                                                'plan_discount'          => 'bx-purchase-tag',
                                                'discount'               => 'bx-purchase-tag',
                                                'plan_currency'          => 'bx-money',
                                                'currency'               => 'bx-money',
                                                'plan_validity'          => 'bx-calendar',

                                                'payment_note'           => 'bx-note',
                                                'payment_mode'           => 'bx-wallet',
                                                'payment_method'         => 'bx-wallet',

                                                'current_plan'           => 'bx-check-circle',
                                                'plan_status'            => 'bx-check-circle',
                                                'plan_description'       => 'bx-detail',

                                                'plan_chat'              => 'bx-message-rounded-dots',
                                                'chat'                   => 'bx-message-rounded-dots',

                                                'plan_view_profile_used' => 'bx-show',
                                                'view_profile_used'      => 'bx-show',

                                                'plan_interest_used'     => 'bx-heart',
                                                'interest_used'          => 'bx-heart',

                                                'plan_contact_used'      => 'bx-user',
                                                'contact_used'           => 'bx-user',

                                                'plan_video_call_used'   => 'bx-video',
                                                'video_call_used'        => 'bx-video',

                                                'plan_video_call'        => 'bx-phone-call',
                                                'video_call'             => 'bx-phone-call',

                                                'coupon_code'            => 'bx-purchase-tag',
                                                'coupon'                 => 'bx-purchase-tag',

                                                'tax_amount'             => 'bx-receipt',
                                                'tax'                    => 'bx-receipt',

                                            ];

                                            $icon = $planIcons[$key] ?? 'bx-info-circle';

                                        @endphp


                                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">

                                            <div class="plan-detail-box">

                                                <div class="plan-detail-icon">

                                                    <i class='bx {{ $icon }}'></i>

                                                </div>


                                                <div class="plan-detail-content">

                                                    <div class="plan-detail-label">
                                                        {{ $planValue['label'] }}
                                                    </div>


                                                    <div class="plan-detail-value">

                                                        @if ($planValue['type'] == 'str')

                                                            {!! _displayNotAvailable($recentValue->$key) !!}


                                                        @elseif ($planValue['type'] == 'used')

                                                            @php
                                                                $field = $planValue['field'];
                                                            @endphp

                                                            <strong>
                                                                {{ $recentValue->$field }}
                                                            </strong>

                                                            <span class="usage-text">
                                                                available out of
                                                                {{ $recentValue->$key }}
                                                            </span>


                                                        @elseif ($planValue['type'] == 'date')

                                                            <i class='bx bx-calendar'></i>

                                                            {{ _displayDate($recentValue->$key, 'j F, Y') }}


                                                        @elseif ($planValue['type'] == 'per')

                                                            {!! _displayNotAvailable($recentValue->$key) !!}

                                                        @endif

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>


                                {{-- =====================================================
                                    RECENT PLAN ADD-ONS
                                ====================================================== --}}
                                @if(isset($recentValue->addOnPlanArr) && !blank($recentValue->addOnPlanArr))

                                    <div class="addon-section">

                                        <div class="addon-heading">

                                            <div class="addon-title-icon">
                                                <i class='bx bx-package'></i>
                                            </div>

                                            <div>
                                                <h5>Add-On Packages</h5>
                                                <span>Packages purchased with this plan</span>
                                            </div>

                                        </div>


                                        <div class="row g-3">

                                            @foreach ($recentValue->addOnPlanArr as $values)

                                                {{-- Package Title --}}
                                                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">

                                                    <div class="addon-card">

                                                        <div class="addon-icon">
                                                            <i class='bx bx-package'></i>
                                                        </div>

                                                        <div class="addon-content">

                                                            <span class="addon-label">
                                                                Package Title
                                                            </span>

                                                            <strong>
                                                                {{ _displayNotAvailable($values->package_title) }}
                                                            </strong>

                                                        </div>

                                                    </div>

                                                </div>


                                                {{-- Package Category --}}
                                                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">

                                                    <div class="addon-card">

                                                        <div class="addon-icon">
                                                            <i class='bx bx-category'></i>
                                                        </div>

                                                        <div class="addon-content">

                                                            <span class="addon-label">
                                                                Package Category
                                                            </span>

                                                            <strong>
                                                                {{ _displayNotAvailable($values->package_category) }}
                                                            </strong>

                                                        </div>

                                                    </div>

                                                </div>


                                                {{-- Package Amount --}}
                                                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">

                                                    <div class="addon-card">

                                                        <div class="addon-icon">
                                                            <i class='bx bx-rupee'></i>
                                                        </div>

                                                        <div class="addon-content">

                                                            <span class="addon-label">
                                                                Package Amount
                                                            </span>

                                                            <strong>
                                                                {{ _displayNotAvailable($values->package_amount) }}
                                                            </strong>

                                                        </div>

                                                    </div>

                                                </div>


                                                {{-- Package Count --}}
                                                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">

                                                    <div class="addon-card">

                                                        <div class="addon-icon">
                                                            <i class='bx bx-hash'></i>
                                                        </div>

                                                        <div class="addon-content">

                                                            <span class="addon-label">
                                                                Package Count
                                                            </span>

                                                            <strong>
                                                                {{ _displayNotAvailable($values->package_count) }}
                                                            </strong>

                                                        </div>

                                                    </div>

                                                </div>

                                            @endforeach

                                        </div>

                                    </div>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>

    </div>

    <div class="content-backdrop fade"></div>

</div>


<style>

/* =========================================================
   PLAN SECTION
========================================================= */

.plan-section {
    width: 100%;
}


/* =========================================================
   HEADER
========================================================= */

.plan-section-header {
    background: #fff;
    border: 1px solid #e7e7e7;
    border-radius: 14px 14px 0 0;

    padding: 17px 21px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 15px;
}


.plan-section-title {
    display: flex;
    align-items: center;
    gap: 13px;
}


/* =========================================================
   MAIN ICON
========================================================= */

.plan-main-icon {
    width: 45px;
    height: 45px;

    border-radius: 11px;

    background: #f3f1ff;

    display: flex;
    align-items: center;
    justify-content: center;
}


.plan-main-icon i {
    font-size: 22px;
    color: #6d63c9;
}


.plan-main-icon.recent {
    background: #f4f4f4;
}


.plan-main-icon.recent i {
    color: #777;
}


.plan-section-title h4 {
    margin: 0 0 3px;

    font-size: 18px;
    font-weight: 600;

    color: #292929;
}


.plan-section-title span {
    display: block;

    font-size: 11px;

    color: #929292;
}


/* =========================================================
   STATUS
========================================================= */

.plan-status {
    display: inline-flex;
    align-items: center;

    gap: 6px;

    padding: 7px 14px;

    border-radius: 30px;

    font-size: 11px;
    font-weight: 600;

    white-space: nowrap;
}


.plan-status.active {
    color: #229653;
    background: #eaf8ef;
}


.plan-status.active i {
    font-size: 7px;
}


.recent-status {
    color: #777;
    background: #f3f3f3;
}


/* =========================================================
   PLAN CARD
========================================================= */

.plan-card {
    background: #fff;

    border: 1px solid #e7e7e7;
    border-top: 0;

    border-radius: 0 0 14px 14px;

    overflow: hidden;
}


.plan-card-body {
    padding: 20px;
}


/* =========================================================
   PLAN DETAIL BOX
========================================================= */

.plan-detail-box {
    min-height: 82px;

    padding: 12px 13px;

    background: #fff;

    border: 1px solid #e7e7e7;

    border-radius: 10px;

    display: flex;
    align-items: center;

    gap: 11px;

    transition: all .2s ease;
}


.plan-detail-box:hover {
    border-color: #d7d7d7;

    box-shadow: 0 4px 12px rgba(0, 0, 0, .04);

    transform: translateY(-1px);
}


/* =========================================================
   BOX ICON
========================================================= */

.plan-detail-icon {
    width: 40px;
    height: 40px;

    min-width: 40px;

    border-radius: 9px;

    background: #f4f2ff;

    display: flex;
    align-items: center;
    justify-content: center;
}


.plan-detail-icon i {
    font-size: 18px;

    color: #6d63c9;
}


/* Usage icon */

.plan-detail-box:has(.usage-text) .plan-detail-icon {
    background: #edf8f1;
}


.plan-detail-box:has(.usage-text) .plan-detail-icon i {
    color: #2a9d5b;
}


/* =========================================================
   DETAIL CONTENT
========================================================= */

.plan-detail-content {
    min-width: 0;
    flex: 1;
}


.plan-detail-label {
    font-size: 10px;

    color: #888;

    margin-bottom: 5px;

    font-weight: 500;

    line-height: 1.3;
}


.plan-detail-value {
    color: #292929;

    font-size: 13px;

    font-weight: 500;

    line-height: 1.4;

    word-break: break-word;
}


.plan-detail-value strong {
    font-size: 17px;

    font-weight: 700;

    color: #292929;
}


.usage-text {
    font-size: 10px;

    color: #888;

    margin-left: 2px;
}


.plan-detail-value > i {
    color: #777;

    margin-right: 4px;

    font-size: 12px;
}


/* =========================================================
   RECENT PLAN
========================================================= */

.recent-plan-card {
    background: #fff;

    border: 1px solid #e7e7e7;

    border-radius: 14px;

    overflow: hidden;
}


.recent-plan-header {
    padding: 15px 20px;

    border-bottom: 1px solid #eee;
}


.recent-plan-name {
    display: flex;

    align-items: center;

    gap: 11px;
}


.recent-plan-icon {
    width: 38px;
    height: 38px;

    border-radius: 9px;

    background: #f4f2ff;

    display: flex;
    align-items: center;
    justify-content: center;
}


.recent-plan-icon i {
    font-size: 17px;

    color: #6d63c9;
}


.recent-plan-header h5 {
    margin: 0 0 2px;

    font-size: 14px;

    font-weight: 600;

    color: #333;
}


.recent-plan-header span {
    font-size: 10px;

    color: #999;
}


/* =========================================================
   ADD ON SECTION
========================================================= */

.addon-section {
    margin-top: 23px;

    padding-top: 20px;

    border-top: 1px solid #eee;
}


.addon-heading {
    display: flex;

    align-items: center;

    gap: 10px;

    margin-bottom: 14px;
}


.addon-title-icon {
    width: 35px;
    height: 35px;

    border-radius: 8px;

    background: #f5f5f5;

    display: flex;
    align-items: center;
    justify-content: center;
}


.addon-title-icon i {
    font-size: 18px;

    color: #777;
}


.addon-heading h5 {
    margin: 0 0 2px;

    font-size: 14px;

    font-weight: 600;

    color: #333;
}


.addon-heading span {
    font-size: 10px;

    color: #999;
}


/* =========================================================
   ADD ON CARD
========================================================= */

.addon-card {
    min-height: 70px;

    padding: 11px;

    background: #fff;

    border: 1px solid #e8e8e8;

    border-radius: 9px;

    display: flex;

    align-items: center;

    gap: 10px;

    transition: all .2s ease;
}


.addon-card:hover {
    border-color: #d8d8d8;

    box-shadow: 0 3px 10px rgba(0, 0, 0, .04);
}


.addon-icon {
    width: 36px;
    height: 36px;

    min-width: 36px;

    border-radius: 8px;

    background: #f4f2ff;

    display: flex;

    align-items: center;
    justify-content: center;
}


.addon-icon i {
    font-size: 17px;

    color: #6d63c9;
}


.addon-content {
    min-width: 0;

    flex: 1;
}


.addon-label {
    display: block;

    font-size: 9px;

    color: #999;

    margin-bottom: 3px;
}


.addon-content strong {
    display: block;

    font-size: 12px;

    font-weight: 600;

    color: #333;

    word-break: break-word;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1199px) {

    .plan-detail-box {
        min-height: 78px;
    }

}


@media (max-width: 767px) {

    .plan-section-header {
        padding: 14px 15px;
    }


    .plan-section-title {
        gap: 9px;
    }


    .plan-main-icon {
        width: 40px;
        height: 40px;
    }


    .plan-main-icon i {
        font-size: 19px;
    }


    .plan-section-title h4 {
        font-size: 16px;
    }


    .plan-section-title span {
        font-size: 10px;
    }


    .plan-status {
        padding: 6px 10px;

        font-size: 10px;
    }


    .plan-card-body {
        padding: 15px;
    }


    .plan-detail-box {
        min-height: 74px;
    }

}


@media (max-width: 480px) {

    .plan-section-header {
        align-items: flex-start;
    }


    .plan-detail-icon {
        width: 36px;
        height: 36px;

        min-width: 36px;
    }


    .plan-detail-icon i {
        font-size: 16px;
    }


    .plan-detail-value {
        font-size: 12px;
    }


    .plan-detail-value strong {
        font-size: 15px;
    }


    .usage-text {
        font-size: 9px;
    }


    .recent-plan-header {
        padding: 13px 15px;
    }

}

</style>

@endsection
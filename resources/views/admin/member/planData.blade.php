<?php $assignType = $assignType ?? 'new_plan'; ?>

@if (!empty($currentPlanData))
    <div class="text-end mb-2">
        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
            data-bs-target="#currentPlanDetailModal">
            <i class='bx bx-info-circle'></i> View Current Plan Details
        </button>
    </div>
@endif

@if ($assignType === 'addon_only')
    <h4 class="plan_typesShow text-center">
        Add-On Assignment @if (isset($membershipPlanArr) && !empty($membershipPlanArr))
            <small class="d-block text-muted fs-14">on current plan: {{ $membershipPlanArr->plan_name }}</small>
        @endif
    </h4>
@elseif (isset($membershipPlanArr) && !empty($membershipPlanArr))
    <h4 class="plan_typesShow text-center">{{ $membershipPlanArr->plan_name }} </h4>
@endif

<div class="mobile_overflow">
    <div class="table_formate-deta">
        @if ($assignType !== 'addon_only' && isset($membershipPlanArr) && !empty($membershipPlanArr))
            <table class="w-100">
                <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
                <thead>
                    <tr>
                        <th class="fs-12" scope="col">Amount</th>
                        <th class="fs-12" scope="col">Currency</th>
                        <th class="fs-12" scope="col">Plan Discount</th>
                        <th class="fs-12" scope="col">Validity</th>
                        <th class="fs-12" scope="col">View Profile</th>
                        <th class="fs-12" scope="col">Interest</th>
                        <th class="fs-12" scope="col">Contact</th>
                        <th class="fs-12" scope="col">Video Call</th>
                        <th class="fs-12" scope="col">Voice Call</th>
                        <th class="fs-12" scope="col">Chat</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fs-12">{{ _displayNotAvailable(number_format($membershipPlanArr->plan_amount, 2)) }}
                        </td>
                        <td class="fs-12">{{ _displayNotAvailable($membershipPlanArr->currency_code) }}</td>
                        <td class="fs-12">{{ _displayNotAvailable($membershipPlanArr->plan_discount) }}%</td>
                        <td class="fs-12">{{ _displayNotAvailable($membershipPlanArr->validity_days) }} Days</td>
                        <td class="fs-12">{{ _displayNotAvailable($membershipPlanArr->view_profile_limit) }}</td>
                        <td class="fs-12">{{ _displayNotAvailable($membershipPlanArr->interests_limit) }}</td>
                        <td class="fs-12">{{ _displayNotAvailable($membershipPlanArr->contact_views_limit) }}</td>
                        <td class="fs-12">{{ _displayNotAvailable($membershipPlanArr->video_minutes_limit . ' Min') }}
                        </td>
                        <td class="fs-12">{{ _displayNotAvailable($membershipPlanArr->audio_minutes_limit . ' Min') }}
                        </td>
                        <td class="fs-12">{{ $membershipPlanArr->can_chat == 0 ? 'No' : 'Yes' }}</td>
                    </tr>
                </tbody>
            </table>
        @endif

        @if (isset($addOnPackageArr) && !blank($addOnPackageArr))
            <table class="w-100 mt-3">
                <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
                <thead>
                    <tr>
                        <th class="fs-12" scope="col">Add On Package Name</th>
                        <th class="fs-12" scope="col">Package Type</th>
                        <th class="fs-12" scope="col">Package Count</th>
                        <th class="fs-12" scope="col">Package Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($addOnPackageArr as $value)
                        <tr>
                            <td class="fs-12">{{ _displayNotAvailable($value->package_title) }}</td>
                            <td class="fs-12">{{ _displayNotAvailable($value->package_category) }}</td>
                            <td class="fs-12">{{ _displayNotAvailable($value->package_count) }}</td>
                            <td class="fs-12">{{ _displayNotAvailable(number_format($value->package_amount, 2)) }}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="fs-12" colspan="3"><strong>Add On Total</strong></td>
                        <td class="fs-12">
                            <strong>{{ number_format($paymentArr->addonAmount ?? 0, 2) }}
                                {{ $paymentArr->currency_code }}</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>
</div>

<div class="plans-bottomo-discountds mt-3">
    <div class="order-summary-box ms-auto">

        @if ($assignType !== 'addon_only')
            <div class="summary-row">
                <span class="summary-label">Plan Amount</span>
                <span class="summary-value">{{ number_format($paymentArr->planAmount ?? 0, 2) }}
                    {{ $paymentArr->currency_code }}</span>
            </div>

            @if (isset($paymentArr->planDiscount) && $paymentArr->planDiscount > 0)
                <div class="summary-row">
                    <span class="summary-label">Plan Discount Amount</span>
                    <span class="summary-value text-danger">
                        - {{ number_format($paymentArr->planDiscount, 2) }} {{ $paymentArr->currency_code }}</span>
                </div>
            @endif
        @endif

        @if (isset($addOnPackageArr) && !blank($addOnPackageArr))
            <div class="summary-row">
                <span class="summary-label">Add On Amount</span>
                <span class="summary-value">{{ number_format($paymentArr->addonAmount ?? 0, 2) }}
                    {{ $paymentArr->currency_code }}</span>
            </div>
        @endif

        @if (isset($siteConfigArr) && $siteConfigArr['tax_applicable'] == 'Yes')
            <div class="summary-row">
                <span class="summary-label">{{ $paymentArr->taxName ?? $siteConfigArr['tax_name'] }}
                    ({{ $paymentArr->taxPercentage ?? $siteConfigArr['service_tax'] }}%)</span>
                <span class="summary-value">{{ number_format($paymentArr->taxAmount, 2) }}
                    {{ $paymentArr->currency_code }}</span>
            </div>
        @endif

        <div class="summary-row summary-total">
            <span class="summary-label">Payable Amount</span>
            <span class="summary-value">{{ number_format($paymentArr->grand_total, 2) }}
                {{ $paymentArr->currency_code }}/-</span>
        </div>
    </div>
</div>

@if (!empty($currentPlanData))
    <!-- Current Plan Details Popup — lets the admin see the member's existing plan
         (usage, tax, previously approved add-ons) without leaving this page. -->
    <div class="modal fade" id="currentPlanDetailModal" tabindex="-1" aria-labelledby="currentPlanDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="currentPlanDetailModalLabel">Member's Current Plan Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @foreach ($currentPlanFieldsArr as $key => $planValue)
                            <div class="col-lg-4 col-md-6 col-6 mb-3">
                                <div class="detalistsleg">
                                    @if ($planValue['type'] == 'str')
                                        <span class="text-muted d-block fs-12">{{ $planValue['label'] }}</span>
                                        <h6 class="mb-0">{{ _displayNotAvailable($currentPlanData->$key) }}</h6>
                                    @elseif ($planValue['type'] == 'used')
                                        <?php $field = $planValue['field']; ?>
                                        <span class="text-muted d-block fs-12">{{ $planValue['label'] }}</span>
                                        <h6 class="mb-0">{{ $currentPlanData->$field }} out of {{ $currentPlanData->$key }}</h6>
                                    @elseif ($planValue['type'] == 'date')
                                        <span class="text-muted d-block fs-12">{{ $planValue['label'] }}</span>
                                        <h6 class="mb-0">{{ _displayDate($currentPlanData->$key, 'j F, Y') }}</h6>
                                    @elseif ($planValue['type'] == 'per')
                                        <span class="text-muted d-block fs-12">{{ $planValue['label'] }}</span>
                                        <h6 class="mb-0">{{ _displayNotAvailable($currentPlanData->$key) }}</h6>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if (isset($currentPlanAddOnArr) && !blank($currentPlanAddOnArr))
                        <hr>
                        <h6 class="mb-2">Approved Add-On Packages on this Plan</h6>
                        <div class="mobile_overflow">
                            <table class="w-100">
                                <thead>
                                    <tr>
                                        <th class="fs-12">Package</th>
                                        <th class="fs-12">Amount</th>
                                        <th class="fs-12">Status</th>
                                        <th class="fs-12">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- NOTE: field names below (package_title / amount / status / created_at) are
                                         based on your AddOnPayment usage elsewhere in the app — adjust to match
                                         your actual column names if they differ. --}}
                                    @foreach ($currentPlanAddOnArr as $addOn)
                                        <tr>
                                            <td class="fs-12">{{ _displayNotAvailable($addOn->package_title ?? '-') }}</td>
                                            <td class="fs-12">{{ _displayNotAvailable(number_format($addOn->amount ?? 0, 2)) }}</td>
                                            <td class="fs-12">{{ _displayNotAvailable($addOn->status ?? '-') }}</td>
                                            <td class="fs-12">{{ _displayDate($addOn->created_at, 'j F, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endif
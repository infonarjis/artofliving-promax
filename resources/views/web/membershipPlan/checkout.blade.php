@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    <!-- checkout section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="col-lg-9 col-xxl-8 mx-auto membership-plans-main">
                    <div class="row px-1">
                        <div class="col-lg-5 px-2">
                            <div class="single_meber-plans p-3">
                                <div class="plan-header-box">
                                    <div class="plan-title-contents">
                                        <p class="fts-22 fw-6 white-color-p">{{ $planDetail->plan_name }}</p>
                                        <h4 class="fts-15 fw-4 white-color-p opacity-75">
                                            {{ __('messages.lbl_for_individuals') }}</h4>
                                    </div>
                                    <div class="plan-pricing fts-28 white-color-p">
                                        @if (!empty($planDetail->plan_discount_amount) && $planDetail->plan_discount_amount > 0)
                                            <div class="plan-price-wrapper">
                                                {{-- Original Price --}}
                                                <div class="plan-original-price">
                                                    {{ $planDetail->currency_code }}
                                                    {{ number_format($planDetail->plan_amount, 0) }}
                                                </div>

                                                {{-- Final Price + Validity --}}
                                                <div class="plan-final-price">
                                                    <span class="plan-amount">
                                                        {{ $planDetail->currency_code }}
                                                        {{ number_format($planDetail->plan_discount_amount, 0) }}
                                                    </span>

                                                    <span class="plan-validity">
                                                        / {{ $planDetail->validity_days }}
                                                        {{ __('messages.lbl_days') }}
                                                    </span>
                                                </div>

                                            </div>
                                        @else
                                            <span
                                                class="fw-6">{{ $planDetail->currency_code . ' ' . $planDetail->plan_amount }}</span>
                                            <span class="fts-16 fw-4 white-color-p opacity-75">/
                                                {{ $planDetail->validity_days }}
                                                {{ __('messages.lbl_days') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="plans_bottomdivsvaf mt-4 px-1">
                                    <h4 class="white-color-n fw-6 fts-18">{{ __('messages.lbl_key_features') }}</h4>
                                    <ul class="listmatchis-items pt-1">
                                        <li class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                            @if ($planDetail->view_profile_limit > 0)
                                                <iconify-icon icon="material-symbols:check"
                                                    class="text-success fts-20"></iconify-icon>
                                            @else
                                                <iconify-icon icon="material-symbols:close"
                                                    class="text-danger fts-20"></iconify-icon>
                                            @endif
                                            {{ __('messages.lbl_allowed_viewed_profile') }} -
                                            {{ $planDetail->view_profile_limit }}
                                        </li>
                                        <li class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                            @if ($planDetail->interests_limit > 0)
                                                <iconify-icon icon="material-symbols:check"
                                                    class="text-success fts-20"></iconify-icon>
                                            @else
                                                <iconify-icon icon="material-symbols:close"
                                                    class="text-danger fts-20"></iconify-icon>
                                            @endif
                                            {{ __('messages.lbl_allowed_interests') }} -
                                            {{ $planDetail->interests_limit }}
                                        </li>
                                        <li class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                            @if ($planDetail->contact_views_limit > 0)
                                                <iconify-icon icon="material-symbols:check"
                                                    class="text-success fts-20"></iconify-icon>
                                            @else
                                                <iconify-icon icon="material-symbols:close"
                                                    class="text-danger fts-20"></iconify-icon>
                                            @endif
                                            {{ __('messages.lbl_allowed_contact_views') }} -
                                            {{ $planDetail->contact_views_limit }}
                                        </li>
                                        <li class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                            @if ($planDetail->can_chat)
                                                <iconify-icon icon="material-symbols:check"
                                                    class="text-success fts-20"></iconify-icon>
                                            @else
                                                <iconify-icon icon="material-symbols:close"
                                                    class="text-danger fts-20"></iconify-icon>
                                            @endif
                                            {{ __('messages.lbl_live_chat') }}
                                        </li>
                                        <li class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                            @if ($planDetail->audio_minutes_limit > 0)
                                                <iconify-icon icon="material-symbols:check"
                                                    class="text-success fts-20"></iconify-icon>
                                                {{ __('messages.lbl_audio_calls') }} -
                                                {{ $planDetail->audio_minutes_limit }} min
                                            @else
                                                <iconify-icon icon="material-symbols:close"
                                                    class="text-danger fts-20"></iconify-icon>
                                                {{ __('messages.lbl_audio_calls') }}
                                            @endif
                                        </li>
                                        <li class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                            @if ($planDetail->video_minutes_limit > 0)
                                                <iconify-icon icon="material-symbols:check"
                                                    class="text-success fts-20"></iconify-icon>
                                                {{ __('messages.lbl_video_calls') }} -
                                                {{ $planDetail->video_minutes_limit }} min
                                            @else
                                                <iconify-icon icon="material-symbols:close"
                                                    class="text-danger fts-20"></iconify-icon>
                                                {{ __('messages.lbl_video_calls') }}
                                            @endif
                                        </li>
                                        @if (_getConstant('AI_MODE') == 'Enabled')
                                            <li class="fts-15 fw-4 white-color70-n d-flex gap-1 mt-lg-2 mt-1">
                                                @if ($planDetail->ai_interest)
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
                                <ul class="mt-2 mt-lg-3">
                                    @php
                                        $planDesc = trim($planDetail->plan_description ?? '');
                                        $limit = 80;
                                    @endphp
                                    @if (strlen($planDesc) > $limit)
                                        <li class="plan-list-style fts-15 fw-4 white-color70-n read-more-box">
                                            <span class="short-text">
                                                {{ \Illuminate\Support\Str::limit($planDesc, $limit) }}
                                            </span>
                                            <span class="full-text d-none">{{ $planDesc }}</span>
                                            <span class="fw-6 primary-color-n read-toggle" style="cursor:pointer;">
                                                {{ __('messages.lbl_read_more') }}
                                            </span>
                                        </li>
                                    @else
                                        <li class="plan-list-style fts-15 fw-4 white-color70-n">
                                            {{ $planDesc ?? '' }}
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-7 px-2">
                            @if ($packages->isNotEmpty())
                                <div class="common-bgwhite-main p-3">
                                    <h3 class="fts-18 fw-7 white-color-n">{{ __('messages.lbl_add_on_package') }}</h3>
                                    <div class="add-ons-list pt-2">
                                        @foreach ($packages as $item)
                                            <div class="single_input_radio-L my-2 py-lg-1">
                                                <input type="checkbox" name="add_on_package_ids[]"
                                                    id="add_on_package_{{ $item->id }}" value="{{ $item->id }}"
                                                    data-title="{{ $item->package_title }}"
                                                    data-amount="{{ $item->package_amount }}"
                                                    data-currency="{{ $planDetail->currency_code }}"
                                                    class="d-none addon-checkbox">
                                                <label for="add_on_package_{{ $item->id }}"
                                                    class="radio_filterinp check fts-14 fw-4 white-color-n">
                                                    {{ $item->package_title }} <span
                                                        class="fw-7">({{ $planDetail->currency_code . ' ' . $item->package_amount }})</span></label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <div class="common-bgwhite-main p-3 mt-3">
                                <div class="plan_prisetextmng">
                                    <div class="plan_amout-middeed mt-md-4 mt-3">
                                        <div class="d-flex justify-content-between mb-lg-1 pb-2">
                                            <div class="fts-14 fw-5 white-color70-n">{{ __('messages.lbl_plan_amount') }}
                                            </div>
                                            <div class="fts-14 fw-5 white-color-n" id="planAmountText"></div>
                                        </div>
                                        @if ($planDetail->plan_discount > 0)
                                            <div class="d-flex justify-content-between mb-lg-1 pb-2">
                                                <div class="fts-14 fw-5 white-color70-n">
                                                    {{ __('messages.lbl_offer_amount') }}</div>
                                                <div class="fts-14 fw-5 white-color-n" id="planDiscountText"></div>
                                            </div>
                                        @endif

                                        {{-- If Addon then this div show  --}}
                                        <div id="selectedAddonsContainer"></div>

                                        @if (isset($configArr['tax_applicable']) && $configArr['tax_applicable'] == 'Yes' && $configArr['service_tax'] > 0)
                                            <div class="d-flex justify-content-between mb-lg-1 pb-2">
                                                <div class="fts-14 fw-5 white-color70-n">
                                                    {{ _getLang('lbl_sales_tax') }}({{ $configArr['service_tax'] }}%)
                                                </div>
                                                <div class="fts-14 fw-5 white-color-n" id="taxAmountText"></div>
                                            </div>
                                        @endif

                                        {{-- COUPON DISCOUNT ROW --}}
                                        <div class="d-flex justify-content-between mb-lg-1 pb-2 d-none" id="couponRow">
                                            {{-- <div class="fts-14 fw-5"> --}}
                                            <div class="fts-14 fw-5 white-color70-n">{{ __('messages.lbl_coupon_code') }}
                                            </div>
                                            {{-- </div> --}}
                                            <div class="fts-14 fw-6 text-success" id="couponAmountText"></div>
                                        </div>

                                        {{-- COUPON APPLIED BADGE --}}
                                        <div class="mt-2" id="couponAppliedBox" style="display:none;">
                                            <span class="badge bg-success-subtle text-success px-3 py-2">
                                                {{ __('messages.lbl_coupon_applied') }}:
                                                <strong id="appliedCouponCode"></strong>
                                                <a href="javascript:void(0)" id="removeCoupon"
                                                    class="ms-2 text-danger">×</a>
                                            </span>
                                        </div>

                                        <hr class="contact-ushorizont darkborder-color-L opacity-25 my-2">
                                        <div class="d-flex justify-content-between pt-2 pb-lg-3">
                                            <div class="fts-14 fw-5 white-color-n">{{ __('messages.lbl_total_amount') }}
                                            </div>
                                            <div class="fts-16 green-color-n fw-6" id="grandTotalText"></div>
                                        </div>
                                        <div class="checkout-coupon-box mb-lg-3 pb-1">
                                            <div class="fts-13 fw-4 white-color70-n mb-2">
                                                {{ __('messages.lbl_have_coupon') }}?</div>
                                            <form action=""
                                                class="footer-email checkout-coupon-form position-relative"
                                                method="get">
                                                <input type="text" name="coupon_code"
                                                    placeholder="{{ __('messages.lbl_coupon_code') }}" id="coupon_code">
                                                <button type="button" id="applyCoupon"
                                                    class="btn-apply-coupon fts-14">{{ __('messages.lbl_apply') }}</button>
                                            </form>
                                        </div>
                                        <input type="hidden" id="plan_id" value="{{ $planDetail->id }}">
                                        <form method="POST" action="{{ route('web.membership.pay') }}">
                                            @csrf
                                            <div class="d-flex gap-2">
                                                <input type="hidden" name="plan_id" id="final_plan_id"
                                                    value="{{ $planDetail->id }}">
                                                <input type="hidden" name="package_ids[]" id="package_ids_0">
                                                <input type="hidden" name="coupon_code" id="final_coupon">
                                                <input type="hidden" name="grand_total" id="grand_total">
                                                <button type="submit" class="comman-bg-btn bg-green fts-15 mt-2 w-100">
                                                    {{ __('messages.lbl_pay_now') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const currency = "{{ $planDetail->currency_code }}";

        function updatePackageInputs(packageIds) {
            // Remove existing hidden inputs
            $('input[name="package_ids[]"]').remove();
            packageIds.forEach(function(id) {
                $('#final_plan_id').after('<input type="hidden" name="package_ids[]" value="' + id + '">');
            });
        }

        function calculatePrice(showCouponMessage = false) {
            let packageIds = [];
            $('.addon-checkbox:checked').each(function() {
                packageIds.push($(this).val());
            });
            updatePackageInputs(packageIds);
            $.ajax({
                url: "{{ route('web.membershipPlan.calculatePrice') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    plan_id: $('#plan_id').val(),
                    package_ids: packageIds,
                    coupon_code: $('#coupon_code').val()
                },
                success: function(res) {
                    $('#planAmountText').text(
                        res.currency + ' ' + res.plan_amount
                    );
                    if (res.plan_discount > 0) {
                        $('#planDiscountText').text('- ' + res.currency + ' ' + res.plan_discount);
                        $('#planDiscountText').parent().show();
                    } else {
                        $('#planDiscountText').parent().hide();
                    }
                    $('#grandTotalText').text(res.currency + ' ' + res.grand_total);
                    $('#grand_total').val(res.grand_total);
                    $('#final_coupon').val(res.coupon_code);
                    if (res.tax_amount > 0) {
                        $('#taxAmountText').text(
                            '+ ' +
                            res.currency +
                            ' ' +
                            res.tax_amount
                        );
                    }

                    if (showCouponMessage) {
                        if (res.coupon_applied) {
                            showToastMessage('success', res.message);
                        } else {
                            showToastMessage('error', res.message);
                        }
                    }

                    if (res.coupon_applied) {
                        $('#couponRow').removeClass('d-none');
                        $('#couponAppliedBox').show();
                        $('#couponAmountText').text('- ' + res.currency + ' ' + res.coupon_discount);
                        $('#appliedCouponCode').text(res.coupon_code);
                        $('.checkout-coupon-box').addClass('d-none');
                    } else {
                        $('#couponRow').addClass('d-none');
                        $('#couponAppliedBox').hide();
                        $('.checkout-coupon-box').removeClass('d-none');
                    }
                },
                error: function(xhr) {
                    showToastMessage('error', xhr.responseJSON?.message ??
                        '{{ __('messages.msg_unexpected_error_occured') }}');
                }
            });
        }

        // ---------------- ADDON ROW RENDER ----------------
        function renderAddonRow(id, title, amount) {
            if ($('#addon_row_' + id).length) return;
            let html = `
            <div class="d-flex justify-content-between mb-lg-1 pb-2 addon-row" id="addon_row_${id}">
                <div class="fts-14 fw-5 white-color70-n">${title}
                <a href="javascript:void(0)" class="saleText-color-n remove-addon fts-14" data-id="${id}">{{ __('messages.lbl_remove') }}</a>
                <p class="fw-4 fts-11 gray3-color-L">({{ __('messages.lbl_addons') }})</p>
                </div>
                <div class="fts-14 fw-5 white-color-n">+ ${currency} ${amount}</div>
            </div>`;
            $('#selectedAddonsContainer').append(html);
        }

        // ---------------- REMOVE ADDON ROW ----------------
        function removeAddonRow(id) {
            $('#addon_row_' + id).remove();
        }

        // ---------------- EVENTS ----------------
        $(document).on('change', '.addon-checkbox', function() {
            let id = $(this).val();
            let title = $(this).data('title');
            let amount = $(this).data('amount');
            if ($(this).is(':checked')) {
                renderAddonRow(id, title, amount);
            } else {
                removeAddonRow(id);
            }
            calculatePrice();
        });

        // Remove from summary
        $(document).on('click', '.remove-addon', function() {
            let id = $(this).data('id');
            $('#add_on_package_' + id).prop('checked', false);
            removeAddonRow(id);
            calculatePrice();
        });

        // Apply coupon
        $('#applyCoupon').on('click', function() {
            let coupon = $('#coupon_code').val().trim();
            if (!coupon) {
                showToastMessage('error', '{{ __('messages.msg_please_enter_coupon_code') }}');
                return;
            }
            calculatePrice(true);
        });

        // Initial load
        $(document).ready(function() {
            calculatePrice();
        });

        // REMOVE COUPON
        $(document).on('click', '#removeCoupon', function() {
            $('#coupon_code').val('');
            $('#final_coupon').val('');
            $('#couponRow').addClass('d-none');
            $('#couponAppliedBox').hide();
            $('.checkout-coupon-box').removeClass('d-none');
            showToastMessage('info', '{{ __('messages.msg_coupon_has_removed') }}');
            calculatePrice();
        });
    </script>
@endpush

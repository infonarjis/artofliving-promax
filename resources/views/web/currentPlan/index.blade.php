@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <!-- current plan section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="dashboard-layout">
                    {{-- LEFT SIDEBAR (Responsive Off-Canvas Drawer on Mobile) --}}
                    @include('web.dashboard.memberLeftSideBar')

                    <main class="main-content-area" id="main-content">
                        @include('web.dashboard.memberTop')
                        <div class="common-bgwhite-main p-3 mt-3 px-lg-5">
                            <div class="right-plans-box">
                                <div class="current-plantitle">
                                    <div class="fts-20 fw-7 white-color-n">{{ __('messages.lbl_current_plan') }}</div>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#buyAddOnPlanModal"
                                        class="getstarted-btn-how ms-lg-auto m-auto m-lg-0 fts-14">
                                        <iconify-icon icon="ph:sparkle-bold"></iconify-icon>
                                        <span>{{ __('messages.msg_buy_add_on_package') }}</span>
                                    </a>
                                </div>
                                <div class="common-bglight-main p-3 p-lg-4 mt-2">
                                    <div class="plan-namesd d-flex align-items-center gap-3 pt-1">
                                        <div class="currant-plan-icon">
                                            <iconify-icon icon="ion:gift"></iconify-icon>
                                        </div>
                                        <div class="fts-18 fw-7 white-color-n">{{ $currentPlan->plan_name }}</div>
                                        @if ($currentPlan->is_renewal === 'Yes')
                                            <span class="badge bg-success ms-2">{{ __('messages.lbl_renewed') }}</span>
                                        @endif
                                    </div>
                                    <div class="row mt-3 pt-lg-1 ps-3 pe-3">
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6 mb-2 mb-lg-3 mb-md-3 p-0">
                                            <div class="plansingle-details">
                                                <div class="fts-14 fw-4 white-color70-n">
                                                    {{ __('messages.lbl_plan_duration') }}</div>
                                                <div class="fts-15 fw-5 white-color-n mt-1">
                                                    {{ $currentPlan->plan_validity_days + $currentPlan->addon_validity_days }}
                                                    {{ __('messages.lbl_days') }}
                                                    @if ($currentPlan->carried_forward_days > 0)
                                                        <span class="d-block fts-13 fw-4 saleText-color-n">
                                                            + {{ $currentPlan->carried_forward_days }}
                                                            {{ __('messages.lbl_days') }}
                                                            ({{ __('messages.lbl_carried_forward') }})
                                                            = {{ $currentPlan->total_validity_days }}
                                                            {{ __('messages.lbl_days') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6 mb-2 mb-lg-3 mb-md-3 p-0">
                                            <div class="plansingle-details">
                                                <div class="fts-14 fw-4 white-color70-n">
                                                    {{ __('messages.lbl_plan_activated_on') }}</div>
                                                <div class="fts-15 fw-5 white-color-n mt-1">
                                                    {{ _displayDate($currentPlan->plan_activate_date, 'j F, Y') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6 mb-2 mb-lg-3 mb-md-3 p-0">
                                            <div class="plansingle-details">
                                                <div class="fts-14 fw-4 white-color70-n">
                                                    {{ __('messages.lbl_plan_expired_on') }}</div>
                                                <div class="fts-15 fw-5 saleText-color-n mt-1">
                                                    {{ _displayDate($currentPlan->plan_expiry_date, 'j F, Y') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6 mb-2 mb-lg-3 mb-md-3 p-0">
                                            <div class="plansingle-details">
                                                <div class="fts-14 fw-4 white-color70-n">
                                                    {{ __('messages.lbl_allowed_viewed_profile') }} (
                                                    {{ __('messages.lbl_remaining') }} )
                                                </div>
                                                <div class="fts-15 fw-5 white-color-n mt-1">
                                                    {{ $currentPlan->view_profile_remaining }}
                                                    {{ __('messages.lbl_out_of') }} {{ $currentPlan->view_profile_total }}
                                                    @if ($currentPlan->carried_forward_view_profile > 0)
                                                        <span class="d-block fts-13 fw-4 saleText-color-n">
                                                            (+{{ $currentPlan->carried_forward_view_profile }}
                                                            {{ __('messages.lbl_carried_forward') }})
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6 mb-2 mb-lg-3 mb-md-3 p-0">
                                            <div class="plansingle-details">
                                                <div class="fts-14 fw-4 white-color70-n">
                                                    {{ __('messages.lbl_allowed_interests') }} (
                                                    {{ __('messages.lbl_remaining') }} )
                                                </div>
                                                <div class="fts-15 fw-5 white-color-n mt-1">
                                                    {{ $currentPlan->interests_remaining }}
                                                    {{ __('messages.lbl_out_of') }} {{ $currentPlan->interests_total }}
                                                    @if ($currentPlan->carried_forward_interest > 0)
                                                        <span class="d-block fts-13 fw-4 saleText-color-n">
                                                            (+{{ $currentPlan->carried_forward_interest }}
                                                            {{ __('messages.lbl_carried_forward') }})
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6 mb-2 mb-lg-3 mb-md-3 p-0">
                                            <div class="plansingle-details">
                                                <div class="fts-14 fw-4 white-color70-n">
                                                    {{ __('messages.lbl_allowed_contact_views') }} (
                                                    {{ __('messages.lbl_remaining') }} )
                                                </div>
                                                <div class="fts-15 fw-5 white-color-n mt-1">
                                                    {{ $currentPlan->contact_views_remaining }}
                                                    {{ __('messages.lbl_out_of') }}
                                                    {{ $currentPlan->contact_views_total }}
                                                    @if ($currentPlan->carried_forward_contact_views > 0)
                                                        <span class="d-block fts-13 fw-4 saleText-color-n">
                                                            (+{{ $currentPlan->carried_forward_contact_views }}
                                                            {{ __('messages.lbl_carried_forward') }})
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6 mb-2 mb-lg-3 mb-md-3 p-0">
                                            <div class="plansingle-details">
                                                <div class="fts-14 fw-4 white-color70-n">
                                                    {{ __('messages.lbl_allowed_chat') }}</div>
                                                <div class="fts-15 fw-5 white-color-n mt-1">
                                                    {{ $currentPlan->can_chat ? __('messages.lbl_yes') : __('messages.lbl_no') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6 mb-2 mb-lg-3 mb-md-3 p-0">
                                            <div class="plansingle-details">
                                                <div class="fts-14 fw-4 white-color70-n">
                                                    {{ __('messages.lbl_audio_calls') }} (
                                                    {{ __('messages.lbl_min_remaining') }} )</div>
                                                <div class="fts-15 fw-5 white-color-n mt-1">
                                                    {{ $currentPlan->audio_minutes_remaining }}
                                                    {{ __('messages.lbl_out_of') }}
                                                    {{ $currentPlan->audio_minutes_total }}
                                                    @if ($currentPlan->carried_forward_audio_minutes > 0)
                                                        <span class="d-block fts-13 fw-4 saleText-color-n">
                                                            (+{{ $currentPlan->carried_forward_audio_minutes }}
                                                            {{ __('messages.lbl_carried_forward') }})
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 col-6 mb-2 mb-lg-3 mb-md-3 p-0">
                                            <div class="plansingle-details">
                                                <div class="fts-14 fw-4 white-color70-n">
                                                    {{ __('messages.lbl_video_calls') }} (
                                                    {{ __('messages.lbl_min_remaining') }} )</div>
                                                <div class="fts-15 fw-5 white-color-n mt-1">
                                                    {{ $currentPlan->video_minutes_remaining }}
                                                    {{ __('messages.lbl_out_of') }}
                                                    {{ $currentPlan->video_minutes_total }}
                                                    @if ($currentPlan->carried_forward_video_minutes > 0)
                                                        <span class="d-block fts-13 fw-4 saleText-color-n">
                                                            (+{{ $currentPlan->carried_forward_video_minutes }}
                                                            {{ __('messages.lbl_carried_forward') }})
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @if (_getConstant('AI_MODE') == 'Enabled')
                                            <div class="col-lg-4 col-md-6 col-sm-6 col-6 mb-2 mb-lg-3 mb-md-3 p-0">
                                                <div class="plansingle-details">
                                                    <div class="fts-14 fw-4 white-color70-n">
                                                        {{ __('messages.lbl_send_auto_ai_interest') }}</div>
                                                    <div class="fts-15 fw-5 white-color-n mt-1">
                                                        {{ $currentPlan->ai_interest ? __('messages.lbl_yes') : __('messages.lbl_no') }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-lg-12 text-lg-end text-md-end text-center p-0">
                                            <a href="{{ route('web.membershipPlan.index') }}"
                                                class="getstarted-btn-how ms-lg-auto m-auto m-lg-0 fts-14">
                                                {{ __('messages.lbl_upgrade_now') }}
                                            </a>
                                        </div>
                                        <div class="col-lg-12 mt-3 p-0">
                                            <div class="plan_paydetails d-block d-lg-flex p-lg-1 p-4">
                                                @if ($currentPlan->tax_amount > 0)
                                                    <div class="lx-setdeea d-flex align-items-center ps-lg-4 w-100">
                                                        <div class="fts-14 fw-4 white-color70-n w-100">
                                                            {{ $currentPlan->tax_name }}
                                                            <span>({{ $currentPlan->tax_percentage }}%)</span>
                                                        </div>
                                                        <div class="fts-16 white-color-n fw-7 w-100">
                                                            {{ $currentPlan->currency_code }}
                                                            <span>{{ $currentPlan->tax_amount }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="lx-setdeea d-flex align-items-center w-100">
                                                    <div class="fts-14 fw-4 white-color70-n w-100">
                                                        {{ __('messages.lbl_grand_total') }}</div>
                                                    <div class="fts-16 fw-7 green-color-n w-100">
                                                        {{ $currentPlan->currency_code }}
                                                        <span>{{ $currentPlan->grand_total }}</span>
                                                    </div>
                                                </div>
                                                <div class="lx-setdeea text-end mt-3 mt-lg-0">
                                                    <a href="{{ route('web.currentPlan.viewInvoice', $currentPlan->id) }}"
                                                        class="view-btn-invoice">
                                                        {{ __('messages.lbl_view_invoice') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($addOnPlan->isNotEmpty())
                                <div class="right-plans-box mt-3 mt-lg-4">
                                    <div class="current-plantitle">
                                        <div class="fts-18 fw-7 white-color-n">{{ __('messages.lbl_add_on_package') }}
                                        </div>
                                    </div>
                                    <div class="common-bglight-main p-3 p-lg-4 mt-2">
                                        <div class="plan-history-list">
                                            @foreach ($addOnPlan as $item)
                                                <div class="single-addon-box py-3">
                                                    <div class="plan-namesd d-flex align-items-center gap-2">
                                                        <div class="history-plan-icon">
                                                            <iconify-icon icon="ion:gift"></iconify-icon>
                                                        </div>
                                                        <div class="fts-14 fw-5 white-color70-n">{{ $item->package_title }}
                                                        </div>
                                                    </div>
                                                    <div class="row pt-1">
                                                        <div class="col-lg-4 col-6 mt-2">
                                                            <div class="plansingle-details">
                                                                <div class="fts-14 fw-4 white-color70-n">
                                                                    {{ __('messages.lbl_package_amount') }}</div>
                                                                <div class="fts-15 fw-5 white-color-n">
                                                                    {{ $item->package_amount }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-6 mt-2">
                                                            <div class="plansingle-details">
                                                                <div class="fts-14 fw-4 white-color70-n">
                                                                    {{ $item->package_category }}</div>
                                                                <div class="fts-15 fw-5 white-color-n">
                                                                    {{ $item->package_count }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 mt-2">
                                                            <div class="plansingle-details">
                                                                <div class="fts-14 fw-4 white-color70-n">
                                                                    {{ __('messages.field_description') }}</div>
                                                                <div class="fts-15 fw-5 white-color-n mt-1">
                                                                    {{ $item->description }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (!empty($currentPlan->previousPayment) || $currentPlan->is_renewal === 'Yes')
                                <div class="right-plans-box mt-3 mt-lg-4">
                                    <div class="current-plantitle">
                                        <div class="fts-18 fw-7 white-color-n">{{ __('messages.lbl_upgrade_details') }}
                                        </div>
                                    </div>
                                    <div class="common-bglight-main p-3 p-lg-4 mt-2">
                                        <div class="row">
                                            <div class="col-lg-6 col-6 mb-2">
                                                <div class="plansingle-details">
                                                    <div class="fts-14 fw-4 white-color70-n">
                                                        {{ __('messages.lbl_previous_plan') }}</div>
                                                    <div class="fts-15 fw-5 white-color-n mt-1">
                                                        {{ $currentPlan->previousPayment->plan_name ?? '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-6 mb-2">
                                                <div class="plansingle-details">
                                                    <div class="fts-14 fw-4 white-color70-n">
                                                        {{ __('messages.lbl_upgraded_on') }}</div>
                                                    <div class="fts-15 fw-5 white-color-n mt-1">
                                                        {{ _displayDate($currentPlan->created_at, 'j F, Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($planHistory->isNotEmpty())
                                <div class="right-plans-box mt-3 mt-lg-4">
                                    <div class="current-plantitle">
                                        <div class="fts-18 fw-7 white-color-n">{{ __('messages.lbl_plan_history') }}</div>
                                    </div>
                                    <div class="common-bglight-main p-3 p-lg-4 mt-2" id="plan-history-table">
                                        {{-- @include(_getConstant('dir_path.WEB_DIR_PATH').'.currentPlan.ajax_plan_history', ['planHistory' => $planHistory]) --}}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </section>

    @include('web.currentPlan.buy_add_on_plan')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            loadHistory('{{ route('web.currentPlan.history') }}');
        });

        $(document).on('click', '.pagination_nav a', function(e) {
            e.preventDefault();
            loadHistory($(this).attr('href'));
        });

        function loadHistory(url) {
            $.ajax({
                url: url,
                type: 'GET',
                beforeSend: function() {
                    $('#plan-history-table').html(
                        '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>'
                    );
                },
                success: function(response) {
                    $('#plan-history-table').html(response);
                }
            });
        }
    </script>
@endpush

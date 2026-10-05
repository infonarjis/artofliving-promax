@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <!-- Main Content section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="dashboard-layout">
                    {{-- LEFT SIDEBAR (Responsive Off-Canvas Drawer on Mobile) --}}
                    @include('web.dashboard.memberLeftSideBar')

                    <main class="main-content-area">

                        <div class="ai-page-header mb-4">
                            <h1 class="fts-28 fw-7 white-color-n mb-2">
                                {{ __('messages.lbl_auto_send_interest_ai_powered_title') }}</h1>
                            <p class="fts-15 fw-4 white-color70-n">
                                {{ __('messages.lbl_auto_send_interest_ai_powered_description') }}</p>
                        </div>

                        <!-- 1. Enable Auto Mode Section -->
                        <div class="common-bgwhite-main p-4 mb-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="fts-18 fw-6 white-color-n mb-1">{{ __('messages.lbl_enable_auto_mode') }}
                                    </h4>
                                    <p class="fts-14 fw-4 white-color70-n mb-0">
                                        {{ __('messages.lbl_when_enabled_ai_will_automatically_select_and_send_interest_to_top_matches_daily') }}
                                    </p>
                                </div>
                                <label class="ai-toggle-switch">
                                    <input type="checkbox" id="autoModeToggle"
                                        {{ $authUser->auto_interest_enabled ? 'checked' : '' }}>
                                    <span class="ai-toggle-slider"></span>
                                </label>
                            </div>
                            <p class="fts-12 fw-4 primary-color-n mt-3 mb-0">
                                <iconify-icon icon="solar:info-circle-linear" class="me-1"></iconify-icon>
                                {{ __('messages.lbl_this_switch_activates_deactivates_the_entire_auto_send_feature') }}
                            </p>
                        </div>

                        <!-- Daily Interest Limit & Compatibility Section -->
                        <div class="common-bgwhite-main p-4 mb-4">
                            <h4 class="fts-18 fw-6 white-color-n mb-4">
                                {{ __('messages.lbl_daily_interest_limit_compatibility') }}</h4>
                            <div class="row">
                                <div class="col-md-6 mb-4 mb-md-0">
                                    <div class="edit_inputMain-sltr w-100">
                                        <label
                                            class="fts-14 fw-5 white-color-n mb-2 d-block">{{ __('messages.lbl_daily_interest_limit') }}</label>
                                        <select name="interestLimit" id="interestLimit" class="Single_searchDv w-100">
                                            <option value="1"
                                                {{ $authUser->daily_interest_limit == 1 ? 'selected' : '' }}>1
                                                {{ __('messages.lbl_per_day') }}</option>
                                            <option value="2"
                                                {{ $authUser->daily_interest_limit == 2 ? 'selected' : '' }}>2
                                                {{ __('messages.lbl_per_day') }}</option>
                                            <option value="3"
                                                {{ $authUser->daily_interest_limit == 3 ? 'selected' : '' }}>3
                                                {{ __('messages.lbl_per_day') }}</option>
                                            <option value="4"
                                                {{ $authUser->daily_interest_limit == 4 ? 'selected' : '' }}>4
                                                {{ __('messages.lbl_per_day') }}</option>
                                            <option value="5"
                                                {{ $authUser->daily_interest_limit == 5 ? 'selected' : '' }}>5
                                                {{ __('messages.lbl_per_day') }}</option>
                                        </select>
                                        <p class="fts-12 fw-4 white-color70-n mt-2 mb-0">
                                            {{ __('messages.lbl_ai_will_never_exceed_this_limit') }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="fts-14 fw-5 white-color-n mb-2 d-flex justify-content-between">
                                        <span>{{ __('messages.lbl_minimum_compatibility_threshold') }}</span>
                                        <span class="primary-color-n"
                                            id="rangeValue">{{ $authUser->min_match_percentage ?? 60 }}%</span>
                                    </label>
                                    <input type="range" class="ai-range-slider" min="0" max="100"
                                        value="{{ $authUser->min_match_percentage ?? 60 }}" id="compatibilityRange">
                                    <div class="d-flex justify-content-between fts-12 white-color70-n">
                                        <span>0%</span>
                                        <span>100%</span>
                                    </div>
                                    <p class="fts-12 fw-4 white-color70-n mt-2 mb-0">
                                        {{ __('messages.lbl_ai_will_show_only_profiles_above_this_compatibility_score') }}
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" id="saveAiSettingsBtn"
                                    class="comman-bg-btn fts-15 fw-6 px-5 py-3 align-items-center">
                                    {{ __('messages.lbl_save_settings') }}
                                </button>
                            </div>
                        </div>

                        @if (!@empty($aiMatchProfile))
                            <div class="common-bgwhite-main p-4 mb-4">
                                <h4 class="fts-18 fw-6 white-color-n mb-3">
                                    {{ __('messages.lbl_today_ai_selected_top_matches') }}</h4>
                                <div class="d-flex gap-3 overflow-auto pb-2 custom-scrollbar ai-scroll">
                                    @foreach ($aiMatchProfile as $item)
                                        @php
                                            $canView = _canViewMemberPhoto(
                                                $item->matchedMember,
                                                $item->hasPhotoRequestAccess,
                                            );
                                            $hasPhoto = _checkPhotoExist($item->matchedMember);
                                            $profileImage = _getMemberProfileImage($item->matchedMember);
                                            if (!$canView && $hasPhoto) {
                                                $profileImage = _getProtectedImage($item->matchedMember->gender);
                                            }
                                        @endphp
                                        <div class="ai-match-card">
                                            <a
                                                href="{{ route('web.userProfile.index', _encrypt($item->matchedMember->id)) }}">
                                                <div class="ai-match-img">
                                                    <img src="{{ $profileImage }}"
                                                        alt="{{ _profileTitle($item->matchedMember) }}">
                                                </div>
                                            </a>
                                            <h5 class="fts-15 fw-6 white-color-n mb-1">
                                                {{ _profileTitle($item->matchedMember) }}</h5>
                                            <p class="fts-12 fw-4 white-color70-n mb-0">
                                                {{ _getMemberAgeHeight($item->matchedMember) }}</p>
                                            <div class="d-flex justify-content-center align-items-center mt-1">
                                                @if ($item->queue_status == 1)
                                                    <div
                                                        class="status-profils accepted fts-11 d-flex align-items-center gap-1">
                                                        <iconify-icon icon="mingcute:send-line"
                                                            class=""></iconify-icon>
                                                        {{ __('messages.lbl_ai_sent_today') }}
                                                    </div>
                                                @else
                                                    <div class="status-profils fts-11 d-flex align-items-center gap-1">
                                                        <iconify-icon icon="material-symbols:schedule"
                                                            class=""></iconify-icon>
                                                        {{ __('messages.lbl_ai_scheduled_today') }}
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                                <p class="fts-12 fw-4 primary-color-n mt-3 mb-0">
                                    <iconify-icon icon="solar:bolt-bold" class="me-1"></iconify-icon>
                                    {{ __('messages.lbl_ai_has_sent_some_interests_already_the_remaining_will_be_sent_today') }}
                                </p>
                            </div>
                        @endif

                        <!-- AI Rules Before Sending Interest Section -->
                        <div class="common-bgwhite-main p-4 mb-4">
                            <h4 class="fts-18 fw-6 white-color-n mb-3 d-flex gap-1 align-items-center">
                                <iconify-icon icon="material-symbols:info-outline-rounded"
                                    class="primary-color-n"></iconify-icon>
                                {{ __('messages.lbl_ai_rules_before_sending_interest') }}
                            </h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="ai-rule-item">
                                        <span class="ai-rule-icon"><iconify-icon
                                                icon="solar:check-circle-bold"></iconify-icon></span>
                                        <span
                                            class="fts-14 fw-5 white-color-n">{{ __('messages.lbl_not_already_sent_interest') }}</span>
                                    </div>
                                    <div class="ai-rule-item">
                                        <span class="ai-rule-icon"><iconify-icon
                                                icon="solar:check-circle-bold"></iconify-icon></span>
                                        <span
                                            class="fts-14 fw-5 white-color-n">{{ __('messages.lbl_profile_not_blocked') }}</span>
                                    </div>
                                    <div class="ai-rule-item">
                                        <span class="ai-rule-icon">
                                            <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                                        </span>
                                        <span
                                            class="fts-14 fw-5 white-color-n">{{ __('messages.lbl_matches_your_preferences') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ai-rule-item">
                                        <span class="ai-rule-icon"><iconify-icon
                                                icon="solar:check-circle-bold"></iconify-icon></span>
                                        <span
                                            class="fts-14 fw-5 white-color-n">{{ __('messages.lbl_within_your_daily_limit') }}</span>
                                    </div>
                                    <div class="ai-rule-item">
                                        <span class="ai-rule-icon"><iconify-icon
                                                icon="solar:check-circle-bold"></iconify-icon></span>
                                        <span
                                            class="fts-14 fw-5 white-color-n">{{ __('messages.lbl_respects_your_plan_limits') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#autoModeToggle').on('change', function() {
                let autoMode = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: "{{ route('web.aiAutoInterest.toggle') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        auto_interest_enabled: autoMode
                    },
                    beforeSend: function() {
                        $('#autoModeToggle').prop('disabled', true);
                    },
                    success: function(response) {
                        showToastMessage(
                            response.status ? 'success' : 'error',
                            response.message
                        );
                    },
                    complete: function() {
                        $('#autoModeToggle').prop('disabled', false);
                    }
                });
            });

            $('#saveAiSettingsBtn').on('click', function(e) {
                e.preventDefault();

                let $btn = $(this);
                $btn.prop('disabled', true).html(
                    '<iconify-icon icon="codex:loader" class="fts-28"></iconify-icon> {{ __('messages.lbl_saving') }}'
                );

                $.ajax({
                    url: "{{ route('web.aiAutoInterest.updateSettings') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        daily_interest_limit: $('#interestLimit').val(),
                        min_match_percentage: $('#compatibilityRange').val(),
                    },
                    success: function(response) {
                        showToastMessage(
                            response.status ? 'success' : 'error', response.message
                        );
                    },
                    error: function() {
                        showToastMessage('error',
                            '{{ __('messages.msg_unexpected_error_occured') }}');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(
                            '{{ __('messages.lbl_save_settings') }}');
                    }
                });
            });
        });
    </script>
@endpush

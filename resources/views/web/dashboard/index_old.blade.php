@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <!-- dashboard section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">

                {{-- Suspicious Activity --}}
                @include('web.suspiciousActivity.warningMsg')
                {{-- Suspicious Activity --}}

                <div class="row">
                    @include('web.dashboard.memberLeftSideBar')
                    <div class="col-lg-9 col-12">
                        @include('web.dashboard.memberTop')
                        <div class="ai-banners-wrap">
                            @if($configArr['gemini_api_status'] == 'APPROVED')
                                {{-- AI Match Making Banner --}}
                                <div class="ai-card ai-card-matchmaking">
                                    <div class="ai-card-glow"></div>
                                    <div class="ai-icon-wrap">
                                        <iconify-icon icon="solar:magic-stick-3-bold-duotone"></iconify-icon>
                                    </div>
                                    <div class="ai-content">
                                        <div class="ai-label">
                                            <iconify-icon icon="solar:cpu-bolt-bold-duotone"></iconify-icon>
                                            {{ __('messages.lbl_ai_powered') }}
                                        </div>
                                        <h3 class="ai-title">{{ __('messages.lbl_ai_selected_matches_for_you') }}</h3>
                                        <p class="ai-desc">{{ __('messages.lbl_ai_selected_matches_for_you_description') }}</p>
                                    </div>
                                    <a href="{{ route('web.aiMatchMaking.index') }}" class="ai-cta ai-cta-primary">
                                        <iconify-icon icon="solar:magic-stick-3-bold-duotone"  class="fts-20"></iconify-icon>
                                        {{ __('messages.lbl_match_with_ai') }}
                                    </a>
                                </div>
                                
                                {{-- Auto Send Interest Banner --}}
                                <div class="ai-card ai-card-autointerest">
                                    <div class="ai-card-glow"></div>
                                    <div class="ai-icon-wrap">
                                        <iconify-icon icon="clarity:users-line"></iconify-icon>
                                    </div>
                                    <div class="ai-content">
                                        <div class="ai-label">
                                            <iconify-icon icon="solar:bolt-bold-duotone"></iconify-icon>
                                            {{ __('messages.lbl_auto_send') }}
                                        </div>
                                        <h3 class="ai-title">{{ __('messages.lbl_auto_send_interest_ai_powered_title') }}</h3>
                                        <p class="ai-desc">{{ __('messages.lbl_auto_send_interest_ai_powered_description') }}
                                        </p>
                                    </div>
                                    <a href="{{ route('web.aiAutoInterest.index') }}" class="ai-cta ai-cta-secondary">
                                        <iconify-icon icon="mingcute:send-line" class="fts-20"></iconify-icon>
                                        {{ __('messages.lbl_ai_auto_send_interest') }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        @if ($premiumMatchesMember->isNotEmpty())
                            <div class="common-dashboard-profiles mt-3 mt-lg-4 pt-1">
                                <div class="d-flex justify-content-between align-items-end gap-2">
                                    <div class="section_title ms-2">
                                        <h2 class="fts-20 fw-6 white-color-n">{{ __('messages.lbl_premium_members') }}</h2>
                                        <p class="fts-14 fw-4 white-color70-n">
                                            {{ __('messages.lbl_premium_members_msg') }}
                                        </p>
                                    </div>
                                    <a href="" class="primary-color-n fw-5 fts-13 text-nowrap"></a>
                                </div>
                                <div class="dashboard-profile-slider mt-1 mt-lg-2">
                                    @foreach ($premiumMatchesMember as $item)
                                        <div class="dashboard-single-profile p-2 mx-1">
                                            <div class="profile-top-dashboard">
                                                @php
                                                    $canView = _canViewMemberPhoto($item, $item->hasPhotoRequestAccess);
                                                    $hasPhoto = _checkPhotoExist($item);
                                                    $profileImage = _getMemberProfileImage($item);

                                                    ## Online Status:
                                                    $onlineStatus = _memberOnlineStatus($item);
                                                @endphp
                                                @if ($onlineStatus['status_code'] == 'online')
                                                    <div class="online-blinking-dot"></div>
                                                @endif
                                                <p class="match-percent-badge-dash">{{ $item->matchPercent }}%
                                                    {{ __('messages.lbl_match') }}</p>
                                                <a href="{{ route('web.userProfile.index', _encrypt($item->id)) }}">
                                                    @if (!$canView && $hasPhoto)
                                                        <img src="{{ _getProtectedImage($item->gender) }}"
                                                            alt="{{ _profileTitle($item) }}" class="dashboard-profile">
                                                    @else
                                                        <img src="{{ $profileImage }}" alt="{{ _profileTitle($item) }}"
                                                            class="dashboard-profile">
                                                    @endif
                                                </a>
                                                @if ($item->plan_status == 'Paid')
                                                    <div class="premium-icon-dash"><iconify-icon
                                                            icon="solar:crown-bold"></iconify-icon></div>
                                                @endif
                                            </div>
                                            <div class="bottom-dashboar-content text-center py-2">
                                                <h4 class="fts-16 fw-6 white-color-n mt-2">{{ _profileTitle($item) }}</h4>
                                                <p class="fts-14 fw-4 white-color70-n mt-1">{{ _profileSubTitle($item) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if ($recommedMatchesMember->isNotEmpty())
                            <div class="common-dashboard-profiles mt-3 mt-lg-4 pt-1">
                                <div class="d-flex justify-content-between align-items-end gap-2">
                                    <div class="section_title ms-2">
                                        <h2 class="fts-20 fw-6 white-color-n">{{ __('messages.lbl_recommended_matches') }}
                                        </h2>
                                        <p class="fts-14 fw-4 white-color70-n">
                                            {{ __('messages.lbl_recommended_matches_msg') }}</p>
                                    </div>
                                    <a href="" class="primary-color-n fw-5 fts-13 text-nowrap"></a>
                                </div>
                                <div class="dashboard-profile-slider mt-1 mt-lg-2">
                                    @foreach ($recommedMatchesMember as $item)
                                        <div class="dashboard-single-profile p-2 mx-1">
                                            <div class="profile-top-dashboard">
                                                @php
                                                    $canView = _canViewMemberPhoto($item, $item->hasPhotoRequestAccess);
                                                    $hasPhoto = _checkPhotoExist($item);
                                                    $profileImage = _getMemberProfileImage($item);
                                                    ## Online Status:
                                                    $onlineStatus = _memberOnlineStatus($item);
                                                @endphp
                                                @if ($onlineStatus['status_code'] == 'online')
                                                    <div class="online-blinking-dot"></div>
                                                @endif
                                                <p class="match-percent-badge-dash">{{ $item->matchPercent }}%
                                                    {{ __('messages.lbl_match') }}</p>
                                                <a href="{{ route('web.userProfile.index', _encrypt($item->id)) }}">
                                                    @if (!$canView && $hasPhoto)
                                                        <img src="{{ _getProtectedImage($item->gender) }}"
                                                            alt="{{ _profileTitle($item) }}" class="dashboard-profile">
                                                    @else
                                                        <img src="{{ $profileImage }}" alt="{{ _profileTitle($item) }}"
                                                            class="dashboard-profile">
                                                    @endif
                                                </a>
                                                @if ($item->plan_status == 'Paid')
                                                    <div class="premium-icon-dash"><iconify-icon
                                                            icon="solar:crown-bold"></iconify-icon></div>
                                                @endif
                                            </div>
                                            <div class="bottom-dashboar-content text-center py-2">
                                                <h4 class="fts-16 fw-6 white-color-n mt-2">{{ _profileTitle($item) }}</h4>
                                                <p class="fts-14 fw-4 white-color70-n mt-1">{{ _profileSubTitle($item) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if ($recentlyJoinedMember->isNotEmpty())
                            <div class="common-dashboard-profiles mt-3 mt-lg-4 pt-1">
                                <div class="d-flex justify-content-between align-items-end gap-2">
                                    <div class="section_title ms-2">
                                        <h2 class="fts-20 fw-6 white-color-n">
                                            {{ __('messages.lbl_recently_joined_members') }}</h2>
                                        <p class="fts-14 fw-4 white-color70-n">
                                            {{ __('messages.lbl_recently_joined_members_msg') }}</p>
                                    </div>
                                    <a href="" class="primary-color-n fw-5 fts-13 text-nowrap"></a>
                                </div>
                                <div class="dashboard-profile-slider mt-1 mt-lg-2">
                                    @foreach ($recentlyJoinedMember as $item)
                                        <div class="dashboard-single-profile p-2 mx-1">
                                            <div class="profile-top-dashboard">
                                                @php
                                                    $canView = _canViewMemberPhoto($item, $item->hasPhotoRequestAccess);
                                                    $hasPhoto = _checkPhotoExist($item);
                                                    $profileImage = _getMemberProfileImage($item);
                                                    ## Online Status:
                                                    $onlineStatus = _memberOnlineStatus($item);
                                                @endphp
                                                @if ($onlineStatus['status_code'] == 'online')
                                                    <div class="online-blinking-dot"></div>
                                                @endif
                                                <p class="match-percent-badge-dash">{{ $item->matchPercent }}%
                                                    {{ __('messages.lbl_match') }}</p>
                                                <a href="{{ route('web.userProfile.index', _encrypt($item->id)) }}">
                                                    @if (!$canView && $hasPhoto)
                                                        <img src="{{ _getProtectedImage($item->gender) }}"
                                                            alt="{{ _profileTitle($item) }}" class="dashboard-profile">
                                                    @else
                                                        <img src="{{ $profileImage }}" alt="{{ _profileTitle($item) }}"
                                                            class="dashboard-profile">
                                                    @endif
                                                </a>
                                                @if ($item->plan_status == 'Paid')
                                                    <div class="premium-icon-dash"><iconify-icon
                                                            icon="solar:crown-bold"></iconify-icon></div>
                                                @endif
                                            </div>
                                            <div class="bottom-dashboar-content text-center py-2">
                                                <h4 class="fts-16 fw-6 white-color-n mt-2">{{ _profileTitle($item) }}</h4>
                                                <p class="fts-14 fw-4 white-color70-n mt-1">{{ _profileSubTitle($item) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if ($recentlyLoginMember->isNotEmpty())
                            <div class="common-dashboard-profiles mt-3 mt-lg-4 pt-1">
                                <div class="d-flex justify-content-between align-items-end gap-2">
                                    <div class="section_title ms-2">

                                        <h2 class="fts-20 fw-6 white-color-n">
                                            {{ __('messages.lbl_recently_active_members') }}</h2>
                                        <p class="fts-14 fw-4 white-color70-n">
                                            {{ __('messages.lbl_recently_active_members_msg') }}</p>
                                    </div>
                                    <a href="" class="primary-color-n fw-5 fts-13 text-nowrap"></a>
                                </div>
                                <div class="dashboard-profile-slider mt-1 mt-lg-2">
                                    @foreach ($recentlyLoginMember as $item)
                                        <div class="dashboard-single-profile p-2 mx-1">
                                            <div class="profile-top-dashboard">
                                                @php
                                                    $canView = _canViewMemberPhoto($item, $item->hasPhotoRequestAccess);
                                                    $hasPhoto = _checkPhotoExist($item);
                                                    $profileImage = _getMemberProfileImage($item);
                                                    ## Online Status:
                                                    $onlineStatus = _memberOnlineStatus($item);
                                                @endphp
                                                @if ($onlineStatus['status_code'] == 'online')
                                                    <div class="online-blinking-dot"></div>
                                                @endif
                                                <p class="match-percent-badge-dash">{{ $item->matchPercent }}%
                                                    {{ __('messages.lbl_match') }}</p>
                                                <a href="{{ route('web.userProfile.index', _encrypt($item->id)) }}">
                                                    @if (!$canView && $hasPhoto)
                                                        <img src="{{ _getProtectedImage($item->gender) }}"
                                                            alt="{{ _profileTitle($item) }}" class="dashboard-profile">
                                                    @else
                                                        <img src="{{ $profileImage }}" alt="{{ _profileTitle($item) }}"
                                                            class="dashboard-profile">
                                                    @endif
                                                </a>
                                                @if ($item->plan_status == 'Paid')
                                                    <div class="premium-icon-dash"><iconify-icon
                                                            icon="solar:crown-bold"></iconify-icon></div>
                                                @endif
                                            </div>
                                            <div class="bottom-dashboar-content text-center py-2">
                                                <h4 class="fts-16 fw-6 white-color-n mt-2">{{ _profileTitle($item) }}</h4>
                                                <p class="fts-14 fw-4 white-color70-n mt-1">{{ _profileSubTitle($item) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Fix Meeting Modal Popups --}}
    @if ($todayMeeting)
        @php
            $todayMeetinStatus = 'Yes';
            $meetingMessage = _getLang('lbl_today_you_have_total_metting', ['#today_meeting#' => $todayMeeting]);
            if ($todayMeeting > 1) {
                $meetingMessage = _getLang('lbl_today_you_have_total_no_of_mettings', [
                    '#today_meeting#' => $todayMeeting,
                ]);
            }
        @endphp
        <!-- Fix Meeting Reminder modal popup  -->
        <div class="customsmallmodel_light photo-request modal fade" id="myMeetingModal" tabindex="-1"
            aria-labelledby="myMeetingLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                        <h2 class="fts-18 fw-6 white-color-n d-flex align-items-center gap-2" id="myMeetingLabel">
                            <div class="icon-circle teal">
                                <iconify-icon icon="healthicons:group-discussion-meeting" class="fts-18"></iconify-icon>
                            </div>
                            {{ __('messages.lbl_my_meeting') }}
                        </h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"><iconify-icon icon="radix-icons:cross-2"></iconify-icon></button>
                    </div>
                    <div class="modal_liteBody px-3 px-lg-4 py-3">
                        <div class="alert alert-message-components my-3 success fade show" role="alert">
                            <div class="alert-icon">
                                <iconify-icon icon="healthicons:group-discussion-meeting"></iconify-icon>
                            </div>
                            <div class="alert-contents pe-3">
                                <h4 class="fts-16 fw-5"> {{ __('messages.lbl_today_you_have_a_meeting') }}</h4>
                                <p class="fts-13 fw-4 opacity-75">
                                    {{ $meetingMessage }}
                                </p>
                            </div>
                        </div>
                        <div class="modal-buttonsGroup d-flex justify-content-center gap-2 mt-4">
                            <a href="{{ route('web.fixMeetings.index') }}" class="click-changeButton">{{ __('messages.lbl_view_meeting_details') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Fix Meeting Modal Popups --}}
    @endif

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const hasMeeting = @json((bool) $todayMeeting);
            if (!hasMeeting) return;

            const todayKey = 'meeting_popup_' + new Date().toISOString().slice(0, 10);

            if (!localStorage.getItem(todayKey)) {
                const modal = new bootstrap.Modal(document.getElementById('myMeetingModal'));
                modal.show();
                localStorage.setItem(todayKey, 'shown');
            }

        });
    </script>
@endpush

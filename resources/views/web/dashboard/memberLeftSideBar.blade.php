@if (Auth::check())
    @php
        $context = $context ?? 'desktop'; // default fallback so it never breaks
        $authUser = Auth::guard('web')->user();
        $currentMemberId = $authUser->id;
        $fixMeetingCount = \App\Models\MatchMemberMeeting::query()
            ->where(function ($q) use ($currentMemberId) {
                $q->where('member1_id', $currentMemberId)->orWhere('member2_id', $currentMemberId);
            })
            ->count();
    @endphp

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="sidebar-backdrop {{ $context === 'mobile' ? 'd-lg-none' : '' }}" id="sidebar-backdrop" aria-hidden="true">
    </div>

    <aside class="sidebar-container {{ $context === 'mobile' ? 'd-lg-none' : '' }}" id="sidebar-container"
        aria-label="Sidebar Navigation">
        <!-- Mobile Drawer Header (Visible only on mobile/tablet) -->
        <div class="mobile-sidebar-header">
            <a href="{{ url('/') }}" class="brand-logo-text" id="brand-logo">
                <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                    alt="{{ $configArr['web_name'] }}" class="brand-logo">
            </a>
            <button class="btn-sidebar-close" id="btn-sidebar-close" aria-label="Close sidebar">
                <iconify-icon icon="ph:x-bold"></iconify-icon>
            </button>
        </div>

        <div class="sidebar-card">
            <!-- Main Primary Menu -->
            <ul class="sidebar-menu-list">
                <li>
                    <a href="{{ route('web.dashboard.index') }}"
                        class="sidebar-menu-item {{ _navActive(['web.dashboard.*']) }}">
                        <span class="sidebar-item-left">
                            <iconify-icon icon="ph:squares-four-fill"></iconify-icon>
                            <span>{{ __('messages.lbl_dashboard') }}</span>
                        </span>
                        {{-- <span class="active-radio-indicator"></span> --}}
                    </a>
                </li>
                <li>
                    <a href="{{ route('web.myProfile.index') }}"
                        class="sidebar-menu-item {{ Route::is('web.myProfile.index') ? 'active' : '' }}"
                        id="side-myprofile">
                        <span class="sidebar-item-left">
                            <iconify-icon icon="ph:user-circle-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_my_profile') }}</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('web.myProfile.downloadBiodataPdf', [$authUser->id]) }}"
                        class="sidebar-menu-item" id="side-download-biodata" target="_blank" rel="noopener">
                        <span class="sidebar-item-left">
                            <iconify-icon icon="ph:file-pdf-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_download_biodata') }}</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('web.myProfile.editProfile', 'upload_photos') }}"
                        class="sidebar-menu-item {{ _navActiveParam('web.myProfile.editProfile', 'id', 'upload_photos') }}"
                        id="side-myprofile">
                        <span class="sidebar-item-left">
                            <iconify-icon icon="ph:images-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_upload_photo_album') }}</span>
                        </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('web.myProfile.editProfile', 'upload_id_proof') }}"
                        class="sidebar-menu-item {{ _navActiveParam('web.myProfile.editProfile', 'id', 'upload_id_proof') }}"
                        id="side-myprofile">
                        <span class="sidebar-item-left">
                            <iconify-icon icon="ph:identification-card-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_id_proof_upload') }}</span>
                        </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('web.myProfile.editProfile', 'upload_horoscope') }}"
                        class="sidebar-menu-item {{ _navActiveParam('web.myProfile.editProfile', 'id', 'upload_horoscope') }}"
                        id="side-myprofile">
                        <span class="sidebar-item-left">
                            <iconify-icon icon="ph:star-four-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_upload_horoscope') }}</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('web.matches.recommended') }}"
                        class="sidebar-menu-item {{ _navActive(['web.matches.*']) }}">
                        <span class="sidebar-item-left">
                            <iconify-icon icon="ph:users-three-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_matches') }}</span>
                        </span>
                        {{-- <span class="badge-pill-count badge-green">1.4k</span> --}}
                    </a>
                </li>
                <li>
                    <a href="{{ route('web.search.type', ['type' => 'quick-search']) }}"
                        class="sidebar-menu-item {{ _navActive(['search*', 'web.search.*']) }}">
                        <span class="sidebar-item-left">
                            <iconify-icon icon="ph:magnifying-glass-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_search') }}</span>
                        </span>
                    </a>
                </li>
                {{-- <li>
                    <a href="short-listed-profile.html" class="sidebar-menu-item">
                        <span class="sidebar-item-left">
                            <iconify-icon icon="ph:chart-line-up-bold"></iconify-icon>
                            <span>Activities</span>
                        </span>
                        <span class="badge-pill-count badge-red">4</span>
                    </a>
                </li> --}}
                <li>
                    <a href="{{ route('web.membershipPlan.index') }}"
                        class="sidebar-menu-item {{ Route::is('web.membershipPlan.index') ? 'active' : '' }}">
                        <span class="sidebar-item-left">
                            <iconify-icon icon="ph:crown-simple-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_membership') }}</span>
                        </span>
                        {{-- <span class="badge-pill-count badge-purple">VIP</span> --}}
                    </a>
                </li>
                @if ($authUser->user_type == 1 || $fixMeetingCount > 0)
                    <li>
                        <a href="{{ route('web.fixMeetings.index') }}"
                            class="sidebar-menu-item {{ Route::is('web.fixMeetings.index') ? 'active' : '' }}">
                            <span class="sidebar-item-left">
                                <iconify-icon icon="ph:calendar-heart-fill"></iconify-icon>
                                <span>{{ __('messages.lbl_my_meetings') }}</span>
                            </span>
                        </a>
                    </li>
                @endif
            </ul>

            <div class="sidebar-divider"></div>

            <!-- MY INSIGHTS -->
            <div class="sidebar-section-header">
                <span class="header-left-title">
                    <iconify-icon icon="ph:squares-four-bold"></iconify-icon>
                    <span>{{ __('messages.lbl_my_insights') }}</span>
                </span>
                {{-- <span class="pill-growth-wk">↗ +14% wk</span> --}}
            </div>

            <a href="{{ route('web.expressInterest.index', ['type' => 'interest_sent']) }}">
                <div class="insight-stat-row">
                    <div class="stat-left-wrap">
                        <div class="stat-icon-circle">
                            <iconify-icon icon="ph:paper-plane-tilt-bold"></iconify-icon>
                        </div>
                        <div class="stat-text-info">
                            <span class="stat-title">{{ __('messages.lbl_send_interests') }}</span>
                            <span class="stat-subtitle">{{ $interestSentCount }}
                                {{ __('messages.lbl_connections') }}</span>
                        </div>
                    </div>
                    <div class="mini-bar-chart" aria-hidden="true">
                        <span class="mini-bar blue" style="height: 5px;"></span>
                        <span class="mini-bar blue" style="height: 9px;"></span>
                        <span class="mini-bar blue" style="height: 14px;"></span>
                        <span class="mini-bar blue" style="height: 11px;"></span>
                        <span class="mini-bar blue" style="height: 8px;"></span>
                    </div>
                </div>
            </a>

            <a href="{{ route('web.viewedProfile.index', ['type' => 'i_viewed']) }}">
                <div class="insight-stat-row">
                    <div class="stat-left-wrap">
                        <div class="stat-icon-circle">
                            <iconify-icon icon="ph:eye-bold" style="color: #f43f5e;"></iconify-icon>
                        </div>
                        <div class="stat-text-info">
                            <span class="stat-title">{{ __('messages.lbl_profile_views') }}</span>
                            <span class="stat-subtitle">{{ $iViewedProfileCount ?? 0 }}
                                {{ __('messages.lbl_total_viewed_profiles') }}</span>
                        </div>
                    </div>
                    <div class="mini-bar-chart" aria-hidden="true">
                        <span class="mini-bar pink" style="height: 6px;"></span>
                        <span class="mini-bar pink" style="height: 11px;"></span>
                        <span class="mini-bar pink" style="height: 15px;"></span>
                        <span class="mini-bar pink" style="height: 9px;"></span>
                        <span class="mini-bar pink" style="height: 13px;"></span>
                    </div>
                </div>
            </a>

            <div class="sidebar-divider"></div>

            <!-- ACTIVITY FILTERS -->
            <div class="sidebar-section-header">
                <span class="header-left-title">
                    <iconify-icon icon="ph:squares-four-bold"></iconify-icon>
                    <span>{{ __('messages.lbl_activity_filters') }}</span>
                </span>
            </div>

            <a href="{{ route('web.expressInterest.index', ['type' => 'interest_sent']) }}"
                class="filter-item-row {{ Route::currentRouteName() == 'web.expressInterest.index' && request()->type == 'interest_sent' ? 'active' : '' }}">
                <span class="sidebar-item-left">
                    <iconify-icon icon="ph:heart-straight-bold"></iconify-icon>
                    <span>{{ __('messages.lbl_express_interest_sent') }}</span>
                </span>
                <span class="filter-badge red">{{ $interestSentCount }}</span>
            </a>
            <a href="{{ route('web.expressInterest.index', ['type' => 'interest_receive']) }}"
                class="filter-item-row {{ Route::currentRouteName() == 'web.expressInterest.index' && request()->type == 'interest_receive' ? 'active' : '' }}">
                <span class="sidebar-item-left">
                    <iconify-icon icon="ph:heart-bold"></iconify-icon>
                    <span>{{ __('messages.lbl_express_interest_received') }}</span>
                </span>
                <span class="filter-badge red">{{ $interestReceiveCount }}</span>
            </a>
            <a href="{{ route('web.viewedProfile.index', ['type' => 'i_viewed']) }}"
                class="filter-item-row {{ Route::currentRouteName() == 'web.viewedProfile.index' && request()->type == 'i_viewed' ? 'active' : '' }}">
                <span class="sidebar-item-left">
                    <iconify-icon icon="ph:eye-bold"></iconify-icon>
                    <span>{{ __('messages.lbl_i_viewed_profile') }}</span>
                </span>
                <span class="filter-badge red">{{ $iViewedProfileCount }}</span>
            </a>
            <a href="{{ route('web.viewedProfile.index', ['type' => 'who_viewed']) }}"
                class="filter-item-row {{ Route::currentRouteName() == 'web.viewedProfile.index' && request()->type == 'who_viewed' ? 'active' : '' }}">
                <span class="sidebar-item-left">
                    <iconify-icon icon="ph:eye-bold"></iconify-icon>
                    <span>{{ __('messages.lbl_who_viewed_my_profile') }}</span>
                </span>
                <span class="filter-badge red">{{ $whoViewedProfileCount }}</span>
            </a>
            <a href="{{ route('web.viewedContact.index', ['type' => 'i_viewed']) }}"
                class="filter-item-row {{ Route::currentRouteName() == 'web.viewedContact.index' && request()->type == 'i_viewed' ? 'active' : '' }}">
                <span class="sidebar-item-left">
                    <iconify-icon icon="ph:address-book-bold"></iconify-icon>
                    <span>{{ __('messages.lbl_i_viewed_contact') }}</span>
                </span>
                <span class="filter-badge red">{{ $iViewedContactCount }}</span>
            </a>
            <a href="{{ route('web.viewedContact.index', ['type' => 'who_viewed']) }}"
                class="filter-item-row {{ Route::currentRouteName() == 'web.viewedContact.index' && request()->type == 'who_viewed' ? 'active' : '' }}">
                <span class="sidebar-item-left">
                    <iconify-icon icon="ph:address-book-bold"></iconify-icon>
                    <span>{{ __('messages.lbl_who_viewed_my_contact') }}</span>
                </span>
                <span class="filter-badge red">{{ $whoViewedContactCount }}</span>
            </a>
            <a href="{{ route('web.shortlist.index') }}"
                class="filter-item-row {{ Route::is('web.shortlist.index') ? 'active' : '' }}">
                <span class="sidebar-item-left">
                    <iconify-icon icon="ph:star-bold"></iconify-icon>
                    <span>{{ __('messages.lbl_shortlist_profile') }}</span>
                </span>
                <span class="filter-badge red">{{ $shortlistCount }}</span>
            </a>
            <a href="{{ route('web.blocklist.index') }}"
                class="filter-item-row {{ Route::is('web.blocklist.index') ? 'active' : '' }}">
                <span class="sidebar-item-left">
                    <iconify-icon icon="ph:prohibit-bold"></iconify-icon>
                    <span>{{ __('messages.lbl_blocklist_profile') }}</span>
                </span>
                <span class="filter-badge red">{{ $blocklistCount }}</span>
            </a>
            <a href="{{ route('web.photoRequest.index', ['type' => 'request_sent']) }}"
                class="filter-item-row {{ Route::currentRouteName() == 'web.photoRequest.index' && request()->type == 'request_sent' ? 'active' : '' }}">
                <span class="sidebar-item-left">
                    <iconify-icon icon="ph:camera-plus-bold"></iconify-icon>
                    <span>{{ __('messages.lbl_photo_request_sent') }}</span>
                </span>
                <span class="filter-badge red">{{ $photoRequestSentCount }}</span>
            </a>
            <a href="{{ route('web.photoRequest.index', ['type' => 'request_receive']) }}"
                class="filter-item-row {{ Route::currentRouteName() == 'web.photoRequest.index' && request()->type == 'request_receive' ? 'active' : '' }}">
                <span class="sidebar-item-left">
                    <iconify-icon icon="ph:camera-bold"></iconify-icon>
                    <span>{{ __('messages.lbl_photo_request_received') }}</span>
                </span>
                <span class="filter-badge red">{{ $photoRequestReceiveCount }}</span>
            </a>
            <a href="{{ route('web.videoVoiceCall.index') }}"
                class="filter-item-row {{ Route::currentRouteName() == 'web.videoVoiceCall.index' ? 'active' : '' }}">
                <span class="sidebar-item-left">
                    <iconify-icon icon="ph:phone-call-bold"></iconify-icon>
                    <span>{{ __('messages.lbl_call_history') }}</span>
                </span>
                <span class="filter-badge red">{{ $callHistoryCount }}</span>
            </a>
        </div>

        <!-- Featured Platinum VIP Card (Ultra-Crisp Vector & CSS) -->
        @php
            $authUser = Auth::guard('web')->user();
        @endphp
        @if ($authUser?->plan_status !== 'Paid')
            <div class="sidebar-vip-card-box" id="sidebar-vip-card" role="button" tabindex="0">
                <div class="vip-top-badges-row">
                    <span class="badge-featured-gold">★ {{ __('messages.lbl_featured') }}</span>
                    {{-- <span class="badge-discount-red">50% OFF</span> --}}
                </div>

                <div class="vip-crown-circle">
                    <iconify-icon icon="ph:crown-fill"></iconify-icon>
                </div>

                <div class="badge-platinum-title">
                    <iconify-icon icon="ph:crown-simple-fill"></iconify-icon>
                    <span>{{ __('messages.lbl_upgrade_membership') }}</span>
                </div>

                <div class="vip-card-heading">
                    {{ __('messages.lbl_upgrade_for') }}<br>
                    <span class="italic-gold">{{ __('messages.lbl_exclusive_access') }}</span>
                </div>

                <div class="vip-card-perks-list">
                    <div class="vip-perk-item">
                        <iconify-icon icon="ph:shield-check-bold"></iconify-icon>
                        <span>{{ __('messages.lbl_verified_numbers') }}</span>
                    </div>
                    <div class="vip-perk-item">
                        <iconify-icon icon="ph:user-circle-gear-bold"></iconify-icon>
                        <span>{{ __('messages.lbl_personal_matchmaker') }}</span>
                    </div>
                </div>

                <button class="btn-sidebar-go-premium" id="btn-go-premium">
                    <span>{{ __('messages.lbl_go_premium') }}</span>
                    <iconify-icon icon="ph:arrow-right-bold"></iconify-icon>
                </button>

                <div class="vip-card-footer-stars">
                    ★★★★★ <span>{{ __('messages.lbl_elite_matrimonial_club') }}</span>
                </div>
            </div>
        @endif

        <!-- Current Active Plan Card -->
        @if ($authUser?->plan_status == 'Paid' && !empty($currentPlan))
            <div class="sidebar-current-plan-card" id="sidebar-current-plan-card">

                <!-- Top Badges -->
                <div class="current-plan-top-row">
                    <span class="badge-active-live">
                        <span class="live-pulse-dot"></span>
                        {{ __('messages.lbl_my_plan') }}
                    </span>

                    <span class="badge-plan-tier">
                        {{ $currentPlan->plan_name }}
                    </span>
                </div>

                <!-- Glowing Emblem -->
                <div class="current-plan-emblem-wrap">
                    <div class="current-plan-emblem">
                        <iconify-icon icon="ph:sketch-logo-fill"></iconify-icon>
                    </div>
                    <div class="emblem-sparkle-glow"></div>
                </div>

                <!-- Plan Heading -->
                <div class="current-plan-header-info">
                    <h4 class="current-plan-name">
                        {{ $currentPlan->plan_name }}
                    </h4>

                    <p class="current-plan-validity">
                        <iconify-icon icon="ph:calendar-check-bold"></iconify-icon>
                        <span>
                            {{ __('messages.lbl_valid_till') }}
                            {{ _displayDate($currentPlan->plan_expiry_date, 'j F, Y') }}
                        </span>
                    </p>
                </div>

                <!-- Quota & Perks Tracker -->
                <div class="current-plan-quota-box">

                    {{-- Profile Views --}}
                    @php
                        $profileTotal = (int) ($currentPlan->view_profile_total ?? 0);
                        $profileRemaining = (int) ($currentPlan->view_profile_remaining ?? 0);
                        $profileUsed = max(0, $profileTotal - $profileRemaining);
                        $profileProgress =
                            $profileTotal > 0 ? min(100, round(($profileRemaining / $profileTotal) * 100)) : 0;
                    @endphp

                    <div class="quota-stat-item">
                        <div class="quota-label-row">
                            <span class="quota-name">
                                <iconify-icon icon="ph:eye-fill" class="quota-icon green"></iconify-icon>
                                {{ __('messages.lbl_view_profile') }}
                            </span>

                            <span class="quota-val">
                                <strong>{{ $profileRemaining }}</strong> / {{ $profileTotal }} left
                            </span>
                        </div>

                        <div class="quota-progress-track">
                            <div class="quota-progress-fill green" style="width: {{ $profileProgress }}%;">
                            </div>
                        </div>
                    </div>

                    {{-- Interests --}}
                    @php
                        $interestsTotal = (int) ($currentPlan->interests_total ?? 0);
                        $interestsRemaining = (int) ($currentPlan->interests_remaining ?? 0);
                        $interestsProgress =
                            $interestsTotal > 0 ? min(100, round(($interestsRemaining / $interestsTotal) * 100)) : 0;
                    @endphp

                    <div class="quota-stat-item">
                        <div class="quota-label-row">
                            <span class="quota-name">
                                <iconify-icon icon="ph:heart-fill" class="quota-icon green"></iconify-icon>
                                {{ __('messages.lbl_express_interest') }}
                            </span>

                            <span class="quota-val">
                                <strong>{{ $interestsRemaining }}</strong> / {{ $interestsTotal }} left
                            </span>
                        </div>

                        <div class="quota-progress-track">
                            <div class="quota-progress-fill green" style="width: {{ $interestsProgress }}%;">
                            </div>
                        </div>
                    </div>

                    {{-- Contact Views --}}
                    @php
                        $contactTotal = (int) ($currentPlan->contact_views_total ?? 0);
                        $contactRemaining = (int) ($currentPlan->contact_views_remaining ?? 0);
                        $contactProgress =
                            $contactTotal > 0 ? min(100, round(($contactRemaining / $contactTotal) * 100)) : 0;
                    @endphp

                    <div class="quota-stat-item">
                        <div class="quota-label-row">
                            <span class="quota-name">
                                <iconify-icon icon="ph:phone-call-fill" class="quota-icon green"></iconify-icon>
                                {{ __('messages.lbl_viewed_contact') }}
                            </span>

                            <span class="quota-val">
                                <strong>{{ $contactRemaining }}</strong> / {{ $contactTotal }} left
                            </span>
                        </div>

                        <div class="quota-progress-track">
                            <div class="quota-progress-fill green" style="width: {{ $contactProgress }}%;">
                            </div>
                        </div>
                    </div>

                    {{-- Audio Calls --}}
                    @php
                        $voiceApproved = $configArr['zego_voice_call_setting'] === 'APPROVED';
                        $videoApproved = $configArr['zego_video_call_setting'] === 'APPROVED';
                    @endphp
                        @if ($voiceApproved)
                        @php
                            $audioTotal = (int) ($currentPlan->audio_minutes_total ?? 0);
                            $audioRemaining = (int) ($currentPlan->audio_minutes_remaining ?? 0);
                            $audioProgress = $audioTotal > 0 ? min(100, round(($audioRemaining / $audioTotal) * 100)) : 0;
                        @endphp

                        <div class="quota-stat-item">
                            <div class="quota-label-row">
                                <span class="quota-name">
                                    <iconify-icon icon="ph:phone-call-fill" class="quota-icon green"></iconify-icon>
                                    {{ __('messages.lbl_audio_calls') }}
                                </span>

                                <span class="quota-val">
                                    <strong>{{ $audioRemaining }}</strong> / {{ $audioTotal }} left
                                </span>
                            </div>

                            <div class="quota-progress-track">
                                <div class="quota-progress-fill green" style="width: {{ $audioProgress }}%;">
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Video Calls --}}
                    @if ($videoApproved)
                        @php
                            $videoTotal = (int) ($currentPlan->video_minutes_total ?? 0);
                            $videoRemaining = (int) ($currentPlan->video_minutes_remaining ?? 0);
                            $videoProgress = $videoTotal > 0 ? min(100, round(($videoRemaining / $videoTotal) * 100)) : 0;
                        @endphp

                        <div class="quota-stat-item">
                            <div class="quota-label-row">
                                <span class="quota-name">
                                    <iconify-icon icon="ph:video-camera-fill" class="quota-icon green"></iconify-icon>
                                    {{ __('messages.lbl_video_calls') }}
                                </span>

                                <span class="quota-val">
                                    <strong>{{ $videoRemaining }}</strong> / {{ $videoTotal }} left
                                </span>
                            </div>

                            <div class="quota-progress-track">
                                <div class="quota-progress-fill green" style="width: {{ $videoProgress }}%;">
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Chat --}}
                    <div class="quota-stat-item">
                        <div class="quota-label-row">
                            <span class="quota-name">
                                <iconify-icon icon="ph:chat-circle-dots-fill" class="quota-icon green"></iconify-icon>
                                {{ __('messages.lbl_allowed_chat') }}
                            </span>

                            <span class="quota-val">
                                <strong>
                                    {{ $currentPlan->can_chat ? __('messages.lbl_yes') : __('messages.lbl_no') }}
                                </strong>
                            </span>
                        </div>

                        <div class="quota-progress-track">
                            <div class="quota-progress-fill green"
                                style="width: {{ $currentPlan->can_chat ? 100 : 0 }}%;">
                            </div>
                        </div>
                    </div>

                    {{-- Auto AI Interest --}}
                    @if (_getConstant('AI_MODE') == 'Enabled')
                        <div class="quota-stat-item">
                            <div class="quota-label-row">
                                <span class="quota-name">
                                    <iconify-icon icon="ph:sparkle-fill" class="quota-icon purple"></iconify-icon>
                                    {{ __('messages.lbl_send_auto_ai_interest') }}
                                </span>

                                <span class="quota-val">
                                    <strong>
                                        {{ $currentPlan->ai_interest ? __('messages.lbl_yes') : __('messages.lbl_no') }}
                                    </strong>
                                </span>
                            </div>

                            <div class="quota-progress-track">
                                <div class="quota-progress-fill purple"
                                    style="width: {{ $currentPlan->ai_interest ? 100 : 0 }}%;">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- CTA Buttons -->
                <div class="current-plan-actions-wrap">

                    <a href="{{ route('web.currentPlan.index') }}" class="btn-manage-current-plan">
                        <span>{{ __('messages.lbl_view_plan_billing') }}</span>
                        <iconify-icon icon="ph:arrow-up-right-bold"></iconify-icon>
                    </a>

                    <a href="{{ route('web.membershipPlan.index') }}" class="btn-renew-upgrade-plan">
                        <iconify-icon icon="ph:sparkle-bold"></iconify-icon>
                        <span>{{ __('messages.lbl_upgrade_plan') }}</span>
                    </a>

                </div>

                <!-- Footer ID & Status -->
                <div class="current-plan-card-footer">
                    <iconify-icon icon="ph:seal-check-fill"></iconify-icon>

                    <span>
                        {{ __('messages.lbl_verified_account_id') }}:
                        {{ $authUser->matri_id }}
                    </span>
                </div>

            </div>
        @endif

        <!-- Concierge & Notifications Footer -->
        <div class="sidebar-footer-row">
            <a href="{{ route('web.contactUs.index') }}" class="concierge-pill-btn" id="btn-concierge">
                <span class="concierge-left">
                    <iconify-icon icon="ph:headphones-bold"></iconify-icon>
                    <span>{{ __('messages.lbl_any_query_contact_us') }}</span>
                </span>
                <span class="online-status-pulse"></span>
            </a>
        </div>
    </aside>
@endif

@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    @php
        $authUser = Auth::guard('web')->user();
        if ($authUser) {
            $authUser->refresh();
        }
        $profileImage = _getMemberProfileImage($authUser, 'Yes');
    @endphp
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="dashboard-layout">
                    {{-- LEFT SIDEBAR (Responsive Off-Canvas Drawer on Mobile) --}}
                    @include('web.dashboard.memberLeftSideBar')

                    <main class="main-content-area" id="main-content">
                        <!-- HERO BANNER: AI Curated Matchmaking -->
                        <section class="hero-ai-banner"
                            aria-label="{{ _getConstant('AI_MODE') == 'Enabled' ? __('messages.lbl_ai_matchmaking_description') : __('messages.lbl_find_your_match') }}">

                            {{-- Left: Profile box (same for AI and non-AI) --}}
                            <div class="hero-profile-box">
                                <img src="{{ $profileImage }}" alt="{{ $authUser->fullname ?? __('messages.lbl_profile') }}">

                                {{-- Name + Matri ID badge on photo --}}
                                <div class="profile-identity-overlay">
                                    <span class="profile-identity-name" title="{{ $authUser->fullname }}">
                                        {{ $authUser->fullname }}
                                    </span>
                                    <span class="matri-id-badge">
                                        <iconify-icon icon="ph:identification-card-fill"></iconify-icon>
                                        {{ $authUser->matri_id }}
                                    </span>
                                </div>

                                <div class="profile-strength-overlay">
                                    <div class="strength-text-row">
                                        <span class="white-color-p">{{ __('messages.lbl_profile_strength') }}</span>
                                        <span class="strength-percent">{{ $completionPercent }}%</span>
                                    </div>
                                    <div class="strength-progress-track">
                                        <div class="strength-progress-fill"
                                            style="width: {{ min(100, max(0, $completionPercent)) }}%;"></div>
                                    </div>
                                </div>
                            </div>

                            @if (_getConstant('AI_MODE') == 'Enabled')
                                {{-- ================= AI VERSION ================= --}}

                                {{-- Center --}}
                                <div class="hero-ai-content">
                                    <div class="hero-pill-badge-top">
                                        <span>{{ __('messages.lbl_ai_compatibility_engine') }}</span>
                                        <span class="red-bullet-dot"></span>
                                        <span class="badge-secondary-text">{{ __('messages.lbl_smart_matchmaker') }}</span>
                                    </div>

                                    <h1 class="hero-title-main">
                                        {{ __('messages.lbl_your_personalized') }}
                                        <span class="text-highlight-ai">{{ __('messages.lbl_ai_curated') }}</span>
                                        {{ __('messages.lbl_matchmaking') }}
                                    </h1>

                                    <p class="hero-subtitle-desc">{{ __('messages.lbl_ai_matchmaking_description') }}</p>

                                    <div class="hero-metrics-row">
                                        <div class="metric-pill-tag purple">
                                            <iconify-icon icon="ph:shield-heart-fill"></iconify-icon>
                                            <span>{{ __('messages.lbl_top_match_today') }}</span>
                                        </div>
                                        <div class="metric-pill-tag blue">
                                            <iconify-icon icon="ph:shield-check-fill"></iconify-icon>
                                            <span>{{ __('messages.lbl_id_vedic_verified') }}</span>
                                        </div>
                                    </div>

                                    <div class="hero-cta-action-row">
                                        <a href="{{ route('web.aiMatchMaking.index') }}" class="btn-hero-curated"
                                            id="btn-view-curated">
                                            <iconify-icon icon="ph:heart-straight-fill"></iconify-icon>
                                            <span>{{ __('messages.lbl_view_curated_matches') }}</span>
                                            <iconify-icon icon="ph:arrow-right-bold"></iconify-icon>
                                        </a>
                                    </div>
                                </div>

                                {{-- Right: Auto-Interest widget --}}
                                <div class="hero-auto-interest-box">
                                    <div class="auto-interest-header">
                                        <div class="auto-interest-title-wrap">
                                            <div class="auto-interest-icon-glow">
                                                <iconify-icon icon="ph:heart-fill"></iconify-icon>
                                            </div>
                                            <span
                                                class="auto-interest-title">{{ __('messages.lbl_ai_auto_interest') }}</span>
                                        </div>
                                        @if ($authUser->auto_interest_enabled)
                                            <span class="badge-active-green">• {{ __('messages.lbl_active') }}</span>
                                        @endif
                                    </div>

                                    <p class="auto-interest-desc">
                                        {{ __('messages.lbl_auto_interest_description', ['percentage' => $autoInterestMatchPercentage ?? 90]) }}
                                    </p>

                                    <div class="auto-dispatch-toggle-row">
                                        <span class="dispatch-label">{{ __('messages.lbl_instant_match_dispatch') }}</span>
                                        <div id="autoModeToggle"
                                            class="toggle-switch-{{ $authUser->auto_interest_enabled ? 'on' : 'off' }}"
                                            role="switch"
                                            aria-checked="{{ $authUser->auto_interest_enabled ? 'true' : 'false' }}"
                                            tabindex="0">
                                            <span class="toggle-status-text">
                                                {{ $authUser->auto_interest_enabled ? __('messages.lbl_on') : __('messages.lbl_off') }}
                                            </span>
                                            <span class="toggle-switch-circle">
                                                <iconify-icon icon="ph:heart-fill"></iconify-icon>
                                            </span>
                                        </div>
                                    </div>

                                    <a href="{{ route('web.aiAutoInterest.index') }}" class="btn-hero-ai-interest"
                                        id="btn-ai-auto-interest">
                                        <iconify-icon icon="ph:arrow-right-bold" class="fts-18"></iconify-icon>
                                        <span>{{ __('messages.lbl_ai_auto_send_interest') }}</span>
                                    </a>
                                </div>
                            @else
                                {{-- ================= NON-AI VERSION ================= --}}

                                {{-- Center --}}
                                <div class="hero-ai-content">
                                    <div class="hero-pill-badge-top">
                                        <span>{{ __('messages.lbl_trusted_matrimony') }}</span>
                                        <span class="red-bullet-dot"></span>
                                        <span
                                            class="badge-secondary-text">{{ __('messages.lbl_verified_profiles') }}</span>
                                    </div>

                                    <h1 class="hero-title-main">
                                        {{ __('messages.lbl_find_your') }}
                                        <span class="text-highlight-ai">{{ __('messages.lbl_perfect_match') }}</span>
                                    </h1>

                                    <p class="hero-subtitle-desc">{{ __('messages.lbl_find_match_description') }}</p>

                                    <div class="hero-metrics-row">
                                        <div class="metric-pill-tag purple">
                                            <iconify-icon icon="ph:shield-heart-fill"></iconify-icon>
                                            <span>{{ __('messages.lbl_top_match_today') }}</span>
                                        </div>
                                        <div class="metric-pill-tag blue">
                                            <iconify-icon icon="ph:shield-check-fill"></iconify-icon>
                                            <span>{{ __('messages.lbl_genuine_profiles') }}</span>
                                        </div>
                                    </div>

                                    <div class="hero-cta-action-row">
                                        <a href="{{ route('web.matches.recommended') }}" class="btn-hero-curated"
                                            id="btn-view-matches">
                                            <iconify-icon icon="ph:heart-straight-fill"></iconify-icon>
                                            <span>{{ __('messages.lbl_view_matches') }}</span>
                                            <iconify-icon icon="ph:arrow-right-bold"></iconify-icon>
                                        </a>
                                    </div>
                                </div>

                                {{-- Right: Quick action widget --}}
                                <div class="hero-auto-interest-box">
                                    <div class="auto-interest-header">
                                        <div class="auto-interest-title-wrap">
                                            <div class="auto-interest-icon-glow">
                                                <iconify-icon icon="ph:magnifying-glass-bold"></iconify-icon>
                                            </div>
                                            <span class="auto-interest-title">{{ __('messages.lbl_quick_search') }}</span>
                                        </div>
                                    </div>

                                    <p class="auto-interest-desc">
                                        {{ __('messages.lbl_quick_search_description_dashboard') }}</p>

                                    <a href="{{ route('web.search.type', ['type' => 'quick-search']) }}"
                                        class="btn-hero-ai-interest" id="btn-quick-search">
                                        <iconify-icon icon="ph:arrow-right-bold" class="fts-18"></iconify-icon>
                                        <span>{{ __('messages.lbl_search_matches') }}</span>
                                    </a>
                                </div>
                            @endif
                        </section>

                        @if ($featuredMember->isNotEmpty())
                            <section class="profile-section-block" id="section-premium"
                                aria-label="{{ __('messages.lbl_featured_members') }}">
                                <div class="profile-section-header">
                                    <div class="section-title-wrap">
                                        <div class="section-title-icon orange">
                                            <iconify-icon icon="ph:heart-straight-fill"></iconify-icon>
                                        </div>
                                        <div class="section-title-text-group">
                                            <div class="section-title-top-row">
                                                <h3 class="section-main-heading">{{ __('messages.lbl_featured_members') }}
                                                </h3>
                                            </div>
                                            <span
                                                class="section-subtitle-line">{{ __('messages.lbl_featured_members_msg') }}</span>
                                        </div>
                                    </div>
                                    <div class="section-header-controls">
                                        <div class="scroll-arrow-buttons">
                                            <button class="btn-scroll-arrow" data-target="scroll-featured"
                                                data-direction="left" aria-label="Scroll Left">
                                                <iconify-icon icon="ph:caret-left-bold"></iconify-icon>
                                            </button>
                                            <button class="btn-scroll-arrow" data-target="scroll-featured"
                                                data-direction="right" aria-label="Scroll Right">
                                                <iconify-icon icon="ph:caret-right-bold"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="scrollable-profiles-container">
                                    <div class="profiles-scroll-row" id="scroll-featured">
                                        @foreach ($featuredMember as $item)
                                            @php
                                                $canView = _canViewMemberPhoto($item, $item->hasPhotoRequestAccess);
                                                $hasPhoto = _checkPhotoExist($item);
                                                $profileImage = _getMemberProfileImage($item);

                                                ## Online Status:
                                                $onlineStatus = _memberOnlineStatus($item);
                                            @endphp
                                            <article class="match-profile-card">
                                                <div class="profile-photo-container">
                                                    @if ($item->plan_status == 'Paid')
                                                        <div class="premium-icon-dash"><iconify-icon
                                                                icon="solar:crown-bold"></iconify-icon></div>
                                                    @endif
                                                    @if ($onlineStatus['status_code'] == 'online')
                                                        <div class="online-blinking-dot"></div>
                                                    @endif
                                                    <a href="{{ route('web.userProfile.index', _encrypt($item->id)) }}">
                                                        @if (!$canView && $hasPhoto)
                                                            <img src="{{ _getProtectedImage($item->gender) }}"
                                                                alt="{{ _profileTitle($item) }}"
                                                                style="object-position: center 20%;">
                                                        @else
                                                            <img src="{{ $profileImage }}"
                                                                alt="{{ _profileTitle($item) }}"
                                                                style="object-position: center 20%;">
                                                        @endif
                                                    </a>
                                                    <div class="card-match-score-pill">
                                                        <iconify-icon icon="ph:lightning-fill"></iconify-icon>
                                                        <span>{{ $item->matchPercent }}%
                                                            {{ __('messages.lbl_match') }}</span>
                                                    </div>
                                                </div>
                                                <div class="profile-card-content">
                                                    <h3 class="profile-name-age">{{ _profileTitle($item) }}</h3>
                                                    <div class="profile-interest-line">
                                                        <iconify-icon icon="bxs:user-detail"></iconify-icon>
                                                        <span>{{ _profileSubTitle($item) }}</span>
                                                    </div>
                                                    {{-- <div class="profile-tag-badges-row">
                                                        <span class="profile-tag-pill compat-green">• High Compatibility</span>
                                                    </div> --}}
                                                    <div class="profile-card-action-row">
                                                        <a href="{{ route('web.userProfile.index', _encrypt($item->id)) }}"
                                                            class="btn-card-connect">
                                                            <iconify-icon icon="ph:paper-plane-tilt-bold"></iconify-icon>
                                                            {{ __('messages.lbl_view_profile') }}
                                                        </a>
                                                        {{-- <button class="btn-card-shortlist">
                                                            <iconify-icon icon="ph:bookmark-simple"></iconify-icon> Shortlist
                                                        </button> --}}
                                                    </div>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        @endif
                        @if ($premiumMatchesMember->isNotEmpty())
                            <section class="profile-section-block" id="section-premium"
                                aria-label="{{ __('messages.lbl_premium_members') }}">
                                <div class="profile-section-header">
                                    <div class="section-title-wrap">
                                        <div class="section-title-icon blue">
                                            <iconify-icon icon="ph:heart-straight-fill"></iconify-icon>
                                        </div>
                                        <div class="section-title-text-group">
                                            <div class="section-title-top-row">
                                                <h3 class="section-main-heading">{{ __('messages.lbl_premium_members') }}
                                                </h3>
                                            </div>
                                            <span
                                                class="section-subtitle-line">{{ __('messages.lbl_premium_members_subtitle') }}</span>
                                        </div>
                                    </div>
                                    <div class="section-header-controls">
                                        <div class="scroll-arrow-buttons">
                                            <button class="btn-scroll-arrow" data-target="scroll-premium"
                                                data-direction="left" aria-label="Scroll Left">
                                                <iconify-icon icon="ph:caret-left-bold"></iconify-icon>
                                            </button>
                                            <button class="btn-scroll-arrow" data-target="scroll-premium"
                                                data-direction="right" aria-label="Scroll Right">
                                                <iconify-icon icon="ph:caret-right-bold"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="scrollable-profiles-container">
                                    <div class="profiles-scroll-row" id="scroll-premium">
                                        @foreach ($premiumMatchesMember as $item)
                                            @php
                                                $canView = _canViewMemberPhoto($item, $item->hasPhotoRequestAccess);
                                                $hasPhoto = _checkPhotoExist($item);
                                                $profileImage = _getMemberProfileImage($item);

                                                ## Online Status:
                                                $onlineStatus = _memberOnlineStatus($item);
                                            @endphp
                                            <article class="match-profile-card">
                                                <div class="profile-photo-container">
                                                    @if ($item->plan_status == 'Paid')
                                                        <div class="premium-icon-dash"><iconify-icon
                                                                icon="solar:crown-bold"></iconify-icon></div>
                                                    @endif
                                                    @if ($onlineStatus['status_code'] == 'online')
                                                        <div class="online-blinking-dot"></div>
                                                    @endif
                                                    <a href="{{ route('web.userProfile.index', _encrypt($item->id)) }}">
                                                        @if (!$canView && $hasPhoto)
                                                            <img src="{{ _getProtectedImage($item->gender) }}"
                                                                alt="{{ _profileTitle($item) }}"
                                                                style="object-position: center 20%;">
                                                        @else
                                                            <img src="{{ $profileImage }}"
                                                                alt="{{ _profileTitle($item) }}"
                                                                style="object-position: center 20%;">
                                                        @endif
                                                    </a>
                                                    <div class="card-match-score-pill">
                                                        <iconify-icon icon="ph:lightning-fill"></iconify-icon>
                                                        <span>{{ $item->matchPercent }}%
                                                            {{ __('messages.lbl_match') }}</span>
                                                    </div>
                                                </div>
                                                <div class="profile-card-content">
                                                    <h3 class="profile-name-age">{{ _profileTitle($item) }}</h3>
                                                    <div class="profile-interest-line">
                                                        <iconify-icon icon="bxs:user-detail"></iconify-icon>
                                                        <span>{{ _profileSubTitle($item) }}</span>
                                                    </div>
                                                    {{-- <div class="profile-tag-badges-row">
                                                        <span class="profile-tag-pill compat-green">• High Compatibility</span>
                                                    </div> --}}
                                                    <div class="profile-card-action-row">
                                                        <a href="{{ route('web.userProfile.index', _encrypt($item->id)) }}"
                                                            class="btn-card-connect">
                                                            <iconify-icon icon="ph:paper-plane-tilt-bold"></iconify-icon>
                                                            {{ __('messages.lbl_view_profile') }}
                                                        </a>
                                                        {{-- <button class="btn-card-shortlist">
                                                            <iconify-icon icon="ph:bookmark-simple"></iconify-icon> Shortlist
                                                        </button> --}}
                                                    </div>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        @endif
                        @if ($recommedMatchesMember->isNotEmpty())
                            <section class="profile-section-block" id="section-recommended"
                                aria-label="{{ __('messages.lbl_recommended_members') }}">
                                <div class="profile-section-header">
                                    <div class="section-title-wrap">
                                        <div class="section-title-icon green">
                                            <iconify-icon icon="ph:user-plus-fill"></iconify-icon>
                                        </div>
                                        <div class="section-title-text-group">
                                            <div class="section-title-top-row">
                                                <h3 class="section-main-heading">
                                                    {{ __('messages.lbl_recommended_members') }}</h3>
                                                {{-- <span class="section-tag-pill badge-blue">★ 98% Vedic Match</span> --}}
                                            </div>
                                            <span
                                                class="section-subtitle-line">{{ __('messages.lbl_recommended_members_subtitle') }}</span>
                                        </div>
                                    </div>
                                    <div class="section-header-controls">
                                        <div class="scroll-arrow-buttons">
                                            <button class="btn-scroll-arrow" data-target="scroll-recommended"
                                                data-direction="left" aria-label="Scroll Left">
                                                <iconify-icon icon="ph:caret-left-bold"></iconify-icon>
                                            </button>
                                            <button class="btn-scroll-arrow" data-target="scroll-recommended"
                                                data-direction="right" aria-label="Scroll Right">
                                                <iconify-icon icon="ph:caret-right-bold"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="scrollable-profiles-container">
                                    <div class="profiles-scroll-row" id="scroll-recommended">
                                        @foreach ($recommedMatchesMember as $item)
                                            @php
                                                $canView = _canViewMemberPhoto($item, $item->hasPhotoRequestAccess);
                                                $hasPhoto = _checkPhotoExist($item);
                                                $profileImage = _getMemberProfileImage($item);

                                                ## Online Status:
                                                $onlineStatus = _memberOnlineStatus($item);
                                            @endphp
                                            <article class="match-profile-card">
                                                <div class="profile-photo-container">
                                                    @if ($item->plan_status == 'Paid')
                                                        <div class="premium-icon-dash"><iconify-icon
                                                                icon="solar:crown-bold"></iconify-icon></div>
                                                    @endif
                                                    @if ($onlineStatus['status_code'] == 'online')
                                                        <div class="online-blinking-dot"></div>
                                                    @endif
                                                    <a href="{{ route('web.userProfile.index', _encrypt($item->id)) }}">
                                                        @if (!$canView && $hasPhoto)
                                                            <img src="{{ _getProtectedImage($item->gender) }}"
                                                                alt="{{ _profileTitle($item) }}"
                                                                style="object-position: center 20%;">
                                                        @else
                                                            <img src="{{ $profileImage }}"
                                                                alt="{{ _profileTitle($item) }}"
                                                                style="object-position: center 20%;">
                                                        @endif
                                                    </a>
                                                    <div class="card-match-score-pill">
                                                        <iconify-icon icon="ph:lightning-fill"></iconify-icon>
                                                        <span>{{ $item->matchPercent }}%
                                                            {{ __('messages.lbl_match') }}</span>
                                                    </div>
                                                </div>
                                                <div class="profile-card-content">
                                                    <h3 class="profile-name-age">{{ _profileTitle($item) }}</h3>
                                                    <div class="profile-interest-line">
                                                        <iconify-icon icon="bxs:user-detail"></iconify-icon>
                                                        <span>{{ _profileSubTitle($item) }}</span>
                                                    </div>
                                                    {{-- <div class="profile-tag-badges-row">
                                                        <span class="profile-tag-pill compat-green">• High Compatibility</span>
                                                    </div> --}}
                                                    <div class="profile-card-action-row">
                                                        <a href="{{ route('web.userProfile.index', _encrypt($item->id)) }}"
                                                            class="btn-card-connect">
                                                            <iconify-icon icon="ph:paper-plane-tilt-bold"></iconify-icon>
                                                            {{ __('messages.lbl_view_profile') }}
                                                        </a>
                                                        {{-- <button class="btn-card-shortlist">
                                                            <iconify-icon icon="ph:bookmark-simple"></iconify-icon> Shortlist
                                                        </button> --}}
                                                    </div>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        @endif
                        @if ($recentlyJoinedMember->isNotEmpty())
                            <section class="profile-section-block" id="section-recently-joined"
                                aria-label="{{ __('messages.lbl_recently_joined_members') }}">
                                <div class="profile-section-header">
                                    <div class="section-title-wrap">
                                        <div class="section-title-icon purple">
                                            <iconify-icon icon="ph:clock-countdown-fill"></iconify-icon>
                                        </div>
                                        <div class="section-title-text-group">
                                            <div class="section-title-top-row">
                                                <h3 class="section-main-heading">
                                                    {{ __('messages.lbl_recently_joined_members') }}</h3>
                                                {{-- <span class="section-tag-pill badge-blue">★ 98% Vedic Match</span> --}}
                                            </div>
                                            <span
                                                class="section-subtitle-line">{{ __('messages.lbl_recently_joined_members_subtitle') }}</span>
                                        </div>
                                    </div>
                                    <div class="section-header-controls">
                                        <div class="scroll-arrow-buttons">
                                            <button class="btn-scroll-arrow" data-target="scroll-recently-joined"
                                                data-direction="left" aria-label="Scroll Left">
                                                <iconify-icon icon="ph:caret-left-bold"></iconify-icon>
                                            </button>
                                            <button class="btn-scroll-arrow" data-target="scroll-recently-joined"
                                                data-direction="right" aria-label="Scroll Right">
                                                <iconify-icon icon="ph:caret-right-bold"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="scrollable-profiles-container">
                                    <div class="profiles-scroll-row" id="scroll-recently-joined">
                                        @foreach ($recentlyJoinedMember as $item)
                                            @php
                                                $canView = _canViewMemberPhoto($item, $item->hasPhotoRequestAccess);
                                                $hasPhoto = _checkPhotoExist($item);
                                                $profileImage = _getMemberProfileImage($item);

                                                ## Online Status:
                                                $onlineStatus = _memberOnlineStatus($item);
                                            @endphp
                                            <article class="match-profile-card">
                                                <div class="profile-photo-container">
                                                    @if ($item->plan_status == 'Paid')
                                                        <div class="premium-icon-dash"><iconify-icon
                                                                icon="solar:crown-bold"></iconify-icon></div>
                                                    @endif
                                                    @if ($onlineStatus['status_code'] == 'online')
                                                        <div class="online-blinking-dot"></div>
                                                    @endif
                                                    <a href="{{ route('web.userProfile.index', _encrypt($item->id)) }}">
                                                        @if (!$canView && $hasPhoto)
                                                            <img src="{{ _getProtectedImage($item->gender) }}"
                                                                alt="{{ _profileTitle($item) }}"
                                                                style="object-position: center 20%;">
                                                        @else
                                                            <img src="{{ $profileImage }}"
                                                                alt="{{ _profileTitle($item) }}"
                                                                style="object-position: center 20%;">
                                                        @endif
                                                    </a>
                                                    <div class="card-match-score-pill">
                                                        <iconify-icon icon="ph:lightning-fill"></iconify-icon>
                                                        <span>{{ $item->matchPercent }}%
                                                            {{ __('messages.lbl_match') }}</span>
                                                    </div>
                                                </div>
                                                <div class="profile-card-content">
                                                    <h3 class="profile-name-age">{{ _profileTitle($item) }}</h3>
                                                    <div class="profile-interest-line">
                                                        <iconify-icon icon="bxs:user-detail"></iconify-icon>
                                                        <span>{{ _profileSubTitle($item) }}</span>
                                                    </div>
                                                    {{-- <div class="profile-tag-badges-row">
                                                        <span class="profile-tag-pill compat-green">• High Compatibility</span>
                                                    </div> --}}
                                                    <div class="profile-card-action-row">
                                                        <a href="{{ route('web.userProfile.index', _encrypt($item->id)) }}"
                                                            class="btn-card-connect">
                                                            <iconify-icon icon="ph:paper-plane-tilt-bold"></iconify-icon>
                                                            {{ __('messages.lbl_view_profile') }}
                                                        </a>
                                                        {{-- <button class="btn-card-shortlist">
                                                            <iconify-icon icon="ph:bookmark-simple"></iconify-icon> Shortlist
                                                        </button> --}}
                                                    </div>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        @endif
                        @if ($recentlyLoginMember->isNotEmpty())
                            <section class="profile-section-block" id="section-recently-login"
                                aria-label="{{ __('messages.lbl_recently_logged_in_members') }}">
                                <div class="profile-section-header">
                                    <div class="section-title-wrap">
                                        <div class="section-title-icon gold">
                                            <iconify-icon icon="ph:users-three-fill"></iconify-icon>
                                        </div>
                                        <div class="section-title-text-group">
                                            <div class="section-title-top-row">
                                                <h3 class="section-main-heading">
                                                    {{ __('messages.lbl_recently_logged_in_members') }}</h3>
                                                {{-- <span class="section-tag-pill badge-blue">★ 98% Vedic Match</span> --}}
                                            </div>
                                            <span
                                                class="section-subtitle-line">{{ __('messages.lbl_recently_logged_in_members_subtitle') }}</span>
                                        </div>
                                    </div>
                                    <div class="section-header-controls">
                                        <div class="scroll-arrow-buttons">
                                            <button class="btn-scroll-arrow" data-target="scroll-recently-joined"
                                                data-direction="left" aria-label="Scroll Left">
                                                <iconify-icon icon="ph:caret-left-bold"></iconify-icon>
                                            </button>
                                            <button class="btn-scroll-arrow" data-target="scroll-recently-joined"
                                                data-direction="right" aria-label="Scroll Right">
                                                <iconify-icon icon="ph:caret-right-bold"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="scrollable-profiles-container">
                                    <div class="profiles-scroll-row" id="scroll-recently-joined">
                                        @foreach ($recentlyLoginMember as $item)
                                            @php
                                                $canView = _canViewMemberPhoto($item, $item->hasPhotoRequestAccess);
                                                $hasPhoto = _checkPhotoExist($item);
                                                $profileImage = _getMemberProfileImage($item);

                                                ## Online Status:
                                                $onlineStatus = _memberOnlineStatus($item);
                                            @endphp
                                            <article class="match-profile-card">
                                                <div class="profile-photo-container">
                                                    @if ($item->plan_status == 'Paid')
                                                        <div class="premium-icon-dash"><iconify-icon
                                                                icon="solar:crown-bold"></iconify-icon></div>
                                                    @endif
                                                    @if ($onlineStatus['status_code'] == 'online')
                                                        <div class="online-blinking-dot"></div>
                                                    @endif
                                                    <a href="{{ route('web.userProfile.index', _encrypt($item->id)) }}">
                                                        @if (!$canView && $hasPhoto)
                                                            <img src="{{ _getProtectedImage($item->gender) }}"
                                                                alt="{{ _profileTitle($item) }}"
                                                                style="object-position: center 20%;">
                                                        @else
                                                            <img src="{{ $profileImage }}"
                                                                alt="{{ _profileTitle($item) }}"
                                                                style="object-position: center 20%;">
                                                        @endif
                                                    </a>
                                                    <div class="card-match-score-pill">
                                                        <iconify-icon icon="ph:lightning-fill"></iconify-icon>
                                                        <span>{{ $item->matchPercent }}%
                                                            {{ __('messages.lbl_match') }}</span>
                                                    </div>
                                                </div>
                                                <div class="profile-card-content">
                                                    <h3 class="profile-name-age">{{ _profileTitle($item) }}</h3>
                                                    <div class="profile-interest-line">
                                                        <iconify-icon icon="bxs:user-detail"></iconify-icon>
                                                        <span>{{ _profileSubTitle($item) }}</span>
                                                    </div>
                                                    {{-- <div class="profile-tag-badges-row">
                                                        <span class="profile-tag-pill compat-green">• High Compatibility</span>
                                                    </div> --}}
                                                    <div class="profile-card-action-row">
                                                        <a href="{{ route('web.userProfile.index', _encrypt($item->id)) }}"
                                                            class="btn-card-connect">
                                                            <iconify-icon icon="ph:paper-plane-tilt-bold"></iconify-icon>
                                                            {{ __('messages.lbl_view_profile') }}
                                                        </a>
                                                        {{-- <button class="btn-card-shortlist">
                                                            <iconify-icon icon="ph:bookmark-simple"></iconify-icon> Shortlist
                                                        </button> --}}
                                                    </div>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        @endif
                    </main>
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
                            <a href="{{ route('web.fixMeetings.index') }}"
                                class="click-changeButton">{{ __('messages.lbl_view_meeting_details') }}</a>
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

        $('#autoModeToggle').on('click', function() {

            let toggle = $(this);
            let autoMode = toggle.attr('aria-checked') === 'true' ? 0 : 1;

            toggle.css('pointer-events', 'none');

            $.ajax({
                url: "{{ route('web.aiAutoInterest.toggle') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    auto_interest_enabled: autoMode
                },

                beforeSend: function() {
                    toggle.addClass('loading');
                },

                success: function(response) {

                    showToastMessage(
                        response.status ? 'success' : 'error',
                        response.message
                    );

                    if (response.status) {
                        toggle.attr('aria-checked', autoMode === 1 ? 'true' : 'false');

                        toggle.find('.toggle-status-text').text(
                            autoMode === 1 ?
                            "{{ __('messages.lbl_on') }}" :
                            "{{ __('messages.lbl_off') }}"
                        );

                        toggle.toggleClass('toggle-switch-on', autoMode === 1);
                        toggle.toggleClass('toggle-switch-off', autoMode === 0);
                    }
                },

                error: function() {
                    showToastMessage(
                        'error',
                        'Something went wrong. Please try again.'
                    );
                },

                complete: function() {
                    toggle.removeClass('loading');
                    toggle.css('pointer-events', '');
                }
            });
        });
    </script>
@endpush

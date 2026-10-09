<header class="matrion-navbar">
    <div class="nav-left">
        <!-- Mobile Sidebar Hamburger Toggle -->
        <button class="mobile-sidebar-toggle" id="mobile-sidebar-toggle" aria-label="Toggle Navigation Menu"
            title="Open Menu">
            <iconify-icon icon="ph:list-bold"></iconify-icon>
        </button>

        <!-- Brand Logo -->
        <a href="{{ url('/') }}" class="brand-logo-text" id="brand-logo">
            <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                alt="{{ $configArr['web_name'] }}" class="brand-logo">
        </a>
    </div>

    <!-- Main Navigation Menu (Centered) -->
    <nav class="nav-center" aria-label="Main Navigation">
        <ul class="nav-links">
            @guest('web')
                <li>
                    <a href="{{ url('/') }}" class="nav-link-btn {{ request()->is('/') ? 'active' : '' }}"
                        id="nav-dashboard">
                        <iconify-icon icon="ph:squares-four-fill"></iconify-icon> {{ __('messages.lbl_home') }}
                    </a>
                </li>
                <!-- Membership Dropdown Menu -->
                <li class="nav-item-dropdown {{ request()->routeIs('web.membershipPlan.*') ? 'nav-active' : '' }}"
                    id="nav-membership-wrapper">
                    <button type="button" class="nav-link-btn dropdown-toggle-btn" id="nav-membership-btn"
                        aria-expanded="false" aria-haspopup="true">
                        <iconify-icon icon="ph:crown-simple-bold"></iconify-icon>
                        {{ __('messages.lbl_membership') }}
                        <iconify-icon icon="ph:caret-down-bold" class="nav-caret"></iconify-icon>
                    </button>
                    <!-- Membership Dropdown Popover -->
                    <div class="dropdown-popover membership-popover" id="membership-dropdown" role="menu">
                        <div class="popover-header">
                            <div class="popover-header-title-wrap">
                                <span class="popover-title">{{ __('messages.lbl_membership') }}</span>
                                <span class="popover-header-sub">{{ __('messages.lbl_plans_active_subscription') }}</span>
                            </div>
                        </div>
                        <div class="matches-popover-list">
                            {{-- Membership Plans --}}
                            <a href="{{ route('web.membershipPlan.index') }}"
                                class="popover-item-link featured {{ _navActive(['web.membershipPlan.*']) }}"
                                role="menuitem">
                                <div class="item-icon-box icon-gold">
                                    <iconify-icon icon="ph:crown-simple-fill"></iconify-icon>
                                </div>
                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">{{ __('messages.lbl_plans') }}</span>
                                        {{-- <span class="item-pill-badge badge-gradient-ai">
                                            {{ __('messages.lbl_upgrade') }}
                                        </span> --}}
                                    </div>
                                    <span class="item-subtext">
                                        {{ __('messages.lbl_browse_membership_packages') }}
                                    </span>
                                </div>
                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a>
                        </div>
                        <div class="popover-footer">
                            <a href="{{ route('web.membershipPlan.index') }}" class="popover-footer-btn">
                                <iconify-icon icon="ph:sparkle-bold"></iconify-icon>
                                <span>
                                    {{ __('messages.lbl_explore_plans') }}
                                </span>
                                <iconify-icon icon="ph:arrow-right-bold" class="footer-arrow"></iconify-icon>
                            </a>
                        </div>
                    </div>
                </li>
                <li>
                    <a href="{{ route('web.successStory.index') }}"
                        class="nav-link-btn {{ _navActive(['web.successStory.*']) }}">
                        <iconify-icon icon="ph:heart-straight-bold"></iconify-icon>
                        {{ __('messages.lbl_success_stories') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('web.contactUs.index') }}"
                        class="nav-link-btn {{ _navActive(['web.contactUs.*']) }}">
                        <iconify-icon icon="ph:chats-circle-bold"></iconify-icon>
                        {{ __('messages.lbl_contact_us') }}
                    </a>
                </li>
            @endguest
            @if (Auth::check())
                @php
                    $authUser = Auth::guard('web')->user();
                    $profileImage = _getMemberProfileImage($authUser, 'Yes');
                @endphp
                <li>
                    <a href="{{ route('web.dashboard.index') }}"
                        class="nav-link-btn {{ _navActive(['web.dashboard.*']) }}" id="nav-dashboard">
                        <iconify-icon icon="ph:squares-four-fill"></iconify-icon> {{ __('messages.lbl_dashboard') }}
                    </a>
                </li>
                <!-- Search Dropdown Menu -->
                <li class="nav-item-dropdown {{ request()->routeIs('web.search.*') ? 'nav-active' : '' }}"
                    id="nav-search-wrapper">
                    <button type="button" class="nav-link-btn dropdown-toggle-btn" id="nav-search-btn"
                        aria-expanded="false" aria-haspopup="true">
                        <iconify-icon icon="ph:magnifying-glass-bold"></iconify-icon> {{ __('messages.lbl_search') }}
                        <iconify-icon icon="ph:caret-down-bold" class="nav-caret"></iconify-icon>
                    </button>
                    <!-- Search Dropdown Popover -->
                    <div class="dropdown-popover search-popover" id="search-dropdown" role="menu">
                        <div class="popover-header">
                            <div class="popover-header-title-wrap">
                                <span class="popover-title">{{ __('messages.lbl_search_profiles') }}</span>
                                <span
                                    class="popover-header-sub">{{ __('messages.lbl_find_matches_by_custom_filters') }}</span>
                            </div>
                            <span class="popover-badge-accent">{{ __('messages.lbl_4_modes') }}</span>
                        </div>

                        <div class="matches-popover-list">
                            <a href="{{ route('web.search.type', ['type' => 'quick-search']) }}"
                                class="popover-item-link {{ _navActiveParam('web.search.type', 'type', 'quick-search') }}"
                                role="menuitem">
                                <div class="item-icon-box icon-blue">
                                    <iconify-icon icon="ph:magnifying-glass-fill"></iconify-icon>
                                </div>
                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">{{ __('messages.lbl_quick_search') }}</span>
                                        {{-- <span class="item-pill-badge">{{ __('messages.lbl_fast_filter') }}</span> --}}
                                    </div>
                                    <span class="item-subtext">{{ __('messages.lbl_quick_search_description') }}</span>
                                </div>
                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a>

                            <a href="{{ route('web.search.type', ['type' => 'advance-search']) }}"
                                class="popover-item-link {{ _navActiveParam('web.search.type', 'type', 'advance-search') }}"
                                role="menuitem">
                                <div class="item-icon-box icon-gold">
                                    <iconify-icon icon="ph:sliders-horizontal-fill"></iconify-icon>
                                </div>
                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">{{ __('messages.lbl_advance_search') }}</span>
                                        <span
                                            class="item-pill-badge badge-gold">{{ __('messages.lbl_deep_filters') }}</span>
                                    </div>
                                    <span class="item-subtext">{{ __('messages.lbl_advance_search_description') }}</span>
                                </div>
                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a>

                            <a href="{{ route('web.search.type', ['type' => 'keyword-search']) }}"
                                class="popover-item-link  {{ _navActiveParam('web.search.type', 'type', 'keyword-search') }}"
                                role="menuitem">
                                <div class="item-icon-box icon-purple">
                                    <iconify-icon icon="ph:text-t-bold"></iconify-icon>
                                </div>
                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">{{ __('messages.lbl_keyword_search') }}</span>
                                        {{-- <span class="item-pill-badge">{{ __('messages.lbl_specific') }}</span> --}}
                                    </div>
                                    <span
                                        class="item-subtext">{{ __('messages.lbl_find_profiles_by_interests_words_hobbies') }}</span>
                                </div>
                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a>

                            <a href="{{ route('web.search.type', ['type' => 'id-search']) }}"
                                class="popover-item-link {{ _navActiveParam('web.search.type', 'type', 'id-search') }}"
                                role="menuitem">
                                <div class="item-icon-box icon-green">
                                    <iconify-icon icon="ph:identification-card-fill"></iconify-icon>
                                </div>
                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">{{ __('messages.lbl_id_search') }}</span>
                                        {{-- <span class="item-pill-badge badge-green">{{ __('messages.lbl_direct') }}</span> --}}
                                    </div>
                                    <span
                                        class="item-subtext">{{ __('messages.lbl_locate_a_candidate_by_member_profile_id') }}</span>
                                </div>
                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a>
                        </div>

                        <div class="popover-footer">
                            <a href="{{ route('web.search.type', ['type' => 'quick-search']) }}"
                                class="popover-footer-btn">
                                <iconify-icon icon="ph:sliders-bold"></iconify-icon>
                                <span>{{ __('messages.lbl_open_search_directory') }}</span>
                                <iconify-icon icon="ph:arrow-right-bold" class="footer-arrow"></iconify-icon>
                            </a>
                        </div>
                    </div>
                </li>
                <!-- Activities Dropdown Menu -->
                <li class="nav-item-dropdown {{ request()->routeIs(
                    'web.expressInterest.index',
                    'web.shortlist.index',
                    'web.photoRequest.index',
                    'web.viewedProfile.index',
                    'web.viewedContact.index',
                    'web.blocklist.index',
                    'web.videoVoiceCall.index',
                )
                    ? 'nav-active'
                    : '' }}"
                    id="nav-activity-wrapper">
                    <button type="button" class="nav-link-btn dropdown-toggle-btn" id="nav-activity-btn"
                        aria-expanded="false" aria-haspopup="true">
                        <iconify-icon icon="ph:chart-line-up-bold"></iconify-icon>
                        {{ __('messages.lbl_activity') }}
                        <iconify-icon icon="ph:caret-down-bold" class="nav-caret">
                        </iconify-icon>
                    </button>
                    <!-- Activities Dropdown Popover -->
                    <div class="dropdown-popover activity-popover" id="activity-dropdown" role="menu">
                        <div class="popover-header">
                            <div class="popover-header-title-wrap">
                                <span class="popover-title">
                                    {{ __('messages.lbl_my_activities') }}
                                </span>
                                <span class="popover-header-sub">
                                    {{ __('messages.lbl_track_interactions_connections') }}
                                </span>
                            </div>
                            {{-- <span class="popover-badge-accent">
                                {{ __('messages.lbl_7_options') }}
                            </span> --}}
                        </div>
                        <div class="matches-popover-list popover-scroll-list">
                            {{-- Shortlisted Profile --}}
                            <a href="{{ route('web.shortlist.index') }}"
                                class="popover-item-link {{ _navActive(['web.shortlist.index']) }}" role="menuitem">
                                <div class="item-icon-box icon-pink">
                                    <iconify-icon icon="ph:bookmark-simple-fill"></iconify-icon>
                                </div>
                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">
                                            {{ __('messages.lbl_shortlist_profile') }}
                                        </span>
                                        {{-- <span class="item-pill-badge">
                                            4 {{ __('messages.lbl_saved') }}
                                        </span> --}}
                                    </div>
                                    <span class="item-subtext">
                                        {{ __('messages.lbl_profiles_bookmarked_consideration') }}
                                    </span>
                                </div>
                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a>


                            {{-- Express Interest --}}
                            <a href="{{ route('web.expressInterest.index', ['type' => 'interest_sent']) }}"
                                class="popover-item-link {{ _navActive(['web.expressInterest.index']) }}"
                                role="menuitem">

                                <div class="item-icon-box icon-blue">
                                    <iconify-icon icon="ph:paper-plane-tilt-fill"></iconify-icon>
                                </div>

                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">
                                            {{ __('messages.lbl_express_interest') }}
                                        </span>

                                        {{-- <span class="item-pill-badge">
                                            12 {{ __('messages.lbl_active') }}
                                        </span> --}}
                                    </div>

                                    <span class="item-subtext">
                                        {{ __('messages.lbl_manage_incoming_outgoing_interests') }}
                                    </span>
                                </div>

                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron">
                                </iconify-icon>
                            </a>


                            {{-- Photo Request --}}
                            <a href="{{ route('web.photoRequest.index', ['type' => 'request_sent']) }}"
                                class="popover-item-link {{ _navActive(['web.photoRequest.index']) }}"
                                role="menuitem">

                                <div class="item-icon-box icon-purple">
                                    <iconify-icon icon="ph:image-fill"></iconify-icon>
                                </div>

                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">
                                            {{ __('messages.lbl_photo_request') }}
                                        </span>

                                        {{-- <span class="item-pill-badge badge-purple">
                                            3 {{ __('messages.lbl_requests') }}
                                        </span> --}}
                                    </div>

                                    <span class="item-subtext">
                                        {{ __('messages.lbl_requested_received_photo_approvals') }}
                                    </span>
                                </div>

                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron">
                                </iconify-icon>
                            </a>


                            {{-- Who Viewed My Profile --}}
                            <a href="{{ route('web.viewedProfile.index', ['type' => 'who_viewed']) }}"
                                class="popover-item-link {{ _navActive(['web.viewedProfile.index']) }}"
                                role="menuitem">

                                <div class="item-icon-box icon-gold">
                                    <iconify-icon icon="ph:eye-fill"></iconify-icon>
                                </div>

                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">
                                            {{ __('messages.lbl_who_viewed_profile') }}
                                        </span>

                                        {{-- <span class="item-pill-badge">
                                            18 {{ __('messages.lbl_visitors') }}
                                        </span> --}}
                                    </div>

                                    <span class="item-subtext">
                                        {{ __('messages.lbl_members_checked_profile_recently') }}
                                    </span>
                                </div>

                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron">
                                </iconify-icon>
                            </a>


                            {{-- Who Viewed My Contact --}}
                            <a href="{{ route('web.viewedContact.index', ['type' => 'who_viewed']) }}"
                                class="popover-item-link {{ _navActive(['web.viewedContact.index']) }}"
                                role="menuitem">

                                <div class="item-icon-box icon-green">
                                    <iconify-icon icon="ph:phone-call-fill"></iconify-icon>
                                </div>

                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">
                                            {{ __('messages.lbl_who_viewed_contact') }}
                                        </span>

                                        {{-- <span class="item-pill-badge badge-green">
                                            6 {{ __('messages.lbl_members') }}
                                        </span> --}}
                                    </div>

                                    <span class="item-subtext">
                                        {{ __('messages.lbl_profiles_viewed_contact_details') }}
                                    </span>
                                </div>

                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron">
                                </iconify-icon>
                            </a>


                            {{-- Block Profiles --}}
                            <a href="{{ route('web.blocklist.index') }}"
                                class="popover-item-link {{ _navActive(['web.blocklist.index']) }}" role="menuitem">

                                <div class="item-icon-box icon-slate">
                                    <iconify-icon icon="ph:prohibit-bold"></iconify-icon>
                                </div>

                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">
                                            {{ __('messages.lbl_blocklist_profile') }}
                                        </span>

                                        {{-- <span class="item-pill-badge">
                                            3 {{ __('messages.lbl_blocked') }}
                                        </span> --}}
                                    </div>

                                    <span class="item-subtext">
                                        {{ __('messages.lbl_manage_restricted_ignored_accounts') }}
                                    </span>
                                </div>

                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron">
                                </iconify-icon>
                            </a>


                            {{-- Call History --}}
                            <a href="{{ route('web.videoVoiceCall.index') }}"
                                class="popover-item-link {{ _navActive(['web.videoVoiceCall.index']) }}"
                                role="menuitem">

                                <div class="item-icon-box icon-teal">
                                    <iconify-icon icon="ph:clock-counter-clockwise-bold"></iconify-icon>
                                </div>

                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">
                                            {{ __('messages.lbl_call_history') }}
                                        </span>

                                        {{-- <span class="item-pill-badge">
                                            14 {{ __('messages.lbl_logs') }}
                                        </span> --}}
                                    </div>

                                    <span class="item-subtext">
                                        {{ __('messages.lbl_voice_meeting_video_call_history') }}
                                    </span>
                                </div>

                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron">
                                </iconify-icon>
                            </a>
                        </div>
                        <div class="popover-footer">
                            <a href="{{ route('web.myProfile.index') }}" class="popover-footer-btn">
                                <iconify-icon icon="ph:chart-line-bold"></iconify-icon>
                                <span>
                                    {{ __('messages.lbl_view_profile') }}
                                </span>
                                <iconify-icon icon="ph:arrow-right-bold" class="footer-arrow">
                                </iconify-icon>
                            </a>
                        </div>

                    </div>
                </li>
                <!-- Matches Dropdown Menu -->
                <li class="nav-item-dropdown {{ request()->routeIs('web.matches.*') ? 'nav-active' : '' }}"
                    id="nav-matches-wrapper">

                    <button type="button" class="nav-link-btn dropdown-toggle-btn" id="nav-matches-btn"
                        aria-expanded="false" aria-haspopup="true">

                        <iconify-icon icon="ph:heart-straight-bold"></iconify-icon>

                        {{ __('messages.lbl_matches') }}

                        <iconify-icon icon="ph:caret-down-bold" class="nav-caret"></iconify-icon>
                    </button>

                    <!-- Matches Dropdown Menu -->
                    <div class="dropdown-popover matches-popover" id="matches-dropdown" role="menu">

                        <div class="popover-header">
                            <div class="popover-header-title-wrap">

                                <span class="popover-title">
                                    {{ __('messages.lbl_curated_matches') }}
                                </span>

                                <span class="popover-header-sub">
                                    {{ __('messages.lbl_tailored_to_preferences') }}
                                </span>

                            </div>
                            {{-- 
                            <span class="popover-badge-accent">
                                {{ __('messages.lbl_active_matches', ['count' => 1428]) }}
                            </span> --}}
                        </div>

                        <div class="matches-popover-list">

                            {{-- AI Recommendations --}}
                            {{-- <a href="{{ route('web.aiMatchMaking.index') }}"
                                class="popover-item-link {{ _navActive(['web.aiMatchMaking.index']) }} featured"
                                role="menuitem">

                                <div class="item-icon-box icon-gold">
                                    <iconify-icon icon="ph:sparkle-fill"></iconify-icon>
                                </div>

                                <div class="item-body">
                                    <div class="item-top-row">

                                        <span class="item-name">
                                            {{ __('messages.lbl_ai_recommendations') }}
                                        </span>
                                    </div>

                                    <span class="item-subtext">
                                        {{ __('messages.lbl_vedic_lifestyle_compatibility') }}
                                    </span>
                                </div>

                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a> --}}


                            {{-- All Matches --}}
                            <a href="{{ route('web.matches.recommended') }}"
                                class="popover-item-link {{ _navActive(['web.matches.recommended']) }}"
                                role="menuitem">

                                <div class="item-icon-box icon-blue">
                                    <iconify-icon icon="ph:users-three-fill"></iconify-icon>
                                </div>

                                <div class="item-body">
                                    <div class="item-top-row">

                                        <span class="item-name">
                                            {{ __('messages.lbl_recommended_matches') }}
                                        </span>

                                        {{-- <span class="item-pill-badge">
                                            {{ __('messages.lbl_total_matches', ['count' => '1.4k']) }}
                                        </span> --}}

                                    </div>

                                    <span class="item-subtext">
                                        {{ __('messages.lbl_browse_all_recommed_matches') }}
                                    </span>
                                </div>

                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a>


                            {{-- Premium Matches --}}
                            <a href="{{ route('web.matches.premium') }}"
                                class="popover-item-link {{ _navActive(['web.matches.premium']) }}" role="menuitem">

                                <div class="item-icon-box icon-purple">
                                    <iconify-icon icon="ph:crown-simple-fill"></iconify-icon>
                                </div>

                                <div class="item-body">
                                    <div class="item-top-row">

                                        <span class="item-name">
                                            {{ __('messages.lbl_premium_matches') }}
                                        </span>

                                        {{-- <span class="item-pill-badge badge-purple">
                                            {{ __('messages.lbl_premium') }}
                                        </span> --}}

                                    </div>

                                    <span class="item-subtext">
                                        {{ __('messages.lbl_premium_compatible_profiles') }}
                                    </span>
                                </div>

                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a>


                            {{-- Nearby Matches --}}
                            <a href="{{ route('web.matches.nearByMe') }}"
                                class="popover-item-link {{ _navActive(['web.matches.nearByMe']) }}" role="menuitem">

                                <div class="item-icon-box icon-green">
                                    <iconify-icon icon="ph:map-pin-fill"></iconify-icon>
                                </div>

                                <div class="item-body">
                                    <div class="item-top-row">

                                        <span class="item-name">
                                            {{ __('messages.lbl_nearby_matches') }}
                                        </span>

                                        {{-- <span class="item-pill-badge badge-green">
                                            {{ __('messages.lbl_nearby_matches_count', ['count' => 24]) }}
                                        </span> --}}

                                    </div>

                                    <span class="item-subtext">
                                        {{ __('messages.lbl_compatible_singles_nearby') }}
                                    </span>
                                </div>

                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a>


                            @if ($authUser->user_type == 0)
                                {{-- Suggested Matches --}}
                                <a href="{{ route('web.matches.suggested') }}"
                                    class="popover-item-link {{ _navActive(['web.matches.suggested']) }}"
                                    role="menuitem">

                                    <div class="item-icon-box icon-pink">
                                        <iconify-icon icon="ph:magic-wand-fill"></iconify-icon>
                                    </div>

                                    <div class="item-body">
                                        <div class="item-top-row">

                                            <span class="item-name">
                                                {{ __('messages.lbl_suggested_matches') }}
                                            </span>

                                        </div>

                                        <span class="item-subtext">
                                            {{ __('messages.lbl_personalized_match_suggestions') }}
                                        </span>
                                    </div>

                                    <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                                </a>
                            @else
                                {{-- Admin Suggested Matches --}}
                                <a href="{{ route('web.receiveAdminMatch.index') }}"
                                    class="popover-item-link {{ _navActive(['web.receiveAdminMatch.index']) }}"
                                    role="menuitem">

                                    <div class="item-icon-box icon-pink">
                                        <iconify-icon icon="ph:user-focus-fill"></iconify-icon>
                                    </div>

                                    <div class="item-body">
                                        <div class="item-top-row">

                                            <span class="item-name">
                                                {{ __('messages.lbl_admin_matches') }}
                                            </span>

                                        </div>

                                        <span class="item-subtext">
                                            {{ __('messages.lbl_matches_from_admin') }}
                                        </span>
                                    </div>

                                    <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                                </a>
                            @endif


                            {{-- My Meetings --}}
                            @if ($authUser->user_type == 1 || $fixMeetingCount > 0)
                                <a href="{{ route('web.fixMeetings.index') }}"
                                    class="popover-item-link {{ _navActive(['web.fixMeetings.index']) }}"
                                    role="menuitem">

                                    <div class="item-icon-box icon-orange">
                                        <iconify-icon icon="ph:calendar-heart-fill"></iconify-icon>
                                    </div>

                                    <div class="item-body">
                                        <div class="item-top-row">

                                            <span class="item-name">
                                                {{ __('messages.lbl_my_meetings') }}
                                            </span>

                                        </div>

                                        <span class="item-subtext">
                                            {{ __('messages.lbl_manage_scheduled_meetings') }}
                                        </span>
                                    </div>

                                    <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                                </a>
                            @endif


                            {{-- Chat With Admin --}}
                            @if ($authUser->user_type == 1)
                                <a href="{{ route('web.personalizeChat.index') }}"
                                    class="popover-item-link {{ _navActive(['web.personalizeChat.index']) }}"
                                    role="menuitem">

                                    <div class="item-icon-box icon-blue">
                                        <iconify-icon icon="ph:chat-circle-dots-fill"></iconify-icon>
                                    </div>

                                    <div class="item-body">
                                        <div class="item-top-row">

                                            <span class="item-name">
                                                {{ __('messages.lbl_chat_with_admin') }}
                                            </span>

                                        </div>

                                        <span class="item-subtext">
                                            {{ __('messages.lbl_get_assistance_from_admin') }}
                                        </span>
                                    </div>

                                    <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                                </a>
                            @endif

                        </div>


                        {{-- Footer --}}
                        <div class="popover-footer">

                            <a href="{{ route('web.matches.recommended') }}" class="popover-footer-btn">

                                <iconify-icon icon="ph:sliders-horizontal-bold"></iconify-icon>

                                <span>
                                    {{ __('messages.lbl_view_all_matches') }}
                                </span>

                                <iconify-icon icon="ph:arrow-right-bold" class="footer-arrow"></iconify-icon>

                            </a>

                        </div>

                    </div>
                </li>
                @if (Auth::check() && $configArr['chat_module_design'] == 'window')
                    <li>
                        <a href="{{ route('web.chat.index') }}"
                            class="nav-link-btn {{ _navActive(['web.chat.*']) }}">
                            <iconify-icon icon="ph:chat-circle-dots-bold"></iconify-icon>
                            {{ __('messages.lbl_chat') }}
                        </a>
                    </li>
                @endif
                <!-- Search Dropdown Menu -->
                <li class="nav-item-dropdown {{ request()->routeIs('web.search.*') ? 'nav-active' : '' }}"
                    id="nav-search-wrapper">
                    <button type="button" class="nav-link-btn dropdown-toggle-btn" id="nav-search-btn"
                        aria-expanded="false" aria-haspopup="true">
                        <iconify-icon icon="ph:magnifying-glass-bold"></iconify-icon> {{ __('messages.lbl_search') }}
                        <iconify-icon icon="ph:caret-down-bold" class="nav-caret"></iconify-icon>
                    </button>
                    <!-- Search Dropdown Popover -->
                    <div class="dropdown-popover search-popover" id="search-dropdown" role="menu">
                        <div class="popover-header">
                            <div class="popover-header-title-wrap">
                                <span class="popover-title">{{ __('messages.lbl_search_profiles') }}</span>
                                <span
                                    class="popover-header-sub">{{ __('messages.lbl_find_matches_by_custom_filters') }}</span>
                            </div>
                            <span class="popover-badge-accent">{{ __('messages.lbl_5_modes') }}</span>
                        </div>

                        <div class="matches-popover-list">
                            <a href="{{ route('web.search.type', ['type' => 'quick-search']) }}"
                                class="popover-item-link {{ _navActiveParam('web.search.type', 'type', 'quick-search') }}"
                                role="menuitem">
                                <div class="item-icon-box icon-blue">
                                    <iconify-icon icon="ph:magnifying-glass-fill"></iconify-icon>
                                </div>
                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">{{ __('messages.lbl_quick_search') }}</span>
                                        {{-- <span class="item-pill-badge">{{ __('messages.lbl_fast_filter') }}</span> --}}
                                    </div>
                                    <span
                                        class="item-subtext">{{ __('messages.lbl_quick_search_description') }}</span>
                                </div>
                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a>

                            <a href="{{ route('web.search.type', ['type' => 'advance-search']) }}"
                                class="popover-item-link {{ _navActiveParam('web.search.type', 'type', 'advance-search') }}"
                                role="menuitem">
                                <div class="item-icon-box icon-gold">
                                    <iconify-icon icon="ph:sliders-horizontal-fill"></iconify-icon>
                                </div>
                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">{{ __('messages.lbl_advance_search') }}</span>
                                        <span
                                            class="item-pill-badge badge-gold">{{ __('messages.lbl_deep_filters') }}</span>
                                    </div>
                                    <span
                                        class="item-subtext">{{ __('messages.lbl_advance_search_description') }}</span>
                                </div>
                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a>

                            <a href="{{ route('web.search.type', ['type' => 'keyword-search']) }}"
                                class="popover-item-link  {{ _navActiveParam('web.search.type', 'type', 'keyword-search') }}"
                                role="menuitem">
                                <div class="item-icon-box icon-purple">
                                    <iconify-icon icon="ph:text-t-bold"></iconify-icon>
                                </div>
                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">{{ __('messages.lbl_keyword_search') }}</span>
                                        {{-- <span class="item-pill-badge">{{ __('messages.lbl_specific') }}</span> --}}
                                    </div>
                                    <span
                                        class="item-subtext">{{ __('messages.lbl_find_profiles_by_interests_words_hobbies') }}</span>
                                </div>
                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a>

                            <a href="{{ route('web.search.type', ['type' => 'id-search']) }}"
                                class="popover-item-link {{ _navActiveParam('web.search.type', 'type', 'id-search') }}"
                                role="menuitem">
                                <div class="item-icon-box icon-green">
                                    <iconify-icon icon="ph:identification-card-fill"></iconify-icon>
                                </div>
                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">{{ __('messages.lbl_id_search') }}</span>
                                        <span
                                            class="item-pill-badge badge-green">{{ __('messages.lbl_direct') }}</span>
                                    </div>
                                    <span
                                        class="item-subtext">{{ __('messages.lbl_locate_a_candidate_by_member_profile_id') }}</span>
                                </div>
                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a>

                            <a href="{{ route('web.savedSearch.index') }}"
                                class="popover-item-link {{ _navActive('web.savedSearch.*') }}" role="menuitem">
                                <div class="item-icon-box icon-pink">
                                    <iconify-icon icon="ph:bookmark-simple-fill"></iconify-icon>
                                </div>
                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">{{ __('messages.lbl_saved_search') }}</span>
                                        <span class="item-pill-badge">
                                            @if ($savedCount > 0)
                                                {{ __('messages.lbl_saved', ['count' => $savedCount]) }}
                                            @else
                                                {{ __('messages.lbl_no_saved') }}
                                            @endif
                                        </span>
                                    </div>
                                    <span
                                        class="item-subtext">{{ __('messages.lbl_access_saved_criteria_with_search_alerts') }}</span>
                                </div>
                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a>
                        </div>

                        <div class="popover-footer">
                            <a href="{{ route('web.search.type', ['type' => 'quick-search']) }}"
                                class="popover-footer-btn">
                                <iconify-icon icon="ph:sliders-bold"></iconify-icon>
                                <span>{{ __('messages.lbl_open_search_directory') }}</span>
                                <iconify-icon icon="ph:arrow-right-bold" class="footer-arrow"></iconify-icon>
                            </a>
                        </div>
                    </div>
                </li>
                <!-- Membership Dropdown Menu -->
                <li class="nav-item-dropdown {{ request()->routeIs('web.membershipPlan.*', 'web.currentPlan.index') ? 'nav-active' : '' }}"
                    id="nav-membership-wrapper">
                    <button type="button" class="nav-link-btn dropdown-toggle-btn" id="nav-membership-btn"
                        aria-expanded="false" aria-haspopup="true">
                        <iconify-icon icon="ph:crown-simple-bold"></iconify-icon>
                        {{ __('messages.lbl_membership') }}
                        <iconify-icon icon="ph:caret-down-bold" class="nav-caret"></iconify-icon>
                    </button>
                    <!-- Membership Dropdown Popover -->
                    <div class="dropdown-popover membership-popover" id="membership-dropdown" role="menu">
                        <div class="popover-header">
                            <div class="popover-header-title-wrap">
                                <span class="popover-title">{{ __('messages.lbl_membership') }}</span>
                                <span
                                    class="popover-header-sub">{{ __('messages.lbl_plans_active_subscription') }}</span>
                            </div>
                            {{-- <span class="popover-badge-accent">{{ __('messages.lbl_vip_active') }}</span> --}}
                        </div>
                        <div class="matches-popover-list">
                            {{-- Membership Plans --}}
                            <a href="{{ route('web.membershipPlan.index') }}"
                                class="popover-item-link featured {{ _navActive(['web.membershipPlan.*']) }}"
                                role="menuitem">
                                <div class="item-icon-box icon-gold">
                                    <iconify-icon icon="ph:crown-simple-fill"></iconify-icon>
                                </div>
                                <div class="item-body">
                                    <div class="item-top-row">
                                        <span class="item-name">{{ __('messages.lbl_plans') }}</span>
                                        {{-- <span class="item-pill-badge badge-gradient-ai">
                                            {{ __('messages.lbl_upgrade') }}
                                        </span> --}}
                                    </div>
                                    <span class="item-subtext">
                                        {{ __('messages.lbl_browse_membership_packages') }}
                                    </span>
                                </div>
                                <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                            </a>
                            {{-- Current Plan --}}
                            @if ($authUser->plan_status == 'Paid')
                                <a href="{{ route('web.currentPlan.index') }}"
                                    class="popover-item-link {{ _navActive(['web.currentPlan.index']) }}"
                                    role="menuitem">
                                    <div class="item-icon-box icon-blue">
                                        <iconify-icon icon="ph:receipt-fill"></iconify-icon>
                                    </div>
                                    <div class="item-body">
                                        <div class="item-top-row">
                                            <span class="item-name">
                                                {{ __('messages.lbl_current_plan') }}
                                            </span>
                                            <span class="item-pill-badge">
                                                {{ $authUser->plan_name ?? 'Active' }}
                                            </span>
                                        </div>
                                        <span class="item-subtext">
                                            {{ __('messages.lbl_plan_validity_credits_invoices') }}
                                        </span>
                                    </div>
                                    <iconify-icon icon="ph:caret-right-bold" class="item-chevron"></iconify-icon>
                                </a>
                            @endif
                        </div>
                        <div class="popover-footer">
                            <a href="{{ route('web.membershipPlan.index') }}" class="popover-footer-btn">
                                <iconify-icon icon="ph:sparkle-bold"></iconify-icon>
                                <span>
                                    {{ __('messages.lbl_explore_plans') }}
                                </span>
                                <iconify-icon icon="ph:arrow-right-bold" class="footer-arrow"></iconify-icon>
                            </a>
                        </div>
                    </div>
                </li>
            @endif
        </ul>
    </nav>

    <div class="nav-right">
        @if (!Auth::check())
            <nav class="nav-center d-none d-md-block" aria-label="Main Navigation">
                <ul class="nav-links">
                    <li>
                        <a href="{{ route('web.register.index') }}"
                            class="nav-link-btn {{ _navActive(['web.register.*']) }}">
                            <iconify-icon icon="ph:user-plus-bold"></iconify-icon>
                            {{ __('messages.lbl_register') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('web.login.index') }}" class="nav-link-btn active">
                            <iconify-icon icon="ph:sign-in-bold"></iconify-icon>
                            {{ __('messages.lbl_login') }}
                        </a>
                    </li>
                </ul>
            </nav>
        @endif

        @php
            $getActiveLanguage = _getActiveLanguage();
            $currentLanguage = App::getLocale();
            $currentLang = collect($getActiveLanguage)->firstWhere('lang_code', $currentLanguage);
            $currentLangName = $currentLang->lang_name ?? 'English';
            $currentLangCode = $currentLang->lang_code ?? 'en';
        @endphp

        <!-- Language Change Selection Dropdown -->
        <div class="nav-item-dropdown" id="nav-language-wrapper">
            <button type="button" class="language-selector-btn" id="language-toggle-btn"
                aria-label="{{ __('messages.lbl_change_language') }}" aria-expanded="false" aria-haspopup="true"
                title="{{ __('messages.lbl_change_language') }}">
                <iconify-icon icon="ph:translate-bold"></iconify-icon>
                <span class="lang-curr-code" id="current-lang-code">{{ strtoupper($currentLangCode) }}</span>
                <iconify-icon icon="ph:caret-down-bold" class="lang-caret"></iconify-icon>
            </button>
            <!-- Language Dropdown Menu -->
            <div class="dropdown-popover language-popover" id="language-dropdown" role="menu">
                <div class="popover-header lang-popover-header">
                    <div class="popover-header-title-wrap">
                        <span class="popover-title">
                            {{ __('messages.lbl_select_language') }}
                        </span>
                        <span class="popover-header-sub">
                            {{ __('messages.lbl_choose_display_language') }}
                        </span>
                    </div>
                    <span class="popover-badge-accent">
                        {{ count($getActiveLanguage) }}
                        {{ __('messages.lbl_languages') }}
                    </span>
                </div>
                <div class="lang-options-list" id="lang-options-list">
                    @foreach ($getActiveLanguage as $value)
                        @php
                            $active = $value->lang_code == $currentLanguage ? 'active' : '';
                        @endphp
                        <a class="lang-option-item {{ $active }}"
                            href="{{ route('language.change', $value->lang_code) }}"
                            data-lang="{{ $value->lang_name }}">
                            <div class="lang-item-left">
                                <span class="lang-flag-indicator">
                                    <iconify-icon icon="akar-icons:language"></iconify-icon>
                                </span>

                                <div class="lang-item-text">

                                    <span class="lang-item-native">
                                        {{ $value->lang_name }}
                                    </span>

                                    <span class="lang-item-sub">
                                        {{ strtoupper($value->lang_code) }}
                                    </span>

                                </div>

                            </div>

                            <iconify-icon icon="ph:check-bold" class="lang-item-check">
                            </iconify-icon>

                        </a>
                    @endforeach

                </div>
            </div>
        </div>

        @if (Auth::check())
            @php
                $authUser = Auth::guard('web')->user();
                $profileImage = _getMemberProfileImage($authUser, 'Yes');
            @endphp
            <!-- Notification Toggle Button & Dropdown -->
            <div class="nav-item-dropdown" id="nav-notifications-wrapper">
                <button type="button" class="theme-toggle-btn notification-bell-btn" id="notification-toggle-btn"
                    aria-label="Notifications" aria-expanded="false" aria-haspopup="true" title="Notifications">
                    <iconify-icon icon="ph:bell-fill"></iconify-icon>
                    @if ($navUnreadCount > 0)
                        <span class="nav-notif-badge"
                            id="nav-notif-badge">{{ $navUnreadCount > 9 ? '9+' : $navUnreadCount }}</span>
                    @endif
                </button>

                <!-- Notifications Dropdown Menu -->
                <div class="dropdown-popover notification-popover" id="notification-dropdown" role="menu">
                    <div class="notif-header">
                        <div class="notif-header-title-box">
                            <span class="popover-title">Notifications</span>
                            <span class="notif-count-pill {{ $navUnreadCount > 0 ? '' : 'd-none' }}"
                                id="notif-header-pill">
                                {{ $navUnreadCount ?? 0 }} {{ __('messages.lbl_new') }}
                            </span>
                        </div>
                        <button type="button" class="btn-mark-all-read" id="btn-mark-all-read"
                            title="Mark all as read" {{ $navUnreadCount > 0 ? '' : 'disabled' }}>
                            <iconify-icon icon="ph:checks-bold"></iconify-icon> Mark all as read
                        </button>
                    </div>

                    <!-- Notification Items List -->
                    <div class="notif-items-scroll" id="notif-items-list">
                        @forelse($navNotifications as $notification)
                            @php
                                $senderData = $notification->sender;
                                $senderId = $senderData->id ?? null;
                                $senderMatriId = $senderData->matri_id ?? '';

                                $redirectUrl =
                                    $senderId && $authUser->id != $senderId
                                        ? route('web.userProfile.index', _encrypt($senderId))
                                        : '#';

                                $senderProfileImage = $senderData
                                    ? _getMemberProfileImage($senderData)
                                    : asset('assets/images/default-avatar.png');
                            @endphp
                            <a href="{{ $redirectUrl }}" class="notif-card-link"
                                data-notif-id="{{ $notification->id }}"
                                data-is-read="{{ $notification->is_read ? 1 : 0 }}">
                                <div class="notif-card {{ $notification->is_read ? '' : 'unread' }}"
                                    id="notif-card-{{ $notification->id }}">
                                    <div class="notif-avatar-wrapper">
                                        <img src="{{ $senderProfileImage }}" alt="{{ $senderMatriId }}"
                                            class="notif-user-avatar"
                                            onerror="this.onerror=null;this.src='{{ asset('assets/images/default-avatar.png') }}';">
                                        {{-- <span class="notif-category-icon purple">
                                            <iconify-icon icon="ph:bookmark-simple-fill"></iconify-icon>
                                        </span> --}}
                                    </div>
                                    <div class="notif-card-body">
                                        <p class="notif-text">{{ $notification->message }}</p>
                                        <div class="notif-timestamp-row">
                                            <span class="notif-time">
                                                <iconify-icon icon="ph:clock-bold"></iconify-icon>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            @if (!$notification->is_read)
                                                <span class="notif-unread-dot"></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="notif-empty-state">
                                <iconify-icon icon="ph:bell-slash-bold"></iconify-icon>
                                <p>No notifications yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- User Profile Pill & Settings Dropdown -->
            <div class="nav-item-dropdown" id="nav-profile-wrapper">
                <div class="user-profile-nav" id="user-profile-pill" role="button" tabindex="0"
                    aria-expanded="false" aria-haspopup="true" title="Account &amp; Settings">
                    <img src="{{ $profileImage }}" alt="{{ $authUser->matri_id }}" class="user-avatar-nav">
                    <div class="user-meta-nav">
                        <span
                            class="user-name-nav">{{ \Illuminate\Support\Str::limit($authUser->fullname, 15, '...') }}</span>
                        <span class="user-status-nav">
                            <span class="user-status-dot"></span> {{ __('messages.lbl_vip_member') }}
                        </span>
                    </div>
                    <iconify-icon icon="ph:gear-six-bold" class="nav-settings-icon"></iconify-icon>
                </div>

                <!-- Profile & Settings Dropdown Menu -->
                <div class="dropdown-popover profile-popover" id="profile-dropdown" role="menu">
                    <!-- Profile Card Header -->
                    <div class="profile-dropdown-header">
                        <div class="profile-header-user">
                            <div class="profile-header-avatar-wrap">
                                <img src="{{ $profileImage }}" alt="{{ $authUser->matri_id }}"
                                    class="profile-header-avatar">
                                <span class="profile-vip-crown" title="{{ __('messages.lbl_vip_member') }}">
                                    <iconify-icon icon="ph:crown-fill"></iconify-icon>
                                </span>
                            </div>
                            <div class="profile-header-info">
                                <div class="profile-name-row">
                                    <h4 class="profile-display-name">
                                        {{ \Illuminate\Support\Str::limit($authUser->fullname, 15, '...') }}
                                    </h4>
                                    @if ($authUser->plan_status == 'Paid')
                                        <span class="profile-verified-badge">
                                            <iconify-icon icon="ph:seal-check-fill"></iconify-icon>
                                        </span>
                                    @endif
                                </div>
                                <span class="profile-membership-id">ID: {{ $authUser->matri_id }}</span>
                                @if ($authUser->plan_status == 'Paid')
                                    <span class="profile-plan-chip">
                                        <span class="status-indicator-live"></span>
                                        {{ $authUser->plan_name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('web.myProfile.index') }}"
                            class="btn-profile-view-edit">
                            <iconify-icon icon="ph:user-circle-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_view_profile') }}</span>
                        </a>
                    </div>

                    <div class="dropdown-menu-separator"></div>

                    <!-- My Profile Options (Exact Requested Options) -->
                    <div class="profile-dropdown-section">
                        <div class="dropdown-section-label">{{ __('messages.lbl_my_profile') }}</div>

                        <!-- 1. My Profile -->
                        <a href="{{ route('web.myProfile.index') }}"
                            class="profile-nav-item {{ _navActive(['web.myProfile.index']) }}" role="menuitem">
                            <div class="nav-item-icon-circle icon-blue">
                                <iconify-icon icon="ph:user-circle-bold"></iconify-icon>
                            </div>
                            <div class="nav-item-text">
                                <span class="nav-item-title">{{ __('messages.lbl_my_profile') }}</span>
                                <span
                                    class="nav-item-desc">{{ __('messages.lbl_view_and_manage_your_personal_family_details') }}</span>
                            </div>
                            <iconify-icon icon="ph:caret-right-bold" class="nav-item-arrow"></iconify-icon>
                        </a>

                        <!-- 2. Upload Photo Album -->
                        <a href="{{ route('web.privacySettings.index') }}"
                            class="profile-nav-item {{ _navActive(['web.privacySettings.index']) }}"
                            role="menuitem">
                            <div class="nav-item-icon-circle icon-purple">
                                <iconify-icon icon="ph:images-square-bold"></iconify-icon>
                            </div>
                            <div class="nav-item-text">
                                <span class="nav-item-title">{{ __('messages.lbl_privacy_settings') }}</span>
                                <span class="nav-item-desc">{{ __('messages.lbl_manage_privacy_settings') }}</span>
                            </div>
                            <iconify-icon icon="ph:caret-right-bold" class="nav-item-arrow"></iconify-icon>
                        </a>

                        <!-- 3. ID Proof Upload -->
                        <a href="{{ route('web.inviteLinks.index') }}"
                            class="profile-nav-item {{ _navActive(['web.inviteLinks.index']) }}" role="menuitem">
                            <div class="nav-item-icon-circle icon-green">
                                <iconify-icon icon="ph:identification-card-bold"></iconify-icon>
                            </div>
                            <div class="nav-item-text">
                                <span class="nav-item-title">{{ __('messages.lbl_invite_friends') }}</span>
                                <span class="nav-item-desc">{{ __('messages.lbl_invite_friends_desc') }}</span>
                            </div>
                            <iconify-icon icon="ph:caret-right-bold" class="nav-item-arrow"></iconify-icon>
                        </a>

                        <!-- 4. Upload Horoscope -->
                        <a href="{{ route('web.deleteProfile.index') }}"
                            class="profile-nav-item {{ _navActive(['web.deleteProfile.index']) }}" role="menuitem">
                            <div class="nav-item-icon-circle icon-gold">
                                <iconify-icon icon="ph:sparkle-bold"></iconify-icon>
                            </div>
                            <div class="nav-item-text">
                                <span class="nav-item-title">{{ __('messages.lbl_delete_profile') }}</span>
                                <span class="nav-item-desc">{{ __('messages.lbl_delete_profile_desc') }}</span>
                            </div>
                            <iconify-icon icon="ph:caret-right-bold" class="nav-item-arrow"></iconify-icon>
                        </a>
                    </div>

                    <div class="dropdown-menu-separator"></div>

                    <!-- Sign Out Footer -->
                    <div class="profile-dropdown-footer">
                        <form id="logout-form" action="{{ route('web.logout') }}" method="POST"
                            style="display: none;">
                            @csrf
                        </form>
                        <a href="#" class="profile-logout-btn"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <iconify-icon icon="ph:sign-out-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_logout') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @if (!Auth::check())
            <div class="sidebar-backdrop d-lg-none" id="sidebar-backdrop" aria-hidden="true"></div>

            <aside class="sidebar-container d-lg-none" id="sidebar-container" aria-label="Sidebar Navigation">

                <!-- Mobile Drawer Header -->
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
                    <ul class="sidebar-menu-list">

                        <li>
                            <a href="{{ url('/') }}"
                                class="sidebar-menu-item {{ _navActive(['web.home.*']) }}" id="side-dashboard">
                                <span class="sidebar-item-left">
                                    <iconify-icon icon="ph:squares-four-fill"></iconify-icon>
                                    <span>{{ __('messages.lbl_home') }}</span>
                                </span>
                                <span class="active-radio-indicator"></span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('web.register.index') }}"
                                class="sidebar-menu-item {{ _navActive(['web.register.*']) }}" id="side-register">
                                <span class="sidebar-item-left">
                                    <iconify-icon icon="ph:user-plus-bold"></iconify-icon>
                                    <span>{{ __('messages.lbl_register') }}</span>
                                </span>
                                <span class="active-radio-indicator"></span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('web.login.index') }}"
                                class="sidebar-menu-item {{ _navActive(['web.login.*']) }}" id="side-login">
                                <span class="sidebar-item-left">
                                    <iconify-icon icon="ph:sign-in-bold"></iconify-icon>
                                    <span>{{ __('messages.lbl_login') }}</span>
                                </span>
                                <span class="active-radio-indicator"></span>
                            </a>
                        </li>

                    </ul>
                </div>
            </aside>
        @endif

    </div>
</header>

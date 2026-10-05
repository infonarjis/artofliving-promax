<!-- Sidebar Menu Toggle Button for Mobile -->
<div class="col-12 d-lg-none mt-3">
    <button class="comman-bg-btn w-100 fts-15 fw-5 d-flex align-items-center justify-content-center gap-2 py-3 border-0"
        style="border-radius: 12px; background: var(--black-color-3); color: var(--white-color);" type="button"
        onclick="document.getElementById('sidebarProfileMenu').classList.add('active'); document.getElementById('sidebarProfileOverlay').classList.add('active');">
        <iconify-icon icon="solar:hamburger-menu-linear" class="fts-20"></iconify-icon>
        {{ __('messages.lbl_open_profile_menu') }}
    </button>
</div>
<div class="sidebar-overlay-mobile d-lg-none" id="sidebarProfileOverlay"
    onclick="document.getElementById('sidebarProfileMenu').classList.remove('active'); this.classList.remove('active');">
</div>

<!-- Sidebar Menu Start -->
<div class="col-lg-3 col-12 mt-3 mt-lg-0 common-right-profilebox position-relative">
    <div class="sidebar-menu-card common-bgwhite-main p-3" id="sidebarProfileMenu">
        <div class="sidebar-mobile-close d-lg-none d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom common-border-n"
            style="border-bottom-color: rgba(255, 255, 255, 0.05) !important;">
            <h5 class="fts-18 fw-6 white-color-n m-0">Menu</h5>
            <button type="button" class="bg-transparent border-0 white-color-n fts-24 d-flex align-items-center p-0"
                onclick="document.getElementById('sidebarProfileMenu').classList.remove('active'); document.getElementById('sidebarProfileOverlay').classList.remove('active');">
                <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
            </button>
        </div>
        <ul class="sidebar-menu-list m-0 p-0">
            <li>
                <a href="{{ route('web.myProfile.index') }}"
                    class="d-flex align-items-center justify-content-between py-3 mb-1 {{ Route::is('web.myProfile.index') ? 'active' : '' }}">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:user-linear" class="fts-20 white-color-n"></iconify-icon>
                        <span class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_my_profile') }}</span>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('web.expressInterest.index', ['type' => 'interest_sent']) }}"
                    class="d-flex align-items-center justify-content-between py-3 mb-1 {{ Route::currentRouteName() == 'web.expressInterest.index' && request()->type == 'interest_sent' ? 'active' : '' }}">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="tabler:heart-up" class="fts-20 white-color-n"></iconify-icon>
                        <span class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_express_interest_sent') }}</span>
                    </div>
                    <span class="badge-count fts-12 fw-5 white-color-n black-bgcolor4-n">{{ $interestSentCount }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('web.expressInterest.index', ['type' => 'interest_receive']) }}"
                    class="d-flex align-items-center justify-content-between py-3 mb-1 {{ Route::currentRouteName() == 'web.expressInterest.index' && request()->type == 'interest_receive' ? 'active' : '' }}">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="tabler:heart-down" class="fts-20 white-color-n"></iconify-icon>
                        <span
                            class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_express_interest_received') }}</span>
                    </div>
                    <span
                        class="badge-count fts-12 fw-5 white-color-n black-bgcolor4-n">{{ $interestReceiveCount }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('web.viewedProfile.index', ['type' => 'i_viewed']) }}"
                    class="d-flex align-items-center justify-content-between py-3 mb-1 {{ Route::currentRouteName() == 'web.viewedProfile.index' && request()->type == 'i_viewed' ? 'active' : '' }}">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:eye-linear" class="fts-20 white-color-n"></iconify-icon>
                        <span class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_i_viewed_profile') }}</span>
                    </div>
                    <span
                        class="badge-count fts-12 fw-5 white-color-n black-bgcolor4-n">{{ $iViewedProfileCount }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('web.viewedProfile.index', ['type' => 'who_viewed']) }}"
                    class="d-flex align-items-center justify-content-between py-3 mb-1 {{ Route::currentRouteName() == 'web.viewedProfile.index' && request()->type == 'who_viewed' ? 'active' : '' }}">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:eye-linear" class="fts-20 white-color-n"></iconify-icon>
                        <span class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_who_viewed_my_profile') }}</span>
                    </div>
                    <span
                        class="badge-count fts-12 fw-5 white-color-n black-bgcolor4-n">{{ $whoViewedProfileCount }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('web.viewedContact.index', ['type' => 'i_viewed']) }}"
                    class="d-flex align-items-center justify-content-between py-3 mb-1 {{ Route::currentRouteName() == 'web.viewedContact.index' && request()->type == 'i_viewed' ? 'active' : '' }}">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:phone-calling-linear" class="fts-20 white-color-n"></iconify-icon>
                        <span class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_i_viewed_contact') }}</span>
                    </div>
                    <span
                        class="badge-count fts-12 fw-5 white-color-n black-bgcolor4-n">{{ $iViewedContactCount }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('web.viewedContact.index', ['type' => 'who_viewed']) }}"
                    class="d-flex align-items-center justify-content-between py-3 mb-1 {{ Route::currentRouteName() == 'web.viewedContact.index' && request()->type == 'who_viewed' ? 'active' : '' }}">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:phone-calling-linear" class="fts-20 white-color-n"></iconify-icon>
                        <span class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_who_viewed_my_contact') }}</span>
                    </div>
                    <span
                        class="badge-count fts-12 fw-5 white-color-n black-bgcolor4-n">{{ $whoViewedContactCount }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('web.shortlist.index') }}"
                    class="d-flex align-items-center justify-content-between py-3 mb-1 {{ Route::is('web.shortlist.index') ? 'active' : '' }}">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:user-check-linear" class="fts-20 white-color-n"></iconify-icon>
                        <span class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_shortlist_profile') }}</span>
                    </div>
                    <span class="badge-count fts-12 fw-5 white-color-n black-bgcolor4-n">{{ $shortlistCount }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('web.blocklist.index') }}"
                    class="d-flex align-items-center justify-content-between py-3 mb-1 {{ Route::is('web.blocklist.index') ? 'active' : '' }}">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:danger-circle-linear" class="fts-20 white-color-n"></iconify-icon>
                        <span class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_blocklist_profile') }}</span>
                    </div>
                    <span class="badge-count fts-12 fw-5 white-color-n black-bgcolor4-n">{{ $blocklistCount }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('web.photoRequest.index', ['type' => 'request_sent']) }}"
                    class="d-flex align-items-center justify-content-between py-3 mb-1 {{ Route::currentRouteName() == 'web.photoRequest.index' && request()->type == 'request_sent' ? 'active' : '' }}">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:gallery-download-linear" class="fts-20 white-color-n"></iconify-icon>
                        <span class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_photo_request_sent') }}</span>
                    </div>
                    <span
                        class="badge-count fts-12 fw-5 white-color-n black-bgcolor4-n">{{ $photoRequestSentCount }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('web.photoRequest.index', ['type' => 'request_receive']) }}"
                    class="d-flex align-items-center justify-content-between py-3 mb-1 {{ Route::currentRouteName() == 'web.photoRequest.index' && request()->type == 'request_receive' ? 'active' : '' }}">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:gallery-send-linear" class="fts-20 white-color-n"></iconify-icon>
                        <span class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_photo_request_received') }}</span>
                    </div>
                    <span
                        class="badge-count fts-12 fw-5 white-color-n black-bgcolor4-n">{{ $photoRequestReceiveCount }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('web.videoVoiceCall.index') }}"
                    class="d-flex align-items-center justify-content-between py-3 mb-1 {{ Route::currentRouteName() == 'web.videoVoiceCall.index' ? 'active' : '' }}">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:phone-linear" class="fts-20 white-color-n"></iconify-icon>
                        <span class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_call_history') }}</span>
                    </div>
                </a>
            </li>
        </ul>
    </div>
    @if ($featuredProfiles->isNotEmpty())
        <div class="pro-common-leftbar p-3 mt-3 d-none d-lg-block">
            <h2 class="fw-6 fts-18 white-color-n">{{ __('messages.lbl_featured_profile') }}</h2>
            <div class="profile-day-slider">
                <div class="profile-of-day-box p-3">
                    @foreach ($featuredProfiles as $member)
                        <div class="profiles-day-items px-5">
                            <div class="profile-the-img">
                                @php
                                    $canView = _canViewMemberPhoto($member, $member->hasPhotoRequestAccess);
                                    $hasPhoto = _checkPhotoExist($member);
                                    $profileImage = _getMemberProfileImage($member);
                                @endphp
                                @if (!$canView && $hasPhoto)
                                    <a href="{{ route('web.userProfile.index', _encrypt($member->id)) }}">
                                        <img src="{{ _getProtectedImage($member->gender) }}"
                                            alt="{{ _profileTitle($member) }}" class="profiles-of-img">
                                    </a>
                                @else
                                    <a href="{{ route('web.userProfile.index', _encrypt($member->id)) }}">
                                        <img src="{{ $profileImage }}" alt="{{ _profileTitle($member) }}"
                                            class="profiles-of-img">
                                    </a>
                                @endif
                            </div>
                            <h4 class="fts-16 fw-5 white-color-n mt-2">{{ _profileTitle($member) }}</h4>
                            <p class="fts-14 fw-4 white-color70-n mt-1">{{ _profileSubTitle($member) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Left Side Advertisement Banner --}}
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.advertisementBanner', [
        'adv_type' => 'Level 1',
    ])
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.advertisementBanner', [
        'adv_type' => 'Level 2',
    ])
    {{-- Left Side Advertisement Banner --}}
</div>
<!-- Sidebar Menu End -->

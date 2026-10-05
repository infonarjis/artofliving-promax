<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Seo Layout Section --}}
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.seoLayout', ['seo' => $seoManagement])

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_favicon'] }}"
        type="image/webp">

    <!-- all css file include  -->
    <link rel="stylesheet" href="{{ asset('storage/web/affiliate/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/affiliate/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/affiliate/css/select-2.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/affiliate/css/main.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    @php
        $affiliateUser = Auth::guard('affiliate')->user();
    @endphp
    <div class="afd-shell">
        <!-- ══ SIDEBAR ══════════════════════════════════ -->
        <aside class="afd-sidebar" id="afdSidebar">
            <div class="afd-sb-brand">
                <a href="{{ url('/') }}" class="mt-1">
                    <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                        alt="{{ $configArr['web_name'] }}" class="brand-logo w-100">
                </a>
            </div>
            <div class="afd-sb-user">
                <div class="afd-sb-avatar">
                    <img src="{{ _assetUrl('upload_path.AFFILIATE_MEMBER_PHOTOS_URL') . $affiliateUser->image }}"
                        alt="{{ $affiliateUser->fullname }}" class="">
                </div>
                <div>
                    <div class="afd-sb-uname">{{ $affiliateUser->fullname }}</div>
                    <div class="afd-sb-urole">Affiliate Partner</div>
                </div>
            </div>
            <nav class="afd-sb-nav">
                <div class="afd-sb-section-label">Main Menu</div>
                <a class="afd-sb-link {{ _navActive(['affiliate.dashboard'], 'afd-sb-active') }}"
                    href="{{ route('affiliate.dashboard') }}">
                    <span class="afd-sb-li"><iconify-icon icon="hugeicons:dashboard-browsing" width="24"
                            height="24"></iconify-icon></span> Dashboard
                </a>
                <a class="afd-sb-link {{ _navActive(['affiliate.assignMember.index'], 'afd-sb-active') }}"
                    href="{{ route('affiliate.assignMember.index') }}">
                    <span class="afd-sb-li"><iconify-icon icon="hugeicons:user-group-02" width="24"
                            height="24"></iconify-icon></span> Referral Members
                </a>
                <a class="afd-sb-link {{ _navActive(['affiliate.incomeList.index'], 'afd-sb-active') }}"
                    href="{{ route('affiliate.incomeList.index') }}">
                    <span class="afd-sb-li"><iconify-icon icon="hugeicons:money-send-square" width="24"
                            height="24"></iconify-icon></span> Income List
                </a>
                <a class="afd-sb-link {{ _navActive(['affiliate.paymentHistory.index'], 'afd-sb-active') }}"
                    href="{{ route('affiliate.paymentHistory.index') }}">
                    <span class="afd-sb-li"><iconify-icon icon="hugeicons:payment-02" width="24"
                            height="24"></iconify-icon></span> Payment History
                </a>
                <a class="afd-sb-link {{ _navActive(['affiliate.visitorClick.index'], 'afd-sb-active') }}"
                    href="{{ route('affiliate.visitorClick.index') }}">
                    <span class="afd-sb-li"><iconify-icon icon="hugeicons:mouse-left-click-04" width="24"
                            height="24"></iconify-icon></span> Visitor Clicks
                </a>
                <div class="afd-sb-section-label" style="margin-top:12px">Account</div>
                <a class="afd-sb-link {{ _navActive(['affiliate.myProfile.index'], 'afd-sb-active') }}"
                    href="{{ route('affiliate.myProfile.index') }}">
                    <span class="afd-sb-li"><iconify-icon icon="hugeicons:edit-user-02" width="24"
                            height="24"></iconify-icon></span> My Profile
                </a>
            </nav>
        </aside>

        <div class="afd-sb-overlay" id="afdOverlay"></div>

        {{-- toast notification --}}
        <div class="afd-content">

            <!-- TOPBAR -->
            <nav class="afd-navbar">
                <div class="afd-nb-left">
                    <div class="afd-hamburger" id="afdHamburger">☰</div>
                    <div class="afd-nb-title">{{ $pageName }}</div>
                </div>
                <div class="afd-nav-right">
                    {{-- <button class="afd-theme-toggle" id="afdThemeToggle" type="button" title="Toggle dark / light mode" aria-label="Toggle dark / light mode">
                        <iconify-icon class="afd-theme-icon-dark" icon="hugeicons:moon-02" width="20" height="20"></iconify-icon>
                        <iconify-icon class="afd-theme-icon-light" icon="hugeicons:sun-03" width="20" height="20"></iconify-icon>
                    </button> --}}
                    <button class="afd-theme-toggle" id="afdThemeToggle" type="button" title="Toggle dark / light mode"
                        aria-label="Toggle dark / light mode">

                        <iconify-icon class="afd-theme-icon-dark" icon="hugeicons:moon-02" width="20"
                            height="20">
                        </iconify-icon>

                        <iconify-icon class="afd-theme-icon-light" icon="hugeicons:sun-03" width="20"
                            height="20">
                        </iconify-icon>

                    </button>
                    <div class="afd-user-menu" id="afdUserMenu">
                        <div class="afd-user-trigger" id="afdUserTrigger">
                            <div class="afd-ut-avatar">
                                <img src="{{ _assetUrl('upload_path.AFFILIATE_MEMBER_PHOTOS_URL') . $affiliateUser->image }}"
                                    alt="{{ $affiliateUser->fullname }}" class="">
                            </div>
                            <span class="afd-ut-name">{{ $affiliateUser->fullname }}</span>
                            <span class="afd-ut-caret">▾</span>
                        </div>
                        <div class="afd-user-dropdown">
                            <div class="afd-ud-head">
                                <div class="afd-ud-hav"><img
                                        src="{{ _assetUrl('upload_path.AFFILIATE_MEMBER_PHOTOS_URL') . $affiliateUser->image }}"
                                        alt="{{ $affiliateUser->fullname }}" class=""></div>
                                <div>
                                    <div class="afd-ud-hname">{{ $affiliateUser->fullname }}</div>
                                    <div class="afd-ud-hemail">{{ $affiliateUser->email }}</div>
                                </div>
                            </div>
                            <div class="afd-ud-body">
                                <a class="afd-ud-item" href="{{ route('affiliate.myProfile.index') }}"><span
                                        class="afd-ud-ico mt-1"><iconify-icon icon="hugeicons:user" width="24"
                                            height="24"></iconify-icon></span> My Profile</a>

                                {{-- Logout --}}
                                <form id="logout-form" action="{{ route('affiliate.logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                                <a class="afd-ud-item afd-danger" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <span class="afd-ud-ico mt-1">
                                        <iconify-icon icon="hugeicons:logout-02" width="24"
                                            height="24"></iconify-icon>
                                    </span> Sign Out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- ══ MAIN ══════════════════════════════════ -->
            {{-- Main Conent --}}
            @yield('affiliate_after_login_content')
        </div>
    </div>

    {{-- toast notification --}}
    @include(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.common.toast_message')

    <!-- progress bar bottom to top  -->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>

    <!-- all js file include  -->
    <script src="{{ asset('storage/web/affiliate/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('storage/web/affiliate/js/select-2.js') }}"></script>
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>
    <script src="{{ asset('storage/web/affiliate/js/slick.min.js') }}"></script>
    <script src="{{ asset('storage/web/affiliate/js/main.js') }}"></script>

    {{-- common.js --}}
    <script>
        const csrfToken = "{{ csrf_token() }}";
        var lbl_loading = "{{ __('messages.lbl_loading') }}";
    </script>
    <script src="{{ asset('storage/web/custom/js/common.js') }}"></script>

    @stack('scripts')

</body>

</html>

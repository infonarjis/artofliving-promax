<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Seo Layout Section --}}
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.seoLayout', ['seo' => $seoManagement])

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_favicon'] }}"
        type="image/webp">

    <script>
        (function() {
            const saved = localStorage.getItem('prom-max-theme');
            if (saved) {
                if (saved === 'light') {
                    document.documentElement.classList.add('light-mode');
                } else {
                    document.documentElement.classList.remove('light-mode');
                }
                return;
            }
            // Default theme for first-time visitors: Light Mode
            document.documentElement.classList.add('light-mode');
            localStorage.setItem('prom-max-theme', 'light');
        })();
    </script>

    <!-- all css file include  -->
    <link rel="stylesheet" href="{{ asset('storage/web/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/assets/css/select-2.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/assets/css/slick.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">
    <link rel="stylesheet" href="{{ asset('storage/web/assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/assets/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/custom/css/custom.css') }}">

    <!-- Custom Dashboard Stylesheet -->
    <link rel="stylesheet" href="{{ asset('storage/web/assets/css/dashboard-new.css') }}">

    <link rel="stylesheet" href="{{ route('theme.css') }}?v={{ \App\Services\ThemeService::version() }}">

    @stack('styles')

    {{-- Google Analystics Code --}}
    {!! $configArr['google_analytics_code'] !!}
    {{-- Google Analystics Code --}}
</head>

<body>
    <!-- page loader  -->
    <div id="page-loader" class="loader-hidden">
        <div class="loader-content">
            <div class="heart-loader"></div>
            <div class="loader-text">{{ $configArr['web_name'] }}</div>
        </div>
    </div>

    <!-- dark & light mode code -->
    <div class="theme-switch">
        <input type="checkbox" id="toggle-theme" class="d-none">
        <label for="toggle-theme" class="switch">
            <span class="circle"></span>
            <span class="icon sun"><iconify-icon icon="akar-icons:sun-fill"></iconify-icon></span>
            <span class="icon moon"><iconify-icon icon="solar:moon-bold-duotone"></iconify-icon></span>
        </label>
    </div>

    <!-- navbar start -->
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.header')

    {{-- Main Content --}}
    @php
        $loginUserData = auth()->user();
        $currentRouteAction = request()->route() ? request()->route()->getActionName() : '';
        $isMembershipOrPayment = str_contains($currentRouteAction, 'MembershipPlanController') || str_contains($currentRouteAction, 'PaymentController');
    @endphp
    @if(Auth::check() && ($loginUserData->plan_status !== 'Paid') && !$isMembershipOrPayment && $loginUserData->is_verify == 'No')
        <script>window.location.href = "{{ route('web.membershipPlan.index') }}";</script>
        @php
            header('Location: ' . route('web.membershipPlan.index'));
            exit;
        @endphp
    @elseif(isset($loginUserData->plan_status) && $loginUserData->plan_status == 'Paid' && isset($loginUserData->is_verify) && $loginUserData->is_verify == 'No')
        @include('web.isVerifyProfile')
    @else
        @yield('web_content')
    @endif

    <!-- footer section start -->
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.footer')

    <!-- Cookies Consent Banner -->
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.cookiesBanner')
    <!-- Cookies Consent Banner -->

    <!-- progress bar bottom to top  -->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>

    {{-- toast notification --}}
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.common.toast_message')
    {{-- toast notification --}}

    {{-- Common Models --}}
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.commonModel')
    {{-- Common Models --}}

    {{-- Footers --}}
    @if (Auth::check() && $configArr['chat_module_design'] == 'popup')
        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.chat.footer_chat')
    @endif
    {{-- Footers --}}

    {{-- Announcement Modal --}}
    @if (Auth::check())
        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.announcementModal')
    @endif
    {{-- Announcement Modal --}}

    <!-- Base Url -->
    <input type="hidden" id="base_url" name="base_url" value="{{ url('/') }}">

    <!-- all js file include  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script src="{{ asset('storage/web/assets/js/bootstrap.bundle.min.js') }}"></script>
    {{-- <script src="{{ asset('storage/web/assets/js/select-2.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>
    <script src="{{ asset('storage/web/assets/js/wow.js') }}"></script>
    <script src="{{ asset('storage/web/assets/js/slick.js') }}"></script>
    <script src="{{ asset('storage/web/assets/js/slick.min.js') }}"></script>
    <script src="{{ asset('storage/web/assets/js/main.js') }}"></script>

    {{-- Zego Cloud --}}
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.zego_cloud')
    {{-- Zego Cloud --}}

    {{-- Firebase Configuration --}}
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.firebaseConfiguration')
    {{-- Firebase Configuration --}}

    <script>
        const csrfToken = "{{ csrf_token() }}";
        const baseUrl   = "{{ rtrim(url('/'), '/') }}";
        var lbl_loading = "{{ __('messages.lbl_loading') }}";
        var lbl_read_more = "{{ __('messages.lbl_read_more') }}";
        var lbl_read_less = "{{ __('messages.lbl_read_less') }}";
        var lbl_block = "{{ __('messages.lbl_block') }}";
        var lbl_unblock = "{{ __('messages.lbl_unblock') }}";
        var lbl_accepted = "{{ __('messages.lbl_accepted') }}";
        var lbl_rejected = "{{ __('messages.lbl_rejected') }}";
        var lbl_please_wait = "{{ __('messages.lbl_please_wait') }}";
        var lbl_interest_in_profile = "{{ __('messages.lbl_interest_in_profile') }}";
        var lbl_send_reminder = "{{ __('messages.lbl_send_reminder') }}";
        var lbl_interest_sent = "{{ __('messages.lbl_interest_sent') }}";
        var lbl_does_not_matter = "{{ __('messages.lbl_does_not_matter') }}";
        var msg_copied = "{{ __('messages.msg_copied') }}";
        var lbl_autofill = "{{ __('messages.lbl_autofill') }}";
    </script>

    <script src="{{ asset('storage/web/custom/js/common.js') }}"></script>
    @if (Auth::check())
        <script src="{{ asset('storage/web/custom/js/express_interest.js') }}"></script>
    @endif
    @stack('scripts')
</body>

</html>

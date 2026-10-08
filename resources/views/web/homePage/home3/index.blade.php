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

    <link rel="stylesheet" href="{{ asset('storage/web/home3/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home3/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home3/assets/css/slick.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="{{ asset('storage/web/home3/assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home3/assets/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home3/assets/css/animations.css') }}">

    <link rel="stylesheet" href="{{ route('theme.css') }}?v={{ \App\Services\ThemeService::version() }}">

    @stack('styles')

    {{-- Google Analystics Code --}}
    {!! $configArr['google_analytics_code'] !!}
    {{-- Google Analystics Code --}}
</head>

<body>
    <!-- dark & light mode code -->
    <div class="theme-switch">
        <input type="checkbox" id="toggle-theme" class="d-none">
        <label for="toggle-theme" class="switch">
            <span class="circle"></span>
            <span class="icon sun"><iconify-icon icon="akar-icons:sun-fill"></iconify-icon></span>
            <span class="icon moon"><iconify-icon icon="solar:moon-bold-duotone"></iconify-icon></span>
        </label>
    </div>

    @php
        $headerSectionBanner = !empty($data['hero_main_background_banner'])
            ? _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['hero_main_background_banner']
            : asset('storage/web/home3') . '/assets/images/hero-bg.png';
    @endphp
    <div class="main-header-bg" style="background-image: url('{{ $headerSectionBanner }}');">
        @php
            $getActiveLanguage = _getActiveLanguage();
            $currentLanguage = App::getLocale();
            $currentLang = collect($getActiveLanguage)->firstWhere('lang_code', $currentLanguage);
            $currentLangCode = $currentLang->lang_code ?? 'en';
        @endphp
        <!-- Navbar -->
        <nav aria-label="navbar" class="navbar-pro-matrimony py-2 py-lg-3 wow fadeInDown" data-wow-duration="0.6s">
            <div class="container-fluid px-xl-5 px-lg-4 px-3" style="max-width: 1540px;">
                <div class="navbar-pro-inner d-flex align-items-center justify-content-between">
                    <!-- Mobile Toggler Button -->
                    <button class="navbar-toggler d-lg-none" type="button" aria-label="Toggle navigation"
                        id="navbarTogglerBtn">
                        <i class="bx bx-menu"></i>
                    </button>

                    <!-- Logo with golden Islamic arch icon -->
                    <a href="{{ url('/') }}"
                        class="brand-logo-wrap d-flex align-items-center text-decoration-none">
                        <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                            alt="{{ $configArr['web_name'] }} logo" class="brand-logo">
                    </a>
                    <!-- Middle Links (Desktop) -->
                    <ul class="navbar-ui navbar-left-links d-none d-lg-flex align-items-center gap-1 dm-sans mb-0">
                        <li><a href="{{ url('/') }}" class="nav-item nav-pill-active"><iconify-icon
                                    icon="fluent:grid-16-filled"
                                    class="nav-icon"></iconify-icon>{{ __('messages.lbl_home') }}</a></li>
                        <li class="nav-item-dropdown position-relative">
                            <a href="javascript:void(0)"
                                class="nav-item nav-dropdown-toggle d-inline-flex align-items-center gap-1"
                                id="searchDropdownBtnDesktop" role="button" aria-expanded="false">
                                <iconify-icon icon="bx:search" class="nav-icon"></iconify-icon>
                                <span>{{ __('messages.lbl_search') }}</span>
                                <iconify-icon icon="lucide:chevron-down" class="dropdown-arrow-icon"></iconify-icon>
                            </a>
                            <div class="nav-custom-dropdown-menu shadow-lg">
                                <a href="{{ route('web.search.type', ['type' => 'quick-search']) }}"
                                    class="dropdown-item-custom d-flex align-items-center gap-3">
                                    <div class="dropdown-icon-box bg-quick">
                                        <iconify-icon icon="solar:magnifer-linear"></iconify-icon>
                                    </div>
                                    <div class="dropdown-item-text">
                                        <span class="item-title">{{ __('messages.lbl_quick_search') }}</span>
                                    </div>
                                </a>
                                <a href="{{ route('web.search.type', ['type' => 'advance-search']) }}"
                                    class="dropdown-item-custom d-flex align-items-center gap-3">
                                    <div class="dropdown-icon-box bg-advance">
                                        <iconify-icon icon="solar:filter-linear"></iconify-icon>
                                    </div>
                                    <div class="dropdown-item-text">
                                        <span class="item-title">{{ __('messages.lbl_advance_search') }}</span>
                                    </div>
                                </a>
                                <a href="{{ route('web.search.type', ['type' => 'keyword-search']) }}"
                                    class="dropdown-item-custom d-flex align-items-center gap-3">
                                    <div class="dropdown-icon-box bg-keyword">
                                        <iconify-icon icon="iconmind:keyword-search-duotone-regular"></iconify-icon>
                                    </div>
                                    <div class="dropdown-item-text">
                                        <span class="item-title">{{ __('messages.lbl_keyword_search') }}</span>
                                    </div>
                                </a>
                                <a href="{{ route('web.search.type', ['type' => 'id-search']) }}"
                                    class="dropdown-item-custom d-flex align-items-center gap-3">
                                    <div class="dropdown-icon-box bg-id">
                                        <iconify-icon icon="solar:card-2-linear"></iconify-icon>
                                    </div>
                                    <div class="dropdown-item-text">
                                        <span class="item-title">{{ __('messages.lbl_id_search') }}</span>
                                    </div>
                                </a>
                            </div>
                        </li>
                        <li><a href="{{ route('web.membershipPlan.index') }}" class="nav-item"><iconify-icon
                                    icon="mdi:crown-outline"
                                    class="nav-icon"></iconify-icon>{{ __('messages.lbl_membership') }}</a></li>
                        <li><a href="{{ route('web.successStory.index') }}" class="nav-item"><iconify-icon
                                    icon="mdi:heart-outline"
                                    class="nav-icon"></iconify-icon>{{ __('messages.lbl_success_stories') }}</a></li>
                        <li><a href="{{ route('web.contactUs.index') }}" class="nav-item">
                                <iconify-icon icon="ph:chats-circle-bold"
                                    class="nav-icon"></iconify-icon>{{ __('messages.lbl_contact_us') }}</a></li>
                    </ul>
                    <!-- Desktop & Mobile Drawer Wrapper -->
                    <div class="navbar-ui-wrapper" id="navbarUiWrapper">
                        <div
                            class="mobile-nav-header d-flex d-lg-none justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center">
                                <a href="{{ url('/') }}"
                                    class="brand-logo-wrap d-flex align-items-center text-decoration-none">
                                    <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                                        alt="{{ $configArr['web_name'] }} logo" class="brand-logo">
                                </a>
                            </div>
                            <button class="close-toggle" type="button" aria-label="Close navigation"
                                id="navbarCloseBtn">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>

                        <!-- Middle Links (Mobile) -->
                        <ul
                            class="navbar-ui navbar-left-links d-lg-flex align-items-center gap-2 gap-xl-4 dm-sans mb-0 d-lg-none">
                            <li><a href="{{ url('/') }}" class="nav-item nav-pill-active"><iconify-icon
                                        icon="fluent:grid-16-filled"
                                        class="nav-icon"></iconify-icon>{{ __('messages.lbl_home') }}</a></li>
                            <li class="nav-item-dropdown position-relative">
                                <a href="javascript:void(0)"
                                    class="nav-item nav-dropdown-toggle d-flex align-items-center"
                                    id="searchDropdownBtnMobile" role="button" aria-expanded="false">
                                    <iconify-icon icon="bx:search" class="nav-icon"></iconify-icon>
                                    <span>{{ __('messages.lbl_search') }}</span>
                                    <iconify-icon icon="lucide:chevron-down"
                                        class="dropdown-arrow-icon"></iconify-icon>
                                </a>
                                <div class="nav-custom-dropdown-menu">
                                    <a href="{{ route('web.search.type', ['type' => 'quick-search']) }}"
                                        class="dropdown-item-custom d-flex align-items-center gap-3">
                                        <div class="dropdown-icon-box bg-quick">
                                            <iconify-icon icon="solar:magnifer-linear"></iconify-icon>
                                        </div>
                                        <div class="dropdown-item-text">
                                            <span class="item-title">{{ __('messages.lbl_quick_search') }}</span>
                                        </div>
                                    </a>
                                    <a href="{{ route('web.search.type', ['type' => 'advance-search']) }}"
                                        class="dropdown-item-custom d-flex align-items-center gap-3">
                                        <div class="dropdown-icon-box bg-advance">
                                            <iconify-icon icon="solar:filter-linear"></iconify-icon>
                                        </div>
                                        <div class="dropdown-item-text">
                                            <span class="item-title">{{ __('messages.lbl_advance_search') }}</span>
                                        </div>
                                    </a>
                                    <a href="{{ route('web.search.type', ['type' => 'keyword-search']) }}"
                                        class="dropdown-item-custom d-flex align-items-center gap-3">
                                        <div class="dropdown-icon-box bg-keyword">
                                            <iconify-icon
                                                icon="iconmind:keyword-search-duotone-regular"></iconify-icon>
                                        </div>
                                        <div class="dropdown-item-text">
                                            <span class="item-title">{{ __('messages.lbl_keyword_search') }}</span>
                                        </div>
                                    </a>
                                    <a href="{{ route('web.search.type', ['type' => 'id-search']) }}"
                                        class="dropdown-item-custom d-flex align-items-center gap-3">
                                        <div class="dropdown-icon-box bg-id">
                                            <iconify-icon icon="solar:card-2-linear"></iconify-icon>
                                        </div>
                                        <div class="dropdown-item-text">
                                            <span class="item-title">{{ __('messages.lbl_id_search') }}</span>
                                        </div>
                                    </a>
                                </div>
                            </li>
                            <li><a href="{{ route('web.membershipPlan.index') }}" class="nav-item"><iconify-icon
                                        icon="mdi:crown-outline"
                                        class="nav-icon"></iconify-icon>{{ __('messages.lbl_membership') }}</a></li>
                            <li><a href="{{ route('web.successStory.index') }}" class="nav-item"><iconify-icon
                                        icon="mdi:heart-outline"
                                        class="nav-icon"></iconify-icon>{{ __('messages.lbl_success_stories') }}</a>
                            </li>
                            <li><a href="{{ route('web.contactUs.index') }}" class="nav-item"><iconify-icon
                                        icon="ph:chats-circle-bold"
                                        class="nav-icon"></iconify-icon>{{ __('messages.lbl_contact_us') }}</a></li>
                        </ul>
                        <!-- Right Links -->
                        <ul class="navbar-ui navbar-right-links d-lg-flex align-items-center gap-2 dm-sans mb-0">
                            <li><a href="{{ route('web.register.index') }}"
                                    class="nav-item text-center"><iconify-icon icon="ph:sign-in-bold"
                                        class="nav-icon"></iconify-icon>{{ __('messages.lbl_register') }}</a>
                            </li>
                            <li class=" mt-lg-0">
                                <a href="{{ route('web.login.index') }}" class="btn-login-pill">
                                    <iconify-icon icon="material-symbols:login-rounded"
                                        class="login-btn-icon"></iconify-icon>
                                    <span>{{ __('messages.lbl_login_now') }}</span>
                                </a>
                            </li>
                            {{-- Language Selector --}}
                            <li class="nav-item-dropdown position-relative" id="nav-language-wrapper">
                                <button type="button" class="language-selector-btn" id="language-toggle-btn"
                                    aria-label="{{ __('messages.lbl_change_language') }}" aria-expanded="false"
                                    aria-haspopup="true">

                                    <iconify-icon icon="ph:translate-bold"></iconify-icon>

                                    <span class="lang-curr-code" id="current-lang-code">
                                        {{ strtoupper($currentLangCode) }}
                                    </span>

                                    <iconify-icon icon="ph:caret-down-bold" class="lang-caret"></iconify-icon>
                                </button>

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
                                                data-lang="{{ $value->lang_name }}" role="menuitem">

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
                            </li>
                        </ul>
                    </div>
                    <!-- Backdrop for mobile drawer -->
                    <div class="mobile-nav-backdrop d-lg-none" id="navbarBackdrop"></div>
                </div>
            </div>
        </nav>

        <!-- Main Hero Section -->
        <header class="main-hero-section position-relative overflow-hidden">
            <!-- Islamic Pattern & Element Flourishes -->
            <img src="{{ asset('storage/web/home3') }}/assets/images/pattern.png" alt="Islamic Pattern"
                class="islamic-section-pattern-flourish pattern-flourish-tr d-none d-lg-block">
            {{-- <img src="{{ asset('storage/web/home3') }}/assets/images/element.png" alt="Islamic Element"
                class="islamic-section-element-flourish element-hero-accent anim-spin-slow d-none d-lg-block"> --}}

            <!-- Islamic Floating Decorative Shapes -->
            <div class="islamic-shape islamic-shape-gold anim-float-1 d-none d-md-block"
                style="top: 8%; left: 2%; width: 70px; height: 70px;">
                <svg viewBox="0 0 100 100" fill="none" width="100%" height="100%">
                    <rect x="25" y="25" width="50" height="50" stroke="#C5A467" stroke-width="2.2"
                        fill="rgba(197, 164, 103, 0.05)" />
                    <rect x="25" y="25" width="50" height="50" stroke="#C5A467" stroke-width="2.2"
                        fill="rgba(197, 164, 103, 0.05)" transform="rotate(45 50 50)" />
                    <circle cx="50" cy="50" r="10" stroke="#C5A467" stroke-width="1.8" />
                </svg>
            </div>
            <div class="islamic-shape islamic-shape-gold anim-float-2 d-none d-lg-block"
                style="top: 55%; left: 1%; width: 56px; height: 56px;">
                <svg viewBox="0 0 100 100" fill="none" width="100%" height="100%">
                    <path
                        d="M50 15C30.67 15 15 30.67 15 50C15 69.33 30.67 85 50 85C61.85 85 72.22 79.12 78.5 70.08C60.5 72.5 45 57 45 39C45 28.5 50.5 19.3 58.8 15.5C55.95 15.17 53.02 15 50 15Z"
                        fill="rgba(197, 164, 103, 0.12)" stroke="#C5A467" stroke-width="2" />
                    <polygon points="75,32 78,39 85,39 80,44 82,51 75,47 68,51 70,44 65,39 72,39"
                        fill="rgba(197, 164, 103, 0.3)" stroke="#C5A467" stroke-width="1.5" />
                </svg>
            </div>
            <div class="islamic-shape islamic-shape-gold anim-spin-slow d-none d-xl-block"
                style="top: 14%; right: 48%; width: 85px; height: 85px; opacity: 0.12;">
                <svg viewBox="0 0 100 100" fill="none" width="100%" height="100%">
                    <circle cx="50" cy="50" r="44" stroke="#C5A467" stroke-width="1.5"
                        stroke-dasharray="4 4" />
                    <rect x="25" y="25" width="50" height="50" stroke="#C5A467" stroke-width="1.5" />
                    <rect x="25" y="25" width="50" height="50" stroke="#C5A467" stroke-width="1.5"
                        transform="rotate(30 50 50)" />
                    <rect x="25" y="25" width="50" height="50" stroke="#C5A467" stroke-width="1.5"
                        transform="rotate(60 50 50)" />
                    <circle cx="50" cy="50" r="16" stroke="#C5A467" stroke-width="1.5" />
                </svg>
            </div>
            <div class="container-fluid px-xl-5 px-lg-4 px-3" style="max-width: 1540px;">
                <div class="row align-items-center hero-content-row">
                    <!-- Left Column Content -->
                    <div class="col-lg-6 col-12 z-index-2">
                        <div class="hero-left-contents dm-sans pe-lg-4">
                            <!-- Premium Matrimony Badge -->
                            <div class="hero-top-badge wow fadeInDown" data-wow-duration="0.7s">
                                <iconify-icon icon="solar:star-fall-minimalistic-bold"
                                    class="badge-icon"></iconify-icon>
                                <span>{{ $data['hero_badge_text'] ?? '' }}</span>
                            </div>

                            <!-- Main Title -->
                            <h1 class="hero-main-title playfair-display wow fadeInUp" data-wow-duration="0.8s"
                                data-wow-delay="0.1s">
                                {!! $data['hero_title'] ?? '' !!}
                            </h1>

                            <!-- Subtitle -->
                            <p class="hero-subtitle-text wow fadeInUp" data-wow-duration="0.8s"
                                data-wow-delay="0.2s">
                                {!! $data['hero_subtitle'] ?? '' !!}
                            </p>

                            <!-- Trust Features Row -->
                            <div class="hero-trust-badges d-flex flex-wrap align-items-center gap-2 gap-sm-3 wow fadeInUp"
                                data-wow-duration="0.8s" data-wow-delay="0.3s">
                                <div class="trust-badge-card">
                                    <div class="trust-badge-icon">
                                        <iconify-icon icon="solar:shield-check-outline"></iconify-icon>
                                    </div>
                                    <div class="trust-badge-info">
                                        <div class="trust-badge-title">{{ $data['hero_trust1_title'] ?? '' }}</div>
                                        <div class="trust-badge-sub">{{ $data['hero_trust1_sub'] ?? '' }}</div>
                                    </div>
                                </div>

                                <div class="trust-badge-card">
                                    <div class="trust-badge-icon">
                                        <iconify-icon icon="solar:lock-keyhole-minimalistic-outline"></iconify-icon>
                                    </div>
                                    <div class="trust-badge-info">
                                        <div class="trust-badge-title">{{ $data['hero_trust2_title'] ?? '' }}</div>
                                        <div class="trust-badge-sub">{{ $data['hero_trust2_sub'] ?? '' }}</div>
                                    </div>
                                </div>

                                <div class="trust-badge-card">
                                    <div class="trust-badge-icon">
                                        <iconify-icon icon="solar:users-group-two-rounded-outline"></iconify-icon>
                                    </div>
                                    <div class="trust-badge-info">
                                        <div class="trust-badge-title">{{ $data['hero_trust3_title'] ?? '' }}</div>
                                        <div class="trust-badge-sub">{{ $data['hero_trust3_sub'] ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column Space -->
                    <div class="col-lg-6 d-none d-lg-block position-relative hero-right-col">
                    </div>
                </div>

                <!-- Search Bar Widget at Bottom -->
                <div class="hero-search-wrapper wow fadeInUp" id="search-widget" data-wow-duration="0.8s"
                    data-wow-delay="0.4s">
                    <div class="hero-search-card">
                        <form class="searchForm search-form-grid" action="{{ route('web.search.searchResult') }}"
                            method="GET">
                            <div class="row align-items-center g-3">
                                <div class="col-lg-3 col-md-6">
                                    <label
                                        class="search-field-label">{{ __('messages.lbl_i_m_looking_for_a') }}</label>
                                    <div class="custom-select-wrapper">
                                        <select name="gender" id="gender" class="form-select custom-select-3d">
                                            <option value="Male" title="Male">{{ __('messages.field_lbl_male') }}
                                            </option>
                                            <option value="Female" title="Female">
                                                {{ __('messages.field_lbl_female') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6">
                                    <label class="search-field-label">{{ __('messages.field_lbl_age') }}</label>
                                    <div class="d-flex align-items-center gap-2">
                                        @php $age = _ageRang(); @endphp
                                        <div class="custom-select-wrapper flex-grow-1">
                                            <select name="part_frm_age" id="part_frm_age"
                                                class="form-select custom-select-3d">
                                                @foreach ($age as $key => $valueArr)
                                                    <option value="{{ $key }}" title="{{ $valueArr }}">
                                                        {{ $valueArr }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span class="age-connector-text">{{ __('messages.field_lbl_to') }}</span>
                                        <div class="custom-select-wrapper flex-grow-1">
                                            <select name="part_to_age" id="part_to_age"
                                                class="form-select custom-select-3d">
                                                @foreach ($age as $key => $valueArr)
                                                    @php
                                                        $selected = '';
                                                        if ($key == '30') {
                                                            $selected = 'selected';
                                                        }
                                                    @endphp
                                                    <option value="{{ $key }}" {{ $selected }}
                                                        title="{{ $valueArr }}">{{ $valueArr }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <label class="search-field-label">{{ __('messages.field_lbl_religion') }}</label>
                                    <div class="custom-select-wrapper">
                                        <select name="religion" id="religion" class="form-select custom-select-3d">
                                            <option class="list" value='' selected
                                                title="{{ __('messages.field_lbl_select_religion') }}">
                                                {{ __('messages.field_lbl_select_religion') }}</option>
                                            @foreach ($religionList as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-6">
                                    <label class="search-field-label d-none d-lg-block">&nbsp;</label>
                                    <button type="submit" class="btn-hero-search-submit w-100">
                                        <span>{{ __('messages.lbl_search') }}</span>
                                        <i class="bx bx-search search-icon-btn"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </header>
    </div>

    <section class="how-work-section position-relative py-5 overflow-hidden">
        <!-- Floating Islamic Geometric Elements -->
        <div class="islamic-shape islamic-shape-gold anim-spin-slow d-none d-md-block"
            style="top: 8%; left: 2%; width: 90px; height: 90px;">
            <svg viewBox="0 0 100 100" fill="none" width="100%" height="100%">
                <rect x="20" y="20" width="60" height="60" stroke="#C5A467" stroke-width="1.8" />
                <rect x="20" y="20" width="60" height="60" stroke="#C5A467" stroke-width="1.8"
                    transform="rotate(45 50 50)" />
                <circle cx="50" cy="50" r="30" stroke="#C5A467" stroke-width="1.2"
                    stroke-dasharray="3 3" />
                <circle cx="50" cy="50" r="12" stroke="#C5A467" stroke-width="1.5" />
            </svg>
        </div>
        <div class="islamic-shape islamic-shape-emerald anim-float-1 d-none d-md-block"
            style="bottom: 8%; right: 2%; width: 80px; height: 80px;">
            <svg viewBox="0 0 100 100" fill="none" width="100%" height="100%">
                <path d="M50 10C50 10 32 24 32 46C32 60 25 68 20 72V90H80V72C75 68 68 60 68 46C68 24 50 10 50 10Z"
                    stroke="#31462E" stroke-width="2" />
                <circle cx="50" cy="14" r="3" fill="#31462E" />
            </svg>
        </div>
        <!-- Background Islamic Pattern & Corner Mandalas -->
        <img src="{{ asset('storage/web/home3') }}/assets/images/pattern.png" alt="Islamic Pattern"
            class="islamic-section-pattern-flourish pattern-flourish-tl d-none d-md-block">
        {{-- <img src="{{ asset('storage/web/home3') }}/assets/images/element.png" alt="Islamic Element"
            class="islamic-section-element-flourish anim-spin-slow d-none d-md-block" style="top: 8%; right: 4%;"> --}}
        <div class="islamic-bg-pattern"></div>
        <div class="islamic-corner-mandala corner-top-left"></div>

        <div class="container py-lg-4 position-relative z-index-1">
            <!-- Section Header -->
            <div class="text-center mx-auto mb-5" style="max-width: 720px;">
                <div class="section-top-badge wow fadeInDown" data-wow-duration="0.6s">
                    <iconify-icon icon="solar:star-fall-minimalistic-bold" class="badge-icon"></iconify-icon>
                    <span>{{ $data['journey_eyebrow'] ?? '' }}</span>
                </div>
                <h2 class="fts-44 fw-8 playfair-display mb-3 wow fadeInUp" data-wow-duration="0.7s"
                    data-wow-delay="0.1s">
                    {!! $data['journey_title'] ?? '' !!}</span>
                </h2>
                <p class="subtitle-color-L fts-16 dm-sans wow fadeInUp" data-wow-duration="0.7s"
                    data-wow-delay="0.2s">
                    {!! $data['journey_subtitle'] ?? '' !!}
                </p>
            </div>

            <!-- Step Cards Grid -->
            <div class="row g-4 justify-content-center position-relative steps-row">
                <!-- Step 1 -->
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="islamic-step-card tilt-effect">
                        <div class="step-card-watermark">
                            <svg width="120" height="120" viewBox="0 0 40 40" fill="none">
                                <path
                                    d="M20 3C20 3 13.5 8 13.5 15.5C13.5 20.5 10.5 23.5 8.5 24.5V36.5H31.5V24.5C29.5 23.5 26.5 20.5 26.5 15.5C26.5 8 20 3 20 3Z"
                                    stroke="#C5A467" stroke-width="1" opacity="0.15" />
                            </svg>
                        </div>
                        <div class="step-header-wrap d-flex align-items-center justify-content-between">
                            <span class="step-pill-badge">STEP 01</span>
                            <span class="step-icon-sparkle"><iconify-icon
                                    icon="solar:sparkles-bold"></iconify-icon></span>
                        </div>

                        <!-- Islamic Arch Icon Container -->
                        <div class="islamic-arch-iconbox iconbox-emerald">
                            <iconify-icon icon="solar:user-plus-bold-duotone" class="step-icon-main"></iconify-icon>
                        </div>

                        <h3 class="step-title playfair-display">{{ $data['journey_step1_title'] ?? '' }}</h3>
                        <p class="step-desc dm-sans">
                            {{ $data['journey_step1_desc'] ?? '' }}
                        </p>

                        <div class="step-footer-tag">
                            <iconify-icon icon="solar:shield-check-bold" class="tag-icon"></iconify-icon>
                            <span>{{ $data['journey_step1_tag'] ?? '' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.25s">
                    <div class="islamic-step-card tilt-effect">
                        <div class="step-card-watermark">
                            <svg width="120" height="120" viewBox="0 0 40 40" fill="none">
                                <path
                                    d="M20 3C20 3 13.5 8 13.5 15.5C13.5 20.5 10.5 23.5 8.5 24.5V36.5H31.5V24.5C29.5 23.5 26.5 20.5 26.5 15.5C26.5 8 20 3 20 3Z"
                                    stroke="#C5A467" stroke-width="1" opacity="0.15" />
                            </svg>
                        </div>
                        <div class="step-header-wrap d-flex align-items-center justify-content-between">
                            <span class="step-pill-badge badge-gold">STEP 02</span>
                            <span class="step-icon-sparkle"><iconify-icon
                                    icon="solar:sparkles-bold"></iconify-icon></span>
                        </div>

                        <!-- Islamic Arch Icon Container -->
                        <div class="islamic-arch-iconbox iconbox-gold">
                            <iconify-icon icon="solar:users-group-two-rounded-bold-duotone"
                                class="step-icon-main"></iconify-icon>
                        </div>

                        <h3 class="step-title playfair-display">{{ $data['journey_step2_title'] ?? '' }}</h3>
                        <p class="step-desc dm-sans">
                            {{ $data['journey_step2_desc'] ?? '' }}
                        </p>

                        <div class="step-footer-tag">
                            <iconify-icon icon="solar:checklist-minimalistic-bold" class="tag-icon"></iconify-icon>
                            <span>{{ $data['journey_step2_tag'] ?? '' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="islamic-step-card tilt-effect">
                        <div class="step-card-watermark">
                            <svg width="120" height="120" viewBox="0 0 40 40" fill="none">
                                <path
                                    d="M20 3C20 3 13.5 8 13.5 15.5C13.5 20.5 10.5 23.5 8.5 24.5V36.5H31.5V24.5C29.5 23.5 26.5 20.5 26.5 15.5C26.5 8 20 3 20 3Z"
                                    stroke="#C5A467" stroke-width="1" opacity="0.15" />
                            </svg>
                        </div>
                        <div class="step-header-wrap d-flex align-items-center justify-content-between">
                            <span class="step-pill-badge">STEP 03</span>
                            <span class="step-icon-sparkle"><iconify-icon
                                    icon="solar:sparkles-bold"></iconify-icon></span>
                        </div>

                        <!-- Islamic Arch Icon Container -->
                        <div class="islamic-arch-iconbox iconbox-emerald">
                            <iconify-icon icon="solar:heart-angle-bold-duotone" class="step-icon-main"></iconify-icon>
                        </div>

                        <h3 class="step-title playfair-display">{{ $data['journey_step3_title'] ?? '' }}</h3>
                        <p class="step-desc dm-sans">
                            {{ $data['journey_step3_desc'] ?? '' }}
                        </p>

                        <div class="step-footer-tag">
                            <iconify-icon icon="solar:verified-check-bold" class="tag-icon"></iconify-icon>
                            <span>{{ $data['journey_step3_tag'] ?? '' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($latestProfile->isNotEmpty())
        <section class="last-added-section py-5 position-relative overflow-hidden">
            <!-- Floating Islamic Star & Rosette -->
            <div class="islamic-shape islamic-shape-gold anim-pulse-glow d-none d-lg-block"
                style="top: 4%; right: 3%; width: 85px; height: 85px;">
                <svg viewBox="0 0 100 100" fill="none" width="100%" height="100%">
                    <circle cx="50" cy="50" r="42" stroke="#C5A467" stroke-width="1.5" />
                    <path d="M50 8L60 38L92 50L60 62L50 92L40 62L8 50L40 38Z" stroke="#C5A467" stroke-width="1.8"
                        fill="rgba(197, 164, 103, 0.06)" />
                    <circle cx="50" cy="50" r="12" stroke="#C5A467" stroke-width="1.5" />
                </svg>
            </div>
            <div class="islamic-shape islamic-shape-emerald anim-float-2 d-none d-lg-block"
                style="bottom: 5%; left: 2%; width: 75px; height: 75px;">
                <svg viewBox="0 0 100 100" fill="none" width="100%" height="100%">
                    <rect x="25" y="25" width="50" height="50" stroke="#31462E" stroke-width="2" />
                    <rect x="25" y="25" width="50" height="50" stroke="#31462E" stroke-width="2"
                        transform="rotate(45 50 50)" />
                </svg>
            </div>
            <!-- Background Islamic Pattern & Corner Mandalas -->
            <img src="{{ asset('storage/web/home3') }}/assets/images/pattern.png" alt="Islamic Pattern"
                class="islamic-section-pattern-flourish pattern-flourish-tr d-none d-md-block">
            {{-- <img src="{{ asset('storage/web/home3') }}/assets/images/element.png" alt="Islamic Element"
                class="islamic-section-element-flourish anim-float-1 d-none d-md-block" style="bottom: 5%; left: 3%;"> --}}
            <div class="islamic-bg-pattern"></div>
            <div class="islamic-corner-mandala corner-top-right"></div>

            <div class="container py-lg-4 position-relative z-index-1">
                <!-- Section Header -->
                <div class="text-center mx-auto mb-5" style="max-width: 720px;">
                    <div class="section-top-badge wow fadeInDown" data-wow-duration="0.6s">
                        <iconify-icon icon="solar:crown-bold" class="badge-icon"></iconify-icon>
                        <span>{!! $data['profiles_eyebrow'] ?? '' !!}</span>
                    </div>
                    <h2 class="fts-44 fw-8 playfair-display mb-3 wow fadeInUp" data-wow-duration="0.7s"
                        data-wow-delay="0.1s">
                        {!! $data['profiles_title'] ?? '' !!}
                    </h2>
                    <p class="subtitle-color-L fts-16 dm-sans wow fadeInUp" data-wow-duration="0.7s"
                        data-wow-delay="0.2s">
                        {!! $data['profiles_subtitle'] ?? '' !!}
                    </p>
                </div>

                <!-- Carousel Slider -->
                <div class="LastProfileSlider 3d-carousel pt-3 wow fadeInUp" data-wow-delay="0.2s">
                    @foreach ($latestProfile as $profile)
                        <div class="mx-3 tilt-effect card-3d-wrapper">
                            <div class="islamic-profile-card">
                                <div class="profile-card-image-wrap">
                                    @php
                                        $canView = _canViewMemberPhoto($profile, $profile->hasPhotoRequestAccess ?? '');

                                        $hasPhoto = _checkPhotoExist($profile);
                                        $profileImage = _getMemberProfileImage($profile);
                                    @endphp
                                    @if (!$canView && $hasPhoto)
                                        <img src="{{ _getProtectedImage($profile->gender) }}"
                                            alt="{{ _profileTitle($profile) }}" class="profile-main-photo">
                                    @else
                                        <img src="{{ $profileImage }}" alt="{{ _profileTitle($profile) }}"
                                            class="profile-main-photo">
                                    @endif
                                    @if ($profile->plan_status == 'Paid')
                                        <div class="profile-top-bar d-flex justify-content-between align-items-center">
                                            <span class="profile-tier-badge badge-platinum">
                                                <iconify-icon icon="solar:crown-bold"></iconify-icon>
                                                {{ $profile->plan_name }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="profile-info-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h3 class="profile-name playfair-display">{{ _profileTitle($profile) }}
                                            </h3>
                                            <p class="profile-specs-line dm-sans">{{ _getMemberAgeHeight($profile) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="profile-tags-row d-flex flex-wrap gap-2 mb-3">
                                        <span class="profile-tag"><iconify-icon
                                                icon="solar:briefcase-outline"></iconify-icon>
                                            {{ _displayNotAvailable(implode(', ', $profile->education_level_names) ?? null) }}</span>
                                        <span class="profile-tag"><iconify-icon
                                                icon="solar:map-point-outline"></iconify-icon>
                                            {{ _getMemberLocation($profile) }}</span>
                                    </div>
                                    <div class="profile-card-actions">
                                        <a href="{{ route('web.userProfile.index', _encrypt($profile->id)) }}"
                                            class="btn-profile-primary w-100">
                                            <span>{{ __('messages.lbl_view_profile') }}</span>
                                            <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="lastProfileArrows d-flex justify-content-center mt-5"></div>
            </div>
        </section>
    @endif

    @if ($successStoryArr->isNotEmpty())
        <section class="Happy-success-stories py-5 position-relative overflow-hidden">
            <!-- Floating Islamic Lantern & Stars -->
            <div class="islamic-shape islamic-shape-gold anim-float-1 d-none d-md-block"
                style="top: 6%; left: 3%; width: 70px; height: 70px;">
                <svg viewBox="0 0 100 100" fill="none" width="100%" height="100%">
                    <path d="M50 5V18M35 25H65L72 45L50 85L28 45L35 25Z" stroke="#C5A467" stroke-width="2" />
                    <circle cx="50" cy="45" r="8" stroke="#C5A467" stroke-width="1.5" />
                </svg>
            </div>
            <div class="islamic-shape islamic-shape-emerald anim-float-2 d-none d-md-block"
                style="bottom: 6%; right: 3%; width: 75px; height: 75px;">
                <svg viewBox="0 0 100 100" fill="none" width="100%" height="100%">
                    <rect x="25" y="25" width="50" height="50" stroke="#31462E" stroke-width="2" />
                    <rect x="25" y="25" width="50" height="50" stroke="#31462E" stroke-width="2"
                        transform="rotate(45 50 50)" />
                </svg>
            </div>
            <!-- Background Islamic Pattern & Corner Mandalas -->
            <img src="{{ asset('storage/web/home3') }}/assets/images/pattern.png" alt="Islamic Pattern"
                class="islamic-section-pattern-flourish pattern-flourish-bl d-none d-md-block">
            {{-- <img src="{{ asset('storage/web/home3') }}/assets/images/element.png" alt="Islamic Element"
                class="islamic-section-element-flourish anim-spin-slow d-none d-md-block" style="top: 8%; right: 4%;"> --}}

            <div class="container py-lg-4 position-relative z-index-1">
                <div class="islamic-stories-wrapper p-4 p-lg-5 wow fadeInUp position-relative overflow-hidden">
                    <!-- Corner Mandalas -->
                    <div class="islamic-corner-mandala corner-top-right"></div>
                    <div class="islamic-corner-mandala corner-bottom-left"></div>

                    <div class="row align-items-center g-4 g-lg-5 position-relative z-index-1">
                        <!-- Left Column Content -->
                        <div class="col-lg-4">
                            <div class="stories-left-header mb-4 mb-lg-0">
                                <div class="section-top-badge mb-3 wow fadeInDown">
                                    <iconify-icon icon="solar:hearts-bold" class="badge-icon"></iconify-icon>
                                    <span>{!! $data['stories_eyebrow'] ?? '' !!}</span>
                                </div>
                                <h2 class="fts-44 fw-8 playfair-display dark-color-L mb-3">
                                    {!! $data['stories_title'] ?? '' !!}
                                </h2>
                                <p class="subtitle-color-L fts-15 dm-sans mb-4">
                                    {!! $data['stories_subtitle'] ?? '' !!}
                                </p>

                                <!-- Micro Stats Counter -->
                                <div class="story-stats-row d-flex gap-4 pt-2 border-top">
                                    <div class="story-stat-item">
                                        <h3 class="stat-num playfair-display mb-0">{!! $data['stories_stat1_number'] ?? '' !!}</h3>
                                        <span class="stat-lbl dm-sans">{!! $data['stories_stat1_label'] ?? '' !!}</span>
                                    </div>
                                    <div class="story-stat-item">
                                        <h3 class="stat-num playfair-display mb-0">{!! $data['stories_stat2_number'] ?? '' !!}</h3>
                                        <span class="stat-lbl dm-sans">{!! $data['stories_stat2_label'] ?? '' !!}</span>
                                    </div>
                                </div>

                                <!-- Slider Navigation Arrows -->
                                <div class="successStoryArrows d-flex mt-4 pt-2"></div>
                            </div>
                        </div>

                        <!-- Right Column Carousel -->
                        <div class="col-lg-8">
                            <div class="happy-success-Slider">
                                @foreach ($successStoryArr as $key => $story)
                                    @php
                                        $weddingImage = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                                        if (
                                            !blank($story->wedding_photo) &&
                                            _checkStorageFileExists(
                                                'upload_path.SUCCESS_STORY_IMAGE_URL',
                                                $story->wedding_photo,
                                            )
                                        ) {
                                            $weddingImage =
                                                _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') .
                                                $story->wedding_photo;
                                        }
                                        $bridegroomName = $story->groomname . ' & ' . $story->bridename;
                                        $storyDesc = Str::limit(strip_tags($story->successmessage), 120);
                                    @endphp
                                    <div class="single-Success-profile mx-2">
                                        <div class="story-card-inner tilt-effect">
                                            <div class="story-img-container">
                                                <img class="stories-img-home w-100 object-fit-cover" height="220"
                                                    src="{{ $weddingImage }}" alt="{{ $bridegroomName }}">
                                                <span class="story-date-badge">
                                                    <iconify-icon icon="solar:calendar-date-bold"></iconify-icon>
                                                    {{ _displayDate($story->marriagedate, 'j F, Y') }}
                                                </span>
                                            </div>
                                            <div class="story-body-content p-4">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h4 class="story-couple-title playfair-display mb-0">
                                                        {{ $bridegroomName }}</h4>
                                                </div>
                                                <p class="story-quote-text dm-sans mb-3">
                                                    {{ $storyDesc }}
                                                </p>
                                                <a href="{{ route('web.successStory.details', $story->id) }}"
                                                    class="btn-story-read-more">
                                                    <span>{!! $data['stories_cta_text'] ?? '' !!}</span>
                                                    <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="Islamic-home-section py-5 position-relative overflow-hidden">
        <!-- Floating Islamic Elements -->
        <div class="islamic-shape islamic-shape-gold anim-pulse-glow d-none d-xl-block"
            style="top: 8%; right: 4%; width: 90px; height: 90px;">
            <svg viewBox="0 0 100 100" fill="none" width="100%" height="100%">
                <circle cx="50" cy="50" r="44" stroke="#C5A467" stroke-width="1.5"
                    stroke-dasharray="3 3" />
                <rect x="22" y="22" width="56" height="56" stroke="#C5A467" stroke-width="1.8" />
                <rect x="22" y="22" width="56" height="56" stroke="#C5A467" stroke-width="1.8"
                    transform="rotate(45 50 50)" />
            </svg>
        </div>
        <!-- Background Islamic Pattern & Corner Mandalas -->
        <img src="{{ asset('storage/web/home3') }}/assets/images/pattern.png" alt="Islamic Pattern"
            class="islamic-section-pattern-flourish pattern-flourish-tr d-none d-md-block">
        <div class="islamic-bg-pattern"></div>
        <div class="islamic-corner-mandala corner-bottom-right"></div>

        <div class="container py-lg-4 position-relative z-index-1">
            <div class="row align-items-center g-4 g-lg-5">
                <!-- Left Visual: Islamic Arch Frame with Couple & Floating Badges -->
                <div class="col-lg-5">
                    <div class="islamic-showcase-wrapper tilt-effect position-relative wow fadeInLeft"
                        data-wow-duration="0.8s">
                        <!-- Decorative Backing Arch Layer & Islamic Star Halo -->
                        <div class="islamic-arch-backdrop"></div>
                        {{-- <img src="{{ asset('storage/web/home3') }}/assets/images/element.png"
                            alt="Islamic Geometric Star Halo" class="element-halo-arch anim-spin-slow"> --}}

                        <!-- Main Arch Photo Frame -->
                        <div class="islamic-main-arch-frame">
                            <img src="{{ _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['why_us_image'] }}"
                                alt="{{ $configArr['web_name'] }} Couple" class="islamic-arch-photo">

                            <!-- Subtle Gradient Mask at bottom -->
                            <div class="islamic-photo-gradient-mask"></div>
                        </div>

                        <!-- Floating Trust Badges -->
                        <div class="islamic-floating-badge float-top-right wow fadeIn" data-wow-delay="0.3s">
                            <div class="float-badge-icon bg-emerald">
                                <iconify-icon icon="solar:shield-check-bold"></iconify-icon>
                            </div>
                            <div class="float-badge-text">
                                <span class="float-badge-val">{{ $data['journey_step3_tag'] ?? '' }}</span>
                                <span class="float-badge-sub">{{ $data['why_us_badge1_subtitle'] ?? '' }}</span>
                            </div>
                        </div>

                        <div class="islamic-floating-badge float-bottom-left wow fadeIn" data-wow-delay="0.5s">
                            <div class="float-badge-icon bg-gold">
                                <iconify-icon icon="solar:users-group-two-rounded-bold"></iconify-icon>
                            </div>
                            <div class="float-badge-text">
                                <span class="float-badge-val">{{ $data['why_us_badge2_title'] ?? '' }}</span>
                                <span class="float-badge-sub">{{ $data['why_us_badge2_subtitle'] ?? '' }}</span>
                            </div>
                        </div>

                        <div class="islamic-floating-badge float-bottom-right wow fadeIn" data-wow-delay="0.7s">
                            <div class="float-badge-icon bg-emerald">
                                <iconify-icon icon="solar:crown-bold"></iconify-icon>
                            </div>
                            <div class="float-badge-text">
                                <span class="float-badge-val">{{ $data['why_us_badge3_title'] ?? '' }}</span>
                                <span class="float-badge-sub">{{ $data['why_us_badge3_subtitle'] ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content & Benefits Grid -->
                <div class="col-lg-7">
                    <div class="ps-lg-3">
                        <div class="section-top-badge mb-3 wow fadeInDown">
                            <iconify-icon icon="solar:star-fall-minimalistic-bold" class="badge-icon"></iconify-icon>
                            <span>{{ $data['why_us_eyebrow'] ?? '' }}</span>
                        </div>

                        <h2 class="fts-44 fw-8 playfair-display mb-3 wow fadeInRight">
                            {!! $data['why_us_title'] ?? '' !!}
                        </h2>

                        <p class="subtitle-color-L fts-16 dm-sans mb-4 wow fadeInRight" data-wow-delay="0.1s">
                            {!! $data['why_us_subtitle'] ?? '' !!}
                        </p>

                        <!-- 4 Features Grid -->
                        <div class="row g-3 g-md-4 mb-4">
                            <!-- Feature 1 -->
                            <div class="col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="islamic-feature-box tilt-effect h-100">
                                    <div class="feature-iconbox iconbox-emerald">
                                        <iconify-icon icon="solar:shield-check-bold-duotone"></iconify-icon>
                                    </div>
                                    <h4 class="feature-title playfair-display">
                                        {{ $data['why_us_feature1_title'] ?? '' }}</h4>
                                    <p class="feature-desc dm-sans">
                                        {{ $data['why_us_feature1_desc'] ?? '' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Feature 2 -->
                            <div class="col-sm-6 wow fadeInUp" data-wow-delay="0.2s">
                                <div class="islamic-feature-box tilt-effect h-100">
                                    <div class="feature-iconbox iconbox-gold">
                                        <iconify-icon icon="solar:user-check-bold-duotone"></iconify-icon>
                                    </div>
                                    <h4 class="feature-title playfair-display">
                                        {{ $data['why_us_feature2_title'] ?? '' }}</h4>
                                    <p class="feature-desc dm-sans">
                                        {{ $data['why_us_feature2_desc'] ?? '' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Feature 3 -->
                            <div class="col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                                <div class="islamic-feature-box tilt-effect h-100">
                                    <div class="feature-iconbox iconbox-gold">
                                        <iconify-icon icon="solar:lock-keyhole-bold-duotone"></iconify-icon>
                                    </div>
                                    <h4 class="feature-title playfair-display">
                                        {{ $data['why_us_feature3_title'] ?? '' }}</h4>
                                    <p class="feature-desc dm-sans">
                                        {{ $data['why_us_feature3_desc'] ?? '' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Feature 4 -->
                            <div class="col-sm-6 wow fadeInUp" data-wow-delay="0.4s">
                                <div class="islamic-feature-box tilt-effect h-100">
                                    <div class="feature-iconbox iconbox-emerald">
                                        <iconify-icon icon="solar:filter-bold-duotone"></iconify-icon>
                                    </div>
                                    <h4 class="feature-title playfair-display">
                                        {{ $data['why_us_feature4_title'] ?? '' }}</h4>
                                    <p class="feature-desc dm-sans">
                                        {{ $data['why_us_feature4_desc'] ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action / Support Bar -->
                        <div class="d-flex flex-wrap align-items-center gap-3 pt-2 wow fadeInUp"
                            data-wow-delay="0.45s">

                            <div class="wali-support-pill d-flex align-items-center gap-2">
                                <div class="support-icon-circle">
                                    <iconify-icon icon="solar:phone-calling-bold"></iconify-icon>
                                </div>
                                <div>
                                    <span
                                        class="support-label d-block">{{ $data['why_us_support_label'] ?? '' }}</span>
                                    <strong class="support-phone">{{ $configArr['contact_no'] }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="islamic-app-section py-5 my-4 position-relative">
        <div class="container">
            <div class="islamic-app-card p-4 p-md-5 wow fadeInUp position-relative overflow-hidden"
                data-wow-duration="0.8s">
                <!-- Background Islamic Pattern & Watermarks -->
                <div class="islamic-bg-pattern-dark"></div>
                <div class="islamic-corner-mandala corner-top-right dark-theme-mandala"></div>
                <div class="app-card-arch-watermark"></div>

                <div class="row align-items-center g-4 g-lg-5 position-relative z-index-1">
                    <!-- Left Column Content -->
                    <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                        <div class="section-top-badge badge-dark-gold mb-3">
                            <iconify-icon icon="solar:smartphone-bold" class="badge-icon"></iconify-icon>
                            <span>{{ $data['app_eyebrow'] ?? '' }}</span>
                        </div>

                        <h2 class="fw-8 fts-48 text-white playfair-display mb-3">
                            {!! $data['app_title'] ?? '' !!}
                        </h2>

                        <p class="fts-16 text-white text-opacity-75 dm-sans mb-4">
                            {!! $data['app_subtitle'] ?? '' !!}
                        </p>

                        <!-- 3 Key Features -->
                        <div class="app-feature-list d-flex flex-column gap-3 mb-4">
                            <div class="app-feat-item d-flex align-items-center gap-3">
                                <div class="app-feat-icon"><iconify-icon icon="solar:bell-bing-bold"></iconify-icon>
                                </div>
                                <span class="text-white fts-14 dm-sans">{!! $data['app_feature1'] ?? '' !!}</span>
                            </div>
                            <div class="app-feat-item d-flex align-items-center gap-3">
                                <div class="app-feat-icon"><iconify-icon
                                        icon="solar:shield-keyhole-bold"></iconify-icon></div>
                                <span class="text-white fts-14 dm-sans">{!! $data['app_feature2'] ?? '' !!}</span>
                            </div>
                            <div class="app-feat-item d-flex align-items-center gap-3">
                                <div class="app-feat-icon"><iconify-icon
                                        icon="solar:chat-round-dots-bold"></iconify-icon></div>
                                <span class="text-white fts-14 dm-sans">{!! $data['app_feature3'] ?? '' !!}</span>
                            </div>
                        </div>

                        <!-- Download Buttons -->
                        <div class="apps-playstore d-flex flex-wrap gap-3 mt-4">
                            <a href="#" class="app-store-btn tilt-effect">
                                <img src="{{ asset('storage/web/home3') }}/assets/images/icon-app-store-white.png"
                                    height="46" alt="Download on the App Store">
                            </a>
                            <a href="#" class="app-store-btn tilt-effect">
                                <img src="{{ asset('storage/web/home3') }}/assets/images/icon-playstore-white.png"
                                    height="46" alt="Get it on Google Play">
                            </a>
                        </div>

                        <!-- Rating Strip -->
                        <div class="app-rating-strip d-inline-flex align-items-center gap-3 mt-4 pt-3">
                            <span class="text-white text-opacity-50">•</span>
                            <span class="text-white text-opacity-75 fts-13">{!! $data['app_rating_text'] ?? '' !!}</span>
                        </div>
                    </div>

                    <!-- Right Column Mockup -->
                    <div class="col-lg-6 mt-5 mt-lg-0 position-relative tilt-effect">
                        <div class="app-mockup-wrapper position-relative text-center">
                            <img src="{{ _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['app_image'] }}"
                                alt="{{ $configArr['web_name'] }} App Mockup"
                                class="img-fluid app-mockup-img drop-shadow-3d">

                            <!-- Floating Notification Pill 1 -->
                            <div class="app-floating-pill pill-top-left wow fadeIn" data-wow-delay="0.4s">
                                <div class="pill-icon-wrap bg-gold">
                                    <iconify-icon icon="solar:heart-angle-bold"></iconify-icon>
                                </div>
                                <div class="text-start">
                                    <span class="pill-title d-block">{{ $data['app_pill1_title'] ?? '' }}</span>
                                    <small class="pill-subtitle">{{ $data['app_pill1_subtitle'] ?? '' }}</small>
                                </div>
                            </div>

                            <!-- Floating Notification Pill 2 -->
                            <div class="app-floating-pill pill-bottom-right wow fadeIn" data-wow-delay="0.6s">
                                <div class="pill-icon-wrap bg-emerald">
                                    <iconify-icon icon="solar:shield-check-bold"></iconify-icon>
                                </div>
                                <div class="text-start">
                                    <span class="pill-title d-block">{{ $data['app_pill2_title'] ?? '' }}</span>
                                    <small class="pill-subtitle">{{ $data['app_pill2_subtitle'] ?? '' }}</small>
                                </div>
                            </div>

                            <!-- Floating Users Glass Card -->
                            <div class="app-users-glass-card wow fadeIn" data-wow-delay="0.5s">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-stack d-flex">
                                        <img src="{{ asset('storage/web/home3') }}/assets/images/usedapps1.png"
                                            class="avatar-circle" alt="User 1">
                                        <img src="{{ asset('storage/web/home3') }}/assets/images/usedapps2.png"
                                            class="avatar-circle" alt="User 2">
                                        <img src="{{ asset('storage/web/home3') }}/assets/images/usedapps3.png"
                                            class="avatar-circle" alt="User 3">
                                    </div>
                                    <div class="ms-3 text-start">
                                        <strong
                                            class="text-white d-block fts-15">{{ $data['app_users_count'] ?? '' }}</strong>
                                        <span
                                            class="fts-12 text-white text-opacity-75">{{ $data['app_users_label'] ?? '' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer-main py-5 position-relative overflow-hidden">
        <div class="islamic-bg-pattern-dark"></div>
        <div class="footer-arch-divider"></div>
        <div class="container pt-4 position-relative z-index-1">
            <div class="row g-4 g-lg-5">
                <!-- Column 1: Brand & Contact -->
                <div class="col-lg-4">
                    <div class="footer-brand mb-4">
                        <a href="{{ url('/') }}"
                            class="brand-logo-wrap d-flex align-items-center text-decoration-none">
                            <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                                alt="{{ $configArr['web_name'] }} logo" class="brand-logo">
                        </a>

                        <p class="text-white text-opacity-75 fts-14 dm-sans line-height-lg mb-4">
                            {!! $data['footer_brand_desc'] ?? '' !!}
                        </p>

                        <div class="footer-contact-list d-flex flex-column gap-2">
                            <div class="footer-contact-item d-flex align-items-center gap-3">
                                <div class="footer-contact-icon">
                                    <iconify-icon icon="solar:phone-calling-bold"></iconify-icon>
                                </div>
                                <div>
                                    <small
                                        class="text-white text-opacity-50 d-block fts-11">{{ __('messages.lbl_phone_number') }}</small>
                                    <a href="tel:{{ $configArr['contact_no'] }}" class="text-decoration-none">
                                        <strong class="text-white fts-13">
                                            {{ $configArr['contact_no'] }}
                                        </strong>
                                    </a>
                                </div>
                            </div>
                            <div class="footer-contact-item d-flex align-items-center gap-3">
                                <div class="footer-contact-icon">
                                    <iconify-icon icon="solar:letter-bold"></iconify-icon>
                                </div>
                                <div>
                                    <small
                                        class="text-white text-opacity-50 d-block fts-11">{{ __('messages.field_lbl_email_id') }}</small>
                                    <a href="mailto:{{ $configArr['contact_email'] }}" class="text-decoration-none">
                                        <strong class="text-white fts-13">
                                            {{ $configArr['contact_email'] }}
                                        </strong>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <h4 class="fts-18 fw-7 text-white playfair-display mb-3 footer-heading">
                        {{ __('messages.lbl_others') }}</h4>
                    <ul class="list-unstyled footer-links dm-sans">
                        <li><a href="{{ route('web.contactUs.index') }}">{{ __('messages.lbl_contact_us') }}</a>
                        </li>
                        <li><a
                                href="{{ route('web.successStory.index') }}">{{ __('messages.lbl_success_stories') }}</a>
                        </li>
                        <li><a
                                href="{{ route('web.advertisement.index') }}">{{ __('messages.lbl_advertise_with_us') }}</a>
                        </li>
                        @guest('web')
                            <li><a href="{{ route('web.register.index') }}">{{ __('messages.lbl_register') }}</a></li>
                            <li><a href="{{ route('web.login.index') }}">{{ __('messages.lbl_login') }}</a></li>
                        @endguest
                        <li><a href="{{ route('web.blog.index') }}">{{ __('messages.lbl_blog') }}</a></li>
                        <li><a href="{{ route('web.event.index') }}">{{ __('messages.lbl_events') }}</a></li>
                        <li><a
                                href="{{ route('web.weddingVendors.index') }}">{{ __('messages.lbl_wedding_vendors') }}</a>
                        </li>
                        <li><a target="_blank"
                                href="{{ route('affiliate.home.index') }}">{{ __('messages.lbl_become_an_affiliate') }}</a>
                        </li>
                        <li><a target="_blank"
                                href="{{ route('web.personalize.index') }}">{{ __('messages.lbl_personalize') }}</a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <h4 class="fts-18 fw-7 text-white playfair-display mb-3 footer-heading">
                        {{ __('messages.lbl_information') }}</h4>
                    <ul class="list-unstyled footer-links dm-sans">
                        <li><a href="{{ route('web.aboutUs.index') }}">{{ __('messages.lbl_about_us') }}</a></li>
                        @foreach ($cmsPages as $page)
                            <li>
                                <a
                                    href="{{ route('web.cmsPages.index', $page->page_url) }}">{{ $page->page_title }}</a>
                            </li>
                        @endforeach
                        <li><a href="{{ route('web.faq.index') }}">{{ __('messages.lbl_faqs') }}</a></li>
                    </ul>
                </div>

                <!-- Column 4: Halal Trust & Socials -->
                <div class="col-lg-4 col-md-4">
                    <h4 class="fts-18 fw-7 text-white playfair-display mb-3 footer-heading">
                        {{ __('messages.lbl_halal_trust_security') }}
                    </h4>

                    <div class="footer-trust-badge-card d-flex align-items-center gap-3 mb-3">
                        <div class="trust-badge-icon bg-emerald">
                            <iconify-icon icon="solar:shield-check-bold"></iconify-icon>
                        </div>
                        <div>
                            <strong class="text-white d-block fts-13">{!! $data['footer_trust1_title'] ?? '' !!}</strong>
                            <small class="text-white text-opacity-65 fts-11">{!! $data['footer_trust1_desc'] ?? '' !!}</small>
                        </div>
                    </div>

                    <div class="footer-trust-badge-card d-flex align-items-center gap-3 mb-4">
                        <div class="trust-badge-icon bg-gold">
                            <iconify-icon icon="solar:lock-keyhole-bold"></iconify-icon>
                        </div>
                        <div>
                            <strong class="text-white d-block fts-13">{!! $data['footer_trust2_title'] ?? '' !!}</strong>
                            <small class="text-white text-opacity-65 fts-11">{!! $data['footer_trust2_desc'] ?? '' !!}</small>
                        </div>
                    </div>

                    <h5 class="fts-14 fw-7 text-white mb-2">{{ __('messages.lbl_connect_with_us') }}</h5>
                    <div class="footer-socials d-flex gap-2">
                        @if ($configArr['instagram_link'] != '')
                            <a target="_blank" href="{{ $configArr['instagram_link'] }}" class="fts-20"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-custom-class="custom-tooltip"
                                data-bs-title="{{ __('messages.lbl_instagram') }}"><iconify-icon
                                    icon="lucide:instagram"></iconify-icon></a>
                        @endif
                        @if ($configArr['facebook_link'] != '')
                            <a target="_blank" href="{{ $configArr['facebook_link'] }}" class="fts-20"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-custom-class="custom-tooltip"
                                data-bs-title="{{ __('messages.lbl_facebook') }}"><iconify-icon
                                    icon="lucide:facebook"></iconify-icon></a>
                        @endif
                        @if ($configArr['youtube_link'] != '')
                            <a target="_blank" href="{{ $configArr['youtube_link'] }}" class="fts-20"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-custom-class="custom-tooltip"
                                data-bs-title="{{ __('messages.lbl_youtube') }}"><iconify-icon icon="lucide:youtube"
                                    class="fts-16"></iconify-icon></a>
                        @endif
                        @if ($configArr['twitter_link'] != '')
                            <a target="_blank" href="{{ $configArr['twitter_link'] }}" class="fts-20"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-custom-class="custom-tooltip"
                                data-bs-title="{{ __('messages.lbl_twitter') }}"><iconify-icon
                                    icon="lucide:twitter"></iconify-icon></a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="footer-bottom-bar d-flex flex-wrap justify-content-between align-items-center pt-4 mt-5">
                <p class="footer-copy mb-0 text-white text-opacity-65 fts-13 dm-sans">
                    {{ $configArr['footer_text'] }}
                </p>
                <span class="text-white text-opacity-60 fts-12 dm-sans mt-2 mt-md-0">
                    <iconify-icon icon="solar:verified-check-bold" class="text-warning me-1"></iconify-icon>
                    {{ $data['footer_verified_text'] ?? '' }}
                </span>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('storage/web/home3/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('storage/web/home3/assets/js/wow.js') }}"></script>
    <script src="{{ asset('storage/web/home3/assets/js/slick.min.js') }}"></script>
    <script src="{{ asset('storage/web/home3/assets/js/main.js') }}"></script>

    <script>
        const csrfToken = "{{ csrf_token() }}";
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
    </script>

    <script src="{{ asset('storage/web/custom/js/common.js') }}"></script>
    @if (Auth::check())
        <script src="{{ asset('storage/web/custom/js/express_interest.js') }}"></script>
    @endif

    @stack('scripts')
</body>

</html>

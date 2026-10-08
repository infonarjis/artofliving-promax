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

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Dancing+Script:wght@600;700&family=Gabarito:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- all css file include -->
    <link rel="stylesheet" href="{{ asset('storage/web/home2/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home2/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home2/assets/css/slick.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="{{ asset('storage/web/home2/assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home2/assets/css/responsive.css') }}">

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

    <!-- navbar section start -->
    <nav aria-label="navbar" class="site-nav">
        <div class="container">
            <div class="site-nav-inner d-flex align-items-center justify-content-between">

                <a href="{{ url('/') }}" class="brand-lockup d-flex align-items-center gap-2">
                    <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                        alt="{{ $configArr['web_name'] }} logo" class="brand-logo">
                </a>

                <button class="navbar-toggler d-lg-none" type="button" aria-label="Open menu">
                    <i class="bx bx-menu"></i>
                </button>

                <div class="nav-drawer">
                    <div class="nav-drawer-head d-lg-none d-flex justify-content-between align-items-center">
                        <a href="{{ url('/') }}" class="d-flex align-items-center gap-2">
                            <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                                alt="{{ $configArr['web_name'] }} logo" class="brand-logo">
                        </a>
                        <button class="close-toggle" aria-label="Close menu"><i class="bx bx-x"></i></button>
                    </div>

                    <ul class="nav-links d-lg-flex align-items-center">
                        <li>
                            <a href="{{ url('/') }}" class="nav-item active">
                                <iconify-icon icon="fluent:grid-16-filled" class="nav-icon"></iconify-icon>
                                <span class="nav-label">{{ __('messages.lbl_home') }}</span>
                                <span class="active-indicator"></span>
                            </a>
                        </li>

                        <li class="nav-dropdown-wrap">
                            <a href="#search" class="nav-item dropdown-toggle-fc">
                                <iconify-icon icon="bx:search" class="nav-icon"></iconify-icon>
                                <span class="nav-label">{{ __('messages.lbl_search') }}</span>
                                <i class="bx bx-chevron-down nav-arrow"></i>
                            </a>

                            <ul class="fc-dropdown-menu">
                                <li>
                                    <a href="{{ route('web.search.type', ['type' => 'quick-search']) }}"
                                        class="fc-dropdown-item">
                                        <span class="dropdown-item-icon">
                                            <iconify-icon icon="bx:bolt-circle"></iconify-icon>
                                        </span>
                                        <div class="dropdown-item-text">
                                            <span class="dropdown-item-title">
                                                {{ __('messages.lbl_quick_search') }}
                                            </span>
                                        </div>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('web.search.type', ['type' => 'advance-search']) }}"
                                        class="fc-dropdown-item">
                                        <span class="dropdown-item-icon">
                                            <iconify-icon icon="bx:slider-alt"></iconify-icon>
                                        </span>
                                        <div class="dropdown-item-text">
                                            <span class="dropdown-item-title">
                                                {{ __('messages.lbl_advance_search') }}
                                            </span>
                                        </div>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('web.search.type', ['type' => 'keyword-search']) }}"
                                        class="fc-dropdown-item">
                                        <span class="dropdown-item-icon">
                                            <iconify-icon icon="ph:text-t-bold"></iconify-icon>
                                        </span>
                                        <div class="dropdown-item-text">
                                            <span class="dropdown-item-title">
                                                {{ __('messages.lbl_keyword_search') }}
                                            </span>
                                        </div>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('web.search.type', ['type' => 'id-search']) }}"
                                        class="fc-dropdown-item">
                                        <span class="dropdown-item-icon">
                                            <iconify-icon icon="bx:id-card"></iconify-icon>
                                        </span>
                                        <div class="dropdown-item-text">
                                            <span class="dropdown-item-title">
                                                {{ __('messages.lbl_id_search') }}
                                            </span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="{{ route('web.membershipPlan.index') }}" class="nav-item">
                                <iconify-icon icon="mdi:crown-outline" class="nav-icon"></iconify-icon>
                                <span class="nav-label">{{ __('messages.lbl_membership') }}</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('web.successStory.index') }}" class="nav-item">
                                <iconify-icon icon="mdi:heart-outline" class="nav-icon"></iconify-icon>
                                <span class="nav-label">{{ __('messages.lbl_success_stories') }}</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('web.contactUs.index') }}" class="nav-item">
                                <iconify-icon icon="ph:chats-circle-bold" class="nav-icon"></iconify-icon>
                                <span class="nav-label">{{ __('messages.lbl_contact_us') }}</span>
                            </a>
                        </li>
                    </ul>
                    @php
    $getActiveLanguage = _getActiveLanguage();
    $currentLanguage = App::getLocale();
    $currentLang = collect($getActiveLanguage)->firstWhere('lang_code', $currentLanguage);
    $currentLangName = $currentLang->lang_name ?? 'English';
    $currentLangCode = $currentLang->lang_code ?? 'en';
@endphp
                    <div class="nav-cta-wrap d-flex align-items-center gap-3">
                        <a href="{{ route('web.register.index') }}" class="nav-login-link">
                            <iconify-icon icon="ph:user-plus-bold" class="nav-icon"></iconify-icon>
                            <span>{{ __('messages.lbl_register') }}</span>
                        </a>

                        <a href="{{ route('web.login.index') }}" class="btn-join-nav">
                            <iconify-icon icon="ph:sign-in-bold" class="nav-icon"></iconify-icon>
                            <span>{{ __('messages.lbl_login') }}</span>
                        </a>

                        {{-- Language Dropdown at Right Corner --}}
                        <div class="nav-item-dropdown" id="nav-language-wrapper">
                            <button type="button" class="language-selector-btn" id="language-toggle-btn"
                                aria-label="{{ __('messages.lbl_change_language') }}" aria-expanded="false"
                                aria-haspopup="true" title="{{ __('messages.lbl_change_language') }}">

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

                                            <iconify-icon icon="ph:check-bold" class="lang-item-check"></iconify-icon>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    <!-- header / hero section start -->
    @php
        $headerSectionBanner = !empty($data['hero_main_background_banner'])
            ? _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['hero_main_background_banner']
            : asset('storage/web/home2') . '/assets/images/her0-section-bg.png';
    @endphp
    <header class="hero-section" style="background-image: url('{{ $headerSectionBanner }}');">
        <div class="container position-relative hero-main-container">
            <div class="row align-items-center hero-content-row">

                <!-- Hero Left Column -->
                <div class="col-lg-7 col-xl-6">
                    <div class="hero-copy wow fadeInUp" data-wow-delay="0.1s">

                        <!-- Eyebrow Pill Badge -->
                        <div class="hero-eyebrow-badge">
                            <span class="lotus-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M12 4c1.8 3.5 3.5 6 7 7-2.5 2-4 4.5-4 8-1.5-2.5-2.3-4.8-3-6.5-.7 1.7-1.5 4-3 6.5 0-3.5-1.5-6-4-8 3.5-1 5.2-3.5 7-7z" />
                                    <path
                                        d="M12 12.5c.8-1.8 2-3 3-4.5-1.5.5-2.5 1.5-3 3.5-.5-2-1.5-3-3-3.5 1 1.5 2.2 2.7 3 4.5z" />
                                </svg>
                            </span>
                            <span>{{ $data['hero_heading'] ?? '' }}</span>
                        </div>

                        <!-- Main Heading -->
                        <h1 class="hero-title">
                            {!! $data['hero_title'] ?? '' !!}
                        </h1>

                        <!-- Lede Description -->
                        <p class="hero-lede">
                            {!! $data['hero_subtitle'] ?? '' !!}
                        </p>

                        <!-- 4 Feature Pills / Trust Row -->
                        <div class="hero-feature-pills">
                            <!-- Feature 1: Verified Profiles -->
                            <div class="feature-pill-item">
                                <div class="pill-icon-box">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none"
                                        stroke="var(--primary-color, #E04B28)" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                        <path d="m9 12 2 2 4-4" />
                                    </svg>
                                </div>
                                <div class="pill-text">
                                    {!! $data['hero_feature_title1'] ?? '' !!}
                                </div>
                            </div>

                            <!-- Feature 2: Trusted Community -->
                            <div class="feature-pill-item">
                                <div class="pill-icon-box">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none"
                                        stroke="var(--primary-color, #E04B28)" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    </svg>
                                </div>
                                <div class="pill-text">
                                    {!! $data['hero_feature_title2'] ?? '' !!}
                                </div>
                            </div>

                            <!-- Feature 3: Safe & Secure Platform -->
                            <div class="feature-pill-item">
                                <div class="pill-icon-box">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none"
                                        stroke="var(--primary-color, #E04B28)" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                                    </svg>
                                </div>
                                <div class="pill-text">
                                    {!! $data['hero_feature_title3'] ?? '' !!}
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="hero-actions d-flex flex-wrap align-items-center gap-3">
                            <a href="{{ route('web.register.index') }}" class="btn-sanatan-primary">
                                <span>{{ __('messages.lbl_get_started_for_free') }}</span>
                                <i class="bx bx-right-arrow-alt"></i>
                            </a>
                            <a href="{{ route('web.successStory.index') }}" class="btn-sanatan-secondary">
                                <span class="play-icon-circle">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="var(--primary-color, #E04B28)" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10" />
                                        <polygon points="10 8 16 12 10 16 10 8"
                                            fill="var(--primary-color, #E04B28)" />
                                    </svg>
                                </span>
                                <span>{{ __('messages.lbl_watch_our_story') }}</span>
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Couple & Calligraphy Layer (Positioned directly over the background mandala aura) -->
        <div class="hero-couple-showcase wow fadeIn" data-wow-delay="0.15s">
            <!-- Indian Hindu Couple Cutout -->
            <div class="couple-img-wrapper">
                @php
                    $coupleImg = !empty($data['hero_couple_image'])
                        ? _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['hero_couple_image']
                        : asset('storage/web/home2') . '/assets/images/header-couple-img-01.png';
                @endphp
                <img src="{{ $coupleImg }}" alt="{{ $configArr['web_name'] }} Couple" class="hero-couple-img">
            </div>

            <!-- Hand-written Script beside the temple -->
            <div class="hero-script-tag" aria-hidden="true">
                <span class="script-line">{{ $data['hero_script_word1'] ?? '' }}</span>
                <span class="script-line">{{ $data['hero_script_word2'] ?? '' }}</span>
                <span class="script-line">{{ $data['hero_script_word3'] ?? '' }}</span>
                <span class="script-line">{{ $data['hero_script_word4'] ?? '' }}</span>
                <span class="script-heart">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#9E462A"
                        stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                    </svg>
                </span>
            </div>
        </div>
        <!-- header search form / filter band -->
        <div class="search-band" id="search">
            <div class="container">
                <div class="search-card-sanatan">
                    <form class="searchForm search-form-grid" action="{{ route('web.search.searchResult') }}"
                        method="GET">
                        <!-- Field 1: Looking For -->
                        <div class="search-col">
                            <label class="fc-field-label">
                                <i class="bx bx-user fc-icon"></i>
                                <span>{{ __('messages.lbl_i_m_looking_for_a') }}</span>
                            </label>
                            <div class="fc-select-wrap">
                                <select name="gender" id="gender" class="fc-custom-select field-select">
                                    <option value="Male" title="Male">{{ __('messages.field_lbl_male') }}
                                    </option>
                                    <option value="Female" title="Female">{{ __('messages.field_lbl_female') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Field 2: Age From -->
                        @php $age = _ageRang(); @endphp
                        <div class="search-col">
                            <label class="fc-field-label">
                                <i class="bx bx-calendar fc-icon"></i>
                                <span>{{ __('messages.field_lbl_age_from') }}</span>
                            </label>
                            <div class="fc-select-wrap">
                                <select name="part_frm_age" id="part_frm_age" class="fc-custom-select field-select">
                                    @foreach ($age as $key => $valueArr)
                                        <option value="{{ $key }}" title="{{ $valueArr }}">
                                            {{ $valueArr }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Field 3: Age To -->
                        <div class="search-col">
                            <label class="fc-field-label">
                                <i class="bx bx-calendar fc-icon"></i>
                                <span>{{ __('messages.field_lbl_age_to') }}</span>
                            </label>
                            <div class="fc-select-wrap">
                                <select name="part_to_age" id="part_to_age" class="fc-custom-select field-select">
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

                        <!-- Field 4: Religion -->
                        <div class="search-col">
                            <label class="fc-field-label">
                                <span class="fc-icon om-symbol"><i class="bx bx-church"></i></span>
                                <span>{{ __('messages.field_lbl_religion') }}</span>
                            </label>
                            <div class="fc-select-wrap">
                                <select name="religion" id="religion" class="fc-custom-select field-select">
                                    <option class="list" value='' selected
                                        title="{{ __('messages.field_lbl_select_religion') }}">
                                        {{ __('messages.field_lbl_select_religion') }}</option>
                                    @foreach ($religionList as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Field 5: Location -->
                        <div class="search-col">
                            <label class="fc-field-label">
                                <i class="bx bx-map fc-icon"></i>
                                <span>{{ __('messages.field_lbl_country') }}</span>
                            </label>
                            <div class="fc-select-wrap">
                                <select name="country_id" id="country_id" class="fc-custom-select field-select">
                                    <option class="list" value='' selected
                                        title="{{ __('messages.field_lbl_country') }}">
                                        {{ __('messages.field_lbl_select_country') }}</option>
                                    @foreach ($religionList as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Search Button -->
                        <div class="search-col search-btn-col">
                            <button type="submit" class="sanatan-search-submit">
                                <i class="bx bx-search"></i>
                                <span>{{ __('messages.lbl_search') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </header>


    <!-- Sacred Journey / How Does It Work section start -->
    <section class="sanatan-journey-section" id="how-it-works">
        <!-- atmospheric warm glow -->
        <div class="journey-ambient-glow" aria-hidden="true"></div>

        <div class="container position-relative">

            <!-- Section Header -->
            <div class="journey-header text-center wow fadeInUp" data-wow-delay="0.1s">
                <div class="journey-eyebrow-pill">
                    <span class="journey-eyebrow-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M12 4c1.8 3.5 3.5 6 7 7-2.5 2-4 4.5-4 8-1.5-2.5-2.3-4.8-3-6.5-.7 1.7-1.5 4-3 6.5 0-3.5-1.5-6-4-8 3.5-1 5.2-3.5 7-7z" />
                        </svg>
                    </span>
                    <span>{!! $data['journey_eyebrow'] ?? '' !!}</span>
                </div>
                <h2 class="journey-main-title">
                    {!! $data['journey_title'] ?? '' !!}
                </h2>
                <p class="journey-subtitle">
                    {!! $data['journey_subtitle'] ?? '' !!}
                </p>
            </div>

            <!-- Progressive Step Cards Grid -->
            <div class="journey-cards-wrap position-relative">

                <!-- Connecting timeline line for desktop -->
                <div class="journey-track-line d-none d-lg-block" aria-hidden="true">
                    <svg width="100%" height="40" viewBox="0 0 900 40" fill="none"
                        xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                        <path d="M 50 20 Q 250 -2, 450 20 T 850 20" stroke="rgba(224, 75, 40, 0.22)"
                            stroke-width="2.5" stroke-dasharray="8 8" stroke-linecap="round" fill="none" />
                    </svg>
                </div>

                <div class="row g-4 justify-content-center align-items-stretch">

                    <!-- Step 1: Create Profile & Biodata -->
                    <div class="col-lg-4 col-md-6">
                        <div class="journey-step-card wow fadeInUp" data-wow-delay="0.15s">
                            <span class="step-watermark-num">01</span>

                            <div class="step-card-head">
                                <span class="step-pill-tag">STEP 01</span>
                                <span class="step-category-tag">{{ $data['journey_step1_badge'] ?? '' }}</span>
                            </div>

                            <div class="step-icon-orb orb-warm">
                                <!-- User Profile with Tilak / Biodata SVG -->
                                <svg width="34" height="34" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M9 3v3" stroke-width="2.5" />
                                    <line x1="19" y1="8" x2="19" y2="14" />
                                    <line x1="22" y1="11" x2="16" y2="11" />
                                </svg>
                            </div>

                            <h3 class="step-card-title">{!! $data['journey_step1_title'] ?? '' !!}</h3>
                            <p class="step-card-desc">
                                {!! $data['journey_step1_desc'] ?? '' !!}
                            </p>

                            <div class="step-card-foot">
                                <span class="step-feature-chip">
                                    <i class="bx bx-check-shield"></i>
                                    <span>{{ $data['journey_step1_chip'] ?? '' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Kundali Milan & Match Discovery (Featured Card) -->
                    <div class="col-lg-4 col-md-6">
                        <div class="journey-step-card step-card-featured wow fadeInUp" data-wow-delay="0.25s">
                            <span class="step-watermark-num">02</span>

                            <div class="step-card-head">
                                <span class="step-pill-tag tag-featured">STEP 02</span>
                                <span class="step-popular-badge">
                                    <i class="bx bxs-star"></i> {{ $data['journey_step2_badge'] ?? '' }}
                                </span>
                            </div>

                            <div class="step-icon-orb orb-gradient">
                                <!-- Astrological Mandala / Match Discovery SVG -->
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                                    stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9" />
                                    <circle cx="12" cy="12" r="4" />
                                    <line x1="12" y1="3" x2="12" y2="21" />
                                    <line x1="3" y1="12" x2="21" y2="12" />
                                    <circle cx="12" cy="12" r="1.5" fill="#FFFFFF" />
                                </svg>
                            </div>

                            <h3 class="step-card-title">{!! $data['journey_step2_title'] ?? '' !!}</h3>
                            <p class="step-card-desc">
                                {!! $data['journey_step2_desc'] ?? '' !!}
                            </p>

                            <div class="step-card-foot">
                                <span class="step-feature-chip chip-highlight">
                                    <i class="bx bx-sparkles"></i>
                                    <span>{{ $data['journey_step2_chip'] ?? '' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Sacred Union & Family Connection -->
                    <div class="col-lg-4 col-md-6">
                        <div class="journey-step-card wow fadeInUp" data-wow-delay="0.35s">
                            <span class="step-watermark-num">03</span>

                            <div class="step-card-head">
                                <span class="step-pill-tag">STEP 03</span>
                                <span class="step-category-tag">{{ $data['journey_step3_badge'] ?? '' }}</span>
                            </div>

                            <div class="step-icon-orb orb-warm">
                                <!-- Sacred Kalash / Holy Vivah Knot SVG -->
                                <svg width="34" height="34" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                </svg>
                            </div>

                            <h3 class="step-card-title">{!! $data['journey_step3_title'] ?? '' !!}</h3>
                            <p class="step-card-desc">
                                {!! $data['journey_step3_desc'] ?? '' !!}
                            </p>

                            <div class="step-card-foot">
                                <span class="step-feature-chip">
                                    <i class="bx bx-heart"></i>
                                    <span>{{ $data['journey_step3_chip'] ?? '' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="journey-cta-banner wow fadeInUp" data-wow-delay="0.4s">
                <div class="journey-cta-card d-flex flex-wrap align-items-center justify-content-between">
                    <div class="journey-cta-left d-flex align-items-center gap-3">
                        <div class="journey-diya-badge">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M12 2c0 3.5-3 5.5-3 9a6 6 0 0 0 12 0c0-3.5-3-5.5-3-9z" />
                                <path d="M4 14h16c0 4-3.5 7-8 7s-8-3-8-7z" />
                            </svg>
                        </div>
                        <div class="text-start">
                            <h4 class="journey-cta-head">{!! $data['journey_cta_title'] ?? '' !!}</h4>
                            <p class="journey-cta-sub">{!! $data['journey_cta_subtitle'] ?? '' !!}</p>
                        </div>
                    </div>
                    <div class="journey-cta-action">
                        <a href="{{ route('web.register.index') }}" class="btn-journey-cta">
                            <span>{{ __('messages.lbl_register_free_today') }}</span>
                            <i class="bx bx-right-arrow-alt"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Last Added Profiles section start -->
    @if ($latestProfile->isNotEmpty())
        <section class="profiles-section sanatan-profiles-section" id="profiles">
            <div class="sanatan-profiles-glow" aria-hidden="true"></div>
            <div class="container position-relative">

                <!-- Section Header -->
                <div class="section-head sanatan-profiles-head d-flex flex-wrap align-items-end justify-content-between gap-3 wow fadeInUp"
                    data-wow-delay="0.1s">
                    <div>
                        <div class="journey-eyebrow-pill mb-2">
                            <span class="eyebrow-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M12 2c0 3.5-3 5.5-3 9a6 6 0 0 0 12 0c0-3.5-3-5.5-3-9z" />
                                    <path d="M4 14h16c0 4-3.5 7-8 7s-8-3-8-7z" />
                                </svg>
                            </span>
                            <span>{!! $data['latest_profile_eyebrow'] ?? '' !!}</span>
                        </div>
                        <h2 class="sanatan-profiles-title">
                            {!! $data['latest_profile_title'] ?? '' !!}
                        </h2>
                        <p class="sanatan-profiles-subtitle">
                            {!! $data['latest_profile_subtitle'] ?? '' !!}
                        </p>
                    </div>
                    <div class="lastProfileArrows slider-arrows d-flex gap-2"></div>
                </div>

                <!-- Profiles Slider -->
                <div class="LastProfileSlider profiles-slider wow fadeInUp" data-wow-delay="0.2s">
                    @foreach ($latestProfile as $profile)
                        <div class="px-2">
                            <div class="sanatan-member-card">
                                <div class="member-card-media">
                                    @php
                                        $canView = _canViewMemberPhoto($profile, $profile->hasPhotoRequestAccess ?? '');

                                        $hasPhoto = _checkPhotoExist($profile);
                                        $profileImage = _getMemberProfileImage($profile);
                                    @endphp
                                    <a href="{{ route('web.userProfile.index', _encrypt($profile->id)) }}"
                                        class="member-img-link">
                                        @if (!$canView && $hasPhoto)
                                            <img src="{{ _getProtectedImage($profile->gender) }}"
                                                alt="{{ _profileTitle($profile) }}" class="member-img">
                                        @else
                                            <img src="{{ $profileImage }}" alt="{{ _profileTitle($profile) }}"
                                                class="member-img">
                                        @endif
                                    </a>
                                    <div class="member-media-bottom">
                                        <span class="member-community-tag">
                                            {{ optional($profile->religionData)->translated_name }} ·
                                            {{ optional($profile->casteData)->translated_name }}
                                        </span>
                                    </div>
                                </div>

                                <div class="member-card-body">
                                    <div class="member-head-line">
                                        <h4 class="member-name"><a
                                                href="{{ route('web.userProfile.index', _encrypt($profile->id)) }}">{{ _profileTitle($profile) }}</a>
                                        </h4>
                                        <span class="member-age-pill">
                                            {{ _birthdateDisplay($profile->birthdate, 0) . ', ' . _displayHeight($profile->height) }}
                                        </span>
                                    </div>

                                    <div class="member-meta-item">
                                        <i class="bx bx-briefcase-alt-2"></i>
                                        <span>{{ _displayNotAvailable(implode(', ', $profile->education_level_names) ?? null) }}</span>
                                    </div>
                                    <div class="member-meta-item">
                                        <i class="bx bx-map-pin"></i>
                                        <span>{{ _getMemberLocation($profile) }}</span>
                                    </div>
                                    <div class="member-card-actions">
                                        <a href="{{ route('web.userProfile.index', _encrypt($profile->id)) }}"
                                            class="btn-member-connect">
                                            <i class="bx bx-show"></i>
                                            <span>{{ __('messages.lbl_view_profile') }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Happy Success Stories section start  -->
    @if ($successStoryArr->isNotEmpty())
        <section class="stories-section sanatan-stories-section" id="stories">
            <div class="sanatan-stories-glow" aria-hidden="true"></div>

            <div class="container position-relative">

                <!-- Section Header -->
                <div class="sanatan-stories-header text-center wow fadeInUp" data-wow-delay="0.1s">
                    <div class="journey-eyebrow-pill mb-3 d-inline-flex">
                        <span class="eyebrow-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M12 2c0 3.5-3 5.5-3 9a6 6 0 0 0 12 0c0-3.5-3-5.5-3-9z" />
                                <path d="M4 14h16c0 4-3.5 7-8 7s-8-3-8-7z" />
                            </svg>
                        </span>
                        <span>{!! $data['success_story_eyebrow'] ?? '' !!}</span>
                    </div>
                    <h2 class="sanatan-stories-title">
                        {!! $data['success_story_title'] ?? '' !!}
                    </h2>
                    <p class="sanatan-stories-subtitle">
                        {!! $data['success_story_subtitle'] ?? '' !!}
                    </p>
                </div>

                <!-- 3-Column Luxury Vivah Stories Showcase -->
                <div class="row g-4 align-items-stretch">
                    @foreach ($successStoryArr as $key => $story)
                        @php
                            $weddingImage = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                            if (
                                !blank($story->wedding_photo) &&
                                _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $story->wedding_photo)
                            ) {
                                $weddingImage =
                                    _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $story->wedding_photo;
                            }
                            $bridegroomName = $story->groomname . ' & ' . $story->bridename;
                            $storyDesc = Str::limit(strip_tags($story->successmessage), 120);
                        @endphp
                        <div class="col-lg-4 col-md-6">
                            <div class="sanatan-story-card wow fadeInUp" data-wow-delay="0.2s">
                                <div class="story-card-media">
                                    <a href="{{ route('web.successStory.details', $story->id) }}">
                                        <img src="{{ $weddingImage }}" alt="{{ $bridegroomName }}"
                                            class="story-card-img">
                                    </a>
                                </div>
                                <div class="story-card-body">
                                    <h3 class="story-couple-names">{{ $bridegroomName }}</h3>
                                    <p class="story-meta-location">
                                        <i class="bx bx-map"></i> {{ _displayDate($story->marriagedate, 'j F, Y') }}
                                    </p>
                                    <blockquote class="story-quote">
                                        {{ $storyDesc }}
                                    </blockquote>
                                    <div class="story-card-footer">
                                        <span class="story-verified-tag"><i class="bx bxs-check-shield"></i>
                                            {{ __('messages.lbl_verified_marriage') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Vivah Milestone & Action Banner -->
                <div class="sanatan-stories-bottom-bar wow fadeInUp" data-wow-delay="0.35s">
                    <div class="row align-items-center g-3">
                        <div class="col-md-8 text-center text-md-start">
                            <div
                                class="d-flex align-items-center justify-content-center justify-content-md-start gap-3">
                                <div class="sanatan-diya-icon-badge">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M12 2c0 3.5-3 5.5-3 9a6 6 0 0 0 12 0c0-3.5-3-5.5-3-9z" />
                                        <path d="M4 14h16c0 4-3.5 7-8 7s-8-3-8-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h5 class="milestone-title mb-1">{!! $data['success_story_bottom_title'] ?? '' !!}</h5>
                                    <p class="milestone-desc mb-0">{!! $data['success_story_bottom_subtitle'] ?? '' !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center text-md-end">
                            <a href="#how-it-works" class="btn-sanatan-story-cta">
                                <span>{{ $data['success_story_cta_text'] ?? '' }}</span>
                                <i class="bx bx-right-arrow-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    @endif

    <!-- Why Choose Us section start (Sanatan Vivah Bento Showcase) -->
    <section class="why-us-section" id="why-us">
        <!-- Ambient Sacred Warmth Background Orbs -->
        <div class="why-ambient-glow glow-top" aria-hidden="true"></div>
        <div class="why-ambient-glow glow-bottom" aria-hidden="true"></div>

        <div class="container position-relative">

            <!-- Section Header -->
            <div class="why-header-wrap text-center wow fadeInUp" data-wow-delay="0.1s">
                <div class="why-sacred-pill">
                    <span class="why-pill-diya">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2">
                            <path d="M12 2c0 3.5-3 5.5-3 9a6 6 0 0 0 12 0c0-3.5-3-5.5-3-9z" fill="currentColor"
                                fill-opacity="0.3" />
                            <path d="M4 14h16c0 4-3.5 7-8 7s-8-3-8-7z" />
                        </svg>
                    </span>
                    <span>{!! $data['why_us_eyebrow'] ?? '' !!}</span>
                </div>

                <h2 class="why-sacred-heading">
                    {!! $data['why_us_title'] ?? '' !!}
                </h2>

                <p class="why-sacred-lead">
                    {!! $data['why_us_subtitle'] ?? '' !!}
                </p>

                <!-- Trust Metric Ribbon -->
                <div
                    class="why-trust-ticker d-flex flex-wrap justify-content-center align-items-center gap-3 gap-md-4">
                    <div class="ticker-item">
                        <i class="bx bxs-sun"></i>
                        <span>{!! $data['why_us_ticker1'] ?? '' !!}</span>
                    </div>
                    <div class="ticker-divider" aria-hidden="true"></div>
                    <div class="ticker-item">
                        <i class="bx bxs-badge-check"></i>
                        <span>{!! $data['why_us_ticker2'] ?? '' !!}</span>
                    </div>
                    <div class="ticker-divider" aria-hidden="true"></div>
                    <div class="ticker-item">
                        <i class="bx bxs-heart-circle"></i>
                        <span>{!! $data['why_us_ticker3'] ?? '' !!}</span>
                    </div>
                </div>
            </div>

            <!-- Main Bento Grid Showcase -->
            <div class="why-bento-grid">

                <!-- Column 1: Left Wing (Vedic Compatibility & Parivaar Privacy) -->
                <div class="why-bento-col why-col-left">

                    <!-- Card 1: Vedic Astrological Harmony -->
                    <div class="why-feature-bento-card wow fadeInLeft" data-wow-delay="0.2s">
                        <div class="why-card-top-bar">
                            <div class="why-feature-icon orb-marigold">
                                <i class="bx bxs-sun"></i>
                            </div>
                            <span class="why-badge-pill pill-marigold">{{ $data['why_us_sec1_badge'] ?? '' }}</span>
                        </div>

                        <h3 class="why-card-title">{{ $data['why_us_sec1_title'] ?? '' }}</h3>
                        <p class="why-card-desc">
                            {{ $data['why_us_sec1_subtitle'] ?? '' }}
                        </p>

                        <!-- Interactive Widget: Kundali Alignment Cloud -->
                        <div class="why-resonance-widget">
                            <div class="resonance-header">
                                <span class="resonance-label"><i class="bx bx-compass"></i>
                                    {{ $data['why_us_sec1_widget_label'] ?? '' }}</span>
                                <span class="resonance-score">{{ $data['why_us_sec1_widget_score'] ?? '' }}</span>
                            </div>
                            <div class="resonance-tags">
                                <span class="res-tag"><i class="bx bx-check"></i>
                                    {{ $data['why_us_sec1_tag1'] ?? '' }}</span>
                                <span class="res-tag"><i class="bx bx-check"></i>
                                    {{ $data['why_us_sec1_tag2'] ?? '' }}</span>
                                <span class="res-tag"><i class="bx bx-check"></i>
                                    {{ $data['why_us_sec1_tag3'] ?? '' }}</span>
                                <span class="res-tag"><i class="bx bx-check"></i>
                                    {{ $data['why_us_sec1_tag4'] ?? '' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Parivaar Dignity & Privacy First -->
                    <div class="why-feature-bento-card wow fadeInLeft" data-wow-delay="0.3s">
                        <div class="why-card-top-bar">
                            <div class="why-feature-icon orb-orange">
                                <i class="bx bx-shield-quarter"></i>
                            </div>
                            <span class="why-badge-pill pill-orange">{{ $data['why_us_sec3_badge'] ?? '' }}</span>
                        </div>

                        <h3 class="why-card-title">{{ $data['why_us_sec3_title'] ?? '' }}</h3>
                        <p class="why-card-desc">
                            {{ $data['why_us_sec3_subtitle'] ?? '' }}
                        </p>

                        <!-- Interactive Widget: Parivaar Privacy Guard -->
                        <div class="why-privacy-widget">
                            <div class="privacy-row">
                                <span class="privacy-name"><i class="bx bx-image-alt"></i>
                                    {{ $data['why_us_sec3_row1_label'] ?? '' }}</span>
                                <span class="privacy-state state-locked"><i class="bx bxs-lock-alt"></i>
                                    {{ $data['why_us_sec3_row1_status'] ?? '' }}</span>
                            </div>
                            <div class="privacy-row">
                                <span class="privacy-name"><i class="bx bx-phone-call"></i>
                                    {{ $data['why_us_sec3_row2_label'] ?? '' }}</span>
                                <span class="privacy-state state-active"><i class="bx bx-check-double"></i>
                                    {{ $data['why_us_sec3_row2_status'] ?? '' }}</span>
                            </div>
                            <div class="privacy-row">
                                <span class="privacy-name"><i class="bx bx-camera-off"></i>
                                    {{ $data['why_us_sec3_row3_label'] ?? '' }}</span>
                                <span class="privacy-state state-shielded"><i class="bx bxs-shield"></i>
                                    {{ $data['why_us_sec3_row3_status'] ?? '' }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Column 2: Mandap Arch Centerpiece -->
                <div class="why-bento-col why-col-center">
                    <div class="cathedral-arch-wrapper wow zoomIn" data-wow-delay="0.25s">

                        <!-- Heavenly Radiance Glow Layer -->
                        <div class="cathedral-halo" aria-hidden="true"></div>

                        <!-- The Main Mandap Arch Frame (Image & Gradient clipped) -->
                        <div class="cathedral-arch-frame">
                            <img src="{{ _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['why_us_image'] }}"
                                alt="Auspicious Hindu Vivah Ceremony under Floral Mandap" class="cathedral-img">
                            <div class="cathedral-gradient-overlay"></div>
                        </div>

                        <!-- Floating Elements placed on wrapper so they NEVER get clipped by overflow:hidden -->
                        <!-- Top Floating Pill: Pavitra Bandhan -->
                        <div class="cathedral-floating-top">
                            <span class="top-cross-symbol">🪔</span>
                            <span>{{ $data['why_us_mandap_top_badge'] ?? '' }}</span>
                        </div>

                        <!-- Floating Stat 1: Marriages Badge (Left) -->
                        <div class="cathedral-badge badge-left-church">
                            <div class="badge-icon-wrap icon-marigold">
                                <i class="bx bxs-star"></i>
                            </div>
                            <div class="badge-info">
                                <strong>{{ $data['why_us_mandap_badge1_title'] ?? '' }}</strong>
                                <span>{{ $data['why_us_mandap_badge1_subtitle'] ?? '' }}</span>
                            </div>
                        </div>

                        <!-- Floating Stat 2: 100% Family Verified Badge (Right) -->
                        <div class="cathedral-badge badge-right-screened">
                            <div class="badge-icon-wrap icon-verified">
                                <i class="bx bxs-check-shield"></i>
                            </div>
                            <div class="badge-info">
                                <strong>{{ $data['why_us_mandap_badge2_title'] ?? '' }}</strong>
                                <span>{{ $data['why_us_mandap_badge2_subtitle'] ?? '' }}</span>
                            </div>
                        </div>

                        <!-- Bottom Glass Pedestal (Vedic Shloka & Vivah Blessing) -->
                        <div class="cathedral-pedestal-card">
                            <div class="pedestal-quote">
                                <i class="bx bxs-quote-alt-left quote-icon"></i>
                                <p>“{{ $data['why_us_mandap_quote'] ?? '' }}”</p>
                                <span class="scripture-cite">— {{ $data['why_us_mandap_quote_cite'] ?? '' }}</span>
                            </div>
                            <div class="pedestal-footer-row">
                                <div class="pedestal-seal">
                                    <i class="bx bxs-badge-check"></i>
                                    <span>{{ $data['why_us_mandap_footer_text'] ?? '' }}</span>
                                </div>
                                <span class="pedestal-stars">★★★★★</span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Column 3: Right Wing (100% Vetted & Dedicated Elder Support) -->
                <div class="why-bento-col why-col-right">

                    <!-- Card 3: 100% Verified Profiles -->
                    <div class="why-feature-bento-card wow fadeInRight" data-wow-delay="0.2s">
                        <div class="why-card-top-bar">
                            <div class="why-feature-icon orb-emerald">
                                <i class="bx bx-check-shield"></i>
                            </div>
                            <span class="why-badge-pill pill-emerald">{{ $data['why_us_sec2_badge'] ?? '' }}</span>
                        </div>

                        <h3 class="why-card-title">{{ $data['why_us_sec2_title'] ?? '' }}</h3>
                        <p class="why-card-desc">
                            {{ $data['why_us_sec2_subtitle'] ?? '' }}
                        </p>

                        <!-- Interactive Widget: 3-Step Vetting Pipeline -->
                        <div class="why-vetting-pipeline">
                            <div class="vetting-step step-done">
                                <div class="step-marker"><i class="bx bx-check"></i></div>
                                <div class="step-content">
                                    <strong>{{ $data['why_us_sec2_step1_title'] ?? '' }}</strong>
                                    <span>{{ $data['why_us_sec2_step1_desc'] ?? '' }}</span>
                                </div>
                            </div>
                            <div class="vetting-step step-done">
                                <div class="step-marker"><i class="bx bx-check"></i></div>
                                <div class="step-content">
                                    <strong>{{ $data['why_us_sec2_step2_title'] ?? '' }}</strong>
                                    <span>{{ $data['why_us_sec2_step2_desc'] ?? '' }}</span>
                                </div>
                            </div>
                            <div class="vetting-step step-done">
                                <div class="step-marker"><i class="bx bx-check"></i></div>
                                <div class="step-content">
                                    <strong>{{ $data['why_us_sec2_step3_title'] ?? '' }}</strong>
                                    <span>{{ $data['why_us_sec2_step3_desc'] ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Dedicated Family & Elder Support -->
                    <div class="why-feature-bento-card wow fadeInRight" data-wow-delay="0.3s">
                        <div class="why-card-top-bar">
                            <div class="why-feature-icon orb-rose">
                                <i class="bx bx-user-voice"></i>
                            </div>
                            <span class="why-badge-pill pill-rose">{{ $data['why_us_sec4_badge'] ?? '' }}</span>
                        </div>

                        <h3 class="why-card-title">{{ $data['why_us_sec4_title'] ?? '' }}</h3>
                        <p class="why-card-desc">
                            {{ $data['why_us_sec4_subtitle'] ?? '' }}
                        </p>

                        <!-- Interactive Widget: Live Relationship Manager Beacon -->
                        <div class="why-pastoral-callout">
                            <div class="callout-advisor-row">
                                <div class="advisor-avatars">
                                    <span class="advisor-circle bg-p1"><i class="bx bx-user"></i></span>
                                    <span class="advisor-circle bg-p2"><i class="bx bx-heart"></i></span>
                                </div>
                                <div class="advisor-status">
                                    <span class="status-live-dot"></span>
                                    <span>{!! $data['why_us_sec4_status'] ?? '' !!}</span>
                                </div>
                            </div>
                            <p class="advisor-subtext">
                                {{ $data['why_us_sec4_desc'] ?? '' }}
                            </p>
                            <a href="#how-it-works" class="btn-pastoral-help">
                                <span>{{ $data['why_us_sec4_cta_text'] ?? '' }}</span>
                                <i class="bx bx-right-arrow-alt"></i>
                            </a>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Bottom Sanatan Community Unity Ribbon -->
            <div class="why-ecumenical-dock wow fadeInUp" data-wow-delay="0.35s">
                <div
                    class="dock-inner d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3">
                    <div class="dock-title-group d-flex align-items-center gap-2">
                        <div class="dock-cross-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M12 2c0 3.5-3 5.5-3 9a6 6 0 0 0 12 0c0-3.5-3-5.5-3-9z" fill="currentColor"
                                    fill-opacity="0.3" />
                                <path d="M4 14h16c0 4-3.5 7-8 7s-8-3-8-7z" />
                            </svg>
                        </div>
                        <div>
                            <span class="dock-main-label">{{ $data['why_us_dock_title'] ?? '' }}</span>
                            <span class="dock-sub-label">{{ $data['why_us_dock_subtitle'] ?? '' }}</span>
                        </div>
                    </div>

                    <!-- Hindu Community Interactive Badges -->
                    {{-- NOTE: this taxonomy list (Brahmin, Rajput, etc.) is intentionally left static —
                         it mirrors a fixed community taxonomy elsewhere in the app rather than free-text
                         admin copy. Flag this to me if you'd like it pulled from a config/DB list instead. --}}
                    <div class="dock-tags-wrap d-flex flex-wrap align-items-center gap-2">
                        <span class="denom-pill"><span class="denom-dot"></span>Brahmin</span>
                        <span class="denom-pill"><span class="denom-dot"></span>Rajput / Kshatriya</span>
                        <span class="denom-pill"><span class="denom-dot"></span>Vaishya / Agarwal</span>
                        <span class="denom-pill"><span class="denom-dot"></span>Maratha</span>
                        <span class="denom-pill"><span class="denom-dot"></span>Kayastha</span>
                        <span class="denom-pill"><span class="denom-dot"></span>Reddy &amp; Kamma</span>
                        <span class="denom-pill"><span class="denom-dot"></span>Iyer &amp; Iyengar</span>
                        <span class="denom-pill"><span class="denom-dot"></span>Jain &amp; Maheshwari</span>
                        <span class="denom-pill"><span class="denom-dot"></span>Punjabi &amp; Sindhi</span>
                        <span class="denom-pill"><span class="denom-dot"></span>Lingayat &amp; Nair</span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Vishwa Sanatan Global Reach section start -->
    <section class="vishwa-reach-section" id="global-reach">
        <div class="vishwa-reach-ambient-glow" aria-hidden="true"></div>
        <div class="container position-relative">

            <!-- Inner Showcase Container -->
            <div class="vishwa-reach-card wow fadeInUp" data-wow-delay="0.15s">

                <!-- Header -->
                <div class="vishwa-reach-header text-center">
                    <div class="vishwa-pill-badge mb-3">
                        <span class="vishwa-pill-globe">
                            <i class="bx bx-globe"></i>
                        </span>
                        <span>{{ $data['global_reach_eyebrow'] ?? '' }}</span>
                    </div>

                    <h2 class="vishwa-reach-title">
                        {!! $data['global_reach_title'] ?? '' !!}
                    </h2>

                    <p class="vishwa-reach-desc mx-auto">
                        {{ $data['global_reach_subtitle'] ?? '' }}
                    </p>
                </div>

                <!-- Bento Grid: Left Global Hub Card (5 cols) + Right 4 Metric Widgets (7 cols) -->
                <div class="row g-4 align-items-stretch mt-1">

                    <!-- Left: Global Diaspora Interactive Showcase Card -->
                    <div class="col-lg-5">
                        <div class="vishwa-diaspora-hub-card h-100">
                            <div class="hub-card-top d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="hub-flame-icon"><i class="bx bxs-sun"></i></span>
                                    <span class="hub-title">{{ $data['global_reach_hub_title'] ?? '' }}</span>
                                </div>
                                <span class="hub-live-badge">
                                    <span class="hub-live-dot"></span>
                                    <span>{{ $data['global_reach_hub_badge'] ?? '' }}</span>
                                </span>
                            </div>

                            <p class="hub-subtitle">
                                {{ $data['global_reach_hub_desc'] ?? '' }}
                            </p>

                            <!-- Diaspora Node List -->
                            <div class="hub-nodes-list">
                                <div class="hub-node-item">
                                    <span class="hub-node-flag">{{ $data['global_reach_node1_flag'] ?? '' }}</span>
                                    <div class="hub-node-info">
                                        <strong>{{ $data['global_reach_node1_title'] ?? '' }}</strong>
                                        <span>{{ $data['global_reach_node1_desc'] ?? '' }}</span>
                                    </div>
                                    <span class="hub-node-count">{{ $data['global_reach_node1_count'] ?? '' }}</span>
                                </div>

                                <div class="hub-node-item">
                                    <span class="hub-node-flag">{{ $data['global_reach_node2_flag'] ?? '' }}</span>
                                    <div class="hub-node-info">
                                        <strong>{{ $data['global_reach_node2_title'] ?? '' }}</strong>
                                        <span>{{ $data['global_reach_node2_desc'] ?? '' }}</span>
                                    </div>
                                    <span class="hub-node-count">{{ $data['global_reach_node2_count'] ?? '' }}</span>
                                </div>

                                <div class="hub-node-item">
                                    <span class="hub-node-flag">{{ $data['global_reach_node3_flag'] ?? '' }}</span>
                                    <div class="hub-node-info">
                                        <strong>{{ $data['global_reach_node3_title'] ?? '' }}</strong>
                                        <span>{{ $data['global_reach_node3_desc'] ?? '' }}</span>
                                    </div>
                                    <span class="hub-node-count">{{ $data['global_reach_node3_count'] ?? '' }}</span>
                                </div>

                                <div class="hub-node-item">
                                    <span class="hub-node-flag">{{ $data['global_reach_node4_flag'] ?? '' }}</span>
                                    <div class="hub-node-info">
                                        <strong>{{ $data['global_reach_node4_title'] ?? '' }}</strong>
                                        <span>{{ $data['global_reach_node4_desc'] ?? '' }}</span>
                                    </div>
                                    <span class="hub-node-count">{{ $data['global_reach_node4_count'] ?? '' }}</span>
                                </div>

                                <div class="hub-node-item">
                                    <span class="hub-node-flag">{{ $data['global_reach_node5_flag'] ?? '' }}</span>
                                    <div class="hub-node-info">
                                        <strong>{{ $data['global_reach_node5_title'] ?? '' }}</strong>
                                        <span>{{ $data['global_reach_node5_desc'] ?? '' }}</span>
                                    </div>
                                    <span class="hub-node-count">{{ $data['global_reach_node5_count'] ?? '' }}</span>
                                </div>
                            </div>

                            <!-- Bottom NRI CTA Action -->
                            <div class="hub-card-footer mt-4 pt-3 d-flex align-items-center justify-content-between">
                                <span class="hub-footer-text"><i class="bx bx-check-shield"></i>
                                    {{ $data['global_reach_hub_footer_text'] ?? '' }}</span>
                                <a href="#profiles" class="btn-hub-explore">
                                    <span>{{ $data['global_reach_hub_cta_text'] ?? '' }}</span>
                                    <i class="bx bx-right-arrow-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Right: 4 Rich Stat Modules Grid -->
                    <div class="col-lg-7">
                        <div class="row g-3 h-100">

                            <!-- Module 1: Vedic Traditions & Gotras -->
                            <div class="col-sm-6">
                                <div class="vishwa-metric-card h-100">
                                    <div class="vishwa-metric-top">
                                        <div class="vishwa-icon-orb orb-marigold">
                                            <i class="bx bxs-sun"></i>
                                        </div>
                                        <span class="vishwa-micro-tag">Gotra Aligned</span>
                                    </div>
                                    <h3 class="vishwa-metric-num">{{ $data['global_reach_metric1_number'] ?? '' }}
                                    </h3>
                                    <h4 class="vishwa-metric-label">{{ $data['global_reach_metric1_label'] ?? '' }}
                                    </h4>
                                    <p class="vishwa-metric-desc">{{ $data['global_reach_metric1_desc'] ?? '' }}</p>
                                    <div class="vishwa-micro-pills mt-2">
                                        <span>Vashishta</span>
                                        <span>Kashyapa</span>
                                        <span>Bharadwaja</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Module 2: Countries Reached -->
                            <div class="col-sm-6">
                                <div class="vishwa-metric-card h-100">
                                    <div class="vishwa-metric-top">
                                        <div class="vishwa-icon-orb orb-orange">
                                            <i class="bx bx-globe"></i>
                                        </div>
                                        <span class="vishwa-micro-tag">Worldwide</span>
                                    </div>
                                    <h3 class="vishwa-metric-num">{{ $data['global_reach_metric2_number'] ?? '' }}
                                    </h3>
                                    <h4 class="vishwa-metric-label">{{ $data['global_reach_metric2_label'] ?? '' }}
                                    </h4>
                                    <p class="vishwa-metric-desc">{{ $data['global_reach_metric2_desc'] ?? '' }}</p>
                                    <div class="vishwa-micro-pills mt-2">
                                        <span>🇺🇸 USA</span>
                                        <span>🇬🇧 UK</span>
                                        <span>🇦🇺 AUS</span>
                                        <span>🇦🇪 UAE</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Module 3: Cities & Mandir Networks -->
                            <div class="col-sm-6">
                                <div class="vishwa-metric-card h-100">
                                    <div class="vishwa-metric-top">
                                        <div class="vishwa-icon-orb orb-amber">
                                            <i class="bx bxs-institution"></i>
                                        </div>
                                        <span class="vishwa-micro-tag">Local Chapters</span>
                                    </div>
                                    <h3 class="vishwa-metric-num">{{ $data['global_reach_metric3_number'] ?? '' }}
                                    </h3>
                                    <h4 class="vishwa-metric-label">{{ $data['global_reach_metric3_label'] ?? '' }}
                                    </h4>
                                    <p class="vishwa-metric-desc">{{ $data['global_reach_metric3_desc'] ?? '' }}</p>
                                    <div class="vishwa-micro-pills mt-2">
                                        <span>Mumbai</span>
                                        <span>Bengaluru</span>
                                        <span>Delhi NCR</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Module 4: Languages & Dialects -->
                            <div class="col-sm-6">
                                <div class="vishwa-metric-card h-100">
                                    <div class="vishwa-metric-top">
                                        <div class="vishwa-icon-orb orb-emerald">
                                            <i class="bx bx-conversation"></i>
                                        </div>
                                        <span class="vishwa-micro-tag">Mother Tongues</span>
                                    </div>
                                    <h3 class="vishwa-metric-num">{{ $data['global_reach_metric4_number'] ?? '' }}
                                    </h3>
                                    <h4 class="vishwa-metric-label">{{ $data['global_reach_metric4_label'] ?? '' }}
                                    </h4>
                                    <p class="vishwa-metric-desc">{{ $data['global_reach_metric4_desc'] ?? '' }}
                                    </p>
                                    <div class="vishwa-micro-pills mt-2">
                                        <span>Hindi</span>
                                        <span>Tamil</span>
                                        <span>Telugu</span>
                                        <span>Marathi</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- Best way to manage section / Sanatan Mobile App Experience -->
    <section class="app-section" id="mobile-app">
        <div class="container">
            <div class="app-panel sanatan-app-panel wow fadeInUp" data-wow-delay="0.15s">

                <!-- Ambient atmospheric glow circles inside panel -->
                <div class="sanatan-app-ambient-glow" aria-hidden="true"></div>

                <div class="row align-items-center grid-app-layout position-relative">

                    <!-- Left: Content & Store Badges -->
                    <div class="col-lg-6">
                        <div class="app-copy sanatan-app-copy">

                            <div class="sanatan-app-pill mb-3">
                                <span class="app-pill-flame">
                                    <i class="bx bxs-sun"></i>
                                </span>
                                <span>{{ $data['app_eyebrow'] ?? '' }}</span>
                            </div>

                            <h2 class="sanatan-app-title">
                                {!! $data['app_title'] ?? '' !!}
                            </h2>

                            <p class="sanatan-app-lede">
                                {!! $data['app_subtitle'] ?? '' !!}
                            </p>

                            <!-- App Feature Checklist -->
                            <div class="sanatan-app-features-list">
                                <div class="sanatan-app-feature-item">
                                    <div class="sanatan-feat-icon"><i class="bx bx-check"></i></div>
                                    <span>{{ $data['app_feature1'] ?? '' }}</span>
                                </div>
                                <div class="sanatan-app-feature-item">
                                    <div class="sanatan-feat-icon"><i class="bx bx-check"></i></div>
                                    <span>{{ $data['app_feature2'] ?? '' }}</span>
                                </div>
                                <div class="sanatan-app-feature-item">
                                    <div class="sanatan-feat-icon"><i class="bx bx-check"></i></div>
                                    <span>{{ $data['app_feature3'] ?? '' }}</span>
                                </div>
                            </div>

                            <!-- Store Badges & Quick Action -->
                            <div class="d-flex flex-wrap align-items-center gap-3 mt-4 pt-2">
                                @if (isset($configArr['ios_app_link']) && !blank($configArr['ios_app_link']))
                                    <a target="_blank" href="{{ $configArr['ios_app_link'] }}"
                                        class="sanatan-store-button">
                                        <i class="bx bxl-apple"></i>
                                        <div class="text-start">
                                            <small>{{ __('messages.lbl_download_on_the') }}</small>
                                            <strong>{{ __('messages.lbl_app_store') }}</strong>
                                        </div>
                                    </a>
                                @endif
                                @if (isset($configArr['android_app_link']) && !blank($configArr['android_app_link']))
                                    <a target="_blank" href="{{ $configArr['android_app_link'] }}"
                                        class="sanatan-store-button">
                                        <i class="bx bxl-play-store"></i>
                                        <div class="text-start">
                                            <small>{{ __('messages.lbl_get_it_on') }}</small>
                                            <strong>{{ __('messages.lbl_play_store') }}</strong>
                                        </div>
                                    </a>
                                @endif

                            </div>

                            <!-- Ratings & Social Proof -->
                            <div class="d-flex flex-wrap align-items-center gap-3 mt-4 pt-2">

                                <div class="sanatan-app-reviews">
                                    <p class="app-users-line mb-0">{!! $data['app_social_proof'] ?? '' !!}</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Right: Interactive Smartphone Mockup with Floating Badges -->
                    <div class="col-lg-6">
                        <div class="app-mockup-wrap sanatan-app-mockup-wrap text-center">

                            <!-- Backlight Halo -->
                            <div class="sanatan-mockup-halo" aria-hidden="true"></div>

                            <!-- Floating Card 1 (Top Left) -->
                            <div class="sanatan-app-float-badge float-top-left wow fadeIn" data-wow-delay="0.3s">
                                <div class="float-badge-icon icon-marigold">
                                    <i class="bx bxs-sun"></i>
                                </div>
                                <div class="text-start">
                                    <strong>{{ $data['app_badge1_title'] ?? '' }}</strong>
                                    <span>{{ $data['app_badge1_subtitle'] ?? '' }}</span>
                                </div>
                            </div>

                            <!-- Smartphone Image -->
                            <img src="{{ _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['app_image'] }}"
                                alt="{{ $configArr['web_name'] }} Matrimonial Mobile App"
                                class="app-mockup sanatan-app-mockup">

                            <!-- Floating Card 2 (Bottom Right) -->
                            <div class="sanatan-app-float-badge float-bottom-right wow fadeIn" data-wow-delay="0.4s">
                                <div class="float-badge-icon icon-emerald">
                                    <i class="bx bxs-check-shield"></i>
                                </div>
                                <div class="text-start">
                                    <strong>{{ $data['app_badge2_title'] ?? '' }}</strong>
                                    <span>{{ $data['app_badge2_subtitle'] ?? '' }}</span>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>



    <!-- Sanatan Community & Heritage Directory Section Start -->
    <section class="sanatan-directory-section fc-directory-section" id="community">
        <div class="sanatan-directory-glow" aria-hidden="true"></div>
        <div class="container position-relative">

            <!-- Section Header -->
            <div class="sanatan-directory-header text-center wow fadeInUp" data-wow-delay="0.1s">
                <div class="sanatan-dir-pill mb-3">
                    <span class="dir-pill-flame"><i class="bx bxs-compass"></i></span>
                    <span>{{ $data['directory_eyebrow'] ?? '' }}</span>
                </div>

                <h2 class="sanatan-directory-title">
                    {!! $data['directory_title'] ?? '' !!}
                </h2>

                <p class="sanatan-directory-desc mx-auto">
                    {{ $data['directory_subtitle'] ?? '' }}
                </p>

            </div>

            <!-- Interactive Community Segmented Nav Tabs -->
            <div class="sanatan-tabs-wrapper mt-4">
                <ul class="nav nav-pills sanatan-community-nav justify-content-center" id="sanatanCommunityTab"
                    role="tablist">
                    @foreach ($matrimonyPagesData as $type => $items)
                        @php
                            $tabId = Str::slug($type);
                        @endphp
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                id="{{ $tabId }}-tab" data-bs-toggle="pill"
                                data-bs-target="#{{ $tabId }}-panel" type="button" role="tab"
                                aria-controls="{{ $tabId }}-panel"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $items['label'] }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
            <!-- Tab Panes Content Area -->
            <div class="tab-content sanatan-tab-content mt-4" id="sanatanCommunityTabContent">
                <!-- Pane 1: Communities & Sub-Castes -->
                @foreach ($matrimonyPagesData as $type => $items)
                    @php
                        $tabId = Str::slug($type);
                    @endphp
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                        id="{{ $tabId }}-panel" role="tabpanel" aria-labelledby="{{ $tabId }}-tab"
                        tabindex="0">
                        <div class="sanatan-dir-panel">
                            <div class="sanatan-tag-cloud">
                                @foreach ($items['items'] as $item)
                                    <a href="{{ route('web.matrimony.index', $item['slug']) }}"
                                        class="sanatan-dir-tag">
                                        {{ $item['matrimony_name'] ?: $item['matrimony_name_old'] ?? 'N/A' }}
                                    </a>
                                @endforeach
                                <a href="{{ route('web.matrimony.moreDetails', Str::slug($type)) }}"
                                    class="sanatan-dir-tag-more">{{ __('messages.lbl_more_details') }} →</a>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    <!-- footer section start -->
    <footer class="sanatan-footer-main fc-footer-main">

        <!-- Top Ambient Glow -->
        <div class="sanatan-footer-glow fc-footer-glow" aria-hidden="true"></div>

        <div class="container position-relative z-1">

            <!-- Top Section: Auspicious Matches & Newsletter Banner -->
            <div class="sanatan-footer-newsletter-card fc-footer-newsletter-card wow fadeInUp" data-wow-delay="0.1s">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="sanatan-newsletter-icon fc-newsletter-icon">
                                <i class="bx bx-envelope-open"></i>
                            </div>
                            <div>
                                <h4 class="sanatan-newsletter-title fc-newsletter-title">
                                    {{ $data['footer_newsletter_title'] ?? '' }}
                                </h4>
                                <p class="sanatan-newsletter-sub fc-newsletter-sub mb-0">
                                    {{ $data['footer_newsletter_subtitle'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="sanatan-newsletter-wrapper fc-newsletter-wrapper">
                            <form action="{{ route('web.newsletter.subscribe') }}" method="POST"
                                class="sanatan-newsletter-form fc-newsletter-form newsletterForm">
                                @csrf

                                <div class="sanatan-input-group fc-input-group">
                                    <i class="bx bx-envelope sanatan-input-icon fc-input-icon"></i>

                                    <input type="email" name="email"
                                        class="sanatan-newsletter-input fc-newsletter-input"
                                        placeholder="Enter your email address..." required>

                                    <button type="submit" class="sanatan-newsletter-btn fc-newsletter-btn">
                                        <span class="btn-text">{{ __('messages.lbl_subscribe_free') }}</span>
                                        <span class="btn-loader"></span>
                                        <i class="bx bx-send"></i>
                                    </button>
                                </div>

                                <div class="newsletter-message" style="display: none;"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Footer Grid (5 Columns - All Options Kept Same) -->
            <div class="row g-4 g-lg-5 sanatan-footer-grid fc-footer-grid">

                <!-- Column 1: Brand & Mission -->
                <div class="col-lg-3 col-md-4">
                    <div class="sanatan-footer-brand-wrap fc-footer-brand-wrap">
                        <a href="{{ url('/') }}" class="d-inline-flex align-items-center gap-2 mb-3">
                            <div class="sanatan-footer-logo-badge fc-footer-logo-badge">
                                <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                                    class="footer-logo" alt="{{ $configArr['web_name'] }} Logo">
                            </div>
                        </a>

                        <p class="sanatan-footer-mission fc-footer-mission">
                            {{ $configArr['full_address'] }}
                        </p>
                    </div>
                </div>

                <!-- Column 2: Help & support -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h4 class="sanatan-footer-title fc-footer-title">{{ __('messages.lbl_help_support') }}</h4>
                    <ul class="sanatan-footer-links fc-footer-links">
                        <li><a href="{{ route('web.contactUs.index') }}">{{ __('messages.lbl_contact_us') }}</a>
                        </li>
                        <li><a
                                href="{{ route('web.successStory.index') }}">{{ __('messages.lbl_success_stories') }}</a>
                        </li>
                        <li><a
                                href="{{ route('web.advertisement.index') }}">{{ __('messages.lbl_advertise_with_us') }}</a>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: Information -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h4 class="sanatan-footer-title fc-footer-title">{{ __('messages.lbl_information') }}</h4>
                    <ul class="sanatan-footer-links fc-footer-links">
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

                <!-- Column 4: Others -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h4 class="sanatan-footer-title fc-footer-title">{{ __('messages.lbl_others') }}</h4>
                    <ul class="sanatan-footer-links fc-footer-links">
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

                <!-- Column 5: Contact info -->
                <div class="col-lg-3 col-md-6 col-6">
                    <h4 class="sanatan-footer-title fc-footer-title">{{ __('messages.lbl_contact_info') }}</h4>
                    <div class="sanatan-footer-contact-box fc-footer-contact-box">
                        <div class="sanatan-contact-item fc-contact-item">
                            <i class="bx bx-phone-call"></i>
                            <div>
                                <small>{{ __('messages.lbl_phone_number') }}</small>
                                <a href="tel:{{ $configArr['contact_no'] }}">{{ $configArr['contact_no'] }}</a>
                            </div>
                        </div>
                        <div class="sanatan-contact-item fc-contact-item">
                            <i class='bx bx-envelope'></i>
                            <div>
                                <small>{{ __('messages.field_lbl_email_id') }}</small>
                                <a
                                    href="mailto:{{ $configArr['contact_email'] }}">{{ $configArr['contact_email'] }}</a>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Circles -->
                    <div class="sanatan-footer-socials fc-footer-socials d-flex align-items-center gap-2 mt-4">
                        @if (!empty($configArr['instagram_link']))
                            <a href="{{ $configArr['instagram_link'] }}" target="_blank"
                                rel="noopener noreferrer" aria-label="Instagram">
                                <i class="bx bxl-instagram"></i>
                            </a>
                        @endif
                        @if (!empty($configArr['facebook_link']))
                            <a href="{{ $configArr['facebook_link'] }}" target="_blank"
                                rel="noopener noreferrer" aria-label="Facebook">
                                <i class="bx bxl-facebook"></i>
                            </a>
                        @endif
                        @if (!empty($configArr['youtube_link']))
                            <a href="{{ $configArr['youtube_link'] }}" target="_blank" rel="noopener noreferrer"
                                aria-label="YouTube">
                                <i class="bx bxl-youtube"></i>
                            </a>
                        @endif
                        @if (!empty($configArr['twitter_link']))
                            <a href="{{ $configArr['twitter_link'] }}" target="_blank" rel="noopener noreferrer"
                                aria-label="Twitter">
                                <i class="bx bxl-twitter"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Copyright Ribbon -->
        <div class="sanatan-footer-bottom-bar fc-footer-bottom-bar">
            <div class="container">
                <div
                    class="d-flex flex-wrap align-items-center justify-content-between gap-3 text-center text-md-start">
                    <p class="mb-0 sanatan-copy-text fc-copy-text">
                        {{ $configArr['footer_text'] }}
                    </p>
                    <div class="sanatan-motto-text fc-motto-text">
                        @foreach ($cmsPages->take(3) as $page)
                            <a href="{{ route('web.cmsPages.index', $page->page_url) }}">
                                <span class="text-white">{{ $page->page_title }}</span>
                            </a>
                            @if (!$loop->last)
                                <span class="dot-gold"></span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </footer>

    <!-- progress bar bottom to top -->
    <div class="progress-wrap shadow">
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

    {{-- Base Url --}}
    <input type="hidden" id="base_url" name="base_url" value="{{ url('/') }}">
    {{-- Base Url --}}

    <!-- all js file include -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('storage/web/home2/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('storage/web/home2/assets/js/wow.js') }}"></script>
    <script src="{{ asset('storage/web/home2/assets/js/slick.min.js') }}"></script>
    <script src="{{ asset('storage/web/home2/assets/js/main.js') }}"></script>

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

    <script>
        $(document).ready(function() {
            const $form = $('.newsletterForm');
            if (!$form.length) return;

            const $messageBox = $form.find('.newsletter-message');
            const $submitBtn = $form.find('.fc-newsletter-btn');
            const $emailInput = $form.find('input[name="email"]');

            let hideTimer = null; // track the auto-hide timeout

            function showMessage(text, type) {
                // Clear any previous timer so it doesn't hide early
                clearTimeout(hideTimer);

                $messageBox
                    .text(text)
                    .removeClass('success error')
                    .addClass(type)
                    .stop(true, true)
                    .fadeIn(200);

                // Auto-hide after 4 seconds
                hideTimer = setTimeout(function() {
                    $messageBox.fadeOut(400, function() {
                        $(this).removeClass('success error').text('');
                    });
                }, 4000);
            }

            $form.on('submit', function(e) {
                e.preventDefault();

                const formData = $form.serialize();

                // Reset UI state
                clearTimeout(hideTimer);
                $messageBox.stop(true, true).hide().removeClass('success error');
                $emailInput.removeClass('is-invalid');
                $submitBtn.addClass('loading').prop('disabled', true);

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: formData,
                    headers: {
                        'Accept': 'application/json',
                    },
                    success: function(data) {
                        showMessage(data.message || 'Thank you for subscribing!', 'success');
                        $form[0].reset();
                    },
                    error: function(xhr) {
                        let msg = 'Something went wrong. Please try again.';

                        if (xhr.responseJSON) {
                            const data = xhr.responseJSON;

                            if (data.errors && data.errors.email) {
                                msg = data.errors.email[0];
                                $emailInput.addClass('is-invalid');
                            } else if (data.message) {
                                msg = data.message;
                            }
                        }

                        showMessage(msg, 'error');
                    },
                    complete: function() {
                        $submitBtn.removeClass('loading').prop('disabled', false);
                    }
                });
            });
        });
    </script>

    @stack('scripts')

</html>

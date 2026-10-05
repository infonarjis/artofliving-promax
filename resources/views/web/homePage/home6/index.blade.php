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
        href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Dancing+Script:wght@600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600;1,700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap"
        rel="stylesheet">

    <!-- all css file include -->
    <link rel="stylesheet" href="{{ asset('storage/web/home6/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home6/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home6/assets/css/slick.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="{{ asset('storage/web/home6/assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home6/assets/css/responsive.css') }}">

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
        $getActiveLanguage = _getActiveLanguage();
        $currentLanguage = App::getLocale();
        $currentLang = collect($getActiveLanguage)->firstWhere('lang_code', $currentLanguage);
        $currentLangCode = $currentLang->lang_code ?? 'en';
    @endphp

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
                        <li><a href="{{ url('/') }}" class="nav-item active">
                            <iconify-icon icon="fluent:grid-16-filled" class="nav-icon"></iconify-icon><span
                                    class="nav-label">{{ __('messages.lbl_home') }}</span><span
                                    class="active-indicator"></span></a></li>
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
                                            <i class="bx bx-bolt-circle"></i>
                                        </span>
                                        <div class="dropdown-item-text">
                                            <span
                                                class="dropdown-item-title">{{ __('messages.lbl_quick_search') }}</span>
                                            <span class="dropdown-item-desc">{{ __('messages.lbl_quick_search_description') }}</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('web.search.type', ['type' => 'advance-search']) }}"
                                        class="fc-dropdown-item">
                                        <span class="dropdown-item-icon">
                                            <i class="bx bx-slider-alt"></i>
                                        </span>
                                        <div class="dropdown-item-text">
                                            <span
                                                class="dropdown-item-title">{{ __('messages.lbl_advance_search') }}</span>
                                            <span class="dropdown-item-desc">{{ __('messages.lbl_advance_search_description') }}</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('web.search.type', ['type' => 'keyword-search']) }}"
                                        class="fc-dropdown-item">
                                        <span class="dropdown-item-icon">
                                            <i class="bx bx-id-card"></i>
                                        </span>
                                        <div class="dropdown-item-text">
                                            <span class="dropdown-item-title">{{ __('messages.lbl_keyword_search') }}</span>
                                            <span class="dropdown-item-desc">{{ __('messages.lbl_find_profiles_by_interests_words_hobbies') }}</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('web.search.type', ['type' => 'id-search']) }}"
                                        class="fc-dropdown-item">
                                        <span class="dropdown-item-icon">
                                            <i class="bx bx-id-card"></i>
                                        </span>
                                        <div class="dropdown-item-text">
                                            <span class="dropdown-item-title">{{ __('messages.lbl_id_search') }}</span>
                                            <span class="dropdown-item-desc">{{ __('messages.lbl_id_search') }}</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li><a href="{{ route('web.membershipPlan.index') }}" class="nav-item"><iconify-icon icon="mdi:crown-outline"
                                        class="nav-icon"></iconify-icon><span class="nav-label">
                                    {{ __('messages.lbl_membership') }}</span></a>
                        </li>
                        <li><a href="{{ route('web.successStory.index') }}" class="nav-item">
                                <iconify-icon icon="ph:heart-straight-bold"></iconify-icon><span
                                    class="nav-label">{{ __('messages.lbl_success_stories') }}</span></a></li>
                        <li><a href="{{ route('web.contactUs.index') }}" class="nav-item">
                                <iconify-icon icon="ph:chats-circle-bold"></iconify-icon><span
                                    class="nav-label">{{ __('messages.lbl_contact_us') }}</span></a></li>
                    </ul>

                    <div class="nav-cta-wrap d-flex align-items-center gap-2">

                        <a href="{{ route('web.register.index') }}" class="nav-login-link">
                            <iconify-icon icon="ph:user-plus-bold"></iconify-icon>
                            {{ __('messages.lbl_register') }}
                        </a>

                        <a href="{{ route('web.login.index') }}" class="btn-join-nav">
                            <iconify-icon icon="ph:sign-in-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_login') }}</span>
                        </a>

                        {{-- Language Dropdown --}}
                        <div class="site-language-wrapper" id="siteLanguageWrapper">

                            <button type="button" class="site-language-toggle" id="siteLanguageToggle"
                                aria-label="{{ __('messages.lbl_change_language') }}" aria-expanded="false"
                                aria-haspopup="true">

                                <iconify-icon icon="ph:translate-bold"></iconify-icon>
                                <span>{{ strtoupper($currentLangCode) }}</span>
                                <iconify-icon icon="ph:caret-down-bold" class="site-language-arrow">
                                </iconify-icon>
                            </button>

                            <div class="site-language-dropdown" id="siteLanguageDropdown">

                                <div class="site-language-heading">
                                    <div>
                                        <strong>{{ __('messages.lbl_select_language') }}</strong>
                                        <small>{{ __('messages.lbl_choose_display_language') }}</small>
                                    </div>

                                    <span class="site-language-count">
                                        {{ count($getActiveLanguage) }}
                                        {{ __('messages.lbl_languages') }}
                                    </span>
                                </div>

                                <div class="site-language-list">
                                    @foreach ($getActiveLanguage as $value)
                                        <a href="{{ route('language.change', $value->lang_code) }}"
                                            class="site-language-option {{ $value->lang_code == $currentLanguage ? 'active' : '' }}"
                                            role="menuitem">

                                            <span class="site-language-icon">
                                                <iconify-icon icon="akar-icons:language"></iconify-icon>
                                            </span>

                                            <span class="site-language-text">
                                                <span>{{ $value->lang_name }}</span>
                                                <small>{{ strtoupper($value->lang_code) }}</small>
                                            </span>

                                            @if ($value->lang_code == $currentLanguage)
                                                <iconify-icon icon="ph:check-circle-fill" class="site-language-check">
                                                </iconify-icon>
                                            @endif
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
    <header class="hero-section">
        <!-- decorative background atmosphere & botanical branch -->
        <div class="hero-lavender-glow" aria-hidden="true"></div>
        <img src="{{ asset('storage/web/home6') }}/assets/images/deco-leaf.png" alt=""
            class="hero-deco-leaf" aria-hidden="true">

        <div class="container position-relative">
            <div class="row align-items-center">
                <!-- Hero Left Column -->
                <div class="col-lg-6 order-2 order-lg-1">
                    <div class="hero-copy wow fadeInUp" data-wow-delay="0.1s">
                        <div class="hero-eyebrow-badge">
                            <span class="church-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="m18 7 4 2v11a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9l4-2" />
                                    <path d="M14 22v-4a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v4" />
                                    <path d="M18 22V5l-6-3-6 3v17" />
                                    <path d="M12 7v5" />
                                    <path d="M10 9h4" />
                                </svg>
                            </span>
                            <span>{{ $data['hero_badge_text'] ?? '' }}</span>
                        </div>

                        <h1 class="hero-title">
                            <span class="d-block title-faith">{{ $data['hero_title_line1'] ?? '' }}</span>
                            <span class="d-inline-flex align-items-center title-love-wrap">
                                <span class="hero-script-love">{{ $data['hero_title_script'] ?? '' }}</span>
                                <span class="title-heart-doodle">
                                    <svg width="34" height="34" viewBox="0 0 34 34" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M 6 30 C 10 29 13 26 15 22 C 10 16 6 10 8 6 C 10 2 15 2 17 6 C 18 8 18 11 16 13 C 14 15 16 18 19 16 C 23 13 27 10 28 15 C 29 20 23 25 15 23 C 11 28 7 30 5 30"
                                            stroke="#7C3AED" stroke-width="2.2" stroke-linecap="round"
                                            stroke-linejoin="round" fill="none" />
                                    </svg>
                                </span>
                            </span>
                        </h1>

                        <p class="hero-lede">
                            {!! $data['hero_subtitle'] ?? '' !!}
                        </p>

                        <div class="hero-actions d-flex flex-wrap align-items-center gap-3">
                            <a href="{{ route('web.register.index') }}" class="btn-fc-primary">
                                <span>{{ __('messages.lbl_register') }}</span>
                                <i class="bx bx-right-arrow-alt"></i>
                            </a>
                            <a href="{{ route('web.successStory.index') }}" class="btn-fc-secondary">
                                <span class="play-icon-circle">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10" />
                                        <polygon points="10 8 16 12 10 16 10 8" fill="currentColor" />
                                    </svg>
                                </span>
                                <span>Hear Their Stories</span>
                            </a>
                        </div>

                        <div class="hero-trust d-flex align-items-center gap-3">
                            <div class="avatar-stack">
                                <img src="{{ asset('storage/web/home6') }}/assets/images/avatar-1.png"
                                    alt="Christian member">
                                <img src="{{ asset('storage/web/home6') }}/assets/images/avatar-2.png"
                                    alt="Christian member">
                                <img src="{{ asset('storage/web/home6') }}/assets/images/avatar-3.png"
                                    alt="Christian member">
                                <img src="{{ asset('storage/web/home6') }}/assets/images/avatar-4.png"
                                    alt="Christian member">
                            </div>
                            <div class="trust-info">
                                <div class="trust-title">{{ $data['hero_trust_title'] ?? '' }}</div>
                                <div class="trust-desc">{{ $data['hero_trust_desc'] ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hero Right Column -->
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="hero-arch-wrap wow fadeIn" data-wow-delay="0.15s">

                        <!-- Loopy continuous heart doodle in background left of the arch -->
                        <div class="bg-loopy-heart-wrap" aria-hidden="true">
                            <svg class="bg-loopy-heart" width="130" height="130" viewBox="0 0 120 120"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M 16 102 C 32 96 52 88 68 74 C 86 58 92 42 88 28 C 84 14 68 12 56 26 C 50 33 48 44 52 53 C 56 61 63 61 65 52 C 67 40 62 28 48 26 C 34 24 24 38 28 52 C 32 68 50 82 72 86 C 90 90 102 92 112 92"
                                    stroke="#8B5CF6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    fill="none" />
                            </svg>
                        </div>

                        <!-- Purple Arch Outline Ring -->
                        <div class="hero-arch-outer-ring" aria-hidden="true"></div>

                        <!-- Dove / Pigeon soaring at top right of the arch -->
                        <img src="{{ asset('storage/web/home6') }}/assets/images/pigion.png" alt="Dove of peace"
                            class="hero-floating-pigeon">

                        <!-- Couple Arch Photo -->
                        <div class="hero-arch-container">
                            @php
                                $heroCoupleImg = !empty($data['hero_couple_image'])
                                    ? _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['hero_couple_image']
                                    : asset('storage/web/home6') . '/assets/images/header-couple-img-01.png';
                            @endphp
                            <img src="{{ $heroCoupleImg }}" alt="{{ $configArr['web_name'] }} Christian couple"
                                class="hero-arch-img">
                        </div>

                        <!-- Floating Card 1: God's Plan (Bottom Left) -->
                        <div class="hero-float-card card-gods-plan">
                            <div class="float-card-icon icon-purple-heart">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                                    stroke="#7C3AED" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                                </svg>
                            </div>
                            <div class="float-card-content">
                                <h4 class="float-card-title">{{ $data['hero_float1_title'] ?? '' }}</h4>
                                <p class="float-card-sub">{{ $data['hero_float1_sub'] ?? '' }}</p>
                            </div>
                        </div>

                        <!-- Floating Card 2: Safe & Trusted (Mid Right) -->
                        <div class="hero-float-card card-safe-trusted">
                            <div class="float-card-icon icon-purple-shield">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                                    stroke="#7C3AED" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    <path d="m9 12 2 2 4-4" />
                                </svg>
                            </div>
                            <div class="float-card-content">
                                <h4 class="float-card-title">{{ $data['hero_float2_title'] ?? '' }}</h4>
                                <p class="float-card-sub">{{ $data['hero_float2_sub'] ?? '' }}</p>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- header search form / filter band -->
    <div class="search-band" id="search">
        <div class="container">
            <div class="search-card-fc">
                <form action="{{ route('web.search.searchResult') }}" method="GET" class="search-form-grid">

                    <!-- Field 1: Looking For -->
                    <div class="search-col">
                        <label class="fc-field-label">
                            <i class="bx bx-user fc-icon"></i>
                            <span>{{ __('messages.lbl_i_m_looking_for_a') }}</span>
                        </label>
                        <div class="fc-select-wrap">
                            <select name="looking_for" id="Looking" class="fc-custom-select field-select">
                                <option value="Female" title="Woman">{{ __('messages.field_lbl_female') }}</option>
                                <option value="Male" title="Man">{{ __('messages.field_lbl_male') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Field 2: Age From -->
                    <div class="search-col">
                        <label class="fc-field-label">
                            <i class="bx bx-calendar fc-icon"></i>
                            <span>{{ __('messages.field_lbl_age_from') }}</span>
                        </label>
                        @php $age = _ageRang(); @endphp
                        <div class="fc-select-wrap">
                            <select name="from_age" id="agefrom" class="fc-custom-select field-select">
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
                            <select name="age_to" id="ageto" class="fc-custom-select field-select">
                                @foreach ($age as $key => $valueArr)
                                    @php $selected = ($key == '30') ? 'selected' : ''; @endphp
                                    <option value="{{ $key }}" {{ $selected }}
                                        title="{{ $valueArr }}">{{ $valueArr }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Field 4: Religion -->
                    <div class="search-col">
                        <label class="fc-field-label">
                            <span class="fc-icon fc-cross-svg">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 2v20M7 8h10" />
                                </svg>
                            </span>
                            <span>{{ __('messages.field_lbl_religion') }}</span>
                        </label>
                        <div class="fc-select-wrap">
                            <select name="religion" id="Denomination" class="fc-custom-select field-select">
                                <option class="list" value="" selected
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
                            <select name="country_id" id="Location" class="fc-custom-select field-select">
                                <option class="list" value="" selected
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
                        <button type="submit" class="fc-search-submit">
                            <i class="bx bx-search"></i>
                            <span>{{ __('messages.lbl_search') }}</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- How Does It Work section start -->
    <section class="steps-section" id="how-it-works">
        <!-- subtle atmospheric glow -->
        <div class="steps-glow" aria-hidden="true"></div>

        <div class="container position-relative">

            <!-- Section Header -->
            <div class="fc-steps-header text-center wow fadeInUp" data-wow-delay="0.1s">
                <div class="fc-pill-badge">
                    <span class="fc-badge-cross">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20M7 8h10" />
                        </svg>
                    </span>
                    <span>{{ $data['journey_badge'] ?? '' }}</span>
                </div>
                <h2 class="fc-steps-title">
                    {!! $data['journey_title'] ?? '' !!}
                </h2>
                <p class="fc-steps-subtitle">
                    {!! $data['journey_subtitle'] ?? '' !!}
                </p>
            </div>

            <!-- Steps Track & Cards Grid -->
            <div class="fc-steps-grid-wrap position-relative">

                <!-- Connecting line running through cards on desktop -->
                <div class="fc-steps-connector d-none d-lg-block" aria-hidden="true">
                    <svg width="100%" height="40" viewBox="0 0 900 40" fill="none"
                        xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                        <path d="M 50 20 Q 250 -5, 450 20 T 850 20" stroke="#DDD6FE" stroke-width="2.5"
                            stroke-dasharray="8 8" stroke-linecap="round" fill="none" />
                    </svg>
                </div>

                <div class="row g-4 justify-content-center">

                    <!-- Step 1 -->
                    <div class="col-lg-4 col-md-6">
                        <div class="fc-step-card wow fadeInUp" data-wow-delay="0.15s">
                            <div class="fc-step-top">
                                <span class="fc-step-badge">STEP 01</span>
                                <span class="fc-step-accent-icon">
                                    <i class="bx bx-heart"></i>
                                </span>
                            </div>

                            <div class="fc-step-icon-wrap icon-gradient-1">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                                    stroke="#702EF3" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <line x1="19" y1="8" x2="19" y2="14" />
                                    <line x1="22" y1="11" x2="16" y2="11" />
                                </svg>
                            </div>

                            <h3 class="fc-step-name">{{ $data['journey_step1_title'] ?? '' }}</h3>
                            <p class="fc-step-desc">
                                {{ $data['journey_step1_desc'] ?? '' }}
                            </p>

                            <div class="fc-step-footer">
                                <span class="fc-step-tag">
                                    <i class="bx bx-check-circle"></i>
                                    <span>{{ $data['journey_step1_tag'] ?? '' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 (Featured / Elevated) -->
                    <div class="col-lg-4 col-md-6">
                        <div class="fc-step-card fc-step-featured wow fadeInUp" data-wow-delay="0.25s">
                            <div class="fc-step-top">
                                <span class="fc-step-badge badge-featured">STEP 02</span>
                                <span class="fc-step-highlight-chip">{{ $data['journey_step2_badge'] ?? '' }}</span>
                            </div>

                            <div class="fc-step-icon-wrap icon-gradient-2">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                                    stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8" />
                                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                    <path d="M11 8v6M8 11h6" />
                                </svg>
                            </div>

                            <h3 class="fc-step-name">{{ $data['journey_step2_title'] ?? '' }}</h3>
                            <p class="fc-step-desc">
                                {{ $data['journey_step2_desc'] ?? '' }}
                            </p>

                            <div class="fc-step-footer">
                                <span class="fc-step-tag tag-purple">
                                    <i class="bx bx-shield-quarter"></i>
                                    <span>{{ $data['journey_step2_tag'] ?? '' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="col-lg-4 col-md-6">
                        <div class="fc-step-card wow fadeInUp" data-wow-delay="0.35s">
                            <div class="fc-step-top">
                                <span class="fc-step-badge">STEP 03</span>
                                <span class="fc-step-accent-icon">
                                    <i class="bx bx-cross"></i>
                                </span>
                            </div>

                            <div class="fc-step-icon-wrap icon-gradient-3">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                                    stroke="#702EF3" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                    <path d="M12 7v6M9 10h6" />
                                </svg>
                            </div>

                            <h3 class="fc-step-name">{{ $data['journey_step3_title'] ?? '' }}</h3>
                            <p class="fc-step-desc">
                                {{ $data['journey_step3_desc'] ?? '' }}
                            </p>

                            <div class="fc-step-footer">
                                <span class="fc-step-tag">
                                    <i class="bx bx-lock-alt"></i>
                                    <span>{{ $data['journey_step3_tag'] ?? '' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Bottom Step CTA Banner -->
            <div class="fc-steps-cta-bar text-center wow fadeInUp" data-wow-delay="0.4s">
                <div class="fc-cta-inner d-flex flex-wrap align-items-center justify-content-between">
                    <div class="fc-cta-text d-flex align-items-center gap-3">
                        <span class="fc-cta-icon-orb">
                            <i class="bx bx-church"></i>
                        </span>
                        <div class="text-start">
                            <h4 class="fc-cta-title">{{ $data['journey_cta_title'] ?? '' }}</h4>
                            <p class="fc-cta-desc">{{ $data['journey_cta_desc'] ?? '' }}</p>
                        </div>
                    </div>
                    <div class="fc-cta-action">
                        <a href="{{ route('web.register.index') }}" class="btn-fc-cta-pill">
                            <span>{{ $data['journey_cta_text'] ?? '' }}</span>
                            <i class="bx bx-right-arrow-alt"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Last Added Profiles section start -->
    @if ($latestProfile->isNotEmpty())
        <section class="profiles-section" id="profiles">
            <div class="profiles-glow" aria-hidden="true"></div>
            <div class="container position-relative">

                <!-- Section Header -->
                <div class="section-head fc-profiles-head d-flex flex-wrap align-items-end justify-content-between gap-3 wow fadeInUp"
                    data-wow-delay="0.1s">
                    <div>
                        <div class="fc-pill-badge mb-2">
                            <span class="fc-badge-cross">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 2v20M7 8h10" />
                                </svg>
                            </span>
                            <span>{{ $data['profiles_badge'] ?? '' }}</span>
                        </div>
                        <h2 class="fc-profiles-title">
                            {!! $data['profiles_title'] ?? '' !!}
                        </h2>
                        <p class="fc-profiles-subtitle">
                            {!! $data['profiles_subtitle'] ?? '' !!}
                        </p>
                    </div>
                    <div class="lastProfileArrows slider-arrows d-flex gap-2"></div>
                </div>

                <!-- Profiles Slider -->
                <div class="LastProfileSlider profiles-slider wow fadeInUp" data-wow-delay="0.2s">
                    @foreach ($latestProfile as $profile)
                        @php
                            $canView = _canViewMemberPhoto($profile, $profile->hasPhotoRequestAccess ?? '');
                            $hasPhoto = _checkPhotoExist($profile);
                            $profileImage = _getMemberProfileImage($profile);
                        @endphp
                        <div class="px-2">
                            <div class="fc-profile-card">
                                <div class="fc-profile-media">
                                    <a href="{{ route('web.userProfile.index', _encrypt($profile->id)) }}"
                                        class="fc-profile-img-link">
                                        @if (!$canView && $hasPhoto)
                                            <img src="{{ _getProtectedImage($profile->gender) }}"
                                                alt="{{ _profileTitle($profile) }}" class="fc-profile-img">
                                        @else
                                            <img src="{{ $profileImage }}" alt="{{ _profileTitle($profile) }}"
                                                class="fc-profile-img">
                                        @endif
                                    </a>
                                    <span class="fc-badge-verified-glass">
                                        <i class="bx bxs-badge-check"></i>
                                        <span>Verified</span>
                                    </span>
                                    <span
                                        class="fc-denom-pill">{{ optional($profile->religionData)->translated_name }}</span>
                                </div>
                                <div class="fc-profile-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h4 class="fc-member-name"><a
                                                    href="{{ route('web.userProfile.index', _encrypt($profile->id)) }}">{{ _profileTitle($profile) }}</a>
                                            </h4>
                                            <p class="fc-member-info">{{ _birthdateDisplay($profile->birthdate, 0) }}
                                                · {{ _displayHeight($profile->height) }}</p>
                                        </div>
                                        <button type="button" class="fc-profile-connect-btn" title="Send Interest"
                                            aria-label="Send interest">
                                            <i class="bx bx-heart"></i>
                                        </button>
                                    </div>
                                    <div class="fc-member-location">
                                        <i class="bx bx-map"></i>
                                        <span>{{ _getMemberLocation($profile) }}</span>
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
        <section class="stories-section" id="stories">
            <div class="stories-atmosphere-glow" aria-hidden="true"></div>

            <div class="container position-relative">

                <!-- Section Header -->
                <div class="fc-stories-header text-center wow fadeInUp" data-wow-delay="0.1s">
                    <div class="fc-pill-badge mb-3">
                        <span class="fc-badge-cross">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M12 2v20M7 8h10" />
                            </svg>
                        </span>
                        <span>{{ $data['stories_badge'] ?? '' }}</span>
                    </div>
                    <h2 class="fc-stories-title">
                        {!! $data['stories_title'] ?? '' !!}
                    </h2>
                    <p class="fc-stories-subtitle">
                        {!! $data['stories_subtitle'] ?? '' !!}
                    </p>
                </div>

                @php
                    $spotlightStory = $successStoryArr->first();
                    $otherStories = $successStoryArr->skip(1)->take(2);

                    $storyImage = function ($story) {
                        $img = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                        if (
                            !blank($story->wedding_photo) &&
                            _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $story->wedding_photo)
                        ) {
                            $img = _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $story->wedding_photo;
                        }
                        return $img;
                    };
                @endphp

                <!-- TIER 1: FEATURED SPOTLIGHT STORY -->
                @if ($spotlightStory)
                    @php
                        $spotlightName = $spotlightStory->groomname . ' & ' . $spotlightStory->bridename;
                        $spotlightQuote = Str::limit(strip_tags($spotlightStory->successmessage), 260);
                    @endphp
                    <div class="fc-spotlight-story-wrap wow fadeInUp" data-wow-delay="0.2s">
                        <div class="fc-spotlight-card">
                            <div class="row g-0 align-items-center">

                                <!-- Left: Cinematic Image -->
                                <div class="col-lg-6">
                                    <div class="fc-spotlight-media">
                                        <img src="{{ $storyImage($spotlightStory) }}" alt="{{ $spotlightName }}"
                                            class="fc-spotlight-img">
                                        <span class="fc-spotlight-tag">
                                            <i class="bx bxs-church"></i>
                                            <span>Holy Matrimony ·
                                                {{ _displayDate($spotlightStory->marriagedate, 'M Y') }}</span>
                                        </span>
                                        <span class="fc-spotlight-badge-corner">
                                            <i class="bx bxs-heart"></i>
                                            <span>God's Plan</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Right: Story -->
                                <div class="col-lg-6">
                                    <div class="fc-spotlight-content">
                                        <div class="fc-quote-mark" aria-hidden="true">
                                            <svg width="46" height="46" viewBox="0 0 24 24" fill="#7C3AED"
                                                opacity="0.12">
                                                <path
                                                    d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z" />
                                            </svg>
                                        </div>

                                        <div class="fc-story-meta-row d-flex align-items-center gap-2 mb-2">
                                            <span
                                                class="fc-story-denom">{{ optional($spotlightStory->religionData)->translated_name }}</span>
                                        </div>

                                        <h3 class="fc-spotlight-names">{{ $spotlightName }}</h3>

                                        <blockquote class="fc-spotlight-quote">
                                            "{{ $spotlightQuote }}"
                                        </blockquote>

                                        <div class="fc-spotlight-actions d-flex align-items-center gap-3 mt-4">
                                            <a href="#how-it-works" class="btn-fc-story-primary">
                                                <span>Find Your God-Given Match</span>
                                                <i class="bx bx-right-arrow-alt"></i>
                                            </a>
                                            <span class="fc-verified-couple-tag">
                                                <i class="bx bxs-check-shield"></i> Verified Marriage
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endif

                <!-- TIER 2: COMPLEMENTARY TESTIMONY CARDS -->
                @if ($otherStories->isNotEmpty())
                    <div class="row g-4 mt-2">
                        @foreach ($otherStories as $story)
                            @php
                                $storyName = $story->groomname . ' & ' . $story->bridename;
                                $storyQuote = Str::limit(strip_tags($story->successmessage), 180);
                            @endphp
                            <div class="col-lg-6">
                                <div class="fc-testimony-card wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="fc-testimony-top">
                                        <div class="fc-testimony-photo-wrap">
                                            <img src="{{ $storyImage($story) }}" alt="{{ $storyName }}"
                                                class="fc-testimony-img">
                                            <span
                                                class="fc-testimony-pill">{{ optional($story->religionData)->translated_name }}</span>
                                        </div>
                                        <div class="fc-testimony-summary">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <h4 class="fc-testimony-names">{{ $storyName }}</h4>
                                                <div class="fc-hearts-rating">
                                                    <i class="bx bxs-heart"></i>
                                                    <i class="bx bxs-heart"></i>
                                                    <i class="bx bxs-heart"></i>
                                                    <i class="bx bxs-heart"></i>
                                                    <i class="bx bxs-heart"></i>
                                                </div>
                                            </div>
                                            <p class="fc-testimony-details">
                                                <i class="bx bx-map text-primary"></i> Married
                                                {{ _displayDate($story->marriagedate, 'F Y') }}
                                            </p>
                                            <blockquote class="fc-testimony-quote">
                                                "{{ $storyQuote }}"
                                            </blockquote>
                                            <div class="fc-testimony-footer">
                                                <span class="fc-testimony-badge">100% Faith-Aligned</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- TIER 3: STATS & TESTIMONY INVITATION BAR -->
                <div class="fc-stories-bottom-bar wow fadeInUp" data-wow-delay="0.45s">
                    <div class="row align-items-center g-3">
                        <div class="col-md-7 text-center text-md-start">
                            <div
                                class="d-flex align-items-center justify-content-center justify-content-md-start gap-3">
                                <div class="fc-dove-badge">
                                    <i class="bx bxs-bell-ring"></i>
                                </div>
                                <div>
                                    <h5 class="fc-bar-title mb-1">{{ $data['stories_bar_title'] ?? '' }}</h5>
                                    <p class="fc-bar-desc mb-0">{{ $data['stories_bar_desc'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 text-center text-md-end">
                            <a href="#how-it-works" class="btn-fc-cta-pill">
                                <span>{{ $data['stories_bar_cta_text'] ?? '' }}</span>
                                <i class="bx bx-right-arrow-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    @endif

    <!-- Why Choose Us section start  -->
    <section class="why-us-section" id="why-us">
        <div class="container position-relative">
            <div class="row align-items-center g-4 g-lg-5">

                <!-- Left: Christian Couple Visual Showcase -->
                <div class="col-lg-5">
                    <div class="fc-why-visual-wrap wow fadeInLeft" data-wow-delay="0.15s">

                        <!-- Soft Purple Background Glow Orb -->
                        <div class="fc-why-glow-orb" aria-hidden="true"></div>

                        <!-- Main Arch Container -->
                        <div class="fc-why-arch-card">
                            <img src="{{ _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['why_us_image'] }}"
                                alt="Christian Married Couple under Floral Arch" class="fc-why-couple-img">
                            <div class="fc-why-arch-overlay"></div>

                            <!-- Bottom Pill Badge on Photo -->
                            <div class="fc-why-photo-pill">
                                <i class="bx bxs-badge-check"></i>
                                <span>{{ $data['why_us_photo_pill_text'] ?? '' }}</span>
                            </div>
                        </div>

                        <!-- Floating Stat 1: Top Right (Churches) -->
                        <div class="fc-floating-stat stat-top-right wow fadeIn" data-wow-delay="0.3s">
                            <div class="stat-icon-circle">
                                <i class="bx bxs-church"></i>
                            </div>
                            <div class="stat-text">
                                <strong>{{ $data['why_us_stat1_title'] ?? '' }}</strong>
                                <span>{{ $data['why_us_stat1_sub'] ?? '' }}</span>
                            </div>
                        </div>

                        <!-- Floating Stat 2: Bottom Left (100% Verified) -->
                        <div class="fc-floating-stat stat-bottom-left wow fadeIn" data-wow-delay="0.4s">
                            <div class="stat-icon-circle icon-shield">
                                <i class="bx bxs-check-shield"></i>
                            </div>
                            <div class="stat-text">
                                <strong>{{ $data['why_us_stat2_title'] ?? '' }}</strong>
                                <span>{{ $data['why_us_stat2_sub'] ?? '' }}</span>
                            </div>
                        </div>

                        <!-- Floating Badge 3: Pure Love Doodle / Hearts -->
                        <div class="fc-floating-pill-center">
                            <i class="bx bxs-heart"></i>
                            <span>{{ $data['why_us_center_pill_text'] ?? '' }}</span>
                        </div>

                    </div>
                </div>

                <!-- Right: Why Families Trust Us Content & Cards -->
                <div class="col-lg-7">
                    <div class="fc-why-content wow fadeInRight" data-wow-delay="0.2s">

                        <div class="fc-pill-badge mb-3">
                            <span class="fc-badge-cross">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 2v20M7 8h10" />
                                </svg>
                            </span>
                            <span>{{ $data['why_us_badge'] ?? '' }}</span>
                        </div>

                        <h2 class="fc-why-title">
                            {!! $data['why_us_title'] ?? '' !!}
                        </h2>

                        <p class="fc-why-lead">
                            {!! $data['why_us_subtitle'] ?? '' !!}
                        </p>

                        <!-- 4 Value Pillar Cards Grid -->
                        <div class="row g-3 g-md-4 mt-2">

                            <!-- Pillar 1 -->
                            <div class="col-md-6">
                                <div class="fc-feature-card">
                                    <div class="fc-feature-icon-orb orb-purple-1">
                                        <i class="bx bx-church"></i>
                                    </div>
                                    <h4 class="fc-feature-name">{{ $data['why_us_feature1_title'] ?? '' }}</h4>
                                    <p class="fc-feature-text">
                                        {{ $data['why_us_feature1_desc'] ?? '' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Pillar 2 -->
                            <div class="col-md-6">
                                <div class="fc-feature-card">
                                    <div class="fc-feature-icon-orb orb-purple-2">
                                        <i class="bx bx-shield-quarter"></i>
                                    </div>
                                    <h4 class="fc-feature-name">{{ $data['why_us_feature2_title'] ?? '' }}</h4>
                                    <p class="fc-feature-text">
                                        {{ $data['why_us_feature2_desc'] ?? '' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Pillar 3 -->
                            <div class="col-md-6">
                                <div class="fc-feature-card">
                                    <div class="fc-feature-icon-orb orb-purple-3">
                                        <i class="bx bx-lock-alt"></i>
                                    </div>
                                    <h4 class="fc-feature-name">{{ $data['why_us_feature3_title'] ?? '' }}</h4>
                                    <p class="fc-feature-text">
                                        {{ $data['why_us_feature3_desc'] ?? '' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Pillar 4 -->
                            <div class="col-md-6">
                                <div class="fc-feature-card">
                                    <div class="fc-feature-icon-orb orb-purple-4">
                                        <i class="bx bx-support"></i>
                                    </div>
                                    <h4 class="fc-feature-name">{{ $data['why_us_feature4_title'] ?? '' }}</h4>
                                    <p class="fc-feature-text">
                                        {{ $data['why_us_feature4_desc'] ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="fc-why-denominations-bar d-flex flex-wrap align-items-center gap-2 mt-4 pt-3">
                            <span class="fc-denom-label"><i class="bx bx-check-double"></i>
                                {{ $data['why_us_denom_label'] ?? '' }}</span>
                            <span class="fc-small-tag">Catholic</span>
                            <span class="fc-small-tag">Protestant</span>
                            <span class="fc-small-tag">Baptist</span>
                            <span class="fc-small-tag">Pentecostal</span>
                            <span class="fc-small-tag">Orthodox</span>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Global Christian Reach section start -->
    <section class="fc-reach-section" id="global-reach">
        <div class="fc-reach-glow" aria-hidden="true"></div>
        <div class="container position-relative">

            <!-- Inner Premium Card Wrap -->
            <div class="fc-reach-banner-card text-center wow fadeInUp" data-wow-delay="0.15s">

                <!-- Header -->
                <div class="fc-reach-header text-center mb-4">
                    <div class="fc-pill-badge mb-3">
                        <span class="fc-badge-cross">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M12 2v20M7 8h10" />
                            </svg>
                        </span>
                        <span>{{ $data['reach_badge'] ?? '' }}</span>
                    </div>

                    <h2 class="fc-reach-title">
                        {!! $data['reach_title'] ?? '' !!}
                    </h2>

                    <p class="fc-reach-desc mx-auto">
                        {!! $data['reach_subtitle'] ?? '' !!}
                    </p>
                </div>

                <!-- 4 Stat Cards Across Full Width -->
                <div class="row g-3 g-lg-4 justify-content-center">

                    <!-- Stat 1: Traditions -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="fc-stat-card">
                            <div class="fc-stat-icon-wrap orb-reach-1">
                                <i class="bx bx-church"></i>
                            </div>
                            <div class="fc-stat-body">
                                <h3 class="fc-stat-number">{{ $data['reach_stat1_number'] ?? '' }}</h3>
                                <h4 class="fc-stat-label">{{ $data['reach_stat1_label'] ?? '' }}</h4>
                                <p class="fc-stat-sub">{{ $data['reach_stat1_sub'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 2: Countries -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="fc-stat-card">
                            <div class="fc-stat-icon-wrap orb-reach-2">
                                <i class="bx bx-world"></i>
                            </div>
                            <div class="fc-stat-body">
                                <h3 class="fc-stat-number">{{ $data['reach_stat2_number'] ?? '' }}</h3>
                                <h4 class="fc-stat-label">{{ $data['reach_stat2_label'] ?? '' }}</h4>
                                <p class="fc-stat-sub">{{ $data['reach_stat2_sub'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 3: Cities & Parishes -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="fc-stat-card">
                            <div class="fc-stat-icon-wrap orb-reach-3">
                                <i class="bx bx-buildings"></i>
                            </div>
                            <div class="fc-stat-body">
                                <h3 class="fc-stat-number">{{ $data['reach_stat3_number'] ?? '' }}</h3>
                                <h4 class="fc-stat-label">{{ $data['reach_stat3_label'] ?? '' }}</h4>
                                <p class="fc-stat-sub">{{ $data['reach_stat3_sub'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 4: Languages -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="fc-stat-card">
                            <div class="fc-stat-icon-wrap orb-reach-4">
                                <i class="bx bx-conversation"></i>
                            </div>
                            <div class="fc-stat-body">
                                <h3 class="fc-stat-number">{{ $data['reach_stat4_number'] ?? '' }}</h3>
                                <h4 class="fc-stat-label">{{ $data['reach_stat4_label'] ?? '' }}</h4>
                                <p class="fc-stat-sub">{{ $data['reach_stat4_sub'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- Best way to manage section / Mobile App Experience -->
    <section class="app-section" id="mobile-app">
        <div class="container">
            <div class="app-panel fc-app-panel wow fadeInUp" data-wow-delay="0.15s">

                <!-- Ambient atmospheric glow circles inside panel -->
                <div class="fc-app-ambient-glow" aria-hidden="true"></div>

                <div class="row align-items-center grid-app-layout position-relative">

                    <!-- Left: Content & Store Badges -->
                    <div class="col-lg-6">
                        <div class="app-copy fc-app-copy">

                            <div class="fc-app-pill-badge mb-3">
                                <span class="fc-badge-cross">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 2v20M7 8h10" />
                                    </svg>
                                </span>
                                <span>{{ $data['app_badge'] ?? '' }}</span>
                            </div>

                            <h2 class="fc-app-title">
                                {!! $data['app_title'] ?? '' !!}
                            </h2>

                            <p class="fc-app-lede">
                                {!! $data['app_subtitle'] ?? '' !!}
                            </p>

                            <!-- App Feature Checklist -->
                            <div class="fc-app-features-list">
                                <div class="fc-app-feature-item">
                                    <div class="fc-feat-icon"><i class="bx bx-check"></i></div>
                                    <span>{{ $data['app_feature1'] ?? '' }}</span>
                                </div>
                                <div class="fc-app-feature-item">
                                    <div class="fc-feat-icon"><i class="bx bx-check"></i></div>
                                    <span>{{ $data['app_feature2'] ?? '' }}</span>
                                </div>
                                <div class="fc-app-feature-item">
                                    <div class="fc-feat-icon"><i class="bx bx-check"></i></div>
                                    <span>{{ $data['app_feature3'] ?? '' }}</span>
                                </div>
                            </div>

                            <!-- Store Badges -->
                            <div class="d-flex flex-wrap align-items-center gap-3 mt-4 pt-2">
                                @if (isset($configArr['ios_app_link']) && !blank($configArr['ios_app_link']))
                                    <a target="_blank" href="{{ $configArr['ios_app_link'] }}"
                                        class="fc-store-button">
                                        <i class="bx bxl-apple"></i>
                                        <div class="text-start">
                                            <small>{{ __('messages.lbl_download_on_the') }}</small>
                                            <strong>{{ __('messages.lbl_app_store') }}</strong>
                                        </div>
                                    </a>
                                @endif
                                @if (isset($configArr['android_app_link']) && !blank($configArr['android_app_link']))
                                    <a target="_blank" href="{{ $configArr['android_app_link'] }}"
                                        class="fc-store-button">
                                        <i class="bx bxl-play-store"></i>
                                        <div class="text-start">
                                            <small>{{ __('messages.lbl_get_it_on') }}</small>
                                            <strong>{{ __('messages.lbl_play_store') }}</strong>
                                        </div>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right: Interactive Smartphone Mockup with Floating Badges -->
                    <div class="col-lg-6">
                        <div class="app-mockup-wrap fc-app-mockup-wrap text-center">

                            <!-- Backlight Halo -->
                            <div class="fc-mockup-halo" aria-hidden="true"></div>

                            <!-- Floating Card 1 (Top Left) -->
                            <div class="fc-app-float-badge float-top-left wow fadeIn" data-wow-delay="0.3s">
                                <div class="float-badge-icon icon-purple">
                                    <i class="bx bxs-bell-ring"></i>
                                </div>
                                <div class="text-start">
                                    <strong>{{ $data['app_float1_title'] ?? '' }}</strong>
                                    <span>{{ $data['app_float1_sub'] ?? '' }}</span>
                                </div>
                            </div>

                            <!-- Smartphone Image -->
                            @php
                                $appMockup = !empty($data['app_image'])
                                    ? _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['app_image']
                                    : asset('storage/web/home6') . '/assets/images/app-bg-mockup.png';
                            @endphp
                            <img src="{{ $appMockup }}"
                                alt="{{ $configArr['web_name'] }} Christian Matrimonial Mobile App"
                                class="app-mockup fc-app-mockup">

                            <!-- Floating Card 2 (Bottom Right) -->
                            <div class="fc-app-float-badge float-bottom-right wow fadeIn" data-wow-delay="0.4s">
                                <div class="float-badge-icon icon-green">
                                    <i class="bx bxs-check-shield"></i>
                                </div>
                                <div class="text-start">
                                    <strong>{{ $data['app_float2_title'] ?? '' }}</strong>
                                    <span>{{ $data['app_float2_sub'] ?? '' }}</span>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>



    <!-- Christian Community Directory Section Start -->
    <section class="fc-directory-section" id="community">
        <div class="fc-directory-glow" aria-hidden="true"></div>
        <div class="container position-relative">

            <!-- Section Header -->
            <div class="fc-directory-header text-center wow fadeInUp" data-wow-delay="0.1s">
                <div class="fc-pill-badge mb-3">
                    <span class="fc-badge-cross">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20M7 8h10" />
                        </svg>
                    </span>
                    <span>{{ $data['directory_badge'] ?? '' }}</span>
                </div>

                <h2 class="fc-directory-title">
                    {!! $data['directory_title'] ?? '' !!}
                </h2>

                <p class="fc-directory-desc mx-auto">
                    {!! $data['directory_subtitle'] ?? '' !!}
                </p>
            </div>

            <div class="row g-4 mt-2">
                @foreach ($matrimonyPagesData as $type => $items)
                    <div class="col-lg-6">
                        <div class="fc-dir-card wow fadeInUp" data-wow-delay="0.15s">
                            <div class="fc-dir-card-header">
                                <div class="fc-dir-icon orb-dir-1">
                                    <i class="bx bx-church"></i>
                                </div>
                                <div>
                                    <h4 class="fc-dir-name">{{ $items['label'] }}</h4>
                                </div>
                            </div>
                            <div class="fc-tag-cloud">
                                @foreach ($items['items'] as $item)
                                    <a href="{{ route('web.matrimony.index', $item['slug']) }}"
                                        class="fc-dir-tag">{{ $item['matrimony_name'] ?: $item['matrimony_name_old'] ?? 'N/A' }}</a>
                                @endforeach
                                <a href="{{ route('web.matrimony.moreDetails', Str::slug($type)) }}"
                                    class="fc-dir-tag-more">{{ __('messages.lbl_more_details') }} →</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Bottom Interactive Search Banner -->
            <div class="fc-dir-bottom-banner mt-5 wow fadeInUp" data-wow-delay="0.35s">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fc-dir-cta-icon">
                            <i class="bx bx-filter-alt"></i>
                        </div>
                        <div>
                            <h5 class="fc-dir-cta-title">{{ $data['directory_bottom_title'] ?? '' }}</h5>
                            <p class="fc-dir-cta-sub mb-0">{{ $data['directory_bottom_desc'] ?? '' }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('web.search.type', ['type' => 'advance-search']) }}"
                            class="btn-fc-pill-primary">
                            <span>{{ $data['directory_bottom_cta_text'] ?? '' }}</span>
                            <i class="bx bx-right-arrow-alt"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- footer section start -->
    <footer class="fc-footer-main">

        <!-- Top Ambient Purple Glow -->
        <div class="fc-footer-glow" aria-hidden="true"></div>

        <div class="container position-relative z-1">

            <!-- Top Section: Faith Community Newsletter Banner -->
            <div class="fc-footer-newsletter-card wow fadeInUp" data-wow-delay="0.1s">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="fc-newsletter-icon">
                                <i class="bx bx-envelope-open"></i>
                            </div>
                            <div>
                                <h4 class="fc-newsletter-title">{{ $data['footer_newsletter_title'] ?? '' }}</h4>
                                <p class="fc-newsletter-sub mb-0">{{ $data['footer_newsletter_sub'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <form class="fc-newsletter-form" action="{{ route('web.newsletter.subscribe') }}">
                            @csrf
                            <div class="fc-input-group">
                                <i class="bx bx-envelope fc-input-icon"></i>
                                <input type="email" name="email" class="fc-newsletter-input"
                                    placeholder="{{ __('messages.field_lbl_enter_your_email_id') }}" required>
                                <button type="submit" class="fc-newsletter-btn">
                                    <span class="btn-text">{{ __('messages.lbl_subscribe_free') }}</span>
                                    <span class="btn-loader"></span>
                                    <i class="bx bx-send"></i>
                                </button>
                            </div>
                        </form>

                        <div class="fc-newsletter-message" style="display: none;"></div>
                    </div>
                </div>
            </div>

            <!-- Main Footer Grid (5 Columns) -->
            <div class="row g-4 g-lg-5 fc-footer-grid">

                <!-- Column 1: Brand, Mission & Scripture -->
                <div class="col-lg-3 col-md-4">
                    <div class="fc-footer-brand-wrap">
                        <a href="{{ url('/') }}" class="d-inline-flex align-items-center gap-2 mb-3">
                            <div class="fc-footer-logo-badge">
                                <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                                    class="footer-logo" alt="{{ $configArr['web_name'] }} Logo">
                            </div>
                        </a>

                        <p class="fc-footer-mission">
                            {{ $data['footer_mission'] ?? '' }}
                        </p>
                    </div>
                </div>

                <!-- Column 2: Help & support -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h4 class="fc-footer-title">{{ __('messages.lbl_help_support') }}</h4>
                    <ul class="fc-footer-links">
                        <li><a href="{{ route('web.contactUs.index') }}">{{ __('messages.lbl_contact_us') }}</a>
                        </li>
                        <li><a href="{{ route('web.faq.index') }}">{{ __('messages.lbl_faqs') }}</a></li>
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
                    <h4 class="fc-footer-title">{{ __('messages.lbl_information') }}</h4>
                    <ul class="fc-footer-links">
                        <li><a href="{{ route('web.aboutUs.index') }}">{{ __('messages.lbl_about_us') }}</a></li>
                        @foreach ($cmsPages as $page)
                            <li><a
                                    href="{{ route('web.cmsPages.index', $page->page_url) }}">{{ $page->page_title }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Column 4: Others -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h4 class="fc-footer-title">{{ __('messages.lbl_others') }}</h4>
                    <ul class="fc-footer-links">
                        @guest('web')
                            <li><a href="{{ route('web.register.index') }}">{{ __('messages.lbl_register') }}</a></li>
                            <li><a href="{{ route('web.login.index') }}">{{ __('messages.lbl_login') }}</a></li>
                        @endguest
                        <li><a href="{{ route('web.event.index') }}">{{ __('messages.lbl_events') }}</a></li>
                        <li><a
                                href="{{ route('web.weddingVendors.index') }}">{{ __('messages.lbl_wedding_vendors') }}</a>
                        </li>
                    </ul>
                </div>

                <!-- Column 5: Contact info -->
                <div class="col-lg-3 col-md-6 col-6">
                    <h4 class="fc-footer-title">{{ __('messages.lbl_contact_info') }}</h4>
                    <div class="fc-footer-contact-box">
                        <div class="fc-contact-item">
                            <i class="bx bx-phone-call"></i>
                            <div>
                                <small>{{ __('messages.lbl_phone_number') }}</small>
                                <a href="tel:{{ $configArr['contact_no'] }}">{{ $configArr['contact_no'] }}</a>
                            </div>
                        </div>
                        <div class="fc-contact-item mt-2">
                            <i class="bx bx-envelope"></i>
                            <div>
                                <small>{{ __('messages.field_lbl_email_id') }}</small>
                                <a
                                    href="mailto:{{ $configArr['contact_email'] }}">{{ $configArr['contact_email'] }}</a>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Circles -->
                    <div class="fc-footer-socials d-flex align-items-center gap-2 mt-4">
                        @if (!empty($configArr['instagram_link']))
                            <a href="{{ $configArr['instagram_link'] }}" target="_blank" rel="noopener noreferrer"
                                aria-label="Instagram"><i class='bx bxl-instagram'></i></a>
                        @endif
                        @if (!empty($configArr['facebook_link']))
                            <a href="{{ $configArr['facebook_link'] }}" target="_blank" rel="noopener noreferrer"
                                aria-label="Facebook"><i class='bx bxl-facebook'></i></a>
                        @endif
                        @if (!empty($configArr['youtube_link']))
                            <a href="{{ $configArr['youtube_link'] }}" target="_blank" rel="noopener noreferrer"
                                aria-label="YouTube"><i class='bx bxl-youtube'></i></a>
                        @endif
                        @if (!empty($configArr['twitter_link']))
                            <a href="{{ $configArr['twitter_link'] }}" target="_blank" rel="noopener noreferrer"
                                aria-label="Twitter"><i class='bx bxl-twitter'></i></a>
                        @endif
                    </div>
                </div>

            </div>

        </div>

        <!-- Bottom Copyright Ribbon -->
        <div class="fc-footer-bottom-bar">
            <div class="container">
                <div
                    class="d-flex flex-wrap align-items-center justify-content-between gap-3 text-center text-md-start">
                    <p class="mb-0 fc-copy-text">
                        {{ $configArr['footer_text'] }}
                    </p>
                    <div class="fc-motto-text">
                        <span>{{ $data['footer_motto1'] ?? '' }}</span>
                        <span class="dot-purple"></span>
                        <span>{{ $data['footer_motto2'] ?? '' }}</span>
                        <span class="dot-purple"></span>
                        <span>{{ $data['footer_motto3'] ?? '' }}</span>
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
    <script src="{{ asset('storage/web/home6/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('storage/web/home6/assets/js/wow.js') }}"></script>
    <script src="{{ asset('storage/web/home6/assets/js/slick.min.js') }}"></script>
    <script src="{{ asset('storage/web/home6/assets/js/main.js') }}"></script>

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
            const $form = $('.fc-newsletter-form');
            if (!$form.length) return;

            const $messageBox = $('.fc-newsletter-message');
            const $submitBtn = $form.find('.fc-newsletter-btn');
            const $emailInput = $form.find('.fc-newsletter-input');

            let hideTimer = null;

            function showMessage(text, type) {
                clearTimeout(hideTimer);

                $messageBox
                    .text(text)
                    .removeClass('success error')
                    .addClass(type)
                    .stop(true, true)
                    .fadeIn(200);

                hideTimer = setTimeout(hideMessage, 2000);
            }

            function hideMessage() {
                clearTimeout(hideTimer);
                $messageBox.stop(true, true).fadeOut(400, function() {
                    $(this).removeClass('success error').text('');
                });
            }

            $emailInput.on('input', function() {
                if ($messageBox.is(':visible')) hideMessage();
            });

            $form.on('submit', function(e) {
                e.preventDefault();

                const formData = $form.serialize();

                hideMessage();
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

</body>

</html>

<!DOCTYPE html>
<html lang="en" data-theme="light">

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

    <!-- Google Fonts — Amiri & DM Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap"
        rel="stylesheet">

    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="{{ asset('storage/web/home5/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home5/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home5/assets/css/slick.css') }}">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
    <!-- Twilight Rose Design System -->
    <link rel="stylesheet" href="{{ asset('storage/web/home5/assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home5/assets/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home5/assets/css/animations.css') }}">

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

    <!-- ==========================================================================
       1. FLOATING GLASS NAVBAR (LOGO LEFT, 3 LINKS CENTER, ACTIONS RIGHT)
       ========================================================================== -->
    <nav aria-label="navbar" class="gm-navbar wow fadeInDown" data-wow-duration="0.6s">
        <div class="container">
            <div class="gm-nav-container">

                <!-- Mobile Menu Hamburger -->
                <button class="navbar-toggler d-lg-none" type="button" id="openDrawerBtn"
                    aria-label="Toggle navigation">
                    <i class="bx bx-menu"></i>
                </button>

                <!-- Brand Logo (Left Side) -->
                <a href="{{ url('/') }}" class="gm-brand-logo">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-1">
                            <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                                alt="{{ $configArr['web_name'] }} logo" class="brand-logo">
                        </div>
                    </div>
                </a>

                <!-- 5 Center Navigation Links (Desktop) -->
                <ul class="gm-nav-center-menu d-none d-lg-flex">
                    <li>
                        <a href="{{ url('/') }}" class="gm-nav-link active">
                            <iconify-icon icon="hugeicons:home-09"></iconify-icon>
                            <span>{{ __('messages.lbl_home') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('web.search.type', ['type' => 'quick-search']) }}" class="gm-nav-link">
                            <iconify-icon icon="hugeicons:ai-search"></iconify-icon>
                            <span>{{ __('messages.lbl_search') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('web.successStory.index') }}" class="gm-nav-link">
                            <iconify-icon icon="hugeicons:sparkles"></iconify-icon>
                            <span>{{ __('messages.lbl_success_stories') }}</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('web.membershipPlan.index') }}" class="gm-nav-link">
                            <iconify-icon icon="hugeicons:shield-01"></iconify-icon>
                            <span>{{ __('messages.lbl_membership') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('web.contactUs.index') }}" class="gm-nav-link">
                            <iconify-icon icon="ph:chats-circle-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_contact_us') }}</span>
                        </a>
                    </li>
                </ul>

                <!-- Right Controls: Theme Switcher & Actions -->
                <div class="gm-nav-actions">
                    <!-- CTAs (Desktop / Tablet) -->
                    <a href="{{ route('web.login.index') }}" class="gm-btn-outline d-none d-sm-inline-flex">
                        <span>{{ __('messages.lbl_login') }}</span>
                    </a>
                    <a href="{{ route('web.register.index') }}" class="gm-btn-primary d-none d-md-inline-flex">
                        <iconify-icon icon="hugeicons:sparkles" class="fts-16"></iconify-icon>
                        <span>{{ __('messages.lbl_register') }}</span>
                    </a>
                    {{-- Desktop Language Selector --}}
                    <div class="gm-language-wrapper" id="gmDesktopLanguage">
                        <button type="button" class="gm-language-toggle" aria-expanded="false" aria-haspopup="true"
                            aria-label="{{ __('messages.lbl_change_language') }}">

                            <iconify-icon icon="ph:translate-bold"></iconify-icon>
                            <span>{{ strtoupper($currentLangCode) }}</span>
                            <iconify-icon icon="lucide:chevron-down" class="gm-language-arrow"></iconify-icon>
                        </button>

                        <div class="gm-language-dropdown">
                            <div class="gm-language-heading">
                                <div>
                                    <strong>{{ __('messages.lbl_select_language') }}</strong>
                                    <small>{{ __('messages.lbl_choose_display_language') }}</small>
                                </div>

                                <span class="gm-language-count">
                                    {{ count($getActiveLanguage) }}
                                    {{ __('messages.lbl_languages') }}
                                </span>
                            </div>

                            <div class="gm-language-list">
                                @foreach ($getActiveLanguage as $value)
                                    <a href="{{ route('language.change', $value->lang_code) }}"
                                        class="gm-language-option {{ $value->lang_code == $currentLanguage ? 'active' : '' }}">

                                        <span class="gm-language-icon">
                                            <iconify-icon icon="akar-icons:language"></iconify-icon>
                                        </span>

                                        <span class="gm-language-text">
                                            <span>{{ $value->lang_name }}</span>
                                            <small>{{ strtoupper($value->lang_code) }}</small>
                                        </span>

                                        @if ($value->lang_code == $currentLanguage)
                                            <iconify-icon icon="ph:check-circle-fill"
                                                class="gm-language-check"></iconify-icon>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    <!-- ==========================================================================
       MOBILE OFFCANVAS DRAWER & BACKDROP
       ========================================================================== -->
    <div class="gm-drawer-overlay" id="drawerOverlay"></div>
    <div class="gm-mobile-drawer" id="mobileDrawer">
        <div class="gm-drawer-header">
            <a href="{{ url('/') }}" class="gm-brand-logo">
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center gap-1">
                        <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                            alt="{{ $configArr['web_name'] }} logo" class="brand-logo">
                    </div>
                </div>
            </a>
            <button class="close-toggle" id="closeDrawerBtn" aria-label="Close menu">
                <i class="bx bx-x"></i>
            </button>
        </div>

        <ul class="gm-drawer-menu">
            <li>
                <a href="{{ url('/') }}" class="gm-nav-link active">
                    <iconify-icon icon="hugeicons:home-09"></iconify-icon>
                    <span>{{ __('messages.lbl_home') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('web.search.type', ['type' => 'quick-search']) }}" class="gm-nav-link">
                    <iconify-icon icon="hugeicons:ai-search"></iconify-icon>
                    <span>{{ __('messages.lbl_search') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('web.successStory.index') }}" class="gm-nav-link">
                    <iconify-icon icon="hugeicons:sparkles"></iconify-icon>
                    <span>{{ __('messages.lbl_success_stories') }}</span>
                </a>
            </li>

            <li>
                <a href="{{ route('web.membershipPlan.index') }}" class="gm-nav-link">
                    <iconify-icon icon="hugeicons:shield-01"></iconify-icon>
                    <span>{{ __('messages.lbl_membership') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('web.contactUs.index') }}" class="gm-nav-link">
                    <iconify-icon icon="ph:chats-circle-bold"></iconify-icon>
                    <span>{{ __('messages.lbl_contact_us') }}</span>
                </a>
            </li>
        </ul>

        <div class="gm-drawer-footer">
            <a href="{{ route('web.login.index') }}" class="gm-btn-outline w-100 justify-content-center mb-2">
                <span>{{ __('messages.lbl_login') }}</span>
            </a>
            <a href="{{ route('web.register.index') }}" class="gm-btn-primary w-100 justify-content-center">
                <iconify-icon icon="hugeicons:sparkles" class="fts-16"></iconify-icon>
                <span>{{ __('messages.lbl_register') }}</span>
            </a>
        </div>
    </div>

    <!-- ==========================================================================
       2. CINEMATIC MUSLIM MATRIMONIAL HERO (SPLIT SHOWCASE LAYOUT)
       ========================================================================== -->
    <section class="gm-hero-section gm-muslim-hero">
        <!-- Atmospheric Ambient Lights & Subtle Halal Motifs -->
        <div class="gm-hero-backdrop-img"></div>
        <div class="gm-hero-ambient-glow glow-primary"></div>
        <div class="gm-hero-ambient-glow glow-accent"></div>
        <div class="gm-hero-ambient-glow glow-emerald"></div>

        <div class="container position-relative" style="z-index: 3;">
            <div class="row align-items-center g-4 g-xl-5 min-vh-hero">

                <!-- Left Showcase Column: Content, Trust & CTAs -->
                <div class="col-lg-7 text-start">
                    <div class="gm-hero-text-wrap">

                        <!-- Islamic Bismillah & Halal Badge -->
                        <div class="gm-hero-badge wow fadeInDown" data-wow-duration="0.7s">
                            <span class="gm-arabic-bismillah">{{ $data['hero_badge_text1'] ?? '' }}</span>
                            <span class="gm-badge-divider"></span>
                            <iconify-icon icon="hugeicons:moon-02" class="gm-badge-icon"></iconify-icon>
                            <span>{{ $data['hero_badge_text2'] ?? '' }}</span>
                        </div>

                        <!-- Main Emotionally Resonant Headline -->
                        <h1 class="gm-hero-title wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.1s">
                            {!! $data['hero_title'] ?? '' !!}
                        </h1>

                        <!-- Subtitle -->
                        <p class="gm-hero-subtitle wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.2s">
                            {!! $data['hero_subtitle'] ?? '' !!}
                        </p>

                        <!-- 3 Halal Trust Pillars -->
                        <div class="gm-trust-pillars wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.25s">
                            <div class="gm-trust-item">
                                <div class="gm-trust-icon">
                                    <iconify-icon icon="hugeicons:shield-01"></iconify-icon>
                                </div>
                                <div class="gm-trust-text">
                                    <strong>{{ $data['hero_trust1_title'] ?? '' }}</strong>
                                    <span>{{ $data['hero_trust1_sub'] ?? '' }}</span>
                                </div>
                            </div>

                            <div class="gm-trust-item">
                                <div class="gm-trust-icon icon-gold">
                                    <iconify-icon icon="hugeicons:user-id-verification"></iconify-icon>
                                </div>
                                <div class="gm-trust-text">
                                    <strong>{{ $data['hero_trust2_title'] ?? '' }}</strong>
                                    <span>{{ $data['hero_trust2_sub'] ?? '' }}</span>
                                </div>
                            </div>

                            <div class="gm-trust-item">
                                <div class="gm-trust-icon icon-emerald">
                                    <iconify-icon icon="hugeicons:sparkles"></iconify-icon>
                                </div>
                                <div class="gm-trust-text">
                                    <strong>{{ $data['hero_trust3_title'] ?? '' }}</strong>
                                    <span>{{ $data['hero_trust3_sub'] ?? '' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- CTAs & Action Buttons -->
                        <div class="gm-hero-action-row wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.3s">
                            <a href="#search-section" class="gm-hero-cta">
                                <span>{{ __('messages.lbl_find_your_match') ?? 'Find Your Match' }}</span>
                                <iconify-icon icon="hugeicons:arrow-right-01" class="fts-20"></iconify-icon>
                            </a>
                            <a href="#search-section" class="gm-hero-secondary-btn">
                                <iconify-icon icon="hugeicons:search-01" class="fts-18"></iconify-icon>
                                <span>{{ __('messages.lbl_browse_profiles') ?? 'Browse Halal Profiles' }}</span>
                            </a>
                        </div>

                        <!-- Social Community Proof & Rating -->
                        <div class="gm-hero-community-proof wow fadeInUp" data-wow-duration="0.8s"
                            data-wow-delay="0.35s">
                            <div class="gm-proof-avatars">
                                <img src="{{ asset('storage/web/home5') }}/assets/images/last-profile-01.png"
                                    alt="Muslim Member">
                                <img src="{{ asset('storage/web/home5') }}/assets/images/last-profile-02.png"
                                    alt="Muslim Member">
                                <img src="{{ asset('storage/web/home5') }}/assets/images/last-profile-03.png"
                                    alt="Muslim Member">
                                <img src="{{ asset('storage/web/home5') }}/assets/images/last-profile-04.png"
                                    alt="Muslim Member">
                                <span class="gm-proof-plus">{{ $data['hero_proof_count'] ?? '' }}</span>
                            </div>
                            <div class="gm-proof-meta">
                                <div class="gm-stars">
                                    <i class="bx bxs-star"></i>
                                    <i class="bx bxs-star"></i>
                                    <i class="bx bxs-star"></i>
                                    <i class="bx bxs-star"></i>
                                    <i class="bx bxs-star"></i>
                                    <span class="gm-stars-score">{{ $data['hero_proof_rating'] ?? '' }}</span>
                                </div>
                                <span class="gm-proof-sub">{{ $data['hero_proof_sub'] ?? '' }}</span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right Showcase Column: Transparent PNG Couple with Islamic Halo & Floating Badges -->
                <div class="col-lg-5 text-center position-relative">
                    <div class="gm-couple-png-stage wow zoomIn" data-wow-duration="0.9s" data-wow-delay="0.2s">

                        <!-- Glowing Islamic Arabesque Halo & Arch Silhouette Backdrop -->
                        <div class="gm-couple-halo-backdrop"></div>
                        <div class="gm-couple-arch-silhouette"></div>

                        <!-- Transparent PNG Couple -->
                        <div class="gm-couple-png-wrapper">
                            <img src="{{ !empty($data['hero_image']) ? _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['hero_image'] : asset('storage/web/home5') . '/assets/images/muslim-couple-hero.png' }}"
                                alt="Muslim Matrimonial Bride and Groom" class="gm-couple-png-img">
                            <div class="gm-couple-floor-shadow"></div>
                        </div>

                        <!-- Floating Micro-Card 1: Compatibility -->
                        <div class="gm-floating-chip chip-top-right wow fadeInRight" data-wow-duration="0.8s"
                            data-wow-delay="0.4s">
                            <div class="gm-chip-icon chip-accent">
                                <iconify-icon icon="hugeicons:favourite"></iconify-icon>
                            </div>
                            <div class="text-start">
                                <strong class="d-block">98% Compatibility</strong>
                                <span class="chip-sub">Deen &amp; Values Aligned</span>
                            </div>
                        </div>

                        <!-- Floating Micro-Card 2: Nikah Mubarak Story -->
                        <div class="gm-floating-card card-bottom-left wow fadeInLeft" data-wow-duration="0.8s"
                            data-wow-delay="0.5s">
                            <div class="gm-story-badge">
                                <iconify-icon icon="hugeicons:sparkles"></iconify-icon>
                                <span>Nikah Mubarak</span>
                            </div>
                            <p class="gm-story-quote">{{ $data['hero_float_quote'] ?? '' }}</p>
                            <div class="gm-story-couple-meta">
                                <span class="gm-couple-names">{{ $data['hero_float_names'] ?? '' }}</span>
                                <span class="gm-story-tag">Blessed Union</span>
                            </div>
                        </div>

                        <!-- Floating Micro-Card 3: Halal Verified -->
                        <div class="gm-floating-chip chip-bottom-right wow fadeInUp" data-wow-duration="0.8s"
                            data-wow-delay="0.6s">
                            <div class="gm-chip-icon chip-success">
                                <iconify-icon icon="hugeicons:shield-01"></iconify-icon>
                            </div>
                            <div class="text-start">
                                <strong class="d-block">100% Halal Verified</strong>
                                <span class="chip-sub">Wali &amp; Family Supported</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ==========================================================================
       3. HALAL SMART SEARCH CAPSULE WIDGET
       ========================================================================== -->
    <div id="search-section" class="gm-search-wrapper pb-3 pb-lg-0">
        <div class="container">
            <div class="gm-search-card wow fadeInUp" data-wow-duration="0.9s" data-wow-delay="0.2s">
                <form action="{{ route('web.search.searchResult') }}" method="GET">
                    <div class="row align-items-center g-3">

                        <!-- Looking for -->
                        <div class="col-lg-3 col-md-6">
                            <div class="gm-search-field">
                                <label for="Looking">
                                    <iconify-icon icon="hugeicons:user-group"></iconify-icon>
                                    <span>{{ __('messages.lbl_i_m_looking_for_a') }}</span>
                                </label>
                                <select name="looking_for" id="Looking" class="custom-select sources">
                                    <option value="Male" title="Male">{{ __('messages.field_lbl_male') }}
                                    </option>
                                    <option value="Female" title="Female">{{ __('messages.field_lbl_female') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Age Range -->
                        <div class="col-lg-3 col-md-6">
                            <div class="d-flex align-items-end gap-2">
                                <div class="gm-search-field w-100">
                                    <label for="agefrom">
                                        <iconify-icon icon="hugeicons:calendar-03"></iconify-icon>
                                        <span>{{ __('messages.field_lbl_age_from') }}</span>
                                    </label>
                                    @php $age = _ageRang(); @endphp
                                    <select name="from_age" id="agefrom" class="custom-select sources">
                                        @foreach ($age as $key => $valueArr)
                                            <option value="{{ $key }}" title="{{ $valueArr }}">
                                                {{ $valueArr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="gm-search-to">to</span>
                                <div class="gm-search-field w-100">
                                    <label for="ageto"
                                        class="opacity-0">{{ __('messages.field_lbl_age_to') }}</label>
                                    <select name="age_to" id="ageto" class="custom-select sources">
                                        @foreach ($age as $key => $valueArr)
                                            @php $selected = ($key == '30') ? 'selected' : ''; @endphp
                                            <option value="{{ $key }}" {{ $selected }}
                                                title="{{ $valueArr }}">{{ $valueArr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Religion -->
                        <div class="col-lg-2 col-md-6">
                            <div class="gm-search-field">
                                <label for="religion">
                                    <iconify-icon icon="hugeicons:moon-02"></iconify-icon>
                                    <span>{{ __('messages.field_lbl_religion') }}</span>
                                </label>
                                <select name="religion" id="religion" class="custom-select sources">
                                    <option class="list" value="" selected
                                        title="{{ __('messages.field_lbl_select_religion') }}">
                                        {{ __('messages.field_lbl_select_religion') }}</option>
                                    @foreach ($religionList as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Location / Country -->
                        <div class="col-lg-2 col-md-6">
                            <div class="gm-search-field">
                                <label for="Country">
                                    <iconify-icon icon="hugeicons:globe-02"></iconify-icon>
                                    <span>{{ __('messages.field_lbl_country') }}</span>
                                </label>
                                <select name="country_id" id="Country" class="custom-select sources">
                                    <option class="list" value="" selected
                                        title="{{ __('messages.field_lbl_select_country') }}">
                                        {{ __('messages.field_lbl_select_country') }}</option>
                                    @foreach ($religionList as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Search Button -->
                        <div class="col-lg-2 col-md-12">
                            <button class="gm-search-submit w-100" type="submit">
                                <span>{{ __('messages.lbl_search') }}</span>
                                <iconify-icon icon="hugeicons:search-01" class="fts-20"></iconify-icon>
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==========================================================================
       4. HOW DOES IT WORK (THE BLESSED HALAL JOURNEY — ISLAMIC REDESIGN)
       ========================================================================== -->
    <section class="gm-steps-section" id="how-it-works">
        <!-- Islamic Ambient Lighting & Geometry Overlay -->
        <div class="gm-islamic-pattern-overlay"></div>


        <div class="gm-islamic-floating-rosette rosette-left wow fadeIn" data-wow-duration="1.5s">
            <img src="{{ asset('storage/web/home5') }}/assets/images/islamic-rosette.svg"
                alt="Islamic Star Rosette" />
        </div>
        <div class="gm-islamic-floating-rosette rosette-right wow fadeIn" data-wow-duration="1.5s"
            data-wow-delay="0.3s">
            <img src="{{ asset('storage/web/home5') }}/assets/images/islamic-rosette.svg"
                alt="Islamic Star Rosette" />
        </div>

        <div class="container position-relative" style="z-index: 2;">

            <!-- Islamic Section Header -->
            <div class="gm-section-header wow fadeInDown" data-wow-duration="0.8s">
                <!-- Bismillah / Islamic Crest -->
                <div class="gm-islamic-header-crest">
                    <span class="crest-line"></span>
                    <span class="crest-star">۞</span>
                    <span class="crest-arabic font-arabic">بِسْمِ ٱللَّٰهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</span>
                    <span class="crest-star">۞</span>
                    <span class="crest-line"></span>
                </div>

                <span class="gm-pill-badge gm-islamic-badge">
                    <iconify-icon icon="hugeicons:moon-02"></iconify-icon>
                    <span>{{ $data['journey_badge_text'] ?? '' }}</span>
                    <span class="badge-dot">✦</span>
                    <span class="badge-arabic-sub font-arabic">٣ خطوات مباركة</span>
                </span>
                <h2 class="gm-section-title">
                    {!! $data['journey_title'] ?? '' !!}
                </h2>
                <p class="gm-section-subtitle">
                    {!! $data['journey_subtitle'] ?? '' !!}
                </p>
            </div>



            <div class="row g-4 position-relative">

                <!-- Step 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="gm-islamic-card wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.1s">
                        <!-- Mihrab Top Arch & Star Finial -->
                        <div class="gm-card-mihrab-crest">
                            <div class="mihrab-finial">
                                <span class="finial-icon">✦</span>
                            </div>
                            <div class="mihrab-arch-svg">
                                <svg viewBox="0 0 200 45" preserveAspectRatio="none">
                                    <path d="M0,45 C45,45 70,8 100,2 C130,8 155,45 200,45" fill="none"
                                        stroke="currentColor" stroke-width="1.5" />
                                </svg>
                            </div>
                        </div>

                        <!-- 8-Pointed Rub el Hizb Number Medallion -->
                        <div class="gm-step-star-badge" title="Step 01">
                            <svg class="star-badge-svg" viewBox="0 0 64 64">
                                <rect x="14" y="14" width="36" height="36" class="star-box b1" />
                                <rect x="14" y="14" width="36" height="36" class="star-box b2" />
                                <circle cx="32" cy="32" r="15" class="star-core" />
                            </svg>
                            <div class="star-num-wrap">

                                <span class="star-english">01</span>
                            </div>
                        </div>

                        <!-- Mihrab Sanctuary Chamber (Icon Housing) -->
                        <div class="gm-mihrab-niche">
                            <div class="gm-niche-arch-bg"></div>
                            <div class="gm-step-icon-halo">
                                <iconify-icon icon="hugeicons:user-add-01"></iconify-icon>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="gm-card-body-content">
                            <div class="gm-step-arabic-tag">{{ $data['journey_step1_tag'] ?? '' }}</div>
                            <h4 class="gm-step-title">{{ $data['journey_step1_title'] ?? '' }}</h4>
                            <p class="gm-step-desc">
                                {{ $data['journey_step1_desc'] ?? '' }}
                            </p>

                            <!-- Halal Trust Highlights -->
                            <div class="gm-step-tags">
                                <span class="gm-halal-pill"><i class="bx bx-shield-quarter"></i> 100% Privacy</span>
                                <span class="gm-halal-pill"><i class="bx bx-user-check"></i> Wali Support</span>
                            </div>
                        </div>


                    </div>
                </div>

                <!-- Step 2 (Highlight Card) -->
                <div class="col-lg-4 col-md-6">
                    <div class="gm-islamic-card gm-card-highlight wow fadeInUp" data-wow-duration="0.8s"
                        data-wow-delay="0.25s">
                        <!-- Halal AI Match Ribbon -->
                        <div class="gm-card-top-tag">
                            <iconify-icon icon="hugeicons:sparkles"></iconify-icon>
                            <span>Smart Halal Match</span>
                        </div>

                        <!-- Mihrab Top Arch & Star Finial -->
                        <div class="gm-card-mihrab-crest">
                            <div class="mihrab-finial">
                                <span class="finial-icon">✦</span>
                            </div>
                            <div class="mihrab-arch-svg">
                                <svg viewBox="0 0 200 45" preserveAspectRatio="none">
                                    <path d="M0,45 C45,45 70,8 100,2 C130,8 155,45 200,45" fill="none"
                                        stroke="currentColor" stroke-width="1.5" />
                                </svg>
                            </div>
                        </div>

                        <!-- 8-Pointed Rub el Hizb Number Medallion -->
                        <div class="gm-step-star-badge" title="Step 02">
                            <svg class="star-badge-svg" viewBox="0 0 64 64">
                                <rect x="14" y="14" width="36" height="36" class="star-box b1" />
                                <rect x="14" y="14" width="36" height="36" class="star-box b2" />
                                <circle cx="32" cy="32" r="15" class="star-core" />
                            </svg>
                            <div class="star-num-wrap">

                                <span class="star-english">02</span>
                            </div>
                        </div>

                        <!-- Mihrab Sanctuary Chamber (Icon Housing) -->
                        <div class="gm-mihrab-niche">
                            <div class="gm-niche-arch-bg"></div>
                            <div class="gm-step-icon-halo">
                                <iconify-icon icon="hugeicons:ai-search"></iconify-icon>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="gm-card-body-content">
                            <div class="gm-step-arabic-tag">{{ $data['journey_step2_tag'] ?? '' }}</div>
                            <h4 class="gm-step-title">{{ $data['journey_step2_title'] ?? '' }}</h4>
                            <p class="gm-step-desc">
                                {{ $data['journey_step2_desc'] ?? '' }}
                            </p>

                            <!-- Halal Trust Highlights -->
                            <div class="gm-step-tags">
                                <span class="gm-halal-pill"><i class="bx bx-badge-check"></i> ID Verified</span>
                                <span class="gm-halal-pill"><i class="bx bx-filter-alt"></i> Deen Compatibility</span>
                            </div>
                        </div>


                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-lg-4 col-md-6 mx-auto">
                    <div class="gm-islamic-card wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.4s">
                        <!-- Mihrab Top Arch & Star Finial -->
                        <div class="gm-card-mihrab-crest">
                            <div class="mihrab-finial">
                                <span class="finial-icon">✦</span>
                            </div>
                            <div class="mihrab-arch-svg">
                                <svg viewBox="0 0 200 45" preserveAspectRatio="none">
                                    <path d="M0,45 C45,45 70,8 100,2 C130,8 155,45 200,45" fill="none"
                                        stroke="currentColor" stroke-width="1.5" />
                                </svg>
                            </div>
                        </div>

                        <!-- 8-Pointed Rub el Hizb Number Medallion -->
                        <div class="gm-step-star-badge" title="Step 03">
                            <svg class="star-badge-svg" viewBox="0 0 64 64">
                                <rect x="14" y="14" width="36" height="36" class="star-box b1" />
                                <rect x="14" y="14" width="36" height="36" class="star-box b2" />
                                <circle cx="32" cy="32" r="15" class="star-core" />
                            </svg>
                            <div class="star-num-wrap">

                                <span class="star-english">03</span>
                            </div>
                        </div>

                        <!-- Mihrab Sanctuary Chamber (Icon Housing) -->
                        <div class="gm-mihrab-niche">
                            <div class="gm-niche-arch-bg"></div>
                            <div class="gm-step-icon-halo">
                                <iconify-icon icon="hugeicons:favourite"></iconify-icon>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="gm-card-body-content">
                            <div class="gm-step-arabic-tag">{{ $data['journey_step3_tag'] ?? '' }}</div>
                            <h4 class="gm-step-title">{{ $data['journey_step3_title'] ?? '' }}</h4>
                            <p class="gm-step-desc">
                                {{ $data['journey_step3_desc'] ?? '' }}
                            </p>

                            <!-- Halal Trust Highlights -->
                            <div class="gm-step-tags">
                                <span class="gm-halal-pill"><i class="bx bx-conversation"></i> Halal
                                    Interaction</span>
                                <span class="gm-halal-pill"><i class="bx bx-home-heart"></i> Sunnah Nikah</span>
                            </div>
                        </div>


                    </div>
                </div>

            </div>

            <!-- Islamic Hadith Quote Banner at Bottom -->
            <div class="gm-islamic-hadith-bar wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.5s">
                <div class="hadith-inner">
                    <span class="hadith-star">۞</span>
                    <p class="hadith-text">
                        {{ $data['journey_hadith_text'] ?? '' }}
                        <span class="hadith-source">{{ $data['journey_hadith_source'] ?? '' }}</span>
                    </p>
                    <span class="hadith-star">۞</span>
                </div>
            </div>

        </div>
    </section>

    <!-- ==========================================================================
       5. LAST ADDED PROFILES (AUTHENTIC MUSLIM MATCHES — ISLAMIC REDESIGN)
       ========================================================================== -->
    @if ($latestProfile->isNotEmpty())
        <section class="gm-profiles-section" id="plans-section">
            <!-- Islamic Subtle Geometric Overlay -->
            <div class="gm-profiles-pattern-bg"></div>

            <div class="container position-relative" style="z-index: 2;">

                <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 mb-lg-5 gap-3">
                    <div>
                        <!-- Bismillah / Islamic Crest Accent -->
                        <div class="gm-islamic-header-crest mb-2">
                            <span class="crest-line"></span>
                            <span class="crest-star">۞</span>
                            <span class="crest-arabic font-arabic">أَعْضَاءٌ مُوَثَّقُونَ حَدِيثاً</span>
                            <span class="crest-star">۞</span>
                            <span class="crest-line"></span>
                        </div>
                        <span class="gm-pill-badge gm-islamic-badge">
                            <iconify-icon icon="hugeicons:shield-01"></iconify-icon>
                            <span>{{ $data['profiles_badge_text'] ?? '' }}</span>
                            <span class="badge-dot">✦</span>
                            <span class="badge-arabic-sub font-arabic">توثيق شرعي</span>
                        </span>
                        <h2 class="gm-section-title mt-2 mb-1">
                            {!! $data['profiles_title'] ?? '' !!}
                        </h2>
                        <p class="gm-profiles-subtitle">
                            {!! $data['profiles_subtitle'] ?? '' !!}
                        </p>
                    </div>
                    <div class="lastProfileArrows"></div>
                </div>

                <div class="LastProfileSlider wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.2s">
                    @foreach ($latestProfile as $profile)
                        @php
                            $canView = _canViewMemberPhoto($profile, $profile->hasPhotoRequestAccess ?? '');
                            $hasPhoto = _checkPhotoExist($profile);
                            $profileImage = _getMemberProfileImage($profile);
                        @endphp
                        <div class="gm-profile-card">
                            <div class="gm-profile-img-holder">
                                <a href="{{ route('web.userProfile.index', _encrypt($profile->id)) }}">
                                    @if (!$canView && $hasPhoto)
                                        <img src="{{ _getProtectedImage($profile->gender) }}"
                                            alt="{{ _profileTitle($profile) }}">
                                    @else
                                        <img src="{{ $profileImage }}" alt="{{ _profileTitle($profile) }}">
                                    @endif
                                </a>
                                @if ($profile->plan_status == 'Paid')
                                    <div class="gm-verified-badge-pill">
                                        <i class="bx bxs-check-shield"></i>
                                        <span>{{ $profile->plan_name }}</span>
                                    </div>
                                @endif
                                <!-- Deen Snapshot Overlay -->
                                <div class="gm-img-overlay-info">
                                    <span class="gm-deen-pill"><i class="bx bx-star"></i>
                                        {{ optional($profile->casteData)->translated_name }}</span>
                                </div>
                            </div>
                            <div class="gm-profile-body">
                                <div class="gm-profile-info-block">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                        <h4 class="gm-profile-name">{{ _profileTitle($profile) }}</h4>
                                    </div>
                                    <p class="gm-profile-info">{{ _birthdateDisplay($profile->birthdate, 0) }} •
                                        {{ _displayHeight($profile->height) }}</p>
                                    <p class="gm-profile-loc"><i class="bx bx-map-pin"></i>
                                        {{ _getMemberLocation($profile) }}</p>
                                </div>
                                <a href="{{ route('web.userProfile.index', _encrypt($profile->id)) }}"
                                    class="gm-btn-view-profile" aria-label="View Profile" title="View Halal Profile">
                                    <iconify-icon icon="hugeicons:view"></iconify-icon>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ==========================================================================
       6. BLESSED NIKAH & SUCCESS STORIES (ISLAMIC THEME)
       ========================================================================== -->
    @if ($successStoryArr->isNotEmpty())
        <section id="success-stories-section" class="gm-stories-section">
            <div class="container">

                <!-- Section Header with Quranic Verse & Islamic Crest -->
                <div class="gm-stories-header-wrapper mb-4 mb-lg-5">
                    <div class="row align-items-end g-3">
                        <div class="col-lg-8">
                            <div class="gm-islamic-crest mb-2">
                                <span class="crest-line"></span>
                                <span class="crest-arabic">{{ $data['stories_verse'] ?? '' }}</span>
                                <span class="crest-line"></span>
                            </div>
                            <span class="gm-pill-badge">
                                <iconify-icon icon="solar:heart-angle-bold" class="text-accent me-1"></iconify-icon>
                                {{ $data['stories_badge_text'] ?? '' }}
                            </span>
                            <h2 class="gm-section-title mt-2 mb-2">
                                {!! $data['stories_title'] ?? '' !!}
                            </h2>
                            <p class="gm-section-desc mb-0">
                                {!! $data['stories_subtitle'] ?? '' !!}
                            </p>
                        </div>
                        <div class="col-lg-4 d-flex justify-content-lg-end justify-content-start">
                            <div class="successStoryArrows"></div>
                        </div>
                    </div>
                </div>

                <!-- Halal Stories Slider -->
                <div class="happy-success-Slider">
                    @foreach ($successStoryArr as $story)
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
                            $storyDesc = Str::limit(strip_tags($story->successmessage), 200);
                        @endphp
                        <div class="gm-story-slide">
                            <div class="gm-nikah-card">
                                <div class="gm-nikah-grid">
                                    <!-- Left: Architectural Mihrab Photo Column -->
                                    <div class="gm-nikah-photo-col">
                                        <div class="gm-nikah-photo-frame">
                                            <img src="{{ $weddingImage }}" alt="{{ $bridegroomName }}"
                                                class="gm-nikah-img">
                                            <!-- Floating Date Pill -->
                                            <div class="gm-nikah-date-tag">
                                                <iconify-icon icon="solar:calendar-date-bold"
                                                    class="me-1 text-accent"></iconify-icon>
                                                <span>{{ _displayDate($story->marriagedate, 'M Y') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right: Content & Testimonial Column -->
                                    <div class="gm-nikah-body-col">
                                        <h3 class="gm-nikah-couple-name">
                                            {{ $bridegroomName }}
                                            <span class="gm-nikah-verified-badge" title="Wali & Sharia Verified">
                                                <iconify-icon icon="solar:verified-check-bold"></iconify-icon>
                                                <span>Halal Verified</span>
                                            </span>
                                        </h3>
                                        <p class="gm-nikah-location">
                                            <iconify-icon icon="solar:map-point-wave-bold"
                                                class="text-accent"></iconify-icon>
                                            {{ optional($story->religionData)->translated_name }}
                                        </p>
                                        <div class="gm-nikah-quote-box">
                                            <iconify-icon icon="solar:quote-up-bold"
                                                class="gm-quote-icon"></iconify-icon>
                                            <p class="gm-nikah-quote-text">
                                                "{{ $storyDesc }}"
                                            </p>
                                        </div>
                                        <div class="gm-nikah-footer">
                                            <a href="{{ route('web.successStory.index') }}"
                                                class="gm-nikah-read-link">
                                                <span>{{ __('messages.lbl_read_full_story') ?? 'Read Full Nikah Story' }}</span>
                                                <iconify-icon icon="hugeicons:arrow-right-01"></iconify-icon>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </section>
    @endif

    <!-- ==========================================================================
       7. WHY CHOOSE MUSLIM MATRIMONIAL (ISLAMIC BENTO ARCHITECTURE)
       ========================================================================== -->
    <section class="gm-excellence-section">
        <div class="container">

            <!-- Section Header with Hadith & Islamic Crest -->
            <div class="gm-section-header text-center mb-5">
                <div class="gm-islamic-crest mb-2">
                    <span class="crest-line"></span>
                    <span class="crest-arabic">{{ $data['why_us_hadith'] ?? '' }}</span>
                    <span class="crest-line"></span>
                </div>
                <span class="gm-pill-badge">
                    <iconify-icon icon="solar:shield-star-bold" class="text-accent me-1"></iconify-icon>
                    {{ $data['why_us_badge_text'] ?? '' }}
                </span>
                <h2 class="gm-section-title mt-2 mb-2">
                    {!! $data['why_us_title'] ?? '' !!}
                </h2>
                <p class="gm-section-desc mx-auto" style="max-width: 680px;">
                    {!! $data['why_us_subtitle'] ?? '' !!}
                </p>
            </div>

            <!-- Asymmetric Islamic Bento Grid -->
            <div class="gm-excellence-bento">
                <div class="row g-4 align-items-stretch">

                    <!-- Left: Architectural Moorish Mihrab Pillar -->
                    <div class="col-lg-5">
                        <div class="gm-bento-hero-arch">
                            <div class="gm-bento-hero-img-wrap">
                                <img src="{{ !empty($data['why_us_image']) ? _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['why_us_image'] : asset('storage/web/home5') . '/assets/images/muslim-couple-portrait.jpg' }}"
                                    alt="Pious Muslim Matrimony - Sacred Halal Union" class="gm-bento-hero-img">
                                <div class="gm-bento-hero-overlay"></div>

                                <!-- Floating Top Trust Pill -->
                                <div class="gm-bento-badge-top">
                                    <iconify-icon icon="solar:shield-check-bold" class="text-accent"></iconify-icon>
                                    <span>{{ $data['why_us_stat_badge_top'] ?? '' }}</span>
                                </div>


                                <!-- Bottom Glassmorphic Trust Card -->
                                <div class="gm-bento-hero-footer">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="gm-bento-stat-circle">
                                            <span class="stat-pct">{{ $data['why_us_stat_pct'] ?? '' }}</span>
                                        </div>
                                        <div>
                                            <h5 class="hero-footer-title mb-1">{{ $data['why_us_stat_title'] ?? '' }}
                                            </h5>
                                            <p class="hero-footer-sub mb-0">{{ $data['why_us_stat_sub'] ?? '' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: 4 Creative Islamic Value Pillars (2x2 Grid) -->
                    <div class="col-lg-7">
                        <div class="row g-4 h-100">

                            <!-- Pillar 1: Wali & Guardian Supervised -->
                            <div class="col-md-6">
                                <div class="gm-bento-card">
                                    <div class="gm-bento-card-header">
                                        <div class="gm-bento-icon-box">
                                            <iconify-icon icon="solar:users-group-two-rounded-bold"></iconify-icon>
                                        </div>
                                        <span class="gm-bento-ar-tag">{{ $data['why_us_sec1_heading'] ?? '' }}</span>
                                    </div>
                                    <h4 class="gm-bento-card-title">{{ $data['why_us_sec1_title'] ?? '' }}</h4>
                                    <p class="gm-bento-card-desc">
                                        {{ $data['why_us_sec1_subtitle'] ?? '' }}
                                    </p>
                                    <div class="gm-bento-card-footer">
                                        <span class="gm-bento-metric-chip">
                                            <iconify-icon icon="solar:shield-check-bold"></iconify-icon>
                                            <span>{{ $data['why_us_sec1_badge'] ?? '' }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pillar 2: 100% Manual ID Vetting -->
                            <div class="col-md-6">
                                <div class="gm-bento-card">
                                    <div class="gm-bento-card-header">
                                        <div class="gm-bento-icon-box">
                                            <iconify-icon icon="solar:verified-check-bold"></iconify-icon>
                                        </div>
                                        <span class="gm-bento-ar-tag">{{ $data['why_us_sec2_heading'] ?? '' }}</span>
                                    </div>
                                    <h4 class="gm-bento-card-title">{{ $data['why_us_sec2_title'] ?? '' }}</h4>
                                    <p class="gm-bento-card-desc">
                                        {{ $data['why_us_sec2_subtitle'] ?? '' }}
                                    </p>
                                    <div class="gm-bento-card-footer">
                                        <span class="gm-bento-metric-chip">
                                            <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                                            <span>{{ $data['why_us_sec2_badge'] ?? '' }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pillar 3: Modesty & Privacy First -->
                            <div class="col-md-6">
                                <div class="gm-bento-card">
                                    <div class="gm-bento-card-header">
                                        <div class="gm-bento-icon-box">
                                            <iconify-icon icon="solar:lock-keyhole-minimalistic-bold"></iconify-icon>
                                        </div>
                                        <span class="gm-bento-ar-tag">{{ $data['why_us_sec3_heading'] ?? '' }}</span>
                                    </div>
                                    <h4 class="gm-bento-card-title">{{ $data['why_us_sec3_title'] ?? '' }}</h4>
                                    <p class="gm-bento-card-desc">
                                        {{ $data['why_us_sec3_subtitle'] ?? '' }}
                                    </p>
                                    <div class="gm-bento-card-footer">
                                        <span class="gm-bento-metric-chip">
                                            <iconify-icon icon="solar:eye-closed-bold"></iconify-icon>
                                            <span>{{ $data['why_us_sec3_badge'] ?? '' }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pillar 4: Sunnah & Deen Harmony -->
                            <div class="col-md-6">
                                <div class="gm-bento-card">
                                    <div class="gm-bento-card-header">
                                        <div class="gm-bento-icon-box">
                                            <iconify-icon icon="solar:compass-bold"></iconify-icon>
                                        </div>
                                        <span class="gm-bento-ar-tag">{{ $data['why_us_sec4_heading'] ?? '' }}</span>
                                    </div>
                                    <h4 class="gm-bento-card-title">{{ $data['why_us_sec4_title'] ?? '' }}</h4>
                                    <p class="gm-bento-card-desc">
                                        {{ $data['why_us_sec4_subtitle'] ?? '' }}
                                    </p>
                                    <div class="gm-bento-card-footer">
                                        <span class="gm-bento-metric-chip">
                                            <iconify-icon icon="solar:heart-angle-bold"></iconify-icon>
                                            <span>{{ $data['why_us_sec4_badge'] ?? '' }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- ==========================================================================
       8. GLOBAL UMMAH STATS RIBBON (ISLAMIC THEME)
       ========================================================================== -->
    <section class="gm-stats-section">
        <div class="container">
            <div class="row g-4">

                <!-- Stat 1 -->
                <div class="col-lg-3 col-sm-6">
                    <div class="gm-stat-item-box">
                        <div class="gm-stat-icon-circle">
                            <iconify-icon icon="solar:users-group-rounded-bold"></iconify-icon>
                        </div>
                        <div>
                            <div class="gm-stat-number">{{ $data['stats_number1'] ?? '' }}</div>
                            <div class="gm-stat-label">{{ $data['stats_label1'] ?? '' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Stat 2 -->
                <div class="col-lg-3 col-sm-6">
                    <div class="gm-stat-item-box">
                        <div class="gm-stat-icon-circle">
                            <iconify-icon icon="solar:global-bold"></iconify-icon>
                        </div>
                        <div>
                            <div class="gm-stat-number">{{ $data['stats_number2'] ?? '' }}</div>
                            <div class="gm-stat-label">{{ $data['stats_label2'] ?? '' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Stat 3 -->
                <div class="col-lg-3 col-sm-6">
                    <div class="gm-stat-item-box">
                        <div class="gm-stat-icon-circle">
                            <iconify-icon icon="solar:crown-line-bold"></iconify-icon>
                        </div>
                        <div>
                            <div class="gm-stat-number">{{ $data['stats_number3'] ?? '' }}</div>
                            <div class="gm-stat-label">{{ $data['stats_label3'] ?? '' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Stat 4 -->
                <div class="col-lg-3 col-sm-6">
                    <div class="gm-stat-item-box">
                        <div class="gm-stat-icon-circle">
                            <iconify-icon icon="solar:map-point-wave-bold"></iconify-icon>
                        </div>
                        <div>
                            <div class="gm-stat-number">{{ $data['stats_number4'] ?? '' }}</div>
                            <div class="gm-stat-label">{{ $data['stats_label4'] ?? '' }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ==========================================================================
       9. ISLAMIC MOBILE MATRIMONY SHOWCASE
       ========================================================================== -->
    <section class="gm-app-section">
        <div class="gm-app-arabesque-overlay"></div>
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center g-4 g-lg-5">

                <div class="col-lg-6">
                    <div class="gm-app-content-wrap">
                        <span class="gm-app-halal-badge mb-3">
                            <iconify-icon icon="solar:shield-star-bold" class="text-accent"></iconify-icon>
                            <span>{{ $data['app_badge_text'] ?? '' }}</span>
                        </span>

                        <h2 class="gm-app-title font-heading mt-2">
                            {!! $data['app_title'] ?? '' !!}
                        </h2>

                        <p class="gm-app-desc mt-3">
                            {!! $data['app_desc'] ?? '' !!}
                        </p>

                        <!-- Key Halal Features Checklist -->
                        <div class="gm-app-checklist mb-4">
                            <div class="gm-check-item">
                                <iconify-icon icon="solar:check-circle-bold" class="text-accent"></iconify-icon>
                                <span>{{ $data['app_check1'] ?? '' }}</span>
                            </div>
                            <div class="gm-check-item">
                                <iconify-icon icon="solar:check-circle-bold" class="text-accent"></iconify-icon>
                                <span>{{ $data['app_check2'] ?? '' }}</span>
                            </div>
                            <div class="gm-check-item">
                                <iconify-icon icon="solar:check-circle-bold" class="text-accent"></iconify-icon>
                                <span>{{ $data['app_check3'] ?? '' }}</span>
                            </div>
                        </div>

                        <!-- Download Buttons -->
                        <div class="d-flex flex-wrap gap-3 mt-4">
                            @if (isset($configArr['ios_app_link']) && !blank($configArr['ios_app_link']))
                                <a target="_blank" href="{{ $configArr['ios_app_link'] }}" class="gm-app-badge-btn"
                                    aria-label="{{ __('messages.lbl_app_store') }}">
                                    <iconify-icon icon="bi:apple" class="gm-app-badge-icon"></iconify-icon>
                                    <div class="text-start">
                                        <div class="fts-10 text-uppercase letter-spacing-1">
                                            {{ __('messages.lbl_download_on_the') }}</div>
                                        <div class="fw-7 fts-16">{{ __('messages.lbl_app_store') }}</div>
                                    </div>
                                </a>
                            @endif
                            @if (isset($configArr['android_app_link']) && !blank($configArr['android_app_link']))
                                <a target="_blank" href="{{ $configArr['android_app_link'] }}"
                                    class="gm-app-badge-btn" aria-label="Get it on Google Play">
                                    <iconify-icon icon="bi:google-play" class="gm-app-badge-icon"></iconify-icon>
                                    <div class="text-start">
                                        <div class="fts-10 text-uppercase letter-spacing-1">
                                            {{ __('messages.lbl_get_it_on') }}</div>
                                        <div class="fw-7 fts-16">{{ __('messages.lbl_play_store') }}</div>
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 text-center position-relative">
                    <div class="gm-app-mockup-stage">
                        @php
                            $appMockup = !empty($data['app_image'])
                                ? _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['app_image']
                                : asset('storage/web/home5') . '/assets/images/app-bg-mockup.png';
                        @endphp
                        <img src="{{ $appMockup }}" alt="Muslim Matrimonial Mobile App"
                            class="gm-app-mockup-img">

                        <!-- Floating Top Wali Badge -->
                        <div class="gm-app-floating-pill-top">
                            <iconify-icon icon="solar:verified-check-bold" class="text-accent fts-18"></iconify-icon>
                            <span>{{ $data['app_image_badge'] ?? '' }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- ==========================================================================
       11. SACRED COMMUNITIES DIRECTORY (INSPIRED BY MOCKUP, OUR COLOR THEME)
       ========================================================================== -->
    <section class="gm-community-directory-section py-5">
        <div class="container">

            <!-- Header Section -->
            <div class="text-center mx-auto mb-4 mb-lg-5" style="max-width: 820px;">
                <div class="gm-explore-badge mb-3 wow fadeInUp" data-wow-duration="0.6s">
                    <iconify-icon icon="solar:verified-check-bold"></iconify-icon>
                    <span>{{ $data['directory_badge_text'] ?? '' }}</span>
                </div>
                <h2 class="gm-section-title mb-2 wow fadeInUp" data-wow-duration="0.7s">
                    {!! $data['directory_title'] ?? '' !!}
                </h2>
                <p class="gm-section-subtitle mb-0 wow fadeInUp" data-wow-duration="0.8s">
                    {!! $data['directory_subtitle'] ?? '' !!}
                </p>
            </div>

            <!-- Navigation Tabs Pill Bar -->
            <div class="gm-comm-tabs-wrap d-flex justify-content-center mb-4 mb-lg-5 wow fadeInUp"
                data-wow-duration="0.7s">

                <div class="gm-comm-tabs-nav" role="tablist">
                    @foreach ($matrimonyPagesData as $type => $items)
                        @php
                            $tabId = Str::slug($type);
                        @endphp

                        <button type="button" class="gm-comm-tab-btn {{ $loop->first ? 'active' : '' }}"
                            data-tab="{{ $tabId }}">

                            <iconify-icon icon="{{ $items['icon'] ?? 'solar:users-group-two-rounded-bold' }}">
                            </iconify-icon>

                            <span>{{ $items['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Main Directory Card -->
            <div class="gm-comm-main-card mb-4 wow fadeInUp" data-wow-duration="0.8s">
                @foreach ($matrimonyPagesData as $type => $items)
                    @php
                        $tabId = Str::slug($type);
                    @endphp
                    <div class="gm-comm-tab-content {{ $loop->first ? 'active' : 'd-none' }}"
                        id="gmCommContent-{{ $tabId }}">
                        <!-- Header -->
                        <div class="gm-comm-card-header">
                            <div class="gm-comm-header-icon">
                                <iconify-icon icon="{{ $items['icon'] ?? 'solar:users-group-two-rounded-bold' }}">
                                </iconify-icon>
                            </div>
                            <div class="gm-comm-header-content">
                                <h3 class="gm-comm-card-title">
                                    {{ $items['title'] ?? $items['label'] }}
                                </h3>
                                @if (!empty($items['sub']))
                                    <p class="gm-comm-card-sub">
                                        {{ $items['sub'] }}
                                    </p>
                                @endif
                            </div>
                            @if (!empty($items['badge']))
                                <div class="gm-comm-badge">
                                    <span>{{ $items['badge'] }}</span>
                                </div>
                            @endif
                        </div>
                        <!-- Chips -->
                        <div class="gm-comm-chips-container mt-3">
                            @foreach ($items['items'] as $item)
                                <a href="{{ route('web.matrimony.index', $item['slug']) }}" class="gm-comm-chip">
                                    <span>
                                        {{ $item['matrimony_name'] ?: $item['matrimony_name_old'] ?? 'N/A' }}
                                    </span>
                                    @if (!empty($item['count']))
                                        <span class="chip-count">
                                            {{ $item['count'] }}
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                            <!-- More Details -->
                            <a href="{{ route('web.matrimony.moreDetails', Str::slug($type)) }}"
                                class="gm-comm-chip gm-comm-chip-all">
                                <span>
                                    {{ __('messages.lbl_more_details') }} &rarr;
                                </span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Secondary Callout Banner Card -->
            <div class="gm-comm-callout-card wow fadeInUp" data-wow-duration="0.9s">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8 d-flex align-items-center gap-3">
                        <div class="gm-callout-icon-box">
                            <iconify-icon icon="solar:tuning-4-bold"></iconify-icon>
                        </div>
                        <div>
                            <h4 class="gm-callout-title mb-1">{{ $data['directory_callout_title'] ?? '' }}</h4>
                            <p class="gm-callout-desc mb-0">{{ $data['directory_callout_desc'] ?? '' }}</p>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="#" class="btn gm-btn-callout">
                            <span>{{ __('messages.lbl_try_advanced_halal_search') }}</span>
                            <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ==========================================================================
       12. ISLAMIC SANCTUARY FOOTER (INSPIRED BY MOCKUP, OUR COLOR THEME)
       ========================================================================== -->
    <footer id="contact-footer" class="gm-footer wow fadeIn" data-wow-duration="1s">
        <div class="container">

            <!-- Pre-Footer: Auspicious Matches / Halal Updates Newsletter Card -->
            <div class="gm-footer-newsletter-card mb-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6 col-md-12 d-flex align-items-center gap-3">
                        <div class="gm-footer-newsletter-icon">
                            <iconify-icon icon="solar:letter-bold"></iconify-icon>
                        </div>
                        <div>
                            <h3 class="gm-fnl-title text-white mb-1">
                                {{ $data['footer_newsletter_title'] ?? '' }}
                            </h3>
                            <p class="gm-fnl-sub mb-0">
                                {{ $data['footer_newsletter_sub'] ?? '' }}
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-12">
                        <form class="gm-fnl-form d-flex align-items-center gap-2"
                            action="{{ route('web.newsletter.subscribe') }}">
                            @csrf
                            <div class="gm-fnl-input-wrapper flex-grow-1 position-relative">
                                <iconify-icon icon="solar:letter-linear" class="gm-fnl-field-icon"></iconify-icon>
                                <input type="email" name="email" class="form-control gm-fnl-input"
                                    placeholder="{{ __('messages.field_lbl_enter_your_email_id') }}" required>
                            </div>
                            <button type="submit" class="btn gm-btn-fnl-submit">
                                <span class="btn-text">{{ __('messages.lbl_subscribe_free') }}</span>
                                <span class="btn-loader"></span>
                                <iconify-icon icon="solar:plain-bold"></iconify-icon>
                            </button>
                        </form>

                        <div class="gm-fnl-message" style="display: none;"></div>
                    </div>
                </div>
            </div>

            <!-- Main Footer Grid (5 Columns) -->
            <div class="row g-4 g-lg-5 pb-5">

                <!-- Column 1: Brand Info -->
                <div class="col-lg-3 col-md-12">
                    <a href="{{ url('/') }}" class="gm-brand-logo mb-3 d-inline-flex">
                        <div class="d-flex flex-column">
                            <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                                alt="{{ $configArr['web_name'] }} logo" class="brand-logo">
                        </div>
                    </a>
                    <p class="gm-footer-brand-text mb-0">
                        {{ $data['footer_desc'] ?? '' }}
                    </p>
                </div>

                <!-- Column 2: Help & support -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h4 class="gm-fcol-title">{{ __('messages.lbl_help_support') }}</h4>
                    <ul class="gm-fcol-links">
                        <li><a href="{{ route('web.contactUs.index') }}">{{ __('messages.lbl_contact_us') }}</a>
                        </li>
                        <li><a href="{{ route('web.faq.index') }}">{{ __('messages.lbl_faqs') }}</a></li>
                        <li><a
                                href="{{ route('web.successStory.index') }}">{{ __('messages.lbl_success_stories') }}</a>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: Information -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h4 class="gm-fcol-title">{{ __('messages.lbl_information') }}</h4>
                    <ul class="gm-fcol-links">
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
                    <h4 class="gm-fcol-title">{{ __('messages.lbl_others') }}</h4>
                    <ul class="gm-fcol-links">
                        @guest('web')
                            <li><a href="{{ route('web.register.index') }}">{{ __('messages.lbl_register') }}</a></li>
                            <li><a href="{{ route('web.login.index') }}">{{ __('messages.lbl_login') }}</a></li>
                        @endguest
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
                <div class="col-lg-3 col-md-3 col-12">
                    <h4 class="gm-fcol-title">{{ __('messages.lbl_contact_info') }}</h4>
                    <div class="gm-fcontact-wrap mt-2">
                        <span
                            class="d-block fts-11 text-uppercase text-white-50 fw-6">{{ __('messages.lbl_phone_number') }}</span>
                        <a href="tel:{{ $configArr['contact_no'] }}"
                            class="fts-15 fw-7 text-white text-decoration-none d-flex align-items-center gap-2 mt-1">
                            <i class="bx bx-phone-call text-accent"></i>
                            <span>{{ $configArr['contact_no'] }}</span>
                        </a>
                        <span
                            class="d-block fts-11 text-uppercase text-white-50 fw-6">{{ __('messages.field_lbl_email_id') }}</span>
                        <a href="tel:{{ $configArr['contact_email'] }}"
                            class="fts-15 fw-7 text-white text-decoration-none d-flex align-items-center gap-2 mt-1">
                            <i class="bx bx-envelope text-accent"></i>
                            <span>{{ $configArr['contact_email'] }}</span>
                        </a>

                        <!-- Socials -->
                        <div class="gm-fsocial-icons d-flex align-items-center gap-2 mt-4">
                            @if (!empty($configArr['instagram_link']))
                                <a href="{{ $configArr['instagram_link'] }}" target="_blank"
                                    rel="noopener noreferrer" aria-label="instagram"><i
                                        class="bx bxl-instagram"></i></a>
                            @endif
                            @if (!empty($configArr['facebook_link']))
                                <a href="{{ $configArr['facebook_link'] }}" target="_blank"
                                    rel="noopener noreferrer" aria-label="facebook"><i
                                        class="bx bxl-facebook"></i></a>
                            @endif
                            @if (!empty($configArr['youtube_link']))
                                <a href="{{ $configArr['youtube_link'] }}" target="_blank"
                                    rel="noopener noreferrer" aria-label="YouTube">
                                    <i class="bx bxl-youtube"></i>
                                </a>
                            @endif
                            @if (!empty($configArr['twitter_link']))
                                <a href="{{ $configArr['twitter_link'] }}" target="_blank"
                                    rel="noopener noreferrer" aria-label="twitter"><i class="bx bxl-twitter"></i></a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- Bottom Copyright & Tagline Bar -->
        <div class="gm-footer-bottom-bar">
            <div class="container">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <p class="mb-0 fts-13 text-white-50">
                        {{ $configArr['footer_text'] }}
                    </p>
                    <div class="fts-13 text-white-50">
                        {{ $data['footer_bottom_tagline'] ?? '' }}
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll Progress / Back to Top -->
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

    <!-- JavaScript Libraries & Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('storage/web/home5/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('storage/web/home5/assets/js/wow.js') }}"></script>
    <script src="{{ asset('storage/web/home5/assets/js/slick.min.js') }}"></script>
    <script src="{{ asset('storage/web/home5/assets/js/main.js') }}"></script>
    <script src="{{ asset('storage/web/home5/assets/js/animations-extra.js') }}"></script>

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
            const $form = $('.gm-fnl-form');
            if (!$form.length) return;

            const $messageBox = $('.gm-fnl-message');
            const $submitBtn = $form.find('.gm-btn-fnl-submit');
            const $emailInput = $form.find('.gm-fnl-input');

            let hideTimer = null;

            function showMessage(text, type) {
                clearTimeout(hideTimer);

                $messageBox
                    .text(text)
                    .removeClass('success error')
                    .addClass(type)
                    .stop(true, true)
                    .fadeIn(200);

                hideTimer = setTimeout(function() {
                    $messageBox.fadeOut(400, function() {
                        $(this).removeClass('success error').text('');
                    });
                }, 2000);
            }

            $form.on('submit', function(e) {
                e.preventDefault();

                const formData = $form.serialize();

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
</body>

</html>

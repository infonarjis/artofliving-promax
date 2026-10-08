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
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Raleway:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet">

    <!-- all css file include -->
    <link rel="stylesheet" href="{{ asset('storage/web/home4/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home4/assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home4/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home4/assets/css/slick.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('storage/web/home4/assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home4/assets/css/responsive.css') }}">
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>

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
        // Banner :
        $headerSectionBanner = !empty($data['hero_main_background_banner'])
            ? _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['hero_main_background_banner']
            : asset('storage/web/home4') . '/assets/images/hero-bg-vivahsutra.jpg';
    @endphp
    <div class="main-header-bg" style="background-image: url('{{ $headerSectionBanner }}');">
        @php
            $getActiveLanguage = _getActiveLanguage();
            $currentLanguage = App::getLocale();
            $currentLang = collect($getActiveLanguage)->firstWhere('lang_code', $currentLanguage);
            $currentLangCode = $currentLang->lang_code ?? 'en';
        @endphp

        <!-- Navbar Section Start -->
        <nav aria-label="navbar" class="navbar-pro-matrimony py-3">
            <div class="container-fluid px-0 px-lg-5">
                <div class="navbar-pro-inner d-flex align-items-center justify-content-between">

                    {{-- Logo --}}
                    <a href="{{ url('/') }}" class="vivah-brand-link d-inline-flex align-items-center">
                        <div class="vivah-logo-component">
                            <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                                alt="{{ $configArr['web_name'] }} logo" class="brand-logo">
                        </div>
                    </a>

                    {{-- Mobile Toggle --}}
                    <button class="navbar-toggler d-lg-none" type="button" aria-label="Toggle navigation">
                        <iconify-icon icon="solar:hamburger-menu-linear"></iconify-icon>
                    </button>

                    {{-- Desktop Navigation --}}
                    <ul class="navbar-desktop-nav d-none d-lg-flex align-items-center gap-2 mb-0 list-unstyled">
                        <li class="active">
                            <a href="{{ url('/') }}" class="nav-item active">
                                <iconify-icon icon="fluent:grid-16-filled" class="nav-icon"></iconify-icon>
                                <span>{{ __('messages.lbl_home') }}</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('web.search.type', ['type' => 'quick-search']) }}" class="nav-item">
                                <iconify-icon icon="bx:search" class="nav-icon"></iconify-icon>
                                <span>{{ __('messages.lbl_search') }}</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('web.membershipPlan.index') }}" class="nav-item">
                                <iconify-icon icon="mdi:crown-outline" class="nav-icon"></iconify-icon>
                                <span>{{ __('messages.lbl_membership') }}</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('web.successStory.index') }}" class="nav-item">
                                <iconify-icon icon="mdi:heart-outline" class="nav-icon"></iconify-icon>
                                <span>{{ __('messages.lbl_success_stories') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('web.contactUs.index') }}" class="nav-item">
                                <iconify-icon icon="ph:chats-circle-bold" class="nav-icon"></iconify-icon>
                                <span>{{ __('messages.lbl_contact_us') }}</span>
                            </a>
                        </li>
                    </ul>

                    {{-- Login, Register and Language --}}
                    <div class="navbar-desktop-actions d-none d-lg-flex align-items-center gap-2">

                        <a href="{{ route('web.login.index') }}" class="btn-vivah-login">
                            <iconify-icon icon="ph:sign-in-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_login') }}</span>
                            <iconify-icon icon="ph:arrow-right-bold"></iconify-icon>
                        </a>

                        <a href="{{ route('web.register.index') }}" class="btn-vivah-register">
                            <iconify-icon icon="ph:user-plus-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_register') }}</span>
                            <iconify-icon icon="ph:arrow-right-bold"></iconify-icon>
                        </a>

                        {{-- Language Dropdown --}}
                        <div class="vivah-language-wrapper" id="vivah-language-wrapper">
                            <button type="button" class="vivah-language-btn" id="vivah-language-toggle"
                                aria-label="{{ __('messages.lbl_change_language') }}" aria-expanded="false"
                                aria-haspopup="true">

                                <iconify-icon icon="ph:translate-bold"></iconify-icon>

                                <span>{{ strtoupper($currentLangCode) }}</span>

                                <iconify-icon icon="ph:caret-down-bold" class="vivah-language-caret"></iconify-icon>
                            </button>

                            <div class="vivah-language-dropdown" id="vivah-language-dropdown" role="menu">

                                <div class="vivah-language-heading">
                                    <div>
                                        <div class="vivah-language-title">
                                            {{ __('messages.lbl_select_language') }}
                                        </div>
                                        <div class="vivah-language-subtitle">
                                            {{ __('messages.lbl_choose_display_language') }}
                                        </div>
                                    </div>

                                    <span class="vivah-language-count">
                                        {{ count($getActiveLanguage) }}
                                        {{ __('messages.lbl_languages') }}
                                    </span>
                                </div>

                                <div class="vivah-language-list">
                                    @foreach ($getActiveLanguage as $value)
                                        <a href="{{ route('language.change', $value->lang_code) }}"
                                            class="vivah-language-option {{ $value->lang_code == $currentLanguage ? 'active' : '' }}"
                                            role="menuitem">

                                            <span class="vivah-language-option-icon">
                                                <iconify-icon icon="akar-icons:language"></iconify-icon>
                                            </span>

                                            <span class="vivah-language-option-text">
                                                <span>{{ $value->lang_name }}</span>
                                                <small>{{ strtoupper($value->lang_code) }}</small>
                                            </span>

                                            @if ($value->lang_code == $currentLanguage)
                                                <iconify-icon icon="ph:check-circle-fill"
                                                    class="vivah-language-check"></iconify-icon>
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
        <!-- Navbar Section End -->

        <!-- VivahSutra Mobile Drawer & Backdrop Overlay -->
        <div class="vivah-drawer-overlay"></div>
        <aside class="vivah-mobile-drawer" id="vivahMobileDrawer">
            <!-- Drawer Header with Consistent Brand Logo & Theme Close Button -->
            <div class="vivah-drawer-header">
                <a href="{{ url('/') }}" class="vivah-drawer-brand">
                    <div class="vivah-logo-component">
                        <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                            alt="{{ $configArr['web_name'] }} logo" class="brand-logo">
                    </div>
                </a>
                <button type="button" class="vivah-drawer-close" aria-label="Close menu">
                    <i class='bx bx-x'></i>
                </button>
            </div>

            <!-- Drawer Body / Navigation -->
            <div class="vivah-drawer-body">
                <ul class="vivah-drawer-menu">
                    <li class="active">
                        <a href="{{ url('/') }}" class="vivah-drawer-link active">
                            <span class="drawer-link-content">
                                <span class="drawer-link-icon"><i class='bx bx-home-alt'></i></span>
                                <span class="drawer-link-text">{{ __('messages.lbl_home') }}</span>
                            </span>
                            <i class='bx bx-chevron-right drawer-chevron'></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('web.search.type', ['type' => 'quick-search']) }}"
                            class="vivah-drawer-link">
                            <span class="drawer-link-content">
                                <span class="drawer-link-icon"><i class='bx bx-search-alt'></i></span>
                                <span class="drawer-link-text">{{ __('messages.lbl_search') }}</span>
                            </span>
                            <i class='bx bx-chevron-right drawer-chevron'></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('web.membershipPlan.index') }}" class="vivah-drawer-link">
                            <span class="drawer-link-content">
                                <span class="drawer-link-icon"><i class='bx bx-crown'></i></span>
                                <span class="drawer-link-text">{{ __('messages.lbl_membership') }}</span>
                            </span>
                            <i class='bx bx-chevron-right drawer-chevron'></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('web.successStory.index') }}" class="vivah-drawer-link">
                            <span class="drawer-link-content">
                                <span class="drawer-link-icon"><i class='bx bx-heart'></i></span>
                                <span class="drawer-link-text">{{ __('messages.lbl_success_stories') }}</span>
                            </span>
                            <i class='bx bx-chevron-right drawer-chevron'></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('web.contactUs.index') }}" class="vivah-drawer-link">
                            <span class="drawer-link-content">
                                <span class="drawer-link-icon"><iconify-icon
                                        icon="ph:chats-circle-bold"></iconify-icon></span>
                                <span class="drawer-link-text">{{ __('messages.lbl_contact_us') }}ss</span>
                            </span>
                            <i class='bx bx-chevron-right drawer-chevron'></i>
                        </a>
                    </li>
                </ul>

                <!-- Action Buttons in Drawer: Login & Register -->
                <div class="vivah-drawer-actions">
                    <a href="{{ route('web.login.index') }}" class="btn-drawer-login">
                        <i class='bx bx-user'></i>
                        <span>{{ __('messages.lbl_login') }}</span>
                        <i class='bx bx-right-arrow-alt ms-auto'></i>
                    </a>
                    <a href="{{ route('web.register.index') }}" class="btn-drawer-register">
                        <i class='bx bx-user-plus'></i>
                        <span>{{ __('messages.lbl_register') }}</span>
                        <i class='bx bx-right-arrow-alt ms-auto'></i>
                    </a>
                </div>
            </div>

            <!-- Drawer Footer with Theme Stamp -->
            <div class="vivah-drawer-footer">
                <div class="drawer-trust-pill">
                    <span class="trust-om"><i class="bx bx-church" style="color:var(--primary-color)"></i></span>
                    <div class="trust-info">
                        <span class="trust-heading">{{ $data['brand_trust_heading'] ?? '' }}</span>
                        <span class="trust-sub">{{ $data['brand_trust_sub'] ?? '' }}</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- hero center section start -->
        <header class="vivah-hero-section">
            <div class="container">
                <div class="vivah-hero-content text-center">

                    <!-- Om Ornament with flourishing lines -->
                    <div class="vivah-om-ornament">
                        <span class="flourish-stem left">
                            <svg width="64" height="12" viewBox="0 0 64 12" fill="none">
                                <line x1="0" y1="6" x2="54" y2="6"
                                    stroke="var(--black-color-5)" stroke-width="1.2" />
                                <circle cx="58" cy="6" r="2.5" fill="var(--black-color-5)" />
                            </svg>
                        </span>
                        <span class="om-glyph" style="color:var(--primary-color)"><i class="bx bx-church"
                                style="color:var(--primary-color)"></i></span>
                        <span class="flourish-stem right">
                            <svg width="64" height="12" viewBox="0 0 64 12" fill="none">
                                <circle cx="6" cy="6" r="2.5" fill="var(--black-color-5)" />
                                <line x1="10" y1="6" x2="64" y2="6"
                                    stroke="var(--black-color-5)" stroke-width="1.2" />
                            </svg>
                        </span>
                    </div>

                    <!-- Category Sub-tag -->
                    <div class="vivah-tagline">{{ $data['hero_tagline'] ?? '' }}</div>

                    <!-- Main Hero Headline -->
                    <h1 class="vivah-main-heading">
                        <span class="heading-serif-top">{{ $data['hero_title_top'] ?? '' }}</span>
                        <span class="heading-serif-accent">{{ $data['hero_title_accent'] ?? '' }}</span>
                    </h1>

                    <!-- Hero Subtitle -->
                    <p class="vivah-hero-subtext">
                        {!! $data['hero_subtitle'] ?? '' !!}
                    </p>

                    <!-- 3 Trust Badges with Dividers -->
                    <div class="vivah-trust-badges-wrapper">
                        <div class="vivah-trust-badge">
                            <div class="badge-icon-box">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                    stroke="var(--primary-color)" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    <path
                                        d="M12 8c-.6-.7-1.5-.9-2.1-.3-.6.6-.5 1.6 0 2.2l2.1 2.1 2.1-2.1c.5-.6.6-1.6 0-2.2-.6-.6-1.5-.4-2.1.3z" />
                                </svg>
                            </div>
                            <div class="badge-text-box">
                                <span class="badge-line-1">{{ $data['hero_trust1_line1'] ?? '' }}</span>
                                <span class="badge-line-2">{{ $data['hero_trust1_line2'] ?? '' }}</span>
                            </div>
                        </div>

                        <div class="vivah-badge-sep"></div>

                        <div class="vivah-trust-badge">
                            <div class="badge-icon-box">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                    stroke="var(--primary-color)" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="8.5" cy="7" r="4" />
                                    <path d="M18 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                                    <path d="M22 21v-1.5a3.5 3.5 0 0 0-3-3.4" />
                                </svg>
                            </div>
                            <div class="badge-text-box">
                                <span class="badge-line-1">{{ $data['hero_trust2_line1'] ?? '' }}</span>
                                <span class="badge-line-2">{{ $data['hero_trust2_line2'] ?? '' }}</span>
                            </div>
                        </div>

                        <div class="vivah-badge-sep"></div>

                        <div class="vivah-trust-badge">
                            <div class="badge-icon-box">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                    stroke="var(--primary-color)" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                </svg>
                            </div>
                            <div class="badge-text-box">
                                <span class="badge-line-1">{{ $data['hero_trust3_line1'] ?? '' }}</span>
                                <span class="badge-line-2">{{ $data['hero_trust3_line2'] ?? '' }}</span>
                            </div>
                        </div>

                        <div class="vivah-badge-sep"></div>


                    </div>

                    <!-- Decorative Filigree Divider -->
                    <div class="vivah-filigree-divider">
                        <svg width="130" height="18" viewBox="0 0 130 18" fill="none">
                            <line x1="0" y1="9" x2="44" y2="9"
                                stroke="var(--black-color-5)" stroke-width="1" opacity="0.6" />
                            <circle cx="48" cy="9" r="2" fill="var(--black-color-5)" />
                            <path d="M65 2 L70 9 L65 16 L60 9 Z" fill="var(--black-color-5)" />
                            <circle cx="65" cy="9" r="1.5" fill="#faf4eb" />
                            <path d="M57 9 Q61 6 65 6 Q69 6 73 9 Q69 12 65 12 Q61 12 57 9 Z"
                                stroke="var(--black-color-5)" stroke-width="0.8" fill="none" />
                            <circle cx="82" cy="9" r="2" fill="var(--black-color-5)" />
                            <line x1="86" y1="9" x2="130" y2="9"
                                stroke="var(--black-color-5)" stroke-width="1" opacity="0.6" />
                        </svg>
                    </div>

                    <!-- Search Form Card -->
                    <div class="vivah-search-card">
                        <form action="{{ route('web.search.searchResult') }}" method="GET"
                            class="vivah-search-form">
                            <!-- Field 1: Looking for -->
                            <div class="vivah-search-cell">
                                <div class="cell-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                        stroke="var(--primary-color)" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                </div>
                                <div class="cell-content">
                                    <span class="cell-caption">{{ __('messages.lbl_i_m_looking_for_a') }}</span>
                                    <select name="gender" id="gender" class="vivah-select2 select2-looking">
                                        <option value="Female" title="Bride">{{ __('messages.field_lbl_female') }}
                                        </option>
                                        <option value="Male" title="Groom">{{ __('messages.field_lbl_male') }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="cell-vdivider"></div>

                            <!-- Field 2: Age -->
                            <div class="vivah-search-cell">
                                <div class="cell-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                        stroke="var(--primary-color)" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2"
                                            ry="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                </div>
                                <div class="cell-content">
                                    <span class="cell-caption">{{ __('messages.field_lbl_age_range') }}</span>
                                    @php $age = _ageRang(); @endphp
                                    <div class="d-flex align-items-center gap-1">
                                        <select name="part_frm_age" class="vivah-select2 select2-age">
                                            @foreach ($age as $key => $valueArr)
                                                <option value="{{ $key }}">{{ $valueArr }}</option>
                                            @endforeach
                                        </select>
                                        <span class="small">{{ __('messages.field_lbl_to') }}</span>
                                        <select name="part_to_age" class="vivah-select2 select2-age">
                                            @foreach ($age as $key => $valueArr)
                                                @php $selected = ($key == '30') ? 'selected' : ''; @endphp
                                                <option value="{{ $key }}" {{ $selected }}>
                                                    {{ $valueArr }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="cell-vdivider"></div>

                            <!-- Field 3: Religion / Country -->
                            <div class="vivah-search-cell">
                                <div class="cell-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                        stroke="var(--primary-color)" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                </div>
                                <div class="cell-content">
                                    <span class="cell-caption">{{ __('messages.field_lbl_country') }}</span>
                                    <select name="country_id" class="vivah-select2 select2-country">
                                        <option value="" selected>{{ __('messages.field_lbl_select_country') }}
                                        </option>
                                        @foreach ($religionList as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="vivah-search-action-btn">
                                <i class='bx bx-search'></i>
                                <span>{{ __('messages.lbl_search') }}</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </header>
    </div>

    <!-- How Does It Work section start (VivahSutra Traditional Sacred Path) -->
    <section class="vivah-how-section py-5 position-relative">
        <div class="container position-relative" style="z-index: 2;">
            <!-- Section Header -->
            <div class="vivah-section-header text-center mb-4 mb-lg-5">
                <!-- Auspicious Tag / Om ornament -->
                <div class="vivah-om-ornament mb-2">
                    <span class="flourish-stem left">
                        <svg width="48" height="10" viewBox="0 0 48 10" fill="none">
                            <line x1="0" y1="5" x2="38" y2="5"
                                stroke="var(--black-color-5)" stroke-width="1" />
                            <circle cx="42" cy="5" r="2" fill="var(--black-color-5)" />
                        </svg>
                    </span>
                    <span class="om-glyph" style="font-size: 20px;"><i class="bx bx-church"
                            style="color:var(--primary-color)"></i></span>
                    <span class="flourish-stem right">
                        <svg width="48" height="10" viewBox="0 0 48 10" fill="none">
                            <circle cx="6" cy="5" r="2" fill="var(--black-color-5)" />
                            <line x1="10" y1="5" x2="48" y2="5"
                                stroke="var(--black-color-5)" stroke-width="1" />
                        </svg>
                    </span>
                </div>

                <div class="vivah-section-tagline">{{ $data['journey_tagline'] ?? '' }}</div>

                <h2 class="vivah-section-heading">
                    {!! $data['journey_title'] ?? '' !!}
                </h2>

                <p class="vivah-section-subtext">
                    {!! $data['journey_subtitle'] ?? '' !!}
                </p>

                <!-- Filigree Divider -->
                <div class="vivah-filigree-divider mt-2 mb-0">
                    <svg width="100" height="14" viewBox="0 0 100 14" fill="none">
                        <line x1="0" y1="7" x2="36" y2="7"
                            stroke="var(--black-color-5)" stroke-width="1" opacity="0.6" />
                        <circle cx="40" cy="7" r="2" fill="var(--black-color-5)" />
                        <path d="M50 2 L54 7 L50 12 L46 7 Z" fill="var(--black-color-5)" />
                        <circle cx="60" cy="7" r="2" fill="var(--black-color-5)" />
                        <line x1="64" y1="7" x2="100" y2="7"
                            stroke="var(--black-color-5)" stroke-width="1" opacity="0.6" />
                    </svg>
                </div>
            </div>

            <!-- Step Cards with Connecting Ribbon Trail -->
            <div class="col-xxl-11 col-xl-12 mx-auto">
                <div class="vivah-steps-wrapper position-relative">

                    <!-- Dotted Gold Connecting Path (Desktop only) -->
                    <div class="vivah-steps-path d-none d-lg-block">
                        <svg class="steps-connector-svg" width="100%" height="40" viewBox="0 0 800 40"
                            fill="none" preserveAspectRatio="none">
                            <path d="M120 20 C 260 20, 280 20, 400 20 C 520 20, 540 20, 680 20" stroke="#c48834"
                                stroke-width="2" stroke-dasharray="6 6" opacity="0.65" />
                        </svg>
                    </div>

                    <div class="row g-4 justify-content-center">

                        <!-- Step 1: Create Profile (Sacred Kalash / Diya Motif) -->
                        <div class="col-lg-4 col-md-6">
                            <div class="vivah-step-card h-100 text-center">
                                <div class="step-card-decor-corner top-left"></div>
                                <div class="step-card-decor-corner top-right"></div>

                                <!-- Step Number Badge -->
                                <div class="step-badge-pill">
                                    <span class="step-label">{{ $data['journey_step1_heading'] ?? '' }}</span>
                                </div>

                                <!-- Traditional Icon with Animated Aura & Mandala Ring -->
                                <div class="step-icon-wrapper">
                                    <div class="step-mandala-ring">
                                        <svg viewBox="0 0 110 110" class="rotating-mandala" fill="none">
                                            <circle cx="55" cy="55" r="50"
                                                stroke="var(--black-color-5)" stroke-width="1.2"
                                                stroke-dasharray="4 4" opacity="0.6" />
                                            <circle cx="55" cy="55" r="44"
                                                stroke="var(--black-color-5)" stroke-width="0.8" opacity="0.4" />
                                            <circle cx="55" cy="5" r="2.5"
                                                fill="var(--black-color-5)" />
                                            <circle cx="105" cy="55" r="2.5"
                                                fill="var(--black-color-5)" />
                                            <circle cx="55" cy="105" r="2.5"
                                                fill="var(--black-color-5)" />
                                            <circle cx="5" cy="55" r="2.5"
                                                fill="var(--black-color-5)" />
                                            <circle cx="90" cy="20" r="2" fill="#c48834" />
                                            <circle cx="90" cy="90" r="2" fill="#c48834" />
                                            <circle cx="20" cy="90" r="2" fill="#c48834" />
                                            <circle cx="20" cy="20" r="2" fill="#c48834" />
                                        </svg>
                                    </div>
                                    <div class="step-icon-core">
                                        <!-- Traditional Sacred Kalash / Diya SVG Artwork -->
                                        <svg width="44" height="44" viewBox="0 0 44 44" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <!-- Diya Flame -->
                                            <path d="M22 4 C20 9, 18 13, 22 17 C26 13, 24 9, 22 4 Z" fill="#ffd56b" />
                                            <path d="M22 8 C21 11, 20 13, 22 15 C24 13, 23 11, 22 8 Z"
                                                fill="#ffffff" />
                                            <!-- Sacred Kalash Coconut & Mango leaves -->
                                            <path d="M16 16 C12 13, 10 17, 18 19 Z" fill="#e5ad42" />
                                            <path d="M28 16 C32 13, 34 17, 26 19 Z" fill="#e5ad42" />
                                            <circle cx="22" cy="17" r="4.5" fill="#d8962c" />
                                            <!-- Kalash Pot (Lota) -->
                                            <path d="M17 21 L27 21 L29 25 C31 31, 28 36, 22 36 C16 36, 13 31, 15 25 Z"
                                                fill="#f8e7b9" stroke="#b67f2b" stroke-width="1.2" />
                                            <!-- Swastik / Auspicious Mark on Kalash -->
                                            <path
                                                d="M22 26 V31 M20 28 H24 M20 26 H20.5 M23.5 31 H24 M24 26 V26.5 M20 30.5 V31"
                                                stroke="var(--primary-color)" stroke-width="1.1"
                                                stroke-linecap="round" />
                                            <!-- Base of Kalash -->
                                            <path d="M18 36 L26 36 L27 38 L17 38 Z" fill="#c48834" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="step-card-body">
                                    <span class="step-sanskrit-sub">{{ $data['journey_step1_sanskrit'] ?? '' }}</span>
                                    <h3 class="step-card-title">{{ $data['journey_step1_title'] ?? '' }}</h3>
                                    <p class="step-card-desc">
                                        {{ $data['journey_step1_desc'] ?? '' }}
                                    </p>
                                    <div class="step-card-footer-decor">
                                        <span class="mini-flower">❀</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Browse & Match (Astro Kundali Chakra / Sacred Lotus Motif) -->
                        <div class="col-lg-4 col-md-6">
                            <div class="vivah-step-card h-100 text-center featured-step">
                                <div class="step-card-decor-corner top-left"></div>
                                <div class="step-card-decor-corner top-right"></div>

                                <!-- Step Number Badge -->
                                <div class="step-badge-pill">
                                    <span class="step-label">{{ $data['journey_step2_heading'] ?? '' }}</span>
                                </div>

                                <!-- Traditional Icon with Animated Aura & Mandala Ring -->
                                <div class="step-icon-wrapper">
                                    <div class="step-mandala-ring">
                                        <svg viewBox="0 0 110 110" class="rotating-mandala" fill="none">
                                            <circle cx="55" cy="55" r="50"
                                                stroke="var(--black-color-5)" stroke-width="1.2"
                                                stroke-dasharray="4 4" opacity="0.6" />
                                            <circle cx="55" cy="55" r="44"
                                                stroke="var(--black-color-5)" stroke-width="0.8" opacity="0.4" />
                                            <circle cx="55" cy="5" r="2.5"
                                                fill="var(--black-color-5)" />
                                            <circle cx="105" cy="55" r="2.5"
                                                fill="var(--black-color-5)" />
                                            <circle cx="55" cy="105" r="2.5"
                                                fill="var(--black-color-5)" />
                                            <circle cx="5" cy="55" r="2.5"
                                                fill="var(--black-color-5)" />
                                            <circle cx="90" cy="20" r="2" fill="#c48834" />
                                            <circle cx="90" cy="90" r="2" fill="#c48834" />
                                            <circle cx="20" cy="90" r="2" fill="#c48834" />
                                            <circle cx="20" cy="20" r="2" fill="#c48834" />
                                        </svg>
                                    </div>
                                    <div class="step-icon-core">
                                        <!-- Traditional Kundali / Lotus Mandala SVG Artwork -->
                                        <svg width="44" height="44" viewBox="0 0 44 44" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <!-- Vedic Kundali Geometric Diamond / Square -->
                                            <rect x="10" y="10" width="24" height="24"
                                                transform="rotate(45 22 22)" stroke="#ffd56b" stroke-width="1.5"
                                                fill="var(--primary-color)" />
                                            <rect x="14" y="14" width="16" height="16" stroke="#c48834"
                                                stroke-width="1" />
                                            <!-- Golden Astro Cross Lines -->
                                            <line x1="14" y1="14" x2="30" y2="30"
                                                stroke="#ffd56b" stroke-width="1" opacity="0.8" />
                                            <line x1="14" y1="30" x2="30" y2="14"
                                                stroke="#ffd56b" stroke-width="1" opacity="0.8" />
                                            <!-- Central Auspicious Star / Sun -->
                                            <circle cx="22" cy="22" r="3.5" fill="#ffd56b" />
                                            <circle cx="22" cy="22" r="1.5"
                                                fill="var(--primary-color)" />
                                            <circle cx="22" cy="6" r="1.5" fill="#f8e7b9" />
                                            <circle cx="22" cy="38" r="1.5" fill="#f8e7b9" />
                                            <circle cx="6" cy="22" r="1.5" fill="#f8e7b9" />
                                            <circle cx="38" cy="22" r="1.5" fill="#f8e7b9" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="step-card-body">
                                    <span class="step-sanskrit-sub">{{ $data['journey_step2_sanskrit'] ?? '' }}</span>
                                    <h3 class="step-card-title">{{ $data['journey_step2_title'] ?? '' }}</h3>
                                    <p class="step-card-desc">
                                        {{ $data['journey_step2_desc'] ?? '' }}
                                    </p>
                                    <div class="step-card-footer-decor">
                                        <span class="mini-flower">❀</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Connect & Saptapadi (Varmala / Sacred Union Motif) -->
                        <div class="col-lg-4 col-md-6">
                            <div class="vivah-step-card h-100 text-center">
                                <div class="step-card-decor-corner top-left"></div>
                                <div class="step-card-decor-corner top-right"></div>

                                <!-- Step Number Badge -->
                                <div class="step-badge-pill">
                                    <span class="step-label">{{ $data['journey_step3_heading'] ?? '' }}</span>
                                </div>

                                <!-- Traditional Icon with Animated Aura & Mandala Ring -->
                                <div class="step-icon-wrapper">
                                    <div class="step-mandala-ring">
                                        <svg viewBox="0 0 110 110" class="rotating-mandala" fill="none">
                                            <circle cx="55" cy="55" r="50"
                                                stroke="var(--black-color-5)" stroke-width="1.2"
                                                stroke-dasharray="4 4" opacity="0.6" />
                                            <circle cx="55" cy="55" r="44"
                                                stroke="var(--black-color-5)" stroke-width="0.8" opacity="0.4" />
                                            <circle cx="55" cy="5" r="2.5"
                                                fill="var(--black-color-5)" />
                                            <circle cx="105" cy="55" r="2.5"
                                                fill="var(--black-color-5)" />
                                            <circle cx="55" cy="105" r="2.5"
                                                fill="var(--black-color-5)" />
                                            <circle cx="5" cy="55" r="2.5"
                                                fill="var(--black-color-5)" />
                                            <circle cx="90" cy="20" r="2" fill="#c48834" />
                                            <circle cx="90" cy="90" r="2" fill="#c48834" />
                                            <circle cx="20" cy="90" r="2" fill="#c48834" />
                                            <circle cx="20" cy="20" r="2" fill="#c48834" />
                                        </svg>
                                    </div>
                                    <div class="step-icon-core">
                                        <!-- Traditional Varmala / Sacred Union Garland & Heart SVG Artwork -->
                                        <svg width="44" height="44" viewBox="0 0 44 44" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <!-- Sacred Varmala Garland Arc with Marigold & Rose Beads -->
                                            <path d="M10 12 C10 28, 22 36, 22 36 C22 36, 34 28, 34 12" stroke="#e5ad42"
                                                stroke-width="2.5" stroke-linecap="round" stroke-dasharray="1 3.5" />
                                            <!-- Central Golden Mangalsutra / Pendant -->
                                            <path d="M22 34 L20 38 L22 41 L24 38 Z" fill="#ffd56b" />
                                            <circle cx="22" cy="35" r="2"
                                                fill="var(--primary-color)" />
                                            <!-- Sacred Heart with Paisley flourish in center -->
                                            <path
                                                d="M22 14 C19 10, 14 11, 14 16 C14 22, 22 26, 22 26 C22 26, 30 22, 30 16 C30 11, 25 10, 22 14 Z"
                                                fill="#ffd56b" />
                                            <circle cx="18" cy="16" r="1.5"
                                                fill="var(--primary-color)" />
                                            <circle cx="26" cy="16" r="1.5"
                                                fill="var(--primary-color)" />
                                            <!-- Decorative Top Rose knots -->
                                            <circle cx="10" cy="12" r="3" fill="#e5ad42" />
                                            <circle cx="34" cy="12" r="3" fill="#e5ad42" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="step-card-body">
                                    <span class="step-sanskrit-sub">{{ $data['journey_step3_sanskrit'] ?? '' }}</span>
                                    <h3 class="step-card-title">{{ $data['journey_step3_title'] ?? '' }}</h3>
                                    <p class="step-card-desc">
                                        {{ $data['journey_step3_desc'] ?? '' }}
                                    </p>
                                    <div class="step-card-footer-decor">
                                        <span class="mini-flower">❀</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Last Added Profiles section start (VivahSutra Sacred Featured Members) -->
    @if ($latestProfile->isNotEmpty())
        <section class="vivah-profiles-section py-5 position-relative">
            <div class="container position-relative" style="z-index: 2;">
                <!-- Section Header -->
                <div class="vivah-section-header text-center mb-4 mb-lg-4">
                    <!-- Auspicious Tag / Om ornament -->
                    <div class="vivah-om-ornament mb-2">
                        <span class="flourish-stem left">
                            <svg width="48" height="10" viewBox="0 0 48 10" fill="none">
                                <line x1="0" y1="5" x2="38" y2="5"
                                    stroke="var(--black-color-5)" stroke-width="1" />
                                <circle cx="42" cy="5" r="2" fill="var(--black-color-5)" />
                            </svg>
                        </span>
                        <span class="om-glyph" style="font-size: 20px;"><i class="bx bx-church"
                                style="color:var(--primary-color)"></i></span>
                        <span class="flourish-stem right">
                            <svg width="48" height="10" viewBox="0 0 48 10" fill="none">
                                <circle cx="6" cy="5" r="2" fill="var(--black-color-5)" />
                                <line x1="10" y1="5" x2="48" y2="5"
                                    stroke="var(--black-color-5)" stroke-width="1" />
                            </svg>
                        </span>
                    </div>

                    <div class="vivah-section-tagline">{{ $data['profiles_tagline'] ?? '' }}</div>

                    <h2 class="vivah-section-heading">
                        {!! $data['profiles_title'] ?? '' !!}
                    </h2>

                    <p class="vivah-section-subtext">
                        {!! $data['profiles_subtitle'] ?? '' !!}
                    </p>

                    <!-- Filigree Divider -->
                    <div class="vivah-filigree-divider mt-2 mb-0">
                        <svg width="100" height="14" viewBox="0 0 100 14" fill="none">
                            <line x1="0" y1="7" x2="36" y2="7"
                                stroke="var(--black-color-5)" stroke-width="1" opacity="0.6" />
                            <circle cx="40" cy="7" r="2" fill="var(--black-color-5)" />
                            <path d="M50 2 L54 7 L50 12 L46 7 Z" fill="var(--black-color-5)" />
                            <circle cx="60" cy="7" r="2" fill="var(--black-color-5)" />
                            <line x1="64" y1="7" x2="100" y2="7"
                                stroke="var(--black-color-5)" stroke-width="1" opacity="0.6" />
                        </svg>
                    </div>
                </div>

                <!-- Profile Cards Slider -->
                <div class="LastProfileSlider vivah-profile-slider position-relative mt-3">
                    @foreach ($latestProfile as $profile)
                        @php
                            $canView = _canViewMemberPhoto($profile, $profile->hasPhotoRequestAccess ?? '');
                            $hasPhoto = _checkPhotoExist($profile);
                            $profileImage = _getMemberProfileImage($profile);
                        @endphp
                        <div class="vivah-profile-slide px-2">
                            <div class="vivah-profile-card">
                                <div class="profile-card-decor-corner top-left"></div>
                                <div class="profile-card-decor-corner top-right"></div>

                                <div class="profile-photo-frame">
                                    <a href="{{ route('web.userProfile.index', _encrypt($profile->id)) }}">
                                        @if (!$canView && $hasPhoto)
                                            <img src="{{ _getProtectedImage($profile->gender) }}"
                                                alt="{{ _profileTitle($profile) }}" class="profile-img">
                                        @else
                                            <img src="{{ $profileImage }}" alt="{{ _profileTitle($profile) }}"
                                                class="profile-img">
                                        @endif
                                    </a>
                                    <div class="profile-photo-overlay"></div>
                                    {{-- <div class="vivah-tier-badge verified">
                                        <i class='bx bxs-badge-check'></i> Verified
                                    </div> --}}
                                </div>

                                <div class="profile-card-details text-center">
                                    <h4 class="profile-name"><a
                                            href="{{ route('web.userProfile.index', _encrypt($profile->id)) }}">{{ _profileTitle($profile) }}</a>
                                    </h4>
                                    <div class="profile-meta-chips">
                                        <span
                                            class="meta-chip">{{ _birthdateDisplay($profile->birthdate, 0) }}</span>
                                        <span class="meta-sep">•</span>
                                        <span class="meta-chip">{{ _displayHeight($profile->height) }}</span>
                                        <span class="meta-sep">•</span>
                                        <span
                                            class="meta-chip">{{ optional($profile->casteData)->translated_name }}</span>
                                    </div>
                                    <div class="profile-sub-tag">
                                        <i class='bx bx-map'></i>{{ _getMemberLocation($profile) }}
                                    </div>
                                    <div class="profile-action-wrapper mt-3">
                                        <a href="{{ route('web.userProfile.index', _encrypt($profile->id)) }}"
                                            class="btn-vivah-connect">
                                            <i class='bx bx-send'></i>
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

    <!-- Success Stories Section (Kalyana Gatha) -->
    @if ($successStoryArr->isNotEmpty())
        <section class="success-stories-section py-5 position-relative">
            <div class="container position-relative" style="z-index: 2;">
                <!-- Section Header -->
                <div class="vivah-section-header text-center mb-4 mb-lg-5">
                    <div class="vivah-om-ornament mb-2">
                        <span class="flourish-stem left">
                            <svg width="48" height="10" viewBox="0 0 48 10" fill="none">
                                <line x1="0" y1="5" x2="38" y2="5"
                                    stroke="var(--black-color-5)" stroke-width="1" />
                                <circle cx="42" cy="5" r="2" fill="var(--black-color-5)" />
                            </svg>
                        </span>
                        <span class="om-glyph" style="font-size: 20px;"><i class="bx bx-church"
                                style="color:var(--primary-color)"></i></span>
                        <span class="flourish-stem right">
                            <svg width="48" height="10" viewBox="0 0 48 10" fill="none">
                                <circle cx="6" cy="5" r="2" fill="var(--black-color-5)" />
                                <line x1="10" y1="5" x2="48" y2="5"
                                    stroke="var(--black-color-5)" stroke-width="1" />
                            </svg>
                        </span>
                    </div>
                    <div class="vivah-section-tagline">{{ $data['stories_tagline'] ?? '' }}</div>
                    <h2 class="vivah-section-heading">
                        {!! $data['stories_title'] ?? '' !!}
                    </h2>
                    <p class="vivah-section-subtext">
                        {!! $data['stories_subtitle'] ?? '' !!}
                    </p>
                    <div class="vivah-filigree-divider mt-2 mb-0">
                        <svg width="100" height="14" viewBox="0 0 100 14" fill="none">
                            <line x1="0" y1="7" x2="36" y2="7"
                                stroke="var(--black-color-5)" stroke-width="1" opacity="0.6" />
                            <circle cx="40" cy="7" r="2" fill="var(--black-color-5)" />
                            <path d="M50 2 L54 7 L50 12 L46 7 Z" fill="var(--black-color-5)" />
                            <circle cx="60" cy="7" r="2" fill="var(--black-color-5)" />
                            <line x1="64" y1="7" x2="100" y2="7"
                                stroke="var(--black-color-5)" stroke-width="1" opacity="0.6" />
                        </svg>
                    </div>
                </div>

                <!-- Success Stories Slider -->
                <div class="success-stories-slider mt-3 mt-lg-4">
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
                        <div class="single-stories-box mx-2 mx-lg-3 position-relative">
                            <div class="top-stories-img">
                                <img src="{{ $weddingImage }}" alt="{{ $bridegroomName }}" class="story-img-cpl">
                                <div class="story-badge-vivah">
                                    <i class='bx bxs-heart'></i> {{ _displayDate($story->marriagedate, 'M Y') }}
                                </div>
                                <div class="bottom-stories-contents p-3 p-lg-4">
                                    <div class="story-couple-meta">
                                        {{ optional($story->religionData)->translated_name }}</div>
                                    <h4 class="story-couple-title">{{ $bridegroomName }}</h4>
                                    <p class="story-couple-quote">
                                        "{{ $storyDesc }}"
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- About VivahSutra Section -->
    <section class="about-home-main-sectoin py-5 position-relative">
        <div class="container position-relative" style="z-index: 2;">
            <!-- Section Header -->
            <div class="top-home-abouttext text-center mb-4 mb-lg-5">
                <div class="vivah-om-ornament mb-2">
                    <span class="flourish-stem left">
                        <svg width="48" height="10" viewBox="0 0 48 10" fill="none">
                            <line x1="0" y1="5" x2="38" y2="5"
                                stroke="var(--black-color-5)" stroke-width="1" />
                            <circle cx="42" cy="5" r="2" fill="var(--black-color-5)" />
                        </svg>
                    </span>
                    <span class="om-glyph" style="font-size: 20px;"><i class="bx bx-church"
                            style="color:var(--primary-color)"></i></span>
                    <span class="flourish-stem right">
                        <svg width="48" height="10" viewBox="0 0 48 10" fill="none">
                            <circle cx="6" cy="5" r="2" fill="var(--black-color-5)" />
                            <line x1="10" y1="5" x2="48" y2="5"
                                stroke="var(--black-color-5)" stroke-width="1" />
                        </svg>
                    </span>
                </div>
                <div class="vivah-section-tagline">{{ $data['about_tagline'] ?? '' }}</div>
                <h2 class="vivah-section-heading">
                    {!! $data['about_title'] ?? '' !!}
                </h2>
                <p class="vivah-section-subtext">
                    {!! $data['about_subtitle'] ?? '' !!}
                </p>
                <div class="vivah-filigree-divider mt-2 mb-0">
                    <svg width="100" height="14" viewBox="0 0 100 14" fill="none">
                        <line x1="0" y1="7" x2="36" y2="7"
                            stroke="var(--black-color-5)" stroke-width="1" opacity="0.6" />
                        <circle cx="40" cy="7" r="2" fill="var(--black-color-5)" />
                        <path d="M50 2 L54 7 L50 12 L46 7 Z" fill="var(--black-color-5)" />
                        <circle cx="60" cy="7" r="2" fill="var(--black-color-5)" />
                        <line x1="64" y1="7" x2="100" y2="7"
                            stroke="var(--black-color-5)" stroke-width="1" opacity="0.6" />
                    </svg>
                </div>
            </div>

            <div class="row align-items-center mt-3 mt-lg-4 g-4">
                <!-- Left: Pillar Feature Cards -->
                <div class="col-lg-6">
                    <div class="about-pillar-card p-4 mb-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="pillar-icon-medallion">
                                <i class='bx bxs-sun'></i>
                            </div>
                            <div>
                                <h4 class="pillar-card-title">{{ $data['about_pillar1_title'] ?? '' }}</h4>
                                <p class="pillar-card-desc mb-0">
                                    {{ $data['about_pillar1_desc'] ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="about-pillar-card p-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="pillar-icon-medallion">
                                <i class='bx bxs-shield-alt-2'></i>
                            </div>
                            <div>
                                <h4 class="pillar-card-title">{{ $data['about_pillar2_title'] ?? '' }}</h4>
                                <p class="pillar-card-desc mb-0">
                                    {{ $data['about_pillar2_desc'] ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Vedic Features Badge Grid -->
                <div class="col-lg-6">
                    <div class="right-features-home p-4 p-xl-5">
                        <span class="features-badge-tag">{{ $data['about_features_badge'] ?? '' }}</span>
                        <h3 class="features-box-heading mt-2">{{ $data['about_features_title'] ?? '' }}</h3>
                        <p class="features-box-sub mb-4">
                            {{ $data['about_features_subtitle'] ?? '' }}
                        </p>
                        <div class="featured-home-textgroup d-flex gap-2 gap-md-3 flex-wrap">
                            <span class="feature-common-btn"><i class='bx bxs-check-circle'></i>
                                {{ $data['about_feature_tag1'] ?? '' }}</span>
                            <span class="feature-common-btn"><i class='bx bxs-check-circle'></i>
                                {{ $data['about_feature_tag2'] ?? '' }}</span>
                            <span class="feature-common-btn"><i class='bx bxs-check-circle'></i>
                                {{ $data['about_feature_tag3'] ?? '' }}</span>
                            <span class="feature-common-btn"><i class='bx bxs-check-circle'></i>
                                {{ $data['about_feature_tag4'] ?? '' }}</span>
                            <span class="feature-common-btn"><i class='bx bxs-check-circle'></i>
                                {{ $data['about_feature_tag5'] ?? '' }}</span>
                            <span class="feature-common-btn"><i class='bx bxs-check-circle'></i>
                                {{ $data['about_feature_tag6'] ?? '' }}</span>
                            <span class="feature-common-btn"><i class='bx bxs-check-circle'></i>
                                {{ $data['about_feature_tag7'] ?? '' }}</span>
                            <span class="feature-common-btn"><i class='bx bxs-check-circle'></i>
                                {{ $data['about_feature_tag8'] ?? '' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sacred Milestones Section -->
    <section class="language-home-section py-4 my-0">
        <div class="container">
            <div class="row g-3 g-lg-4 text-center text-sm-start">
                <div class="col-lg-3 col-sm-6">
                    <div class="milestone-box d-flex gap-3 align-items-center p-3">
                        <div class="milestone-icon">
                            <i class='bx bxs-award'></i>
                        </div>
                        <div class="milestone-content">
                            <h4 class="milestone-number">{{ $data['milestone1_number'] ?? '' }}</h4>
                            <p class="milestone-label mb-0">{{ $data['milestone1_label'] ?? '' }}</p>
                            <span class="milestone-sub">{{ $data['milestone1_sub'] ?? '' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="milestone-box d-flex gap-3 align-items-center p-3">
                        <div class="milestone-icon">
                            <i class='bx bxs-group'></i>
                        </div>
                        <div class="milestone-content">
                            <h4 class="milestone-number">{{ $data['milestone2_number'] ?? '' }}</h4>
                            <p class="milestone-label mb-0">{{ $data['milestone2_label'] ?? '' }}</p>
                            <span class="milestone-sub">{{ $data['milestone2_sub'] ?? '' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="milestone-box d-flex gap-3 align-items-center p-3">
                        <div class="milestone-icon">
                            <i class='bx bxs-heart-circle'></i>
                        </div>
                        <div class="milestone-content">
                            <h4 class="milestone-number">{{ $data['milestone3_number'] ?? '' }}</h4>
                            <p class="milestone-label mb-0">{{ $data['milestone3_label'] ?? '' }}</p>
                            <span class="milestone-sub">{{ $data['milestone3_sub'] ?? '' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="milestone-box d-flex gap-3 align-items-center p-3">
                        <div class="milestone-icon">
                            <i class='bx bxs-badge-check'></i>
                        </div>
                        <div class="milestone-content">
                            <h4 class="milestone-number">{{ $data['milestone4_number'] ?? '' }}</h4>
                            <p class="milestone-label mb-0">{{ $data['milestone4_label'] ?? '' }}</p>
                            <span class="milestone-sub">{{ $data['milestone4_sub'] ?? '' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- VivahSutra Mobile App Section -->
    <section class="manage-way-section py-5 position-relative">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center px-lg-4 g-4">
                <div class="col-lg-6">
                    <div class="manage-way-leftside ms-xxl-3">
                        <span class="vivah-section-tagline">{{ $data['app_tagline'] ?? '' }}</span>
                        <h2 class="vivah-section-heading mt-1 mb-2">
                            {!! $data['app_title'] ?? '' !!}
                        </h2>
                        <h4 class="app-sub-heading mt-2 mb-3">
                            {{ $data['app_sub_heading'] ?? '' }}
                        </h4>
                        <p class="app-desc mb-4">
                            {{ $data['app_desc'] ?? '' }}
                        </p>
                        <div class="apps-playstore d-flex gap-3 mt-3">
                            @if (isset($configArr['ios_app_link']) && !blank($configArr['ios_app_link']))
                                <a target="_blank" href="{{ $configArr['ios_app_link'] }}"
                                    class="app-store-badge"><img
                                        src="{{ asset('storage/web/home4') }}/assets/images/icon-app-store.png"
                                        alt="Download on App Store"></a>
                            @endif
                            @if (isset($configArr['android_app_link']) && !blank($configArr['android_app_link']))
                                <a target="_blank" href="{{ $configArr['android_app_link'] }}"
                                    class="app-store-badge"><img
                                        src="{{ asset('storage/web/home4') }}/assets/images/icon-playstore.png"
                                        alt="Get it on Google Play"></a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0 text-center">
                    <div class="manage-way-right position-relative">
                        <div class="app-phone-glow"></div>
                        @php
                            $appMockup = !empty($data['app_image'])
                                ? _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['app_image']
                                : asset('storage/web/home4') . '/assets/images/app-bg-mockup.png';
                        @endphp
                        <img src="{{ $appMockup }}" alt="{{ $configArr['web_name'] }} Mobile App"
                            class="app-mockup-img">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Community & Profile Directory Section -->
    <section class="explore-community-main py-5 position-relative">
        <div class="container position-relative" style="z-index: 2;">
            <!-- Section Header -->
            <div class="vivah-section-header text-center mb-4 mb-lg-5">
                <div class="vivah-om-ornament mb-2">
                    <span class="flourish-stem left">
                        <svg width="48" height="10" viewBox="0 0 48 10" fill="none">
                            <line x1="0" y1="5" x2="38" y2="5"
                                stroke="var(--black-color-5)" stroke-width="1" />
                            <circle cx="42" cy="5" r="2" fill="var(--black-color-5)" />
                        </svg>
                    </span>
                    <span class="om-glyph" style="font-size: 20px;"><i class="bx bx-church"
                            style="color:var(--primary-color)"></i></span>
                    <span class="flourish-stem right">
                        <svg width="48" height="10" viewBox="0 0 48 10" fill="none">
                            <circle cx="6" cy="5" r="2" fill="var(--black-color-5)" />
                            <line x1="10" y1="5" x2="48" y2="5"
                                stroke="var(--black-color-5)" stroke-width="1" />
                        </svg>
                    </span>
                </div>
                <div class="vivah-section-tagline">{{ $data['directory_tagline'] ?? '' }}</div>
                <h2 class="vivah-section-heading">
                    {!! $data['directory_title'] ?? '' !!}
                </h2>
                <p class="vivah-section-subtext">
                    {!! $data['directory_subtitle'] ?? '' !!}
                </p>
                <div class="vivah-filigree-divider mt-2 mb-0">
                    <svg width="100" height="14" viewBox="0 0 100 14" fill="none">
                        <line x1="0" y1="7" x2="36" y2="7"
                            stroke="var(--black-color-5)" stroke-width="1" opacity="0.6" />
                        <circle cx="40" cy="7" r="2" fill="var(--black-color-5)" />
                        <path d="M50 2 L54 7 L50 12 L46 7 Z" fill="var(--black-color-5)" />
                        <circle cx="60" cy="7" r="2" fill="var(--black-color-5)" />
                        <line x1="64" y1="7" x2="100" y2="7"
                            stroke="var(--black-color-5)" stroke-width="1" opacity="0.6" />
                    </svg>
                </div>
            </div>

            {{-- Accordion panels are generated from the same $matrimonyPagesData
                 source home2's directory tabs use, so every design stays in
                 sync with the real matrimony taxonomy data. --}}
            <div class="community-inner-main pt-1 pt-lg-2">
                <div class="accordion" id="accordionExample">
                    <div class="row g-3 g-lg-4">
                        @foreach ($matrimonyPagesData as $type => $items)
                            @php
                                $tabId = Str::slug($type);
                            @endphp
                            <div class="col-lg-6">
                                <div class="single-community-str">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#community-{{ $tabId }}"
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                        aria-controls="community-{{ $tabId }}">
                                        <span class="acc-title-wrap"><i class='bx bxs-user-detail'></i>
                                            {{ $items['label'] }}</span>
                                    </button>
                                    <div id="community-{{ $tabId }}"
                                        class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                        data-bs-parent="#accordionExample">
                                        <div class="matri-comunity-s mt-3">
                                            @foreach ($items['items'] as $item)
                                                <a
                                                    href="{{ route('web.matrimony.index', $item['slug']) }}">{{ $item['matrimony_name'] ?: $item['matrimony_name_old'] ?? 'N/A' }}</a><span
                                                    class="lvg"></span>
                                            @endforeach
                                            <a href="{{ route('web.matrimony.moreDetails', Str::slug($type)) }}"
                                                class="btn-more-details">{{ __('messages.lbl_more_details') }} →</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="footer-main pt-5 position-relative">
        <div class="footer-top-ornament"></div>
        <div class="container position-relative" style="z-index: 2;">
            <div class="row g-4 mb-4">
                <!-- Col 1: Brand & Sanskrit Blessing -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand-wrap">
                        <a href="{{ url('/') }}">
                            <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                                class="footer-logo mb-3" alt="{{ $configArr['web_name'] }}">
                        </a>
                        <p class="footer-brand-desc">
                            {{ $data['footer_desc'] ?? '' }}
                        </p>
                        <div class="footer-shloka-box p-3 mt-3">
                            <p class="shloka-sanskrit mb-1">{{ $data['footer_shloka_sanskrit'] ?? '' }}</p>
                            <p class="shloka-meaning mb-0">{{ $data['footer_shloka_meaning'] ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div class="col-lg-2 col-6">
                    <div class="footer_linking-mng">
                        <h5 class="footer-col-title">{{ __('messages.lbl_others') }}</h5>
                        <ul class="footerlist mt-3">
                            <li><a
                                    href="{{ route('web.search.type', ['type' => 'quick-search']) }}">{{ __('messages.lbl_search') }}</a>
                            </li>
                            <li><a
                                    href="{{ route('web.membershipPlan.index') }}">{{ __('messages.lbl_membership') }}</a>
                            </li>
                            <li><a
                                    href="{{ route('web.successStory.index') }}">{{ __('messages.lbl_success_stories') }}</a>
                            </li>
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
                </div>

                <!-- Col 3: Legal & Trust -->
                <div class="col-lg-2 col-6">
                    <div class="footer_linking-mng">
                        <h5 class="footer-col-title">{{ __('messages.lbl_information') }}</h5>
                        <ul class="footerlist mt-3">
                            <li><a href="{{ route('web.aboutUs.index') }}">{{ __('messages.lbl_about_us') }}</a>
                            </li>
                            @foreach ($cmsPages as $page)
                                <li><a
                                        href="{{ route('web.cmsPages.index', $page->page_url) }}">{{ $page->page_title }}</a>
                                </li>
                            @endforeach
                            <li><a href="{{ route('web.faq.index') }}">{{ __('messages.lbl_faqs') }}</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Col 4: Sacred Support -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-right-part">
                        <h5 class="footer-col-title">{{ __('messages.lbl_contact_info') }}</h5>
                        <p class="footer-support-intro mt-2">
                            {{ $data['footer_support_intro'] ?? '' }}
                        </p>
                        <div class="footer-contact-items mt-3">
                            <div class="footer-contact-suport d-flex gap-3 align-items-center mb-2">
                                <i class='bx bx-phone-call'></i>
                                <div>
                                    <span class="contact-sub-label">{{ __('messages.lbl_phone_number') }}</span>
                                    <a href="tel:{{ $configArr['contact_no'] }}"
                                        class="contact-val d-block">{{ $configArr['contact_no'] }}</a>
                                </div>
                            </div>
                            <div class="footer-contact-suport d-flex gap-3 align-items-center mb-2">
                                <i class='bx bx-mail-send'></i>
                                <div>
                                    <span class="contact-sub-label">{{ __('messages.field_lbl_email_id') }}</span>
                                    <a href="mailto:{{ $configArr['contact_email'] }}"
                                        class="contact-val d-block">{{ $configArr['contact_email'] }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Copyright Bar -->
        <div class="copy-right-footers py-3">
            <div class="container">
                <div class="d-md-flex text-center align-items-center justify-content-between">
                    <div class="fts-13 footer-copyright-text fw-5 mb-2 mb-md-0">
                        {{ $configArr['footer_text'] }}
                    </div>
                    <div class="social-footers-home d-flex justify-content-center gap-2 flex-wrap">
                        @if (!empty($configArr['instagram_link']))
                            <a href="{{ $configArr['instagram_link'] }}" target="_blank"
                                rel="noopener noreferrer" class="social-circle" title="Instagram"><i
                                    class='bx bxl-instagram'></i></a>
                        @endif
                        @if (!empty($configArr['facebook_link']))
                            <a href="{{ $configArr['facebook_link'] }}" target="_blank"
                                rel="noopener noreferrer" class="social-circle" title="Facebook"><i
                                    class='bx bxl-facebook'></i></a>
                        @endif
                        @if (!empty($configArr['youtube_link']))
                            <a href="{{ $configArr['youtube_link'] }}" target="_blank" rel="noopener noreferrer"
                                class="social-circle" title="YouTube"><i class='bx bxl-youtube'></i></a>
                        @endif
                        @if (!empty($configArr['twitter_link']))
                            <a href="{{ $configArr['twitter_link'] }}" target="_blank" rel="noopener noreferrer"
                                class="social-circle" title="Twitter"><i class='bx bxl-twitter'></i></a>
                        @endif
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
    <script src="{{ asset('storage/web/home4/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('storage/web/home4/assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('storage/web/home4/assets/js/wow.js') }}"></script>
    <script src="{{ asset('storage/web/home4/assets/js/slick.min.js') }}"></script>
    <script src="{{ asset('storage/web/home4/assets/js/main.js') }}"></script>

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

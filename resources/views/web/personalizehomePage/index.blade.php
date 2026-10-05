<!DOCTYPE html>
<html lang="en">

<head>
    {{-- Seo Layout Section --}}
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.seoLayout')

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_favicon'] }}"
        type="image/webp">

    <!-- all css file include  -->
    <link rel="stylesheet" href="{{ asset('storage/web/personalized_assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/personalized_assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/personalized_assets/css/select-2.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/personalized_assets/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/personalized_assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/personalized_assets/css/responsive.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
    
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Logo -->
                <a href="{{ url('/') }}">
                    <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                        alt="{{ $configArr['web_name'] }}" class="brand-logo">
                </a>

                <!-- Right Actions -->
                <div class="d-flex align-items-center gap-3">
                    <a href="tel:{{ $configArr['contact_no'] }}"
                        class="header-phone d-none d-md-flex align-items-center gap-2 text-decoration-none">
                        <iconify-icon icon="hugeicons:call"></iconify-icon> {{ $configArr['contact_no'] }}
                    </a>
                    <button class="menu-btn" data-bs-toggle="offcanvas" data-bs-target="#sideMenu">
                        <span><iconify-icon class="mt-2" icon="gg:menu-right" width="26 "
                                height="26"></iconify-icon></span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Side Menu (Offcanvas) -->
    <div class="offcanvas offcanvas-end offcanvas-dark" tabindex="-1" id="sideMenu">
        <div class="offcanvas-header border-bottom border-secondary">
            <h5 class="brand-logo mb-0">
                <a href="{{ url('/') }}">
                    <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                        alt="{{ $configArr['web_name'] }}">
                </a>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body py-4">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="#">{{ __('messages.lbl_home') }}</a></li>
                <li class="nav-item"><a class="nav-link"
                        href="#aboutService">{{ __('messages.lbl_about_assisted_service') }}</a></li>
                <li class="nav-item"><a class="nav-link"
                        href="#successStories">{{ __('messages.lbl_success_stories') }}</a></li>
                <li class="nav-item"><a class="nav-link"
                        href="#pricingPlans">{{ __('messages.lbl_pricing_plans') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="#contactUs">{{ __('messages.lbl_contact_us') }}</a></li>
                <li class="nav-item"><a href="tel:{{ $configArr['contact_no'] }}"
                        class="header-phone d-flex bg-transparent mt-4 d-md-none align-items-center gap-2 text-decoration-none">
                        <iconify-icon icon="hugeicons:call"></iconify-icon> {{ $configArr['contact_no'] }}
                    </a></li>
            </ul>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center"
        style="background: linear-gradient(90deg, var(--black-color-2) 30%, rgba(9, 15, 28, 0.4) 100%), url({{ _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $homePageData->banner_section_image }}) no-repeat center / cover;">
        <div class="container">
            <div class="row align-items-center">

                <!-- Left Content -->
                <div class="col-lg-6 mb-5 mb-lg-0 wow animate__animated animate__fadeInLeft">
                    <span class="hero-badge"><span><iconify-icon class="mt-1" icon="hugeicons:ai-brain-01"
                                width="24" height="24"></iconify-icon></span>
                        {{ $homePageData->banner_section_heading }}</span>
                    <h1 class="white-color-n fts-68 fw-8 mb-4">
                        {{ $homePageData->banner_section_title }} <br>
                        <span class="primary-color-n">{{ $homePageData->banner_section_title2 }}</span>
                    </h1>
                    <p class="white-color70-n fts-16 fw-4 mb-5" style="max-width: 450px; line-height: 1.6;">
                        {{ $homePageData->banner_section_subtitle }}
                    </p>
                    <div class="d-flex flex-wrap gap-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="iconify primary-color-n fts-20"><iconify-icon class="mt-1"
                                    icon="material-symbols:verified-outline" width="24"
                                    height="24"></iconify-icon></span>
                            <span class="white-color-n fts-14 fw-5">{{ $homePageData->feature_1_text }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="iconify primary-color-n fts-20"><iconify-icon icon="hugeicons:lock-key"
                                    width="24" height="24"></iconify-icon></span>
                            <span class="white-color-n fts-14 fw-5">{{ $homePageData->feature_2_text }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Form -->
                <div class="col-lg-5 offset-lg-1 wow animate__animated animate__fadeInRight">
                    <div class="lead-form-card">
                        <h3 class="white-color-n fts-24 fw-7 mb-2">{{ $homePageData->inquiry_title }}</h3>
                        <p class="white-color70-n fts-14 mb-4 pb-2">{{ $homePageData->inquiry_subtitle }}</p>
                        @include('web.common.alert_message')
                        <form id="personalizedEnquiryForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label-small">{{ __('messages.field_lbl_fullname') }}</label>
                                <input type="text" name="full_name" required id="full_name" class="custom-input"
                                    placeholder="{{ __('messages.field_lbl_enter_full_name') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label-small">{{ __('messages.field_lbl_mobile_number') }}</label>
                                <div class="phone-group">
                                    <select name="country_code" required id="country_code"
                                        class="custom-input phone-code">
                                        @php echo _defaultCountryCode() @endphp
                                    </select>
                                    <input type="tel" name="mobile_no" id="mobile_no" class="custom-input"
                                        placeholder="{{ __('messages.field_lbl_enter_mobile_number') }}">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label-small">{{ __('messages.field_lbl_email_id') }}</label>
                                <input type="email" required name="email" id="email" class="custom-input"
                                    placeholder="{{ __('messages.field_lbl_enter_your_email_id') }}">
                            </div>
                            <button type="submit" id="submitBtn"
                                class="btn-custom btn-primary-custom w-100 py-3 rounded-5">
                                {{ __('messages.lbl_submit') }} <iconify-icon icon="icon-park-outline:right"
                                    width="18" height="18"></iconify-icon>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Curation Section -->
    <section class="curation-section">
        <div class="container wow animate__animated animate__fadeInUp">
            <div class="row align-items-center">
                <!-- Left Image -->
                <div class="col-lg-5 mb-5 mb-lg-0">
                    <div class="curation-circle">
                        <!-- Using an illustrative placeholder image -->
                        <img src="{{ _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $homePageData->curation_section_banner }}"
                            alt="Consultant">
                    </div>
                </div>

                <!-- Right Content -->
                <div class="col-lg-6 offset-lg-1">
                    <h2 class="white-color-n fts-36 fw-7 mb-5">{{ $homePageData->curation_section_heading }}</h2>

                    <div class="icon-list-item">
                        <div class="icon-circle-bg"><iconify-icon class="mt-1" icon="hugeicons:ai-brain-01"
                                width="24" height="24"></iconify-icon></div>
                        <div>
                            <h4 class="white-color-n fts-16 fw-6 mb-2">{{ $homePageData->curation_section_title1 }}
                            </h4>
                            <p class="white-color70-n fts-14 fw-4" style="line-height: 1.6;">
                                {{ $homePageData->curation_section_subtitle1 }}</p>
                        </div>
                    </div>

                    <div class="icon-list-item">
                        <div class="icon-circle-bg"><iconify-icon class="mt-1" icon="hugeicons:clock-01"
                                width="24" height="24"></iconify-icon>
                        </div>
                        <div>
                            <h4 class="white-color-n fts-16 fw-6 mb-2">{{ $homePageData->curation_section_title2 }}
                            </h4>
                            <p class="white-color70-n fts-14 fw-4" style="line-height: 1.6;">
                                {{ $homePageData->curation_section_subtitle2 }}</p>
                        </div>
                    </div>

                    <div class="mt-4 pt-2">
                        <a href="#" class="btn-custom btn-outline-custom">
                            <iconify-icon class="mt-1 text-primary" icon="hugeicons:customer-support" width="24"
                                height="24"></iconify-icon>
                            {{ $homePageData->curation_schedule_text }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- System Advantages Section -->
    <section class="py-5" id="aboutService">
        <div class="container wow animate__animated animate__fadeInUp">

            <div class="d-flex justify-content-between align-items-end mb-5">
                <div>
                    <h2 class="white-color-n fts-36 fw-7 mb-2">{{ $homePageData->system_advantages_section_title }}
                    </h2>
                    <p class="white-color70-n fts-14">{{ $homePageData->system_advantages_section_subtitle }}</p>
                </div>
                <div class="slider-nav-arrows">
                    <div class="custom-arrow adv-prev"><iconify-icon icon="icon-park-outline:left" width="18"
                            height="18"></iconify-icon></div>
                    <div class="custom-arrow adv-next"><iconify-icon icon="icon-park-outline:right" width="18"
                            height="18"></iconify-icon></div>
                </div>
            </div>

            <!-- Advantages Slider -->
            <div class="advantages-slider">
                <!-- Slide 1 -->
                <div>
                    <div class="advantage-card">
                        <span class="advantage-num">01</span>
                        <div class="position-relative z-1 pt-4 mt-3">
                            <h4 class="white-color-n fts-20 fw-6 mb-3">{{ $homePageData->curation_section_heading }}
                            </h4>
                            <p class="white-color70-n fts-14 fw-4" style="line-height: 1.6;">
                                {{ $homePageData->curation_section_heading }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="advantage-card">
                        <span class="advantage-num">01</span>
                        <div class="position-relative z-1 pt-4 mt-3">
                            <h4 class="white-color-n fts-20 fw-6 mb-3">{{ $homePageData->advantage_1_title }}</h4>
                            <p class="white-color70-n fts-14 fw-4" style="line-height: 1.6;">
                                {{ $homePageData->advantage_1_subtitle }}</p>
                        </div>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div>
                    <div class="advantage-card">
                        <span class="advantage-num">02</span>
                        <div class="position-relative z-1 pt-4 mt-3">
                            <h4 class="white-color-n fts-20 fw-6 mb-3">{{ $homePageData->advantage_2_title }}</h4>
                            <p class="white-color70-n fts-14 fw-4" style="line-height: 1.6;">
                                {{ $homePageData->advantage_2_subtitle }}</p>
                        </div>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div>
                    <div class="advantage-card">
                        <span class="advantage-num">03</span>
                        <div class="position-relative z-1 pt-4 mt-3">
                            <h4 class="white-color-n fts-20 fw-6 mb-3">{{ $homePageData->advantage_3_title }}</h4>
                            <p class="white-color70-n fts-14 fw-4" style="line-height: 1.6;">
                                {{ $homePageData->advantage_3_subtitle }}</p>
                        </div>
                    </div>
                </div>
                <!-- Slide 4 -->
                <div>
                    <div class="advantage-card">
                        <span class="advantage-num">04</span>
                        <div class="position-relative z-1 pt-4 mt-3">
                            <h4 class="white-color-n fts-20 fw-6 mb-3">{{ $homePageData->advantage_4_title }}</h4>
                            <p class="white-color70-n fts-14 fw-4" style="line-height: 1.6;">
                                {{ $homePageData->advantage_4_subtitle }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    @if (!empty($photoSuccesStory) || !empty($videoSuccesStory))
        <section class="py-5 my-5" id="successStories" style="background-color: var(--black-color-1);">
            <div class="container py-5 wow animate__animated animate__fadeInUp">

                <h2 class="white-color-n fts-36 fw-7 mb-4">{{ $homePageData->success_story_title }}</h2>

                <!-- Tabs & Navigation -->
                <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
                    <ul class="nav nav-pills nav-pills-custom gap-3" id="testimonialTabs" role="tablist">
                        @if (!empty($photoSuccesStory))
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="pill"
                                    data-bs-target="#successStoriesTab" type="button"
                                    role="tab">{{ __('messages.lbl_success_stories') }}</button>
                            </li>
                        @endif
                        @if (!empty($videoSuccesStory))
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#videoStoriesTab"
                                    type="button" role="tab">
                                    <span class="iconify me-2" data-icon="hugeicons:video-replay"></span>
                                    {{ __('messages.lbl_video_stories') }}
                                </button>
                            </li>
                        @endif
                    </ul>
                    <div class="slider-nav-arrows">
                        <div class="custom-arrow testi-prev"><iconify-icon icon="icon-park-outline:left"
                                width="18" height="18"></iconify-icon></span></div>
                        <div class="custom-arrow testi-next"><iconify-icon icon="icon-park-outline:right"
                                width="18" height="18"></iconify-icon></div>
                    </div>
                </div>

                <!-- Tabs Content -->
                <div class="tab-content" id="testimonialTabsContent">

                    <!-- Success Stories Tab -->
                    <div class="tab-pane fade show active" id="successStoriesTab" role="tabpanel">
                        <div class="testimonials-slider">
                            <!-- Slide 1 -->
                            @foreach ($photoSuccesStory as $story)
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
                                            _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $story->wedding_photo;
                                    }
                                    $bridegroomName = $story->groomname . ' & ' . $story->bridename;
                                    $storyDesc = Str::limit(strip_tags($story->successmessage), 600);
                                @endphp
                                <div class="testimonial-slide-wrap">
                                    <div class="row align-items-center">
                                        <div class="col-lg-4 mb-4 mb-lg-0">
                                            <div class="testimonial-img-box">
                                                <img src="{{ $weddingImage }}" alt="{{ $bridegroomName }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="testi-content-box">
                                                <div
                                                    class="testi-title-row d-flex justify-content-between align-items-center">
                                                    <h3 class="white-color-n fts-24 fw-7">{{ $bridegroomName }}</h3>
                                                    <span
                                                        class="white-color70-n fts-14">{{ _displayDate($story->created_at, 'j F, Y') }}</span>
                                                </div>
                                                <p class="white-color70-n fts-14 fw-4 mb-3" style="line-height: 1.8;">
                                                    {{ $storyDesc }}
                                                </p>
                                                <a href="{{ route('web.successStory.details', $story->id) }}"
                                                    class="btn-read-more">{{ __('messages.lbl_read_more') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Video Stories Tab -->
                    @if (!empty($videoSuccesStory))
                        <div class="tab-pane fade" id="videoStoriesTab" role="tabpanel">
                            <div class="row justify-content-center">
                                <div class="col-lg-10">
                                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden border"
                                        style="border-color: var(--black-color-3) !important;">
                                        <div class="vp-wrapper">
                                            @php
                                                $bridegroomName =
                                                    $videoSuccesStory->groomname . ' & ' . $videoSuccesStory->bridename;
                                                $storyDesc = Str::limit(
                                                    strip_tags($videoSuccesStory->successmessage),
                                                    120,
                                                );

                                                $videoFileUrl = '';
                                                $youtubeId = '';
                                                $posterUrl = '';
                                                if ($videoSuccesStory->video_type == 'youtube') {
                                                    preg_match(
                                                        '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\&\?\/]+)/',
                                                        'https://youtu.be/YkjOuYP8wjc?list=RDYkjOuYP8wjc',
                                                        $matches,
                                                    );
                                                    $youtubeId = $matches[1] ?? '';
                                                    $thumbnail = $youtubeId
                                                        ? 'https://img.youtube.com/vi/' .
                                                            $youtubeId .
                                                            '/maxresdefault.jpg'
                                                        : '';
                                                } else {
                                                    if (
                                                        !blank($videoSuccesStory->wedding_video_file) &&
                                                        _checkStorageFileExists(
                                                            'upload_path.SUCCESS_STORY_IMAGE_URL',
                                                            $videoSuccesStory->wedding_video_file,
                                                        )
                                                    ) {
                                                        $videoFileUrl =
                                                            _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') .
                                                            $videoSuccesStory->wedding_video_file;
                                                    }

                                                    if (
                                                        !blank($videoSuccesStory->wedding_video_thumbnail) &&
                                                        _checkStorageFileExists(
                                                            'upload_path.SUCCESS_STORY_IMAGE_URL',
                                                            $videoSuccesStory->wedding_video_thumbnail,
                                                        )
                                                    ) {
                                                        $posterUrl =
                                                            _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') .
                                                            $videoSuccesStory->wedding_video_thumbnail;
                                                    }
                                                }
                                            @endphp
                                            @if ($videoSuccesStory->video_type == 'youtube')
                                                <div class="vp-poster"
                                                    style="background-image:url('{{ $thumbnail }}')">
                                                    <div class="vp-play-btn">▶</div>
                                                    <div class="vp-title-block">
                                                        <h2>{{ $bridegroomName }}</h2>
                                                        <p>{{ $storyDesc }}</p>
                                                    </div>
                                                </div>

                                                <iframe class="vp-youtube" src=""
                                                    data-src="https://www.youtube.com/embed/{{ $youtubeId }}?autoplay=1"
                                                    frameborder="0" allowfullscreen
                                                    style="display:none;width:100%;height:auto; aspect-ratio: 16/9; border-radius:20px;">
                                                </iframe>
                                            @else
                                                <video class="vp-video" poster="{{ $posterUrl }}"
                                                    preload="metadata"
                                                    style="display:none;width:100%;border-radius:20px;">
                                                    <source src="{{ $videoFileUrl }}" type="video/mp4">
                                                </video>
                                                <!-- Play Button -->
                                                <div class="vp-play-btn" id="vpPlayBtn">
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M8 5v14l11-7z" />
                                                    </svg>
                                                </div>
                                                <!-- Title -->
                                                <div class="vp-title-block">
                                                    <h2>{{ $bridegroomName }}</h2>
                                                    <p>{{ $storyDesc }}</p>
                                                </div>
                                                <!-- Controls -->
                                                <div class="vp-controls" id="vpControls">
                                                    <button class="ctrl-btn btnPlayPause">
                                                        <svg class="iconPlay" viewBox="0 0 24 24">
                                                            <path d="M8 5v14l11-7z" />
                                                        </svg>
                                                        <svg class="iconPause" viewBox="0 0 24 24"
                                                            style="display:none">
                                                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                                                        </svg>
                                                    </button>
                                                    <div class="vp-progress-wrap">
                                                        <input type="range" class="vp-progress" value="0"
                                                            min="0" max="100">
                                                    </div>
                                                    <span class="vp-time">0:00 / 0:00</span>
                                                    <button class="ctrl-btn btnMute">
                                                        <svg class="iconVol" viewBox="0 0 24 24"></svg>
                                                        <svg class="iconMute" viewBox="0 0 24 24"
                                                            style="display:none"></svg>
                                                    </button>
                                                    <input type="range" class="vp-volume" value="100"
                                                        min="0" max="100">
                                                    <button class="ctrl-btn btnFullscreen">
                                                        <svg viewBox="0 0 24 24"></svg>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                <!-- CTA Bottom -->
                <div class="text-center mt-5 pt-4 border-top" style="border-color: var(--black-color-3) !important;">
                    <h4 class="white-color-n fts-16 fw-6 mb-3">{{ $homePageData->cta_title }}</h4>
                    <a href="#" class="btn-custom btn-outline-custom mt-2">
                        <iconify-icon class="mt-1 text-primary" icon="hugeicons:customer-support" width="24"
                            height="24"></iconify-icon>
                        {{ $homePageData->cta_subtitle }}
                    </a>
                </div>

            </div>
        </section>
    @endif

    <!-- Pricing Section -->
    @if (!empty($personalizePlans))
        <section class="py-5" id="pricingPlans">
            <div class="container wow animate__animated animate__fadeInUp">
                <div class="pricing-container">
                    <div class="row align-items-center">

                        <!-- Left Title -->
                        <div class="col-lg-5 mb-5 mb-lg-0 pe-lg-5">
                            <h2 class="white-color-n fts-36 fw-7 mb-3">{{ $homePageData->assisted_service_title }}
                            </h2>
                            <p class="white-color70-n fts-16 mb-5 pb-4">{{ $homePageData->assisted_service_subtitle }}
                            </p>

                            <div class="slider-nav-arrows">
                                <div class="custom-arrow price-prev"><iconify-icon icon="icon-park-outline:left"
                                        width="18" height="18"></iconify-icon></span>
                                </div>
                                <div class="custom-arrow price-next"><iconify-icon icon="icon-park-outline:right"
                                        width="18" height="18"></iconify-icon></div>
                            </div>
                        </div>

                        <!-- Right Pricing Slider -->
                        <div class="col-lg-7">
                            <div class="pricing-slider">
                                <!-- Slide 1: Diamond Tier -->
                                @foreach ($personalizePlans as $plan)
                                    <div class="px-2">
                                        <div class="pricing-card">
                                            <div class="pricing-header">
                                                <div>
                                                    <h3 class="white-color-p fts-24 fw-7 mb-1">{{ $plan->plan_name }}
                                                    </h3>
                                                    @if (!blank($plan->plan_description))
                                                        <span
                                                            class="primary-color-n fts-12 fw-6">{{ $plan->plan_description }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-end">
                                                    <h3 class="white-color-p fts-36 fw-7 mb-0">
                                                        {{ $plan->currency_code . ' ' . $plan->plan_amount }}
                                                    </h3>
                                                    <span class="white-color70-n fts-10 text-uppercase">
                                                        {{ $plan->validity_days }}-{{ __('messages.lbl_days_lifecycle') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <ul class="pricing-list">
                                                <li>
                                                    {{ __('messages.lbl_allowed_interests') }}
                                                    <span class="val">{{ $plan->interests_limit }}
                                                        <iconify-icon icon="gg:check-o"
                                                            class="primary-color-n fts-20"></iconify-icon>
                                                    </span>
                                                </li>
                                                <li>
                                                    {{ __('messages.lbl_allowed_contact_views') }}
                                                    <span class="val">{{ $plan->contact_views_limit }}
                                                        <iconify-icon icon="gg:check-o"
                                                            class="primary-color-n fts-20"></iconify-icon>
                                                    </span>
                                                </li>
                                                <li>
                                                    {{ __('messages.lbl_audio_calls') }}
                                                    <span class="val">
                                                        @if ($plan->audio_minutes_limit > 0)
                                                            {{ $plan->audio_minutes_limit }} min
                                                            <iconify-icon icon="gg:check-o"
                                                                class="primary-color-n fts-20"></iconify-icon>
                                                        @else
                                                            {{ __('messages.lbl_not_available') }}
                                                            <iconify-icon icon="zondicons:close-outline"
                                                                class="text-danger fts-20"></iconify-icon>
                                                        @endif
                                                    </span>
                                                </li>
                                                <li>
                                                    {{ __('messages.lbl_video_calls') }}
                                                    <span class="val">
                                                        @if ($plan->video_minutes_limit > 0)
                                                            {{ $plan->video_minutes_limit }} min
                                                            <iconify-icon icon="gg:check-o"
                                                                class="primary-color-n fts-20"></iconify-icon>
                                                        @else
                                                            {{ __('messages.lbl_not_available') }}
                                                            <iconify-icon icon="zondicons:close-outline"
                                                                class="text-danger fts-20"></iconify-icon>
                                                        @endif
                                                    </span>
                                                </li>
                                                <li>
                                                    {{ __('messages.lbl_live_chat') }}
                                                    <span class="val">
                                                        @if ($plan->can_chat > 0)
                                                            {{ __('messages.lbl_yes') }}
                                                            <iconify-icon icon="gg:check-o"
                                                                class="primary-color-n fts-20"></iconify-icon>
                                                        @else
                                                            {{ __('messages.lbl_not_available') }}
                                                            <iconify-icon icon="zondicons:close-outline"
                                                                class="text-danger fts-20"></iconify-icon>
                                                        @endif
                                                    </span>
                                                </li>
                                            </ul>
                                            <div class="p-4 text-center">
                                                <a href="{{ route('web.membershipPlan.checkout', $plan->id) }}"
                                                    class="btn-custom btn-white-custom w-100 py-3 rounded-2 fw-7 text-uppercase mb-3">
                                                    {{ __('messages.lbl_buy_this_plan') }}
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

    <!-- Query Box -->
    <section class="py-5 my-4" id="contactUs">
        <div class="container wow animate__animated animate__fadeInUp">
            <div class="query-box">
                <h2 class="white-color-n fts-28 fw-7 mb-4">{{ __('messages.lbl_have_any_queries') }}</h2>
                <a href="tel:{{ $configArr['contact_no'] }}" class="btn-custom btn-outline-custom px-4">
                    <iconify-icon class="mt-0 text-primary" icon="hugeicons:customer-support" width="24"
                        height="24"></iconify-icon> {{ $configArr['contact_no'] }}
                </a>
            </div>
        </div>
    </section>

    <!-- footer section start -->
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.footer')

    <!-- Scripts -->
    <script src="{{ asset('storage/web/personalized_assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('storage/web/personalized_assets/js/select-2.js') }}"></script>
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>
    <script src="{{ asset('storage/web/personalized_assets/js/wow.js') }}"></script>
    <script src="{{ asset('storage/web/personalized_assets/js/slick.js') }}"></script>
    <script src="{{ asset('storage/web/personalized_assets/js/slick.min.js') }}"></script>
    <script src="{{ asset('storage/web/personalized_assets/js/main.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    {{-- common.js --}}
    <script>
        const csrfToken = "{{ csrf_token() }}";
        var lbl_loading = "{{ __('messages.lbl_loading') }}";
        var lbl_read_more = "{{ __('messages.lbl_read_more') }}";
        var lbl_read_less = "{{ __('messages.lbl_read_less') }}";
        var lbl_block = "{{ __('messages.lbl_block') }}";
        var lbl_unblock = "{{ __('messages.lbl_unblock') }}";
    </script>
    <script src="{{ asset('storage/web/custom/js/common.js') }}"></script>

    <script>
        $(document).ready(function() {

            $("#personalizedEnquiryForm").validate({
                rules: {
                    full_name: {
                        required: true,
                        minlength: 3
                    },
                    country_code: {
                        required: true
                    },
                    mobile_no: {
                        required: true,
                        digits: true,
                        minlength: 8,
                        maxlength: 15
                    },
                    email: {
                        required: true,
                        email: true
                    }
                },
                messages: {
                    full_name: "{{ __('messages.field_lbl_please_enter_full_name') }}",
                    country_code: "{{ __('messages.msg_country_code_required') }}",
                    mobile_no: "{{ __('messages.msg_please_enter_valid_mobile_number') }}",
                    email: "{{ __('messages.msg_please_enter_valid_email') }}"
                },
                errorElement: "small",
                errorClass: "text-danger",
                highlight: function(element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid");
                },
                errorPlacement: function(error, element) {
                    if (element.hasClass("select2-hidden-accessible")) {
                        error.insertAfter(element.next('.select2'));
                    } else if (element.attr("name") === "mobile_no") {
                        error.insertAfter(element.closest('.phone-group'));
                    } else if (element.is(":checkbox") || element.is(":radio")) {
                        // For checkboxes or radio buttons, insert after their associated label
                        var label = $("label[for='" + element.attr("id") + "']");
                        if (label.length) {
                            error.insertAfter(label);
                        } else {
                            error.insertAfter(element); // fallback
                        }
                    } else {
                        // Default placement
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    let $btn = $(form).find('button[type="submit"]');
                    $btn.prop('disabled', true).text('{{ __('messages.lbl_please_wait') }}');

                    $.post("{{ route('web.personalize.storeEnquiry') }}", $(form).serialize(),
                        function(res) {

                            $btn.prop('disabled', false).text('{{ __('messages.lbl_submit') }}');
                            if (res.status) {
                                showAlertMessage('success', res.message);
                            } else {
                                showAlertMessage('error', res.message);
                            }

                        }).fail(function() {
                        showAlertMessage('error', '{{ __('messages.msg_unexpected_error_occured') }}');
                        $btn.prop('disabled', false).text('{{ __('messages.lbl_submit') }}');
                    });

                    return false;
                }
            });

        });
    </script>
    <script>
        document.querySelectorAll(".vp-wrapper").forEach(wrapper => {
            const poster = wrapper.querySelector(".vp-poster");
            const video = wrapper.querySelector(".vp-video");
            const controls = wrapper.querySelector(".vp-controls");
            const btnPlay = wrapper.querySelector(".btnPlayPause");
            const iconPlay = wrapper.querySelector(".iconPlay");
            const iconPause = wrapper.querySelector(".iconPause");
            const progress = wrapper.querySelector(".vp-progress");
            const timeDisplay = wrapper.querySelector(".vp-time");
            const volume = wrapper.querySelector(".vp-volume");
            const btnMute = wrapper.querySelector(".btnMute");
            const iconVol = wrapper.querySelector(".iconVol");
            const iconMute = wrapper.querySelector(".iconMute");
            const btnFs = wrapper.querySelector(".btnFullscreen");
            const playBtn = wrapper.querySelector(".vp-play-btn");
            const youtube = wrapper.querySelector(".vp-youtube");

            /* ---------------- Start Video ---------------- */
            function startVideo() {
                if (playBtn) playBtn.style.display = "none";
                if (poster) poster.style.display = "none";
                if (video) {
                    video.style.display = "block";
                    video.play();
                }
                if (youtube) {
                    youtube.style.display = "block";
                    youtube.src = youtube.dataset.src;
                }
                if (controls) controls.classList.add("active");
            }
            if (video) {
                video.style.display = "block";
            }
            if (playBtn) {
                playBtn.addEventListener("click", startVideo);
            }
            if (poster) {
                poster.addEventListener("click", startVideo);
            }

            /* ---------------- MP4 Controls ---------------- */
            if (video && btnPlay) {
                $('.vp-title-block').hide();

                btnPlay.addEventListener("click", () => {
                    video.paused ? video.play() : video.pause();
                });
                video.addEventListener("play", updatePlayPause);
                video.addEventListener("pause", updatePlayPause);

                function updatePlayPause() {
                    if (!iconPlay || !iconPause) return;
                    const paused = video.paused;
                    iconPlay.style.display = paused ? "block" : "none";
                    iconPause.style.display = paused ? "none" : "block";
                }

                /* -------- Progress -------- */
                video.addEventListener("timeupdate", () => {
                    if (!video.duration) return;
                    const pct = (video.currentTime / video.duration) * 100;
                    if (progress) progress.value = pct;
                    if (timeDisplay) {
                        timeDisplay.textContent =
                            `${fmt(video.currentTime)} / ${fmt(video.duration)}`;
                    }
                });

                if (progress) {
                    progress.addEventListener("input", () => {
                        video.currentTime = (progress.value / 100) * video.duration;
                    });
                }

                /* -------- Volume -------- */
                if (volume) {
                    volume.addEventListener("input", () => {
                        video.volume = volume.value / 100;
                        video.muted = video.volume === 0;
                        updateVolIcon();
                    });
                }

                if (btnMute) {
                    btnMute.addEventListener("click", () => {
                        video.muted = !video.muted;
                        if (volume) volume.value = video.muted ? 0 : video.volume * 100;
                        updateVolIcon();
                    });
                }

                function updateVolIcon() {
                    if (!iconVol || !iconMute) return;
                    const muted = video.muted || video.volume === 0;
                    iconVol.style.display = muted ? "none" : "block";
                    iconMute.style.display = muted ? "block" : "none";
                }

                /* -------- Fullscreen -------- */
                if (btnFs) {
                    btnFs.addEventListener("click", () => {
                        if (!document.fullscreenElement) {
                            wrapper.requestFullscreen();
                        } else {
                            document.exitFullscreen();
                        }
                    });
                }

                /* -------- Time Format -------- */
                function fmt(s) {
                    const m = Math.floor(s / 60);
                    const sec = Math.floor(s % 60).toString().padStart(2, "0");
                    return `${m}:${sec}`;
                }

                /* -------- Video End -------- */
                video.addEventListener("ended", () => {
                    playBtn.style.removeProperty("display");
                    if (controls) controls.classList.remove("active");
                    if (poster) poster.style.display = "block";
                    video.currentTime = 0;
                    if (progress) progress.value = 0;
                    if (timeDisplay) timeDisplay.textContent = "0:00 / 0:00";
                });
            }
        });
    </script>
</body>

</html>

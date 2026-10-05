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

    @stack('styles')
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
    <!-- navbar start  -->
    <div class="main-navbardesign py-3">
        <div class="container">
            <nav class="navbar-motersDesign d-flex align-items-center justify-content-between" aria-label="navbar">
                <div class="mobile-logo-category d-flex align-items-center gap-4">
                    <a href="{{ route('affiliate.home.index') }}">
                        <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                            alt="{{ $configArr['web_name'] }}" class="brand-logo">
                    </a>
                </div>
                <div class="mobile-navbar">
                    <ul class="navbar-ui-affiliat d-lg-flex align-items-center gap-2">
                        <li class="d-flex align-items-center gap-2 ps-2">
                            <a href="{{ route('affiliate.home.index') }}" class="nav-items fts-14 me-md-3">Home</a>
                            <!-- <a href="{{ route('affiliate.home.index') }}#how-its-works" class="nav-items fts-14 me-md-3">How It Works</a> -->
                            <a href="{{ route('affiliate.home.index') }}#features" class="nav-items fts-14 me-md-3">Features</a>
                            <a href="{{ route('affiliate.home.index') }}#testimonials" class="nav-items fts-14 me-md-3">Testimonials</a>
                            <a href="{{ route('affiliate.login.index') }}"
                                class="comman-border-btn fts-15">{{ __('messages.lbl_login') }}</a>
                            <a href="{{ route('affiliate.register.index') }}"
                                class="comman-bg-btn fts-15">{{ __('messages.lbl_register') }}</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    {{-- Main Conent --}}
    @yield('affiliate_content')

    {{-- toast notification --}}
    @include(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.common.toast_message')
    {{-- toast notification --}}

    <!-- footer section start -->
    <footer class="footer-main pt-3 pt-lg-5 black-bgcolor2-n">
        <div class="container">
            <div class="row px-2">
                <div class="col-lg-3 col-sm-3 mt-3 mt-lg-0 px-1">
                    <div class="footer_linking-mng pe-lg-2">
                        <a href="{{ route('affiliate.home.index') }}">
                            <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                                alt="{{ $configArr['web_name'] }}" class="footer-logo">
                        </a>
                        <div class="footerlist mt-2 mt-lg-3">
                            <div class="fts-18 fw-7 white-color-n mb-1">{{ __('messages.field_lbl_address') }}</div>
                            <div class="footer-contact-suport py-1">
                                <a href=""
                                    class="fts-14 white-color70-n fw-4">{{ $configArr['full_address'] }}</a>
                            </div>
                            <div class="footer-contact-suport py-1">
                                <a href="" class="fts-14 white-color70-n fw-4">{{ __('messages.lbl_email') }} :
                                    {{ $configArr['contact_email'] }}</a>
                            </div>
                            <div class="footer-contact-suport py-1">
                                <a href="" class="fts-14 white-color70-n fw-4">{{ __('messages.lbl_phone_no') }}
                                    : {{ $configArr['contact_no'] }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-4 mt-3 mt-lg-0 px-1">
                    <div class="footer_linking-mng">
                        <div class="fts-18 fw-7 white-color-n">{{ __('messages.lbl_help_support') }}</div>
                        <ul class="footerlist mt-1 mt-lg-3">
                            <li><a href="{{ route('web.contactUs.index') }}"
                                    class="fts-14 fw-4 white-color70-n mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_contact_us') }}</a>
                            </li>
                            <li><a href="{{ route('web.successStory.index') }}"
                                    class="fts-14 fw-4 white-color70-n mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_success_stories') }}</a>
                            </li>
                            <li><a href="{{ route('web.advertisement.index') }}"
                                    class="fts-14 fw-4 white-color70-n mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_advertise_with_us') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-4 mt-3 mt-lg-0 px-1">
                    <div class="footer_linking-mng">
                        <div class="fts-18 fw-7 white-color-n">{{ __('messages.lbl_information') }}</div>
                        <ul class="footerlist mt-1 mt-lg-3">
                            <li><a href="{{ route('web.faq.index') }}"
                                    class="fts-14 fw-4 white-color70-n mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_faqs') }}</a>
                            </li>
                            @foreach ($cmsPages as $page)
                                <a href="{{ route('web.cmsPages.index', $page->page_url) }}"
                                    class="fts-14 fw-4 white-color70-n mb-lg-2 d-inline-block py-1">{{ $page->page_title }}</a>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-8 mt-3 mt-lg-0 px-1">
                    <div class="footer-right-part">
                        <div class="fts-18 fw-7 white-color-n">{{ __('messages.lbl_others') }}</div>
                        <ul class="footerlist mt-1 mt-lg-3">
                            @guest('web')
                                <li><a href="{{ route('web.register.index') }}"
                                        class="fts-14 fw-4 white-color70-n mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_register') }}</a>
                                </li>
                                <li><a href="{{ route('web.login.index') }}"
                                        class="fts-14 fw-4 white-color70-n mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_login') }}</a>
                                </li>
                            @endguest
                            <li><a href="{{ route('web.blog.index') }}"
                                    class="fts-14 fw-4 white-color70-n mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_blog') }}</a>
                            </li>
                            <li><a href="{{ route('web.event.index') }}"
                                    class="fts-14 fw-4 white-color70-n mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_events') }}</a>
                            </li>
                            <li><a href="{{ route('web.weddingVendors.index') }}"
                                    class="fts-14 fw-4 white-color70-n mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_wedding_vendors') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-8 mt-3 mt-lg-0 px-1">
                    <div class="footer-right-part">
                        <div class="social-footer mt-2 pt-lg-1">
                            <div class="fts-18 fw-7 white-color-n">{{ __('messages.lbl_follow_us') }}</div>
                            <div class="social-footers-home d-flex gap-2 flex-wrap mt-1">
                                @if ($configArr['instagram_link'] != '')
                                    <a target="_blank" href="{{ $configArr['instagram_link'] }}" class="fts-20"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-custom-class="custom-tooltip"
                                        data-bs-title="{{ __('messages.lbl_instagram') }}"><iconify-icon
                                            icon="skill-icons:instagram"></iconify-icon></a>
                                @endif
                                @if ($configArr['facebook_link'] != '')
                                    <a target="_blank" href="{{ $configArr['facebook_link'] }}" class="fts-20"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-custom-class="custom-tooltip"
                                        data-bs-title="{{ __('messages.lbl_facebook') }}"><iconify-icon
                                            icon="logos:facebook"></iconify-icon></a>
                                @endif
                                @if ($configArr['youtube_link'] != '')
                                    <a target="_blank" href="{{ $configArr['youtube_link'] }}" class="fts-20"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-custom-class="custom-tooltip"
                                        data-bs-title="{{ __('messages.lbl_youtube') }}"><iconify-icon
                                            icon="logos:youtube-icon" class="fts-16"></iconify-icon></a>
                                @endif
                                @if ($configArr['twitter_link'] != '')
                                    <a target="_blank" href="{{ $configArr['twitter_link'] }}" class="fts-20"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-custom-class="custom-tooltip"
                                        data-bs-title="{{ __('messages.lbl_twitter') }}"><iconify-icon
                                            icon="ri:twitter-x-fill"></iconify-icon></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copy-right-footers black-bgcolor2-n py-2 mt-lg-4 mt-3">
            <div class="container">
                <div class="d-lg-flex d-md-flex text-center align-items-center justify-content-between">
                    <div class="fts-14 white-color70-n fw-5 py-1 opacity-75">{{ $configArr['footer_text'] }}</div>
                </div>
            </div>
        </div>
    </footer>


    <!-- progress bar bottom to top  -->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>

    <!-- all js file include  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('storage/web/affiliate/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('storage/web/affiliate/js/select-2.js') }}"></script>
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>
    <script src="{{ asset('storage/web/affiliate/js/slick.min.js') }}"></script>
    <script src="{{ asset('storage/web/affiliate/js/main.js') }}"></script>

    {{-- common.js --}}
    <script>
        const csrfToken = "{{ csrf_token() }}";
        var lbl_loading = "{{ __('messages.lbl_loading') }}";
        var msg_copied = "{{ __('messages.msg_copied') }}";
        var lbl_autofill = "{{ __('messages.lbl_autofill') }}";
    </script>
    <script src="{{ asset('storage/web/custom/js/common.js') }}"></script>

    @stack('scripts')
</body>

</html>

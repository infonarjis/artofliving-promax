@extends(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.layouts.web_layout')
@section('affiliate_content')
    <!-- header section start  -->
    <header class="affiliate-header-main pb-4 pb-lg-5 pt-3">
        <div class="affeliate-header-outer-back">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="left-affiliate-header py-3 me-xxl-5">
                            <h1 class="fts-50 fw-7 white-color-n">
                                {{ $resultListArr->banner_title }}
                            </h1>
                            <p class="fts-15 fw-4 white-color-n opacity-75 mt-2 mt-lg-3">
                                {{ $resultListArr->banner_subtitle }}
                            </p>
                            <div class="d-flex gap-2 mt-lg-3 mt-2">
                                <a href="{{ route('affiliate.register.index') }}"
                                    class="btn-border-header fts-15">{{ __('messages.lbl_join_now') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <img src="{{ _assetUrl('upload_path.BANNER_IMAGE_URL') . $resultListArr->banner_img }}"
                            alt="{{ $resultListArr->banner_title }}" class="w-100 px-sm-5">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Benefits affiliate section  -->
    <section class="affiliate-work-main py-4 py-lg-5" id="how-its-works">
        <div class="container">
            <div class="col-xl-10 mx-auto">
                <div class="common-afiliate-heading text-center">
                    <h2 class="fts-36 fw-7 white-color-n">{{ $resultListArr->how_it_works_title }}</h2>
                    <p class="fts-15 fw-4 white-color70-n">
                        {{ $resultListArr->how_it_works_subtitle }}
                    </p>
                </div>
                <div class="row mt-2 mt-lg-4">
                    <div class="col-lg-4 col-sm-6 py-2">
                        <div class="work-affiliat-single position-relative text-center">
                            <img src="{{ asset('storage/web/') }}/affiliate/images/benefits-sm-1.png" class="benifit-img-sm"
                                alt="">
                            <img src="{{ asset('storage/web/') }}/affiliate/images/affiliat-work-01.png"
                                alt="Sign up for free" class="affiliat-works">
                            <h4 class="fts-18 fw-6 white-color-n mt-2 mt-lg-3">
                                {{ $resultListArr->how_it_works_section1_title }}</h4>
                            <p class="fts-14 fw-4 white-color70-n mt-1">{{ $resultListArr->how_it_works_section1_subtitle }}
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 py-2">
                        <div class="work-affiliat-single position-relative text-center">
                            <img src="{{ asset('storage/web/') }}/affiliate/images/benefits-sm-2.png"
                                class="benifit-img-sm" alt="">
                            <img src="{{ asset('storage/web/') }}/affiliate/images/affiliat-work-02.png"
                                alt="Share your unique referral link" class="affiliat-works">
                            <h4 class="fts-18 fw-6 white-color-n mt-2 mt-lg-3">
                                {{ $resultListArr->how_it_works_section2_title }}</h4>
                            <p class="fts-14 fw-4 white-color70-n mt-1">{{ $resultListArr->how_it_works_section2_subtitle }}
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 py-2">
                        <div class="work-affiliat-single position-relative text-center">
                            <img src="{{ asset('storage/web/') }}/affiliate/images/benefits-sm-3.png"
                                class="benifit-img-sm" alt="">
                            <img src="{{ asset('storage/web/') }}/affiliate/images/affiliat-work-03.png"
                                alt="Earn commissions on each referral" class="affiliat-works">
                            <h4 class="fts-18 fw-6 white-color-n mt-2 mt-lg-3">
                                {{ $resultListArr->how_it_works_section3_title }}</h4>
                            <p class="fts-14 fw-4 white-color70-n mt-1">{{ $resultListArr->how_it_works_section3_subtitle }}
                            </p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('affiliate.register.index') }}"
                    class="btn-affiliate-common fts-14 mt-2 mt-lg-3">{{ __('messages.lbl_start_earning_today') }}</a>
            </div>
        </div>
    </section>

    <!-- why affiliate trust section start  -->
    <section class="trust-affiliate-main py-4 py-lg-5 black-bgcolor1-n">
        <div class="container">
            <div class="row px-lg-5">
                <div class="col-lg-6">
                    <div class="position-relative mx-4">
                        <img src="{{ _assetUrl('upload_path.BANNER_IMAGE_URL') . $resultListArr->why_affiliate_banner }}"
                            alt="Affiliate Banner" class="w-100">
                    </div>
                </div>
                <div class="col-lg-6 mt-3">
                    <div class="right-why-trust">
                        <h2 class="fts-36 fw-7 white-color-n">{!! $resultListArr->why_affiliate_title !!}</h2>
                        <p class="fts-15 fw-4 white-color70-n">{{ $resultListArr->why_affiliate_subtitle }}</p>
                        <div class="trusted-views-aflt">
                            <div class="single-trusted-flt my-3 my-lg-4 d-flex gap-3 align-items-center">
                                <div class="sm-trusted-bx"><iconify-icon icon="mingcute:happy-fill"></iconify-icon>
                                </div>
                                <div class="content-trusted-rty">
                                    <h4 class="fts-20 fw-7 white-color-n">{{ $resultListArr->why_affiliate_sec1_title }}
                                    </h4>
                                    <p class="fts-14 fw-4 white-color-n">{{ $resultListArr->why_affiliate_sec1_subtitle }}
                                    </p>
                                </div>
                            </div>
                            <div class="single-trusted-flt my-3 my-lg-4 d-flex gap-3 align-items-center">
                                <div class="sm-trusted-bx"><iconify-icon icon="solar:dollar-bold"
                                        style="color: #3B82F6;"></iconify-icon></div>
                                <div class="content-trusted-rty">
                                    <h4 class="fts-20 fw-7 white-color-n">{{ $resultListArr->why_affiliate_sec2_title }}
                                    </h4>
                                    <p class="fts-14 fw-4 white-color-n">{{ $resultListArr->why_affiliate_sec2_subtitle }}
                                    </p>
                                </div>
                            </div>
                            <div class="single-trusted-flt my-3 my-lg-4 d-flex gap-3 align-items-center">
                                <div class="sm-trusted-bx"><iconify-icon icon="tdesign:heart-filled"
                                        style="color: #22C55E;"></iconify-icon></div>
                                <div class="content-trusted-rty">
                                    <h4 class="fts-20 fw-7 white-color-n">{{ $resultListArr->why_affiliate_sec3_title }}
                                    </h4>
                                    <p class="fts-14 fw-4 white-color-n">{{ $resultListArr->why_affiliate_sec3_subtitle }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works section  -->
    <section class="affiliate-work-main py-4 py-lg-5">
        <div class="container">
            <div class="col-xl-10 mx-auto">
                <div class="common-afiliate-heading text-center">
                    <h2 class="fts-36 fw-7 white-color-n">{{ $resultListArr->whychooseus_title }}</h2>
                    <p class="fts-15 fw-4 white-color70-n">{{ $resultListArr->whychooseus_subtitle }}</p>
                </div>
                <div class="row mt-2 mt-lg-4 position-relative">
                    <div class="col-lg-4 col-sm-6 py-2">
                        <div class="work-affiliat-single position-relative text-center">
                            <img src="{{ asset('storage/web/') }}/affiliate/images/benefits-sm-1.png"
                                class="benifit-img-sm" alt="">
                            <div class="work-steps-number">1</div>
                            <h4 class="fts-18 fw-6 white-color-n mt-2 mt-lg-3 pt-lg-1">
                                {{ $resultListArr->whychooseus_sec1_title }}</h4>
                            <p class="fts-14 fw-4 white-color70-n mt-1">{{ $resultListArr->whychooseus_sec1_subtitle }}</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 py-2">
                        <div class="work-affiliat-single position-relative text-center">
                            <img src="{{ asset('storage/web/') }}/affiliate/images/benefits-sm-2.png"
                                class="benifit-img-sm" alt="">
                            <div class="work-steps-number nm2">2</div>
                            <h4 class="fts-18 fw-6 white-color-n mt-2 mt-lg-3 pt-lg-1">
                                {{ $resultListArr->whychooseus_sec2_title }}</h4>
                            <p class="fts-14 fw-4 white-color70-n mt-1">{{ $resultListArr->whychooseus_sec2_subtitle }}</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 py-2">
                        <div class="work-affiliat-single position-relative text-center">
                            <img src="{{ asset('storage/web/') }}/affiliate/images/benefits-sm-3.png"
                                class="benifit-img-sm" alt="">
                            <div class="work-steps-number nm3">3</div>
                            <h4 class="fts-18 fw-6 white-color-n mt-2 mt-lg-3 pt-lg-1">
                                {{ $resultListArr->whychooseus_sec3_title }}</h4>
                            <p class="fts-14 fw-4 white-color70-n mt-1">{{ $resultListArr->whychooseus_sec3_subtitle }}</p>
                        </div>
                    </div>
                </div>
                <div class="black-bgcolor1-n p-3 mt-2 mt-lg-4 rounded-3">
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="affiliate-service py-2 text-center">
                                <h4 class="fts-28 fw-7 s-color-1">{{ $resultListArr->whychooseus_sec4_title }}</h4>
                                <p class="fts-14 fw-4 white-color70-n">{{ $resultListArr->whychooseus_sec4_subtitle }}</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="affiliate-service py-2 text-center">
                                <h4 class="fts-28 fw-7 s-color-2">{{ $resultListArr->whychooseus_sec5_title }}</h4>
                                <p class="fts-14 fw-4 white-color70-n">{{ $resultListArr->whychooseus_sec5_subtitle }}</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="affiliate-service py-2 text-center">
                                <h4 class="fts-28 fw-7 s-color-3">{{ $resultListArr->whychooseus_sec6_title }}</h4>
                                <p class="fts-14 fw-4 white-color70-n">{{ $resultListArr->whychooseus_sec6_subtitle }}</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="affiliate-service py-2 text-center">
                                <h4 class="fts-28 fw-7 s-color-4">{{ $resultListArr->whychooseus_sec7_title }}</h4>
                                <p class="fts-14 fw-4 white-color70-n">{{ $resultListArr->whychooseus_sec7_subtitle }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features section start -->
    <section class="affiliate-feature-section black-bgcolor1-n py-4 py-lg-5" id="features">
        <div class="container">
            <div class="common-afiliate-heading text-center">
                <h2 class="fts-36 fw-7 white-color-n">{{ $resultListArr->affiliate_feature_title }}</h2>
                <p class="fts-15 fw-4 white-color70-n">{{ $resultListArr->affiliate_feature_subtitle }}</p>
            </div>
            <div class="row mt-2 mt-lg-3">
                @php
                    $features = [
                        [
                            'class' => 'feature-1',
                            'icon' => 'hugeicons:chart-03',
                            'title' => $resultListArr->affiliate_feature_sec1_title,
                            'subtitle' => $resultListArr->affiliate_feature_sec1_subtitle,
                        ],
                        [
                            'class' => 'feature-2',
                            'icon' => 'proicons:dollar',
                            'title' => $resultListArr->affiliate_feature_sec2_title,
                            'subtitle' => $resultListArr->affiliate_feature_sec2_subtitle,
                        ],
                        [
                            'class' => 'feature-3',
                            'icon' => 'mingcute:link-line',
                            'title' => $resultListArr->affiliate_feature_sec3_title,
                            'subtitle' => $resultListArr->affiliate_feature_sec3_subtitle,
                        ],
                        [
                            'class' => 'feature-4',
                            'icon' => 'ion:bag-check-outline',
                            'title' => $resultListArr->affiliate_feature_sec4_title,
                            'subtitle' => $resultListArr->affiliate_feature_sec4_subtitle,
                        ],
                        [
                            'class' => 'feature-5',
                            'icon' => 'streamline-ultimate:headphones-customer-support-question',
                            'title' => $resultListArr->affiliate_feature_sec5_title,
                            'subtitle' => $resultListArr->affiliate_feature_sec5_subtitle,
                        ],
                        [
                            'class' => 'feature-6',
                            'icon' => 'lsicon:marketplace-outline',
                            'title' => $resultListArr->affiliate_feature_sec6_title,
                            'subtitle' => $resultListArr->affiliate_feature_sec6_subtitle,
                        ],
                    ];
                @endphp

                <div class="row mt-2 mt-lg-3">

                    @foreach ($features as $feature)
                        <div class="col-lg-4">

                            <div class="common-feature-box {{ $feature['class'] }} p-3 p-md-4 my-2">

                                <div class="feature-icon">
                                    <iconify-icon icon="{{ $feature['icon'] }}"></iconify-icon>
                                </div>

                                <div class="feature-contents mt-2 pt-1 pe-lg-4">

                                    <h4 class="fts-16 fw-6 white-color-n">
                                        {{ $feature['title'] }}
                                    </h4>

                                    <p class="fts-14 fw-4 white-color70-n mt-1">
                                        {{ $feature['subtitle'] }}
                                    </p>

                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>
            </div>
            <a href="{{ route('affiliate.login.index') }}" class="btn-affiliate-common fts-14 mt-2 mt-lg-3">
                {{ __('messages.lbl_explore_all_features') }}
            </a>
        </div>
    </section>

    <!-- testimonial section start -->
    <section class="affiliate-testimonial-section py-4 py-lg-5" id="testimonials">
        <div class="container">
            <div class="common-afiliate-heading text-center">
                <h2 class="fts-36 fw-7 white-color-n">{{ $resultListArr->affiliate_testimonial_title }}</h2>
                <p class="fts-15 fw-4 white-color70-n">{{ $resultListArr->affiliate_testimonial_subtitle }}</p>
            </div>
            <div class="review-slider mt-1">
                @foreach ($testimonial as $item)
                    <div class="single-review-box p-3 mx-2 my-3">
                        <p class="fts-15 fw-4 white-color-n">"{{ $item->description }}"</p>
                        <div class="review-user-flex d-flex gap-2 mt-2 pt-1">
                            <div class="review-user">
                                <img src="{{ _assetUrl('upload_path.AFFILIATE_TESTIMONIAL_IMG') . $item->image }}"
                                    alt="{{ $item->name }}" class="review-user-img">
                            </div>
                            <div class="review-user-contents">
                                <h4 class="fts-15 fw-6 white-color-n">{{ $item->name }}</h4>
                                <p class="fts-13 white-color70-n fw-4">{{ $item->designation }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

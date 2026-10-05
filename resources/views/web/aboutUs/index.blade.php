@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
{{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="row px-1">
                    <div class="col-xxl-9 col-lg-9 px-2">
                        <div class="common-bgwhite-main p-3 p-md-4">
                            <div class="about-toplistimgsmain px-lg-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="left-textabtdfngvd">
                                            <div class="fw-7 fts-24 white-color-n">{{ $resultArr->about_us_title }}</div>
                                            <div class="fts-15 fw-4 white-color70-n mt-2">
                                                {{ $resultArr->about_us_sub_title }}
                                            </div>
                                            <ul class="listabout-items mt-lg-4">
                                                <li class="fts-15 mt-lg-3 mt-2">
                                                    <iconify-icon icon="heroicons:check-badge"
                                                        class="me-1 fts-20"></iconify-icon>
                                                    {{ $resultArr->about_us_brow_sec1 }}
                                                </li>
                                                <li class="fts-15 mt-lg-3 mt-2">
                                                    <iconify-icon icon="heroicons:check-badge"
                                                        class="me-1 fts-20"></iconify-icon>
                                                    {{ $resultArr->about_us_brow_sec2 }}
                                                </li>
                                                <li class="fts-15 mt-lg-3 mt-2">
                                                    <iconify-icon icon="heroicons:check-badge"
                                                        class="me-1 fts-20"></iconify-icon>
                                                    {{ $resultArr->about_us_brow_sec3 }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <div class="aboutus-contents-div">
                                            <p class="fts-14 white-color70-n fw-4 mb-2">{!! $resultArr->about_us_small_desc !!}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="story-imagecoupl mt-3 mt-lg-4">
                                @php
                                    $aboutUsBanner = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                                    if (
                                        !blank($resultArr->about_us_image) &&
                                        _checkStorageFileExists(
                                            'upload_path.OTHER_IMAGE_URL',
                                            $resultArr->about_us_image,
                                        )
                                    ) {
                                        $aboutUsBanner =
                                            _assetUrl('upload_path.OTHER_IMAGE_URL') . $resultArr->about_us_image;
                                    }
                                @endphp
                                <img src="{{ $aboutUsBanner }}" alt="Banner" class="big_storyprofils">
                            </div>
                            <div class="aboutus-contents-div mt-3 mt-lg-4 pt-1 px-lg-2">
                                <h1 class="fw-7 white-color-n fts-24 mb-1 mb-lg-2">{{ _getLang('lbl_about_us') }}</h1>
                                <div class="fts-14 white-color70-n fw-4 mb-2 about-us-desc">
                                    {!! _convertOembedToIframe($resultArr->about_us_desc) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-lg-3 px-2">
                        <div class="pro-common-leftbar mb-3">
                            <div class="apps-profiles p-3">
                                <h4 class="fw-6 fts-18 white-color-n pe-lg-5">{{ __('messages.lbl_best_way_to_manage_your_profile') }}</h4>
                                <div class="left-sidebar-apps d-flex gap-2 mt-2">
                                    @if(isset($configArr['ios_app_link']) && !blank($configArr['ios_app_link']))
                                        <a href="{{ $configArr['ios_app_link'] }}">
                                            <img src="{{ asset('storage/web/') }}/assets/images/icon-app-store.png" alt="App Store" class="w-100">
                                        </a>
                                    @endif
                                    @if(isset($configArr['android_app_link']) && !blank($configArr['android_app_link']))
                                        <a href="{{ $configArr['android_app_link'] }}">
                                            <img src="{{ asset('storage/web/') }}/assets/images/icon-playstore.png" alt="Play Store" class="w-100">
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @include('web.dashboard.memberRightSideBar')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

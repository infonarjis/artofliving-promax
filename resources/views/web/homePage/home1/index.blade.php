@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <!-- header section start  -->
    <header class="hiro-header-common pt-4 pt-lg-5">
        <div class="inner-promatri-header position-relative">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 py-3">
                        <div class="header-content-text">
                            <h1 class="fts-50 fw-7 white-color-n">{{ $homePageData->banner_section_title }}</h1>
                            <p class="white-color-n fw-4 fts-15 mt-1 mt-lg-2">{{ $homePageData->banner_section_subtitle }}
                            </p>
                            <div class="user-story-header d-flex flex-wrap gap-2 mt-3 mt-lg-4 pt-2">
                                <div class="header-st-icon">
                                    <iconify-icon icon="iconamoon:heart-light"></iconify-icon>
                                </div>
                                <h2 class="white-color-n fw-6 fts-24 ms-1">{{ $homePageData->banner_section_story_count }}
                                    <span class="d-block fts-14">{{ $homePageData->banner_section_story_text }}</span>
                                </h2>
                                <div class="header-users-img ms-3 ps-1">
                                    @foreach ($successStoryArr as $item)
                                        @if (
                                            !blank($item->wedding_photo) &&
                                                _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $item->wedding_photo))
                                            <img src="{{ _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $item->wedding_photo }}"
                                                alt="" class="h-users-img">
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <!-- Search for your right partner Starts -->
                        <div class="for-search-relative wow fadeInUp mt-4 mt-lg-5">
                            <form class="searchForm" action="{{ route('web.search.searchResult') }}" method="GET">
                                <div class="row align-items-end">
                                    <div class="col-lg-3 custom-width-gender">
                                        <p class="fts-14 fw-5 white-color-n">{{ __('messages.lbl_i_m_looking_for_a') }}</p>
                                        <div class="left">
                                            <select name="gender" id="gender" class="custom-select sources">
                                                <option value="Male" title="Male">{{ __('messages.field_lbl_male') }}
                                                </option>
                                                <option value="Female" title="Female">{{ __('messages.field_lbl_female') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 custom-width-age">
                                        <p class="fts-14 fw-5 white-color-n">{{ __('messages.field_lbl_age') }}</p>
                                        <div class="d-flex align-items-center w-100 gap-2">
                                            @php $age = _ageRang(); @endphp
                                            <div class="age-search w-100">
                                                <div class="left">
                                                    <select name="part_frm_age" id="part_frm_age" class="custom-select sources">
                                                        @foreach ($age as $key => $valueArr)
                                                            <option value="{{ $key }}"
                                                                title="{{ $valueArr }}">
                                                                {{ $valueArr }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="fw-5 fts-14 white-color-n mx-2">{{ __('messages.field_lbl_to') }}
                                            </div>
                                            <div class="age-search w-100">
                                                <div class="left">
                                                    <select name="part_to_age" id="part_to_age" class="custom-select sources">
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
                                    </div>
                                    <div class="col-lg-3 custom-width-religion">
                                        <p class="fts-14 fw-5 white-color-n">{{ __('messages.field_lbl_religion') }}</p>
                                        <div class="left pe-lg-3">
                                            <select name="religion" id="religion" class="custom-select sources">
                                                <option class="list" value='' selected
                                                    title="{{ __('messages.field_lbl_select_religion') }}">
                                                    {{ __('messages.field_lbl_select_religion') }}</option>
                                                @foreach ($religionList as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <button type="submit" class="searchnow fts-15 mx-auto mt-2 mt-lg-0">
                                            {{ __('messages.lbl_search') }}
                                            <iconify-icon icon="quill:arrow-right"></iconify-icon></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="header-right-bg px-3 px-lg-0 mt-3">
                            <img src="{{ _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $homePageData->banner_section_image }}"
                                alt="Banner" class="w-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- how does work steps -->
    <section class="wrok-steps-section py-4 py-lg-5">
        <div class="container">
            <div class="section_title text-center">
                <div class="fts-32 fw-7 white-color-n">{{ $homePageData->whychooseus_title }}</div>
                <p class="fts-14 fw-4 white-color70-n">{{ $homePageData->whychooseus_subtitle }}</p>
            </div>
            <div class="row px-1 mt-2 mt-md-3">
                <div class="col-lg-3 col-sm-6 px-2 py-2">
                    <div class="how-works-box text-center h-100">
                        <div class="how-works-icons mx-auto">
                            <iconify-icon icon="stash:shield-user"></iconify-icon>
                        </div>
                        <div class="how-works-contents mt-2 pt-1">
                            <h2 class="fts-18 fw-6 white-color-n">{{ $homePageData->whychooseus_sec1_title }}</h2>
                            <p class="fts-14 fw-4 white-color70-n mt-1">{{ $homePageData->whychooseus_sec1_subtitle }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 px-2 py-2">
                    <div class="how-works-box clr-1 text-center h-100">
                        <div class="how-works-icons mx-auto">
                            <iconify-icon icon="hugeicons:security"></iconify-icon>
                        </div>
                        <div class="how-works-contents mt-2 pt-1">
                            <h2 class="fts-18 fw-6 white-color-n">{{ $homePageData->whychooseus_sec2_title }}</h2>
                            <p class="fts-14 fw-4 white-color70-n mt-1">{{ $homePageData->whychooseus_sec2_subtitle }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 px-2 py-2">
                    <div class="how-works-box clr-2 text-center h-100">
                        <div class="how-works-icons mx-auto">
                            <iconify-icon icon="solar:heart-outline"></iconify-icon>
                        </div>
                        <div class="how-works-contents mt-2 pt-1">
                            <h2 class="fts-18 fw-6 white-color-n">{{ $homePageData->whychooseus_sec3_title }}</h2>
                            <p class="fts-14 fw-4 white-color70-n mt-1">{{ $homePageData->whychooseus_sec3_subtitle }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 px-2 py-2">
                    <div class="how-works-box clr-3 text-center h-100">
                        <div class="how-works-icons mx-auto">
                            <iconify-icon icon="streamline:call-center-support-service"></iconify-icon>
                        </div>
                        <div class="how-works-contents mt-2 pt-1">
                            <h2 class="fts-18 fw-6 white-color-n">{{ $homePageData->whychooseus_sec4_title }}</h2>
                            <p class="fts-14 fw-4 white-color70-n mt-1">{{ $homePageData->whychooseus_sec4_subtitle }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Profiles section -->
    @if ($latestProfile->isNotEmpty())
        <section class="meet-member-section py-4 py-lg-5">
            <div class="container">
                <div class="section_title text-center">
                    <div class="fts-32 fw-7 white-color-n">{{ $homePageData->last_profile_title }}</div>
                    <p class="fts-14 fw-4 white-color70-n">{{ $homePageData->last_profile_subtitle }}</p>
                </div>
                <div class="meet-member-slider mt-2 mt-lg-3">
                    @foreach ($latestProfile as $item)
                        <div class="single-member-box position-relative mx-2">
                            <a href="{{ route('web.userProfile.index', _encrypt($item->id)) }}">
                                <div class="meet-member-profile mx-auto">
                                    @php
                                        $canView = _canViewMemberPhoto($item, $item->hasPhotoRequestAccess ?? '');

                                        $hasPhoto = _checkPhotoExist($item);
                                        $profileImage = _getMemberProfileImage($item);
                                    @endphp
                                    @if (!$canView && $hasPhoto)
                                        <img src="{{ _getProtectedImage($item->gender) }}"
                                            alt="{{ _profileTitle($item) }}" class="meet-profile-member">
                                    @else
                                        <img src="{{ $profileImage }}" alt="{{ _profileTitle($item) }}"
                                            class="meet-profile-member">
                                    @endif
                                </div>
                            </a>
                            <div class="meet-member-contents mt-2">
                                <div class="left-text-meet text-center">
                                    <h2 class="fts-18 fw-6 white-color-n">{{ _profileTitle($item) }}</h2>
                                    <p class="fts-14 fw-4 white-color70-n">{{ _getMemberAgeHeight($item) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- success stories section start  -->
    @if ($successStoryArr->isNotEmpty())
        <section class="home-stories-section">
            <div class="container">
                <div class="section_title text-center col-lg-7 mx-auto">
                    <div class="fts-32 fw-7 white-color-n">{{ $homePageData->success_story_title }}</div>
                    <p class="fts-14 fw-4 white-color70-n">{{ $homePageData->success_story_subtitle }}</p>
                </div>
                <div class="home-stories-inner d-flex flex-wrap flex-lg-nowrap gap-sm-3 gap-2 mt-3 mt-lg-4">
                    @foreach ($successStoryArr as $key => $item)
                        @php
                            $weddingImage = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                            if (
                                !blank($item->wedding_photo) &&
                                _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $item->wedding_photo)
                            ) {
                                $weddingImage = _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $item->wedding_photo;
                            }
                            $bridegroomName = $item->groomname . ' & ' . $item->bridename;
                        @endphp
                        <div class="single-stories-web {{ $key == 1 ? 'active' : '' }}">
                            <a href="{{ route('web.successStory.details', $item->id) }}">
                                <img src="{{ $weddingImage }}" alt="{{ $bridegroomName }}" class="home-str-img">
                            </a>
                            <h2 class="fts-18">{{ $bridegroomName }}</h2>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <div class="bg-personalized-apps">
        <!-- personalized section start -->
        <section class="home-personalized-section pt-4 pt-lg-5">
            <div class="container">
                <div class="home-personalized-box p-3 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-xxl-8 col-lg-8">
                            <div class="home-left-personalized">
                                <img src="{{ _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $homePageData->personalize_logo }}"
                                    alt="logo" class="personalize-logo">
                                <h2 class="white-color-n fts-36 fw-6 mt-3 mt-md-4">{{ $homePageData->personalize_title }}
                                </h2>
                                <a target="_blank" href="{{ route('web.personalize.index') }}"
                                    class="btn-consolation fts-15 mt-2 mt-md-3"
                                    aria-label="Learn more about personalizing your matrimonial profile">
                                    {{ __('messages.lbl_learn_more') }}
                                </a>
                            </div>
                        </div>
                        <div class="col-xxl-4 col-lg-4">
                            <div class="home-right-personalized mx-5 position-relative">
                                <img src="{{ _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $homePageData->personalize_image }}"
                                    alt="Personalize Chat" class="w-100">
                                <div class="personalize-matches-box position-1">
                                    <div class="box-matches-icon"><iconify-icon
                                            icon="stash:shield-check-light"></iconify-icon></div>
                                    <div class="content-matches-text">
                                        <p class="white-color-n fw-5 fts-15">{!! $homePageData->personalize_text1 !!}</p>
                                    </div>
                                </div>
                                <div class="personalize-matches-box position-2">
                                    <div class="box-matches-icon"><iconify-icon icon="formkit:time"></iconify-icon></div>
                                    <div class="content-matches-text">
                                        <p class="white-color-n fw-5 fts-15">{!! $homePageData->personalize_text2 !!}</p>
                                    </div>
                                </div>
                                <div class="personalize-matches-box position-3">
                                    <div class="box-matches-icon"><iconify-icon icon="iconoir:heart"></iconify-icon></div>
                                    <div class="content-matches-text">
                                        <p class="white-color-n fw-5 fts-15">{!! $homePageData->personalize_text3 !!}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Best way to manage section -->
        <section class="manage-way-section py-4 py-lg-5">
            <div class="container">
                <div class="apps-away-bgmain px-3 py-3 py-md-5">
                    <div class="row align-items-center">
                        <div class="col-lg-6 mt-3 mt-lg-0">
                            <div class="manage-way-leftside ms-xxl-3 mb-3">
                                @php
                                    $mobileSection = preg_split('/\s+/', trim($homePageData->mobile_section_subtext));
                                @endphp
                                <h2 class="fts-16 fw-5 white-color-p mb-3 mb-lg-4">
                                    @foreach ($mobileSection as $value)
                                        {{ $value }}
                                        @if (!$loop->last)
                                            <span></span>
                                        @endif
                                    @endforeach
                                </h2>
                                <h2 class="fw-7 fts-50 white-color-p">{{ $homePageData->mobile_section_title }}</h2>
                                <div class="apps-playstore d-flex gap-2 mt-3 mt-lg-4">
                                    @if (isset($configArr['ios_app_link']) && !blank($configArr['ios_app_link']))
                                        <a target="_blank" href="{{ $configArr['ios_app_link'] }}"><img
                                                src="{{ asset('storage/web/') }}/assets/images/icon-app-store.png"
                                                alt="{{ __('messages.lbl_app_store') }}"></a>
                                    @endif
                                    @if (isset($configArr['android_app_link']) && !blank($configArr['android_app_link']))
                                        <a target="_blank" href="{{ $configArr['android_app_link'] }}"><img
                                                src="{{ asset('storage/web/') }}/assets/images/icon-playstore.png"
                                                alt="{{ __('messages.lbl_play_store') }}">
                                        </a>
                                    @endif
                                </div>
                                <div class="appusesesusermain d-flex align-items-center gap-3 mt-3 mt-md-4">
                                    <div class="usersappsgroup ms-4">
                                        @foreach ($latestProfile->take(5) as $item)
                                            <img src="{{ _getMemberProfileImage($item) }}"
                                                alt="{{ _profileTitle($item) }}" class="commanprofile users5">
                                        @endforeach
                                    </div>
                                    <p class="fts-14 fw-6 white-color-p">
                                        {{ $homePageData->mobile_tag_line }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <img src="{{ _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $homePageData->mobile_banner }}"
                                alt="Mobile Banner" class="w-100">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Why Thousands Trust Love Matrimonial section -->
    <section class="why-matrimony-section py-4 py-lg-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="section_title pe-lg-5 me-lg-5">
                        <h2 class="fts-32 fw-7 white-color-n">{{ $homePageData->aboutus_title }}</h2>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="section_title ps-lg-5 ms-lg-5">
                        <p class="fts-15 fw-4 white-color70-n text-lg-end">
                            {{ $homePageData->aboutus_subtitle }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="row mt-3 mt-lg-4">
                <div class="col-lg-5">
                    <img src="{{ asset('storage/web/') }}/assets/images/trusted-img.png" alt=""
                        class="w-100 px-5">
                </div>
                <div class="col-lg-7">
                    <div class="right-whytrust-matri mt-3 mt-lg-0">
                        <p class="fts-15 fw-4 white-color-n">
                            {{ $homePageData->aboutus_description }}
                        </p>
                        <div class="multie-trusted-step">
                            <div class="row mt-2 px-2">
                                <div class="col-md-6 pt-2 px-1">
                                    <div class="single-trusted-box mt-4">
                                        <div class="trusted-step-icon">
                                            <iconify-icon icon="stash:user-id"></iconify-icon>
                                        </div>
                                        <h2 class="fts-16 fw-6 white-color-n mt-2">{{ $homePageData->aboutus_sec_title1 }}
                                        </h2>
                                        <p class="fts-14 fw-4 white-color70-n">{{ $homePageData->aboutus_sec_subtitle1 }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 pt-2 px-1">
                                    <div class="single-trusted-box mt-4">
                                        <div class="trusted-step-icon">
                                            <iconify-icon icon="ri:ai"></iconify-icon>
                                        </div>
                                        <h2 class="fts-16 fw-6 white-color-n mt-2">{{ $homePageData->aboutus_sec_title2 }}
                                        </h2>
                                        <p class="fts-14 fw-4 white-color70-n">{{ $homePageData->aboutus_sec_subtitle2 }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 pt-2 px-1">
                                    <div class="single-trusted-box mt-4">
                                        <div class="trusted-step-icon">
                                            <iconify-icon icon="stash:private-content"></iconify-icon>
                                        </div>
                                        <h2 class="fts-16 fw-6 white-color-n mt-2">{{ $homePageData->aboutus_sec_title3 }}
                                        </h2>
                                        <p class="fts-14 fw-4 white-color70-n">{{ $homePageData->aboutus_sec_subtitle3 }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 pt-2 px-1">
                                    <div class="single-trusted-box mt-4">
                                        <div class="trusted-step-icon">
                                            <iconify-icon icon="fluent:person-support-28-regular"></iconify-icon>
                                        </div>
                                        <h2 class="fts-16 fw-6 white-color-n mt-2">{{ $homePageData->aboutus_sec_title4 }}
                                        </h2>
                                        <p class="fts-14 fw-4 white-color70-n">{{ $homePageData->aboutus_sec_subtitle4 }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- community section start -->
    @if ($matrimonyPagesData->isNotEmpty())
        <section class="explore-community-main py-4 py-lg-5">
            <div class="container">
                <div class="section_title col-md-8 mx-auto text-center">
                    <div class="fts-32 fw-6 white-color-n">{{ $homePageData->browse_matrimony_title }}</div>
                    <p class="fts-15 fw-4 white-color-n">
                        {{ $homePageData->browse_matrimony_subtitle }}
                    </p>
                </div>
                <div class="col-lg-7 col-xxl-6 mx-auto">
                    <div class="community-inner-main mt-3 mt-lg-4">
                        <ul class="nav nav-pills" id="matrimony-pills-tab" role="tablist" aria-orientation="horizontal">

                            @foreach ($matrimonyPagesData as $type => $items)
                                @php
                                    $tabId = Str::slug($type);
                                @endphp

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link w-100 fts-15 {{ $loop->first ? 'active' : '' }}"
                                        id="{{ $tabId }}-tab" data-bs-toggle="pill"
                                        data-bs-target="#{{ $tabId }}-pane" type="button" role="tab"
                                        aria-controls="{{ $tabId }}-pane"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                        {{ $items['label'] }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content mt-2 pt-lg-2" id="pills-tabContent">
                            @foreach ($matrimonyPagesData as $type => $items)
                                @php
                                    $tabId = Str::slug($type);
                                @endphp
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                    id="{{ $tabId }}-pane" role="tabpanel"
                                    aria-labelledby="{{ $tabId }}-tab">

                                    <div class="matri-comunity-s text-center">
                                        @foreach ($items['items'] as $item)
                                            <a href="{{ route('web.matrimony.index', $item['slug']) }}"
                                                class="fts-14 fw-4 white-color-n d-inline-block mt-1">
                                                {{ $item['matrimony_name'] ?: $item['matrimony_name_old'] ?? 'N/A' }}
                                            </a>

                                            @if (!$loop->last)
                                                <div class="lvg d-inline-block"></div>
                                            @endif
                                        @endforeach

                                        <div class="lvg d-inline-block"></div>
                                        <a href="{{ route('web.matrimony.moreDetails', Str::slug($type)) }}"
                                            class="fts-14 fw-5 primary-color-n">
                                            {{ __('messages.lbl_more_details') }}
                                        </a>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection

@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <!-- dashboard section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="dashboard-layout">
                    {{-- LEFT SIDEBAR (Responsive Off-Canvas Drawer on Mobile) --}}
                    @include('web.dashboard.memberLeftSideBar')

                    <main class="main-content-area" id="main-content">
                        @include('web.dashboard.memberTop')
                        <div class="common-tabs-design">
                            <ul class="nav flex-nowrap d-flex nav-pills p-1" id="pills-tab" role="tablist">
                                <li class="nav-item w-50">
                                    <button class="nav-link fts-15 active" id="myprofile-tab" data-bs-toggle="pill"
                                        data-bs-target="#myprofile" type="button" role="tab" aria-controls="myprofile"
                                        aria-selected="true"><iconify-icon icon="lets-icons:user-add"
                                            class="fts-20 me-1"></iconify-icon>{{ __('messages.lbl_my_profile') }}</button>
                                </li>
                                <li class="nav-item w-50">
                                    <button class="nav-link fts-15" id="partner-profile-tab" data-bs-toggle="pill"
                                        data-bs-target="#partner-profile" type="button" role="tab"
                                        aria-controls="partner-profile" aria-selected="false"><iconify-icon
                                            icon="tabler:users-plus" class="fts-20 me-1"></iconify-icon>
                                        {{ __('messages.lbl_partner_preferences') }}</button>
                                </li>
                            </ul>
                        </div>

                        <div class="common-tablist-design">
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="myprofile" role="tabpanel"
                                    aria-labelledby="myprofile">
                                    @foreach ($memberDetailSections as $section)
                                        <div class="common-bgwhite-main mt-2 py-2 px-3 ps-lg-4 pe-1 pe-sm-3">
                                            <div
                                                class="topcolleps-edtbar d-flex align-items-center justify-content-between py-lg-1">
                                                <h4 class="fts-16 fw-7 white-color-n w-100">{{ $section['label'] }}</h4>
                                                <div class="d-flex align-items-center gap-1">
                                                    <a href="{{ route('web.myProfile.editProfile', $section['section_key']) }}"
                                                        class="btn-editfields primary-btn-hover"><iconify-icon
                                                            icon="lets-icons:edit-fill"></iconify-icon></a>
                                                    <a class="profile-arrow-collapse" data-bs-toggle="collapse"
                                                        href="#{{ $section['section_key'] }}"
                                                        aria-controls="{{ $section['section_key'] }}"
                                                        aria-expanded="true"><iconify-icon
                                                            icon="iconamoon:arrow-down-2"></iconify-icon></a>
                                                </div>
                                            </div>
                                            <div class="collapse show" id="{{ $section['section_key'] }}">
                                                <div class="">
                                                    <div class="row mt-2 px-1">
                                                        @foreach ($section['fields'] as $fields)
                                                            <div class="col-md-4 col-6 mb-3 pb-md-1 px-2">
                                                                <div class="fts-14 fw-5 white-color70-n">
                                                                    {{ $fields['label'] }}</div>
                                                                <div class="fts-14 fw-5 white-color-n mt-1 read-more-box">
                                                                    @php
                                                                        $fieldValue = _displayNotAvailable(
                                                                            $fields['value'],
                                                                        );
                                                                        $limit = 80;
                                                                    @endphp
                                                                    @if (strlen($fieldValue) > $limit)
                                                                        <span class="short-text">
                                                                            {{ \Illuminate\Support\Str::limit($fieldValue, $limit) }}
                                                                        </span>
                                                                        <span
                                                                            class="full-text d-none">{{ $fieldValue }}</span>
                                                                        <span class="fw-6 primary-color-n read-toggle"
                                                                            style="cursor:pointer;">
                                                                            {{ __('messages.lbl_read_more') }}
                                                                        </span>
                                                                    @else
                                                                        @if ($fieldValue == 'N/A')
                                                                            <span class="d-flex align-items-center gap-1">
                                                                                {{ $fieldValue ?? '' }}
                                                                                @if (isset($fields['isRequired']) && $fields['isRequired'] == true)
                                                                                    <iconify-icon
                                                                                    class="text-danger"
                                                                                    icon="heroicons-solid:exclamation"></iconify-icon>
                                                                                @endif
                                                                            </span>
                                                                        @else
                                                                            {{ $fieldValue ?? '' }}
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="tab-pane fade" id="partner-profile" role="tabpanel"
                                    aria-labelledby="partner-profile-tab">
                                    <div class="common-bgwhite-main mt-2 py-2 px-3 ps-lg-4 pe-1 pe-sm-3">
                                        <div
                                            class="topcolleps-edtbar d-flex align-items-center justify-content-between py-lg-1">
                                            <h4 class="fts-16 fw-7 white-color-n w-100">
                                                {{ $memberPartnerSection['label'] }}</h4>
                                            <div class="d-flex align-items-center gap-1">
                                                <a href="{{ route('web.myProfile.editProfile', $memberPartnerSection['section_key']) }}"
                                                    class="btn-editfields primary-btn-hover"><iconify-icon
                                                        icon="lets-icons:edit-fill"></iconify-icon></a>
                                                <a class="profile-arrow-collapse" data-bs-toggle="collapse"
                                                    href="#{{ $memberPartnerSection['section_key'] }}"
                                                    aria-controls="{{ $memberPartnerSection['section_key'] }}"
                                                    aria-expanded="true"><iconify-icon
                                                        icon="iconamoon:arrow-down-2"></iconify-icon></a>
                                            </div>
                                        </div>
                                        <div class="collapse show" id="{{ $memberPartnerSection['section_key'] }}">
                                            <div class="">
                                                <div class="row mt-2 px-1">
                                                    @foreach ($memberPartnerSection['fields'] as $fields)
                                                        <div class="col-md-4 col-6 mb-3 pb-md-1 px-2">
                                                            <div class="fts-14 fw-5 white-color70-n">{{ $fields['label'] }}
                                                            </div>
                                                            <div class="fts-14 fw-5 white-color-n mt-1">
                                                                {{ _displayNotAvailable($fields['value']) }}</div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </section>
@endsection

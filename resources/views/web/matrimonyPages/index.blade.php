@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    <section class="common-section-bg pb-4 pb-lg-5 mt-3">
        <div class="common-section-page">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-3 col-lg-4 pe-lg-0">
                        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.matrimonyPages.matrimonyPageSidebar')
                    </div>
                    <div class="col-xxl-9 col-lg-8 ps-lg-4 mt-3 mt-lg-0">
                        <div class="right-browse-matrimony">
                            <div class="breadcrumb-browse">
                                <iconify-icon icon="mynaui:home-solid" class="fts-18"></iconify-icon>
                                <p class="fts-15">{{ $matrimony->pagename }}</p>
                            </div>
                            <div class="browse-matri-bottommain mt-3">
                                <div class="browse-poster-contents">
                                    @php
                                        $imageURL = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                                        if (
                                            !blank($matrimony->banner_img) &&
                                            _checkStorageFileExists(
                                                'upload_path.COMMUNITY_BANNER_IMAGE_URL',
                                                $matrimony->banner_img,
                                            )
                                        ) {
                                            $imageURL =
                                                _assetUrl('upload_path.COMMUNITY_BANNER_IMAGE_URL') .
                                                $matrimony->banner_img;
                                        }
                                    @endphp
                                    <img src="{{ $imageURL }}" alt="{{ $matrimony->pagename }}" class="browse-banner">
                                    <h4 class="fts-20 fw-6 white-color-n mt-2 pt-1">{!! $matrimony->title !!}</h4>
                                    <p class="fts-14 fw-4 white-color70-n mt-1">{{ $matrimony->matrimony_description }}</p>
                                </div>
                                <div class="browse-profiles-main mt-3 mt-lg-4">
                                    <div class="common-tabs-design">
                                        <ul class="nav flex-nowrap d-flex nav-pills" id="pills-tab" role="tablist">
                                            <li class="nav-item w-50">
                                                <button class="nav-link fts-16 active" id="male-tab" data-bs-toggle="pill"
                                                    data-bs-target="#male" type="button" role="tab"
                                                    aria-controls="male" aria-selected="true"><iconify-icon
                                                        icon="ic:sharp-male"
                                                        class="fts-28"></iconify-icon>{{ __('messages.field_lbl_male') }}</button>
                                            </li>
                                            <li class="nav-item w-50">
                                                <button class="nav-link fts-16" id="female-tab" data-bs-toggle="pill"
                                                    data-bs-target="#female" type="button" role="tab"
                                                    aria-controls="female" aria-selected="false"><iconify-icon
                                                        icon="ic:sharp-female"
                                                        class="fts-28"></iconify-icon>{{ __('messages.field_lbl_female') }}</button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="common-tablist-design mt-3">
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="male" role="tabpanel"
                                                aria-labelledby="male-tab">

                                                <div class="row px-2" id="maleMembers"></div>

                                                <div id="malePagination" class="mt-3"></div>
                                            </div>

                                            <div class="tab-pane fade" id="female" role="tabpanel"
                                                aria-labelledby="female-tab">

                                                <div class="row px-2" id="femaleMembers"></div>

                                                <div id="femalePagination" class="mt-3"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            function loadMembers(gender, page = 1) {
                let memberContainer = gender === 'Male' ? '#maleMembers' : '#femaleMembers';
                $.ajax({
                    url: "{{ route('web.matrimony.members', $matrimony->slug) }}",
                    type: "GET",
                    data: {
                        gender: gender,
                        page: page
                    },
                    // Show loader before fetching data
                    beforeSend: function() {
                        $(memberContainer).html(`
                            <div class="col-12 text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        `);
                    },
                    success: function(response) {
                        $(memberContainer).html(response.html);
                        if (gender === 'Male') {
                            $('#malePagination').html(response.pagination || '');
                        } else {
                            $('#femalePagination').html(response.pagination || '');
                        }
                    },
                    error: function(xhr) {
                        $(memberContainer).html(`
                            <div class="col-12 text-center py-5">
                                <p class="text-danger mb-0">
                                    Something went wrong. Please try again.
                                </p>
                            </div>
                        `);
                        console.log(xhr.responseText);
                    }
                });
            }

            // Initial Male data
            loadMembers('Male');

            // Bootstrap tab change
            $('#pills-tab button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
                let target = $(e.target).data('bs-target');
                if (target === '#male') {
                    loadMembers('Male');
                } else if (target === '#female') {
                    loadMembers('Female');
                }
            });

            // Pagination
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');

                if (!url) {
                    return;
                }

                let page = new URL(url).searchParams.get('page');

                let activeTab = $('#pills-tab .nav-link.active').data('bs-target');

                let gender = activeTab === '#male' ?
                    'Male' :
                    'Female';

                loadMembers(gender, page);
            });

        });
    </script>
@endpush

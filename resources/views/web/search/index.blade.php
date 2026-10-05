@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@push('styles')
    <link rel="stylesheet" href="{{ asset('storage/web/assets/css/rangeSlider.css') }}">
@endpush
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    
    <!-- quick search & Advance search & id search start  -->
    <section class="common-section-bg py-4 py-lg-5">
        <div class="common-section-page">
            <div class="container">
                <div class="col-xxl-10 col-lg-11 mx-auto">
                    <div class="searching_leftside-main">
                        <div class="common-bgwhite-main p-lg-4 p-3">
                            <div class="common-tabs-design searching_leftside-main">
                                <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                    <li class="nav-item w-25">
                                        <button
                                            class="nav-link fts-14 fw-5 {{ $activeTab == 'quicksearch' ? 'active' : '' }}"
                                            id="pills-quicksearch-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-quicksearch" type="button" role="tab"
                                            aria-controls="pills-quicksearch"
                                            aria-selected="{{ $activeTab == 'quicksearch' ? 'true' : 'false' }}"><iconify-icon
                                                icon="ion:search"
                                                class="fts-18 me-1"></iconify-icon>{{ __('messages.lbl_quick_search') }}</button>
                                    </li>
                                    <li class="nav-item w-25">
                                        <button
                                            class="nav-link fts-14 fw-5 {{ $activeTab == 'advancesearch' ? 'active' : '' }}"
                                            id="pills-advancesearch-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-advancesearch" type="button" role="tab"
                                            aria-controls="pills-advancesearch"
                                            aria-selected="{{ $activeTab == 'advancesearch' ? 'true' : 'false' }}"><iconify-icon
                                                icon="tabler:circle-plus"
                                                class="fts-18 me-1"></iconify-icon>{{ __('messages.lbl_advance_search') }}</button>
                                    </li>
                                    <li class="nav-item w-25">
                                        <button
                                            class="nav-link fts-14 fw-5 {{ $activeTab == 'keywordsearch' ? 'active' : '' }}"
                                            id="pills-keywordsearch-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-keywordsearch" type="button" role="tab"
                                            aria-controls="pills-keywordsearch"
                                            aria-selected="{{ $activeTab == 'keywordsearch' ? 'true' : 'false' }}">
                                            <iconify-icon icon="hugeicons:id" class="fts-18 me-1"></iconify-icon>
                                            {{ __('messages.lbl_keyword_search') }}
                                        </button>
                                    </li>
                                    <li class="nav-item w-25">
                                        <button class="nav-link fts-14 fw-5 {{ $activeTab == 'idsearch' ? 'active' : '' }}"
                                            id="pills-idsearch-tab" data-bs-toggle="pill" data-bs-target="#pills-idsearch"
                                            type="button" role="tab" aria-controls="pills-idsearch"
                                            aria-selected="{{ $activeTab == 'idsearch' ? 'true' : 'false' }}">
                                            <iconify-icon icon="hugeicons:id" class="fts-18 me-1"></iconify-icon>
                                            {{ __('messages.lbl_id_search') }}
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content mt-3 mt-lg-4" id="pills-tabContent">
                                <div class="tab-pane fade {{ $activeTab == 'quicksearch' ? 'show active' : '' }}"
                                    id="pills-quicksearch" role="tabpanel" aria-labelledby="pills-home-tab">
                                    @include('web.search.quickSearch')
                                </div>
                                <div class="tab-pane fade {{ $activeTab == 'advancesearch' ? 'show active' : '' }}"
                                    id="pills-advancesearch" role="tabpanel" aria-labelledby="pills-advancesearch-tab">
                                    @include('web.search.advanceSearch')
                                </div>
                                <div class="tab-pane fade {{ $activeTab == 'keywordsearch' ? 'show active' : '' }}"
                                    id="pills-keywordsearch" role="tabpanel" aria-labelledby="pills-keywordsearch-tab">
                                    @include('web.search.keywordSearch')
                                </div>
                                <div class="tab-pane fade {{ $activeTab == 'idsearch' ? 'show active' : '' }}"
                                    id="pills-idsearch" role="tabpanel" aria-labelledby="pills-idsearch-tab">
                                    @include('web.search.idSearch')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('web.search.savedSearchModal')
@endsection
@push('scripts')
    <script>
        var lbl_yrs = "{{ __('messages.lbl_yrs') }}";
    </script>
    <script src="{{ asset('storage/web/assets/js/rangeSlider.js') }}"></script>
@endpush

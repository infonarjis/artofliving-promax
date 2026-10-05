@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@push('styles')
    <link rel="stylesheet" href="{{ asset('storage/web/assets/css/rangeSlider.css') }}">
@endpush
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    <!-- search result section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="row px-1">
                    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.search.result.searchfilter')
                    <div class="col-xl-9 col-lg-8 px-2 mt-3 mt-lg-0">
                        <form id="searchkeywordsForm" method="GET" action="{{ route('web.search.searchResult') }}">
                            <div class="row align-items-center gy-3">

                                <!-- Left Title -->
                                <div class="col-lg-6 col-12 px-3">
                                    <h2 class="fts-18 fw-7 white-color-n mb-0">
                                        {{ __('messages.lbl_search_result') }}
                                        <span>
                                            (<span id="resultCount">0</span>)
                                        </span>
                                    </h2>
                                </div>

                                <!-- Right Search -->
                                <div class="col-lg-6 col-12 px-3">
                                    <div class="search-wrapper">

                                        <div class="comman_inputfield_main">
                                            <input type="text" name="keyword_search" id="keyword_search"
                                                value="{{ request()->input('id_search') ?: request()->input('keyword_search', '') }}"
                                                placeholder="{{ __('messages.lbl_search_keywords') }}"
                                                class="input_comman_field">
                                        </div>

                                        <button type="button" class="btn-vendor-search keyword-search fts-15 fw-5">
                                            <iconify-icon icon="cuida:search-outline" class="fts-20">
                                            </iconify-icon>
                                        </button>

                                    </div>
                                </div>

                            </div>
                        </form>

                        <div class="seaech-result-main" id="resultData" data-url="{{ url()->full() }}">
                            {{-- Ajax Load Data --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        var lbl_yrs = "{{ __('messages.lbl_yrs') }}";
    </script>
    <script src="{{ asset('storage/web/assets/js/rangeSlider.js') }}"></script>
    <script>
        // Flag to prevent programmatic/init-time changes from triggering a second search.
        // Cleared automatically 300ms after DOM ready (covers rangeSlider's own init events).
        let isInitializing = true;
        let searchDebounceTimer = null;

        // AJAX pagination (same pattern as viewed-profile / shortlist / matches pages)
        $(document).on('click', '#resultData .ajax-pagination, #resultData .pagination a', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            fetchResults(url);
        });

        // Keyword search button
        $(document).on('click', '.keyword-search', function(e) {
            e.preventDefault();
            triggerSearch();
        });

        // Keyword search on Enter key or when cleared
        $("#keyword_search").keyup(function(e) {
            if (isInitializing || window.suppressFilterChange) return;
            if (e.keyCode === 13 || $(this).val() == "") {
                triggerSearch();
            }
        });

        // Sort dropdown
        $(document).on('change', '#searchForm input, #searchForm select', function() {
            if (isInitializing || window.suppressFilterChange) {
                console.log('[blocked - init]', this.id || this.name);
                return;
            }
            console.log('[triggerSearch fired by]', this.id || this.name);
            triggerSearch();
        });

        // Debounced trigger — builds the combined query & fetches
        function triggerSearch() {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(function() {
                let searchFilters = $('#searchForm').serialize();
                let keywordFilters = $('#searchkeywordsForm').serialize();
                let url = $('#searchForm').attr('action') +
                    '?' +
                    searchFilters +
                    '&' +
                    keywordFilters;
                fetchResults(url);
            }, 200);
        }

        // Core AJAX fetch + render
        function fetchResults(url, pushHistory = true) {
            let container = $('#resultData');
            if (!container.length || container.data('loading')) return;
            container.data('loading', true);

            $.ajax({
                url: url,
                type: "GET",
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                beforeSend: function() {
                    container.html(`
                        <div class="text-center py-5 mt-5">
                            <div class="spinner-border text-primary"></div>
                        </div>
                    `);
                },
                success: function(response) {
                    container.html(response.html);
                    $('#resultCount').text(response.resultCount);

                    // if ($(window).width() > 768) {
                    if (!('ontouchstart' in window)) {
                        $('html, body').animate({
                            scrollTop: container.offset().top - 100
                        }, 500);
                    }

                    if (pushHistory) {
                        window.history.pushState({
                                html: response.html,
                                resultCount: response.resultCount
                            },
                            '',
                            url
                        );
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    showToastMessage('error', '{{ __('messages.msg_unexpected_error_occured') }}');
                },
                complete: function() {
                    container.data('loading', false);
                }
            });
        }

        // Browser Back/Forward handling
        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.html) {
                $('#resultData').html(e.state.html);
                $('#resultCount').text(e.state.resultCount);
            } else {
                fetchResults(location.href, false);
            }
        });

        // Page init — SINGLE source of truth for the first load
        $(document).ready(function() {

            setTimeout(function() {
                $('#resultData').html(`
                        <div class="text-center py-5 mt-5">
                            <div class="spinner-border text-primary"></div>
                        </div>
                    `);
                isInitializing = false;
            }, 100);
        });
    </script>
@endpush

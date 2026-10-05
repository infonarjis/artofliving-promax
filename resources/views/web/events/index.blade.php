@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    
    <!-- event section start  -->
    <section class="common-section-bg pb-4 pb-lg-5">
        <div class="common-section-page">
            <div class="vendor-common-topbar event-bnr position-relative"></div>
            <div class="event-inner-section">
                <div class="container">
                    <div class="vendor-heading-content text-center">
                        <h2 class="fw-6 white-color-n fts-32">{{ __('messages.lbl_events') }}</h2>
                        <p class="fts-15 fw-4 white-color-n">{{ __('messages.lbl_event_description') }}</p>
                    </div>
                    <form method="GET" action="{{ route('web.event.index') }}">
                        @csrf
                        <div class="col-xxl-5 col-lg-6 mx-auto mt-3 d-flex gap-2">
                            <div class="comman_inputfield_main w-100">
                                <input type="text" name="keywords" value="{{ request('keywords') }}"
                                    placeholder="Keywords" class="input_comman_field">
                            </div>
                            <button type="submit" class="btn-vendor-search fts-24 fw-5"><iconify-icon
                                    icon="heroicons-outline:search"></iconify-icon></button>
                        </div>
                    </form>

                    <div id="event-data-container">
                        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.events.ajax_result')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            let typingTimer;
            let delay = 500; // 0.5 sec debounce
            /* AUTO SEARCH WHILE TYPING */
            $(document).on('keyup', 'input[name="keywords"]', function() {
                clearTimeout(typingTimer);
                let keyword = $(this).val().trim();
                typingTimer = setTimeout(function() {
                    // If cleared → reload full data :
                    if (keyword.length === 0) {
                        loadEvents("{{ route('web.event.index') }}");
                        return;
                    }

                    // Only search if length >= 3 :
                    if (keyword.length >= 3) {
                        loadEvents("{{ route('web.event.index') }}?keywords=" + encodeURIComponent(
                            keyword));
                    }

                }, delay);
            });


            /* FORM SUBMIT (Manual Search Button) */
            $(document).on('submit', 'form[action="{{ route('web.event.index') }}"]', function(e) {
                e.preventDefault();
                loadEvents($(this).attr('action') + '?' + $(this).serialize());
            });

            /* PAGINATION */
            $(document).on('click', '.ajaxPagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                if (url) {
                    loadEvents(url);
                }
            });

            /* MAIN AJAX FUNCTION */
            function loadEvents(url) {
                $.ajax({
                    url: url,
                    type: "GET",
                    beforeSend: function() {
                        $('#event-data-container').html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-light"></div>
                    </div>
                `);
                    },
                    success: function(response) {
                        $('#event-data-container').html(response);
                        window.history.pushState({}, '', url);
                    },
                    error: function() {
                        showToastMessage('error', '{{ __('messages.msg_something_went_wrong') }}');
                    }
                });
            }
        });
    </script>
@endpush

@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9 col-12">
                        <div class="seaech-result-main">
                            <div class="ai-page-header mb-4">
                                <h1 class="fts-28 fw-7 white-color-n mb-2">
                                    {{ __('messages.lbl_ai_selected_matches_for_you') }}</h1>
                                <p class="fts-15 fw-4 white-color70-n">
                                    {{ __('messages.lbl_ai_selected_matches_for_you_description') }}
                                </p>
                            </div>

                            <div class="pro-matches-list" id="resultData">
                                @include(_getConstant('dir_path.WEB_DIR_PATH') . '.aiMatchMaking.loader')
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 ps-lg-2 mt-4">
                        @include('web.dashboard.memberRightSideBar')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            loadAiMatches();

            // AJAX pagination (same pattern as the rest of the member dashboard pages)
            $(document).on('click', '#resultData .ajax-pagination, #resultData .pagination a', function(e) {
                e.preventDefault();
                let page = new URL($(this).attr('href')).searchParams.get('page');
                loadAiMatches(page);
            });

            function loadAiMatches(page = 1) {
                let container = $('#resultData');
                if (!container.length || container.data('loading')) return;
                container.data('loading', true);

                let data = $('#aiMatchMakingForm').serialize();
                data += '&page=' + page;

                $.ajax({
                    url: $('#aiMatchMakingForm').attr('action'),
                    type: "GET",
                    data: data,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        container.html(response);
                        $('html, body').animate({
                            scrollTop: container.offset().top - 100
                        }, 300);
                    },
                    error: function() {
                        container.html(
                            '<div class="alert alert-danger">Something went wrong.</div>'
                        );
                        showToastMessage('error', '{{ __('messages.msg_unexpected_error_occured') }}');
                    },
                    complete: function() {
                        container.data('loading', false);
                    }
                });
            }

            // Exposed globally in case filter changes elsewhere need to refresh the list
            window.reloadAiMatches = function(page = 1) {
                loadAiMatches(page);
            };

        });
    </script>
@endpush

@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
{{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    
    <section class="common-section-bg pb-4 pb-lg-5 pt-2 mt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9 col-12">
                        <div class="seaech-result-main">
                            {{-- Near By Matches --}}
                            @if ($type == 'nearByMe')
                                @include(_getConstant('dir_path.WEB_DIR_PATH') . '.matches.nearby_match')
                            @endif
                            {{-- Near By Matches --}}

                            <h2 class="fts-18 fw-7 white-color-n">{{ $page }}</h2>
                            <div class="pro-matches-list" id="resultData" data-url="{{ url()->full() }}">
                                @include(_getConstant('dir_path.WEB_DIR_PATH') . '.matches.ajax_result')
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
        $(function() {
            // AJAX pagination (same pattern as viewed-profile / shortlist pages)
            $(document).on('click', '#resultData .ajax-pagination, #resultData .pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                loadResult($('#resultData'), url);
            });

            function loadResult(container, url = null) {
                if (!container.length || container.data('loading')) return;
                container.data('loading', true);

                $.ajax({
                    url: url || container.data('url'),
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    beforeSend: function() {
                        container.html(`
                            <div class="text-center py-5">
                                <div class="spinner-border text-light"></div>
                            </div>
                        `);
                    },
                    success: function(response) {
                        container.html(response);
                        $('html, body').animate({
                            scrollTop: container.offset().top - 100
                        }, 500);
                    },
                    error: function() {
                        showToastMessage('error', '{{ __('messages.msg_unexpected_error_occured') }}');
                    },
                    complete: function() {
                        container.data('loading', false);
                    }
                });
            }

            // Exposed globally in case other actions on this page need to refresh the list
            window.reloadMatches = function(url = "{{ url()->full() }}") {
                loadResult($('#resultData'), url);
            };
        });
    </script>
@endpush
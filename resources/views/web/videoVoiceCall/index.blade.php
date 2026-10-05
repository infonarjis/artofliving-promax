{{-- resources/views/web/videoVoiceCallHistory/index.blade.php --}}
@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')

@section('web_content')
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="row">
                    @include('web.dashboard.memberLeftSideBar')

                    <div class="col-lg-9 col-12">
                        @include('web.dashboard.memberTop')

                        <div class="row">
                            <div class="col-xl-12 col-lg-12 mt-3">
                                <div class="common-bgwhite-main p-3 p-lg-4">
                                    <h2 class="fts-18 fw-7 white-color-n mb-1">
                                        {{ __('messages.lbl_call_history') }}
                                    </h2>
                                    <p class="fts-14 white-color-n mb-3">
                                        {{ __('messages.lbl_call_history_subtitle') }}
                                    </p>
                                    <div class="common-tablist-design mt-2 mt-lg-3">
                                        <div class="chats-listings-box pe-2" id="resultData" data-url="{{ url()->full() }}">
                                            @include(_getConstant('dir_path.WEB_DIR_PATH') .
                                                    '.videoVoiceCall.ajax_result',
                                                compact('resultData'))
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
        /* ── Pagination ── */
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
                    container.html(
                        '<div class="text-center py-5">' +
                        '<div class="spinner-border text-light" role="status">' +
                        '<span class="visually-hidden">Loading...</span>' +
                        '</div>' +
                        '</div>'
                    );
                },
                success: function(response) {
                    container.html(response);
                    $('html, body').animate({
                        scrollTop: container.offset().top - 100
                    }, 400);
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
        window.reloadCallHistory = function(url = "{{ url()->full() }}") {
            loadResult($('#resultData'), url);
        };
    </script>
@endpush

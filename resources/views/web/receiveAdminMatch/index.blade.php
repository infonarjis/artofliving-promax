@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="dashboard-layout">
                    {{-- LEFT SIDEBAR (Responsive Off-Canvas Drawer on Mobile) --}}
                    @include('web.dashboard.memberLeftSideBar')

                    <main class="main-content-area" id="main-content">
                        @include('web.dashboard.memberTop')
                        <div class="common-bgwhite-main p-3 p-lg-4" id="resultData" data-url="{{ url()->full() }}">
                            <h2 class="fts-18 fw-7 white-color-n">{{ __('messages.lbl_match_from_admin') }}</h2>
                            @include(_getConstant('dir_path.WEB_DIR_PATH') . '.receiveAdminMatch.ajax_result')
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            // AJAX pagination (same pattern as the rest of the member dashboard pages)
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
                            '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>'
                        );
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

            // Exposed globally in case other actions need to refresh the list
            window.reloadAdminMatch = function(url = "{{ url()->full() }}") {
                loadResult($('#resultData'), url);
            };

            function updateUI(card, id, otherUserId, otherUsermatriId, type) {
                let statusBox = card.find('.status-profils');
                let btnBox = card.find('.action-btn-box');

                if (type === 1) {
                    // Accepted → hide all buttons
                    statusBox
                        .removeClass('pending rejected')
                        .addClass('accepted')
                        .text('{{ __('messages.lbl_accepted') }}');

                    btnBox.html(''); // hide both buttons

                } else {
                    //  Rejected → show Accept only
                    statusBox
                        .removeClass('pending accepted')
                        .addClass('rejected')
                        .text('{{ __('messages.lbl_rejected') }}');
                    btnBox.html(''); // hide both buttons
                    // btnBox.html(`
                //     <button class="btn-accept-btn fts-13 acceptMatchBtn"
                //         data-id="${id}"
                //         data-otheruserid="${otherUserId}"
                //         data-otherusermatriid="${otherUsermatriId}">
                //         {{ __('messages.lbl_accept') }}
                //     </button>
                // `);
                }
            }

            function sendMatchResponse(btn, responseType) {
                let id = btn.data('id');
                let otherUserId = btn.data('otheruserid');
                let otherUsermatriId = btn.data('otherusermatriid');
                let card = btn.closest('.request-intrest-box');

                $.ajax({
                    url: "{{ route('web.receiveAdminMatch.acceptReject') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        otherUserId: otherUserId,
                        otherUsermatriId: otherUsermatriId,
                        response: responseType
                    },
                    beforeSend: function() {
                        btn.prop('disabled', true).text('{{ __('messages.lbl_please_wait') }}');
                    },
                    success: function() {
                        updateUI(card, id, otherUserId, otherUsermatriId, responseType);
                    },
                    error: function() {
                        btn.prop('disabled', false).text('Try Again');
                        showToastMessage('error', '{{ __('messages.msg_unexpected_error_occured') }}');
                    }
                });
            }

            $(document).on('click', '.acceptMatchBtn', function() {
                sendMatchResponse($(this), 1);
            });

            $(document).on('click', '.rejectMatchBtn', function() {
                sendMatchResponse($(this), 2);
            });

        });
    </script>
@endpush

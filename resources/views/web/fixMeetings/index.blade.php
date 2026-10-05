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
                            @include(_getConstant('dir_path.WEB_DIR_PATH') . '.fixMeetings.ajax_result')
                        </div>
                    </main>
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
            window.reloadFixMeetings = function(url = "{{ url()->full() }}") {
                loadResult($('#resultData'), url);
            };

            function updateCardUI(card, response, label) {
                const statusBox = card.find('.meeting-status');
                const actionBox = card.find('.meeting-accept-reject');

                // Member accept
                if (label.includes('member') && response == 1) {
                    statusBox
                        .removeClass('pending reject')
                        .addClass('accept')
                        .text('{{ __('messages.lbl_accepted') }}');

                    actionBox.remove(); // remove buttons completely
                }

                // Member reject
                if (label.includes('member') && response == 2) {
                    statusBox
                        .removeClass('pending accept')
                        .addClass('reject')
                        .text('{{ __('messages.lbl_rejected') }}');

                    actionBox.remove();
                }

                // Meeting complete
                if (label === 'meeting_status') {
                    statusBox
                        .removeClass('pending reject')
                        .addClass('accept')
                        .text('{{ __('messages.lbl_completed') }}');

                    actionBox.remove();
                }
            }

            function sendAjax(btn, extraData = {}) {
                // const card = btn.closest('.common-bgwhite-main');
                const card = btn.closest('.meeting-card');

                $.ajax({
                    url: "{{ route('web.fixMeetings.acceptReject') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: btn.data('id'),
                        response: btn.data('response'),
                        memberLabel: btn.data('label'),
                        ...extraData
                    },
                    success: function(res) {
                        if (res.status) {
                            const card = btn.closest('.meeting-card');
                            updateCardUI(card, btn.data('response'), btn.data('label'));

                            btn.closest('.collapse').collapse('hide');

                            showToastMessage('success', res.message);
                        } else {
                            showToastMessage('error', res.message);
                        }
                    },
                    error: function() {
                        showToastMessage('error', '{{ __('messages.msg_unexpected_error_occured') }}');
                    }
                });
            }

            // ✅ Accept button
            $(document).on('click', '.acceptRejectBtn', function() {
                sendAjax($(this));
            });

            // ✅ Reject submit
            $(document).on('click', '.addRemarks', function() {
                const btn = $(this);
                const textarea = btn.closest('.comman_inputfield_main').find('textarea');

                if (textarea.val().trim() === '') {
                    textarea.focus();
                    return;
                }

                sendAjax(btn, {
                    rejectBy: btn.closest('form').find('input[name="rejectBy"]').val(),
                    member_reject_remark: textarea.val(),
                    meeting_remark: textarea.val()
                });

                btn.closest('.collapse').collapse('hide');
            });

        });
    </script>
@endpush

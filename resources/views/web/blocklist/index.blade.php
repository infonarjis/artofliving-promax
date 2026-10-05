@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <!-- blocklist section start -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="container">
                    <div class="dashboard-layout">
                        {{-- LEFT SIDEBAR (Responsive Off-Canvas Drawer on Mobile) --}}
                        @include('web.dashboard.memberLeftSideBar')

                        <main class="main-content-area">
                            @include('web.dashboard.memberTop')
                            <div class="common-bgwhite-main p-3 p-lg-4">
                                <h2 class="fts-18 fw-7 white-color-n mb-1">
                                    {{ __('messages.lbl_blocklist_profile') }}
                                </h2>
                                <p class="fts-14 white-color-n mb-3">
                                    {{ __('messages.lbl_blocklist_profile_subtitle') }}
                                </p>
                                <div id="resultData">
                                    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.blocklist.ajax_result')
                                </div>
                            </div>
                        </main>
                    </div>
                </div>
            </div>
    </section>

    {{-- Modal Popup --}}
    <div class="customsmallmodel_light alertsize modal fade" id="BlockModal" tabindex="-1"
        aria-labelledby="BlockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                    <h2 class="fts-18 fw-6 white-color-n" id="BlockModalLabel">{{ __('messages.lbl_unblock_profile') }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
                    </button>
                </div>
                <div class="modal_liteBody px-3 px-lg-4 py-3">
                    <p class="fts-14 fw-4 white-color70-n">{{ __('messages.lbl_unblock_profile_messages') }}</p>
                    <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                        <button type="button" id="confirmRemoveBtn"
                            class="click-changeButton">{{ __('messages.lbl_yes_unblock') }}</button>
                        <button type="button" class="clickClosebutton"
                            data-bs-dismiss="modal">{{ __('messages.lbl_cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        let pendingRemoveId = null;
        let pendingRemoveBtn = null;

        $(function() {
            // AJAX pagination (same pattern as viewed-profile page)
            $(document).on('click', '.ajax-pagination', function(e) {
                e.preventDefault();
                let link = $(this);
                let container = link.closest('[data-url]');
                if (!container.length) {
                    container = $('#resultData');
                }
                loadResult(container, link.attr('href'));
            });

            $(document).on('click', '#resultData .pagination a', function(e) {
                e.preventDefault();
                loadResult($('#resultData'), $(this).attr('href'));
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
                                <div class="spinner-border text-primary"></div>
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

            // Exposed globally, same as reloadAjaxlist before
            window.reloadAjaxlist = function(url = "{{ route('web.blocklist.index') }}") {
                loadResult($('#resultData'), url);
            };
        });

        $(document).on('click', '.remove-blocklist', function() {
            pendingRemoveId = $(this).data('id');
            pendingRemoveBtn = $(this);
            $('#BlockModal').modal('show');
        });

        $(document).on('click', '#confirmRemoveBtn', function() {
            $('#BlockModal').modal('hide');

            let id = pendingRemoveId;
            let button = pendingRemoveBtn;

            if (!id || !button) return;

            $.ajax({
                url: "{{ route('web.blocklist.remove', ':id') }}".replace(':id', id),
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    button.html('{{ __('messages.lbl_removing') }}');
                    button.prop('disabled', true);
                },
                success: function(response) {
                    if (response.status) {
                        button.closest('.col-md-6').fadeOut(300, function() {
                            $(this).remove();
                            if ($('#resultData .col-md-6').length === 0) {
                                reloadAjaxlist(window.location.href);
                            }
                        });
                    } else {
                        showToastMessage('error', response.message);
                        button.prop('disabled', false);
                    }
                },
                error: function() {
                    showToastMessage('error', '{{ __('messages.msg_unexpected_error_occured') }}');
                    button.prop('disabled', false);
                }
            });

            pendingRemoveId = null;
            pendingRemoveBtn = null;
        });

        // Clear pending state if modal is dismissed without confirming
        $('#BlockModal').on('hidden.bs.modal', function() {
            pendingRemoveId = null;
            pendingRemoveBtn = null;
        });
    </script>
@endpush

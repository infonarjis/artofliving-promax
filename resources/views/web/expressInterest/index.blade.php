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

                        <div class="common-tabs-design">
                            <ul class="nav flex-nowrap d-flex nav-pills" id="pills-tab" role="tablist">
                                <li class="nav-item w-50">
                                    <button class="nav-link fts-15 {{ $type == 'interest_sent' ? 'active' : '' }}"
                                        id="interest_sent-tab" data-bs-toggle="pill" data-bs-target="#interest_sent"
                                        type="button" role="tab" aria-controls="interest_sent"
                                        aria-selected="true"><iconify-icon icon="line-md:arrow-up" class="fts-20 me-1"
                                            style="transform: rotate(45deg);"></iconify-icon>{{ __('messages.lbl_express_interest_sent') }}</button>
                                </li>
                                <li class="nav-item w-50">
                                    <button class="nav-link fts-15 {{ $type == 'interest_receive' ? 'active' : '' }}"
                                        id="interest_receive-tab" data-bs-toggle="pill" data-bs-target="#interest_receive"
                                        type="button" role="tab" aria-controls="interest_receive"
                                        aria-selected="false"><iconify-icon icon="line-md:arrow-up" class="fts-20 me-1"
                                            style="transform: rotate(215deg);"></iconify-icon>{{ __('messages.lbl_express_interest_received') }}</button>
                                </li>
                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-xl-9 col-lg-8 mt-3 pe-lg-2">
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade {{ $type == 'interest_sent' ? 'show active' : '' }}"
                                        id="interest_sent">
                                        <div class="common-bgwhite-main p-3 p-lg-4" id="sentInterestData"
                                            data-url="{{ route('web.expressInterest.index', ['type' => 'interest_sent']) }}">
                                        </div>
                                    </div>

                                    <div class="tab-pane fade {{ $type == 'interest_receive' ? 'show active' : '' }}"
                                        id="interest_receive">
                                        <div class="common-bgwhite-main p-3 p-lg-4" id="receiveInterestData"
                                            data-url="{{ route('web.expressInterest.index', ['type' => 'interest_receive']) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 mt-3 ps-lg-2">
                                @include('web.dashboard.memberRightSideBar')
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal Popup --}}
    <div class="customsmallmodel_light alertsize modal fade" id="interestDeleteModal" tabindex="-1"
        aria-labelledby="interestDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                    <h2 class="fts-18 fw-6 white-color-n" id="interestDeleteModalLabel">
                        {{ __('messages.lbl_delete_request') }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
                    </button>
                </div>
                <div class="modal_liteBody px-3 px-lg-4 py-3">
                    <p class="fts-14 fw-4 white-color70-n">
                        {{ __('messages.lbl_delete_request_msg') }}
                    </p>
                    <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                        <button type="button" id="confirmRemoveBtn"
                            class="click-changeButton">{{ __('messages.lbl_yes_remove') }}</button>
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
        $(document).ready(function() {
            let currentType = @json($type); // 'interest_sent' or 'interest_receive'
            let container = (currentType === 'interest_sent') ? '#sentInterestData' : '#receiveInterestData';
            let url = $(container).data('url');
            loadData(url, container);
        });

        $(document).on('shown.bs.tab', 'button[data-bs-toggle="pill"]', function(e) {
            let target = $(e.target).data('bs-target');
            let container = (target === '#interest_sent') ? '#sentInterestData' : '#receiveInterestData';
            let url = $(container).data('url');

            if ($(container).html().trim() === '') {
                loadData(url, container);
            }
        });

        // AJAX pagination (same pattern as viewed-profile page),
        // adapted to resolve the correct tab container
        $(document).on('click', '.ajax-pagination', function(e) {
            e.preventDefault();
            let link = $(this);
            let href = link.attr('href');

            let container = link.closest('[data-url]');
            if (!container.length) {
                container = $('.tab-pane.show.active').find('[data-url]');
            }
            if (!container.length) return;

            loadData(href, '#' + container.attr('id'));
        });

        function loadData(url, targetDiv) {
            let container = $(targetDiv);
            if (!container.length || container.data('loading')) return;
            container.data('loading', true);

            $.ajax({
                url: url,
                type: "GET",
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

        $(document).on('click', '.accept-request, .reject-request', function() {

            let id = $(this).data('id');
            let isAccept = $(this).hasClass('accept-request');
            let button = $(this);

            let url = isAccept ?
                "{{ route('web.expressInterest.accept', ':id') }}" :
                "{{ route('web.expressInterest.reject', ':id') }}";

            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    button.prop('disabled', true)
                        .html('<iconify-icon icon="codex:loader" class="fts-28"></iconify-icon>');
                },
                success: function(response) {

                    if (response.status) {

                        showToastMessage('success', response.message);

                        // Reload only current tab
                        let activeTab = $('.tab-pane.active');
                        let container = activeTab.find('[data-url]');
                        let url = container.data('url');

                        loadData(url, '#' + container.attr('id'));

                    } else {
                        showToastMessage('error', response.message);
                    }
                },
                error: function() {
                    showToastMessage('error', '{{ __('messages.msg_unexpected_error_occured') }}');
                    button.prop('disabled', false);
                }
            });
        });

        let pendingRemoveId = null;
        let pendingRemoveBtn = null;

        $(document).on('click', '.remove-interest', function() {
            pendingRemoveId = $(this).data('id');
            pendingRemoveBtn = $(this);
            $('#interestDeleteModal').modal('show');
        });

        $(document).on('click', '#confirmRemoveBtn', function() {
            $('#interestDeleteModal').modal('hide');

            let id = pendingRemoveId;
            let button = pendingRemoveBtn;

            if (!id || !button) return;

            $.ajax({
                url: "{{ route('web.expressInterest.remove', ':id') }}".replace(':id', id),
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    button.prop('disabled', true).html(
                        '<iconify-icon icon="codex:loader"></iconify-icon>');
                },
                success: function(response) {
                    if (response.status) {

                        showToastMessage('success', response.message);

                        // Reload correct tab content (THIS IS THE FIX)
                        let activeTab = $('.tab-pane.active');
                        let container = activeTab.find('[data-url]');
                        let url = container.data('url');

                        loadData(url, '#' + container.attr('id'));

                    } else {
                        showToastMessage('error', response.message);
                        button.prop('disabled', false).html(
                            '<iconify-icon icon="fluent:delete-28-regular"></iconify-icon>');
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
        $('#interestDeleteModal').on('hidden.bs.modal', function() {
            pendingRemoveId = null;
            pendingRemoveBtn = null;
        });
    </script>
@endpush

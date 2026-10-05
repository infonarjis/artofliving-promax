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
                                    <button class="nav-link fts-15  {{ $type == 'i_viewed' ? 'active' : '' }}"
                                        id="I-viewed-tab" data-bs-toggle="pill" data-bs-target="#I-viewed" type="button"
                                        role="tab" aria-controls="I-viewed" aria-selected="true"><iconify-icon
                                            icon="line-md:arrow-up" class="fts-20 me-1"
                                            style="transform: rotate(45deg);"></iconify-icon>{{ __('messages.lbl_i_viewed_profile') }}</button>
                                </li>
                                <li class="nav-item w-50">
                                    <button class="nav-link fts-15 {{ $type == 'who_viewed' ? 'active' : '' }}"
                                        id="Who-viewed-tab" data-bs-toggle="pill" data-bs-target="#Who-viewed"
                                        type="button" role="tab" aria-controls="Who-viewed"
                                        aria-selected="false"><iconify-icon icon="line-md:arrow-up" class="fts-20 me-1"
                                            style="transform: rotate(215deg);"></iconify-icon>{{ __('messages.lbl_who_viewed_my_profile') }}</button>
                                </li>
                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-xl-9 col-lg-8 mt-3 pe-lg-2">
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade {{ $type == 'i_viewed' ? 'show active' : '' }}"
                                        id="I-viewed">
                                        <div class="common-bgwhite-main p-3 p-lg-4" id="iViewedData"
                                            data-url="{{ route('web.viewedProfile.index', ['type' => 'i_viewed']) }}">
                                        </div>
                                    </div>

                                    <div class="tab-pane fade {{ $type == 'who_viewed' ? 'show active' : '' }}"
                                        id="Who-viewed">
                                        <div class="common-bgwhite-main p-3 p-lg-4" id="whoViewedData"
                                            data-url="{{ route('web.viewedProfile.index', ['type' => 'who_viewed']) }}">
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
@endsection
@push('scripts')
    <script>
        $(function() {
            // Load active tab
            loadTab($('.tab-pane.show.active').find('[data-url]'));
            // Tab change
            $(document).on('shown.bs.tab', 'button[data-bs-toggle="pill"]', function(e) {
                let container = $($(e.target).data('bs-target')).find('[data-url]');
                if ($.trim(container.html()) === '') {
                    loadTab(container);
                }
            });

            // AJAX pagination
            $(document).on('click', '.ajax-pagination', function(e) {
                e.preventDefault();
                let link = $(this);
                let container = link.closest('[data-url]');
                if (!container.length) {
                    container = $('.tab-pane.show.active').find('[data-url]');
                }
                loadTab(container, link.attr('href'));
            });

            // AJAX load
            function loadTab(container, url = null) {
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
                    },
                    error: function() {
                        showToastMessage(
                            'error',
                            '{{ __('messages.msg_unexpected_error_occured') }}'
                        );
                    },
                    complete: function() {
                        container.data('loading', false);
                    }
                });
            }
        });
    </script>
@endpush

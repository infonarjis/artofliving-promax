@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <!-- Saved Search section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="dashboard-layout">
                    {{-- LEFT SIDEBAR (Responsive Off-Canvas Drawer on Mobile) --}}
                    @include('web.dashboard.memberLeftSideBar')

                    <main class="main-content-area" id="main-content">
                        @include('web.dashboard.memberTop')
                        <div class="common-bgwhite-main p-3 mt-3 px-lg-5">
                            <div class="saved-search-section mt-3 mt-lg-4">
                                <div class="saved-searchtitle">
                                    <div class="fts-20 fw-6 white-color-n">{{ __('messages.lbl_saved_search') }}</div>
                                </div>
                            </div>
                            <div class="plan-history-list pt-lg-1" id="resultData" data-url="{{ url()->full() }}">
                                @include(_getConstant('dir_path.WEB_DIR_PATH') . '.savedSearch.ajax_result')
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
        $(document).on('click', '#resultData .ajax-pagination, #resultData .pagination a', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            fetchSavedSearch(url);
        });

        function fetchSavedSearch(url = null) {
            let container = $('#resultData');
            if (!container.length || container.data('loading')) return;
            container.data('loading', true);

            $.ajax({
                url: url || container.data('url'),
                type: "GET",
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                beforeSend: function() {
                    container.html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
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

        let deleteUrlTemplate = "{{ route('web.savedSearch.delete', ':id') }}";
        $(document).on('click', '.deleteSavedSearch', function() {
            let id = $(this).data('id');

            if (!confirm('Are you sure you want to delete this saved search?')) {
                return;
            }

            let url = deleteUrlTemplate.replace(':id', id);

            $.ajax({
                url: url,
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status) {
                        fetchSavedSearch("{{ route('web.savedSearch.index') }}");
                    } else {
                        showToastMessage('error', response.message);
                    }
                },
                error: function() {
                    showToastMessage('error', '{{ __('messages.msg_unexpected_error_occured') }}');
                }
            });
        });
    </script>
@endpush
@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <!-- current plan section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="dashboard-layout">
                    {{-- LEFT SIDEBAR (Responsive Off-Canvas Drawer on Mobile) --}}
                    @include('web.dashboard.memberLeftSideBar')

                    <main class="main-content-area" id="main-content">
                        <div class="common-bgwhite-main p-3 mt-3 px-lg-5">
                            @if ($planHistory->isNotEmpty())
                                <div class="right-plans-box mt-3 mt-lg-4">
                                    <div class="current-plantitle">
                                        <div class="fts-18 fw-7 white-color-n">{{ __('messages.lbl_plan_history') }}</div>
                                    </div>
                                    <div class="common-bglight-main p-3 p-lg-4 mt-2" id="plan-history-table">
                                        {{-- @include(_getConstant('dir_path.WEB_DIR_PATH').'.currentPlan.ajax_plan_history', ['planHistory' => $planHistory]) --}}
                                    </div>
                                </div>
                            @endif
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
            loadHistory('{{ route('web.currentPlan.history') }}');
        });

        $(document).on('click', '.pagination_nav a', function(e) {
            e.preventDefault();
            loadHistory($(this).attr('href'));
        });

        function loadHistory(url) {
            $.ajax({
                url: url,
                type: 'GET',
                beforeSend: function() {
                    $('#plan-history-table').html(
                        '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>'
                    );
                },
                success: function(response) {
                    $('#plan-history-table').html(response);
                }
            });
        }
    </script>
@endpush

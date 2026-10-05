@extends(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.layouts.mainlayout')
@section('affiliate_after_login_content')
    <main class="afd-main">

        <div class="afd-page-header d-flex align-items-start justify-content-between gap-3 flex-wrap">
            <div>
                <div class="afd-breadcrumb">
                    <a href="{{ route('affiliate.dashboard') }}">
                        <iconify-icon icon="hugeicons:dashboard-browsing" style="vertical-align:-2px"></iconify-icon>
                        Dashboard
                    </a>
                    <span style="font-size:10px">›</span>
                    <span class="afd-bc-active">Clicks</span>
                </div>
                <div class="afd-page-title">
                    Visitor Clicks <small style="font-size:14px; color:var(--afd-muted); font-weight:400">from
                        {{ $affiliateUser->fullname }} Affiliate Link</small>
                </div>
            </div>
        </div>

        <div class="afd-stats-row">
            <div class="afd-stat-card afd-sc-red">
                <div class="afd-sc-icon"><iconify-icon icon="hugeicons:mouse-01"></iconify-icon></div>
                <div class="afd-sc-label">Total Clicks</div>
                <div class="afd-sc-number" id="totalClicks">{{ $totalClicks }}</div>
            </div>
            <div class="afd-stat-card afd-sc-blue">
                <div class="afd-sc-icon"><iconify-icon icon="hugeicons:user-multiple"></iconify-icon></div>
                <div class="afd-sc-label">Unique Visitors</div>
                <div class="afd-sc-number" id="uniqueVisitors">{{ $uniqueVisitors }}</div>
            </div>
            <div class="afd-stat-card afd-sc-gold">
                <div class="afd-sc-icon"><iconify-icon icon="logos:chrome"></iconify-icon></div>
                <div class="afd-sc-label">Top Browser</div>
                <div class="afd-sc-number" style="font-size:20px" id="topBrowser">{{ $topBrowser->browser ?? '-' }} <small
                        style="font-size:12px; font-weight:500; color:var(--afd-muted)">({{ $topBrowser->total ?? 0 }}
                        clicks)</small>
                </div>
            </div>
        </div>

        <div class="afd-panel" id="resultData">
            @include(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.visitorClick.ajax_result')
        </div>

    </main>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {

            function fetchClicks(page = 1) {
                $.ajax({
                    url: "?page=" + page,
                    type: "GET",
                    data: {
                        filter: $('#filter').val(),
                        search: $('#search').val()
                    },
                    success: function(response) {
                        $('#resultData').html(response);
                    }
                });
            }

            $('#filter').change(function() {
                fetchClicks();
            });

            let timer;
            $('#search').keyup(function() {
                clearTimeout(timer);
                timer = setTimeout(() => fetchClicks(), 400);
            });

            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                fetchClicks(page);
            });

        });
    </script>
@endpush

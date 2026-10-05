@extends(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.layouts.mainlayout')
@section('affiliate_after_login_content')
    <!-- MAIN -->
    <main class="afd-main">

        <div class="afd-page-header d-flex align-items-start justify-content-between gap-3 flex-wrap">
            <div>
                <div class="afd-breadcrumb">
                    <a href="{{ route('affiliate.dashboard') }}"><iconify-icon icon="hugeicons:dashboard-square-01"
                            style="vertical-align:-2px"></iconify-icon>
                        Dashboard</a>
                    <span style="font-size:10px">›</span>
                    <span class="afd-bc-active">{{ $pageName }}</span>
                </div>
                <div class="afd-page-title">
                    User Registrations <span>from {{ $affiliateUser->fullname }} Affiliate Link</span>
                </div>
            </div>
        </div>

        <div class="afd-stats-row">
            <div class="afd-stat-card afd-sc-blue">
                <div class="afd-sc-icon"><iconify-icon icon="hugeicons:user"></iconify-icon></div>
                <div class="afd-sc-label">Total Referred Registrations</div>
                <div class="afd-sc-number">{{ $totalMemberCount }}</div>
                <div class="afd-sc-sub">Members who registered using your referral link</div>
            </div>
            <div class="afd-stat-card afd-sc-gold">
                <div class="afd-sc-icon"><iconify-icon icon="hugeicons:heart-check"></iconify-icon></div>
                <div class="afd-sc-label">Approved Members</div>
                <div class="afd-sc-number">{{ $verifiedCount }}</div>
                <div class="afd-sc-sub">Referred members whose profiles are approved</div>
            </div>
            <div class="afd-stat-card afd-sc-green">
                <div class="afd-sc-icon"><iconify-icon icon="hugeicons:money-bag-02"></iconify-icon></div>
                <div class="afd-sc-label">Premium Conversions</div>
                <div class="afd-sc-number">{{ $premiumCount }}</div>
                <div class="afd-sc-sub">Referred members who upgraded to premium</div>
            </div>
        </div>

        <div class="afd-panel">
            <div class="afd-toolbar">
                <div class="afd-select-wrap">
                    <select class="afd-custom-select filter-change" name="range">
                        <option value="">All Time</option>
                        <option value="month">This Month</option>
                        <option value="7days">Last 7 Days</option>
                    </select>
                </div>
                <div class="afd-select-wrap">
                    <select class="afd-custom-select filter-change" name="status">
                        <option value="">Status: All</option>
                        <option value="APPROVED">Approved</option>
                        <option value="UNAPPROVED">Unapproved</option>
                        <option value="Suspended">Suspended</option>
                    </select>
                </div>
                <div class="afd-select-wrap">
                    <select class="afd-custom-select filter-change" name="plan">
                        <option value="">Plan: All Plans</option>
                        @foreach ($plans as $item)
                            <option value="{{ $item->id }}">{{ $item->plan_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="afd-search-wrap">
                    <span class="afd-search-icon"><iconify-icon icon="hugeicons:search-02" width="18" height="18"
                            style="vertical-align:-3px"></iconify-icon></span>
                    <input type="text" id="search" class="filter-change" name="search"
                        placeholder="Search name, plan…">
                </div>
            </div>
            <div id="resultData">
                @include(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.assignMember.ajax_result')
            </div>
        </div>

    </main>
@endsection

@push('scripts')
    <script>
        function loadData(page = 1) {
            let data = {};

            $('.filter-change').each(function() {
                data[$(this).attr('name')] = $(this).val();
            });

            $.ajax({
                url: "?page=" + page,
                data: data,
                success: function(res) {
                    $('#resultData').html(res);
                }
            });
        }

        $(document).on('change', 'select.filter-change', function() {
            loadData();
        });

        let typingTimer;
        $(document).on('keyup', 'input[name="search"]', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => loadData(), 400);
        });

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            loadData(page);
        });
    </script>
@endpush

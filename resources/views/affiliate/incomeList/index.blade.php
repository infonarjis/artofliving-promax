@extends(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.layouts.mainlayout')
@section('affiliate_after_login_content')
    <!-- MAIN -->
    <main class="afd-main">

        <div class="afd-page-header d-flex align-items-start justify-content-between gap-3 flex-wrap mb-2">
            <div>
                <div class="afd-breadcrumb">
                    <a href="{{ route('affiliate.dashboard') }}"><iconify-icon icon="hugeicons:dashboard-square-01"
                            style="vertical-align:-2px"></iconify-icon>
                        Dashboard</a>
                    <span style="font-size:10px">›</span>
                    <span class="afd-bc-active">{{ $pageName }}</span>
                </div>
            </div>
        </div>

        <div class="afd-stats-row">
            <div class="afd-stat-card afd-sc-blue">
                <div class="afd-sc-icon"><iconify-icon icon="hugeicons:user"></iconify-icon></div>
                <div class="afd-sc-label">Verified Profile</div>
                <div class="afd-sc-number">{{ $verifyProfileCommission ?? 0 }} / ₹ {{ number_format($verifyProfileAmount ?? 0, 2) }}</div>
            </div>
            <div class="afd-stat-card afd-sc-gold">
                <div class="afd-sc-icon"><iconify-icon icon="hugeicons:heart-check"></iconify-icon></div>
                <div class="afd-sc-label">On Field Verification Profile</div>
                <div class="afd-sc-number">{{ $onFieldVerifyProfile ?? 0 }} / ₹ {{ number_format($onFieldVerifyAmount ?? 0, 2) }}</div>
            </div>
            <div class="afd-stat-card afd-sc-green">
                <div class="afd-sc-icon"><iconify-icon icon="hugeicons:money-bag-02"></iconify-icon></div>
                <div class="afd-sc-label">Paid Members</div>
                <div class="afd-sc-number">{{ $paidProfileCommission ?? 0 }} / ₹ {{ number_format($paidProfileAmount ?? 0, 2) }}</div>
            </div>
        </div>

        <div class="afd-panel">
            <div id="resultData">
                @include(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.incomeList.ajax_result')
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

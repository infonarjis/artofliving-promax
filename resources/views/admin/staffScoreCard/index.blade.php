@extends(_getConstant('dir_path.ADMIN_DIR_PATH').'.admin_layout')
@section('admin_content')
@push('styles')
<style>
    :root {
        --clr-attendance: #4c6ef5;
        --clr-kpi: #f59f00;
        --clr-revenue: #12b886;
        --clr-profile: #ae3ec9;
        --clr-wp: #868e96;
        --clr-score: #e8590c;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    /* ============ Page header bar ============ */
    .scorecard-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #eef0f2;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
        padding: 18px 24px;
        margin-bottom: 26px;
    }

    .scorecard-title {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .scorecard-title .title-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #5c7cfa, #4c6ef5);
        color: #fff;
        flex: 0 0 auto;
        box-shadow: 0 4px 10px rgba(76, 110, 245, 0.25);
    }

    .scorecard-title .title-icon svg {
        width: 19px;
        height: 19px;
        stroke-width: 2;
    }

    .scorecard-title h1 {
        font-size: 17px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        letter-spacing: 0.1px;
    }

    .scorecard-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-report {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        border-radius: 999px;
        padding: 9px 18px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.2px;
        color: #fff;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        cursor: pointer;
    }

    .btn-report svg {
        width: 15px;
        height: 15px;
        stroke-width: 2.4;
    }

    .btn-report:hover {
        transform: translateY(-2px);
        color: #fff;
        opacity: 0.95;
    }

    .btn-pdf {
        background: linear-gradient(135deg, #ff8787, #e8590c);
        box-shadow: 0 4px 12px rgba(232, 89, 12, 0.28);
    }

    .btn-csv {
        background: linear-gradient(135deg, #38d9a9, #12b886);
        box-shadow: 0 4px 12px rgba(18, 184, 134, 0.28);
    }

    .month-picker-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8f9fb;
        border: 1px solid #eef0f2;
        border-radius: 999px;
        padding: 6px 8px 6px 16px;
    }

    .month-picker-wrap svg {
        width: 15px;
        height: 15px;
        color: #868e96;
        stroke-width: 2.2;
        flex: 0 0 auto;
    }

    .month-picker-wrap input[type="month"] {
        border: none;
        background: transparent;
        font-size: 13px;
        font-weight: 600;
        color: #4c6ef5;
        padding: 5px 8px;
        border-radius: 999px;
        outline: none;
    }

    .month-picker-wrap input[type="month"]:focus {
        background: #ffffff;
        box-shadow: 0 0 0 2px rgba(76, 110, 245, 0.15);
    }

    /* ============ Staff roster (score card rows) ============ */
    .staff-roster {
        display: flex;
        flex-direction: column;
        gap: 18px;
        margin-top: 25px;
    }

    .staff-row {
        display: flex;
        align-items: center;
        gap: 22px;
        background: #ffffff;
        border-radius: 14px;
        padding: 18px 22px;
        border: 1px solid #eef0f2;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
        transition: box-shadow 0.25s ease;
        flex-wrap: wrap;
        position: relative;
        overflow: hidden;
    }

    .staff-row::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        height: 4px;
        width: 100%;
        background: linear-gradient(90deg, #00c6ff, #0072ff);
    }

    .staff-row:hover {
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .staff-identity {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 0 0 auto;
        min-width: 250px;
    }

    .staff-avatar-wrap {
        position: relative;
        flex: 0 0 auto;
    }

    .staff-avatar-wrap img {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        object-fit: cover;
        display: block;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #eef0f2;
    }

    .staff-identity-text {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .staff-name-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .staff-name-row h4 {
        font-size: 15px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        white-space: nowrap;
    }

    .staff-identity .staff-tag {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        color: #4c6ef5;
        background: rgba(76, 110, 245, 0.14);
        border-radius: 5px;
        padding: 2px 7px;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .rating-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 10.5px;
        font-weight: 700;
        border-radius: 999px;
        padding: 3px 10px 3px 8px;
        letter-spacing: 0.2px;
        white-space: nowrap;
        width: fit-content;
    }

    .rating-badge svg {
        width: 12px;
        height: 12px;
        stroke-width: 2.6;
        flex: 0 0 auto;
    }

    .rating-outstanding { background: linear-gradient(135deg, rgba(255, 212, 59, 0.28), rgba(245, 159, 0, 0.22)); color: #a15c00; }
    .rating-exceeds { background: rgba(18, 184, 134, 0.18); color: #0b8560; }
    .rating-meets { background: rgba(76, 110, 245, 0.18); color: #3b56d9; }
    .rating-needs { background: rgba(245, 159, 0, 0.2); color: #ad7300; }
    .rating-under { background: rgba(230, 73, 73, 0.18); color: #c62828; }
    .rating-default { background: rgba(134, 142, 150, 0.18); color: #5a6268; }

    .staff-identity small {
        display: block;
        color: #9aa3af;
        font-size: 12.5px;
    }

    .staff-divider {
        width: 1px;
        align-self: stretch;
        background: #eef0f2;
        margin: 0 4px;
    }

    .staff-chips {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        flex: 1;
    }

    .chip {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid transparent;
    }

    .chip .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex: 0 0 auto;
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
    }

    .chip .chip-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .chip .chip-value {
        font-size: 15px;
        font-weight: 800;
        white-space: nowrap;
    }

    .chip-attendance { background: rgba(76, 110, 245, 0.22); }
    .chip-attendance .dot { background: var(--clr-attendance); }
    .chip-attendance .chip-label { color: #3b56d9; }
    .chip-attendance .chip-value { color: #2d43b3; }

    .chip-kpi { background: rgba(245, 159, 0, 0.24); }
    .chip-kpi .dot { background: var(--clr-kpi); }
    .chip-kpi .chip-label { color: #ad7300; }
    .chip-kpi .chip-value { color: #8f5f00; }

    .chip-revenue { background: rgba(18, 184, 134, 0.22); }
    .chip-revenue .dot { background: var(--clr-revenue); }
    .chip-revenue .chip-label { color: #0b8560; }
    .chip-revenue .chip-value { color: #08704f; }

    .chip-profile { background: rgba(174, 62, 201, 0.22); }
    .chip-profile .dot { background: var(--clr-profile); }
    .chip-profile .chip-label { color: #832b98; }
    .chip-profile .chip-value { color: #6d1f80; }

    .chip-wp { background: rgba(134, 142, 150, 0.26); }
    .chip-wp .dot { background: var(--clr-wp); }
    .chip-wp .chip-label { color: #5a6268; }
    .chip-wp .chip-value { color: #40464b; }

    .chip-score { background: rgba(232, 89, 12, 0.22); }
    .chip-score .dot { background: var(--clr-score); }
    .chip-score .chip-label { color: #b1420a; }
    .chip-score .chip-value { color: #963707; }

    @media (max-width: 576px) {
        .staff-divider {
            display: none;
        }
    }
</style>
@endpush
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Toast with Placements -->
    <div class="bs-toast toast toast-placement-ex m-2" role="alert" aria-live="assertive" aria-atomic="true"
        data-delay="2000">
        <div class="toast-header">
            <i class="bx bx-bell me-2"></i>
            <div class="me-auto fw-semibold toast-title">Bootstrap</div>
            <small>Now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">Fruitcake chocolate bar tootsie roll gummies gummies jelly beans cake.</div>
    </div>

    <!-- Basic Layout -->
    <div class="container-fluid p-0">
        <div class="scorecard-header">
            <h1 class="scorecard-title">
                <span class="title-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10M18 20V4M6 20v-4"/></svg>
                </span>
                <span class="fs-24">{{ $pageName }}</span>
            </h1>

            <div class="scorecard-actions">
                <a href="javascript:void(0)" data-href="{{ route($downloadPdfUrl) }}" class="btn-report btn-pdf downloadReport">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    Download PDF
                </a>
                <a href="javascript:void(0)" data-href="{{ route($downloadCsvUrl) }}" class="btn-report btn-csv downloadReport">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    Download CSV
                </a>
                <div class="month-picker-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    <input type="month" id="score_card_month_year" name="month_year" value="{{ $monthYear }}">
                </div>
            </div>
        </div>
        <div class="text-nowrap" id="resultData">
            @include('admin.staffScoreCard.ajaxResultData')
        </div>
    </div>
</div>
@csrf
<input type="hidden" name="ajaxRequestUrl" id="ajaxRequestUrl" value="{{ route($ajaxPaginationRequestUrl) }}">
@push('scripts')
<script>
    $(document).ready(function() {
        $('#score_card_month_year').change(function() {
            showLoader();

            let formData = new FormData();
            formData.append('month_year', $(this).val());
            let action = $("#ajaxRequestUrl").val();
            ajaxRequest($(this),formData,action,'ajaxResponseScorecard');
        })

        $('.downloadReport').click(function() {
            let href = $(this).attr('data-href');
            href += '/'+$('#score_card_month_year').val();
            window.open(href, '_blank');
        });

    });

    function ajaxResponseScorecard(_this, response) {
        hideLoader();
        if (response.status == "success") {
            $("#resultData").html(response.html);
        }
    }
</script>
@endpush
@endsection
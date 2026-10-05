@extends(_getConstant('dir_path.ADMIN_DIR_PATH').'.admin_layout')
@section('admin_content')
@push('styles')
<style>
    .staff-summary {
        border: 0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
        margin-bottom: 15px;
    }

    .staff-summary .panel-heading {
        background: #4f63ff;
        color: #fff;
        padding: 10px 15px;
        font-size: 15px;
    }

    .label-info {
        background: rgba(255, 255, 255, .15);
        padding: 4px 10px;
        border-radius: 12px;
    }

    /* SECTION */

    .section-block {
        background: #fff;
        border: 1px solid #ececec;
        border-radius: 8px;

        padding: 12px;

        margin-bottom: 12px;
    }

    .section-block h4 {
        margin: 0 0 10px;

        padding-bottom: 7px;

        font-size: 14px;

        font-weight: 600;

        border-bottom: 1px solid #eee;

        color: #2d3748;
    }

    .section-block h4 i {
        margin-right: 5px;
    }

    /* ROW */

    .info-row {
        margin-bottom: 6px;
    }

    .info-row>div {
        margin-bottom: 6px;

        font-size: 12px;

        line-height: 1.4;
    }

    .info-row strong {
        color: #444;
    }

    /* HIGHLIGHT */

    .highlight-box {
        background: #f4fff7;

        border: 1px solid #d7f2dd;

        border-radius: 6px;

        padding: 8px 12px;

        margin-top: 8px;
    }

    .highlight-box p {
        margin: 0;
    }

    .highlight-box .amount {
        font-size: 18px;

        font-weight: 700;

        color: #198754;
    }

    /* ALERT */

    .alert-warning {
        padding: 10px 12px;

        font-size: 12px;

        border-radius: 6px;

        margin-top: 10px;
    }

    /* SALARY */

    .salary-section {
        background: #f8f9ff;

        padding: 15px;

        border-radius: 10px;
    }

    .salary-section label {
        font-size: 12px;

        margin-bottom: 4px;

        font-weight: 600;
    }

    .salary-section .form-group {
        margin-bottom: 10px;
    }

    .salary-section .form-control {
        height: 34px;

        border-radius: 6px;

        font-size: 12px;

        padding: 6px 10px;

        box-shadow: none;
    }

    .salary-summary-box {
        background: #fff;

        border: 1px solid #ececec;

        border-radius: 8px;

        padding: 12px;

        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
    }

    .salary-summary-box p {
        display: flex;

        justify-content: space-between;

        margin: 0 0 8px;

        font-size: 12px;
    }

    .salary-summary-box p:last-child {
        margin-bottom: 0;
    }

    .salary-summary-box span {
        float: none;
    }

    .salary-summary-box b {
        font-size: 13px;
    }

    .salary-summary-box hr {
        margin: 10px 0;
    }

    /* COLORS */

    .text-success {
        color: #1e9d54 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .text-warning {
        color: #ff9800 !important;
    }

    .text-primary {
        color: #4f63ff !important;
    }

    /* BUTTON */

    .btn-generate {
        background: #4f63ff;

        border: 0;

        color: #fff;

        border-radius: 6px;

        padding: 8px 18px;

        font-size: 13px;

        font-weight: 600;
    }

    .btn-generate:hover {
        background: #3f54f5 !important;

        color: #fff !important;
    }

    .mb-15 {
        margin-bottom: 10px;
    }

    @media(max-width:767px) {

        .staff-summary .panel-body {
            padding: 10px;
        }

        .salary-summary-box {
            margin-top: 10px;
        }

    }
</style>
@endpush
<div class="container-xxl flex-grow-1 container-p-y mb-4">
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
    <div class="container-fluid">
        <div class="table-responsive text-nowrap" id="resultData">
            @if (isset($dataArr['isEdit']) && $dataArr['isEdit'] == '1')
                @include('admin.staff.paySlipAjaxEdit')
            @else
                @include('admin.staff.paySlipAjax')
            @endif
        </div>
    </div>
</div>
@csrf
<input type="hidden" name="ajaxRequestUrl" id="ajaxRequestUrl" value="{{ route($ajaxPaginationRequestUrl) }}">
@push('scripts')
<script>
    $(document).ready(function() {
        $('body').on('change', '#month_year', function() {
            let formData = new FormData();
            formData.append('month_year', $(this).val());
            formData.append('staff_id', $('#staff_id').val());
            let action = $("#ajaxRequestUrl").val();
            ajaxRequest($(this), formData, action, 'ajaxResponseSalary');
        })

        $('body').on('change', '#staff_pay_head_id', function() {
            var selectedValue = $(this).val();
            var selectedText = $(this).find('option:selected').text();
            var payHeadType = $(this).find('option:selected').data('type'); 
            if ($("#pay_heads_" + selectedValue).length > 0) {
                return; // If it exists, stop the function
            }
            if (selectedValue == '') {
                return;
            }
            $class = 'text-danger';
            $calculationType = 'deductionAmount';
            if(payHeadType == 'Earning'){
                $class = 'text-success';
                $calculationType = 'earningAmount';
            }
            var newColumn = '<div class="form-inline row mb-15">'+
                        '<div class="col-sm-6">'+
                            '<label class="'+$class+'">'+selectedText+'</label>'+
                        '</div>'+
                        '<div class="col-sm-3">'+
                            '<input type="number" class="form-control '+$calculationType+' calculateSalary" name="pay_head_id['+selectedValue+']" id="pay_heads_'+selectedValue+'" value="0.00">'+
                        '</div>'+
                    '</div>';
            $("#addNewColumn").append(newColumn);
        });

        $(document).on('input', '.calculateSalary', function() {
            var totalEarnings = 0;
            var totalDeductions = 0;

            $('.earningAmount').each(function() {
                var val = parseFloat($(this).val()) || 0;
                totalEarnings += val;
            });
            $('.deductionAmount').each(function() {
                var val = parseFloat($(this).val()) || 0;
                totalDeductions += val;
            });
            var basicSalary = parseFloat($('#basicSalary').val()) || 0;
            var netSalary = basicSalary + totalEarnings - totalDeductions;
            $('.totalEarning').text(' '+totalEarnings.toFixed(2));
            $('.totalDeduction').text(' '+totalDeductions.toFixed(2));
            $('.netPayable').text(' '+netSalary.toFixed(2));
        });

    });

    function ajaxResponseSalary(_this, response) {
        if (response.status == "success") {
            $("#resultData").html(response.html);
        }
    }
</script>
@endpush
@endsection
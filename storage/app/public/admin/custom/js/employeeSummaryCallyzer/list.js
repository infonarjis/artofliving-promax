'use strict';

// Global date filter state — empty means "no filter applied"
let startDate = '';
let endDate   = '';

$(document).ready(function () {

    // ─── Read DB values from hidden inputs ────────────────────────────────────
    const callFrom = $('#callFrom').val(); // 'YYYY-MM-DD HH:mm:ss' or ''
    const callTo   = $('#callTo').val();   // 'YYYY-MM-DD HH:mm:ss' or ''

    // ─── Initialise DateRangePicker ───────────────────────────────────────────
    $('#daterange').daterangepicker({
        opens          : 'left',
        maxDate        : moment(),
        autoUpdateInput: false,          // ← prevent auto-fill on init
        autoApply      : false,
        timePicker     : true,
        timePicker24Hour: true,
        timePickerSeconds: false,
        locale: {
            format     : 'DD/MM/YYYY HH:mm',
            cancelLabel: 'Clear',
        },
        // Restore saved range, or default to today's boundaries for the UI only
        startDate: callFrom
            ? moment(callFrom, 'YYYY-MM-DD HH:mm:ss')
            : moment().startOf('day'),
        endDate: callTo
            ? moment(callTo, 'YYYY-MM-DD HH:mm:ss')
            : moment().endOf('day'),
    });

    // ─── Restore saved range into globals (only when both values exist) ───────
    if (callFrom && callTo) {
        startDate = callFrom;
        endDate   = callTo;
    }

    // ─── Initial data load ────────────────────────────────────────────────────
    getAjaxPaginationData();

    // ─── Apply: user confirmed a selection ───────────────────────────────────
    $('#daterange').on('apply.daterangepicker', function (ev, picker) {
        startDate = picker.startDate.format('YYYY-MM-DD HH:mm:ss');
        endDate   = picker.endDate.format('YYYY-MM-DD HH:mm:ss');

        // Update visible input in display format
        $(this).val(
            picker.startDate.format('DD/MM/YYYY HH:mm') +
            ' - ' +
            picker.endDate.format('DD/MM/YYYY HH:mm')
        );

        // Sync hidden inputs
        $('#callFrom').val(startDate);
        $('#callTo').val(endDate);

        $('#clearDateRange').show();
        getAjaxPaginationData();
    });

    // ─── Cancel / "Clear" button inside picker ────────────────────────────────
    $('#daterange').on('cancel.daterangepicker', function () {
        clearDateFilter();
    });

    // ─── External clear (×) button ────────────────────────────────────────────
    $('#clearDateRange').on('click', function () {
        clearDateFilter();
    });

    // ─── Staff filter ─────────────────────────────────────────────────────────
    $('#staff_id').on('change', function () {
        getAjaxPaginationData();
    });

});

// ─── Helper: reset date filter state ─────────────────────────────────────────
function clearDateFilter() {
    startDate = '';
    endDate   = '';

    $('#daterange').val('');
    $('#callFrom').val('');
    $('#callTo').val('');
    $('#clearDateRange').hide();

    getAjaxPaginationData();
}

// ─── Build & fire AJAX request ────────────────────────────────────────────────
function getAjaxPaginationData() {
    showLoader();
    const formData = new FormData();

    // Pagination limit
    const limit = ($("#recordLimit").length && $("#recordLimit").val() > 0)
        ? $("#recordLimit").val()
        : 10;
    formData.append('limit', limit);

    // Current page
    const page = ($("#page").length && $("#page").val() > 0)
        ? $("#page").val()
        : 1;
    formData.append('page', page);

    // Date range — only appended when a filter is active
    if (startDate) formData.append('call_from', startDate);
    if (endDate)   formData.append('call_to',   endDate);

    // Staff filter
    if ($('#staff_id').length && $('#staff_id').val() && $('#staff_id').val() != '0') {
        formData.append('staff_id', $('#staff_id').val());
    }

    const url = $('#ajaxRequestUrl').val();
    ajaxRequest('#resultData', formData, url, 'ajaxPaginationResponse');
}

// ─── AJAX response handlers ───────────────────────────────────────────────────
function ajaxPaginationResponse(selector, response) {
    hideLoader();
    if (response.status === 'success') {
        $('.all_check').prop('checked', false);
        $(selector).html(response.html);
    }
}

function ajaxChangeStatusResponse(selector, response) {
    const isSuccess = response.status === 'success';

    $('.all_check, .checkboxId').prop('checked', false);

    showNotification(
        'top-0', 'end-0',
        isSuccess ? 'bg-success' : 'bg-danger',
        'withicon',
        isSuccess ? 'fa fa-check' : 'fa fa-times',
        response.msg,
        isSuccess ? 'Success' : 'Failed'
    );

    if (isSuccess) {
        setTimeout(getAjaxPaginationData, 1500);
    }
}

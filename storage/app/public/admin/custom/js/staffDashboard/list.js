startDate = '';
endDate = '';
$(document).ready((function () {
    loadMemberGraph(startDate, endDate);

    // Staff Id:
    $('#staff_id').change(function () {
        var staff_id = $(this).val();
        if (staff_id) {
            let staff_id_url = btoa(staff_id);
            let staffLoginUrl = $("#base_url").val() + '/staff/login-history/' + staff_id_url;
            $('#staffLoginLink').attr('href', staffLoginUrl);
        } else {
            $('#staffLoginLink').attr('href', $("#base_url").val() + '/staff/login-history');
        }
        loadMemberGraph(startDate, endDate);
    });
    // Date Picker:
    $('input[name="daterange"]').daterangepicker({
        opens: 'right',
        maxDate: moment(),
        autoApply: false,
        autoUpdateInput: false
    }, function (start, end, label) {
        $('input[name="daterange"]').val(
            start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY')
        );
    });

    var picker = $('input[name="daterange"]').data('daterangepicker');
    // Add Clear button
    picker.container.find('.drp-buttons').prepend(
        '<button type="button" class="btn btn-sm btn-clear" style="float:left;">Clear</button>'
    );

    // Clear button click
    picker.container.on('click', '.btn-clear', function () {
        $('input[name="daterange"]').val('');
        startDate = '';
        endDate = '';
        picker.hide();
        loadMemberGraph(startDate, endDate);
    });
    $('input[name="daterange"]').on('apply.daterangepicker', function (ev, picker) {
        startDate = picker.startDate.format('YYYY-MM-DD');
        endDate = picker.endDate.format('YYYY-MM-DD');
        loadMemberGraph(startDate, endDate);
    });
    $('input[name="daterange"]').on('cancel.daterangepicker', function (ev, picker) {
        startDate = '';
        endDate = '';
        loadMemberGraph(startDate, endDate);
    });
}));

// Member Daily Income Graph Get Data : Date : 25-02-2022
function loadMemberGraph(startDate, endDate) {
    var formData = new FormData();
    formData.append('staff_id', $('#staff_id').val());
    formData.append('start_date', startDate)
    formData.append('end_date', endDate)
    var url = $("#base_url").val() + '/staff/get-graph-data';
    ajaxRequest('#resultData', formData, url, 'ajaxMemberGraphResponse');
}

function ajaxMemberGraphResponse(_this, response) {
    if (response.status == "success") {
        // Append Data:
        if (response.data.staff_name != '') {
            $('#showStaffDetails').removeClass('d-none');
            $('#staffName').html(response.data.staff_name);
            $('#staffLastLogin').html(response.data.staff_last_login);
            // Staff Total Working Times:
            $('.totalTimeSpend').removeClass('d-none');
            $('#totalTimeSpend').html(response.data.totalTimeSpend);
        } else {
            $('#showStaffDetails').addClass('d-none');
            $('.totalTimeSpend').addClass('d-none');
        }
        $('#viewProfileCount').html(response.data.viewProfileCount);
        $('#memberPaymentCount').html(response.data.memberPaymentCount);
        $('#editprofileCount').html(response.data.editprofileCount);
        $('#uploadPhotoCount').html(response.data.uploadPhotoCount);
        $('#assignedMemberCount').html(response.data.assignedMemberCount);
        $('#pendingAssignMemberCount').html(response.data.pendingAssignMemberCount);
        $('#addCommentCount').html(response.data.addCommentCount);
    } else {
        ajaxMemberGraphResponse
    }
}

function getDashboardEvent(key = '') {
    var formData = new FormData();
    formData.append('activity_type', key);
    formData.append('staff_id', $('#staff_id').val());
    formData.append('start_date', endDate);
    formData.append('end_date', startDate);
    formData.append('checkCount', $('.' + key).text());
    var action = $("#base_url").val() + '/staff/dashboard-data';
    ajaxRequest($(this), formData, action, 'ajaxChangeStatusResponse');
}

function ajaxChangeStatusResponse(_this, response) {
    if (response.status == 'success') {
        window.location.href = response.redirectUrl;
    } else {
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", 'No Data Found', "Failed");
    }
}
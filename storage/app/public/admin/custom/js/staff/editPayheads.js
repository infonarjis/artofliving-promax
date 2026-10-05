$(document).ready(function () {
    $("#staff_pay_head_id").select2({ placeholder: "Select Staff Pay Head", allowClear: !0 });

    $(document).on('change', '#staff_pay_head_id', function() {
        var selectedValue = $(this).val();
        var selectedText = $(this).find('option:selected').text();
        var payHeadType = $(this).find('option:selected').data('type'); 
        if ($("#pay_heads_" + selectedValue).length > 0) {
            return; // If it exists, stop the function
        }
        $class = 'pay-heads-danger';
        if(payHeadType == 'Earning'){
            $class = 'pay-heads-success';
        }
        var newColumn = `<div class="mb-2 row">
                <label for="pay_heads_${selectedValue}" class="col-md-7 col-form-label ${$class} ">${selectedText} </label>
                <div class="col-md-5 d-flex gap-2">
                    <input class="form-control" type="number" name="pay_heads_${selectedValue}" id="pay_heads_${selectedValue}">
                    <button type="button" class="add-pay-heads-btn removeDropdown" data-pay_head_id="${selectedValue}" id="removeDropdown"><i class='bx bx-trash' ></i></i></button>
                </div>
            </div>`;
        $("#addNewColumn").append(newColumn);
    });
    
    $(document).on('click', '.removeDropdown', function() {
        $(this).closest('.row').remove();
        let formData = new FormData($("#staffPayHeadForm")[0]);
        formData.append("pay_head_id", $(this).attr('data-pay_head_id'));
        let baseUrl = $('#base_url').val();
        let actionUrl = baseUrl+'/staff/pay-heads/remove';
        ajaxRequest("#staffPayHeadForm", formData, actionUrl, "responseAddEdit");
    });

    // Fetch Plan Data on Change
    $(".getDateRangeData").on("change", function () {
        let dateRange = $(this).val();
        if (dateRange) {
            let formData = new FormData();
            formData.append("staff_id", $("#staff_id").val());
            formData.append("date_range", dateRange);
            let requestUrl = $("#getstaffSalaryPayhead").val();
            ajaxRequest("#resultData", formData, requestUrl, "ajaxGetDateRangeDataResponse");
        } else {
            showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select at least one plan!", "Failed");
        }
    });
    $(".getDateRangeData").trigger("change");

    // Generate Salary Slips:
    $(document).on('click', '#generateSalarySlips', function() {
        let formData = new FormData($("#staffPayHeadForm")[0]);
        formData.append("staff_id", $("#staff_id").val());
        formData.append("date_range", $("#date_range").val());
        let baseUrl = $('#base_url').val();
        let actionUrl = baseUrl+'/staff/generate-salary';
        ajaxRequest("#resultData", formData, actionUrl, "responceSalaryGenerate");
    });

    // Form Submission Handling
    $("#staffPayHeadSubmit").on("click", function () {
        let $button = $(this);
        $button.prop("disabled", true).html('<i class="mr-1 fa fa-spin fa-refresh h3 jqvmap-region"></i> Loading...');
        let formData = new FormData($("#staffPayHeadForm")[0]);
        let actionUrl = $("#staffPayHeadForm").attr("action");
        ajaxRequest("#staffPayHeadForm", formData, actionUrl, "responseAddEdit");
    });
});

// Handle Plan Data Response
function ajaxGetDateRangeDataResponse(target, response) {
    if (response.status === "success") {
        $("#resultData").html(response.html);
        $('#staffBasicSalary').html(response.data.staffBasicSalary);
        $('#totalEarningAmount').html(response.data.totalEarningAmount);
        $('#totalDeductionAmount').html(response.data.totalDeductionAmount);
        $('#totalPayableAmount').html(response.data.totalPayableAmount);
        $('#takenLeaves').html(response.data.takenLeaves);
        $('#workingDays').html(response.data.workingDays);
    }
}

// Handle Form Submission Response
function responseAddEdit(target, response) {
    let $button = $("#staffPayHeadSubmit");
    $button.text("Submit").prop("disabled", false);
    if (response.status === "success") {
        $('#staffBasicSalary').html(response.data.staffBasicSalary);
        $('#totalEarningAmount').html(response.data.totalEarningAmount);
        $('#totalDeductionAmount').html(response.data.totalDeductionAmount);
        $('#totalPayableAmount').html(response.data.totalPayableAmount);

        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", response.msg, "Success");
    } else {
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", response.msg, "Failed");
    }
}
function responceSalaryGenerate(target, response) {
    if (response.status === "success") {
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", response.msg, "Success");
    } else {
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", response.msg, "Failed");
    }
}

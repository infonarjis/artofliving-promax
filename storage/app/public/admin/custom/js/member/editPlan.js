$(document).ready(function () {

    /*** Character Counter ***/
    $(".checkLimit").on('keyup', function () {
        const maxLength = parseInt($(this).attr("maxlength"), 10);
        const currentLength = $(this).val().length;
        const remaining = maxLength - currentLength;
        $("#add-char").text(`${currentLength} / ${maxLength} (${remaining} Remaining Character${remaining !== 1 ? 's' : ''})`);
    });

    /*** Assignment Type helpers (New Plan vs Add-On Only) ***/
    function getAssignType() {
        return $('input[name="assignType"]:checked').val() || 'new_plan';
    }

    function toggleAssignTypeUI() {
        const assignType = getAssignType();
        if (assignType === 'addon_only') {
            $("#planSelectWrap").hide();
            $("#plan_id").prop('required', false).val(null).trigger('change.select2');
        } else {
            $("#planSelectWrap").show();
            $("#plan_id").prop('required', true);
        }
        updatePlanCalculation();
    }

    $('input[name="assignType"]').on('change', function () {
        toggleAssignTypeUI();
    });

    /*** Central function: called on plan change, add-on checkbox click, and assign-type toggle ***/
    function updatePlanCalculation() {
        const assignType = getAssignType();
        const planId = $("#plan_id").val();
        const memberId = $("#memberId").val();
        const addOnIds = $('.addOnPackageCheckBox:checked').map(function () {
            return $(this).val();
        }).get();

        if (assignType === 'new_plan' && !planId) {
            $("#resultData").html('<h4 class="plan_typesShow text-center">No Plan Selected</h4>');
            return;
        }
        if (assignType === 'addon_only' && addOnIds.length === 0) {
            $("#resultData").html('<h4 class="plan_typesShow text-center">Select add-on package(s) to assign</h4>');
            return;
        }

        const formData = new FormData();
        formData.append("assignType", assignType);
        formData.append("memberId", memberId);
        if (assignType === 'new_plan') {
            formData.append("planId", planId);
        }

        // send as a real array: package_ids[]
        addOnIds.forEach(function (id) {
            formData.append("package_ids[]", id);
        });

        const url = $("#getPlanDataUrl").val();
        ajaxRequest("#resultData", formData, url, "ajaxGetPlanResponse");
    }

    /*** Plan Selection Trigger ***/
    $(".getPlanData").on('change', function () {
        updatePlanCalculation();
    });

    /*** Add-On Checkbox Trigger ***/
    $(document).on('click', '.addOnPackageCheckBox', function () {
        updatePlanCalculation();
    });

    /*** Form Submit ***/
    $("#planAssignSubmit").on('click', function () {
        const assignType = getAssignType();
        const planId = $("#plan_id").val() ? $("#plan_id").val().trim() : "";
        const addOnSelected = $('.addOnPackageCheckBox:checked').map(function () {
            return $(this).val();
        }).get();
        const paymentMode = $("#payment_mode").val().trim();
        const paymentNote = $("#payment_note").val().trim();

        if (assignType === 'new_plan' && planId === "") {
            showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select a plan!", "Failed");
            return false;
        }
        if (assignType === 'addon_only' && addOnSelected.length === 0) {
            showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select at least one add-on package!", "Failed");
            return false;
        }
        if (paymentMode === "") {
            showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select payment mode!", "Failed");
            return false;
        }
        if (paymentNote === "") {
            showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please enter payment note!", "Failed");
            return false;
        }

        $(this).html('<i class="me-1 bx bx-loader-alt bx-spin text-white"></i> Loading...');
        $(this).prop("disabled", true);

        const formData = new FormData($("#planAssignForm")[0]);

        // remove any stray add_on_id[] fields already serialized by the form
        // and re-append checked ones explicitly (avoids duplicates / stale state)
        for (const key of Array.from(formData.keys())) {
            if (key === "add_on_id[]") formData.delete(key);
        }
        addOnSelected.forEach(function (id) {
            formData.append("add_on_id[]", id);
        });

        const actionUrl = $("#planAssignForm").attr("action");
        ajaxRequest("#planAssignForm", formData, actionUrl, "responseAddEdit");
    });

    // Initialize UI state on page load (also covers the disabled-radio edge case)
    toggleAssignTypeUI();
});

/*** AJAX Response for Plan Data ***/
function ajaxGetPlanResponse(e, response) {
    if (response.status === "success") {
        $("#resultData").html(response.html);
    } else {
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", response.msg || "Something went wrong!", "Failed");
    }
}

/*** AJAX Response for Form Submit ***/
function responseAddEdit(e, response) {
    const $submitBtn = $("#planAssignSubmit");
    $submitBtn.text("Submit");
    $submitBtn.prop("disabled", false);

    if (response.status === "success") {
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", response.msg, "Success");
        setTimeout(() => {
            window.location.href = response.redirect;
        }, 2000);
    } else {
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", response.msg || "Failed to submit!", "Failed");
    }
}
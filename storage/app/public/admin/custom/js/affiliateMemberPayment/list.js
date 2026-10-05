$(document).ready((function () { getAjaxPaginationData() })); function getAjaxPaginationData() { let a = new FormData; let e = $(".tabClick.active").attr("id"); a.append("activeTab", e); if ($("#recordLimit").length > 0 && $("#recordLimit").val() != "" && $("#recordLimit").val() != 0) { a.append("limit", $("#recordLimit").val()) } else { a.append("limit", 10) } let t = 1; if ($("#page").length > 0 && $("#page").val() != "" && $("#page").val() != 0) { t = $("#page").val() } if ($("#searchText").val() != "") { a.append("searchKeyword", $("#searchText").val()) } a.append("page", t); a.append("conditionColumn", $(".tabClick.active").attr("data-conditionColumn")); a.append("conditionVal", $(".tabClick.active").attr("data-conditionVal")); let c = $("#ajaxRequestUrl").val(); ajaxRequest("#resultData", a, c, "ajaxPaginationResponse") } function ajaxPaginationResponse(a, e) { if (e.status == "success") { $(".all_check").prop("checked", false); $(a).html(e.html) } scrollTop("#myTab"); $.each(e.data.tabCount, (function (a, e) { $("#" + a).find(".countRecord").text(e) })) } function ajaxChangeStatusResponse(a, e) { if (e.status == "success") { $(".all_check").prop("checked", false); $(".checkboxId").prop("checked", false); showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", e.msg, "Success"); setTimeout((function () { getAjaxPaginationData() }), 1500) } else { $(".all_check").prop("checked", false); $(".checkboxId").prop("checked", false); showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", e.msg, "Failed") } }

function getProfileReportId(member_id) {
    $('#followup_id').val(member_id);
}

$(document).ready(function () {
    $("body").on("click", "#addMemberCommentSubmit", function () {
        let submitButton = $(this);
        let form = $("#addMemberCommentForm");
        // Validate form
        if (!form.valid()) return false;
        // Disable button and show loading state
        submitButton.text("Loading...")
            .prepend('<i class="me-1 bx bx-loader-alt bx-spin text-white"></i> ')
            .prop("disabled", true);
        let formData = new FormData(form[0]);
        let actionUrl = form.attr("action");

        ajaxRequest(submitButton, formData, actionUrl, "responseAddComment");
    });
});

function responseAddComment(button, response) {
    let submitButton = $("#addMemberCommentSubmit");

    // Enable the submit button
    button.prop("disabled", false);
    submitButton.text("Save comments");

    if (response.status === "success") {
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", response.msg, "Success");
        location.reload();
    } else {
        submitButton.prop("disabled", false);
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", response.msg, "Failed");
    }
}
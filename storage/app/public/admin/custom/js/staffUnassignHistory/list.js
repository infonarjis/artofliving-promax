$(document).ready(function () {
    getAjaxPaginationData();
    $("#assignStaffMember").click(function (e) {
        let a = $(this).attr("data-action");
        let t = new FormData();
        if ($(".checkboxId:checked").length <= 0) {
            showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select at least one record to process!", "Failed");
            return false;
        }
        if ($("#staffAdminId").val() == "") {
            showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select staff to process!", "Failed");
            return false;
        }
        $(this).prop("disabled", true);
        let i = [];
        $(".checkboxId:checked").each(function () {
            i.push($(this).val());
        });
        t.append("subAdminId", $("#staffAdminId").val());
        t.append("adminType", $("option:selected", "#staffAdminId").attr("data-type"));
        t.append("id", i);
        ajaxRequest($(this), t, a, "responseAssignMember");
    });
});
function getAjaxPaginationData() {
    let e = new FormData();
    let a = $(".tabClick.active").attr("id");
    e.append("activeTab", a);
    if ($("#recordLimit").length > 0 && $("#recordLimit").val() != "" && $("#recordLimit").val() != 0) {
        e.append("limit", $("#recordLimit").val());
    } else {
        e.append("limit", 10);
    }
    let t = 1;
    if ($("#page").length > 0 && $("#page").val() != "" && $("#page").val() != 0) {
        t = $("#page").val();
    }
    if ($("#searchText").val() != "") {
        e.append("searchKeyword", $("#searchText").val());
    }
    e.append("page", t);
    e.append("conditionColumn", $(".tabClick.active").attr("data-conditionColumn"));
    e.append("conditionVal", $(".tabClick.active").attr("data-conditionVal"));
    let i = $("#ajaxRequestUrl").val();
    ajaxRequest("#resultData", e, i, "ajaxPaginationResponse");
}
function ajaxPaginationResponse(e, a) {
    if (a.status == "success") {
        $(".all_check").prop("checked", false);
        $(e).html(a.html);
    }
    scrollTop("#myTab");
    $.each(a.data.tabCount, function (e, a) {
        $("#" + e)
            .find(".countRecord")
            .text(a);
    });
}
function responseAssignMember(e, a) {
    $(e).text("Assign Staff");
    if (a.status == "success") {
        $(".checkboxId").prop("checked", false);
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", a.msg, "Success");
        setTimeout(function () {
            $(e).prop("disabled", false);
            getAjaxPaginationData();
        }, 1500);
    } else {
        $(e).prop("disabled", false);
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", a.msg, "Failed");
    }
}

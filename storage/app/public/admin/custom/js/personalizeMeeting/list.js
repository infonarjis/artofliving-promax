$(document).ready((function() {
    getAjaxPaginationData()
}));

function getAjaxPaginationData() {
    let a = new FormData;
    let e = $(".tabClick.active").attr("id");
    a.append("activeTab", e);
    if ($("#recordLimit").length > 0 && $("#recordLimit").val() != "" && $("#recordLimit").val() != 0) {
        a.append("limit", $("#recordLimit").val())
    } else {
        a.append("limit", 10)
    }
    let t = 1;
    if ($("#page").length > 0 && $("#page").val() != "" && $("#page").val() != 0) {
        t = $("#page").val()
    }
    if ($("#searchText").val() != "") {
        a.append("searchKeyword", $("#searchText").val())
    }
    if ($("#personalizeMatchId").length > 0) {
        a.append("personalizeMatchId", $("#personalizeMatchId").val())
    }
    a.append("page", t);
    a.append("conditionColumn", $(".tabClick.active").attr("data-conditionColumn"));
    a.append("conditionVal", $(".tabClick.active").attr("data-conditionVal"));
    let c = $("#ajaxRequestUrl").val();
    ajaxRequest("#resultData", a, c, "ajaxPaginationResponse")
}

function ajaxPaginationResponse(a, e) {
    if (e.status == "success") {
        $(".all_check").prop("checked", false);
        $(a).html(e.html)
    }
    scrollTop("#myTab");
    $.each(e.data.tabCount, (function(a, e) {
        $("#" + a).find(".countRecord").text(e)
    }))
}

function ajaxChangeStatusResponse(a, e) {
    if (e.status == "success") {
        $(".all_check").prop("checked", false);
        $(".checkboxId").prop("checked", false);
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", e.msg, "Success");
        setTimeout((function() {
            getAjaxPaginationData()
        }), 1500)
    } else {
        $(".all_check").prop("checked", false);
        $(".checkboxId").prop("checked", false);
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", e.msg, "Failed")
    }
}
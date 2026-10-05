function getAjaxPaginationData() {
    let a = new FormData();
    if ($("#isFilterApply").length === 1 && $("#isFilterApply").val() == 1) {
        a = new FormData($("#filterForm")[0]);
        a.append("isFilterApply", $("#isFilterApply").val());
    } else {
        a = new FormData();
    }
    e = $(".tabClick.active").attr("id");
    if (a.append("activeTab", e), $("#recordLimit").length > 0 && "" != $("#recordLimit").val() && 0 != $("#recordLimit").val() ? a.append("limit", $("#recordLimit").val()) : a.append("limit", 10), t = 1, $("#page").length > 0 && "" != $("#page").val() && 0 != $("#page").val()) var t = $("#page").val();
    "" != $("#searchText").val() && a.append("searchKeyword", $("#searchText").val()), a.append("page", t), a.append("conditionColumn", $(".tabClick.active").attr("data-conditionColumn")), a.append("conditionVal", $(".tabClick.active").attr("data-conditionVal"));
    ajaxRequest("#resultData", a, $("#ajaxRequestUrl").val(), "ajaxPaginationResponse")
}

function ajaxPaginationResponse(a, e) {
    if (e.status === "success") {
        $(".all_check").prop("checked", false);
        $(a).html(e.html);
        scrollTop("#myTab");
        $(".totalSalesReports").html(e.data.totalSalesReports);
        $.each(e.data.tabCount, function (key, count) {
            $("#" + key).find(".countRecord").text(count);
        });
    }
}

function ajaxChangeStatusResponse(a, e) {
    "success" == e.status ? ($(".all_check").prop("checked", !1), $(".checkboxId").prop("checked", !1), showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", e.msg, "Success"), setTimeout(function () {
        getAjaxPaginationData()
    }, 1500)) : ($(".all_check").prop("checked", !1), $(".checkboxId").prop("checked", !1), showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", e.msg, "Failed"))
}

function getFilterFormResponse(el, response) {
    if (response.status == "success") {
        $("#filterModal").html(response.html);
        $($(el).attr("target-modal")).modal("show");

        $(".single").select2({ placeholder: "Please Select" });
    } else {
        $(el).prop("disabled", false);
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", response.msg, "Failed");
    }
}

$(document).ready(function () {
    getAjaxPaginationData()

    $("body").on("click", ".closeFilterPopup", function () {
        $("#filterModal").modal("hide");
    });

    $(".getfilterModal").click(function () {
        let actionUrl = $(this).attr("data-action");
        let formData = new FormData();

        if ($("#isFilterApply").length === 1 && $("#isFilterApply").val() == 1) {
            $("#filterModal").modal("show");
        } else {
            ajaxRequest($(this), formData, actionUrl, "getFilterFormResponse");
        }
    });

    $(document).on("submit", "#filterForm", function (e) {
        e.preventDefault();
        $("#isFilterApply").val(1);
        getAjaxPaginationData();
        setTimeout(function () {
            setTimeout(function () {
                $("#filterModal").modal("hide");
            }, 100);
        }, 100);
    });
});
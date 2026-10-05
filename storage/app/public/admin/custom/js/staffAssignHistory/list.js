function getFilterFormResponse(t, a) {
    "success" == a.status
        ? ($("#filterModal").html(a.html), $($(t).attr("target-modal")).modal("show"), $(".single").select2({ placeholder: "Please Select" }))
        : ($(t).prop("disabled", !1), showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", a.msg, "Failed"));
}
function getAjaxPaginationData() {
    if (1 == $("#isFilterApply").length && 1 == $("#isFilterApply").val()) {
        var t = new FormData($("#filterForm")[0]);
        t.append("isFilterApply", $("#isFilterApply").val());
    } else var t = new FormData();
    let a = $(".tabClick.active").attr("id");
    t.append("activeTab", a), $("#recordLimit").length > 0 && "" != $("#recordLimit").val() && 0 != $("#recordLimit").val() ? t.append("limit", $("#recordLimit").val()) : t.append("limit", 10);
    let e = 1;
    $("#page").length > 0 && "" != $("#page").val() && 0 != $("#page").val() && (e = $("#page").val()),
        "" != $("#searchText").val() && t.append("searchKeyword", $("#searchText").val()),
        t.append("page", e),
        t.append("conditionColumn", $(".tabClick.active").attr("data-conditionColumn")),
        t.append("conditionVal", $(".tabClick.active").attr("data-conditionVal"));
    let i = $("#ajaxRequestUrl").val();
    ajaxRequest("#resultData", t, i, "ajaxPaginationResponse");
}
function ajaxPaginationResponse(t, a) {
    "success" == a.status && ($(".all_check").prop("checked", !1), $(t).html(a.html)),
        scrollTop("#myTab"),
        $.each(a.data.tabCount, function (t, a) {
            $("#" + t)
                .find(".countRecord")
                .text(a);
        });
}
$(document).ready(function () {
    getAjaxPaginationData(),
        $("body").on("click", ".closeFilterPopup", function () {
            $("#filterModal").modal("hide");
        }),
        $(".getfilterModal").click(function () {
            let t = $(this).attr("data-action"),
                a = new FormData();
            1 == $("#isFilterApply").length && 1 == $("#isFilterApply").val() ? $("#filterModal").modal("show") : ajaxRequest($(this), a, t, "getFilterFormResponse");
        }),
        $(document).on("submit", "#filterForm", function (t) {
            t.preventDefault(),
                $("#isFilterApply").val(1),
                getAjaxPaginationData(),
                setTimeout(function () {
                    setTimeout(function () {
                        $("#filterModal").modal("hide");
                    }, 100);
                }, 100);
        });
});

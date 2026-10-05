$(document).ready(function () {
    getAjaxPaginationData();

    $('#languageChangeModal').on('hidden.bs.modal', function () {
        location.reload();
    })

    $("body").on("click", "#changeLanguageSubmit", function () {
        if (!$("body").find("#changeLanguageForm").valid()) return !1;
        {
            $(this).text(""), $(this).append('<i class="me-1 bx bx-loader-alt bx-spin text-white"></i>').append("Loading..."), $(this).prop("disabled", !0);
            let e = new FormData($("#changeLanguageForm")[0]),
                t = $("#changeLanguageForm").attr("action");
            ajaxRequest($(this), e, t, "responseEditLanguage");
        }
    }),
    // Language Changes:
    $("#resultData,#memberModalDiv").on("click", ".languageChange", function () {
        if ($("." + $(this).data("class")).length > 0) {
            $("." + $(this).data("class")).remove();
        }
        $("#" + $(this).data("removeclass")).modal("hide");
        let t = $(this).data("removeclass");
        setTimeout(function () {
            $("." + t).remove();
        }, 1500);
        let e = new FormData();
        if ($(this).data("key") != "") {
            e.append("key", $(this).data("key"));
            e.append("langCode", $(this).data("lang_code"));
            if ($(this).data("action") != undefined && $(this).data("action") != "") {
                let t = $(this).data("action");
                ajaxRequest($(this), e, t, "ajaxgetLangDataResponce");
            } else {
                showNotification("top", "right", "danger", "withicon", "fa fa-times", "Something wend wrong!", "Failed");
            }
        }
    });
});

function ajaxgetLangDataResponce(_this, response){
    if (response.status == "success") {
        $('#languageChangeModal').html(response.html);
        $('#languageChangeModal').modal('show');
    } else {
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-check", response.msg, "Failed");
    }
}

function responseEditLanguage(_this, response) {
    $(_this).text("Submit");
    (_this).prop("disabled", false);
    if (response.status == "success") {
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", response.msg, "Success");
    } else {
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-check", response.msg, "Failed");
    }
}

function getAjaxPaginationData() {
    let a = new FormData();
    let e = $(".tabClick.active").attr("id");
    a.append("activeTab", e);
    if ($("#recordLimit").length > 0 && $("#recordLimit").val() != "" && $("#recordLimit").val() != 0) {
        a.append("limit", $("#recordLimit").val());
    } else {
        a.append("limit", 10);
    }
    let t = 1;
    if ($("#page").length > 0 && $("#page").val() != "" && $("#page").val() != 0) {
        t = $("#page").val();
    }
    if ($("#searchText").val() != "") {
        a.append("searchKeyword", $("#searchText").val());
    }
    if ($("#languageId").length > 0 && $("#languageId").val() != "" && $("#languageId").val() != 0) {
        a.append("languageId",$("#languageId").val());
    }
    a.append("page", t);
    a.append("conditionColumn", $(".tabClick.active").attr("data-conditionColumn"));
    a.append("conditionVal", $(".tabClick.active").attr("data-conditionVal"));
    let c = $("#ajaxRequestUrl").val();
    ajaxRequest("#resultData", a, c, "ajaxPaginationResponse");
}
function ajaxPaginationResponse(a, e) {
    if (e.status == "success") {
        $(".all_check").prop("checked", false);
        $(a).html(e.html);
    }
    scrollTop("#myTab");
    $.each(e.data.tabCount, function (a, e) {
        $("#" + a)
            .find(".countRecord")
            .text(e);
    });
}
function ajaxChangeStatusResponse(a, e) {
    if (e.status == "success") {
        $(".all_check").prop("checked", false);
        $(".checkboxId").prop("checked", false);
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", e.msg, "Success");
        setTimeout(function () {
            getAjaxPaginationData();
        }, 1500);
    } else {
        $(".all_check").prop("checked", false);
        $(".checkboxId").prop("checked", false);
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", e.msg, "Failed");
    }
}

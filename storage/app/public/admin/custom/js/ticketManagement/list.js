$(document).ready(function () {
    getAjaxPaginationData();

    $("#resultData,#memberModalDiv").on("click", ".viewComment", function () {
        if ($("." + $(this).data("class")).length > 0) {
            $("." + $(this).data("class")).remove();
        }
        $("#" + $(this).data("removeclass")).modal("hide");
        let e = $(this).data("removeclass");
        setTimeout(function () {
            $("." + e).remove();
        }, 1500);
        let t = new FormData();
        if ($(this).data("id") != "") {
            t.append("id", $(this).data("id"));
            if ($(this).data("action") != undefined && $(this).data("action") != "") {
                let e = $(this).data("action");
                ajaxRequest($(this), t, e, "ajaxViewCommentResponse");
            } else {
                showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Something wend wrong!", "Failed");
            }
        }
    });
    $("#resultData,#memberModalDiv").on("click", ".addComment", function () {
        if ($("." + $(this).data("class")).length > 0) {
            $("." + $(this).data("class")).remove();
        }
        $("#" + $(this).data("removeclass")).modal("hide");
        let e = $(this).data("removeclass");
        setTimeout(function () {
            $("." + e).remove();
        }, 1500);
        let t = new FormData();
        if ($(this).data("id") != "") {
            t.append("id", $(this).data("id"));
            if ($(this).data("action") != undefined && $(this).data("action") != "") {
                let e = $(this).data("action");
                ajaxRequest($(this), t, e, "ajaxAddCommentResponse");
            } else {
                showNotification("top", "right", "danger", "withicon", "fa fa-times", "Something wend wrong!", "Failed");
            }
        }
    });
    $("body").on("click", "#addMemberCommentSubmit", function () {
        if ($("body").find("#addMemberCommentForm").valid()) {
            $(this).text("");
            $(this).append('<i class="me-1 bx bx-loader-alt bx-spin text-white"></i>').append("Loading...");
            $(this).prop("disabled", true);
            let e = new FormData($("#addMemberCommentForm")[0]);
            let t = $("#addMemberCommentForm").attr("action");
            ajaxRequest($(this), e, t, "responseAddComment");
        } else {
            return false;
        }
    });
});

function responseAddComment(e, t) {
    $(e).prop("disabled", false);
    $("#addMemberCommentSubmit").text("Save comments");
    if (t.status == "success") {
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", t.msg, "Success");
        $("#addMemberCommentForm")[0].reset();
        $("#comment").val("");
        $("#next_followup_date")[0].reset();
        setTimeout(function () {
            $("#addMemberCommentSubmit").prop("disabled", false);
        }, 2e3);
    } else {
        $("#addMemberCommentSubmit").prop("disabled", false);
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed");
    }
}

function ajaxAddCommentResponse(e, t) {
    if (t.status == "success") {
        $("#add_commentModal").html(t.html);
        $($(e).attr("target-modal")).modal("show");
    } else {
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed");
    }
}

function ajaxViewCommentResponse(e, t) {
    if (t.status == "success") {
        $("#view_commentModal").html(t.html);
        $($(e).attr("target-modal")).modal("show");
    } else {
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed");
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

function getFilterFormResponse(e, t) {
    hideLoader();
    "success" == t.status
        ? ($("#filterModal").html(t.html), $($(e).attr("target-modal")).modal("show"), $(".single").select2({ placeholder: "Please Select", allowClear: !0 }))
        : ($(e).prop("disabled", !1), showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed"));
}
function responseAddComment(e, t) {
    $(e).prop("disabled", !1),
        $("#addMemberCommentSubmit").text("Save comments"),
        "success" == t.status
            ? (showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", t.msg, "Success"),
                $("#addMemberCommentForm")[0].reset(),
                $("#comment").val(""),
                $("#next_followup_date")[0].reset(),
                setTimeout(function () {
                    $("#addMemberCommentSubmit").prop("disabled", !1);
                }, 2e3))
            : ($("#addMemberCommentSubmit").prop("disabled", !1), showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed"));
}
function ajaxAddCommentResponse(e, t) {
    hideLoader();
    "success" == t.status ? ($("#add_commentModal").html(t.html), $($(e).attr("target-modal")).modal("show")) : showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed");
}
function ajaxViewCommentResponse(e, t) {
    hideLoader();
    "success" == t.status ? ($("#view_commentModal").html(t.html), $($(e).attr("target-modal")).modal("show")) : showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed");
}
function getAjaxPaginationData() {
    showLoader();
    if (1 == $("#isFilterApply").length && 1 == $("#isFilterApply").val()) {
        var e = new FormData($("#filterForm")[0]);
        e.append("isFilterApply", $("#isFilterApply").val());
    } else var e = new FormData();
    let t = $(".tabClick.active").attr("id");
    e.append("activeTab", t),
        $("#recordLimit").length > 0 && "" != $("#recordLimit").val() && 0 != $("#recordLimit").val() ? e.append("limit", $("#recordLimit").val()) : e.append("limit", 10),
        $("#orderByList").length > 0 && "" != $("#orderByList").val() && 0 != $("#orderByList").val() ? e.append("order", $("#orderByList").val()) : e.append("order", "id");
    let a = 1;
    $("#page").length > 0 && "" != $("#page").val() && 0 != $("#page").val() && (a = $("#page").val()),
        "" != $("#searchText").val() && e.append("searchKeyword", $("#searchText").val()),
        $("#memberMatriId").length > 0 && e.append("memberMatriId", $("#memberMatriId").val()),
        e.append("page", a),
        e.append("conditionColumn", $(".tabClick.active").attr("data-conditionColumn")),
        e.append("conditionVal", $(".tabClick.active").attr("data-conditionVal"));
    let i = $("#ajaxRequestUrl").val();
    ajaxRequest("#resultData", e, i, "ajaxPaginationResponse");
}
function ajaxPaginationResponse(e, t) {
    hideLoader();
    "success" == t.status && ($(".all_check").prop("checked", !1), $(e).html(t.html)),
        scrollTop("#myTab"),
        $.each(t.data.tabCount, function (e, t) {
            $("#" + e)
                .find(".countRecord")
                .text(t);
        });
}
function ajaxChangeStatusResponse(e, t) {
    hideLoader();
    $(e).prop('disabled', false);
    "success" == t.status
        ? ($(".all_check").prop("checked", !1),
            $(".checkboxId").prop("checked", !1),
            showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", t.msg, "Success"),
            setTimeout(function () {
                getAjaxPaginationData();
            }, 1500))
        : ($(".all_check").prop("checked", !1), $(".checkboxId").prop("checked", !1), showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed"));
}
function ajaxChangeStatusResponseSendMatches(e, t) {
    "success" == t.status
        ? ($(".all_check").prop("checked", !1),
            $(".checkboxId").prop("checked", !1),
            showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", t.msg, "Success"),
            setTimeout(function () {
                getAjaxPaginationData();
            }, 1500))
        : ($(".all_check").prop("checked", !1), $(".checkboxId").prop("checked", !1), showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed"));
}
function responseAssignMember(e, t) {
    hideLoader();
    $(e).text("Assign Staff"),
        "success" == t.status
            ? ($(".checkboxId").prop("checked", !1),
                showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", t.msg, "Success"),
                setTimeout(function () {
                    $(e).prop("disabled", !1), getAjaxPaginationData();
                }, 1500))
            : ($(e).prop("disabled", !1), showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed"));
}
function responseUnassignMember(e, t) {
    hideLoader();
    $(e).text("Unassign Staff"),
        "success" == t.status
            ? ($(".checkboxId").prop("checked", !1),
                showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", t.msg, "Success"),
                setTimeout(function () {
                    $(e).prop("disabled", !1), getAjaxPaginationData();
                }, 1500))
            : ($(e).prop("disabled", !1), showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed"));
}

function ajaxChangeStatusResponseBothMatch(e, t) {
    hideLoader();
    if (t.status == "success") {
        $(this).prop("disabled", false);
        $(this).checked = false;
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", t.msg, "Success");
        setTimeout((function () {
            location.reload();
        }), 1500)
    } else {
        $(this).prop("disabled", false);
        $(this).checked = false;
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed")
    }
}

$(document).ready(function () {
    getAjaxPaginationData(),
        $(".sendMatch").click(function () {
            let e = new FormData();
            if ($(".checkboxId:checked").length <= 0) return showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select at least one record to process!", "Failed"), !1;
            showLoader();
            let t = [];
            $(".checkboxId:checked").each(function () {
                t.push($(this).val());
            }),
                e.append('type', $(this).attr("data-type")),
                e.append($(this).attr("data-label"), $(this).attr("data-value")),
                e.append("id", t);
            $(this).prop('disabled', true);
            let a = $(this).attr("data-action");
            ajaxRequest($(this), e, a, "ajaxChangeStatusResponse");
        }),

        $("#manuallyMatchSendBtn").click((function () {
            showLoader();
            let q = new FormData;
            $(this).prop("disabled", true);
            let w = $(this).data("action");
            q.append("memberMatriId", $("#memberMatriId").val()),
                q.append("id", $("#otherUserData").val());
            ajaxRequest($(this), q, w, "ajaxChangeStatusResponseBothMatch");
        }));

    $(".single").select2({ placeholder: "Please Select", allowClear: !0 }),
        $("#orderByList").change(function () {
            "" != $(this).val() && getAjaxPaginationData();
        }),
        $("#assignMember").click(function (e) {
            let t = $(this).attr("data-action"),
                a = new FormData();
            if ($(".checkboxId:checked").length <= 0) return showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select at least one record to process!", "Failed"), !1;
            if ("" == $("#subAdminId").val()) return showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select staff to process!", "Failed"), !1;
            showLoader();
            $(this).prop("disabled", !0);
            let i = [];
            $(".checkboxId:checked").each(function () {
                i.push($(this).val());
            }),
                a.append("subAdminId", $("#subAdminId").val()),
                a.append("adminType", $("option:selected", "#subAdminId").attr("data-type")),
                a.append("id", i),
                ajaxRequest($(this), a, t, "responseAssignMember");
        }),
        $("#unassignMember").click(function (e) {
            let t = $(this).attr("data-action"),
                a = new FormData();
            if ($(".checkboxId:checked").length <= 0) return showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select at least one record to process!", "Failed"), !1;
            if ("" == $("#subAdminId").val()) return showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select staff to process!", "Failed"), !1;
            showLoader();
            $(this).prop("disabled", !0);
            let i = [];
            $(".checkboxId:checked").each(function () {
                i.push($(this).val());
            }),
                a.append("subAdminId", $("#subAdminId").val()),
                a.append("adminType", $("option:selected", "#subAdminId").attr("data-type")),
                a.append("id", i),
                ajaxRequest($(this), a, t, "responseUnassignMember");
        }),
        $("#resultData,#memberModalDiv").on("click", ".addComment", function () {
            showLoader();
            $("." + $(this).data("class")).length > 0 && $("." + $(this).data("class")).remove(), $("#" + $(this).data("removeclass")).modal("hide");
            let e = $(this).data("removeclass");
            setTimeout(function () {
                $("." + e).remove();
            }, 1500);
            let t = new FormData();
            if ("" != $(this).data("id")) {
                if ((t.append("id", $(this).data("id")), void 0 != $(this).data("action") && "" != $(this).data("action"))) {
                    let a = $(this).data("action");
                    ajaxRequest($(this), t, a, "ajaxAddCommentResponse");
                } else showNotification("top", "right", "danger", "withicon", "fa fa-times", "Something wend wrong!", "Failed");
            }
        }),
        $("#resultData,#memberModalDiv").on("click", ".viewComment", function () {
            showLoader();
            $("." + $(this).data("class")).length > 0 && $("." + $(this).data("class")).remove(), $("#" + $(this).data("removeclass")).modal("hide");
            let e = $(this).data("removeclass");
            setTimeout(function () {
                $("." + e).remove();
            }, 1500);
            let t = new FormData();
            if ("" != $(this).data("id")) {
                if ((t.append("id", $(this).data("id")), void 0 != $(this).data("action") && "" != $(this).data("action"))) {
                    let a = $(this).data("action");
                    ajaxRequest($(this), t, a, "ajaxViewCommentResponse");
                } else showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Something wend wrong!", "Failed");
            }
        }),
        $("body").on("click", "#addMemberCommentSubmit", function () {
            if (!$("body").find("#addMemberCommentForm").valid()) return !1;
            {
                $(this).text(""), $(this).append('<i class="me-1 bx bx-loader-alt bx-spin text-white"></i>').append("Loading..."), $(this).prop("disabled", !0);
                let e = new FormData($("#addMemberCommentForm")[0]),
                    t = $("#addMemberCommentForm").attr("action");
                ajaxRequest($(this), e, t, "responseAddComment");
            }
        }),
        $("#memberModalDiv").on("click", ".closeModal", function () {
            $("#" + $(this).data("modal")).modal("hide");
            let e = $(this).data("modal");
            setTimeout(function () {
                $("." + e).remove();
            }, 1500);
        }),
        $("body").on("click", ".closeFilterPopup", function () {
            $("#filterModal").modal("hide");
        }),
        $(".getfilterModal").click(function () {
            showLoader();
            let e = $(this).attr("data-action"),
                t = new FormData();
            1 == $("#isFilterApply").length && 1 == $("#isFilterApply").val() ? $("#filterModal").modal("show") : ajaxRequest($(this), t, e, "getFilterFormResponse");
        }),
        $(document).on("submit", "#filterForm", function (e) {
            e.preventDefault(),
                $("#isFilterApply").val(1),
                getAjaxPaginationData(),
                setTimeout(function () {
                    setTimeout(function () {
                        $("#filterModal").modal("hide");
                    }, 100);
                }, 100);
        });
});
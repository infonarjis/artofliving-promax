$(document).ready(function () {
    getAjaxPaginationData();
    $("body").on("click", ".closeFilterPopup", function () {
        $("#filterModal").modal("hide")
    }),
        $(".getfilterModal").click(function () {
            let e = $(this).attr("data-action"),
                t = new FormData;
            1 == $("#isFilterApply").length && 1 == $("#isFilterApply").val() ? $("#filterModal").modal("show") : ajaxRequest($(this), t, e, "getFilterFormResponse")
        }), $(document).on("submit", "#filterForm", function (e) {
            e.preventDefault(), $("#isFilterApply").val(1), getAjaxPaginationData(), setTimeout(function () {
                setTimeout(function () {
                    $("#filterModal").modal("hide")
                }, 100)
            }, 100)
        });
    $("#assignStaffMember").click(function (e) {
        let t = $(this).attr("data-action");
        let a = new FormData();
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
        a.append("subAdminId", $("#staffAdminId").val());
        a.append("adminType", $("option:selected", "#staffAdminId").attr("data-type"));
        a.append("id", i);
        ajaxRequest($(this), a, t, "responseAssignMember");
    });
    $("#unassignStaffMember").click(function (e) {
        let t = $(this).attr("data-action");
        let a = new FormData();
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
        a.append("subAdminId", $("#staffAdminId").val());
        a.append("adminType", $("option:selected", "#staffAdminId").attr("data-type"));
        a.append("id", i);
        ajaxRequest($(this), a, t, "responseUnassignMember");
    });
    $("#assignFranchise").click(function (e) {
        let t = $(this).attr("data-action"),
            a = new FormData();
        if ($(".checkboxId:checked").length <= 0) return showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select at least one record to process!", "Failed"), !1;
        if ("" == $("#franchiseSubAdminId").val()) return showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select franchise to process!", "Failed"), !1;
        $(this).prop("disabled", !0);
        let i = [];
        $(".checkboxId:checked").each(function () {
            i.push($(this).val());
        }),
            a.append("subAdminFranchiseId", $("#franchiseSubAdminId").val()),
            a.append("adminType", $("option:selected", "#franchiseSubAdminId").attr("data-type")),
            a.append("id", i),
            ajaxRequest($(this), a, t, "responseAssignFranchise");
    }),
        $("#unassignFranchise").click(function (e) {
            let t = $(this).attr("data-action"),
                a = new FormData();
            if ($(".checkboxId:checked").length <= 0) return showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select at least one record to process!", "Failed"), !1;
            if ("" == $("#franchiseSubAdminId").val()) return showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select franchise to process!", "Failed"), !1;
            $(this).prop("disabled", !0);
            let i = [];
            $(".checkboxId:checked").each(function () {
                i.push($(this).val());
            }),
                a.append("subAdminFranchiseId", $("#franchiseSubAdminId").val()),
                a.append("adminType", $("option:selected", "#franchiseSubAdminId").attr("data-type")),
                a.append("id", i),
                ajaxRequest($(this), a, t, "responseUnassignFranchise");
        }),
        $("#changeInterest").click(function (e) {
            let t = $(this).attr("data-action");
            let a = new FormData();
            if ($(".checkboxId:checked").length <= 0) {
                showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select at least one record to process!", "Failed");
                return false;
            }
            if ($("#interestValue").val() == "") {
                showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select interest to process!", "Failed");
                return false;
            }
            $(this).prop("disabled", true);
            let i = [];
            $(".checkboxId:checked").each(function () {
                i.push($(this).val());
            });
            console.log($("#interestValue").val());
            a.append("interestValue", $("#interestValue").val());
            a.append("id", i);
            ajaxRequest($(this), a, t, "responseChangeInterest");
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
    $("#memberModalDiv").on("click", ".closeModal", function () {
        $("#" + $(this).data("modal")).modal("hide");
        let e = $(this).data("modal");
        setTimeout(function () {
            $("." + e).remove();
        }, 1500);
    });

    $("#resultData").on("click", ".actionBtnNew", function (e) {
        e.preventDefault();
        let $btn = $(this);

        // Determine target id(s): row-level (data-id) takes priority, else bulk-checked rows
        let ids = [];
        if ($btn.attr("data-id") !== undefined && $btn.attr("data-id") !== "") {
            ids = [$btn.attr("data-id")];
        } else {
            $(".checkboxId:checked").each(function () {
                ids.push($(this).val());
            });
        }

        if (ids.length <= 0) {
            showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Please select at least one record to process!", "Failed");
            return false;
        }

        let column = $btn.attr("data-column");
        let value = $btn.attr("data-value");
        let actionUrl = $("#changeStatusUrl").val();

        if (!actionUrl) {
            showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", "Something went wrong!", "Failed");
            return false;
        }

        // Confirm before destructive actions (isConfirm="1")
        if ($btn.attr("isConfirm") == "1") {
            let confirmMsg = column === "is_deleted"
                ? "Are you sure you want to delete the selected record(s)?"
                : "Are you sure you want to proceed?";
            if (!confirm(confirmMsg)) {
                return false;
            }
        }

        let formData = new FormData();
        formData.append("id", ids.join(","));
        formData.append(column, value);

        showLoader();
        $btn.prop("disabled", true);

        ajaxRequest($btn, formData, actionUrl, "ajaxChangeStatusResponse");
    });
});

function getFilterFormResponse(e, t) {
    "success" == t.status ? (
        $("#filterModal").html(t.html),
        $($(e).attr("target-modal")).modal("show"),
        $(".single").select2({
            placeholder: "Please Select"
        })) : ($(e).prop("disabled", !1),
            showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed"))
}

function responseAddComment(e, t) {
    $(e).prop("disabled", false);
    $("#addMemberCommentSubmit").text("Save comments");
    if (t.status == "success") {
        showNotification("top-0","end-0","bg-success","withicon","fa fa-check",t.msg,"Success");
        // Reset the complete form
        $("#addMemberCommentForm")[0].reset();
        // Explicitly clear comment
        $("#comment").val("");
        // Clear follow-up date
        $("#next_followup_date").val("");
        setTimeout(function () {
            $("#addMemberCommentSubmit").prop("disabled", false);
        }, 2000);
    } else {
        $("#addMemberCommentSubmit").prop("disabled", false);
        showNotification("top-0","end-0","bg-danger","withicon","fa fa-times",t.msg,"Failed");
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
    if (1 == $("#isFilterApply").length && 1 == $("#isFilterApply").val()) {
        var e = new FormData($("#filterForm")[0]);
        e.append("isFilterApply", $("#isFilterApply").val())
    } else var e = new FormData;
    let t = $(".tabClick.active").attr("id");
    e.append("activeTab", t);
    if ($("#recordLimit").length > 0 && $("#recordLimit").val() != "" && $("#recordLimit").val() != 0) {
        e.append("limit", $("#recordLimit").val());
    } else {
        e.append("limit", 10);
    }
    let a = 1;
    if ($("#page").length > 0 && $("#page").val() != "" && $("#page").val() != 0) {
        a = $("#page").val();
    }
    if ($("#searchText").val() != "") {
        e.append("searchKeyword", $("#searchText").val());
    }
    e.append("page", a);
    e.append("conditionColumn", $(".tabClick.active").attr("data-conditionColumn"));
    e.append("conditionVal", $(".tabClick.active").attr("data-conditionVal"));
    let i = $("#ajaxRequestUrl").val();
    ajaxRequest("#resultData", e, i, "ajaxPaginationResponse");
}
function ajaxPaginationResponse(e, t) {
    if (t.status == "success") {
        $(".all_check").prop("checked", false);
        $(e).html(t.html);
    }
    scrollTop("#myTab");
    $.each(t.data.tabCount, function (e, t) {
        $("#" + e)
            .find(".countRecord")
            .text(t);
    });
}
function ajaxChangeStatusResponse(e, t) {
    hideLoader();
    if (t.status == "success") {
        $(".all_check").prop("checked", false);
        $(".checkboxId").prop("checked", false);
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", t.msg, "Success");
        setTimeout(function () {
            getAjaxPaginationData();
        }, 1500);
    } else {
        $(".all_check").prop("checked", false);
        $(".checkboxId").prop("checked", false);
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed");
    }
}
function responseAssignMember(e, t) {
    $(e).text("Assign Staff");
    if (t.status == "success") {
        $(".checkboxId").prop("checked", false);
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", t.msg, "Success");
        setTimeout(function () {
            $(e).prop("disabled", false);
            getAjaxPaginationData();
        }, 1500);
    } else {
        $(e).prop("disabled", false);
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed");
    }
}
function responseUnassignMember(e, t) {
    $(e).text("Unassign Staff");
    if (t.status == "success") {
        $(".checkboxId").prop("checked", false);
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", t.msg, "Success");
        setTimeout(function () {
            $(e).prop("disabled", false);
            getAjaxPaginationData();
        }, 1500);
    } else {
        $(e).prop("disabled", false);
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed");
    }
}
function responseChangeInterest(e, t) {
    $(e).text("Change Interest");
    if (t.status == "success") {
        $(".checkboxId").prop("checked", false);
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", t.msg, "Success");
        setTimeout(function () {
            $(e).prop("disabled", false);
            getAjaxPaginationData();
        }, 1500);
    } else {
        $(e).prop("disabled", false);
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed");
    }
}
function responseAssignFranchise(e, t) {
    $(e).text("Assign Franchise"),
        "success" == t.status
            ? ($(".checkboxId").prop("checked", !1),
                showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", t.msg, "Success"),
                setTimeout(function () {
                    $(e).prop("disabled", !1), getAjaxPaginationData();
                }, 1500))
            : ($(e).prop("disabled", !1), showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed"));
}
function responseUnassignFranchise(e, t) {
    $(e).text("Unassign Franchise"),
        "success" == t.status
            ? ($(".checkboxId").prop("checked", !1),
                showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", t.msg, "Success"),
                setTimeout(function () {
                    $(e).prop("disabled", !1), getAjaxPaginationData();
                }, 1500))
            : ($(e).prop("disabled", !1), showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", t.msg, "Failed"));
}

function toggleFollowupValidation() {
    const status = $("#lead_status").val();

    if (status === "close") {
        // Close → Comment & Followup Date not required
        $("#comment").prop("required", false).val("");
        $("#next_followup_date").prop("required", false).val("");
        $("#next_followup_time").prop("required", false).val("");
    } else {
        // Open → Comment & Followup Date required
        $("#comment").prop("required", true);
        $("#next_followup_date").prop("required", true);

        // Time remains optional
        $("#next_followup_time").prop("required", false);
    }
}

// When Lead Status changes
$("body").on("change", "#lead_status", function () {
    toggleFollowupValidation();
});

// Initial state
toggleFollowupValidation();
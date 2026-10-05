$(document).ready(function () {
    let deleteConfirmText = "Are you remove profile photo ?";
    $(".deleteProfileImage").on("click", function () {
        showLoader();
        let formData = new FormData();
        formData.append($(this).attr("data-label"), $(this).attr("data-value"));
        formData.append("id", $(this).attr("data-id"));
        let url = $("#changeStatusUrl").val();
        if (confirm(deleteConfirmText)) {
            ajaxRequest($(this), formData, url, "ajaxChangeStatusResponse");
        }
    });

    $(".approvedImage").on("click", function () {
        showLoader();
        let formData = new FormData();
        formData.append($(this).attr("data-label"), $(this).val());
        formData.append("id", $(this).attr("data-id"));
        let url = $("#changeStatusUrl").val();
        ajaxRequest($(this), formData, url, "ajaxChangeStatusResponse");
    });

    let actionConfirmText = "Are you sure to proceed ?";

    $(".actionBtnList").on("click", function () {
        showLoader();
        let formData = new FormData();
        formData.append($(this).attr("data-column"), $(this).attr("data-value"));
        formData.append("id", $(this).attr("data-id"));
        let url = $("#changeStatusUrl").val();
        if ($(this).attr("isconfirm") && $(this).attr("isconfirm") == 1) {
            if (confirm(actionConfirmText)) {
                ajaxRequest($(this), formData, url, "ajaxChangeStatusResponse");
            }
        } else {
            ajaxRequest($(this), formData, url, "ajaxChangeStatusResponse");
        }
    });

    $(".addComment").on("click", function () {
        showLoader();
        let removeClass = $(this).data("class");
        let modalId = $(this).data("removeclass");
        if ($("." + removeClass).length > 0) {
            $("." + removeClass).remove();
        }
        $("#" + modalId).modal("hide");
        setTimeout(function () {
            $("." + modalId).remove();
        }, 1500);

        let formData = new FormData();
        if ($(this).data("id") !== "") {
            formData.append("id", $(this).data("id"));
            if ($(this).data("action")) {
                ajaxRequest(
                    $(this),
                    formData,
                    $(this).data("action"),
                    "ajaxAddCommentResponse"
                );
            } else {
                showNotification(
                    "top",
                    "right",
                    "danger",
                    "withicon",
                    "fa fa-times",
                    "Something wend wrong!",
                    "Failed"
                );
            }
        }
    });

    $(".viewComment").on("click", function () {
        showLoader();
        let removeClass = $(this).data("class");
        let modalId = $(this).data("removeclass");
        if ($("." + removeClass).length > 0) {
            $("." + removeClass).remove();
        }
        $("#" + modalId).modal("hide");
        setTimeout(function () {
            $("." + modalId).remove();
        }, 1500);

        let formData = new FormData();
        if ($(this).data("id") !== "") {
            formData.append("id", $(this).data("id"));
            if ($(this).data("action")) {
                ajaxRequest(
                    $(this),
                    formData,
                    $(this).data("action"),
                    "ajaxViewCommentResponse"
                );
            } else {
                showNotification(
                    "top-0",
                    "end-0",
                    "bg-danger",
                    "withicon",
                    "fa fa-times",
                    "Something wend wrong!",
                    "Failed"
                );
            }
        }
    });

    $("body").on("click", "#addMemberCommentSubmit", function () {
        if ($("#addMemberCommentForm").valid()) {
            $(this)
                .html('<i class="me-1 bx bx-loader-alt bx-spin text-white"></i> Loading...')
                .prop("disabled", true);
            let formData = new FormData($("#addMemberCommentForm")[0]);
            let url = $("#addMemberCommentForm").attr("action");
            ajaxRequest($(this), formData, url, "responseAddComment");
        } else {
            return false;
        }
    });

    $("#memberModalDiv").on("click", ".closeModal", function () {
        let modalId = $(this).data("modal");
        $("#" + modalId).modal("hide");
        setTimeout(function () {
            $("." + modalId).remove();
        }, 1500);
    });
});

function ajaxChangeStatusResponse(element, response) {
    hideLoader();
    $(".all_check, .checkboxId").prop("checked", false);
    if (response.status === "success") {
        // let redirectUrl = $("#redirectUrl").val();
        // $("#" + $(element).attr("data-key")).remove();
        // if ($(element).attr("data-label") === "photo1") {
        //     $("#viewProfile").attr("src", $(element).attr("data-img"));
        // }
        showNotification(
            "top-0",
            "end-0",
            "bg-success",
            "withicon",
            "fa fa-check",
            response.msg,
            "Success"
        );

        // setTimeout(function () {
        //     if ($(element).attr("data-column") === "is_deleted") {
        //         window.location.href = redirectUrl;
        //     }
        // }, 500);
        setTimeout(function () {
            location.reload()
        }, 1500);

    } else {
        showNotification(
            "top-0",
            "end-0",
            "bg-danger",
            "withicon",
            "fa fa-times",
            response.msg,
            "Failed"
        );
    }
}

function responseAddComment(element, response) {
    $(element).prop("disabled", false);
    $("#addMemberCommentSubmit").text("Save comments");
    if (response.status === "success") {
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", response.msg, "Success");
        $("#addMemberCommentForm")[0].reset();
        $("#comment").val("");
        $("#next_followup_date")[0].reset();
        setTimeout(function () {
            $("#addMemberCommentSubmit").prop("disabled", false);
        }, 2000);
    } else {
        $("#addMemberCommentSubmit").prop("disabled", false);
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", response.msg, "Failed");
    }
}

function ajaxAddCommentResponse(element, response) {
    hideLoader();
    if (response.status === "success") {
        $("#add_commentModal").html(response.html);
        $($(element).attr("target-modal")).modal("show");
    } else {
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", response.msg, "Failed");
    }
}

function ajaxViewCommentResponse(element, response) {
    hideLoader();
    if (response.status === "success") {
        $("#view_commentModal").html(response.html);
        $($(element).attr("target-modal")).modal("show");
    } else {
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", response.msg, "Failed");
    }
}
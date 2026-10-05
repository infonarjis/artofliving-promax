$(document).ready(function () {
    getAjaxPaginationData();

    // Assign Staff
    $("#assignStaffMember").on("click", function () {
        let $button = $(this);
        let action = $button.attr("data-action");
        let formData = new FormData();
        if ($(".checkboxId:checked").length === 0) {
            showNotification("top-0","end-0","bg-danger","withicon","fa fa-times","Please select at least one record to process!","Failed");
            return false;
        }

        if ($("#staffAdminId").val() === "") {
            showNotification("top-0","end-0","bg-danger","withicon","fa fa-times","Please select staff to process!","Failed");
            return false;
        }

        $button.prop("disabled", true);

        let ids = [];

        $(".checkboxId:checked").each(function () {
            ids.push($(this).val());
        });

        formData.append("subAdminId", $("#staffAdminId").val());
        formData.append(
            "adminType",
            $("#staffAdminId option:selected").data("type")
        );
        formData.append("id", ids);

        ajaxRequest($button, formData, action, "responseAssignMember");
    });

    // Add Comment Modal
    $("#resultData,#memberModalDiv").on("click", ".addComment", function () {
        showLoader();
        let $this = $(this);
        if ($("." + $this.data("class")).length) {
            $("." + $this.data("class")).remove();
        }
        $("#" + $this.data("removeclass")).modal("hide");
        let removeClass = $this.data("removeclass");
        setTimeout(function () {
            $("." + removeClass).remove();
        }, 1500);
        if (!$this.data("id")) {
            return;
        }

        let formData = new FormData();
        formData.append("id", $this.data("id"));
        let action = $this.data("action");
        if (!action) {
            showNotification("top-0","end-0","bg-danger","withicon","fa fa-times","Something went wrong!","Failed");
            return;
        }
        ajaxRequest($this, formData, action, "ajaxAddCommentResponse");
    });

    // View Comment Modal
    $("#resultData,#memberModalDiv").on("click", ".viewComment", function () {
        showLoader();
        let $this = $(this);
        if ($("." + $this.data("class")).length) {
            $("." + $this.data("class")).remove();
        }
        $("#" + $this.data("removeclass")).modal("hide");
        let removeClass = $this.data("removeclass");
        setTimeout(function () {
            $("." + removeClass).remove();
        }, 1500);
        if (!$this.data("id")) {
            return;
        }
        let formData = new FormData();
        formData.append("id", $this.data("id"));
        let action = $this.data("action");
        if (!action) {
            showNotification("top-0","end-0","bg-danger","withicon","fa fa-times","Something went wrong!","Failed");
            return;
        }
        ajaxRequest($this, formData, action, "ajaxViewCommentResponse");
    });

    // Save Comment
    $("body").on("click", "#addMemberCommentSubmit", function () {
        let $button = $(this);
        if (!$("#addMemberCommentForm").valid()) {
            return false;
        }
        $button.html('<i class="me-1 bx bx-loader-alt bx-spin text-white"></i> Loading...').prop("disabled", true);

        let formData = new FormData(
            document.getElementById("addMemberCommentForm")
        );
        let action = $("#addMemberCommentForm").attr("action");
        ajaxRequest($button, formData, action, "responseAddComment");
    });

    // Refresh listing after modal closes
    $(document).on('hidden.bs.modal', '#add_commentModal', function () {
        // Reset form
        $('#addMemberCommentForm')[0].reset();
        // Reload AJAX data
        getAjaxPaginationData();
    });
    $(document).on('hide.bs.modal', '#add_commentModal', function () {
        console.log('Modal is closing');
    });
});

function getAjaxPaginationData() {
    let formData = new FormData();
    formData.append("activeTab", $(".tabClick.active").attr("id"));
    formData.append("limit", $("#recordLimit").length && $("#recordLimit").val()? $("#recordLimit").val() : 10);
    formData.append("page", $("#page").length && $("#page").val() ? $("#page").val() : 1);
    if ($("#searchText").val() !== "") {
        formData.append("searchKeyword", $("#searchText").val());
    }
    formData.append("conditionColumn", $(".tabClick.active").data("conditioncolumn"));
    formData.append("conditionVal", $(".tabClick.active").data("conditionval"));
    ajaxRequest("#resultData",formData,$("#ajaxRequestUrl").val(),"ajaxPaginationResponse");
}

function ajaxPaginationResponse(element, response) {
    if (response.status === "success") {
        $(".all_check").prop("checked", false);
        $(element).html(response.html);
        scrollTop("#myTab");
        $.each(response.data.tabCount, function (key, value) {
            $("#" + key).find(".countRecord").text(value);
        });
    }
}

function responseAssignMember(button, response) {
    $(button).text("Assign Staff");
    if (response.status === "success") {
        $(".checkboxId").prop("checked", false);
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", response.msg, "Success");
        setTimeout(function () {
            $(button).prop("disabled", false);
            getAjaxPaginationData();
        }, 1500);
    } else {
        $(button).prop("disabled", false);
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", response.msg,"Failed");
    }
}

function responseAddComment(button, response) {
    $(button).prop("disabled", false);
    $("#addMemberCommentSubmit").text("Save Comments").prop("disabled", false);
    if (response.status === "success") {
        showNotification("top-0","end-0","bg-success","withicon","fa fa-check",response.msg,"Success");
        // Reset Form
        document.getElementById("addMemberCommentForm").reset();
    } else {
        showNotification("top-0","end-0","bg-danger","withicon","fa fa-times",response.msg,"Failed");
    }
}

function ajaxAddCommentResponse(element, response) {
    hideLoader();
    if (response.status === "success") {
        $("#add_commentModal").html(response.html);
        $($(element).attr("target-modal")).modal("show");
    } else {
        showNotification("top-0","end-0","bg-danger","withicon","fa fa-times",response.msg,"Failed");
    }
}

function ajaxViewCommentResponse(element, response) {
    hideLoader();
    if (response.status === "success") {
        $("#view_commentModal").html(response.html);
        $($(element).attr("target-modal")).modal("show");
    } else {
        showNotification("top-0","end-0","bg-danger","withicon","fa fa-times",response.msg,"Failed");
    }
}

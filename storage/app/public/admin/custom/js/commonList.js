$(document).ready(function () {

    // Initial pagination load
    getAjaxPaginationData();

    // Close filter modal
    $("body").on("click", ".closeFilterPopup", function () {
        $("#filterModal").modal("hide");
    });

    // Open filter modal
    $("body").on("click", ".getfilterModal", function () {
        const $this = $(this);
        const actionUrl = $this.attr("data-action");
        const formData = new FormData();

        // If filter is already applied, simply open the modal
        if ($("#isFilterApply").length === 1 && $("#isFilterApply").val() === "1") {
            $("#filterModal").modal("show");
        } else {
            // Otherwise load filter form using AJAX
            ajaxRequest($this, formData, actionUrl, "getFilterFormResponse");
        }
    });

    // Submit filter form
    $(document).on("submit", "#filterForm", function (event) {
        event.preventDefault();
        // Mark filter as applied
        $("#isFilterApply").val("1");
        // Reset page to first page when applying a new filter
        if ($("#page").length > 0) {
            $("#page").val(1);
        }
        // Reload data
        getAjaxPaginationData();
        // Close modal
        setTimeout(function () {
            $("#filterModal").modal("hide");
        }, 100);
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

/**
 * Get pagination data
 */
function getAjaxPaginationData() {
    let formData;
    // Check whether filter is applied
    if ($("#isFilterApply").length === 1 && $("#isFilterApply").val() === "1") {
        // Make sure filter form exists
        if ($("#filterForm").length > 0) {
            formData = new FormData($("#filterForm")[0]);
        } else {
            formData = new FormData();
        }

        formData.set("isFilterApply", $("#isFilterApply").val());
    } else {
        formData = new FormData();
    }

    // Active tab
    const $activeTab = $(".tabClick.active");
    const activeTab = $activeTab.attr("id") || "";
    const conditionColumn = $activeTab.attr("data-conditionColumn") || "";
    const conditionVal = $activeTab.attr("data-conditionVal") || "";

    // Record limit
    const limit = parseInt($("#recordLimit").val(),10) || 10;

    // Current page
    const page = parseInt($("#page").val(), 10) || 1;

    // Search keyword
    const searchKeyword = $("#searchText").val();
    // Member Matri ID
    const memberMatriId = $("#memberMatriId").length > 0 ? $("#memberMatriId").val() : "";

    // Append / update request parameters
    formData.set("activeTab", activeTab);
    formData.set("conditionColumn", conditionColumn);
    formData.set("conditionVal", conditionVal);
    formData.set("limit", limit);
    formData.set("page", page);

    // Search keyword
    if (searchKeyword && searchKeyword.trim() !== "") {
        formData.set("searchKeyword", searchKeyword.trim());
    } else {
        formData.delete("searchKeyword");
    }

    // Member Matri ID
    if (memberMatriId) {
        formData.set("memberMatriId", memberMatriId);
    }

    // AJAX URL
    const url = $("#ajaxRequestUrl").val();

    // Send AJAX request
    ajaxRequest("#resultData", formData, url, "ajaxPaginationResponse");
}

/**
 * Pagination AJAX response
 */
function ajaxPaginationResponse(element, response) {
    if (response.status === "success") {
        // Uncheck all checkboxes
        $(".all_check").prop("checked", false);
        $(".checkboxId").prop("checked", false);

        // Update result data
        $(element).html(response.html);

        // Update tab counts
        if (response.data && response.data.tabCount) {
            $.each(
                response.data.tabCount,
                function (key, value) {
                    $("#" + key).find(".countRecord").text(value);
                }
            );
        }
    } else {
        // Error notification
        showNotification("top-0","end-0","bg-danger","withicon","fa fa-times",response.msg || "Something went wrong.","Failed");
    }
    // Scroll to tabs
    scrollTop("#myTab");
}

/**
 * Change status AJAX response
 */
function ajaxChangeStatusResponse(element, response) {
    hideLoader()
    // Reset checkboxes
    $(".all_check").prop("checked", false);
    $(".checkboxId").prop("checked", false);
    if (response.status === "success") {
        showNotification("top-0","end-0","bg-success","withicon","fa fa-check",response.msg,"Success");
        // Reload pagination data
        setTimeout(function () {
            getAjaxPaginationData();
        }, 1500);
    } else {
        showNotification("top-0","end-0","bg-danger","withicon","fa fa-times",response.msg || "Something went wrong.","Failed");
    }
}


/**
 * Get filter form AJAX response
 */
function getFilterFormResponse(element, response) {
    if (response.status === "success") {
        // Load filter form
        $("#filterModal").html(response.html);
        // Open modal
        const targetModal = $(element).attr("target-modal");
        if (targetModal) {
            $(targetModal).modal("show");
        } else {
            $("#filterModal").modal("show");
        }
        // Initialize Select2
        $(".single").select2({
            placeholder: "Please Select",
            allowClear: true
        });
    } else {
        // Enable button again
        $(element).prop("disabled", false);
        // Show error
        showNotification("top-0","end-0","bg-danger","withicon","fa fa-times",response.msg || "Something went wrong.","Failed");
    }
}
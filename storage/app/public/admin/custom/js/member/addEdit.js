$(document).ready(function () {
    $(".single").select2({
        allowClear: !0
    });
    setTimeout(function () {
        $('.disbaledValue').trigger('change');
    }, 100);
    $('.disbaledValue').on('select2:select', function (e) {
        let selectedValues = $(this).val() || [];
        let newSelection = e.params.data.id;
        if (newSelection === 'Does Not Matter' && selectedValues.length > 1) {
            // Clear others and keep only 'Does Not Matter'
            $(this).val(['Does Not Matter']).trigger('change.select2');
        } else if (newSelection !== 'Does Not Matter' && selectedValues.includes('Does Not Matter')) {
            // Remove 'Does Not Matter' and keep newly selected
            let updatedValues = selectedValues.filter(val => val !== 'Does Not Matter');
            $(this).val(updatedValues).trigger('change.select2');
        }
    });


    $(".formSubmitBtn").on("click", function (e) {
        e.preventDefault();
        let btn = $(this);
        // Prevent multiple clicks
        if (btn.data("processing")) {
            return false;
        }
        let form = $("#" + btn.data("formid"));
        // Validate form
        if (!form.valid()) {
            let firstInvalidField = form.find(
                "input.error, select.error, textarea.error"
            ).first();
            if (firstInvalidField.length) {
                $("html, body").animate(
                    {
                        scrollTop: firstInvalidField.offset().top - 120,
                    },
                    300
                );
                firstInvalidField.focus();
            }
            return false;
        }

        // Store original button text
        btn.data("processing", true);
        btn.data("original-text", btn.html());
        // Loading state
        btn.addClass("loading").prop("disabled", true).html('<i class="bx bx-loader bx-spin"></i> Loading...');
        let formData = new FormData(form[0]);
        formData.append("mode", $("#mode").val());
        formData.append("id", $("#id").val());

        ajaxRequest(
            btn,
            formData,
            $("#formUrl").val(),
            "responseAddEditMember"
        );
    });


    // Marital Status
    setTimeout(function () {
        $("#marital_status").trigger("change");
    }, 100);

    $('.total_children').hide();

    $("#marital_status").change(function () {
        let value = $("#marital_status").val();

        if (value == "2" || value == "3" || value == "4") {
            $('.total_children').show();
            $("#total_children").prop("required", true);
        } else {
            $('.status_children').hide();
            $('.total_children').hide();

            $("#total_children")
                .val("")
                .prop("required", false);

            $("#status_children")
                .val("")
                .prop("required", false);
        }
    });

    // Total Children
    setTimeout(function () {
        $("#total_children").trigger("change");
    }, 100);

    $("#total_children").change(function () {
        let value = $("#total_children").val();

        if (value == "2" || value == "3" || value == "4" || value == "5") {
            $('.status_children').show();
            $("#status_children").prop("required", true);
        } else {
            $('.status_children').hide();

            $("#status_children")
                .val("")
                .prop("required", false);
        }
    });

    $("#generateAboutMeBtn").click(function () {

        let $btn = $(this);
        let $textarea = $('#about_me_description');

        // Prevent multiple clicks
        if ($btn.hasClass('loading')) {
            return false;
        }

        // Button loading state
        $btn.addClass('loading')
            .prop('disabled', true)
            .html('<i class="bx bx-loader bx-spin"></i> Generating...');

        let formData = new FormData();
        formData.append("id", $("#id").val());
        let url = $("#base_url").val() + "/member/generate-about-me";
        ajaxRequest($(this), formData, url, "responseGenerateAboutMe");
    });

    // Marital Status:
    setTimeout(function () {
        $("#residence_type").trigger("change");
    }, 100),
        $('.residence_type_other').hide();
    $("#residence_type").change(function (e) {
        let residenceValue = $("#residence_type :selected").val();
        if (residenceValue == "3") {
            $('.residence_type_other').show();
        } else {
            $('.residence_type_other').hide();
            $("#residence_type_other").val("");
        }
    });
});

function responseAddEditMember(btn, response) {
    // Restore button state
    btn.removeClass("loading").prop("disabled", false).data("processing", false).html(btn.data("original-text") || "Submit");
    // Error Response
    if (response.status !== "success") {
        showNotification("top-0","end-0","bg-danger","withicon","fa fa-times",response.message,"Failed");
        return;
    }

    // Success Notification
    showNotification("top-0","end-0","bg-success","withicon","fa fa-check",response.message,"Success");

    setTimeout(function () {
        let currentTab = $(".nav-link.active");
        let nextButton = currentTab.closest("li").next("li").find("button");

        // Last tab completed
        if (!nextButton.length) {
            window.location.href = $("#successUrl").val();
            return;
        }

        let currentTarget = currentTab.data("bs-target");
        let nextTarget = nextButton.data("bs-target");

        // Switch tabs
        currentTab.removeClass("active");
        nextButton.addClass("active");

        $(currentTarget).removeClass("active show");
        $(nextTarget).addClass("active show");

        // Change Add -> Edit
        if (response.data && response.data.mode === "add") {
            $("#mode").val("edit");
            $("#id").val(response.data.member_id);
        }

        // Show/Hide sections
        if (response.data && response.data.mode !== "edit") {
            $(".memberAlertMsg").addClass("d-none");
            $(".formAdd").removeClass("d-none");
        }

        // Scroll to top of tabs
        $("html, body").animate({
            scrollTop: $(".nav-tabs").offset().top - 100
        }, 400);

    }, 1000);
}

function responseGenerateAboutMe(_this, response) {
    // Reset button
    $(_this).removeClass('loading').prop('disabled', false).html('Generate with Ai');
    if (response.status == 'success') {
        $('#about_me_description').val(response.data).hide().fadeIn(400);
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", response.message, "Success")
    } else {
        showNotification("top-0", "end-0", "bg-danger", "withicon", "fa fa-times", response.message, "Failed")
    }
}


function updateMarriedDropdown(mainSelector, marriedSelector, label) {
    $(mainSelector).on("change", function () {
        const count = $(this).find("option:selected").text().trim();
        const words = ["Zero", "One", "Two", "Three", "Four"];
        const $dropdown = $(marriedSelector);
        // Store current selected value
        const selectedValue = $dropdown.val();
        $dropdown.empty();
        if (count === "" || count === "Select") {
            $dropdown.prop("disabled", true);
            $dropdown.append(
                `<option value="">Select No Of Married ${label}</option>`
            );
            return;
        }
        const isFourPlus = count === "4 +";
        const max = isFourPlus ? 4 : parseInt(count) || 0;
        $dropdown.prop("disabled", false);
        $dropdown.append(
            `<option value="">Select No Of Married ${label}</option>`
        );
        $dropdown.append(
            `<option value="1">No married ${label.toLowerCase()}</option>`
        );
        for (let i = 1; i <= max; i++) {
            const word = words[i];
            const suffix =
                i === 1 ? label.slice(0, -1).toLowerCase() : label.toLowerCase();
            const text = `${word} married ${suffix}`;
            $dropdown.append(
                `<option value="${i + 1}">${text}</option>`
            );
        }
        if (isFourPlus) {
            $dropdown.append(
                `<option value="6">Above four married ${label.toLowerCase()}</option>`
            );
        }
        // Restore selected value
        if (selectedValue && $dropdown.find(`option[value="${selectedValue}"]`).length) {
            $dropdown.val(selectedValue);
        } else {
            $dropdown.val('');
        }
    });

    // Trigger on page load
    $(mainSelector).trigger("change");
}
$(document).ready(function () {
    function handleDoesNotMatter(selector) {
        $(selector).each(function () {
            let values = $(this).val() || [];
            if (values.includes('Does Not Matter') && values.length > 1) {
                $(this).val(['Does Not Matter']).trigger('change.select2');
            }
        });

        $(selector).on('select2:select', function (e) {
            let selectedValues = $(this).val() || [];
            let newSelection = e.params.data.id;
            let updated = newSelection === 'Does Not Matter'
                ? ['Does Not Matter']
                : selectedValues.filter(v => v !== 'Does Not Matter');

            $(this).val(updated).trigger('change.select2');
            $(this).data('selected', updated); // keep the snapshot in sync
        });
    }
    handleDoesNotMatter('.does-not-matter');
});

function dependentDropdown(parent, child, type, placeholder) {
    $(document).on("change", parent, function () {
        let parent_id = $(this).val();
        if (!parent_id || parent_id.includes('Does Not Matter')) {
            // no real parent selected — child can only be "Does Not Matter"
            $(child).html('<option value="Does Not Matter" selected>' + lbl_does_not_matter + '</option>')
                .trigger('change');
            $(child).data('selected', ['Does Not Matter']);
            return;
        }
        let selectedValue = $(child).data("selected");
        let isMultiple = $(child).prop('multiple');
        $(child).html('<option value="">' + lbl_loading + '</option>');
        let actionUrl = $('#base_url').val() + '/get-dependency-dropdown-data';
        $.ajax({
            url: actionUrl,
            type: "POST",
            data: { id: parent_id, type: type, _token: $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                let option = `<option value="">${placeholder}</option>`;
                $.each(res, function (id, name) {
                    option += `<option value="${id}">${name}</option>`;
                });
                $(child).html(option);

                if (selectedValue) {
                    if (isMultiple) {
                        if (!Array.isArray(selectedValue)) selectedValue = [selectedValue];
                        // sanitize stale/corrupted snapshot
                        if (selectedValue.includes('Does Not Matter')) {
                            selectedValue = ['Does Not Matter'];
                        }
                        $(child).val(selectedValue).trigger('change');
                        $(child).data('selected', selectedValue); // keep snapshot clean going forward
                    } else {
                        $(child).val(selectedValue).trigger('change');
                    }
                }
            }
        });
    });
}

function showToastMessage(type, message, autoHide = true, delay = 5000) {
    // Create toast element
    const toast = $(`
        <div class="m-toast m-${type}">
            <div class="m-icon"><iconify-icon icon="${getIconForType(type)}"></iconify-icon></div>
            <div class="m-content">
                <span class="m-message">${message}</span>
            </div>
            <!-- <button class="m-close cursor-pointer"><iconify-icon icon="carbon:close-filled"></iconify-icon></button> -->
        </div>
    `);

    // Add to container
    $('#toast-container').append(toast);

    // Show the toast with animation
    setTimeout(() => {
        toast.addClass('show');
    }, 10);

    // Handle close button click
    toast.find('.m-close').on('click', function () {
        hideToast(toast);
    });

    // Auto hide after delay
    if (autoHide) {
        setTimeout(() => {
            hideToast(toast);
        }, delay);
    }
    return toast;
}

// Hide toast function
function hideToast(toast) {
    toast.addClass('hide');

    // Remove from DOM after animation completes
    setTimeout(() => {
        toast.remove();
    }, 300);
}

// Get icon based on toast type
function getIconForType(type) {
    const icons = {
        'success': 'lets-icons:check-fill',
        'info': 'heroicons:information-circle',
        'warning': 'typcn:warning-outline',
        'error': 'ix:error'
    };
    return icons[type] || 'info-circle';
}

function showAlertMessage(type = 'error', title = '', message = '', autohide = true) {
    const alertBox = $('#response-alert');
    let icon = 'mdi:shield-alert';
    if (type === 'success') {
        icon = 'mdi:check-circle';
    } else if (type === 'warning') {
        icon = 'mdi:alert-circle';
    }
    // Remove old classes
    alertBox.removeClass('success error warning d-none');

    // Add new type class
    alertBox.addClass(type);

    // Set content
    $('#response-alert-title').text(title);
    $('#response-alert-message').text(message);
    $('#response-alert-icon').attr('icon', icon);

    // Show
    alertBox.removeClass('d-none').addClass('show');

    // Auto hide (optional)
    if (autohide) {
        setTimeout(function () {
            alertBox.addClass('d-none').removeClass('show');
        }, 3000);
    }
}

// Add & Remove Shortlist :
$(document).on('click', '.add-shortlist', function () {
    let btn = $(this);
    let receiverId = btn.data('id');
    let icon = btn.find('iconify-icon');
    let text = btn.find('span');

    $.ajax({
        url: $('#base_url').val() + '/shortlist/add-remove',
        type: "POST",
        data: {
            _token: csrfToken,
            receiver_member_id: receiverId
        },
        success: function (response) {
            if (response.message === "Unauthenticated.") {
                $('#loginModal').modal('show');
                return;
            }
            if (response.status) {
                // Toggle icon without reload
                let currentIcon = icon.attr('icon');
                if (currentIcon === 'flowbite:star-outline') {
                    icon.attr('icon', 'flowbite:star-solid');
                    text.text('Shortlisted');
                } else {
                    icon.attr('icon', 'flowbite:star-outline');
                    text.text('Shortlist');
                }
                showToastMessage('success', response.message);
            } else {
                showToastMessage('error', response.message);
            }
        },
        error: function (xhr) {
            // Proper way — Laravel usually sends 401 status
            if (xhr.status === 401) {
                $('#loginModal').modal('show');
                return;
            }
            showToastMessage('error', 'Something went wrong. Please try again.');
        }
    });
});

// Add & Remove Block :
$(document).on('click', '.block-action', function (e) {
    e.preventDefault();
    let btn = $(this);
    // Prevent double click / duplicate AJAX request
    if (btn.data('processing') === true) {
        return;
    }
    btn.data('processing', true);
    btn.prop('disabled', true);
    let id = btn.data('id');
    let icon = btn.find('.block-icon');
    let text = btn.find('.block-text');
    $.ajax({
        url: $('#base_url').val() + '/blocklist/action',
        type: "POST",
        data: {
            _token: csrfToken,
            receiver_member_id: id
        },
        success: function (response) {
            if (response.message === "Unauthenticated.") {
                $('#loginModal').modal('show');
                return;
            }
            if (!response.status) {
                showToastMessage('error', response.message);
                return;
            }
            // Toggle UI based on response
            if (response.type === 'blocked') {
                icon.attr('icon', 'bx:shield-x');
                text.text(lbl_unblock);
                btn.data('blocked', 1);
            } else {
                icon.attr('icon', 'bx:shield');
                text.text(lbl_block);
                btn.data('blocked', 0);
            }
            showToastMessage('success', response.message);
        },
        error: function (xhr) {
            if (xhr.status === 401) {
                $('#loginModal').modal('show');
                return;
            }
            showToastMessage('error', 'Something went wrong. Please try again.');
        },
        complete: function () {
            // Allow clicking again only after AJAX is completed
            btn.data('processing', false);
            btn.prop('disabled', false);
        }
    });
});

$(document).ready(function () {
    const baseUrl = $('#base_url').val();

    let notifMarkedOnOpen = false;
    // Bell clicked: keep unread highlighting visible, but mark all read silently
    $('#notification-toggle-btn').on('click', function () {
        // Second click (closing): now clear the highlight styling
        if (notifMarkedOnOpen) {
            clearUnreadHighlight();
            notifMarkedOnOpen = false;
            return;
        }

        // Badge exists only when there are unread notifications
        if (!$('#nav-notif-badge').length) return;

        $.ajax({
            url: baseUrl + '/notification/mark-all-read',
            type: 'POST',
            data: { _token: csrfToken },
            success: function (res) {
                if (!res.status) return;

                notifMarkedOnOpen = true;

                // Server state is read now; stop per-item click from re-sending requests
                $('.notif-card-link').data('is-read', 1);

                // Clear badge/pill/button immediately, but leave .unread cards and dots
                // in the list so the user can still see what was new
                $('#nav-notif-badge').remove();
                $('#notif-header-pill').addClass('d-none');
                $('#btn-mark-all-read').attr('disabled', true);
            }
        });
    });

    function clearUnreadHighlight() {
        $('.notif-card').removeClass('unread');
        $('.notif-unread-dot').remove();
    }

    // Also clear highlight when the dropdown is closed by clicking outside
    $(document).on('click', function (e) {
        if (notifMarkedOnOpen && !$(e.target).closest('#nav-notifications-wrapper').length) {
            clearUnreadHighlight();
            notifMarkedOnOpen = false;
        }
    });
});

// Read More & Read Less :
$(document).on('click', '.read-toggle', function () {
    let box = $(this).closest('.read-more-box');
    box.toggleClass('expanded');
    if (box.hasClass('expanded')) {
        box.find('.short-text').addClass('d-none');
        box.find('.full-text').removeClass('d-none');
        $(this).text(lbl_read_less);
    } else {
        box.find('.short-text').removeClass('d-none');
        box.find('.full-text').addClass('d-none');
        $(this).text(lbl_read_more);
    }
});

// Common Ajax Request : Date : 31-07-2023
function ajaxRequest(element, formData, requestUrl, responseFunction = '') {
    formData.append('isPost', 1);
    formData.append('_token', $("input[name=_token]").val());
    $.ajax({
        url: requestUrl,
        type: 'POST',
        processData: false,
        contentType: false,
        data: formData,

        success: function (data) {
            // var responseData = JSON.parse(data);
            var responseData = data;
            if (responseData.redirectUrl != '' && responseData.redirectUrl != undefined) {
                window.location.href = responseData.redirectUrl;
            }

            // Call Back Function :
            if (responseFunction != '') {
                window[responseFunction](element, responseData);
            }

            setTimeout(function () {
                $("#overlay").fadeOut(300);
            }, 500);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(element).prop('disabled', false);
            if (jqXHR.status == 422) {
            } else {
            }
        }
    });
}

function showPageLoader() {
    const loader = document.getElementById("page-loader");
    if (loader) {
        loader.classList.remove("loader-hidden");
    }
}

function hidePageLoader() {
    const loader = document.getElementById("page-loader");
    if (loader) {
        loader.classList.add("loader-hidden");
    }
}


$(document).ready(function () {
    $(document).on('input', '.limit-char', function () {
        const target = $(this).data('target');
        $('#' + target).text($(this).val().length);
    });

    // Initialize on page load
    $('.limit-char').each(function () {
        const target = $(this).data('target');
        $('#' + target).text($(this).val().length);
    });
});

function openTimePicker(id) {
    const input = document.getElementById(id);
    try {
        input.showPicker();
    } catch (e) {
        // Fallback for browsers that don't support showPicker()
        input.focus();
        input.click();
    }
}

// For Loader For Range Slider & Select 2 Js Before Load
/* =====================================================================
   plugin-init.js
   Load as the LAST script in your main layout, after jQuery, Select2,
   ion.rangeSlider and your slider-bootstrap file.
   - Initialises every Select2 + range slider on the page
   - Re-initialises automatically for modals, tabs and AJAX-loaded HTML
   - Falls back to raw controls if a plugin never loads
   ===================================================================== */
(function ($) {
    'use strict';
    if (!$) return;

    var SELECT_SEL = 'select.js-example-basic-multiple, select.select2';

    function initSelect2(root) {
        if (!$.fn.select2) return;
        $(root).find(SELECT_SEL).each(function () {
            var $el = $(this);
            if ($el.hasClass('select2-hidden-accessible')) return;

            var $modal = $el.closest('.modal');
            $el.select2({
                width: '100%',
                placeholder: $el.data('placeholder') || '',
                closeOnSelect: !$el.prop('multiple'),
                dropdownParent: $modal.length ? $modal : $(document.body)
            });
        });
    }

    function initSliders() {
        if (typeof window.initAllSliders === 'function' &&
            $.fn.ionRangeSlider) {
            window.initAllSliders($);   // already skips initialised sliders
        }
    }

    function initAll(root) {
        initSelect2(root || document);
        initSliders();
    }

    // Public: call after you inject HTML via AJAX -> window.initPlugins('#container')
    window.initPlugins = initAll;

    // 1) On DOM ready
    $(function () {
        initAll(document);
    });

    // 2) Tabs and modals: widths are wrong if initialised while hidden
    $(document).on('shown.bs.tab shown.bs.modal shown.bs.collapse', function () {
        initAll(document);
    });

    // 3) Any HTML added later (AJAX, partials, dynamic forms)
    var timer = null;
    if (window.MutationObserver) {
        new MutationObserver(function (mutations) {
            for (var i = 0; i < mutations.length; i++) {
                if (mutations[i].addedNodes.length) {
                    clearTimeout(timer);
                    timer = setTimeout(function () { initAll(document); }, 50);
                    break;
                }
            }
        }).observe(document.body, { childList: true, subtree: true });
    }

    // 4) Fallback: never leave skeletons forever if a plugin failed to load
    setTimeout(function () {
        var selectMissing = !$.fn.select2 && $(SELECT_SEL).length;
        var sliderMissing = !$.fn.ionRangeSlider && $('input[class*="js-range-slider"]').length;
        if (selectMissing || sliderMissing) {
            document.documentElement.classList.add('plugins-failed');
            console.error('[plugin-init] A plugin failed to load; showing raw controls.');
        }
    }, 4000);

})(window.jQuery);
// For Loader For Range Slider & Select 2 Js Before Load

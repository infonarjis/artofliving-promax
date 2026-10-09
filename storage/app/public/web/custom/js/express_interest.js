$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    // -----------------------------------------------------------------
    // URL helper: always prefixes the app base URL (works in a subfolder
    // like /promax as well as on a domain root)
    // -----------------------------------------------------------------
    function appUrl(path) {
        return baseUrl + '/' + String(path).replace(/^\/+/, '');
    }

    // -----------------------------------------------------------------
    // Shared low-level POST helper
    // -----------------------------------------------------------------
    function pmiPostAction(url, callbacks) {
        $.post(url)
            .done(function(res) {
                if (!res || !res.status) {
                    showToastMessage('error', (res && res.message) || 'Something went wrong.');
                    callbacks.onFail();
                    return;
                }
                callbacks.onSuccess(res);
            })
            .fail(function(xhr) {
                callbacks.onFail();

                if (xhr.status === 419) {
                    showToastMessage('error',
                        'Your session has expired. Please refresh the page and try again.');
                    return;
                }

                showToastMessage('error', 'Something went wrong. Please try again.');
            });
    }

    // -----------------------------------------------------------------
    // Accept / Reject an incoming interest — Layout 1 (pill / wrap)
    // -----------------------------------------------------------------
    function pmiFinalButtonHtml(accepted) {
        return accepted ?
            `<button class="pmi-btn-final pmi-final-accepted" disabled>
                <iconify-icon icon="ph:heart-fill"></iconify-icon>${lbl_accepted}
             </button>` :
            `<button class="pmi-btn-final pmi-final-rejected" disabled>
                <iconify-icon icon="ph:heart"></iconify-icon>${lbl_rejected}
             </button>`;
    }

    function pmiHandleWrapResponse($wrap, url) {
        const $btns = $wrap.find('button');
        $btns.prop('disabled', true).html(`
            <iconify-icon class="pmi-spin" icon="eos-icons:loading" width="16"></iconify-icon>
            ${lbl_please_wait}
        `);

        pmiPostAction(url, {
            onSuccess: function(res) {
                const accepted = url.includes('/accept/');
                $wrap.replaceWith(pmiFinalButtonHtml(accepted));
                showToastMessage('success', res.message || '');
            },
            onFail: function() {
                $btns.prop('disabled', false);
            }
        });
    }

    // -----------------------------------------------------------------
    // Accept / Reject an incoming interest — Layout 2 (card panel)
    // -----------------------------------------------------------------
    function pmiRenderCardFinalState($panel, accepted) {
        $panel
            .attr('class', `pmi-card-panel ${accepted ? 'pmi-card-accepted' : 'pmi-card-rejected'}`)
            .html(`
                <p class="pmi-card-label"><em>${lbl_interest_in_profile}</em></p>
                <div class="pmi-card-circle ${accepted ? 'pmi-card-accepted' : 'pmi-card-rejected'}">
                    <iconify-icon icon="${accepted ? 'ph:heart-fill' : 'ph:heart'}"></iconify-icon>
                </div>
                <p class="pmi-card-caption">${accepted ? lbl_accepted : lbl_rejected}</p>
            `);
    }

    function pmiHandleCardResponse($panel, url) {
        const $btns = $panel.find('button');
        $btns.prop('disabled', true).html(
            `<iconify-icon class="pmi-spin" icon="eos-icons:loading" width="16"></iconify-icon>`
        );

        pmiPostAction(url, {
            onSuccess: function(res) {
                const accepted = url.includes('/accept/');
                pmiRenderCardFinalState($panel, accepted);
                showToastMessage('success', res.message || '');
            },
            onFail: function() {
                $btns.prop('disabled', false);
                $btns.each(function(i) {
                    $(this).html(
                        i === 0 ?
                        `<iconify-icon icon="ph:check-bold"></iconify-icon>` :
                        `<iconify-icon icon="ph:x-bold"></iconify-icon>`
                    );
                });
            }
        });
    }

    // -----------------------------------------------------------------
    // Dispatcher: picks the right renderer based on which container
    // the clicked element lives in — this is what lets one JS file
    // drive both layouts without touching the HTML.
    // -----------------------------------------------------------------
    function pmiRespondToInterest($triggerEl, url) {
        const $wrap = $triggerEl.closest('.pmi-respond-wrap');
        if ($wrap.length) {
            pmiHandleWrapResponse($wrap, url);
            return;
        }

        const $panel = $triggerEl.closest('.pmi-card-panel');
        if ($panel.length) {
            pmiHandleCardResponse($panel, url);
        }
    }

    // Open: tapping "Respond to Interest" reveals Accept/Reject — Layout 1 only
    $(document).on('click', '.pmi-respond-toggle', function(e) {
        e.stopPropagation();
        const $wrap = $(this).closest('.pmi-respond-wrap');
        $('.pmi-respond-wrap.pmi-open').not($wrap).removeClass('pmi-open');
        $wrap.addClass('pmi-open');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.pmi-respond-wrap').length) {
            $('.pmi-respond-wrap.pmi-open').removeClass('pmi-open');
        }
    });

    // Accept: fires immediately — works for both layouts' accept buttons
    $(document).on('click', '.pmi-accept, .pmi-card-accept-sm', function(e) {
        e.stopPropagation();
        const interestId = $(this).data('interest-id');
        pmiRespondToInterest($(this), appUrl(`express-interest/accept/${interestId}`));
    });

    // Reject: confirm via modal before firing — works for both layouts
    let pmiPendingRejectTarget = null;
    let pmiPendingRejectId = null;

    const $pmiRejectModalEl = document.getElementById('pmiInterestRejectModal');
    const pmiRejectModal = $pmiRejectModalEl ? new bootstrap.Modal($pmiRejectModalEl) : null;

    $(document).on('click', '.pmi-reject, .pmi-card-reject-sm', function(e) {
        e.stopPropagation();
        pmiPendingRejectId = $(this).data('interest-id');

        const $wrap = $(this).closest('.pmi-respond-wrap');
        pmiPendingRejectTarget = $wrap.length ? $wrap : $(this).closest('.pmi-card-panel');

        if (pmiRejectModal) pmiRejectModal.show();
    });

    $(document).on('click', '#pmiConfirmRejectBtn', function() {
        if (!pmiPendingRejectId || !pmiPendingRejectTarget) return;

        if (pmiRejectModal) pmiRejectModal.hide();

        const url = appUrl(`express-interest/reject/${pmiPendingRejectId}`);
        if (pmiPendingRejectTarget.hasClass('pmi-respond-wrap')) {
            pmiHandleWrapResponse(pmiPendingRejectTarget, url);
        } else {
            pmiHandleCardResponse(pmiPendingRejectTarget, url);
        }

        pmiPendingRejectId = null;
        pmiPendingRejectTarget = null;
    });

    // -----------------------------------------------------------------
    // Send Interest (state: none) / Send Reminder (state: sent)
    // Same endpoint & trigger classes in both layouts — only the
    // render target differs (a lone button vs. a full card panel).
    // -----------------------------------------------------------------
    function pmiSendInterestPill($btn, id) {
        const originalHtml = $btn.html();
        const originalClasses = $btn.attr('class');

        $btn.prop('disabled', true).html(`
            <iconify-icon class="pmi-spin" icon="eos-icons:loading" width="16"></iconify-icon>
            ${lbl_please_wait}
        `);

        $.post(appUrl('express-interest/send'), { receiver_member_id: id })
            .done(function(res) {
                if (!res || res.status === false) {
                    showToastMessage('error', (res && res.message) || 'Something went wrong.');
                    $btn.prop('disabled', false).attr('class', originalClasses).html(originalHtml);
                    return;
                }

                const data = res.data || res;
                const reminders = Number(data.reminder_count ?? data.total_reminder_count ?? 0);
                const maxReminders = Number(data.max_reminders ?? data.max_reminder_count ?? 0);
                const canSendMore = maxReminders > 0 && reminders < maxReminders;

                $btn
                    .removeClass('pmi-state-interest pmi-send-interest')
                    .addClass('pmi-btn-pill pmi-state-interested pmi-state-reminder pmi-send-reminder')
                    .prop('disabled', !canSendMore)
                    .html(
                        `<iconify-icon icon="ph:heart-fill"></iconify-icon> ` +
                        (canSendMore ?
                            `${lbl_send_reminder} (${reminders}/${maxReminders})` :
                            `${lbl_interest_sent}`)
                    );

                showToastMessage('success', res.message || '');
            })
            .fail(function(xhr) {
                $btn.prop('disabled', false).attr('class', originalClasses).html(originalHtml);

                if (xhr.status === 419) {
                    showToastMessage('error',
                        'Your session has expired. Please refresh the page and try again.');
                    return;
                }

                showToastMessage('error', 'Something went wrong. Please try again.');
            });
    }

    function pmiSendInterestCard($btn, $panel, id) {
        const originalPanelHtml = $panel.html();
        const originalPanelClass = $panel.attr('class');

        $btn.prop('disabled', true).html(
            `<iconify-icon class="pmi-spin" icon="eos-icons:loading" width="16"></iconify-icon>`
        );

        $.post(appUrl('express-interest/send'), { receiver_member_id: id })
            .done(function(res) {
                if (!res || res.status === false) {
                    showToastMessage('error', (res && res.message) || 'Something went wrong.');
                    $panel.attr('class', originalPanelClass).html(originalPanelHtml);
                    return;
                }

                const data = res.data || res;
                const reminders = Number(data.reminder_count ?? data.total_reminder_count ?? 0);
                const maxReminders = Number(data.max_reminders ?? data.max_reminder_count ?? 0);
                const canSendMore = maxReminders > 0 && reminders < maxReminders;

                $panel
                    .attr('class', `pmi-card-panel${canSendMore ? '' : ' pmi-card-sent'}`)
                    .html(`
                        <p class="pmi-card-label"><em>${lbl_interest_in_profile}</em></p>
                        <button type="button"
                            class="pmi-card-circle ${canSendMore ? 'pmi-card-reminder pmi-send-reminder' : 'pmi-card-sent'}"
                            data-id="${id}" ${canSendMore ? '' : 'disabled'}>
                            <iconify-icon icon="ph:heart-fill"></iconify-icon>
                        </button>
                        <p class="pmi-card-caption">
                            ${canSendMore ?
                                `${lbl_send_reminder} (${reminders}/${maxReminders})` :
                                `${lbl_interest_sent}`}
                        </p>
                    `);

                showToastMessage('success', res.message || '');
            })
            .fail(function(xhr) {
                $panel.attr('class', originalPanelClass).html(originalPanelHtml);

                if (xhr.status === 419) {
                    showToastMessage('error',
                        'Your session has expired. Please refresh the page and try again.');
                    return;
                }

                showToastMessage('error', 'Something went wrong. Please try again.');
            });
    }

    $(document).on('click', '.pmi-send-interest, .pmi-send-reminder', function(e) {
        e.stopPropagation();
        const $btn = $(this);
        if ($btn.is(':disabled')) return;

        const id = $btn.data('id');
        const $panel = $btn.closest('.pmi-card-panel');

        if ($panel.length) {
            pmiSendInterestCard($btn, $panel, id);
        } else {
            pmiSendInterestPill($btn, id);
        }
    });

    // -----------------------------------------------------------------
    // Shortlist toggle — identical markup/behaviour in both layouts
    // -----------------------------------------------------------------
    $(document).on('click', '.pmi-shortlist', function(e) {
        e.stopPropagation();
        const $btn = $(this);

        if ($btn.is(':disabled')) return;

        const id = $btn.data('id');
        const isActive = $btn.hasClass('pmi-active');
        const url = appUrl(isActive ? 'shortlist/remove' : 'shortlist/add');

        $btn.prop('disabled', true);

        $.post(url, { receiver_member_id: id })
            .done(function(res) {
                if (!res || !res.status) {
                    showToastMessage('error', (res && res.message) || 'Something went wrong.');
                    $btn.prop('disabled', false);
                    return;
                }

                $btn.toggleClass('pmi-active', !isActive).prop('disabled', false);
                showToastMessage('success', res.message || '');
            })
            .fail(function(xhr) {
                $btn.prop('disabled', false);

                if (xhr.status === 419) {
                    showToastMessage('error',
                        'Your session has expired. Please refresh the page and try again.');
                    return;
                }

                showToastMessage('error', 'Something went wrong. Please try again.');
            });
    });
});
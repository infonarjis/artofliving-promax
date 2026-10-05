@if (isset($riskStatus) &&
        $riskStatus &&
        $riskStatus->warning_count > 0 &&
        !$riskStatus->is_restricted &&
        !$riskStatus->is_suspended &&
        now()->diffInHours($riskStatus->last_warning_at) <= 24)
    <div class="alert alert-message-components my-3 warning fade show" role="alert">
        <div class="alert-icon">
            <iconify-icon icon="typcn:info"></iconify-icon>
        </div>
        <div class="alert-contents pe-3 ">
            <h4 class="fts-18 fw-6 text-warning">{{ __('messages.lbl_unusual_activity_detected_on_your_account') }}</h4>
            <p class="fts-16 fw-4 white-color-p">
                {{ __('messages.lbl_unusual_activity_detected_on_your_account_description') }}
            </p>
            <p class="fts-13 fw-4 text-danger">
                {{ __('messages.lbl_this_is_an_automated_safety_check_to_protect_all_members') }}
            </p>
        </div>
    </div>
    {{-- Dynamic Response Alert --}}
@endif
@if (isset($riskStatus) &&
        $riskStatus &&
        $riskStatus->warning_count > 0 &&
        !$riskStatus->is_restricted &&
        !$riskStatus->is_suspended &&
        now()->diffInHours($riskStatus->last_warning_at) <= 24)
    <div class="customsmallmodel_light photo-request modal fade" id="suspiciousWarningModal" tabindex="-1"
        aria-labelledby="upgradeMembershipPlanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                    <h2 class="fts-18 fw-6 white-color-n d-flex align-items-center gap-2"
                        id="upgradeMembershipPlanLabel">
                        <div class="icon-circle amber">
                            <iconify-icon icon="typcn:info" class="fts-18"></iconify-icon>
                        </div>
                        {{ __('messages.lbl_account_activity_warning') }}
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><iconify-icon
                            icon="radix-icons:cross-2"></iconify-icon></button>
                </div>
                <div class="modal_liteBody px-3 px-lg-4 py-3">
                    <div class="alert alert-message-components my-3 warning fade show" role="alert">
                        <div class="alert-icon">
                            <iconify-icon icon="typcn:info"></iconify-icon>
                        </div>
                        <div class="alert-contents pe-3">
                            <h4 class="fts-16 fw-5">
                                {{ __('messages.lbl_unusual_activity_detected_on_your_account_description') }}
                            </h4>
                        </div>
                    </div>
                    <p class="fts-13 fw-4 text-danger">
                        {{ __('messages.lbl_this_is_an_automated_safety_check_to_protect_all_members') }}
                    </p>
                    <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="click-changeButton" data-bs-dismiss="modal">
                            {{ __('messages.lbl_i_understand') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@if (isset($riskStatus) &&
        $riskStatus &&
        $riskStatus->warning_count > 0 &&
        !$riskStatus->is_restricted &&
        !$riskStatus->is_suspended &&
        now()->diffInHours($riskStatus->last_warning_at) <= 24)
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let lastShown = localStorage.getItem('suspicious_modal_shown_at');
            let now = new Date().getTime();
            // 6 hours gap
            if (!lastShown || (now - lastShown) > (6 * 60 * 60 * 1000)) {
                var myModal = new bootstrap.Modal(document.getElementById('suspiciousWarningModal'));
                myModal.show();

                localStorage.setItem('suspicious_modal_shown_at', now);
            }
        });
    </script>
@endif

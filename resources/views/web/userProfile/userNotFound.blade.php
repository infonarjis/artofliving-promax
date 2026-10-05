@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')

@push('styles')
    <style>
        /* --- Empty/pending/blocked centered state --- */
        .state-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 28px;
            text-align: center;
            gap: 14px;
        }

        .state-avatar-wrap {
            position: relative;
            margin-bottom: 4px;
        }

        .state-avatar {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 0 0 3px #ECEBF5;
        }

        .state-badge {
            position: absolute;
            bottom: -4px;
            right: -4px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 13px;
            border: 3px solid #fff;
        }

        .state-badge.pending {
            background: #F2A93B;
        }

        .state-badge.blocked {
            background: var(--danger);
        }

        .state-badge.sent {
            background: var(--primary-color);
        }

        .state-title {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
        }

        .state-sub {
            font-size: 13px;
            color: var(--ink-soft);
            max-width: 280px;
            line-height: 1.5;
            margin: 0;
        }

        .state-actions {
            display: flex;
            gap: 10px;
            margin-top: 6px;
        }

        .btn {
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 13.5px;
            padding: 11px 22px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: transform .15s ease, filter .15s ease;
        }

        .btn-cc {
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 13.5px;
            padding: 11px 22px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: transform .15s ease, filter .15s ease;
        }

        .btn-unblock {
            background: var(--success);
            color: #fff;
            box-shadow: 0 8px 18px rgba(34, 176, 125, .28);
        }

        .user-not-found-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }

        .user-not-found-state-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 28px;
            gap: 12px;
        }

        .user-not-found-state-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 0 0 3px #ECEBF5;
            margin-bottom: 4px;
        }

        .user-not-found-state-avatar.grayscale {
            filter: grayscale(70%);
        }

        .user-not-found-state-avatar.blocked {
            filter: grayscale(100%);
            opacity: .6;
        }

        .user-not-found-state-title {
            color: var(--white-color);
            font-size: 16px;
            font-weight: 700;
            margin: 0;
        }

        .user-not-found-state-sub {
            font-size: 13px;
            color: #6B7086;
            max-width: 320px;
            line-height: 1.5;
            margin: 0;
        }

        .user-not-found-state-actions {
            display: flex;
            gap: 10px;
            margin-top: 6px;
        }

        .user-not-found-pill {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .03em;
            padding: 5px 14px;
            border-radius: 999px;
        }

        .user-not-found-pill-info {
            background: #EFEAFF;
            color: #6C5CE7;
        }

        .user-not-found-pill-warn {
            background: #FFF6E5;
            color: #B8790A;
        }

        .user-not-found-pill-danger {
            background: #FDECEC;
            color: #E5484D;
        }

        .user-not-found-btn-success {
            background: #22B07D;
            color: #fff;
        }

        .user-not-found-btn {
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 13.5px;
            padding: 11px 22px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .user-not-found-btn.primary-color {
            background: var(--primary-color);
            color: #fff;
        }

        .user-not-found-btn.light-color {
            border: 2px solid var(--primary-color);
            color: var(--primary-color) !important;
            background: transparent;
        }
    </style>
@endpush

@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    
    @php
        $action_type = $action_type ?? '';

        if (!empty($userData)) {
            $receiverProfileImage = _getMemberDefaultImage($userData->gender);

            $profileMessage = __('messages.lbl_profile_with_id_is_no_longer_available', [
                'matri_id' => $userData->matri_id,
            ]);

            if ($action_type == 'personalized_user_restricted') {
                $profileMessage = __(
                    'messages.lbl_this_profile_has_been_personalized_and_is_currently_unavailable_please_contact_the_administrator_for_more_information',
                );
            }
        } else {
            $authUser = auth()->guard('web')->user();

            $receiverProfileImage =
                $authUser->gender === 'Male' ? _getMemberDefaultImage('Female') : _getMemberDefaultImage('Male');

            $profileMessage = __('messages.lbl_profile_is_no_longer_available');
        }
    @endphp
    @if ($action_type == 'blocked_user')
        <div class="user-not-found-state-panel">
            <img src="{{ $receiverProfileImage }}" class="user-not-found-state-avatar blocked" alt="">
            <span class="user-not-found-pill user-not-found-pill-danger">{{ __('messages.lbl_blocked') }}</span>
            <h3 class="user-not-found-state-title">
                {{ __('messages.msg_you_blocked_member', ['name' => _profileTitle($userData)]) }}
            </h3>
            <p class="user-not-found-state-sub">{{ __('messages.msg_blocked_member_description') }}</p>
            <button class="user-not-found-btn user-not-found-btn-success unblock-member-action">
                <iconify-icon icon="ph:check-circle-fill"></iconify-icon>
                {{ __('messages.lbl_unblock') }} {{ _profileTitle($userData) }}
            </button>
        </div>
    @elseif ($action_type == 'upgrade_membership_plan')
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content">
                <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                    <h2 class="fts-18 fw-6 white-color-n d-flex align-items-center gap-2" id="upgradeMembershipPlanLabel">
                        <div class="icon-circle amber">
                            <iconify-icon icon="solar:crown-broken" class="fts-18"></iconify-icon>
                        </div>
                        {{ __('messages.lbl_upgrade_to_membership_plan') }}
                    </h2>
                </div>
                <div class="modal_liteBody px-3 px-lg-4 py-3">
                    <div class="alert alert-message-components my-3 warning fade show" role="alert">
                        <div class="alert-icon">
                            <iconify-icon icon="solar:crown-broken"></iconify-icon>
                        </div>
                        <div class="alert-contents pe-3">
                            <h4 class="fts-16 fw-5">{{ __('messages.lbl_upgrade_to_membership_plan') }}</h4>
                            <p class="fts-13 fw-4 opacity-75">
                                {{ __('messages.lbl_upgrade_to_membership_plan_to_unlock_full_access') }}
                            </p>
                        </div>
                    </div>
                    <div class="modal-buttonsGroup d-flex justify-content-center gap-2 mt-4">
                        <a href="{{ route('web.membershipPlan.index') }}"
                            class="click-changeButton">{{ __('messages.lbl_upgrade_membership_now') }}</a>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($action_type != 'blocked_user')
        {{-- User Not Found --}}
        <div class="user-not-found-state-panel">
            <img src="{{ $receiverProfileImage }}" class="user-not-found-state-avatar grayscale" alt="">
            <span class="user-not-found-pill user-not-found-pill-danger">
                {{ __('messages.lbl_unavailable') }}
            </span>
            <h3 class="user-not-found-state-title">
                {{ __('messages.lbl_this_profile_is_no_longer_available') }}
            </h3>
            <p class="user-not-found-state-sub">
                {{ $profileMessage }}
            </p>
            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('web.matches.recommended') }}" class="user-not-found-btn primary-color">
                    <iconify-icon icon="ph:user-not-found-circle-dots-fill"></iconify-icon>
                    {{ __('messages.lbl_browse_matches') }}
                </a>
                <a href="{{ route('web.dashboard.index') }}" class="user-not-found-btn light-color">
                    <iconify-icon icon="ph:user-not-found-circle-dots-fill"></iconify-icon>
                    {{ __('messages.lbl_back_to_dashoard') }}
                </a>
            </div>
        </div>
    @endif
    {{-- User Not Found --}}
@endsection

@push('scripts')
    @if (!empty($userData))
        <script>
            $(document).on('click', '.unblock-member-action', function() {

                const $button = $(this);
                // Prevent multiple clicks
                if ($button.prop('disabled')) {
                    return;
                }
                $button.prop('disabled', true);
                $button.prop('disabled', true).html(`
                <iconify-icon icon="eos-icons:loading" width="20"></iconify-icon>
                {{ __('messages.lbl_please_wait') }}
            `);
                $.ajax({
                    url: "{{ route('web.blocklist.addRemove') }}",
                    type: "POST",
                    data: {
                        _token: csrfToken,
                        receiver_member_id: "{{ $userData->id }}"
                    },
                    success: function(response) {
                        showToastMessage(
                            'success',
                            response.message || 'Action completed successfully.'
                        );
                        // Reload if the block/unblock state needs to update
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message ||
                            'Something went wrong. Please try again.';
                        showToastMessage('error', message);
                        $button.prop('disabled', false);
                    }
                });
            });
        </script>
    @endif
@endpush

@php
    $onlineStatus = _memberOnlineStatus($result);
@endphp
<div class="pro-matches-list card-1-result">
    <div class="single-matches-box-main mt-3">
        <div class="row">
            <div class="col-lg-3 matches-left-main-profiles">
                <div class="d-flex gap-2">
                    <div class="position-relative w-100">
                        @php
                            $canView = _canViewMemberPhoto($result, $result->hasPhotoRequestAccess);
                            $hasPhoto = _checkPhotoExist($result);
                            $profileImage = _getMemberProfileImage($result);
                        @endphp
                        @if (!$canView && $hasPhoto)
                            <div class="matches-users-profiles photo-protected-main">
                                <img src="{{ _getProtectedImage($result->gender) }}" alt="{{ _profileTitle($result) }}"
                                    class="matches-profiles">
                                <div class="photo-request-btn-wrap">
                                    <button type="button" class="btn-photo-request open-photo-request-modal"
                                        data-receiver-id="{{ $result->id }}">
                                        <iconify-icon icon="solar:camera-add-bold"></iconify-icon>
                                        {{ __('messages.lbl_request_photo') }}
                                    </button>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('web.userProfile.index', _encrypt($result->id)) }}">
                                <div class="matches-users-profiles">
                                    <img src="{{ $profileImage }}" alt="{{ _profileTitle($result) }}"
                                        class="matches-profiles">
                                </div>
                            </a>
                        @endif

                        @if ($result->plan_status == 'Paid')
                            <div class="premium-icon-dash"><iconify-icon icon="solar:crown-bold"></iconify-icon></div>
                        @endif
                        @if ($result->matchPercent > 0)
                            <p class="match-percent-badge">{{ $result->matchPercent }}% Match</p>
                        @endif
                        {{-- <div class="matchprofile-namestatus align-items-center">
                            @if ($onlineStatus['status_code'] == 'online')
                                <p class="green-color-n fts-13 fw-6 text-uppercase">{{ __('messages.lbl_online') }}</p>
                            @endif
                        </div> --}}
                    </div>
                    <div class="matches-calling-chats d-flex flex-column">
                        @php
                            $isPaid = auth('web')->check() && auth('web')->user()->plan_status === 'Paid';
                            $voiceApproved = $configArr['zego_voice_call_setting'] === 'APPROVED';
                            $videoApproved = $configArr['zego_video_call_setting'] === 'APPROVED';
                            $canVideoCall = $isPaid && $videoApproved && $canVideoCall;
                            $canVoiceCall = $isPaid && $voiceApproved && $canVoiceCall;
                            $canChat = $isPaid && $currentPlan->can_chat;
                        @endphp
                        @if ($voiceApproved)
                            @if ($canVoiceCall)
                                <a href="{{ route('web.videoVoiceCall.inititeVoiceCall', _encrypt($result->id)) }}"
                                    class="btn-matches-call audi-call">
                                    <iconify-icon icon="mingcute:phone-call-line"></iconify-icon>
                                </a>
                            @else
                                <button class="btn-matches-call audi-call" data-bs-toggle="modal"
                                    data-bs-target="#upgradeMembershipPlan">
                                    <iconify-icon icon="mingcute:phone-call-line"></iconify-icon>
                                </button>
                            @endif
                        @endif
                        @if ($videoApproved)
                            @if ($canVideoCall)
                                <a href="{{ route('web.videoVoiceCall.inititeVideoCall', _encrypt($result->id)) }}"
                                    class="btn-matches-call">
                                    <iconify-icon icon="lucide:video"></iconify-icon>
                                </a>
                            @else
                                <button class="btn-matches-call" data-bs-toggle="modal"
                                    data-bs-target="#upgradeMembershipPlan">
                                    <iconify-icon icon="lucide:video"></iconify-icon>
                                </button>
                            @endif
                        @endif
                        @if ($canChat)
                            @if ($configArr['chat_module_design'] == 'popup')
                                <button class="btn-matches-call message"
                                    onclick="startChatFromButton({{ $result->id }})">
                                    <iconify-icon icon="iconoir:chat-bubble"></iconify-icon>
                                </button>
                            @else
                                <a href="{{ route('web.chat.chatConversation', _encrypt($result->id)) }}">
                                    <button class="btn-matches-call message">
                                        <iconify-icon icon="iconoir:chat-bubble"></iconify-icon>
                                    </button>
                                </a>
                            @endif
                        @else
                            <button class="btn-matches-call" data-bs-toggle="modal"
                                data-bs-target="#upgradeMembershipPlan">
                                <iconify-icon icon="iconoir:chat-bubble"></iconify-icon>
                            </button>
                        @endif
                        <button class="btn-matches-call more report-profile-btn" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Report Profile" data-receiver-id="{{ $result->id }}">
                            <iconify-icon icon="lucide:info"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 matches-right-main-contents ps-xl-1">
                <div class="matches-flex-content-list d-xl-flex gap-2 justify-content-between gap-xxl-3 px-2">
                    <div class="matches-profile-contents mt-lg-2 w-100">
                        <a href="{{ route('web.userProfile.index', _encrypt($result->id)) }}">
                            <div class="fts-20 fw-7 white-color-n mb-2 mt-4 mt-lg-0">
                                {{ _profileTitle($result) }}
                            </div>
                        </a>
                        <div
                            class="d-flex align-items-center gap-2 mb-3 white-color-n fts-14 fw-5 justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <iconify-icon icon="hugeicons:message-02" width="22"
                                    class="black-color6-n {{ $onlineStatus['status_class'] }}  "></iconify-icon>
                                {{ $onlineStatus['status_text'] }}
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <iconify-icon icon="mdi:ring" width="22"
                                    class="black-color6-n text-primary"></iconify-icon>
                                {{ _displayNotAvailable(optional($result->maritalStatusData)->translated_name) }}
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <iconify-icon icon="mdi:temple-hindu" width="22"
                                    class="black-color6-n text-warning"></iconify-icon>
                                {{ _displayNotAvailable(optional($result->religionData)->translated_name) }}
                            </div>
                        </div>
                        <div class="section-divider"></div>
                        <ul class="matches-details-lists  gap-1">
                            <li class="odd-matches fts-14 white-color-n my-lg-1 fw-5">
                                <span class="fw-4">{{ __('messages.field_lbl_date_of_birth') }}</span>
                                <span class="text-end">{{ _displayDate($result->birthdate, 'j F, Y') }}</span>
                            </li>
                            <li class="odd-matches fts-14 white-color-n my-lg-1 fw-5">
                                <span class="fw-4">{{ __('messages.field_lbl_education') }}</span>
                                <span class="text-end">
                                    {{ _displayNotAvailable(\Illuminate\Support\Str::limit(implode(', ', $result->education_level_names ?? []), 35)) }}
                                </span>
                            </li>
                            <li class="odd-matches fts-14 white-color-n my-lg-1 fw-5">
                                <span class="fw-4">{{ __('messages.lbl_location') }}</span>
                                <span class="text-end">{{ _getMemberLocation($result) }}</span>
                            </li>
                        </ul>
                        @php
                            $profileText = trim($result->about_me_description ?? '');
                            $limit = 80;
                        @endphp
                        @if (strlen($profileText) > $limit)
                            <div class="white-color-n fts-14 mt-2 read-more-box">
                                <span class="short-text">
                                    {{ \Illuminate\Support\Str::limit($profileText, $limit) }}
                                </span>
                                <span class="full-text d-none">{{ $profileText }}</span>
                                <span class="fw-6 primary-color-n read-toggle" style="cursor:pointer;">
                                    {{ __('messages.lbl_read_more') }}
                                </span>
                            </div>
                        @elseif(!blank($profileText))
                            <div class="white-color-n fts-14 mt-2">
                                {{ $profileText }}
                            </div>
                        @endif
                    </div>
                    <div class="right-divider"></div>
                    <div class="matche-action-btngroup d-flex flex-xl-column gap-2 mt-2 justify-content-between">
                        <button class="btn-matches-Shortlist mb-lg-1 add-shortlist" data-id="{{ $result->id }}">
                            @if ($result->is_shortlisted)
                                <iconify-icon
                                    icon="flowbite:star-solid"></iconify-icon>{{ __('messages.lbl_shortlist') }}
                            @else
                                <iconify-icon
                                    icon="flowbite:star-outline"></iconify-icon>{{ __('messages.lbl_shortlist') }}
                            @endif
                        </button>

                        @if (!Auth::check())
                            <button class="btn-matches-Interest mb-lg-1" data-bs-toggle="modal"
                                data-bs-target="#upgradeMembershipPlan">
                                <iconify-icon icon="uil:heart"></iconify-icon>{{ __('messages.lbl_interest') }}
                            </button>
                        @else
                            @php
                                $interestState = $result->interest_state ?? 'none';
                                $interestStatus = $result->interest_status ?? null;
                            @endphp
                            @if ($interestState === 'none')
                                <button class="pmi-btn-pill pmi-state-interest pmi-send-interest"
                                    data-id="{{ $result->id }}" type="button">
                                    <iconify-icon icon="ph:heart"></iconify-icon>
                                    {{ __('messages.lbl_interest') }}
                                </button>
                            @elseif ($interestState === 'sent' && $interestStatus === 'Pending')
                                <button
                                    class="pmi-btn-pill pmi-state-interested {{ $result->can_send_reminder ? 'pmi-state-reminder pmi-send-reminder' : '' }}"
                                    data-id="{{ $result->id }}" type="button"
                                    {{ $result->can_send_reminder ? '' : 'disabled' }}>
                                    <iconify-icon icon="ph:heart-fill"></iconify-icon>
                                    @if ($result->can_send_reminder)
                                        {{ __('messages.lbl_send_reminder') . '(' . $result->reminder_count . '/' . _getConstant('express_interest.max_reminders') . ')' }}
                                    @else
                                        {{ __('messages.lbl_interest_sent') }}
                                    @endif
                                </button>
                            @elseif ($interestState === 'sent' && $interestStatus === 'Accepted')
                                <button class="pmi-btn-final pmi-final-accepted" disabled>
                                    <iconify-icon icon="ph:heart-fill"></iconify-icon>
                                    {{ __('messages.lbl_accepted') }}
                                </button>
                            @elseif ($interestState === 'sent' && $interestStatus === 'Rejected')
                                <button class="pmi-btn-final pmi-final-rejected" disabled>
                                    <iconify-icon icon="ph:heart"></iconify-icon>
                                    {{ __('messages.lbl_rejected') }}
                                </button>
                            @elseif ($interestState === 'received' && $interestStatus === 'Pending')
                                <div class="pmi-respond-wrap">
                                    <button class="pmi-btn-pill pmi-state-interested pmi-respond-toggle"
                                        type="button">
                                        <iconify-icon icon="ph:heart-fill"></iconify-icon>
                                        {{ __('messages.lbl_respond_interest') }}
                                    </button>

                                    <div class="pmi-respond-options">
                                        <button class="pmi-btn-pill pmi-accept"
                                            data-interest-id="{{ $result->interest_id }}" type="button">
                                            <iconify-icon icon="ph:check-bold"></iconify-icon>
                                            {{ __('messages.lbl_accept') }}
                                        </button>

                                        <button class="pmi-btn-pill pmi-reject"
                                            data-interest-id="{{ $result->interest_id }}" type="button">
                                            <iconify-icon icon="ph:x-bold"></iconify-icon>
                                            {{ __('messages.lbl_reject') }}
                                        </button>
                                    </div>
                                </div>
                            @elseif ($interestState === 'received' && $interestStatus === 'Accepted')
                                <button class="pmi-btn-final pmi-final-accepted" disabled>
                                    <iconify-icon icon="ph:heart-fill"></iconify-icon>
                                    {{ __('messages.lbl_accepted') }}
                                </button>
                            @elseif ($interestState === 'received' && $interestStatus === 'Rejected')
                                <button class="pmi-btn-final pmi-final-rejected" disabled>
                                    <iconify-icon icon="ph:heart"></iconify-icon>
                                    {{ __('messages.lbl_rejected') }}
                                </button>
                            @endif
                        @endif

                        @php $isBlocked = $result->isBlockedByAuth(); @endphp
                        <button class="block-action btn-matches-Report mb-lg-1" data-id="{{ $result->id }}"
                            data-blocked="{{ $isBlocked ? 1 : 0 }}">
                            <iconify-icon class="block-icon fts-24 white-color70-n"
                                icon="{{ $isBlocked ? 'bx:shield-x' : 'bx:shield' }}">
                            </iconify-icon>
                            <span class="block-text">
                                {{ $isBlocked ? 'Unblock' : 'Block' }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

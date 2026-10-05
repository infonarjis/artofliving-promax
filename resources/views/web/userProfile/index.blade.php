@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    @php
        $onlineStatus = _memberOnlineStatus($userData);

        $photoVal1 = _getMemberProfileImage($userData, '', 'photo1');
        $photoVal2 = null;
        $photoVal3 = null;
        $photoVal4 = null;
        if (!empty($userData->photo2) && $userData->photo2_status == 'APPROVED') {
            $photoVal2 = _getMemberProfileImage($userData, '', 'photo2');
        }
        if (!empty($userData->photo3) && $userData->photo3_status == 'APPROVED') {
            $photoVal3 = _getMemberProfileImage($userData, '', 'photo3');
        }
        if (!empty($userData->photo4) && $userData->photo4_status == 'APPROVED') {
            $photoVal4 = _getMemberProfileImage($userData, '', 'photo4');
        }
        $photos = array_values(array_filter([$photoVal1, $photoVal2, $photoVal3, $photoVal4]));
    @endphp
    <!-- user profile section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="col-xl-11 mx-auto">
                    <div class="multie-user-profile-slide">
                        <div class="slides_userprofiles-main">
                            <div class="single-user-profile slide active">
                                <div class="single-matches-box-main position-relative px-lg-5 px-3">
                                    {{-- <div class="preview-user-arrow d-none d-lg-flex cursor-pointer"><iconify-icon
                                            icon="mingcute:arrow-left-line"></iconify-icon></div>
                                    <div class="next-user-arrow d-none d-lg-flex cursor-pointer"><iconify-icon
                                            icon="mingcute:arrow-right-line"></iconify-icon></div> --}}
                                    <div class="pro-matches-list card-1-result">

                                        <div class="row">
                                            <div class="col-lg-3 matches-left-main-profiles">
                                                <div class="d-flex gap-2">
                                                    <div class="position-relative w-100">
                                                        @php
                                                            $profileImage = _getMemberProfileImage($userData);

                                                            $canView = _canViewMemberPhoto(
                                                                $userData,
                                                                $userData->hasPhotoRequestAccess,
                                                            );
                                                            $hasPhoto = _checkPhotoExist($userData);
                                                        @endphp

                                                        {{-- REQUIRED: PhotoSwipe gallery wrapper --}}
                                                        @if (!$canView && $hasPhoto)
                                                            <div class="matches-users-profiles photo-protected-main">
                                                                {{-- Protected image --}}
                                                                <img src="{{ _getProtectedImage($userData->gender) }}"
                                                                    class="matches-profiles" style="cursor: zoom-in;">
                                                                <div class="photo-request-btn-wrap">
                                                                    <button type="button"
                                                                        class="btn-photo-request open-photo-request-modal"
                                                                        data-receiver-id="{{ $userData->id }}">
                                                                        <iconify-icon
                                                                            icon="solar:camera-add-bold"></iconify-icon>
                                                                        {{ __('messages.lbl_request_photo') }}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div id="member-gallery" class="matches-users-profiles">
                                                                {{-- visible image ONLY --}}
                                                                <img id="mainImage" src="{{ $photos[0] }}"
                                                                    class="matches-profiles" style="cursor: zoom-in;">

                                                                {{-- hidden gallery items --}}
                                                                @foreach ($photos as $photo)
                                                                    <a href="{{ $photo }}" data-pswp-width="500"
                                                                        data-pswp-height="500"></a>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        @if ($userData->plan_status == 'Paid')
                                                            <div class="premium-icon-dash-user-profile"><iconify-icon
                                                                    icon="solar:crown-bold"></iconify-icon></div>
                                                        @endif
                                                        @if ($userData->matchPercent > 0)
                                                            <p class="match-percent-badge">{{ $userData->matchPercent }}%
                                                                {{ __('messages.lbl_match') }}</p>
                                                        @endif
                                                        <div class="matchprofile-namestatus align-items-center">
                                                            <iconify-icon id="openGallery" class="white-color-p"
                                                                icon="hugeicons:album-02" width="24" height="24"
                                                                style="cursor:pointer;">
                                                            </iconify-icon>
                                                            @if ($onlineStatus['status_code'] == 'online')
                                                                <p class="green-color-n fts-13 fw-6 text-uppercase">
                                                                    {{ __('messages.lbl_online') }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="matches-calling-chats d-flex flex-column">
                                                        @php
                                                            $isPaid = $authUser->plan_status === 'Paid';
                                                            $voiceApproved =
                                                                $configArr['zego_voice_call_setting'] === 'APPROVED';
                                                            $videoApproved =
                                                                $configArr['zego_video_call_setting'] === 'APPROVED';
                                                            $canVideoCall = $isPaid && $videoApproved && $canVideoCall;
                                                            $canVoiceCall = $isPaid && $voiceApproved && $canVoiceCall;
                                                            $canChat = $isPaid && $currentPlan->can_chat;
                                                        @endphp
                                                        @if ($voiceApproved)
                                                            @if ($canVoiceCall)
                                                                <a href="{{ route('web.videoVoiceCall.inititeVoiceCall', _encrypt($userData->id)) }}"
                                                                    class="btn-matches-call audi-call">
                                                                    <iconify-icon
                                                                        icon="mingcute:phone-call-line"></iconify-icon>
                                                                </a>
                                                            @else
                                                                <button class="btn-matches-call audi-call"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#upgradeMembershipPlan">
                                                                    <iconify-icon
                                                                        icon="mingcute:phone-call-line"></iconify-icon>
                                                                </button>
                                                            @endif
                                                        @endif
                                                        @if ($videoApproved)
                                                            @if ($canVideoCall)
                                                                <a href="{{ route('web.videoVoiceCall.inititeVideoCall', _encrypt($userData->id)) }}"
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
                                                                    onclick="startChatFromButton({{ $userData->id }})">
                                                                    <iconify-icon icon="iconoir:chat-bubble"></iconify-icon>
                                                                </button>
                                                            @else
                                                                <a
                                                                    href="{{ route('web.chat.chatConversation', _encrypt($userData->id)) }}">
                                                                    <button class="btn-matches-call message">
                                                                        <iconify-icon
                                                                            icon="iconoir:chat-bubble"></iconify-icon>
                                                                    </button>
                                                                </a>
                                                            @endif
                                                        @else
                                                            <button class="btn-matches-call message" data-bs-toggle="modal"
                                                                data-bs-target="#upgradeMembershipPlan">
                                                                <iconify-icon icon="iconoir:chat-bubble"></iconify-icon>
                                                            </button>
                                                        @endif
                                                        <button class="btn-matches-call more report-profile-btn"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ __('messages.lbl_report_profile') }}"
                                                            data-receiver-id="{{ $userData->id }}">
                                                            <iconify-icon icon="lucide:info"></iconify-icon>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-9 matches-right-main-contents ps-xl-1">
                                                <div
                                                    class="matches-flex-content-list d-xl-flex gap-2 justify-content-between gap-xxl-3 px-2">
                                                    <div class="matches-profile-contents mt-lg-2 w-100">
                                                        <div class="fts-20 fw-7 white-color-n mb-2 mt-4 mt-lg-0">
                                                            {{ _profileTitle($userData) }}
                                                        </div>
                                                        <div
                                                            class="d-flex  mb-3 white-color-n fts-14 fw-5 justify-content-between">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <iconify-icon icon="hugeicons:message-02" width="22"
                                                                    class="black-color6-n {{ $onlineStatus['status_class'] }}  "></iconify-icon>
                                                                {{ $onlineStatus['status_text'] }}
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <iconify-icon icon="mdi:ring" width="22"
                                                                    class="black-color6-n text-primary"></iconify-icon>
                                                                {{ _displayNotAvailable($userData->maritalStatusData->translated_name) }}
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <iconify-icon icon="mdi:temple-hindu" width="22"
                                                                    class="black-color6-n text-warning"></iconify-icon>
                                                                {{ _displayNotAvailable($userData->religionData->translated_name) }}
                                                            </div>
                                                        </div>
                                                        <div class="section-divider"></div>
                                                        <ul class="matches-details-lists  gap-1">
                                                            <li class="odd-matches fts-14 white-color-n my-lg-1 fw-5">
                                                                <span
                                                                    class="fw-4">{{ __('messages.field_lbl_date_of_birth') }}</span>
                                                                <span
                                                                    class="text-end">{{ _displayDate($userData->birthdate, 'j F, Y') }}</span>
                                                            </li>
                                                            <li class="odd-matches fts-14 white-color-n my-lg-1 fw-5">
                                                                <span
                                                                    class="fw-4">{{ __('messages.field_lbl_education') }}</span>
                                                                <span class="text-end">
                                                                    {{ _displayNotAvailable(implode(', ', $userData->education_level_names) ?? null) }}
                                                                </span>
                                                            </li>
                                                            <li class="odd-matches fts-14 white-color-n my-lg-1 fw-5">
                                                                <span
                                                                    class="fw-4">{{ __('messages.lbl_location') }}</span>
                                                                <span
                                                                    class="text-end">{{ _getMemberLocation($userData) }}</span>
                                                            </li>
                                                        </ul>
                                                        @php
                                                            $profileText = trim($userData->about_me_description ?? '');
                                                            $limit = 80;
                                                        @endphp
                                                        @if (strlen($profileText) > $limit)
                                                            <div class="white-color-n fts-14 mt-2 read-more-box">
                                                                <span
                                                                    class="fw-4">{{ __('messages.field_lbl_about_me') }}
                                                                    : </span>
                                                                <span class="short-text">
                                                                    {{ \Illuminate\Support\Str::limit($profileText, $limit) }}
                                                                </span>
                                                                <span class="full-text d-none">{{ $profileText }}</span>
                                                                <span class="fw-6 primary-color-n read-toggle"
                                                                    style="cursor:pointer;">
                                                                    {{ __('messages.lbl_read_more') }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <div class="white-color-n fts-14 mt-2">
                                                                <span
                                                                    class="fw-4">{{ __('messages.field_lbl_about_me') }}
                                                                    : </span>
                                                                {{ _displayNotAvailable($profileText) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="right-divider"></div>
                                                    <div
                                                        class="matche-action-btngroup d-flex flex-wrap flex-xl-column gap-2 mt-2">
                                                        <button class="btn-matches-Shortlist mb-lg-1 add-shortlist"
                                                            data-id="{{ $userData->id }}">
                                                            @if ($userData->is_shortlisted)
                                                                <iconify-icon icon="flowbite:star-solid"></iconify-icon>
                                                                <span>{{ __('messages.lbl_shortlisted') }}</span>
                                                            @else
                                                                <iconify-icon icon="flowbite:star-outline"></iconify-icon>
                                                                <span>{{ __('messages.lbl_shortlist') }}</span>
                                                            @endif
                                                        </button>

                                                        @php
                                                            $interestState = $userData->interest_state ?? 'none';
                                                            $interestStatus = $userData->interest_status ?? null;
                                                        @endphp
                                                        @if ($interestState === 'none')
                                                            <button
                                                                class="pmi-btn-pill pmi-state-interest pmi-send-interest"
                                                                data-id="{{ $userData->id }}" type="button">
                                                                <iconify-icon icon="ph:heart"></iconify-icon>
                                                                {{ __('messages.lbl_interest') }}
                                                            </button>
                                                        @elseif ($interestState === 'sent' && $interestStatus === 'Pending')
                                                            <button
                                                                class="pmi-btn-pill pmi-state-interested {{ $userData->can_send_reminder ? 'pmi-state-reminder pmi-send-reminder' : '' }}"
                                                                data-id="{{ $userData->id }}" type="button"
                                                                {{ $userData->can_send_reminder ? '' : 'disabled' }}>
                                                                <iconify-icon icon="ph:heart-fill"></iconify-icon>
                                                                @if ($userData->can_send_reminder)
                                                                    {{ __('messages.lbl_send_reminder') . '(' . $userData->reminder_count . '/' . _getConstant('express_interest.max_reminders') . ')' }}
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
                                                                <button
                                                                    class="pmi-btn-pill pmi-state-interested pmi-respond-toggle"
                                                                    type="button">
                                                                    <iconify-icon icon="ph:heart-fill"></iconify-icon>
                                                                    {{ __('messages.lbl_respond_interest') }}
                                                                </button>

                                                                <div class="pmi-respond-options">
                                                                    <button class="pmi-btn-pill pmi-accept"
                                                                        data-interest-id="{{ $userData->interest_id }}"
                                                                        type="button">
                                                                        <iconify-icon icon="ph:check-bold"></iconify-icon>
                                                                        {{ __('messages.lbl_accept') }}
                                                                    </button>

                                                                    <button class="pmi-btn-pill pmi-reject"
                                                                        data-interest-id="{{ $userData->interest_id }}"
                                                                        type="button">
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


                                                        @if ($authUser->plan_status === 'Paid' || $hasViewedContact)
                                                            <button
                                                                class="btn-matches-ContactRequest mb-lg-1 view-contact-btn"
                                                                data-id="{{ $userData->id }}"
                                                                data-viewed="{{ $hasViewedContact ? 1 : 0 }}">
                                                                <iconify-icon
                                                                    icon="streamline:user-profile-focus"></iconify-icon>
                                                                {{ __('messages.lbl_view_contact') }}
                                                            </button>
                                                        @else
                                                            <button class="btn-matches-ContactRequest mb-lg-1 "
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#upgradeMembershipPlan">
                                                                <iconify-icon
                                                                    icon="streamline:user-profile-focus"></iconify-icon>
                                                                {{ __('messages.lbl_view_contact') }}
                                                            </button>
                                                        @endif

                                                        @php $isBlocked = $userData->isBlockedByAuth(); @endphp
                                                        <button class="block-action btn-matches-Report mb-lg-1"
                                                            data-id="{{ $userData->id }}"
                                                            data-blocked="{{ $isBlocked ? 1 : 0 }}">
                                                            <iconify-icon class="block-icon fts-24 white-color70-n"
                                                                icon="{{ $isBlocked ? 'bx:shield-x' : 'bx:shield' }}">
                                                            </iconify-icon>
                                                            <span class="block-text">
                                                                {{ $isBlocked ? __('messages.lbl_unblock') : __('messages.lbl_block') }}
                                                            </span>
                                                        </button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="common-tabs-design mt-3">
                                    <ul class="nav flex-nowrap d-flex nav-pills p-1" id="pills-tab" role="tablist">
                                        <li class="nav-item w-50">
                                            <button class="nav-link fts-15 active" id="myprofile-tab"
                                                data-bs-toggle="pill" data-bs-target="#myprofile" type="button"
                                                role="tab" aria-controls="myprofile"
                                                aria-selected="true"><iconify-icon icon="lets-icons:user-add"
                                                    class="fts-20 me-1"></iconify-icon>
                                                {{ __('messages.lbl_my_profile') }}
                                            </button>
                                        </li>
                                        <li class="nav-item w-50">
                                            <button class="nav-link fts-15" id="partner-profile-tab"
                                                data-bs-toggle="pill" data-bs-target="#partner-profile" type="button"
                                                role="tab" aria-controls="partner-profile"
                                                aria-selected="false"><iconify-icon icon="tabler:users-plus"
                                                    class="fts-20 me-1"></iconify-icon>
                                                {{ __('messages.lbl_partner_preferences') }}
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="common-tablist-design mt-3">
                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="myprofile" role="tabpanel"
                                            aria-labelledby="myprofile">
                                            @foreach ($memberDetailSections as $section)
                                                <div class="common-bgwhite-main mt-2 py-2 px-3 ps-lg-4 pe-1 pe-sm-3">
                                                    <div
                                                        class="topcolleps-edtbar d-flex align-items-center justify-content-between py-lg-1">
                                                        <h4 class="fts-16 fw-7 white-color-n w-100">
                                                            {{ $section['label'] }}
                                                        </h4>
                                                        <div class="d-flex align-items-center gap-1">
                                                            <a class="profile-arrow-collapse" data-bs-toggle="collapse"
                                                                href="#{{ $section['section_key'] }}"
                                                                aria-controls="{{ $section['section_key'] }}"
                                                                aria-expanded="true"><iconify-icon
                                                                    icon="iconamoon:arrow-down-2"></iconify-icon></a>
                                                        </div>
                                                    </div>
                                                    <div class="collapse show" id="{{ $section['section_key'] }}">
                                                        <div class="">
                                                            <div class="row mt-2 px-1">
                                                                @foreach ($section['fields'] as $fields)
                                                                    <div class="col-md-4 col-6 mb-3 pb-md-1 px-2">
                                                                        <div class="fts-14 fw-5 white-color70-n">
                                                                            {{ $fields['label'] }}</div>
                                                                        <div class="fts-14 fw-5 white-color-n mt-1">
                                                                            {{ _displayNotAvailable($fields['value']) }}
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="tab-pane fade" id="partner-profile" role="tabpanel"
                                            aria-labelledby="partner-profile-tab">
                                            <div class="common-bgwhite-main mt-2 py-2 px-3 ps-lg-4 pe-1 pe-sm-3">
                                                <div
                                                    class="topcolleps-edtbar d-flex align-items-center justify-content-between py-lg-1">
                                                    <h4 class="fts-16 fw-7 white-color-n w-100">
                                                        {{ $memberPartnerSection['label'] }}</h4>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <a class="profile-arrow-collapse" data-bs-toggle="collapse"
                                                            href="#{{ $memberPartnerSection['section_key'] }}"
                                                            aria-controls="{{ $memberPartnerSection['section_key'] }}"
                                                            aria-expanded="true"><iconify-icon
                                                                icon="iconamoon:arrow-down-2"></iconify-icon></a>
                                                    </div>
                                                </div>
                                                <div class="collapse show"
                                                    id="{{ $memberPartnerSection['section_key'] }}">
                                                    <div class="">
                                                        <div class="row mt-2 px-1">
                                                            @foreach ($memberPartnerSection['fields'] as $fields)
                                                                <div class="col-md-4 col-6 mb-3 pb-md-1 px-2">
                                                                    <div class="fts-14 fw-5 white-color70-n">
                                                                        {{ $fields['label'] }}</div>
                                                                    <div class="fts-14 fw-5 white-color-n mt-1">
                                                                        {{ _displayNotAvailable($fields['value']) }}</div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- add view contact details modal popup  -->
    <div class="customsmallmodel_light couponsize modal fade" id="ContactViews" tabindex="-1"
        aria-labelledby="ContactViewsLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered contact-views-dialog">
            <div class="modal-content black-bgcolor1-n p-1 contact-views-content" id="contactModalContent">

            </div>
        </div>
    </div>

    <div class="customsmallmodel_light modal fade contact-confirm-modal" id="contactConfirmModal" tabindex="-1"
        aria-labelledby="contactConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered contact-views-dialog">
            <div class="modal-content contact-confirm-content" id="contactConfirmModalContent">
                <!-- AJAX CONTENT HERE -->
                <div class="modal-header mx-3 mx-lg-4 pt-4 pb-2 border-0">
                    <h2 class="fts-20 fw-7 white-color-n" id="contactConfirmModalLabel">
                        {{ __('messages.lbl_view_contact_details') }}
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
                    </button>
                </div>

                <div class="modal_liteBody contact-confirm-body">

                    @if ($hasViewedContact == 'No')
                        <div class="alert alert-message-components my-3 error fade show" role="alert">
                            <div class="alert-icon">
                                <iconify-icon icon="mdi:shield-alert"></iconify-icon>
                            </div>
                            <div class="alert-contents pe-3">
                                <h4 class="fts-16 fw-5">{{ __('messages.lbl_send_interest_to_view_contact_details') }}
                                </h4>
                                <p class="fts-13 fw-4 opacity-75">
                                    {{ __('messages.lbl_send_interest_to_view_contact_details_msg') }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="contact-confirm-icon-wrap">
                            <iconify-icon icon="ph:address-book-fill"></iconify-icon>
                        </div>

                        <p class="contact-confirm-remaining-text">
                            {{ __('messages.msg_you_have') }}
                            <strong class="contact-confirm-count"
                                id="remainingContactsCount">{{ $remainingContacts }}</strong>
                            {{ __('messages.lbl_contact_views_remaining') }}
                        </p>

                        @if ($remainingContacts > 0)
                            <p class="contact-confirm-desc" id="contactConfirmDescription">
                                {{ __('messages.msg_view_contact_confirmation_description') }}
                            </p>
                        @endif

                        <div class="alert contact-confirm-noleft-alert {{ $remainingContacts > 0 ? 'd-none' : '' }}"
                            id="noContactsLeftAlert">
                            <iconify-icon icon="ph:warning-bold" class="me-1"></iconify-icon>
                            {{ __('messages.msg_no_contact_views_left') }}
                            <a href="{{ route('web.membershipPlan.index') }}" class="fw-bold text-decoration-underline">
                                {{ __('messages.lbl_upgrade_plan') }}
                            </a>
                        </div>
                    @endif
                </div>

                <div class="modal-footer border-top-0 pt-0 justify-content-center gap-2">
                    <button type="button" class="contact-confirm-btn contact-confirm-btn-cancel"
                        data-bs-dismiss="modal">
                        {{ __('messages.lbl_cancel') }}
                    </button>

                    @if ($hasViewedContact != 'No')
                        <button type="button"
                            class="contact-confirm-btn contact-confirm-btn-primary d-flex justify-content-center align-items-center gap-1"
                            id="confirmViewContactBtn" {{ $remainingContacts <= 0 ? 'disabled' : '' }}>
                            <iconify-icon icon="ph:eye-bold"></iconify-icon>
                            {{ __('messages.lbl_confirm_view_contact') }}
                        </button>
                    @endif
                </div>
                <!-- AJAX CONTENT HERE -->
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/photoswipe@5/dist/photoswipe.css">

    <script type="module">
        document.getElementById('openGallery')?.addEventListener('click', function() {
            const firstLink = document.querySelector('#member-gallery a');
            if (firstLink) firstLink.click();
        });

        import PhotoSwipeLightbox from 'https://unpkg.com/photoswipe@5/dist/photoswipe-lightbox.esm.js';

        const lightbox = new PhotoSwipeLightbox({
            gallery: '#member-gallery',
            children: 'a',
            pswpModule: () => import('https://unpkg.com/photoswipe@5/dist/photoswipe.esm.js')
        });

        lightbox.init();
    </script>
    <script>
        // Holds the receiver whose contact is pending confirmation.
        let pendingContactReceiverId = null;

        // Small helper to avoid repeating the disable/spinner/restore pattern.
        function setButtonLoading($btn, isLoading, loadingLabel) {
            if (!$btn || !$btn.length) return;
            if (isLoading) {
                $btn.data('original-html', $btn.html());
                $btn.prop('disabled', true).html(`
            <iconify-icon icon="eos-icons:loading" width="20"></iconify-icon>
            ${loadingLabel}
        `);
            } else {
                let originalHtml = $btn.data('original-html');
                if (originalHtml) {
                    $btn.html(originalHtml);
                }
                $btn.prop('disabled', false);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 1 — View Contact button clicked
        |--------------------------------------------------------------------------
        |
        | data-viewed="1" → Contact was already viewed. Skip confirmation modal.
        | data-viewed="0" → First time viewing contact. Show confirmation modal.
        |
        */
        $(document).on('click', '.view-contact-btn', function() {
            let $btn = $(this);
            pendingContactReceiverId = $btn.attr('data-id');

            let alreadyViewed = $btn.attr('data-viewed') === '1';
            if (alreadyViewed) {
                fetchAndShowContact($btn);
                return;
            }

            $('#contactConfirmModal').modal('show');
        });

        /*
        |--------------------------------------------------------------------------
        | Shared Function — Fetch and Show Contact
        |--------------------------------------------------------------------------
        */
        function fetchAndShowContact($triggerBtn) {
            if (!pendingContactReceiverId) {
                return;
            }
            $.ajax({
                url: "{{ route('web.userProfile.viewContact') }}",
                type: "POST",
                data: {
                    _token: csrfToken,
                    receiver_member_id: pendingContactReceiverId
                },
                beforeSend: function() {
                    $('.view-contact-btn').prop('disabled', true);
                    if ($triggerBtn) {
                        setButtonLoading($triggerBtn, true, '{{ __('messages.lbl_please_wait') }}');
                    }
                },
                success: function(res) {
                    $('.view-contact-btn').prop('disabled', false);
                    if ($triggerBtn) {
                        setButtonLoading($triggerBtn, false);
                    }
                    if (res.status) {
                        $('#contactModalContent').html(res.html);
                        $('#ContactViews').modal('show');
                        showAlertMessage('success', res.message);
                    } else {
                        showToastMessage('error', res.message);
                    }
                },
                error: function() {
                    $('.view-contact-btn').prop('disabled', false);
                    if ($triggerBtn) {
                        setButtonLoading($triggerBtn, false);
                    }
                    showToastMessage('error', '{{ __('messages.msg_unexpected_error_occured') }}');
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2 — Confirm & View Contact
        |--------------------------------------------------------------------------
        | This is called only for the first contact view.
        */
        $('#confirmViewContactBtn').on('click', function() {
            if (!pendingContactReceiverId) {
                return;
            }
            let $btn = $(this);

            $.ajax({
                url: "{{ route('web.userProfile.viewContact') }}",
                type: "POST",
                data: {
                    _token: csrfToken,
                    receiver_member_id: pendingContactReceiverId
                },
                beforeSend: function() {
                    setButtonLoading($btn, true, '{{ __('messages.lbl_please_wait') }}');
                    $('.view-contact-btn').prop('disabled', true);
                },
                success: function(res) {
                    setButtonLoading($btn, false);
                    $('.view-contact-btn').prop('disabled', false);

                    /*
                    |--------------------------------------------------------------------------
                    | Failed Response
                    |--------------------------------------------------------------------------
                    */
                    if (!res.status) {
                        // Prefer a structured flag from the backend over sniffing the message text.
                        let noContactsLeft = res.error_code === 'NO_CONTACTS_LEFT' || res
                            .no_contacts_left === true;

                        if (noContactsLeft) {
                            $('#remainingContactsCount').text(0);
                            $('#contactConfirmDescription').remove();
                            $('#noContactsLeftAlert').removeClass('d-none');
                            $('#confirmViewContactBtn').prop('disabled', true);
                        } else {
                            $('#contactConfirmModal').modal('hide');
                        }

                        showToastMessage('error', res.message);
                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Success
                    |--------------------------------------------------------------------------
                    */
                    $('#contactConfirmModal').modal('hide');

                    $('#contactModalContent').html(res.html);
                    $('#ContactViews').modal('show');

                    showAlertMessage('success', res.message);

                    // Use attr() because the click handler also reads the value using attr().
                    $('.view-contact-btn[data-id="' + pendingContactReceiverId + '"]').attr(
                        'data-viewed', '1');

                    if (typeof res.remaining_contacts !== 'undefined') {
                        $('#remainingContactsCount').text(res.remaining_contacts);

                        if (res.remaining_contacts <= 0) {
                            $('#contactConfirmDescription').remove();
                            $('#noContactsLeftAlert').removeClass('d-none');
                            $('#confirmViewContactBtn').prop('disabled', true);
                        }
                    }
                },
                error: function(xhr) {
                    setButtonLoading($btn, false);
                    $('.view-contact-btn').prop('disabled', false);
                    $('#contactConfirmModal').modal('hide');

                    let message = '{{ __('messages.msg_unexpected_error_occured') }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToastMessage('error', message);
                }
            });
        });
    </script>
@endpush

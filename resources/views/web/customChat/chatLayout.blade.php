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
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, .12);
            margin-bottom: 2px;
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

        .chat-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }

        .chat-state-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 28px;
            gap: 12px;
        }

        .chat-state-avatar {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 0 0 3px #ECEBF5;
            margin-bottom: 4px;
        }

        .chat-state-avatar.grayscale {
            filter: grayscale(70%);
        }

        .chat-state-avatar.blocked {
            filter: grayscale(100%);
            opacity: .6;
        }

        .chat-state-title {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
        }

        .chat-state-sub {
            font-size: 13px;
            color: #6B7086;
            max-width: 320px;
            line-height: 1.5;
            margin: 0;
        }

        .chat-state-actions {
            display: flex;
            gap: 10px;
            margin-top: 6px;
        }

        .chat-pill {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .03em;
            padding: 5px 14px;
            border-radius: 999px;
        }

        .chat-pill-info {
            background: #EFEAFF;
            color: #6C5CE7;
        }

        .chat-pill-warn {
            background: #FFF6E5;
            color: #B8790A;
        }

        .chat-pill-danger {
            background: #FDECEC;
            color: #E5484D;
        }

        .chat-btn {
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

        .chat-btn-primary {
            background: var(--primary-color);
            color: #fff;
        }

        .chat-btn-muted {
            background: #F2F2F7;
            color: #1E2135;
        }

        .chat-btn-danger-soft {
            background: #FDECEC;
            color: #E5484D;
        }

        .chat-btn-success {
            background: #22B07D;
            color: #fff;
        }

        .chat-detail-footer.locked {
            opacity: .5;
            pointer-events: none;
        }

        .chat-header-action-btn.more-btn {
            background: #F2F2F7;
            color: #6B7086;
        }

        .chat-header-actions {
            position: relative;
        }

        .chat-more-menu {
            position: absolute;
            top: 46px;
            right: 0;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 12px 34px rgba(30, 33, 53, .16);
            border: 1px solid #ECEBF5;
            width: 220px;
            padding: 6px;
            display: none;
            flex-direction: column;
            z-index: 30;
        }

        .chat-more-menu.open {
            display: flex;
        }

        .chat-more-menu button {
            display: flex;
            align-items: center;
            gap: 10px;
            background: none;
            border: none;
            text-align: left;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            color: #1E2135;
            cursor: pointer;
        }

        .chat-more-menu button:hover {
            background: #F6F6FB;
        }

        .chat-more-menu button.danger {
            color: #E5484D;
        }

        .chat-more-menu button.success {
            color: #22B07D;
        }

        .chat-block-btn {
            display: block;
            width: 100%;
            padding: 0.5rem 1rem;

            clear: both;
            font-weight: 400;

            color: var(--bs-dropdown-link-color);
            text-align: center;
            text-decoration: none;
            white-space: nowrap;

            background-color: transparent;
            border: 0;

            cursor: pointer;
        }

        /* Icon alignment */
        .chat-block-btn iconify-icon {
            vertical-align: middle;
            margin-right: 0.4rem;
        }

        /* Hover / Active */
        .chat-block-btn:hover,
        .chat-block-btn:focus,
        .chat-block-btn.active,
        .chat-block-btn:active {
            background-color: #dddddd;
        }

        /* Header action buttons (call, video, more) */
        .chat-header-action-btn.circle-action-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.06);
            border: none;
            border-radius: 50%;
            color: #e4e6eb;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .chat-header-action-btn.circle-action-btn:hover,
        .chat-header-action-btn.circle-action-btn[aria-expanded="true"] {
            background: rgba(255, 255, 255, 0.14);
        }

        /* Dropdown wrapper needs relative positioning so the menu anchors correctly */
        .chat-header-dropdown {
            position: relative;
        }

        /* The dropdown panel itself */
        .chat-header-dropdown .common-dropdown-menu {
            margin-top: 10px;
            min-width: 148px;
            padding: 6px;
            background: #1f2430;
            /* solid dark background, not transparent */
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45);
            z-index: 1050;
        }

        .chat-header-dropdown .common-dropdown-menu li {
            list-style: none;
        }

        /* Block / Unblock buttons */
        .chat-block-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 6px;
            background: transparent;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-align: left;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .chat-block-btn iconify-icon {
            font-size: 18px;
            flex-shrink: 0;
        }

        .chat-block-btn.danger {
            color: #f04438;
        }

        .chat-block-btn.danger:hover {
            background: rgba(240, 68, 56, 0.12);
        }

        .chat-block-btn.success {
            color: #12b76a;
        }

        .chat-block-btn.success:hover {
            background: rgba(18, 183, 106, 0.12);
        }
    </style>
@endpush
@section('web_content')
    @php
        ## Login member profile :
        $profileImage = _getMemberProfileImage($authUser, 'Yes');
        ## Other Member profile
        $receiverProfileImage = _getMemberProfileImage($receiver);
        $canView = _canViewMemberPhoto($receiver, $receiver->hasPhotoRequestAccess);
        $hasPhoto = _checkPhotoExist($receiver);
        if (!$canView && $hasPhoto) {
            $receiverProfileImage = _getProtectedImage($receiver->gender);
        }
        $onlineStatus = _memberOnlineStatus($receiver);
    @endphp
    <!-- chat panel section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="row">
                    <div class="dashboard-layout">
                        {{-- LEFT SIDEBAR (Responsive Off-Canvas Drawer on Mobile) --}}
                        @include('web.dashboard.memberLeftSideBar')
                        <main class="main-content-area" id="main-content">
                            <div class="common-bgwhite-main p-3 p-lg-4">
                                <div class="inner_chatspanelmng">
                                    <div class="chat_headers-lites">
                                        <a href="javascript:void(0);" class="back-chatbtn close-conversation"
                                            title="{{ __('messages.lbl_back_to_chats') }}">
                                            <iconify-icon icon="famicons:play-skip-back-outline"></iconify-icon>
                                        </a>
                                        <div class="header-left_user d-flex align-items-center gap-2">
                                            <div class="user-dp-profiles">
                                                <a href="{{ route('web.userProfile.index', _encrypt($receiver->id)) }}">
                                                    <img src="{{ $receiverProfileImage }}"
                                                        alt="{{ _profileTitle($receiver) }}" class="prifile-dp">
                                                </a>
                                            </div>
                                            <div class="name-statususer ms-1">
                                                <a href="{{ route('web.userProfile.index', _encrypt($receiver->id)) }}">
                                                    <div class="username fts-18">{{ _profileTitle($receiver) }}
                                                    </div>
                                                </a>
                                                @php
                                                    $onlineStatus = _memberOnlineStatus($receiver);
                                                    $onlineClass =
                                                        $onlineStatus['status_code'] === 'online'
                                                            ? 'online'
                                                            : 'nodistrub';
                                                @endphp
                                                <div class="status-usersview {{ $onlineClass }}">
                                                    <span
                                                        class="fts-12 fw-5 white-color70-n">{{ $onlineStatus['status_text'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="header-rightcalled d-flex gap-2">
                                            @php
                                                $isPaid = $authUser->plan_status === 'Paid';
                                                $voiceApproved = $configArr['zego_voice_call_setting'] === 'APPROVED';
                                                $videoApproved = $configArr['zego_video_call_setting'] === 'APPROVED';
                                                $canVideoCall = $isPaid && $videoApproved && $canVideoCall; // rename later
                                                $canVoiceCall = $isPaid && $voiceApproved && $canVoiceCall;
                                                $canChat = $isPaid && $currentPlan->can_chat;
                                            @endphp
                                            @if ($voiceApproved)
                                                @if ($canVoiceCall)
                                                    <a href="{{ route('web.videoVoiceCall.inititeVoiceCall', _encrypt($receiver->id)) }}"
                                                        class="call-voice-video">
                                                        <iconify-icon icon="mdi:call"></iconify-icon>
                                                    </a>
                                                @else
                                                    <button class="call-voice-video" data-bs-toggle="modal"
                                                        data-bs-target="#upgradeMembershipPlan">
                                                        <iconify-icon icon="mdi:call"></iconify-icon>
                                                    </button>
                                                @endif
                                            @endif
                                            @if ($videoApproved)
                                                @if ($canVideoCall)
                                                    <a href="{{ route('web.videoVoiceCall.inititeVideoCall', _encrypt($receiver->id)) }}"
                                                        class="call-voice-video">
                                                        <iconify-icon icon="weui:video-call-filled"></iconify-icon>
                                                    </a>
                                                @else
                                                    <button class="call-voice-video" data-bs-toggle="modal"
                                                        data-bs-target="#upgradeMembershipPlan">
                                                        <iconify-icon icon="weui:video-call-filled"></iconify-icon>
                                                    </button>
                                                @endif
                                            @endif
                                            {{-- More Actions --}}
                                            <div class="dropdown chat-header-dropdown">
                                                <button class="chat-header-action-btn more-btn circle-action-btn btn-more"
                                                    id="btnHeaderMore" data-bs-toggle="dropdown" data-bs-display="static"
                                                    aria-expanded="false" title="More Actions">

                                                    <iconify-icon icon="ph:dots-three-vertical-bold"></iconify-icon>
                                                </button>

                                                <ul class="common-dropdown-menu dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="btnHeaderMore">
                                                    <li>
                                                        <button type="button" class="chat-block-btn danger"
                                                            id="blockMemberBtn"
                                                            style="{{ $chatState === 'blocked_by_me' ? 'display:none !important;' : '' }}">
                                                            <iconify-icon icon="ph:prohibit"></iconify-icon>
                                                            <span>{{ __('messages.lbl_block_profile') }}</span>
                                                        </button>
                                                        <button type="button" class="chat-block-btn success"
                                                            id="unblockMemberBtn"
                                                            style="{{ $chatState === 'blocked_by_me' ? '' : 'display:none !important;' }}">

                                                            <iconify-icon icon="ph:check-circle"></iconify-icon>

                                                            <span>{{ __('messages.lbl_unblock_profile') }}</span>
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            {{-- More Actions --}}
                                        </div>
                                    </div>
                                    <div class="chat-body" id="chatBodyContainer">

                                        {{-- 1. No request sent yet --}}
                                        <div class="chat-state-panel" id="stateNoRequest"
                                            style="{{ $chatState === 'no_request' ? '' : 'display:none;' }}">
                                            <img src="{{ $receiverProfileImage }}" class="chat-state-avatar"
                                                alt="">
                                            <h3 class="chat-state-title">
                                                {{ __('messages.lbl_start_a_conversation_with') }}
                                                {{ _profileTitle($receiver) }}
                                            </h3>
                                            <p class="chat-state-sub">
                                                {{ __('messages.msg_send_chat_request_description') }}
                                            </p>
                                            <button class="chat-btn chat-btn-primary" id="sendRequestBtn">
                                                <iconify-icon icon="ph:chat-circle-dots-fill"></iconify-icon>
                                                {{ __('messages.lbl_send_chat_request') }}
                                            </button>
                                        </div>

                                        {{-- 2. I sent a request, waiting --}}
                                        <div class="chat-state-panel" id="stateRequestSent"
                                            style="{{ $chatState === 'request_sent' ? '' : 'display:none;' }}">
                                            <img src="{{ $receiverProfileImage }}" class="chat-state-avatar"
                                                alt="">
                                            <span
                                                class="chat-pill chat-pill-info">{{ __('messages.lbl_request_sent') }}</span>
                                            <h3 class="chat-state-title">
                                                {{ __('messages.msg_waiting_for_member_to_respond', ['name' => _profileTitle($receiver)]) }}
                                            </h3>
                                            <p class="chat-state-sub">
                                                {{ __('messages.msg_you_can_cancel_this_request_anytime') }}</p>
                                            <button class="chat-btn chat-btn-muted" id="cancelRequestBtn">
                                                <iconify-icon icon="ph:x-bold"></iconify-icon>
                                                {{ __('messages.lbl_cancel_request') }}
                                            </button>
                                        </div>

                                        {{-- 3. Someone sent ME a request --}}
                                        <div class="chat-state-panel" id="stateRequestReceived"
                                            style="{{ $chatState === 'request_received' ? '' : 'display:none;' }}">
                                            <img src="{{ $receiverProfileImage }}" class="chat-state-avatar"
                                                alt="">
                                            <span
                                                class="chat-pill chat-pill-warn">{{ __('messages.lbl_new_chat_request') }}</span>
                                            <h3 class="chat-state-title">
                                                {{ __('messages.msg_member_wants_to_chat', ['name' => _profileTitle($receiver)]) }}
                                            </h3>
                                            <p class="chat-state-sub">
                                                {{ __('messages.msg_accept_or_reject_request_description') }}</p>
                                            <div class="chat-state-actions">
                                                <button class="chat-btn chat-btn-primary" id="acceptRequestBtn">
                                                    <iconify-icon icon="ph:check-bold"></iconify-icon>
                                                    {{ __('messages.lbl_accept') }}
                                                </button>
                                                <button class="chat-btn chat-btn-danger-soft" id="rejectRequestBtn">
                                                    <iconify-icon icon="ph:x-bold"></iconify-icon>
                                                    {{ __('messages.lbl_reject') }}
                                                </button>
                                            </div>
                                        </div>

                                        {{-- 4. Rejected --}}
                                        <div class="chat-state-panel" id="stateRejected"
                                            style="{{ in_array($chatState, ['rejected_by_me', 'rejected_by_other']) ? '' : 'display:none;' }}">
                                            <img src="{{ $receiverProfileImage }}" class="chat-state-avatar grayscale"
                                                alt="">
                                            <span
                                                class="chat-pill chat-pill-danger">{{ __('messages.lbl_chat_rejected') }}</span>
                                            <h3 class="chat-state-title" id="rejectedTitle">
                                                {{ __('messages.msg_this_chat_was_rejected') }}</h3>
                                            <p class="chat-state-sub">
                                                {{ __('messages.msg_send_new_request_to_reconnect') }}
                                            </p>
                                            <button class="chat-btn chat-btn-primary" id="reSendRequestBtn">
                                                <iconify-icon icon="ph:arrow-counter-clockwise-bold"></iconify-icon>
                                                {{ __('messages.lbl_send_chat_request') }}
                                            </button>
                                        </div>

                                        {{-- 5. I blocked them --}}
                                        <div class="chat-state-panel" id="stateBlockedByMe"
                                            style="{{ $chatState === 'blocked_by_me' ? '' : 'display:none;' }}">
                                            <img src="{{ $receiverProfileImage }}" class="chat-state-avatar blocked"
                                                alt="">
                                            <span
                                                class="chat-pill chat-pill-danger">{{ __('messages.lbl_blocked') }}</span>
                                            <h3 class="chat-state-title">
                                                {{ __('messages.msg_you_blocked_member', ['name' => _profileTitle($receiver)]) }}
                                            </h3>
                                            <p class="chat-state-sub">
                                                {{ __('messages.msg_blocked_member_description') }}</p>
                                            <button class="chat-btn chat-btn-success" id="unblockFromPanelBtn">
                                                <iconify-icon icon="ph:check-circle-fill"></iconify-icon>
                                                {{ __('messages.lbl_unblock') }} {{ _profileTitle($receiver) }}
                                            </button>
                                        </div>

                                        {{-- 6. They blocked me --}}
                                        <div class="chat-state-panel" id="stateBlockedByOther"
                                            style="{{ $chatState === 'blocked_by_other' ? '' : 'display:none;' }}">
                                            <img src="{{ $receiverProfileImage }}" class="chat-state-avatar grayscale"
                                                alt="">
                                            <span
                                                class="chat-pill chat-pill-danger">{{ __('messages.lbl_unavailable') }}</span>
                                            <h3 class="chat-state-title">{{ __('messages.msg_chat_unavailable') }}
                                            </h3>
                                            <p class="chat-state-sub">
                                                {{ __('messages.msg_cannot_message_this_member') }}</p>
                                        </div>

                                        {{-- 7. Accepted — normal chat --}}
                                        <div class="chats_middlesScreen-main" id="chatContainer"
                                            style="{{ $chatState === 'accepted' ? '' : 'display:none;' }}">
                                            {{-- Chat Messages --}}
                                        </div>
                                    </div>

                                    {{-- AI Chat Suggestions --}}
                                    {{-- <div class="ai-suggestions-bar" id="aiSuggestionsBar">
                                    <div class="ai-suggestions-label">
                                        <iconify-icon icon="mingcute:ai-fill"></iconify-icon>
                                        <span>{{ __('messages.lbl_ai_suggestions') }}</span>
                                    </div>
                                    <div class="ai-chips-row" id="aiChipsRow"></div>
                                </div> --}}
                                    {{-- AI Chat Suggestions --}}

                                    <div class="chats_bottomsmaindivs d-flex align-items-end gap-2 {{ $chatState === 'accepted' ? '' : 'locked' }}"
                                        id="chatDetailFooter">
                                        <div class="send-msgboxed-mn position-relative w-100">
                                            <input type="text" placeholder="{{ __('messages.lbl_type_a_message') }}"
                                                class="typing-msgdiv" id="textareaincrease"
                                                {{ $chatState === 'accepted' ? '' : 'disabled' }}>
                                            <button class="send-msgbtn"><iconify-icon
                                                    icon="iconamoon:send-light"></iconify-icon></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </main>
                    </div>
                </div>
            </div>
    </section>
@endsection

@push('scripts')
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>

    <script>
        let oldestId = null;
        let latestId = null;
        let isLoadingOld = false;

        let receiver_profile = @json($receiverProfileImage);
        let user_profile = @json($profileImage);

        window.conversationId = @json($conversation->id);
        window.currentUserId = @json($authUser->id);
        window.receiverId = @json($receiver->id);

        {!! $configArr['firebase_configuration'] !!}
        firebase.initializeApp(firebaseConfig);

        // ✅ THIS WILL WORK
        const db = firebase.database();

        $(document).ready(function() {
            loadMessages(window.conversationId);

            // Mark as seen (current user)
            let chatUrl = 'chat_conversations_users/' + window.conversationId + '/' + window.currentUserId;
            db.ref(chatUrl).update({
                chat_status: 2
            });

            // Listen receiver status
            let receiverUrl = 'chat_conversations_users/' + window.conversationId + '/' + window.receiverId;
            window.chatRefOtherUser = db.ref(receiverUrl);

            window.chatRefOtherUser.on('child_changed', chatStatusListener);

            window.receiverChatStatus = 0;

            window.chatRefOtherUser.once('value').then(snapshot => {
                let data = snapshot.val();
                window.receiverChatStatus = data?.chat_status ?? 0;
            });
        });

        function chatStatusListener(snapshot) {
            let data = snapshot.val();
            window.receiverChatStatus = data?.chat_status ?? 0;
            changeMsgStatus();
        }

        function changeMsgStatus() {
            if (window.receiverChatStatus == 2) {
                $('.msg-send-time iconify-icon')
                    .addClass('read-msg')
                    .attr('icon', 'line-md:check-all');
            } else if (window.receiverChatStatus == 1) {
                $('.msg-send-time iconify-icon')
                    .attr('icon', 'line-md:check-all');
            }
        }

        function formatTime(time) {
            if (!time) return '';
            let date = new Date(time);
            return date.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function scrollToBottom() {
            const el = document.getElementById('chatContainer');
            setTimeout(() => {
                el.scrollTop = el.scrollHeight;
            }, 100);
        }

        function loadMessages(conversationId) {

            if (oldestId === null) {
                $('#chatContainer').html(
                    '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>'
                );
            }

            let url = '/chat/messages/' + conversationId;

            if (oldestId !== null) {
                url += '?last_id=' + oldestId;
            }

            $.get(url, function(res) {

                if (oldestId === null) {
                    $('#chatContainer').empty();

                    if (!res.data || res.data.length === 0) {
                        $('#chatContainer').html(
                            `<div class="chatlayout-line fts-10 noConversationFound">
                            <span>{{ __('messages.lbl_no_conversation_found') }}</span>
                        </div>`
                        );
                        return;
                    }
                }

                res.data.forEach(msg => renderMessage(msg, true));

                oldestId = res.oldest_id;
                latestId = res.latest_id;

                // if (oldestId === null && res.data.length > 0) {
                scrollToBottom();
                // }
            });
        }

        $('#chatContainer').on('scroll', function() {
            if ($(this).scrollTop() === 0 && oldestId && !isLoadingOld) {
                isLoadingOld = true;
                loadOldMessages();
            }
        });

        function loadOldMessages() {
            $.get('/chat/messages/' + window.conversationId + '?last_id=' + oldestId, function(res) {

                let container = $('#chatContainer')[0];
                let oldHeight = container.scrollHeight;

                res.data.forEach(msg => {
                    renderMessage(msg, false, true);
                });

                oldestId = res.oldest_id;

                let newHeight = container.scrollHeight;
                container.scrollTop = newHeight - oldHeight;

                isLoadingOld = false;
            });
        }

        function renderMessage(msg, append = true, prepend = false) {

            const isSender = Number(msg.sender_member_id) === Number(window.currentUserId);
            const time = formatTime(msg.send_on);
            const chatType = Number(msg.type); // 0 = text, 1 = voice call, 2 = video call

            const activeMinutes = msg.active_call_minute > 0 ? `${msg.active_call_minute} min` : 'No Answer';

            let callTypeClass = '';
            if (msg.active_call_minute > 0) {
                callTypeClass = isSender ? 'outgoing-answered' : 'incoming-answered';
            } else {
                callTypeClass = isSender ? 'outgoing-missed' : 'incoming-missed';
            }

            let icon = 'mdi:check';
            let cls = '';

            if (Number(msg.chat_status) === 1) {
                icon = 'line-md:check-all';
            } else if (Number(msg.chat_status) === 2) {
                icon = 'line-md:check-all';
                cls = 'read-msg';
            }

            let contentHtml = '';

            let html = isSender ? `
                <div class="single-send-chat d-flex justify-content-end align-items-end gap-2 my-2">
                    <div class="send-msg-time">${time}</div>
                    <div class="send-chat-msg">
                        <p class="msg-send">${escapeHtml(msg.message)}</p>
                        <div class="msg-send-time sended" data-id="${msg.id}">
                            <iconify-icon class="${cls}" icon="${icon}"></iconify-icon>
                            <p>${time}</p>
                        </div>
                    </div>
                    <div class="send-chat-profile">
                        <img src="${user_profile}" class="send-profile">
                    </div>
                </div>
            ` : `
                <div class="single-received-chat d-flex align-items-end gap-2 my-2">
                    <div class="received-chat-profile">
                        <img src="${isSender ? user_profile : receiver_profile}" class="received-profile">
                    </div>
                    <div class="receive-chat-multie">
                        <div class="inner-receive-single-msg d-flex gap-2 my-1">
                            <div class="received-chat-msg">
                                <p class="msg-received">${escapeHtml(msg.message)}</p>
                            </div>
                            <div class="received-msg-time">${time}</div>
                        </div>
                    </div>
                </div>
            `;

            if (prepend) {
                $('#chatContainer').prepend(html);
            } else {
                $('#chatContainer').append(html);
            }
        }

        function escapeHtml(text) {
            return $('<div>').text(text).html();
        }

        $('#textareaincrease').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                $('.send-msgbtn').trigger('click');
            }
        });

        $('.send-msgbtn').click(function() {
            let message = $('#textareaincrease').val().trim();
            if (!message) return;
            let $btn = $(this);
            let container = $('#chatContainer')[0];
            let isAtBottom = (container.scrollHeight - container.scrollTop - container.clientHeight) <= 5;
            //  show loading state
            $btn.prop('disabled', true).html('<iconify-icon icon="codex:loader" class="fts-28"></iconify-icon>');
            $.ajax({
                url: '/chat/send',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    conversation_id: window.conversationId,
                    receiver_id: window.receiverId,
                    chat_status: window.receiverChatStatus,
                    message: message
                },
                success: function(response) {
                    if (response.status) {
                        $('#textareaincrease').val('');

                        if ($('.noConversationFound').length > 0) {
                            $('.noConversationFound').closest('div').remove();
                        }

                        renderMessage(response.data, true);
                        if (isAtBottom) {
                            scrollToBottom();
                        }
                    } else {
                        showToastMessage('error', response.message);
                    }
                },
                error: function(xhr) {
                    let message = '{{ __('messages.msg_unexpected_error_occured') }}';
                    if (xhr.responseJSON) {
                        // Custom error message from controller
                        if (xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        // Validation errors
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            message = Object.values(xhr.responseJSON.errors)
                                .flat()
                                .join('<br>');
                        }
                    }
                    showToastMessage('error', message);
                },
                complete: function() {
                    // always runs (success or error)
                    $btn.prop('disabled', false).html(
                        '<iconify-icon icon="ph:paper-plane-right-fill"></iconify-icon>');
                }
            });
        });

        // Hide Chat Box :
        $('.close-conversation').click(function() {
            // Mark messages as delivered
            var chatUrl = 'chat_conversations_users/' + window.conversationId + '/' + window.currentUserId;
            var chatRef = db.ref(chatUrl);
            chatRef.update({
                chat_status: 1
            });
            window.chatRefOtherUser.off('child_changed', chatStatusListener);
            window.conversationId = null;

            window.location.href = "{{ route('web.chat.index') }}";
        });

        let lastMsgUrl = 'chat_last_message/' + window.currentUserId + '/' + window.conversationId;
        window.chatLastMsgRef = db.ref(lastMsgUrl);

        window.chatLastMsgRef.on('value', function(snapshot) {
            const data = snapshot.val();
            if (!data) return;

            if (Number(data.sender_id) === Number(window.currentUserId)) return;

            const container = document.getElementById('chatContainer');
            const isAtBottom = (container.scrollHeight - container.scrollTop - container.clientHeight) <= 5;

            // console.log('data',data);

            renderMessage({
                id: data.message_id,
                sender_member_id: data.sender_id,
                message: data.message,
                send_on: new Date(),
                type: 0,
                chat_status: 0
            }, true);

            if (isAtBottom) scrollToBottom();
        });

        // New Code For Chat Request & Block Member For Chats : 
        /*
        |----------------------------------------------------------------
        | STATE PANEL SWITCHING
        |----------------------------------------------------------------
        */
        const statePanels = {
            no_request: '#stateNoRequest',
            request_sent: '#stateRequestSent',
            request_received: '#stateRequestReceived',
            rejected_by_me: '#stateRejected',
            rejected_by_other: '#stateRejected',
            blocked_by_me: '#stateBlockedByMe',
            blocked_by_other: '#stateBlockedByOther',
            accepted: '#chatContainer',
        };

        function showChatState(state) {
            window.chatState = state;

            Object.values(statePanels).forEach(sel => $(sel).hide());
            $(statePanels[state] || '#stateNoRequest').show();

            const footerLocked = state !== 'accepted';
            $('#chatDetailFooter').toggleClass('locked', footerLocked);
            $('#textareaincrease').prop('disabled', footerLocked);

            // header "more" menu items
            $('#rejectChatBtn').toggle(state === 'accepted');
            $('#blockMemberBtn').toggle(state !== 'blocked_by_me');
            $('#unblockMemberBtn').toggle(state === 'blocked_by_me');

            // call buttons only usable once accepted
            $('#videoCallBtn, #voiceCallBtn').toggleClass('disabled', state !== 'accepted')
                .prop('disabled', state !== 'accepted');

            if (state === 'accepted' && window.conversationId && oldestId === null) {
                initAcceptedChat();
            }
        }

        function initAcceptedChat() {
            loadMessages(window.conversationId);

            let chatUrl = 'chat_conversations_users/' + window.conversationId + '/' + window.currentUserId;
            db.ref(chatUrl).update({
                chat_status: 2
            });

            let receiverUrl = 'chat_conversations_users/' + window.conversationId + '/' + window.receiverId;
            window.chatRefOtherUser = db.ref(receiverUrl);
            window.chatRefOtherUser.on('child_changed', chatStatusListener);

            window.receiverChatStatus = 0;
            window.chatRefOtherUser.once('value').then(snapshot => {
                let data = snapshot.val();
                window.receiverChatStatus = data?.chat_status ?? 0;
            });
        }

        // Send a new (or re-send a previously rejected) chat request
        $('#sendRequestBtn, #reSendRequestBtn').click(function() {
            postChatAction("{{ route('web.chat.sendChatRequest') }}", {
                receiver_id: window.receiverId
            }, function(res) {
                showChatState(res.chat_state);
            });
        });

        // Cancel my own pending outgoing request
        $('#cancelRequestBtn').click(function() {
            postChatAction("{{ route('web.chat.cancelChatRequest') }}", {
                conversation_id: window.conversationId
            }, function(res) {
                window.conversationId = null;
                showChatState(res.chat_state);
            });
        });

        // Accept an incoming request
        $('#acceptRequestBtn').click(function() {
            postChatAction("{{ route('web.chat.respondChatRequest') }}", {
                conversation_id: window.conversationId,
                action: 'accept'
            }, function(res) {
                showChatState(res.chat_state);
            });
        });

        // Reject an incoming request
        $('#rejectRequestBtn').click(function() {
            postChatAction("{{ route('web.chat.respondChatRequest') }}", {
                conversation_id: window.conversationId,
                action: 'reject'
            }, function(res) {
                $('#rejectedTitle').text('{{ __('messages.msg_you_rejected_this_chat') }}');
                showChatState(res.chat_state);
            });
        });

        // Reject / end an already-accepted chat (from the header menu)
        $('#rejectChatBtn').click(function() {
            $('#chatMoreMenu').removeClass('open');
            postChatAction("{{ route('web.chat.respondChatRequest') }}", {
                conversation_id: window.conversationId,
                action: 'reject'
            }, function(res) {
                $('#rejectedTitle').text('{{ __('messages.msg_you_rejected_this_chat') }}');
                showChatState(res.chat_state);
            });
        });

        // Block / Unblock
        $('#blockMemberBtn').click(function() {
            $('#chatMoreMenu').removeClass('open');
            postChatAction("{{ route('web.chat.blockUnblockMember') }}", {
                receiver_id: window.receiverId,
                action: 'block'
            }, function(res) {
                showChatState(res.chat_state);
            });
        });

        $('#unblockMemberBtn, #unblockFromPanelBtn').click(function() {
            $('#chatMoreMenu').removeClass('open');
            postChatAction("{{ route('web.chat.blockUnblockMember') }}", {
                receiver_id: window.receiverId,
                action: 'unblock'
            }, function(res) {
                showChatState(res.chat_state);
            });
        });

        /*
        |----------------------------------------------------------------
        | CHAT REQUEST / BLOCK ACTIONS
        |----------------------------------------------------------------
        */
        function postChatAction(url, data, onSuccess) {
            $.ajax({
                url: url,
                type: 'POST',
                data: Object.assign({
                    _token: '{{ csrf_token() }}'
                }, data),
                success: function(response) {
                    if (response.status) {
                        if (response.conversation_id) {
                            window.conversationId = response.conversation_id;
                        }
                        if (response.message) {
                            showToastMessage('success', response.message);
                        }
                        onSuccess && onSuccess(response);
                    } else {
                        showToastMessage('error', response.message);
                    }
                },
                error: function(xhr) {
                    let message = '{{ __('messages.msg_unexpected_error_occured') }}';
                    if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToastMessage('error', message);
                }
            });
        }
    </script>
@endpush

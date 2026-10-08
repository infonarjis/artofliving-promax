<!-- pro matrimony chat system -->
<div class="pro-chat-container">
    <button class="pro-chats-btn">
        <iconify-icon id="proChatIcon" icon="proicons:chat"></iconify-icon>
        <div class="notification-count d-none">0</div>
    </button>
    <div class="chatlist-design-main pb-3">
        <div class="pro-chat-headerbox d-block">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <div class="user-dp-profiles-text">
                    <h4 class="fts-16 white-color-p">{{ __('messages.lbl_message_list') }}</h4>
                </div>
                <div class="video-audio-group">
                    <button class="chat-crosshide-btn chat-hide-box"><iconify-icon
                            icon="codex:cross"></iconify-icon></button>
                </div>
            </div>
            <div class="chatlist-searchbox position-relative mt-2">
                <iconify-icon icon="gg:search"></iconify-icon>
                <input type="search" name="" class="msg-search" id=""
                    placeholder="{{ __('messages.lbl_search_here') }}">
            </div>
            <div class="chat-tabs-design mt-3 d-flex gap-2 ">
                <button class="chat-tab-btn active"
                    data-tab="recent-chats">{{ __('messages.lbl_recent_chat') }}</button>
                <button class="chat-tab-btn" data-tab="online-members">{{ __('messages.lbl_online_members') }}</button>
            </div>
        </div>
        <div class="pro-chatlist-centerbar position-relative py-2 px-3">
            <div class="inner-chat-listview" id="chatListContainer">
                <div class="chat-daybox-line fts-10 mt-2"><span>{{ __('messages.lbl_no_conversation_found') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="chatlistview-design-main mobile-responsive">
        <div class="chatlist-box-pro position-relative">
            <div class="pro-chat-headerbox">
                <div class="user-dp-profiles-head d-flex align-items-center gap-2">
                    <img id="chatUserImage" src="" alt="user" class="user-dp">
                    <div class="user-dp-titles">
                        <a href="javascript:void(0)" id="chatUserUrlBtn" class="">
                            <h4 class="fts-15 white-color-p fw-5" id="chatUserName"></h4>
                        </a>
                        <p class="offline-user" id="chatUserStatus"></p>
                    </div>
                </div>
                <div class="video-audio-group d-flex gap-1">
                    @if ($configArr['zego_voice_call_setting'] == 'APPROVED')
                        <a href="javascript:void(0)" id="voiceCallBtn" class="chat-common-btn">
                            <iconify-icon icon="ic:sharp-call"></iconify-icon>
                        </a>
                    @endif

                    @if ($configArr['zego_video_call_setting'] == 'APPROVED')
                        <a href="javascript:void(0)" id="videoCallBtn" class="chat-common-btn">
                            <iconify-icon icon="weui:video-call-filled"></iconify-icon>
                        </a>
                    @endif
                    <button class="chat-common-btn chat-hide-box close-conversation">
                        <iconify-icon icon="qlementine-icons:close-16"></iconify-icon>
                    </button>
                    <div class="chat-btn-moreinfo">
                        <button class="btn-moreinfo" data-bs-toggle="dropdown" aria-expanded="false">
                            <iconify-icon icon="humbleicons:dots-vertical"></iconify-icon>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="javascript:void(0)" class="info-items reject-chat-action" id="rejectChatBtn"
                                    style="display:none;">
                                    <iconify-icon class="fts-16" icon="ph:x-circle"></iconify-icon>
                                    <span class="white-color-p">{{ __('messages.lbl_reject_chat') }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" class="info-items block-action-new" data-id=""
                                    data-blocked="0">
                                    <iconify-icon class="block-icon fts-16 chat-block-icon"
                                        icon="bx:shield"></iconify-icon>
                                    <span
                                        class="block-text chat-block-text white-color-p">{{ __('messages.lbl_block') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Chat body: holds the request/block state panels AND the message thread. --}}
            <div class="pro-chatbox-centerbar position-relative py-4 px-3" id="chatBodyContainer"
                style="background: url({{ asset('storage/web/') }}/assets/images/chat-bg.png), var(--black-color-1) no-repeat;">

                {{-- 1. No request sent yet --}}
                <div class="pro-chat-state-panel" id="stateNoRequest" style="display:none;">
                    <img src="" class="pro-chat-state-avatar" id="stateNoRequestAvatar" alt="">
                    <h4 class="pro-chat-state-title">{{ __('messages.lbl_send_chat_request') }}</h4>
                    <p class="pro-chat-state-sub">{{ __('messages.msg_send_chat_request_description') }}</p>
                    <button class="pro-chat-state-btn primary" id="sendRequestBtn">
                        <iconify-icon icon="ph:chat-circle-dots-fill"></iconify-icon>
                        {{ __('messages.lbl_send_chat_request') }}
                    </button>
                </div>

                {{-- 2. I sent a request, waiting --}}
                <div class="pro-chat-state-panel" id="stateRequestSent" style="display:none;">
                    <img src="" class="pro-chat-state-avatar" id="stateRequestSentAvatar" alt="">
                    <span class="pro-chat-pill info">{{ __('messages.lbl_request_sent') }}</span>
                    <h4 class="pro-chat-state-title">{{ __('messages.msg_you_can_cancel_this_request_anytime') }}</h4>
                    <button class="pro-chat-state-btn muted" id="cancelRequestBtn">
                        <iconify-icon icon="ph:x-bold"></iconify-icon>
                        {{ __('messages.lbl_cancel_request') }}
                    </button>
                </div>

                {{-- 3. Someone sent ME a request --}}
                <div class="pro-chat-state-panel" id="stateRequestReceived" style="display:none;">
                    <img src="" class="pro-chat-state-avatar" id="stateRequestReceivedAvatar"
                        alt="">
                    <span class="pro-chat-pill warn">{{ __('messages.lbl_new_chat_request') }}</span>
                    <h4 class="pro-chat-state-title">{{ __('messages.msg_accept_or_reject_request_description') }}
                    </h4>
                    <div class="pro-chat-state-actions">
                        <button class="pro-chat-state-btn primary" id="acceptRequestBtn">
                            <iconify-icon icon="ph:check-bold"></iconify-icon>
                            {{ __('messages.lbl_accept') }}
                        </button>
                        <button class="pro-chat-state-btn danger" id="rejectRequestBtn">
                            <iconify-icon icon="ph:x-bold"></iconify-icon>
                            {{ __('messages.lbl_reject') }}
                        </button>
                    </div>
                </div>

                {{-- 4. Rejected --}}
                <div class="pro-chat-state-panel" id="stateRejected" style="display:none;">
                    <img src="" class="pro-chat-state-avatar grayscale" id="stateRejectedAvatar"
                        alt="">
                    <span class="pro-chat-pill danger">{{ __('messages.lbl_chat_rejected') }}</span>
                    <h4 class="pro-chat-state-title" id="rejectedTitle">
                        {{ __('messages.msg_this_chat_was_rejected') }}</h4>
                    <p class="pro-chat-state-sub">{{ __('messages.msg_send_new_request_to_reconnect') }}</p>
                    <button class="pro-chat-state-btn primary" id="reSendRequestBtn">
                        <iconify-icon icon="ph:arrow-counter-clockwise-bold"></iconify-icon>
                        {{ __('messages.lbl_send_chat_request') }}
                    </button>
                </div>

                {{-- 5. I blocked them --}}
                <div class="pro-chat-state-panel" id="stateBlockedByMe" style="display:none;">
                    <img src="" class="pro-chat-state-avatar blocked" id="stateBlockedByMeAvatar"
                        alt="">
                    <span class="pro-chat-pill danger">{{ __('messages.lbl_blocked') }}</span>
                    <h4 class="pro-chat-state-title">{{ __('messages.msg_blocked_member_description') }}</h4>
                    <button class="pro-chat-state-btn success" id="unblockFromPanelBtn">
                        <iconify-icon icon="ph:check-circle-fill"></iconify-icon>
                        {{ __('messages.lbl_unblock') }}
                    </button>
                </div>

                {{-- 6. They blocked me --}}
                <div class="pro-chat-state-panel" id="stateBlockedByOther" style="display:none;">
                    <img src="" class="pro-chat-state-avatar grayscale" id="stateBlockedByOtherAvatar"
                        alt="">
                    <span class="pro-chat-pill danger">{{ __('messages.lbl_unavailable') }}</span>
                    <h4 class="pro-chat-state-title">{{ __('messages.msg_chat_unavailable') }}</h4>
                    <p class="pro-chat-state-sub">{{ __('messages.msg_cannot_message_this_member') }}</p>
                </div>

                {{-- 7. Accepted — normal message thread --}}
                <div id="chatContainer" style="display:none;"></div>
            </div>
            <div class="pro-bottom-chatbox px-3 black-bgcolor3-n py-2" id="chatDetailFooter">
                <div class="inner-chatbox-sent position-relative d-flex align-items-end gap-2">
                    <textarea name="" class="chat-type-msg" placeholder="{{ __('messages.lbl_enter_message') }}"
                        id="textareaincrease" disabled></textarea>
                    <button class="btn-send-msg" id="btnSendMsg" disabled><iconify-icon
                            icon="iconamoon:send-light"></iconify-icon></button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
    <script>
        window.userProfileRoute = @json(route('web.userProfile.index', 'ENCRYPT_ID'));
        window.voiceCallRoute = @json(route('web.videoVoiceCall.inititeVoiceCall', 'ENCRYPT_ID'));
        window.videoCallRoute = @json(route('web.videoVoiceCall.inititeVideoCall', 'ENCRYPT_ID'));

        {!! $configArr['firebase_configuration'] !!}
        firebase.initializeApp(firebaseConfig);

        // THIS WILL WORK
        const db = firebase.database();

        window.currentUserId = {{ auth()->guard('web')->id() }};
        window.chatState = 'no_request';
        let nextPageUrl = '/chat/list';

        function loadChatList(url = '/chat/list') {
            if (!url) return;

            $.get(url, function(res) {

                nextPageUrl = res.next_page;
                let html = '';
                if (res.data.length === 0) {
                    html =
                        `<div class="chat-daybox-line fts-10 mt-2"><span>{{ __('messages.lbl_no_conversation_found') }}</span></div>`;
                } else {
                    res.data.forEach(chat => {
                        let payload = encodeURIComponent(JSON.stringify({
                            conversationId: chat.conversation_id,
                            userId: chat.user_id,
                            userName: chat.user_name,
                            user_profile: chat.user_profile,
                            receiver_profile: chat.receiver_profile,
                            encryptedId: chat.encrypted_user_id,
                            online_status_text: chat.online_status_text ?? 'Offline',
                            online_status_code: chat.online_status_code ?? '',
                            chatState: chat.chat_state ?? 'no_request'
                        }));

                        // online member status:
                        let onlineStatusHtml = 'away-member';
                        if (chat.online_status_code === 'online') {
                            onlineStatusHtml = 'online-member';
                        } else if (chat.online_status_code === 'offline') {
                            onlineStatusHtml = 'offline-member';
                        }

                        html += `
                            <div class="single-chat-listbox chat-item" id="chatList${chat.conversation_id}" data-search="${(chat.user_name + ' ' + (chat.last_message ?? ''))
                                .toLowerCase()
                                .replace(/"/g,'')}"
                                onclick="openChat('${payload}')">
                                <div class="chat-list-singleinner d-flex">
                                    <div class="profile-list-content d-flex gap-2 w-100">
                                        <div class="chat-list-profiles ${onlineStatusHtml} position-relative">
                                            <img src="${chat.receiver_profile}" class="user-profile-img">
                                        </div>
                                        <div class="chat-list-contents">
                                            <h4>${chat.user_name}</h4>
                                            <p>${chat.last_message ?? ''}</p>
                                        </div>
                                    </div>
                                    <div class="last-msg-times-views text-end w-100">
                                        <p>${chat.last_time}</p>
                                        ${chat.unread_count > 0 ? `<div class="total-msg">${chat.unread_count}</div>` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                        // Mark messages as delivered
                        var chatUrl = 'chat_conversations_users/' + chat.conversation_id + '/' + window
                            .currentUserId;
                        var chatRef = db.ref(chatUrl);
                        chatRef.update({
                            chat_status: 1
                        });
                    });
                }

                $('#chatListContainer').html(html);

                updateNotificationCount(res.total_unread);
            });
        }

        function formatTime(time) {
            if (!time) return '';

            let date = new Date(time);
            return date.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        $('.inner-chat-listview').on('scroll', function() {
            let div = $(this)[0];

            if (div.scrollTop + div.clientHeight >= div.scrollHeight - 10) {
                loadChatList(nextPageUrl);
            }
        });

        let lastMessageId = null;

        function loadMessages(conversationId, receiver_profile, user_profile) {
            $('#chatContainer').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');

            $.get('/chat/messages/' + conversationId, function(res) {

                $('#chatContainer').html(
                    '<div class="chat-daybox-line fts-10"><span>{{ __('messages.lbl_no_conversation_found') }}</span></div>'
                );

                res.data.forEach(msg => {

                    let isSender = msg.sender_member_id == window.currentUserId;

                    let time = formatTime(msg.send_on);

                    let html = '';

                    if (isSender) {
                        // SEND MESSAGE (RIGHT)
                        html = `
                    <div class="single-send-chat d-flex justify-content-end align-items-end gap-2 my-2">
                        <div class="send-msg-time">${time}</div>
                        <div class="send-chat-msg">
                            <p class="msg-send">${msg.message}</p>
                            <div class="msg-send-time ${msg.is_read == 'Yes' ? 'viewed' : 'sended'}">
                                <iconify-icon class="${msg.chat_status == '2' ? 'read-msg' : ''}" icon="${msg.chat_status == '0' ? 'mdi:check' : 'line-md:check-all'}"></iconify-icon>
                                <p>${time}</p>
                            </div>
                        </div>
                        <div class="send-chat-profile">
                            <img src="${user_profile}" class="send-profile">
                        </div>
                    </div>
                `;
                    } else {
                        // RECEIVED MESSAGE (LEFT)
                        html = `
                    <div class="single-received-chat d-flex align-items-end gap-2 my-2">
                        <div class="received-chat-profile">
                            <img src="${receiver_profile}" class="received-profile">
                        </div>
                        <div class="receive-chat-multie">
                            <div class="inner-receive-single-msg d-flex gap-2 my-1">
                                <div class="received-chat-msg">
                                    <p class="msg-received">${msg.message}</p>
                                </div>
                                <div class="received-msg-time">${time}</div>
                            </div>
                        </div>
                    </div>
                `;
                    }

                    $('#chatContainer').append(html);
                });
                lastMessageId = res.oldest_id;

                scrollToBottom();
            });
        }

        $('#chatContainer').on('scroll', function() {

            if ($(this).scrollTop() === 0 && lastMessageId) {

                $.get('/chat/messages/' + window.conversationId + '?oldest_id=' + lastMessageId, function(res) {

                    let oldHeight = $('#chatContainer')[0].scrollHeight;

                    res.data.forEach(msg => {
                        prependMessage(msg, msg.sender_member_id == window.currentUserId);
                    });

                    lastMessageId = res.oldest_id;

                    // maintain scroll position
                    let newHeight = $('#chatContainer')[0].scrollHeight;
                    $('#chatContainer').scrollTop(newHeight - oldHeight);
                });
            }
        });

        function prependMessage(msg, isSender) {

            let time = formatTime(msg.send_on);

            let html = '';
            if (isSender) {
                // SEND MESSAGE (RIGHT)
                html = `
            <div class="single-send-chat d-flex justify-content-end align-items-end gap-2 my-2">
                <div class="send-msg-time">${time}</div>
                <div class="send-chat-msg">
                    <p class="msg-send">${msg.message}</p>
                    <div class="msg-send-time ${msg.is_read == 'Yes' ? 'viewed' : 'sended'}">
                        <iconify-icon class="${msg.chat_status == '2' ? 'read-msg' : ''}" icon="${msg.chat_status == '0' ? 'mdi:check' : 'line-md:check-all'}"></iconify-icon>
                        <p>${time}</p>
                    </div>
                </div>
                <div class="send-chat-profile">
                    <img src="${user_profile}" class="send-profile">
                </div>
            </div>
            `;
            } else {
                // RECEIVED MESSAGE (LEFT)
                html = `
            <div class="single-received-chat d-flex align-items-end gap-2 my-2">
                <div class="received-chat-profile">
                    <img src="${receiver_profile}" class="received-profile">
                </div>
                <div class="receive-chat-multie">
                    <div class="inner-receive-single-msg d-flex gap-2 my-1">
                        <div class="received-chat-msg">
                            <p class="msg-received">${msg.message}</p>
                        </div>
                        <div class="received-msg-time">${time}</div>
                    </div>
                </div>
            </div>
            `;
            }

            $('#chatContainer').prepend(html);
        }

        /*
        |----------------------------------------------------------------
        | CHAT REQUEST STATE PANEL SWITCHING
        |----------------------------------------------------------------
        */
        const proStatePanels = {
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
            state = state || 'no_request';

            window.chatState = state;

            Object.values(proStatePanels).forEach(sel => $(sel).hide());
            $(proStatePanels[state] || '#stateNoRequest').show();

            $('.pro-chat-state-avatar').attr(
                'src',
                window.receiver_profile || ''
            );

            const footerLocked = state !== 'accepted';

            $('#chatDetailFooter').toggleClass('locked', footerLocked);
            $('#textareaincrease, #btnSendMsg').prop('disabled', footerLocked);

            // Reject button only for accepted chat
            $('#rejectChatBtn').toggle(state === 'accepted');

            // Block button:
            // Show it unless the OTHER user has blocked me.
            $('.block-action-new').toggle(state !== 'blocked_by_other');

            // Call buttons only when chat is accepted
            $('#voiceCallBtn, #videoCallBtn')
                .toggleClass('disabled', state !== 'accepted');

            if (state === 'accepted' && window.conversationId) {
                loadMessages(
                    window.conversationId,
                    window.receiver_profile,
                    window.user_profile
                );

                let chatUrl =
                    'chat_conversations_users/' +
                    window.conversationId +
                    '/' +
                    window.currentUserId;

                db.ref(chatUrl).update({
                    chat_status: 2
                });

                let receiverUrl =
                    'chat_conversations_users/' +
                    window.conversationId +
                    '/' +
                    window.receiverId;

                if (window.chatRefOtherUser) {
                    window.chatRefOtherUser.off(
                        'child_changed',
                        chatStatusListener
                    );
                }

                window.chatRefOtherUser = db.ref(receiverUrl);

                window.chatRefOtherUser.on(
                    'child_changed',
                    chatStatusListener
                );

                window.receiverChatStatus = 0;

                window.chatRefOtherUser.once('value').then(snapshot => {
                    let data = snapshot.val();
                    window.receiverChatStatus = data?.chat_status ?? 0;
                });
            }
        }

        function postChatAction(url, data, onSuccess) {
            $.ajax({
                url: url,
                type: 'POST',
                data: Object.assign({
                    _token: $('meta[name="csrf-token"]').attr('content')
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

        // Send a new (or re-send a previously rejected) chat request
        $(document).on('click', '#sendRequestBtn, #reSendRequestBtn', function() {
            postChatAction("{{ route('web.chat.sendChatRequest') }}", {
                receiver_id: window.receiverId
            }, function(res) {
                showChatState(res.chat_state);
                loadChatList(activeTab === 'recent-chats' ? '/chat/list' : '/chat/online-members');
            });
        });

        // Cancel my own pending outgoing request
        $(document).on('click', '#cancelRequestBtn', function() {
            postChatAction("{{ route('web.chat.cancelChatRequest') }}", {
                conversation_id: window.conversationId
            }, function(res) {
                showChatState(res.chat_state);
            });
        });

        // Accept an incoming request
        $(document).on('click', '#acceptRequestBtn', function() {
            postChatAction("{{ route('web.chat.respondChatRequest') }}", {
                conversation_id: window.conversationId,
                action: 'accept'
            }, function(res) {
                showChatState(res.chat_state);
            });
        });

        // Reject an incoming request
        $(document).on('click', '#rejectRequestBtn', function() {
            postChatAction("{{ route('web.chat.respondChatRequest') }}", {
                conversation_id: window.conversationId,
                action: 'reject'
            }, function(res) {
                $('#rejectedTitle').text('{{ __('messages.msg_you_rejected_this_chat') }}');
                showChatState(res.chat_state);
            });
        });

        // Reject / end an already-accepted chat (from the header "more" menu)
        $(document).on('click', '#rejectChatBtn', function() {
            postChatAction("{{ route('web.chat.respondChatRequest') }}", {
                conversation_id: window.conversationId,
                action: 'reject'
            }, function(res) {
                $('#rejectedTitle').text('{{ __('messages.msg_you_rejected_this_chat') }}');
                showChatState(res.chat_state);
            });
        });

        // Block / Unblock (header "more" menu toggle + the blocked-state panel button)
        function updateBlockButtonUI(isBlocked) {
            $('.block-action-new')
                .data('blocked', isBlocked ? 1 : 0)
                .attr('data-blocked', isBlocked ? 1 : 0);
            $('.block-action-new .block-icon').attr('icon', isBlocked ? 'ph:check-circle' : 'bx:shield');
            $('.block-action-new .block-text').text(
                isBlocked ? '{{ __('messages.lbl_unblock_profile') }}' : '{{ __('messages.lbl_block') }}'
            );
        }

        $(document).on('click', '.block-action-new', function(e) {
            e.preventDefault();
            let isBlocked = $(this).data('blocked') == 1;
            postChatAction("{{ route('web.chat.blockUnblockMember') }}", {
                receiver_id: window.receiverId,
                action: isBlocked ? 'unblock' : 'block'
            }, function(res) {
                updateBlockButtonUI(!isBlocked);
                showChatState(res.chat_state);
            });
        });

        $(document).on('click', '#unblockFromPanelBtn', function() {
            postChatAction("{{ route('web.chat.blockUnblockMember') }}", {
                receiver_id: window.receiverId,
                action: 'unblock'
            }, function(res) {
                updateBlockButtonUI(false);
                showChatState(res.chat_state);
            });
        });

        function openChat(encoded) {

            let data = JSON.parse(decodeURIComponent(encoded));

            window.conversationId = data.conversationId;
            window.receiverId = data.userId;
            window.receiver_profile = data.receiver_profile;
            window.user_profile = data.user_profile;
            window.userName = data.userName;
            window.encryptedId = data.encryptedId;

            $('#chatUserName').text(data.userName);
            $('#chatUserImage').attr('src', data.receiver_profile);
            $('#chatList'+data.conversationId).find('.total-msg').remove();

            // online user class:
            if (data.online_status_code === 'online') {
                $('#chatUserStatus').text(data.online_status_text);
                $('#chatUserStatus').removeClass('offline-user');
                $('#chatUserStatus').addClass('online-user');
            } else {
                $('#chatUserStatus').text(data.online_status_text);
                $('#chatUserStatus').removeClass('online-user');
                $('#chatUserStatus').addClass('offline-user');
            }

            //  Profile URL
            let userProfileUrl = window.userProfileRoute.replace('ENCRYPT_ID', data.encryptedId);
            $('#chatUserUrlBtn').attr('href', userProfileUrl);

            // ✅ Call URLs
            let voiceUrl = window.voiceCallRoute.replace('ENCRYPT_ID', data.encryptedId);
            let videoUrl = window.videoCallRoute.replace('ENCRYPT_ID', data.encryptedId);
            $('#voiceCallBtn').attr('href', voiceUrl);
            $('#videoCallBtn').attr('href', videoUrl);

            // Block Profile:
            let chatState = data.chatState || 'no_request';
            $('.block-action-new.info-items').attr('data-id', window.receiverId);
            updateBlockButtonUI(chatState === 'blocked_by_me');

            // Render whichever panel matches the current request/block state.
            // (loadMessages / firebase read-receipt wiring happens inside showChatState
            // only when the state is 'accepted'.)
            showChatState(chatState);

            document.querySelector(".chatlistview-design-main").classList.add("active");
        }

        function startChatFromButton(receiverId) {

            $.post('/chat/create', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                receiver_id: receiverId
            }, function(res) {

                let payload = encodeURIComponent(JSON.stringify({
                    conversationId: res.conversation_id,
                    userId: res.user_id,
                    userName: res.user_name,
                    user_profile: res.user_profile,
                    receiver_profile: res.receiver_profile,
                    encryptedId: res.encrypted_user_id,
                    online_status_text: res.online_status_text,
                    online_status_code: res.online_status_code,
                    chatState: res.chat_state || 'no_request'
                }));

                openChat(payload); // SAME FUNCTION
            });
        }

        // store callback separately
        function chatStatusListener(snapshot) {
            let data = snapshot.val();
            window.receiverChatStatus = data;
            changeMsgStatus();
        }

        function changeMsgStatus() {
            if (window.receiverChatStatus == 2) {
                $('.msg-send-time iconify-icon').addClass('read-msg').attr('icon', 'line-md:check-all');
            } else if (window.receiverChatStatus == 1) {
                $('.msg-send-time iconify-icon').attr('icon', 'line-md:check-all');
            }
        }

        $('.btn-send-msg').click(function() {

            if (window.chatState !== 'accepted') return;

            let message = $('.chat-type-msg').val();
            // ✅ show loading state
            let $btn = $(this);
            $btn.prop('disabled', true).html('<iconify-icon icon="codex:loader"></iconify-icon>');

            if (!message.trim()) {
                $btn.prop('disabled', false).html('<iconify-icon icon="iconamoon:send-light"></iconify-icon>');
                return;
            }
            $.post('/chat/send', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                conversation_id: window.conversationId,
                receiver_id: window.receiverId,
                chat_status: window.receiverChatStatus,
                message: message
            }, function(res) {
                $('.chat-type-msg').val('');
                // always runs (success or error)
                $btn.prop('disabled', false)
                    .html('<iconify-icon icon="iconamoon:send-light"></iconify-icon>');

                if (res.status) {
                    // instantly show message
                    appendNewMessage(res.data, true);
                } else {
                    showToastMessage('error', res.message);
                    // server says the request isn't accepted (e.g. rejected/blocked
                    // in another tab) — resync the panel instead of trusting stale UI
                    if (res.chat_state) {
                        showChatState(res.chat_state);
                    }
                }
            });
        });

        function scrollToBottom() {
            let container = document.getElementById('chatContainer');
            container.scrollTop = container.scrollHeight;
        }

        function appendNewMessage(msg, isSender = false) {

            let time = formatTime(msg.send_on);

            let html = '';
            if (isSender) {
                html = `
            <div class="single-send-chat d-flex justify-content-end align-items-end gap-2 my-2">
                <div class="send-msg-time">${time}</div>
                <div class="send-chat-msg">
                    <p class="msg-send">${msg.message}</p>
                    <div class="msg-send-time sended">
                        <iconify-icon class="${window.receiverChatStatus == '2' ? 'read-msg' : ''}" icon="${window.receiverChatStatus == '0' ? 'mdi:check' : 'line-md:check-all'}"></iconify-icon>
                        <p>${time}</p>
                    </div>
                </div>
                <div class="send-chat-profile">
                    <img src="${user_profile}" class="send-profile">
                </div>
            </div>
            `;
            } else {
                html = `
            <div class="single-received-chat d-flex align-items-end gap-2 my-2">
                <div class="received-chat-profile">
                    <img src="${receiver_profile}" class="received-profile">
                </div>
                <div class="receive-chat-multie">
                    <div class="inner-receive-single-msg d-flex gap-2 my-1">
                        <div class="received-chat-msg">
                            <p class="msg-received">${msg.message}</p>
                        </div>
                        <div class="received-msg-time">${time}</div>
                    </div>
                </div>
            </div>
            `;
            }

            $('#chatContainer').append(html);
            scrollToBottom();
        }

        // function listenFirebase(isSkipped = 0, conversationId = null) {
        //     var chatUrl = 'chat/' + window.currentUserId;
        //     var chatRef = db.ref(chatUrl);
        //     chatRef.on('child_added', function(snapshot) {
        //         if (isSkipped == 1) {
        //             isSkipped = 0;
        //             return;
        //         }
        //         let data = snapshot.val();

        //         // 🔥 If chat window open
        //         if (window.conversationId == data.conversation_id) {

        //             // load only new message
        //             loadNewMessage(data);

        //         } else {

        //             // 🔥 Chat closed → refresh list + show notification
        //             loadChatList();
        //             showNotification("New");
        //         }

        //     });
        // }

        function listenFirebase(isSkipped = 0, conversationId = null) {
            let chatUrl = 'chat_last_message/' + window.currentUserId;
            let chatRef = db.ref(chatUrl);
            chatRef.on('value', function(snapshot) {
                if (isSkipped == 1) {
                    isSkipped = 0;
                    return;
                }
                let data = snapshot.val();
                if (data && Object.keys(data).length > 0) {

                    let chatClosed = false;

                    $.each(data, function (key, message) {

                        if (String(window.conversationId) === String(message.conversation_id)) {

                            // Current chat is open → Load message and break loop
                            loadNewMessage(message);

                            return false; // Break loop

                        } else {

                            chatClosed = true;

                        }

                    });

                    // Refresh list only if no message belongs to the open conversation
                    if (chatClosed && !Object.values(data).some(
                        message => String(window.conversationId) === String(message.conversation_id)
                    )) {
                        loadChatList();
                        showNotification("New");
                    }
                }
            });
        }

        function loadNewMessage(data) {
            let time = formatTime(data.time);
            let html = `
            <div class="single-received-chat d-flex align-items-end gap-2 my-2">
                <div class="received-chat-profile">
                    <img src="${window.receiver_profile}" class="received-profile">
                </div>
                <div class="receive-chat-multie">
                    <div class="inner-receive-single-msg d-flex gap-2 my-1">
                        <div class="received-chat-msg">
                            <p class="msg-received">${data.message}</p>
                        </div>
                        <div class="received-msg-time">${data.time}</div>
                    </div>
                </div>
            </div>
            `;

            $('#chatContainer').append(html);
            $('#chatList'+data.conversation_id).find('.chat-list-contents').find('p').text(data.message);
            scrollToBottom();
        }

        function showNotification(text) {
            $('.notification-count').show().text('New');

            // optional sound
            // let audio = new Audio('/notification.mp3');
            // audio.play();
        }

        function updateNotificationCount(count) {
            let badge = $('.notification-count');
            if (count > 0) {
                badge.removeClass('d-none').text(count);
            } else {
                badge.addClass('d-none').text('');
            }
        }

        // Hide Chat Box :
        $('.close-conversation').click(function() {
            // Mark messages as delivered
            var chatUrl = 'chat_conversations_users/' + window.conversationId + '/' + window.currentUserId;
            var chatRef = db.ref(chatUrl);
            chatRef.update({
                chat_status: 1
            });
            if (window.chatRefOtherUser) {
                window.chatRefOtherUser.off('child_changed', chatStatusListener);
            }
            window.conversationId = null;
        });

        // Search chats while typing
        $(document).on('input', '.msg-search', function() {
            let q = $(this).val().toLowerCase().trim();
            $('.chat-item').each(function() {
                let text = $(this).data('search');
                if (!q || text.indexOf(q) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Initial load
        loadChatList();
        listenFirebase(1);

        let activeTab = 'recent-chats';

        $('.chat-tab-btn').click(function() {
            $('.chat-tab-btn').removeClass('active');
            $(this).addClass('active');
            activeTab = $(this).data('tab');

            $('#chatListContainer').html(
                '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
            if (activeTab === 'recent-chats') {
                loadChatList('/chat/list');
            } else {
                loadChatList('/chat/online-members');
            }
        });
    </script>
@endpush

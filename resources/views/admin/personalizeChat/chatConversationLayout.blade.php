<div class="inner_chatspanelmng">
    <div class="chat_headers-lites">
        <div class="header-left_user d-flex align-items-center gap-2">
            <div class="user-dp-profiles">
                <img src="{{ $returnDataArr['profileImageReceiver'] }}" alt="{{ $returnDataArr['receiver_matri_id'] }}"
                    class="prifile-dp">
            </div>
            <div class="name-statususer ms-1">
                <div class="username">{{ $returnDataArr['receiver_matri_id'] }}</div>

                @php
                    $onlineStatus = $returnDataArr['onlineStatus'];
                    $onlineClass = $onlineStatus['status_code'] === 'online' ? 'online' : 'offline';
                @endphp
                <div class="status-usersview {{ $onlineClass }}">
                    <span class="fts-12 fw-5 white-color70-n">{{ $onlineStatus['status_text'] }}</span>
                </div>
            </div>
        </div>
        <div class="header-right">
            <button class="btn_chat_refresh"><i class='bx bx-refresh'></i></button>
        </div>
    </div>
    <div class="chats_middlesScreen-main" id="adminChatDiv" data-member_id="{{ $returnDataArr['receiver_member_id'] }}"
        data-page="2" data-is-more="1">
        <div id="messageListAdmin">
            @if (isset($returnDataArr['getMessageList']) && !empty($returnDataArr['getMessageList']))
                <button class="loader_chat__more d-none"><i class='bx bx-refresh bx-spin'></i> loading..</button>
                @foreach ($returnDataArr['getMessageList'] as $valueArr)
                    @if ($valueArr->sender_type == 2)
                        <div class="receive_messages-div my-md-3 my-2 d-flex gap-2 gap-lg-3 align-items-start">
                            <div class="receive-messages">
                                <div class="receive-single-msg">
                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ $valueArr->message }}
                                    @endif
                                    <span
                                        class="receive_msg_time">{{ _displayDate($valueArr->created_at, 'j M, y g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($valueArr->sender_type == 1)
                        <div
                            class="send_messages-div my-md-3 my-2 d-flex align-items-sm-start justify-content-end gap-2 gap-lg-3">
                            <div class="send-messages">
                                <div class="send-single-msg">
                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ $valueArr->message }}
                                    @endif
                                    <span
                                        class="send_msg_time">{{ _displayDate($valueArr->created_at, 'j M, y g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="alert alert-info text-center mt-3" role="alert">
                    Start Your Conversation
                </div>
            @endif
        </div>
    </div>
    <div class="chats_bottomsmaindivs d-flex align-items-end gap-2">
        <form id="sendMessageForm" name="sendMessageForm" class="d-flex align-items-end gap-2 w-100"
            action="{{ route('admin.personalizeChat.sendMessage') }}" method="POST">
            @csrf
            <div class="send-msgboxed-mn position-relative w-100">
                <textarea name="message" id="textareaincrease" class="typing-msgdiv" placeholder="Type a message"></textarea>
                <input type="hidden" name="receiver_member_id" value="{{ $returnDataArr['receiver_member_id'] }}">
                <button class="send-msgbtn" id="sendMessage">
                    <i class='bx bx-send'></i>
                </button>
            </div>
        </form>
    </div>
</div>

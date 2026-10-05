@foreach ($getRecentMember as $recentMemberValue)
    @php
        ## Member Profile Image:
        $profileImage = _getMemberProfileImage($recentMemberValue->member, 'yes');
    @endphp
    <div class="single_chatlist createConversation" id="createConversation{{ $recentMemberValue->member_id }}"
        member-id="{{ $recentMemberValue->member_id }}" member-matriId="{{ $recentMemberValue->matri_id }}">
        <div class="left_chatUserimg position-relative">
            <img src="{{ $profileImage }}" alt="Profile Image" class="users-dp" loading="lazy">
            @php
                $onlineStatus = _memberOnlineStatus($recentMemberValue->member);
                $onlineClass = $onlineStatus['status_code'] === 'online' ? 'online' : 'offline';
            @endphp
            <div class="{{ $onlineClass }}-status"></div>
        </div>
        <div class="right_chats--textlist d-flex gap-2 justify-content-between w-100">
            <div class="user_chat__descript pe-1">
                <div class="user__name">{{ $recentMemberValue->matri_id }}</div>
                @php
                    $lastMessage = Str::limit($recentMemberValue->last_message, 18);
                @endphp
                @if (!blank($lastMessage))
                    <div class="user_msg_Descript">
                        @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                            {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                        @else
                            {{ _displayNotAvailable(strip_tags($lastMessage)) }}
                        @endif
                    </div>
                @else
                    <div class="user_msg_Descript">Star Conversation</div>
                @endif
            </div>
            <div class="user_time_msg text-center">
                <div class="msg-times">{{ _displayDate($recentMemberValue->last_message_time, 'j M, y h:i A') }}</div>
                @if ($recentMemberValue->admin_unread_count > 0)
                    <div class="msg_count" id="unreadMsgCount{{ $recentMemberValue->member_id }}">
                        {{ $recentMemberValue->admin_unread_count }}</div>
                @endif
            </div>
        </div>
    </div>
@endforeach

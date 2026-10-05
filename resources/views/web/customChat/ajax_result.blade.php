<div class="chats-listings-box pe-2">
    @forelse($data as $value)
        @php
            $hasUnread = ($value['unread_count'] ?? 0) > 0;
        @endphp
        <a href="{{ isset($value['encrypted_user_id']) ? route('web.chat.chatConversation', $value['encrypted_user_id']) : 'javascript:void(0)' }}">
            <div class="single-chats-divmain mb-2">
                <div class="chat-flexsingles d-flex justify-content-between {{ $hasUnread ? 'unread' : '' }}">
                    <div class="chat-leftimgtexts d-flex gap-3 w-100">
                        @php
                            $onlineClass = 'away-member';
                            if ($value['online_status_code'] === 'online') {
                                $onlineClass = 'online-member';
                            } elseif ($value['online_status_code'] === 'offline') {
                                $onlineClass = 'offline-member';
                            }
                        @endphp
                        <div class="user-chatprofiles {{ $onlineClass }}">
                            <img src="{{ $value['receiver_profile'] }}" alt="{{ $value['user_name'] }}" class="chatuser">
                        </div>
                        <div class="chat-previewcontents mt-1">
                            <h4 class="fts-16 fw-6 white-color-n">{{ e($value['user_name']) }}</h4>
                            <p class="fts-14 fw-4 white-color70-n">{{ $value['last_message'] }}</p>
                        </div>
                    </div>
                    <div class="last-msg-times-views text-end w-100">
                        <div class="fts-14 white-color-n fw-4">{{ $value['last_time'] }}</div>
                        @if ($value['unread_count'] > 0)
                            <div class="total-msg">{{ $value['unread_count'] }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </a>
    @empty
        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.noDataFound', [
            'message' => __('messages.lbl_no_data_found'),
        ])
    @endforelse
</div>

<div class="chats-listings-box pe-2">
    @forelse($data as $value)
        <div class="single-chats-divmain mb-2">
            <div class="chat-flexsingles d-flex justify-content-between">
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
                        <p class="fts-14 fw-4 white-color70-n">{{ $value['user_subtitle'] }}</p>
                    </div>
                </div>
                <div class="last-msg-times-views text-end w-100">
                    <a href="{{ route('web.chat.chatConversation', $value['encrypted_user_id']) }}"
                        class="btn btn-chat-now rounded-pill px-4 py-2">
                        <iconify-icon icon="ph:chat-circle-dots-bold" class="align-middle me-1"
                            style="font-size: 16px;"></iconify-icon> {{ __('messages.lbl_chat_now') }}
                    </a>
                </div>
            </div>
        </div>
    @empty
        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.noDataFound', [
            'message' => __('messages.lbl_no_data_found'),
        ])
    @endforelse
</div>

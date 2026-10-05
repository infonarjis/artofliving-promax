@forelse ($messages as $item)
    @if ($item->sender_type == 1)
        <div class="receive_messages-div my-md-2 my-1 d-flex gap-2 gap-lg-3 align-items-start">
            <div class="receive-user-dp">
                <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_favicon'] }}" alt=""
                    class="reciveuser-profile">
            </div>
            <div class="receive-messages">
                <div class="receive-single-msg">{{ $item->message }}</div>
                <div class="receive-msg-timing">{{ $item->created_at->format('d M, Y H:i') }}</div>
            </div>
        </div>
    @else
        <div class="send_messages-div my-md-2 my-1 d-flex align-items-sm-start justify-content-end gap-2 gap-lg-3">
            <div class="send-messages">
                <div class="send-single-msg">
                    {{ $item->message }}
                </div>
                <div class="send-msg-timing">{{ $item->created_at->format('d M, Y H:i') }}</div>
            </div>
            <div class="send-user-dp">
                @php
                    $profileImage = _getMemberProfileImage($authUser, 'Yes');
                @endphp
                <img src="{{ $profileImage }}" alt="" class="senduser-profile">
            </div>
        </div>
    @endif
@empty
    <p class="text-center opacity-50">{{ __('messages.lbl_select_user_to_start_chatting') }}</p>
@endforelse

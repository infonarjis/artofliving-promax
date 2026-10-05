@forelse($resultData as $item)
    @php
        $isSent = $item->isSent;

        $user = $isSent ? $item->receiver : $item->sender;

        $canView = _canViewMemberPhoto($user, $item->hasPhotoRequestAccess);
        $hasPhoto = _checkPhotoExist($user);
        $profileImage = _getMemberProfileImage($user);

        $isMissed = $item->end_reason === 'Canceled';
    @endphp
    <div class="single-chats-divmain mb-2">
        <div class="call-history-box d-flex align-items-center justify-content-between">
            <div class="chat-leftimgtexts d-flex gap-3 w-100">
                @if (!$canView && $hasPhoto)
                    <a href="javascript:void(0)" class="open-photo-request-modal" data-receiver-id="{{ $user->id }}">
                        <div class="user-chatprofiles lockprofiles-1">
                            <img src="{{ _getProtectedImage($user->gender) }}" alt="{{ _profileTitle($user) }}"
                                class="chatuser">
                        </div>
                    </a>
                @else
                    <a href="{{ route('web.userProfile.index', _encrypt($user->id)) }}">
                        <div class="user-chatprofiles lockprofiles-1">
                            <img src="{{ $profileImage }}" alt="{{ _profileTitle($user) }}" class="chatuser">
                        </div>
                    </a>
                @endif
                <div class="chat-previewcontents mt-1">
                    <a href="{{ route('web.userProfile.index', _encrypt($user->id)) }}">
                        <h4 class="fts-16 fw-6 white-color-n">{{ _profileTitle($user) }}</h4>
                    </a>
                    <p class="fts-13 fw-4 white-color70-n">
                        {{ _displayDate($item->created_at, 'd M, h:i A') }}
                    </p>

                    @php
                        $isSent = $item->isSent;
                    @endphp

                    @if ($isSent && $isMissed)
                        {{-- Outgoing + missed/canceled --}}
                        <div class="ouotgoin-miss-call fts-18 text-danger">
                            <iconify-icon icon="mdi:phone-outgoing"></iconify-icon>
                        </div>
                    @elseif ($isSent && !$isMissed)
                        {{-- Outgoing + answered --}}
                        <div class="ouotgoin-call fts-18 text-success">
                            <iconify-icon icon="mdi:phone-outgoing"></iconify-icon>
                        </div>
                    @elseif (!$isSent && $isMissed)
                        {{-- Incoming + missed --}}
                        <div class="incommin-miss-call fts-18 text-danger">
                            <iconify-icon icon="mdi:phone-incoming"></iconify-icon>
                        </div>
                    @else
                        {{-- Incoming + answered --}}
                        <div class="incommin-call fts-18 text-success">
                            <iconify-icon icon="mdi:phone-incoming"></iconify-icon>
                        </div>
                    @endif

                </div>
            </div>
            <div class="calling-right-icon">
                @php
                    $isPaid = $authUser->plan_status === 'Paid';
                    $voiceApproved = $configArr['zego_voice_call_setting'] === 'APPROVED';
                    $videoApproved = $configArr['zego_video_call_setting'] === 'APPROVED';
                    $canVideoCall = $isPaid && $videoApproved && $canVideoCall; // rename later
                    $canVoiceCall = $isPaid && $voiceApproved && $canVoiceCall;
                    $canChat = $isPaid && $currentPlan->can_chat;

                    $receiverId = $user->id !== $authUser->id ? $user->id : $item->sender->id;
                @endphp
                @if ($item->type == 'videoCall')
                    @if ($canVoiceCall)
                        <a href="{{ route('web.videoVoiceCall.inititeVideoCall', _encrypt($receiverId)) }}" class="btn-history-call-video" title="Video Call">
                            <iconify-icon icon="material-symbols:videocam-rounded"></iconify-icon>
                        </a>
                    @else
                        <button class="btn-history-call-video" data-bs-toggle="modal"
                            data-bs-target="#upgradeMembershipPlan" title="Video Call">
                            <iconify-icon icon="material-symbols:videocam-rounded"></iconify-icon>
                        </button>
                    @endif
                @else
                    @if ($canVideoCall)
                        <a href="{{ route('web.videoVoiceCall.inititeVoiceCall', _encrypt($receiverId)) }}" class="btn-history-call-video" title="Voice">
                            <iconify-icon icon="ic:sharp-call"></iconify-icon>
                        </a>
                    @else
                        <button class="btn-history-call-video" data-bs-toggle="modal"
                            data-bs-target="#upgradeMembershipPlan" title="Voice Call">
                            <iconify-icon icon="ic:sharp-call"></iconify-icon>
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </div>
@empty
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.noDataFound', [
        'message' => __('messages.lbl_no_call_history_found'),
    ])
@endforelse

<!-- pagination  -->
@if ($resultData->hasPages())
    {{ $resultData->links(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.pagination') }}
@endif

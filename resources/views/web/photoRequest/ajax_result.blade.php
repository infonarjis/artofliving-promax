<h2 class="fts-18 fw-7 white-color-n mb-1">
    @if($type == 'request_sent')
        {{ __('messages.lbl_photo_request_sent') }}
    @else
        {{ __('messages.lbl_photo_request_received') }}
    @endif
</h2>
<p class="fts-14 white-color-n mb-3">
    @if($type == 'request_sent')
        {{ __('messages.lbl_photo_request_sent_subtitle') }}
    @else
        {{ __('messages.lbl_photo_request_received_subtitle') }}
    @endif
</p>

<!-- Profiles Grid -->
<div class="row g-3">
    @forelse($resultData as $item)
        @php
            $user = $type == 'request_sent' ? $item->receiver : $item->sender;

            $canView = _canViewMemberPhoto($user, $item->hasPhotoRequestAccess);
            $hasPhoto = _checkPhotoExist($user);
            $profileImage = _getMemberProfileImage($user);
        @endphp
        @if ($type == 'request_sent')
            <div class="request-intrest-box py-3 request-card">
                <div class="request-intrest-top position-relative">
                    <div class="profile-request-intrest">
                        @if (!$canView && $hasPhoto)
                            <a href="javascript:void(0)" class="open-photo-request-modal" data-receiver-id="{{ $user->id }}">
                                <div class="inner-request-profile position-relative">
                                    <img src="{{ _getProtectedImage($user->gender) }}" alt="{{ _profileTitle($user) }}"
                                        class="profile-request-img">
                                </div>
                            </a>
                        @else
                            <a href="{{ route('web.userProfile.index', _encrypt($user->id)) }}">
                                <div class="inner-request-profile position-relative">
                                    <img src="{{ $profileImage }}" alt="{{ _profileTitle($user) }}"
                                        class="profile-request-img">
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="request-intrest-bottom w-100 position-relative mt-2 ">
                    <a href="{{ route('web.userProfile.index',_encrypt($user->id)) }}">
                        <h4 class="fts-16 fw-6 white-color-n mb-2">{{ _profileTitle($user) }}</h4>
                    </a>
                    <p class="fts-14 fw-4 white-color70-n">
                        {{ _profileSubTitle($user) }}
                    </p>
                    <div class="d-flex gap-2 flex-wrap mt-2 justify-content-between align-items-end">
                        @if ($item->receiver_response == 'Accepted')
                            <div class="status-profils accepted fts-13">{{ $item->receiver_response }}</div>
                        @elseif($item->receiver_response == 'Rejected')
                            <div class="status-profils rejected fts-13">{{ $item->receiver_response }}</div>
                        @else
                            <div class="status-profils fts-13">{{ $item->receiver_response }}</div>
                            <button class="btn-request-intrest-delete remove-request" data-id="{{ $item->id }}">
                                <iconify-icon icon="fluent:delete-28-regular"></iconify-icon>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="request-intrest-box py-3">
                <div class="request-intrest-top position-relative">
                    <div class="profile-request-intrest">
                        @if (!$canView && $hasPhoto)
                            <a href="javascript:void(0)" class="open-photo-request-modal" data-receiver-id="{{ $user->id }}">
                                <div class="inner-request-profile position-relative">
                                    <img src="{{ _getProtectedImage($user->gender) }}" alt="{{ _profileTitle($user) }}"
                                        class="profile-request-img">
                                </div>
                            </a>
                        @else
                            <a href="{{ route('web.userProfile.index', _encrypt($user->id)) }}">
                                <div class="inner-request-profile position-relative">
                                    <img src="{{ $profileImage }}" alt="{{ _profileTitle($user) }}"
                                        class="profile-request-img">
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
                <div
                    class="request-intrest-bottom w-100 position-relative d-flex align-items-center justify-content-between mt-2">
                    <div class="d-block">
                        <a href="{{ route('web.userProfile.index',_encrypt($user->id)) }}">
                            <h4 class="fts-16 fw-6 white-color-n mb-2">{{ _profileTitle($user) }}</h4>
                        </a>
                        <p class="fts-14 fw-4 white-color70-n">
                            {{ _profileSubTitle($user) }}
                        </p>
                        @if ($item->receiver_response == 'Accepted')
                            <div class="status-profils mt-2 accepted fts-13">{{ $item->receiver_response }}</div>
                        @elseif($item->receiver_response == 'Rejected')
                            <div class="status-profils mt-2 rejected fts-13">{{ $item->receiver_response }}</div>
                        @else
                            <div class="status-profils fts-13 mt-2">{{ $item->receiver_response }}</div>
                        @endif
                    </div>
                    <div class="d-flex gap-2 flex-wrap mt-2 justify-content-between"></div>
                    @if ($type != 'request_sent' && $item->receiver_response == 'Pending')
                        <div class="d-flex gap-2 flex-column">
                            <button class="btn-accept-btn fts-13 accept-request"
                                data-id="{{ $item->id }}">{{ __('messages.lbl_accept') }}</button>
                            <button class="btn-reject-btn fts-13 reject-request"
                                data-id="{{ $item->id }}">{{ __('messages.lbl_reject') }}</button>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @empty
        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.noDataFound', [
            'message' => __('messages.lbl_no_photorequest_data_found'),
        ])
    @endforelse
</div>

<!-- pagination  -->
@if ($resultData->hasPages())
    {{ $resultData->links(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.pagination') }}
@endif

<!-- Profiles Grid -->
@forelse($resultArr as $result)
    @php
        $selMemberColumnId = 2;
        $otherMemberColumnId = 1;
        if ($result->id == $authUser->id) {
            $selMemberColumnId = 1;
            $otherMemberColumnId = 2;
        }

        $memberMeetingStatus = 0;
        if ($result->response == 0) {
            $statusClass = 'pending';
            $status = 'Pending';
        }
        if ($result->response == 1) {
            $statusClass = 'accepted';
            $status = 'Accepted';
        }
        if ($result->response == 2) {
            $statusClass = 'rejected';
            $status = 'Rejected';
        }
    @endphp

    <div class="request-intrest-box py-3">
        <div class="request-intrest-top position-relative">
            <div class="profile-request-intrest">
                @php
                    $receiver       = $result->receiver;
                    $canView      = _canViewMemberPhoto($receiver, $result->hasPhotoRequestAccess);
                    $hasPhoto     = _checkPhotoExist($receiver);
                    $profileImage = _getMemberProfileImage($receiver);
                @endphp
                @if (!$canView && $hasPhoto)
                    <a href="javascript:void(0)" class="open-photo-request-modal" data-receiver-id="{{ $receiver->id }}">
                        <div class="inner-request-profile position-relative">
                            <img src="{{ _getProtectedImage($receiver->gender) }}" alt="{{ _profileTitle($receiver) }}"
                                class="profile-request-img">
                        </div>
                    </a>
                @else
                    <a href="{{ route('web.userProfile.index', _encrypt($receiver->id)) }}">
                        <div class="inner-request-profile position-relative">
                            <img src="{{ $profileImage }}" alt="{{ _profileTitle($receiver) }}" class="profile-request-img">
                        </div>
                    </a>
                @endif
            </div>
        </div>
        <div
            class="request-intrest-bottom w-100 position-relative d-flex align-results-center justify-content-between mt-2 pe-5">
            <div class="d-block">
                <a href="{{ route('web.userProfile.index', _encrypt($receiver->id)) }}">
                    <h4 class="fts-16 fw-6 white-color-n mb-2">{{ _profileTitle($receiver) }}</h4>
                </a>
                <p class="fts-14 fw-4 white-color70-n">
                    {{ _profileSubTitle($receiver) }}
                </p>
                <div class="status-profils mt-2 fts-13 {{ $statusClass }}">{{ $status }}</div>
            </div>
            <div class="d-flex gap-2 flex-wrap mt-2 justify-content-between"></div>
            <div class="d-flex gap-2 flex-column action-btn-box">
                @if ($result->response == 0)
                    <button class="btn-accept-btn fts-13 acceptMatchBtn"
                        data-id="{{ $result->id }}"
                        data-otheruserid="{{ $receiver->id }}"
                        data-otherusermatriid="{{ $receiver->matri_id }}">{{ __('messages.lbl_accept') }}</button>
                    <button class="btn-reject-btn fts-13 rejectMatchBtn"
                        type="button"
                        data-id="{{ $result->id }}"
                        data-otheruserid="{{ $receiver->id }}"
                        data-otherusermatriid="{{ $receiver->matri_id }}">{{ __('messages.lbl_reject') }}</button>
                @endif
            </div>
        </div>
    </div>
@empty
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.noDataFound', [
        'message' => __('messages.lbl_no_matches_found'),
    ])
@endforelse

<!-- pagination  -->
@if ($resultArr->hasPages())
    {{ $resultArr->links(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.pagination') }}
@endif

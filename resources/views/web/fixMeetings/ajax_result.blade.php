<!-- Profiles Grid -->
@forelse($resultArr as $result)
    @php
        $selMemberColumnId = 1;
        $otherMemberColumnId = 2;
        if ($result->member1_id != $authUser->id) {
            $selMemberColumnId = 2;
            $otherMemberColumnId = 1;
        }

        $selMemberColumnStatus = 'member' . $selMemberColumnId . '_status';
        $oppositeMemberStatus = 'member' . $otherMemberColumnId . '_status';
        $oppositeMemberMatri = 'member' . $otherMemberColumnId . '_matri_id';

        ## Current User Status
        if ($result->$selMemberColumnStatus == 0) {
            $status = 'Pending';
            $statusClass = 'pending';
        }
        if ($result->$selMemberColumnStatus == 1) {
            $status = 'Accepted';
            $statusClass = 'accept';
        }
        if ($result->$selMemberColumnStatus == 2) {
            $status = 'Rejected';
            $statusClass = 'reject';
        }

        ## Other User Status :
        if ($result->$oppositeMemberStatus == 0) {
            $otherUserStatus = 'Pending From';
            $statusClassNew = 'feexmeeting-pending';
        }
        if ($result->$oppositeMemberStatus == 1) {
            $otherUserStatus = 'Accepted From';
            $statusClassNew = 'feexmeeting-accept';
        }
        if ($result->$oppositeMemberStatus == 2) {
            $otherUserStatus = 'Rejected From';
            $statusClassNew = 'feexmeeting-reject';
        }
    @endphp

    <div class="common-bgwhite-main meeting-card p-3 mt-3">
        <div class="meeting-single-box d-lg-flex gap-4">
            <div class="meeting-status-images position-relative">
                @php
                    $otherMember = $result->otherMember;
                    $canView = _canViewMemberPhoto($otherMember, $otherMember->hasPhotoRequestAccess);
                    $hasPhoto = _checkPhotoExist($otherMember);
                    $profileImage = _getMemberProfileImage($otherMember);
                @endphp
                @if (!$canView && $hasPhoto)
                    <a href="javascript:void(0)" class="open-photo-request-modal"
                        data-receiver-id="{{ $otherMember->id }}">
                        <img src="{{ _getProtectedImage($otherMember->gender) }}" alt="{{ _profileTitle($otherMember) }}"
                            class="user-meeting-img">
                    </a>
                @else
                    <a href="{{ route('web.userProfile.index', _encrypt($otherMember->id)) }}">
                        <img src="{{ $profileImage }}" alt="{{ _profileTitle($otherMember) }}" class="user-meeting-img">
                    </a>
                @endif
                @if($result->meeting_status == 1)
                    <div class="meeting-status accept">{{ _getLang('lbl_completed') }}</div>
                @else
                    <div class="meeting-status {{ $statusClass }}">{{ $status }}</div>
                @endif
            </div>
            <div class="meeting-content-result w-100 mt-3 mt-lg-2">
                <a href="{{ route('web.userProfile.index', _encrypt($otherMember->id)) }}">
                    <h4 class="fts-18 fw-7 white-color-n">{{ _profileTitle($otherMember) }}</h4>
                </a>
                <p class="fts-14 fw-4 white-color70-n">
                    {{ $result->description ?? 'N/A' }}
                </p>
                <ul class="profile-topbar-boxlist d-flex gap-3 flex-wrap pt-lg-2">
                    <li class="mt-2">
                        <h5 class="fts-15 fw-5 white-color-n">{{ __('messages.field_lbl_address') }}</h5>
                        <p class="fts-14 fw-4 white-color70-n">
                            {{ $result->address ?? '' }}
                        </p>
                    </li>
                    <li class="mt-2">
                        <h5 class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_meeting_time') }}</h5>
                        <p class="fts-14 fw-4 white-color70-n">
                            {{ _displayDate($result->date_time, 'j F, Y H:i') }}
                        </p>
                    </li>
                    @if ($result->$selMemberColumnStatus != 0 && $result->meeting_status == 0)
                        <li class="mt-2">
                            <h5 class="fts-15 fw-7 title-color-L white-color-n">{{ $otherUserStatus }} <span
                                    class="primary-color-n">({{ $result->$oppositeMemberMatri }})</span></h5>
                        </li>
                    @endif
                    @if(isset($result->meeting_remark) && $result->meeting_remark != '' && $result->meeting_status == 1)
                        <li class="mt-2">
                            <h5 class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_complete_remarks') }}</h5>
                            <p class="fts-14 fw-4 white-color70-n">
                                {{ _displayNotAvailable($result->meeting_remark) }}
                            </p>
                        </li>
                    @endif
                </ul>
                <div class="meeting-accept-reject d-flex justify-content-md-end gap-2 mt-3">
                    @if ($result->$selMemberColumnStatus == 0)
                        <button class="btn-meeting-accept fts-14 acceptRejectBtn" data-id="{{ $result->id }}"
                            data-response="1"
                            data-label="{{ $selMemberColumnStatus }}">{{ _getLang('lbl_accept') }}</button>
                        <button class="btn-meeting-reject fts-14" type="button" data-bs-toggle="collapse"
                            data-bs-target="#reject_status{{ $result->id }}" aria-expanded="false"
                            aria-controls="reject_status{{ $result->id }}">{{ _getLang('lbl_reject') }}</button>
                    @endif
                    @if ($result->$selMemberColumnStatus == 1 && $result->$oppositeMemberStatus == 1 && $result->meeting_status == 0)
                        <button class="btn-meeting-accept fts-14" type="button" data-bs-toggle="collapse"
                            data-bs-target="#complete_status{{ $result->id }}" aria-expanded="false"
                            aria-controls="complete_status{{ $result->id }}">{{ _getLang('lbl_click_to_complete') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
        <div class="collapse complate-metting-suggest pt-3 mt-3" id="reject_status{{ $result->id }}">
            <form class="addMeetingRemarks" id="addMeetingRemarks" action="{{ route('web.fixMeetings.acceptReject') }}"
                method="POST">
                @csrf
                <div class="comman_inputfield_main">
                    <label for="member_reject_remark">{{ _getLang('lbl_reject_remarks') }}</label>
                    <textarea name="member_reject_remark" required id="member_reject_remark" class="input_comman_field textareasize"
                        placeholder="{{ _getLang('lbl_enter_reject_remarks') }}"></textarea>
                    <input type="hidden" name="rejectBy" id="rejectBy"
                        value="member{{ $selMemberColumnId }}_reject_remark">
                    <button type="button" class="getstarted-btn-how addRemarks h6" data-id="{{ $result->id }}"
                        data-response="2"
                        data-label="{{ $selMemberColumnStatus }}">{{ _getLang('lbl_submit') }}</button>
                </div>
            </form>
        </div>
        <div class="collapse complate-metting-suggest pt-3 mt-3" id="complete_status{{ $result->id }}">
            <form class="addMeetingCompleted" id="addMeetingCompleted"
                action="{{ route('web.fixMeetings.acceptReject') }}" method="POST">
                @csrf
                <div class="comman_inputfield_main">
                    <label for="meeting_remark">{{ _getLang('lbl_complete_remarks') }}</label>
                    <textarea name="meeting_remark" required id="meeting_remark" class="input_comman_field textareasize"
                        placeholder="{{ _getLang('lbl_enter_complete_remarks') }}"></textarea>
                    <input type="hidden" name="rejectBy" value="meeting_remark">
                    <button type="button" class="getstarted-btn-how addRemarks h6" data-id="{{ $result->id }}"
                        data-response="1" data-label="meeting_status">{{ _getLang('lbl_submit') }}</button>
                </div>
            </form>
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

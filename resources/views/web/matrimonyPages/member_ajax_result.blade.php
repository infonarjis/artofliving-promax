@forelse($members as $member)
    @php
        $canView = _canViewMemberPhoto($member, $member->hasPhotoRequestAccess);
        
        $hasPhoto = _checkPhotoExist($member);
        $profileImage = _getMemberProfileImage($member);
    @endphp
    <div class="col-xxl-3 col-xl-4 col-sm-6 px-1 mb-2">
        <div class="single-dashboard-profiles Platinum">
            <div class="profile-box-whitebg">
                <div class="tops_imagesdsd">
                    <a href="{{ route('web.userProfile.index',_encrypt($member->id)) }}">
                        @if(!$canView && $hasPhoto)
                            <img src="{{ _getProtectedImage($member->gender) }}" alt="{{ _profileTitle($member) }}" class="lastprofileimg">
                        @else
                            <img src="{{ $profileImage }}" alt="{{ _profileTitle($member) }}" class="lastprofileimg">
                        @endif
                    </a>
                    @if ($member->plan_status == 'Paid')
                        <div class="commanbadge Platinum">{{ $member->plan_name }}</div>
                    @endif
                </div>
                <div class="profile-dashboard-text text-center mt-2 pb-1">
                    <a href="{{ route('web.userProfile.index',_encrypt($member->id)) }}">
                        <h4 class="fts-16 fw-6 white-color-n">{{ _profileTitle($member) }}</h4>
                    </a>
                    <p class="fts-14 fw-4 white-color70-n">{{ _profileSubTitle($member) }}</p>
                </div>
            </div>
        </div>
    </div>
@empty
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.noDataFound', [
        'message' => __('messages.lbl_no_events_found'),
    ])
@endforelse

<!-- pagination  -->
@if ($members->hasPages())
    {{ $members->links(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.pagination') }}
@endif

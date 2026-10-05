@if ($paidProfiles->isNotEmpty())
    <div class="pro-common-leftbar">
        <div class="premium-profile-leftbar">
            <div class="title-premium-profile p-3 pb-2">
                <div class="fw-6 fts-16 white-color-n">{{ __('messages.lbl_new_premium_profile') }}</div>
            </div>
            <div class="left-premium-profile-list pb-2 pt-1">
                @foreach ($paidProfiles as $member)
                    <div class="single-premium-profile px-3 py-2 my-1 cursor-pointer d-flex gap-2 align-items-center">
                        <div class="left-premiup-imgdiv position-relative">
                            @php
                                $canView = _canViewMemberPhoto($member, $member->hasPhotoRequestAccess);
                                $hasPhoto = _checkPhotoExist($member);
                                $profileImage = _getMemberProfileImage($member);
                            @endphp
                            @if (!$canView && $hasPhoto)
                                <a href="{{ route('web.userProfile.index', _encrypt($member->id)) }}">
                                    <img src="{{ _getProtectedImage($member->gender) }}"
                                        alt="{{ _profileTitle($member) }}" class="premium-sm-profile">
                                </a>
                            @else
                                <a href="{{ route('web.userProfile.index', _encrypt($member->id)) }}">
                                    <img src="{{ $profileImage }}" alt="{{ _profileTitle($member) }}"
                                        class="premium-sm-profile">
                                </a>
                            @endif
                        </div>
                        <div class="right-premium-contents ps-1">
                            <h4 class="fts-15 fw-6 white-color-n">{{ _profileTitle($member) }}</h4>
                            <p class="fts-14 fw-4 white-color70-n">{{ _profileSubTitle($member) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
@if ($featuredProfiles->isNotEmpty())
    <div class="pro-common-leftbar p-3 mt-3 d-none d-lg-block">
        <h2 class="fw-6 fts-18 white-color-n">{{ __('messages.lbl_featured_profile') }}</h2>
        <div class="profile-day-slider">
            <div class="profile-of-day-box p-3">
                @foreach ($featuredProfiles as $member)
                    <div class="profiles-day-items px-5">
                        <div class="profile-the-img">
                            @php
                                $canView = _canViewMemberPhoto($member, $member->hasPhotoRequestAccess);
                                $hasPhoto = _checkPhotoExist($member);
                                $profileImage = _getMemberProfileImage($member);
                            @endphp
                            @if (!$canView && $hasPhoto)
                                <a href="{{ route('web.userProfile.index', _encrypt($member->id)) }}">
                                    <img src="{{ _getProtectedImage($member->gender) }}"
                                        alt="{{ _profileTitle($member) }}" class="profiles-of-img">
                                </a>
                            @else
                                <a href="{{ route('web.userProfile.index', _encrypt($member->id)) }}">
                                    <img src="{{ $profileImage }}" alt="{{ _profileTitle($member) }}"
                                        class="profiles-of-img">
                                </a>
                            @endif
                        </div>
                        <h4 class="fts-16 fw-5 white-color-n mt-2">{{ _profileTitle($member) }}</h4>
                        <p class="fts-14 fw-4 white-color70-n mt-1">{{ _profileSubTitle($member) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Advertisement Banner --}}
@include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.advertisementBanner', ['adv_type' => 'Level 1'])
@include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.advertisementBanner', ['adv_type' => 'Level 2'])
{{-- Advertisement Banner --}}

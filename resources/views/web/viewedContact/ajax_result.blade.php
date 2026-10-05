<h2 class="fts-18 fw-7 white-color-n mb-1">
    @if($type == 'i_viewed')
        {{ __('messages.lbl_viewed_contacts') }}
    @else
        {{ __('messages.lbl_who_viewed_contact') }}
    @endif
</h2>
<p class="fts-14 white-color-n mb-3">
    @if($type == 'i_viewed')
        {{ __('messages.lbl_viewed_contacts_subtitle') }}
    @else
        {{ __('messages.lbl_who_viewed_contact_subtitle') }}
    @endif
</p>

<div class="row g-3  px-1">
    @forelse($resultData as $item)
        @php
            $user = $type == 'i_viewed' ? $item->receiver : $item->sender;

            $canView = _canViewMemberPhoto($user, $item->hasPhotoRequestAccess);
            $hasPhoto = _checkPhotoExist($user);
            $profileImage = _getMemberProfileImage($user);
        @endphp
        <div class="col-xxl-3 col-xl-4 col-6 px-2 mt-3">
            <div class="viewed-profile-box position-relative p-2 p-sm-3">
                <div class="top-viewed-profiles position-relative">

                    <a href="{{ route('web.userProfile.index', _encrypt($user->id)) }}"
                        class="btn-view-profile">
                        <iconify-icon icon="solar:eye-linear"></iconify-icon>
                    </a>

                    @if (!$canView && $hasPhoto)
                        <a href="javascript:void(0)" class="open-photo-request-modal"
                            data-receiver-id="{{ $user->id }}">
                            <div class="viewed-inner-profile">
                                <img src="{{ _getProtectedImage($user->gender) }}" alt="{{ _profileTitle($user) }}"
                                    class="view-profile-img">
                            </div>
                        </a>
                    @else
                        <a href="{{ route('web.userProfile.index', _encrypt($user->id)) }}">
                            <div class="viewed-inner-profile">
                                <img src="{{ $profileImage }}" alt="{{ _profileTitle($user) }}"
                                    class="view-profile-img">
                            </div>
                        </a>
                    @endif
                </div>
                <div class="matches-content-sd text-center mt-2">
                    <a href="{{ route('web.userProfile.index', _encrypt($user->id)) }}">
                        <div class="white-color-n fts-16 fw-5">{{ _profileTitle($user) }}</div>
                    </a>
                    <div class="fts-14 fw-4 white-color70-n">{{ _profileSubTitle($user) }}</div>
                </div>
            </div>
        </div>
    @empty
        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.noDataFound', [
            'message' => __('messages.lbl_no_data_found'),
        ])
    @endforelse
</div>

<!-- pagination  -->
@if ($resultData->hasPages())
    {{ $resultData->links(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.pagination') }}
@endif

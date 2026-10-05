<!-- Profiles Grid -->
<div class="row g-3">
    @forelse($resultData as $shortlist)
        <div class="col-md-6 col-12">
            <div class="common-bgwhite-main p-3 d-flex gap-3 align-items-center h-100 shortlist-profile-card">
                <div class="position-relative p-0 shortlist-profile-imgbox">
                    @php
                        $receiver = $shortlist->receiver;
                        $canView = _canViewMemberPhoto($receiver, $shortlist->hasPhotoRequestAccess);
                        $hasPhoto = _checkPhotoExist($receiver);
                        $profileImage = _getMemberProfileImage($receiver);
                    @endphp

                    @if (!$canView && $hasPhoto)
                        <a href="javascript:void(0)" class="open-photo-request-modal"
                            data-receiver-id="{{ $receiver->id }}">
                            <div class="shortlist-profile-inner-img">
                                <img src="{{ _getProtectedImage($receiver->gender) }}"
                                    alt="{{ _profileTitle($receiver) }}" class="shortlist-inner-img">
                            </div>
                        </a>
                    @else
                        <a href="{{ route('web.userProfile.index', _encrypt($receiver->id)) }}">
                            <div class="shortlist-profile-inner-img">
                                <img src="{{ $profileImage }}" alt="{{ _profileTitle($receiver) }}"
                                    class="shortlist-inner-img">
                            </div>
                        </a>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('web.userProfile.index', _encrypt($receiver->id)) }}">
                            <h4 class="fts-18 fw-6 white-color-n mb-1">{{ _profileTitle($receiver) }}</h4>
                        </a>
                        <a href="{{ route('web.userProfile.index', _encrypt($receiver->id)) }}">
                            <iconify-icon icon="hugeicons:eye" class="fts-20 white-color70-n"></iconify-icon>
                        </a>
                    </div>

                    <p class="fts-14 fw-4 white-color70-n mb-1">{{ _getMemberAgeHeight($receiver) }}</p>
                    <p class="fts-14 fw-4 white-color70-n mb-3">{{ _getMemberLocation($receiver) }}</p>
                    <button
                        class="shortlist-card-btn remove-shortlist d-inline-flex align-items-center gap-2 px-3 py-2 fts-14 fw-6"
                        data-id="{{ $shortlist->id }}">
                        <iconify-icon icon="mingcute:star-fill" class="fts-20"></iconify-icon>
                        {{ __('messages.lbl_remove_shortlist') }}
                    </button>
                </div>
            </div>
        </div>
    @empty
        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.noDataFound', [
            'message' => __('messages.lbl_no_shortlist_found'),
        ])
    @endforelse
</div>

<!-- pagination  -->
@if ($resultData->hasPages())
    {{ $resultData->links(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.pagination') }}
@endif

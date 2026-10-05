@php
    $photoVal1 = _getMemberProfileImage($receiver, '', 'photo1');
    $photoVal2 = _getMemberProfileImage($receiver, '', 'photo2');
    $photoVal3 = _getMemberProfileImage($receiver, '', 'photo3');
    $photoVal4 = _getMemberProfileImage($receiver, '', 'photo4');
    ## Check Photo Exists:
    $hasPhoto = _checkPhotoExist($receiver);
    ## Photo View :
    $canView = _canViewMemberPhoto($receiver, $receiver->hasPhotoRequestAccess);
@endphp
<div class="modal-header mx-3 mx-lg-4 pt-4 pb-2 border-0">
    <h2 class="fts-20 fw-7 white-color-n" id="ContactViewsLabel">{{ __('messages.lbl_contact_details_of') }}
        <span class="fw-7">{{ _profileTitle($receiver) }}</span>
    </h2>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
</div>

<div class="modal_liteBody px-3 px-lg-4 py-3 pb-4">
    {{-- Alert Messages --}}
    @if ($type == 'view_contact')
        @include('web.common.alert_message')
        <div class="row">
            <div class="col-lg-6 col-md-6 pe-lg-3">
                <div class="userprofiles-leftpanel">
                    <div class="user-prfilesimg-groups d-flex gap-2 mb-3">
                        @if (!$canView && $hasPhoto)
                            <div class="w-100">
                                <img src="{{ _getProtectedImage($receiver->gender) }}" alt="{{ _profileTitle($receiver) }}"
                                    class="w-100 object-fit-cover contact-views-main-img">
                            </div>
                            <div class="d-flex flex-column justify-content-between contact-views-thumb-list">
                                <img src="{{ _getProtectedImage($receiver->gender) }}" alt="{{ _profileTitle($receiver) }}"
                                    class="w-100 object-fit-cover contact-views-thumb-item">
                                <img src="{{ _getProtectedImage($receiver->gender) }}" alt="{{ _profileTitle($receiver) }}"
                                    class="w-100 object-fit-cover contact-views-thumb-item">
                                <img src="{{ _getProtectedImage($receiver->gender) }}" alt="{{ _profileTitle($receiver) }}"
                                    class="w-100 object-fit-cover contact-views-thumb-item">
                            </div>
                        @else
                            <div class="w-100">
                                <img src="{{ $photoVal1 }}"
                                    alt="{{ _profileTitle($receiver) }}"
                                    class="w-100 object-fit-cover contact-views-main-img">
                            </div>
                            <div class="d-flex flex-column justify-content-between contact-views-thumb-list">
                                <img src="{{ $photoVal2 }}"
                                    alt="{{ _profileTitle($receiver) }}"
                                    class="w-100 object-fit-cover contact-views-thumb-item">
                                <img src="{{ $photoVal3 }}"
                                    alt="{{ _profileTitle($receiver) }}"
                                    class="w-100 object-fit-cover contact-views-thumb-item">
                                <img src="{{ $photoVal4 }}"
                                    alt="{{ _profileTitle($receiver) }}"
                                    class="w-100 object-fit-cover contact-views-thumb-item">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 ps-lg-4 mt-4 mt-md-0">
                <div class="rightside-details-vw pt-1">
                    <div class="row">
                        <div class="col-12 mb-2 pb-1">
                            <div class="single-details">
                                <div class="fts-13 fw-4 white-color70-n mb-1">{{ __('messages.field_lbl_matri_id') }}
                                </div>
                                <div class="fts-15 fw-6 white-color-n">{{ $receiver->matri_id }}</div>
                            </div>
                        </div>
                        <div class="col-12 mb-2 pb-1">
                            <div class="single-details">
                                <div class="fts-13 fw-4 white-color70-n mb-1">{{ __('messages.field_lbl_fullname') }}
                                </div>
                                <div class="fts-15 fw-6 white-color-n">{{ $receiver->fullname }}</div>
                            </div>
                        </div>
                        <div class="col-12 mb-2 pb-1">
                            <div class="single-details">
                                <div class="fts-13 fw-4 white-color70-n mb-1">{{ __('messages.field_lbl_email_id') }}
                                </div>
                                <div class="fts-15 fw-6 white-color-n">
                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ $receiver->email }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-2 pb-1">
                            <div class="single-details">
                                <div class="fts-13 fw-4 white-color70-n mb-1">
                                    {{ __('messages.field_lbl_mobile_number') }}
                                </div>
                                <div class="fts-15 fw-6 white-color-n">
                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ $receiver->mobile }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if (_checkFieldEnable('alternate_number', 'user_profile_list'))
                            <div class="col-6 mb-2 pb-1">
                                <div class="single-details">
                                    <div class="fts-13 fw-4 white-color70-n mb-1">
                                        {{ __('messages.field_lbl_alternate_number') }}</div>
                                    <div class="fts-15 fw-6 white-color-n">
                                        @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                            {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                        @else
                                            {{ _displayNotAvailable($receiver->alternate_number) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-12 mb-2 pb-1">
                            <div class="single-details">
                                <div class="fts-13 fw-4 white-color70-n mb-1">{{ __('messages.lbl_location') }}</div>
                                <div class="fts-15 fw-6 white-color-n lh-base pe-lg-5">
                                    {{ _getMemberLocation($receiver) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-2 pb-1">
                            <div class="single-details">
                                <div class="fts-13 fw-4 white-color70-n mb-1">{{ __('messages.field_lbl_address') }}
                                </div>
                                <div class="fts-15 fw-6 white-color-n lh-base pe-lg-5">
                                    {{ _displayNotAvailable($receiver->address) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($type == 'expressInterest')
        <div class="alert alert-message-components my-3 error fade show" role="alert">
            <div class="alert-icon">
                <iconify-icon icon="mdi:shield-alert"></iconify-icon>
            </div>
            <div class="alert-contents pe-3">
                <h4 class="fts-16 fw-5">{{ __('messages.lbl_send_interest_to_view_contact_details') }}</h4>
                <p class="fts-13 fw-4 opacity-75">
                    {{ __('messages.lbl_send_interest_to_view_contact_details_msg') }}
                </p>
            </div>
        </div>
    @elseif ($type == 'upgrade_membership')
        <div class="alert alert-message-components my-3 error fade show" role="alert">
            <div class="alert-icon">
                <iconify-icon icon="mdi:shield-alert"></iconify-icon>
            </div>
            <div class="alert-contents pe-3">
                <h4 class="fts-16 fw-5">{{ __('messages.lbl_upgrade_to_membership_plan') }}</h4>
                <p class="fts-13 fw-4 opacity-75">
                    {{ __('messages.lbl_upgrade_to_membership_plan_to_unlock_full_access') }}
                </p>
            </div>
        </div>
        <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('web.membershipPlan.index') }}" class="click-changeButton">{{ __('messages.lbl_upgrade_membership_now') }}</a>
        </div>
    @endif
</div>

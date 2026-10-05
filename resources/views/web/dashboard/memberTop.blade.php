@php
    $authUser = Auth::guard('web')->user();
    if ($authUser) {
        $authUser->refresh();
    }
    $profileImage = _getMemberProfileImage($authUser, 'Yes');
@endphp
<div class="common-bgwhite-main p-2 p-xxl-3">
    <div class="row">
        <div class="col-3 common-left-profilebox">
            <div class="common-profile-users align-items-center px-3 py-2 gap-2" id="small-profile-user"
                style="display: flex;">
                <div class="profile-placeholder-upload m-0 my-1">
                    <div class="profile-placeholder-small">
                        <img src="{{ $profileImage }}" alt="{{ $authUser->fullname }}" class="profile-rb-image">
                    </div>
                </div>
                <div class="profile-content">
                    <div class="position-relative d-flex align-items-center justify-content-center">
                        <h4 class="fts-16 fw-6 white-color-n">{{ $authUser->fullname }}</h4>
                        <span class="user-online-status"></span>
                    </div>
                    <h5 class="fts-14 fw-4 white-color70-n mt-2">ID - {{ $authUser->matri_id }}</h5>
                </div>
            </div>
            <div class="common-profile-users" id="big-profile-user" style="display: none;">
                <div class="profile-placeholder-upload">
                    <div class="profile-placeholder-user">
                        <img src="{{ $profileImage }}" alt="{{ $authUser->fullname }}" class="profile-rb-image">
                    </div>
                    <a href="{{ route('web.myProfile.editProfile', 'upload_photos') }}">
                        <div class="view-profile-upload"><iconify-icon icon="solar:camera-bold"></iconify-icon></div>
                    </a>
                </div>
                <div class="profile-content text-center mt-2 pt-1">
                    <div class="position-relative d-flex align-items-center justify-content-center">
                        <h4 class="fts-18 fw-6 white-color-n">{{ $authUser->fullname }}</h4>
                        <span class="user-online-status"></span>
                    </div>
                    <h5 class="fts-14 fw-4 white-color70-n mt-2">ID - {{ $authUser->matri_id }}</h5>
                </div>
                <span class="fts-12 user-plan-badge">
                    <iconify-icon icon="material-symbols:crown-outline-rounded" class="fts-16"></iconify-icon>
                    {{ $authUser->plan_name }}
                </span>
            </div>
            <div class="mobile-email-verify mt-2" style="display: none;">
                @if ($authUser->mobile_verify_status == 'Yes')
                    {{-- Mobile Verified --}}
                    <div class="verified-profiles w-50 text-center cursor-pointer" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="{{ __('messages.lbl_verified') }}">

                        <div class="verified-profile-icon text-center position-relative">
                            <iconify-icon icon="proicons:call" class="verified-phone"></iconify-icon>
                            <iconify-icon icon="iconamoon:check-bold" class="verified-check"></iconify-icon>
                        </div>

                        <p class="fts-14 fw-4 white-color70-n mt-1">
                            {{ __('messages.lbl_verified') }}
                        </p>
                    </div>
                @else
                    {{-- Mobile Not-Verified --}}
                    <div class="verified-profiles w-50 text-center cursor-pointer" data-bs-toggle="modal"
                        data-bs-target="#mobileVerificationModal" data-bs-tooltip="tooltip" data-bs-placement="top"
                        title="{{ __('messages.lbl_not_verified') }}">

                        <div class="verified-profile-icon text-center position-relative">
                            <iconify-icon icon="proicons:call" class="verified-phone"></iconify-icon>
                            <iconify-icon icon="pepicons-pop:info" class="verified-cross"></iconify-icon>
                        </div>

                        <p class="fts-14 fw-4 white-color70-n mt-1">
                            {{ __('messages.lbl_not_verified') }}
                        </p>
                    </div>
                @endif

                @if ($authUser->email_verify_status == 'Verify')
                    {{-- Email Verified --}}
                    <div class="verified-profiles w-50 text-center cursor-pointer" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="{{ __('messages.lbl_verified') }}">
                        <div class="verified-profile-icon text-center position-relative">
                            <iconify-icon icon="mage:email-opened" class="verified-phone"></iconify-icon>
                            <iconify-icon icon="iconamoon:check-bold" class="verified-check"></iconify-icon>
                        </div>
                        <p class="fts-14 fw-4 white-color70-n mt-1">{{ __('messages.lbl_verified') }}</p>
                    </div>
                @else
                    {{-- Email Not-Verified --}}
                    <div class="verified-profiles w-50 text-center cursor-pointer" data-bs-toggle="modal"
                        data-bs-target="#sendEmailVerification" data-bs-tooltip="tooltip" data-bs-placement="top"
                        title="{{ __('messages.lbl_not_verified') }}">
                        <div class="verified-profile-icon text-center position-relative">
                            <iconify-icon icon="mage:email-opened" class="verified-phone"></iconify-icon>
                            <iconify-icon icon="pepicons-pop:info" class="verified-cross"></iconify-icon>
                        </div>
                        <p class="fts-14 fw-4 white-color70-n mt-1">{{ __('messages.lbl_not_verified') }} </p>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-6 common-center-profilebox ps-xl-1 px-3">
            <div class="profile-details-center-main mt-3">
                <div class="row px-1 top-profile-short">
                    <div class="col-md-6 px-2">
                        <div class="profile-action-box">
                            <h3 class="fw-5 fts-16 white-color-n">{{ __('messages.lbl_profile_completed') }}
                                ({{ $completionPercent }}%)</h3>
                            <div class="progress-bar-boxs mt-1">
                                <div class="d-flex gap-1 align-items-center">
                                    <div class="progress" aria-label="Basic example"
                                        aria-valuenow="{{ $completionPercent }}" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $completionPercent }}%"></div>
                                    </div>
                                    <a href="{{ route('web.myProfile.index') }}" class="progress-edit-btn">
                                        <iconify-icon icon="fluent:edit-12-filled"></iconify-icon>
                                    </a>
                                </div>
                                @if ($completionPercent < 100)
                                    <p class="fts-14 fw-4 white-color-n opacity-75">
                                        {{ 100 - $completionPercent }}% {{ __('messages.lbl_need_more_details') }}
                                    </p>
                                @else
                                    <p class="fts-14 fw-4 text-success">
                                        {{ __('messages.lbl_profile_completed') }} <iconify-icon
                                            icon="lets-icons:check-fill"></iconify-icon>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 px-2 mt-2 mt-lg-0 ps-lg-4">
                        <div class="profile-actiopn-complate w-100">
                            <div class="profile-action-box d-flex justify-content-between gap-2">
                                <div class="profile-action-btn">
                                    <a href="{{ route('web.shortlist.index') }}" class="btn-action">
                                        <iconify-icon icon="mynaui:star"></iconify-icon>
                                    </a>
                                </div>
                                <div class="profile-action-btn">
                                    <a href="{{ route('web.expressInterest.index') }}" class="btn-action">
                                        <iconify-icon icon="solar:heart-linear"></iconify-icon>
                                    </a>
                                </div>
                                <div class="profile-action-btn">
                                    @if ($configArr['chat_module_design'] == 'popup')
                                        <button class="btn-action open-chat">
                                            <iconify-icon icon="solar:chat-dots-broken"></iconify-icon>
                                        </button>
                                    @else
                                        <a href="{{ route('web.chat.index') }}" class="btn-action">
                                            <iconify-icon icon="solar:chat-dots-broken"></iconify-icon>
                                        </a>
                                    @endif
                                </div>
                                <a class="profile-expand-btn text-center d-none d-lg-block" data-bs-toggle="collapse"
                                    href="#collapseProfile" aria-expanded="false" aria-controls="collapseProfile">
                                    <iconify-icon id="profileExpandIcon" icon="iconamoon:arrow-down-2"></iconify-icon>
                                    <p class="white-color-n fw-5 fts-14 mt-1">{{ __('messages.lbl_expand') }}</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="collapse" id="collapseProfile">
                    <div class="row px-1 top-profile-short align-items-center">
                        <div class="col-md-6 px-2">
                            <ul class="profile-topbar-boxlist mt-3 mt-lg-4">
                                <li class="py-1 my-4 d-flex justify-content-between">
                                    <h5 class="fts-14 fw-4 white-color70-n w-50">{{ __('messages.field_lbl_gender') }}
                                    </h5>
                                    <p class="fts-14 fw-4 white-color-n">{{ $authUser->gender }}</p>
                                </li>
                                <li class="py-1 my-4 d-flex justify-content-between">
                                    <h5 class="fts-14 fw-4 white-color70-n w-50">
                                        {{ __('messages.field_lbl_birthdate') }}</h5>
                                    <p class="fts-14 fw-4 white-color-n">
                                        {{ _displayDate($authUser->birthdate, 'd-m-Y') }}</p>
                                </li>
                                <li class="py-1 my-4 d-flex justify-content-between">
                                    @php
                                        use Carbon\Carbon;
                                        $age = $authUser->birthdate ? Carbon::parse($authUser->birthdate)->age : null;
                                    @endphp
                                    <h5 class="fts-14 fw-4 white-color70-n w-50">{{ __('messages.field_lbl_age') }}
                                    </h5>
                                    <p class="fts-14 fw-4 white-color-n">
                                        {{ $age ? $age . ' ' . __('messages.lbl_years') : '-' }}</p>
                                </li>
                                <li class="py-1 my-4 d-flex justify-content-between">
                                    <h5 class="fts-14 fw-4 white-color70-n w-50">{{ __('messages.field_lbl_city') }}
                                    </h5>
                                    <p class="fts-14 fw-4 white-color-n">{{ $authUser->cityData->city_name ?? 'N/A' }}
                                    </p>
                                </li>
                                <li class="py-1 mt-4 d-flex justify-content-between">
                                    <h5 class="fts-14 fw-4 white-color70-n w-50">
                                        {{ __('messages.field_lbl_marital_status') }}</h5>
                                    <p class="fts-14 fw-4 white-color-n">
                                        {{ $authUser->maritalStatusData->marital_status_name ?? 'N/A' }}</p>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6 px-2 ps-lg-4">
                            <div class="photo-upload-pro-matri mt-2 mt-lg-3">
                                @php
                                    $photos = [
                                        $authUser->photo1 ?? null,
                                        $authUser->photo2 ?? null,
                                        $authUser->photo3 ?? null,
                                        $authUser->photo4 ?? null,
                                    ];
                                @endphp

                                <div class="small-photo-upload-main d-flex flex-wrap gap-2">
                                    @foreach ($photos as $index => $photo)
                                        @if ($index % 2 == 0)
                                            <div class="d-flex gap-2 w-100">
                                        @endif

                                        @if (!blank($photo) && _checkStorageFileExists('upload_path.MEMBER_PHOTOS_URL', $photo))
                                            <label class="upload-box photo-upload-profile-pro">
                                                <img src="{{ _assetUrl('upload_path.MEMBER_PHOTOS_URL') . $photo }}"
                                                    alt="Photo {{ $index + 1 }}" class="w-100">
                                            </label>
                                        @endif

                                        @if ($index % 2 == 1)
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- Verify Email Modal --}}
<div class="customsmallmodel_light modal fade" id="sendEmailVerification" tabindex="-1"
    aria-labelledby="sendEmailVerificationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header mx-3 mx-lg-4 pt-4">
                <h1 class="fts-18 fw-6 white-color-n" id="sendEmailVerificationLabel">
                    {{ __('messages.lbl_verify_your_email') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
                </button>
            </div>
            <div class="px-3 px-lg-4 pt-3">
                <span class="fts-16 fw-5 white-color-n">
                    {{ __('messages.lbl_click_button_to_send_email_for_confirm_your_email_address') }}
                </span>
            </div>
            <div class="modal-buttonsGroup text-center px-3 px-lg-4 pb-4 mt-4">
                <button type="button" class="click-changeButton"
                    id="sendVerificationBtn">{{ __('messages.lbl_comfirm_email') }}</button>
            </div>
        </div>
    </div>
</div>

@include('web.dashboard.mobileVerificationModal')

<div id="afterMemberTop"></div>

@push('scripts')
    <script>
        $(window).on('load', function() {
            const $target = $('#afterMemberTop');

            if ($target.length) {
                $('html, body').animate({
                    scrollTop: $target.offset().top + $target.outerHeight()
                }, 500);
            }
        });

        // send verification btn :
        $(document).on('click', '#sendVerificationBtn', function() {
            let btn = $(this);
            btn.prop('disabled', true).text('{{ __('messages.lbl_sending_btn') }}');
            $.ajax({
                url: "{{ route('web.dashboard.sendConfirmationEmail') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    btn.prop('disabled', false).text("{{ __('messages.lbl_comfirm_email') }}");
                    showToastMessage('success', res.message);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text("{{ __('messages.lbl_comfirm_email') }}");
                    showToastMessage(
                        'error',
                        xhr.responseJSON?.message ??
                        '{{ __('messages.msg_unexpected_error_occured') }}'
                    );
                }
            });
        });
        
        // Handle Scroll for Navbar shadow or background change
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                $('.navbar').addClass('bg-white shadow-sm');
            } else {
                $('.navbar').removeClass('bg-white shadow-sm');
            }
        });
    </script>
@endpush

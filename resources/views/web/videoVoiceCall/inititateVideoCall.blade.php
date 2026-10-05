@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@push('styles')
    <style>
        /* --- container must have real height for the overlay to fill --- */
        .video-screen-area {
            position: relative;
            width: 100%;
            min-height: 400px;
            background: #111;
            border-radius: 12px;
            overflow: hidden;
        }

        .video-main-screen {
            position: relative;
            width: 100%;
            height: 100%;
            min-height: 400px;
        }

        /* --- the calling overlay --- */
        .zegocloud-connecting {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #0a061a;
            z-index: 5;
        }

        .calling-avatar-wrap {
            position: relative;
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }

        .calling-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            position: relative;
            z-index: 2;
            background: #ccc;
        }

        .ripple {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.6);
            transform: translate(-50%, -50%) scale(1);
            animation: rippleEffect 2s ease-out infinite;
            z-index: 1;
        }

        .ripple-2 {
            animation-delay: 0.6s;
        }

        .ripple-3 {
            animation-delay: 1.2s;
        }

        @keyframes rippleEffect {
            0% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
            }

            100% {
                transform: translate(-50%, -50%) scale(1.8);
                opacity: 0;
            }
        }

        .callconnect-pulse {
            margin-top: 40px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6a5af9, #d16ba5);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulseGlow 1.5s ease-in-out infinite;
        }

        @keyframes pulseGlow {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(106, 90, 249, 0.5);
            }

            50% {
                box-shadow: 0 0 0 14px rgba(106, 90, 249, 0);
            }
        }

        .calling-status {
            color: #fff;
            text-align: center;
        }

        .calling-status .dots::after {
            content: '';
            display: inline-block;
            width: 1em;
            text-align: left;
            animation: dotsLoading 1.4s steps(4, end) infinite;
        }

        @keyframes dotsLoading {
            0% {
                content: '';
            }

            25% {
                content: '.';
            }

            50% {
                content: '..';
            }

            75% {
                content: '...';
            }

            100% {
                content: '';
            }
        }

        .sCsSbKP9yxvw4LQAeaTz {
            max-height: 520px;
        }
    </style>
@endpush
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    
    <!-- video call section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2 pt-lg-3">
        <div class="common-section-page video-call-page">
            <div class="container">
                <div class="video-call-wrap">
                    <div class="video-screen-area position-relative">
                        <div class="UserCalLayers">
                            <div id="root" class="video-main-screen">
                                <div class="zegocloud-connecting">
                                    <div class="calling-avatar-wrap">
                                        <div class="ripple ripple-1"></div>
                                        <div class="ripple ripple-2"></div>
                                        <div class="ripple ripple-3"></div>
                                        <img src="{{ _getMemberProfileImage($receiver) }}"
                                            onerror="this.src='/assets/images/default-avatar.png'" class="calling-avatar"
                                            alt="">
                                    </div>

                                    <div class="callconnect-pulse">
                                        @if ($resultArr['callType'] == 'videoCall')
                                            <iconify-icon icon="mingcute:phone-call-line"
                                                class="fts-32 white-color-p"></iconify-icon>
                                        @else
                                            <iconify-icon icon="lucide:video" class="fts-32 white-color-p"></iconify-icon>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="video-call-actions d-flex align-items-center justify-content-between gap-2 gap-lg-3 mt-3">
                        <div class="caller-name-pill fts-16 fw-5 white-color-p">
                            {{ __('messages.lbl_calling') }} {{ _profileTitle($receiver) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <input type="hidden" name="send_call_member_id" id="send_call_member_id" value="{{ $receiver->matri_id }}">
    <input type="hidden" name="isVideoCall" id="isVideoCall" value="{{ $resultArr['isVideoCall'] }}">
    <input type="hidden" name="callType" id="callType" value="{{ $resultArr['callType'] }}">
    <input type="hidden" name="remainMinutes" id="remainMinutes" value="{{ $resultArr['remainMinutes'] }}">
    <input type="hidden" name="changeStatusUrl" id="changeStatusUrl"
        value="{{ route('web.videoVoiceCall.addCallMinutes') }}">
    <input type="hidden" name="connectAccept" id="connectAccept" value="{{ $resultArr['isConnect'] }}">

    <div class="customsmallmodel_light couponsize modal fade" id="videoCallModal" tabindex="-1"
        aria-labelledby="videoCallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                    <h2 class="fts-18 fw-6 white-color-n" id="callModalTitle"></h2>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><iconify-icon
                            icon="radix-icons:cross-2"></iconify-icon></button> --}}
                </div>
                <div class="modal_liteBody px-3 px-lg-4 py-3">
                    @if (!$resultArr['isVideoCall'])
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
                    @else
                        {{-- Alert Message --}}
                        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.common.alert_message')
                    @endif
                    <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('web.userProfile.index', _encrypt($receiver->id)) }}"
                                class="click-changeButton">{{ __('messages.lbl_back_to_profile') }}
                            </a>
                        @if (!$resultArr['isVideoCall'])
                            <a href="{{ route('web.membershipPlan.index') }}"
                                class="click-changeButton">{{ __('messages.lbl_upgrade_membership_now') }}</a>
                        @elseif (!$resultArr['isConnect'])
                            <a href="{{ route('web.userProfile.index', _encrypt($receiver->id)) }}"
                                class="click-changeButton">{{ __('messages.lbl_view_profile') }}</a>
                        @else
                            @if ($resultArr['callType'] == 'videoCall')
                                <a href="{{ route('web.videoVoiceCall.index') }}">
                                    <button type="button" class="click-changeButton"
                                        id="callBtn">{{ __('messages.lbl_call_list') }}</button>
                                </a>
                            @else
                                <a href="{{ route('web.videoVoiceCall.index') }}">
                                    <button type="button" class="click-changeButton"
                                        id="callBtn">{{ __('messages.lbl_call_list') }}</button>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Video Call Modal Popup Start -->
    @csrf
@endsection

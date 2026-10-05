@if (Auth::check() &&
        ($configArr['zego_video_call_setting'] == 'APPROVED' || $configArr['zego_voice_call_setting'] == 'APPROVED'))
    @php $currentMemberData = auth()->user(); @endphp
    <input type="hidden" name="loggedInUserId" id="loggedInUserId" value="{{ $currentMemberData['matri_id'] }}">


    @if (!blank($configArr['zegocloud_appid']))
        <input type="hidden" name="zegoAppId" id="zegoAppId" value="{{ $configArr['zegocloud_appid'] }}">
    @endif
    @if (!blank($configArr['zegocloud_server_secret_key']))
        <input type="hidden" name="zegoSecretKey" id="zegoSecretKey"
            value="{{ $configArr['zegocloud_server_secret_key'] }}">
    @endif

    <script src="https://unpkg.com/zego-zim-web@2.16.0/index.js" defer></script>
    <script src="https://unpkg.com/@zegocloud/zego-uikit-prebuilt/zego-uikit-prebuilt.js" defer></script>

    @push('scripts')
        <script>
            let intervalId;
            let remainMinutes = $('#remainMinutes').val();
            let callMinutes = 0;
            let validateCallTimerInterval;
            $(document).ready(function() {
                $('#videoCallModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                zegoInit();
                if ($("#connectAccept").length > 0 && $("#connectAccept").val() == "Yes") {
                    if ($("#send_call_member_id").length > 0 && $("#send_call_member_id").val() != "") {
                        if ($('#isVideoCall').val() == '1' && $('#remainMinutes').val() > 0) {
                            var callType = $('#callType').val();
                            setTimeout(function() {
                                if (callType == 'voiceCall') {
                                    sendVoiceCallRequest($("#send_call_member_id").val());
                                } else {
                                    sendVideoCallRequest($("#send_call_member_id").val());
                                }
                            }, 1000);
                        } else {
                            $('#videoCallModal').find('#callModalTitle').html(
                                '{{ __('messages.lbl_call_is_not_initiate_with') }} ' + $("#send_call_member_id")
                                .val());
                            showAlertMessage('error', '{{ __('messages.lbl_please_upgrade_your_membership_plan') }}',
                                '',
                                false);
                            $('#videoCallModal').modal('show');
                        }
                    }
                } else {
                    $('#videoCallModal').find('#callModalTitle').html(
                        '{{ __('messages.lbl_call_is_not_initiate_with') }} ' + $("#send_call_member_id").val());
                    if ($('#callType').val() == 'voiceCall') {
                        showAlertMessage('error',
                            '{{ __('messages.lbl_please_send_interest_first_or_if_already_sent_then_wait_while_accept_to_make_voice_call') }}',
                            '', false);
                    } else if ($('#callType').val() == 'videoCall') {
                        showAlertMessage('error',
                            '{{ __('messages.lbl_please_send_interest_first_or_if_already_sent_then_wait_while_accept_to_make_video_call') }}',
                            '', false);

                    }
                    $('#videoCallModal').modal('show');
                }
            });

            // Global Video Call Variables
            let zp;
            var callStartTimer = null;
            var callEndTimer = null;
            var callStartTime = "";
            var callEndTime = "";
            // var totalVideoSecond = "10";
            // var totalVoiceSecond = "10";

            // Initialize ZEGOCLOUD SDK

            function zegoInit() {
                try {
                    var zegoAppId = $("#zegoAppId").val();
                    const appID = Number(zegoAppId);
                    const serverSecret = $("#zegoSecretKey").val();
                    const userID = $("#loggedInUserId").val();
                    const userName = $("#loggedInUserId").val();
                    const logoUrl = $('.desktop-logo img').attr('src');

                    // Check if userID is valid
                    if (!userID) {
                        console.error("Error: userID is undefined or empty");
                        return;
                    }

                    // Generate KitToken for Test (or Production in actual use)
                    const KitToken = ZegoUIKitPrebuilt.generateKitTokenForTest(
                        // const KitToken = ZegoUIKitPrebuilt.generateKitTokenForProduction(
                        appID,
                        serverSecret,
                        null,
                        userID,
                        userName
                    );
                    // console.log('KitToken:'+ KitToken);
                    // Create ZEGOCLOUD instance
                    zp = ZegoUIKitPrebuilt.create(KitToken);

                    // Ensure plugins are added correctly
                    zp.addPlugins({
                        ZIM
                    });
                    zp.setCallInvitationConfig({
                        // The callback for the call invitation is accepted before joining the room (a room is used for making a call), which can be used to set up the room config. The Call Kit enables you to join the room automatically, and the room config adapts according to the specific call type (ZegoInvitationType).
                        onSetRoomConfigBeforeJoining: (callType) => {
                            return {
                                container: document.getElementById("root"),
                                showScreenSharingButton: false,
                                showAudioVideoSettingsButton: false,
                                // turnOnCameraWhenJoining: false,
                                showMyCameraToggleButton: false,
                                showRoomDetailsButton: false,
                                showTextChat: false,
                                showInviteToCohostButton: false,
                                showLeavingView: false,
                                showLeaveRoomConfirmDialog: false,
                                showPreJoinView: false,
                                showRoomTimer: true,
                                enableNotifyWhenAppRunningInBackgroundOrQuit: true,
                                branding: {
                                    logoURL: logoUrl
                                },
                            };
                        },
                        ringtoneConfig: {
                            incomingCallUrl: $('#base_url').val() +
                                '/storage/assets/commonImages/call_ringtone.mp3', // The ringtone when receiving an incoming call invitation.
                            outgoingCallUrl: $('#base_url').val() +
                                '/storage/assets/commonImages/call_ringtone.mp3', // The ringtone when sending a call invitation. 
                        },
                        // The callee will receive the notification through this callback when receiving a call invitation. User 2
                        onIncomingCallReceived: (callID, caller, callType, callees) => {
                            // alert('onIncomingCallReceived')
                        },

                        // The callee will receive the notification through this callback when the caller canceled the call invitation. User 2
                        onIncomingCallCanceled: (callID, caller) => {
                            // alert('onIncomingCallCanceled')
                        },

                        // The caller will receive the notification through this callback when the callee accepts the call invitation. User 1
                        onOutgoingCallAccepted: (callID, callee) => {
                            validateCallTimerInterval = setInterval(validateTimeInterval, 1000);
                        },

                        // The caller will receive the notification through this callback when the callee is on a call. User 1
                        onOutgoingCallRejected: (callID, callee) => {
                            // $('#videoCallModal').find('#callModalTitle').html('Member on another call <span class="primary-color-F">'+$("#send_call_member_id").val()+'</span>');
                            // $('#videoCallModal').find('#errorCallMessage').text('Member on another call, Please try again later!');
                            // $('#videoCallModal').find('.alert-success-msg').hide();
                            // $('#videoCallModal').find('.alert-error-msg').show();
                            // $('#videoCallModal').modal('show');
                        },

                        // The caller will receive the notification through this callback when the callee declines the call invitation. User 1
                        onOutgoingCallDeclined: (callID, callee) => {
                            // $('#videoCallModal').find('#callModalTitle').html('Call declined by <span class="primary-color-F">'+$("#send_call_member_id").val()+'</span>');
                            // $('#videoCallModal').find('#errorCallMessage').text('Call has been declined!');
                            // $('#videoCallModal').find('.alert-success-msg').hide();
                            // $('#videoCallModal').find('.alert-error-msg').show();
                            // $('#videoCallModal').modal('show');
                        },

                        // The callee will receive the notification through this callback when he didn't respond to the call invitation. User 2
                        onIncomingCallTimeout: (callID, caller) => {
                            // alert('onIncomingCallTimeout')
                        },

                        // The caller will receive the notification through this callback when the call invitation timed out. User 1
                        onOutgoingCallTimeout: (callID, callees) => {
                            // $('#videoCallModal').find('#callModalTitle').html('Call timeout by <span class="primary-color-F">'+$("#send_call_member_id").val()+'</span>');
                            // $('#videoCallModal').find('#errorCallMessage').text('Call has been timeout!');
                            // $('#videoCallModal').find('.alert-success-msg').hide();
                            // $('#videoCallModal').find('.alert-error-msg').show();
                            // $('#videoCallModal').modal('show');
                        },

                        // The callback for the call invitation ends (this will be triggered when the call invitation is refused/timed out/canceled/ended due to busy status.)
                        onCallInvitationEnded: (reason, data) => {
                            if ($("#send_call_member_id").length > 0 && $("#send_call_member_id").val() != "") {
                                var reasonType = 'error';
                                if (reason == 'Busy') {
                                    $('#videoCallModal').find('#callModalTitle').html(
                                        '{{ __('messages.lbl_member_on_another_call') }} ' + $(
                                            "#send_call_member_id").val());
                                    showAlertMessage('error',
                                        '{{ __('messages.lbl_member_on_another_call_please_try_again_later') }}',
                                        '',
                                        false);
                                } else if (reason == 'Timeout') {
                                    $('#videoCallModal').find('#callModalTitle').html(
                                        '{{ __('messages.lbl_call_timeout_by') }} ' + $("#send_call_member_id")
                                        .val());
                                    showAlertMessage('error', '{{ __('messages.lbl_call_has_been_timeout') }}', '',
                                        false);
                                } else if (reason == 'Declined') {
                                    $('#videoCallModal').find('#callModalTitle').html(
                                        '{{ __('messages.lbl_call_declined_by') }} ' + $(
                                            "#send_call_member_id").val());
                                    showAlertMessage('error', '{{ __('messages.lbl_call_has_been_declined') }}',
                                        '', false);
                                } else if (reason == 'Canceled') {
                                    $('#videoCallModal').find('#callModalTitle').html(
                                        '{{ __('messages.lbl_call_cancelled_with') }}' + $(
                                            "#send_call_member_id").val());
                                    showAlertMessage('error', '{{ __('messages.lbl_call_has_been_canceled') }}',
                                        '', false);
                                } else if (reason == 'LeaveRoom') {
                                    var reasonType = 'success';
                                    if (remainMinutes == callMinutes) {
                                        var reasonType = 'error';
                                        var baseUrl = $('#base_url').val();
                                        var url = baseUrl + '/membership-plan';
                                        $('#videoCallModal').find('#callModalTitle').html(
                                            '{{ __('messages.lbl_call_ended_with') }} ' + $(
                                                "#send_call_member_id").val());
                                        showAlertMessage('error',
                                            '{{ __('messages.lbl_your_call_session_has_ended_as_the_allotted_time_has_expired_please_upgrade_your_membership_plan') }}',
                                            '', false);
                                        $('#callBtn').text('{{ __('messages.lbl_okay') }}');
                                        $('#callBtn').attr('href', url);
                                    } else {
                                        $('#videoCallModal').find('#callModalTitle').html(
                                            '{{ __('messages.lbl_call_ended_with') }} ' + $(
                                                "#send_call_member_id").val());
                                        showAlertMessage('success',
                                            '{{ __('messages.lbl_call_has_been_ended') }}!', '', false);
                                    }
                                } else {
                                    $('#videoCallModal').find('#callModalTitle').html(
                                        'Something went wrong while calling ' + $("#send_call_member_id").val());
                                    showAlertMessage('error', '{{ __('messages.msg_something_went_wrong') }}', '',
                                        false);
                                }
                                $('#videoCallModal').modal('show');
                                // Save Call History :
                                var formData = new FormData();
                                formData.append('active_call_minute', callMinutes);
                                formData.append('end_reason', reason);
                                formData.append('sender_matri_id', $("#loggedInUserId").val());
                                formData.append('receiver_matri_id', $("#send_call_member_id").val());
                                formData.append('type', $('#callType').val());
                                saveCallHistory(formData);
                            }
                        },

                        // When the callee user clicks the accept button on the call invitation popup, the callee will receive this callback. User 1
                        onIncomingCallAcceptButtonPressed: () => {
                            // alert('onIncomingCallAcceptButtonPressed')
                        },

                        // When the callee user clicks the decline button on the call invitation popup, the callee will receive this callback. User 1
                        onIncomingCallDeclineButtonPressed: () => {
                            // alert('onIncomingCallDeclineButtonPressed')
                        },
                    });
                    // console.log("ZEGOCLOUD instance created successfully");
                } catch (error) {
                    console.error("Error during SDK initialization:", error);
                }
            }

            // Function to Handle Video Call
            function handleSend(callType, callee) {
                if (!callee) {
                    alert("userID cannot be empty!!");
                    return;
                }
                // Send call invitation
                zp.sendCallInvitation({
                        callees: [{
                            userID: callee,
                            userName: callee
                        }],
                        callType: callType,
                        timeout: 15,
                        notificationConfig: {
                            resourcesID: 'zegouikit_call',
                            title: 'Call invitation',
                            message: 'Incoming video call from ' + $("#loggedInUserId").val()
                        }
                    })
                    .then((res) => {
                        console.warn(res);
                        if (res.errorInvitees.length) {
                            // alert("The user does not exist or is offline.");
                            $('#videoCallModal').find('#callModalTitle').html(
                                '{{ __('messages.lbl_user_does_not_exist_or_is_offline') }}');
                            showAlertMessage('error',
                                '{{ __('messages.lbl_the_user_does_not_exist_or_is_offline_please_try_again_later') }}',
                                '',
                                false);
                            $('#videoCallModal').modal('show');
                        }
                    })
                    .catch((err) => {
                        console.error("Error sending call invitation:", err);
                        $('#videoCallModal').find('#callModalTitle').html('Error sending call invitation');
                        showAlertMessage('error',
                            '{{ __('messages.lbl_error_sending_call_invitation_please_try_again_later') }}', '', false);
                        $('#videoCallModal').modal('show');
                    });
            }

            // Function to Check call invite waiting :
            function validateTimeInterval() {
                if (remainMinutes <= callMinutes) {
                    stopValidateInterval();
                    return false;
                }
                callMinutes++;
            }

            // Function to stop the interval after 10 seconds
            function stopValidateInterval() {
                $('.QeMJj1LEulq1ApqLHxuM').trigger('click');
                clearInterval(validateCallTimerInterval);
            }

            // Wrapper to Send Video Call Request
            function sendVideoCallRequest(callee) {
                handleSend(ZegoUIKitPrebuilt.InvitationTypeVideoCall, callee);
            }

            // Wrapper to Send Voice Call Request
            function sendVoiceCallRequest(callee) {
                handleSend(ZegoUIKitPrebuilt.InvitationTypeVoiceCall, callee);
            }

            function saveCallHistory(formData, element = null, responseFunction = null) {
                var url = $('#changeStatusUrl').val();
                formData.append('isPost', 1);
                formData.append('_token', $("input[name=_token]").val());
                $("#overlay").fadeIn(200);
                $.ajax({
                    url: url,
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(responseData) {
                        if (responseData.redirectUrl) {
                            window.location.href = responseData.redirectUrl;
                            return;
                        }
                        if (responseFunction && typeof window[responseFunction] === 'function') {
                            window[responseFunction](element, responseData);
                        }
                    },
                    error: function(jqXHR) {
                        if (element) $(element).prop('disabled', false);

                        if (jqXHR.status === 422) {
                            console.log('Validation error', jqXHR.responseJSON);
                        } else {
                            console.log('Server error');
                        }
                    },

                    complete: function() {
                        $("#overlay").fadeOut(300);
                    }
                });
            }
        </script>

        <script>
            $('#videoCallModal').on('hidden.bs.modal', function() {
                window.history.back();
            });
        </script>
    @endpush
@endif

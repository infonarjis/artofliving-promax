@if (isset($getMessageList) && !empty($getMessageList))
    @foreach ($getMessageList as $valueArr)
        @if ($valueArr->sender_type == 2)
            <div class="receive_messages-div my-md-3 my-2 d-flex gap-2 gap-lg-3 align-items-start">
                <div class="receive-messages">
                    <div class="receive-single-msg">
                        @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                            {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                        @else
                            {{ $valueArr->message }}
                        @endif
                        <span class="receive_msg_time">{{ _displayDate($valueArr->created_at, 'j M, y g:i A') }}</span>
                    </div>
                </div>
            </div>
        @endif
        @if ($valueArr->sender_type == 1)
            <div class="send_messages-div my-md-3 my-2 d-flex align-items-sm-start justify-content-end gap-2 gap-lg-3">
                <div class="send-messages">
                    <div class="send-single-msg">
                        @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                            {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                        @else
                            {{ $valueArr->message }}
                        @endif
                        <span class="send_msg_time">{{ _displayDate($valueArr->created_at, 'j M, y g:i A') }}</span>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@else
    <div class="more_conversation_btn mt-2">
        No More Conversation Found ...
    </div>
@endif

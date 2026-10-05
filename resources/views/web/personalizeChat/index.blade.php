@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <!-- dashboard section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="dashboard-layout">
                    {{-- LEFT SIDEBAR (Responsive Off-Canvas Drawer on Mobile) --}}
                    @include('web.dashboard.memberLeftSideBar')

                    <main class="main-content-area" id="main-content">
                        @include('web.dashboard.memberTop')
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 mt-3">
                                <div class="common-bgwhite-main p-3 p-lg-4">
                                    <div class="inner_chatspanelmng">
                                        <div class="chat_headers-lites">
                                            <div class="header-left_user d-flex align-items-center gap-2">
                                                <button class="back-chatbtn" type="button">
                                                    <iconify-icon icon="clarity:administrator-solid"></iconify-icon>
                                                </button>
                                                <div class="name-statususer ms-1">
                                                    <div class="username fts-18">
                                                        {{ __('messages.lbl_match_maker_administrator') }}</div>
                                                    {{-- <div class="status-usersview online">
                                                        <span class="fts-12 fw-5 white-color70-n">Online</span>
                                                    </div> --}}
                                                </div>
                                            </div>
                                            <div class="header-rightcalled d-flex gap-2">
                                                <button class="call-voice-video" onclick="fetchMessages()">
                                                    <iconify-icon icon="ci:arrow-reload-02"></iconify-icon>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="chats_middlesScreen-main" id="resultData">
                                            {{-- @include(_getConstant('dir_path.WEB_DIR_PATH').'.personalizeChat.ajax_result') --}}
                                        </div>
                                        <div class="chats_bottomsmaindivs d-flex align-items-end gap-2">
                                            <div class="send-msgboxed-mn position-relative w-100">
                                                <textarea id="textareaincrease" class="typing-msgdiv" placeholder="Type a message"></textarea>
                                                <button type="button" id="sendMessageBtn" class="send-msgbtn">
                                                    <iconify-icon icon="iconamoon:send-light"></iconify-icon>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {

            // Function to send message
            function sendMessage() {
                let message = $('#textareaincrease').val().trim();
                if (message.length == 0) return;

                $.ajax({
                    url: "{{ route('web.personalizeChat.sendMessage') }}",
                    type: 'POST',
                    data: {
                        message: message,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        $('#textareaincrease').val('');
                        fetchMessages();
                    },
                    error: function(err) {
                        alert('Error sending message.');
                    }
                });
            }

            // Send message on button click
            $('#sendMessageBtn').on('click', function() {
                sendMessage();
            });

            // Send message on Enter key press
            $('#textareaincrease').on('keypress', function(e) {
                if (e.which === 13 && !e.shiftKey) { // Enter without Shift
                    e.preventDefault(); // Prevent new line
                    sendMessage();
                }
            });

            // Auto-refresh every 5 seconds
            setInterval(fetchMessages, 2000);

            // Scroll to bottom initially
            fetchMessages();
        });
        // Fetch messages
        function fetchMessages() {
            $.ajax({
                url: "{{ route('web.personalizeChat.getMessages') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(html) {
                    $('#resultData').html(html);
                    $('#resultData').scrollTop($('#resultData')[0].scrollHeight);
                }
            });
        }
    </script>
@endpush

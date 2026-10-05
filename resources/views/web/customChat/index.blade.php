@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <!-- chat list section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="dashboard-layout">
                    {{-- LEFT SIDEBAR (Responsive Off-Canvas Drawer on Mobile) --}}
                    @include('web.dashboard.memberLeftSideBar')

                    <main class="main-content-area" id="main-content">
                        @include('web.dashboard.memberTop')
                        <div class="common-bgwhite-main p-3 p-lg-4">
                            <div class="common-tabs-design">
                                <ul class="nav flex-nowrap d-flex nav-pills" id="pills-tab" role="tablist">
                                    <li class="nav-item w-50">
                                        <button class="nav-link fts-15 active" id="recentChat-tab" data-bs-toggle="pill"
                                            data-bs-target="#recentChat" type="button" role="tab"
                                            aria-controls="recentChat"
                                            aria-selected="true">{{ __('messages.lbl_recent_chat') }}</button>
                                    </li>
                                    <li class="nav-item w-50">
                                        <button class="nav-link fts-15" id="onlineMembers-tab" data-bs-toggle="pill"
                                            data-bs-target="#onlineMembers" type="button" role="tab"
                                            aria-controls="onlineMembers"
                                            aria-selected="false">{{ __('messages.lbl_online_members') }}</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="common-tablist-design mt-3">
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="recentChat" role="tabpanel"
                                        aria-labelledby="recentChat" data-url="{{ route('web.chat.getChatList') }}">
                                        <!-- Chat List Container -->
                                        <div class="tab-content-area">
                                            {{-- Ajax Html Add Here --}}
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="onlineMembers" role="tabpanel"
                                        aria-labelledby="onlineMembers-tab"
                                        data-url="{{ route('web.chat.onlineMembers') }}">
                                        <!-- Chat List Container -->
                                        <div class="tab-content-area">
                                            {{-- Ajax Html Add Here --}}
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
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>

    <script>
        {!! $configArr['firebase_configuration'] !!}
        firebase.initializeApp(firebaseConfig);

        const db = firebase.database();

        window.currentUserId = @json($authUser->id);

        $(document).ready(function() {

            function loadTab(tabPane, force = false) {
                if (!tabPane || tabPane.length === 0) return;

                let url = tabPane.data('url');
                if (!url) return;

                if (tabPane.data('loaded') && !force) return;

                const content = tabPane.find('.tab-content-area');

                $.ajax({
                    url: url,
                    type: "GET",
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-Tab-Ajax': 'chat-tabs'
                    },
                    beforeSend: function() {
                        // Only show the spinner on first load, not on background refresh
                        if (!force) {
                            content.html(`
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary"></div>
                                </div>
                            `);
                        }
                    },
                    success: function(response) {
                        content.html(response.html);
                        tabPane.data('loaded', true);

                        if (response.type == 'recent_chat' && Array.isArray(response.data) && response
                            .data.length) {
                            // Batch all chat_status writes into a single multi-path update
                            const updates = {};
                            response.data.forEach(chat => {
                                updates[
                                        `chat_conversations_users/${chat.conversation_id}/${window.currentUserId}/chat_status`
                                        ] =
                                    1;
                            });
                            db.ref().update(updates);
                        }
                    },
                    error: function() {
                        content.html(`
                            <div class="text-danger text-center py-5">
                                Error loading data
                            </div>
                        `);
                    }
                });
            }

            // Load default active tab
            loadTab($('#pills-tabContent .tab-pane.active'));

            // Start listening for live chat updates (always resolves the CURRENT active tab)
            listenFirebase(1);

            // On tab change
            $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
                let targetId = $(e.target).data('bs-target');
                loadTab($(targetId));
            });

            function listenFirebase(isSkipped = 0) {
                var chatRef = db.ref('chat_last_message/' + window.currentUserId);

                function handleUpdate(snapshot) {
                    if (isSkipped == 1) {
                        isSkipped = 0;
                        return;
                    }
                    // Always resolve the active pane at the moment the event fires,
                    // instead of closing over whichever pane was active on page load.
                    const activePane = $('#pills-tabContent .tab-pane.active');
                    loadTab(activePane, true);
                }

                chatRef.on('child_added', handleUpdate);
                chatRef.on('child_changed', handleUpdate);

                // Detach listeners on page unload to avoid leaks/duplicate bindings
                $(window).on('beforeunload', function() {
                    chatRef.off('child_added', handleUpdate);
                    chatRef.off('child_changed', handleUpdate);
                });
            }

        });
    </script>
@endpush

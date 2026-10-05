@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="chat_personalize_main">
            <div class="personalize_chat-inner d-flex p-2">
                <div class="left_chatlistpanel">
                    <div class="top_header--chats px-3 py-3 d-flex align-items-center gap-2">
                        <div class="chat_topbar w-100">
                            <i class="bx bx-search"></i>
                            <input type="search" name="searchKeyword" id="searchKeyword" placeholder="Searching...."
                                class="search_id-input">
                        </div>
                        <div class="chat_backpanels" id="toggle_chatdata">
                            <div class="arrow_toggle">
                            </div>
                        </div>
                    </div>
                    <div class="chat_listpanel__main px-2" id="recentChatsList">
                        @include('admin.personalizeChat.recentChatMemberList')
                    </div>
                </div>

                <div class="chats-listingschats-box py-2 px-2 px-lg-3" id="chatConversation">
                    <div class="row">
                        <div class="col-lg-2"></div>
                        <div class="col-lg-8 mt-4">
                            <div class="alert alert-primary text-center" role="alert">
                                Start Your Conversation To Personalize User
                            </div>
                        </div>
                        <div class="col-lg-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @csrf
    <input type="hidden" name="page" id="page" value="1">
@endsection

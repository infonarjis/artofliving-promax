@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('admin.message')
        <div class="d-flex align-items-start row">
            <!-- LEFT TABS -->
            <div class="nav flex-column nav-pills px-3 py-2 col-md-3 mb-3" role="tablist">
                {{-- <button class="nav-link text-start active" data-bs-toggle="pill"
                    data-bs-target="#website_layout_settings_1">
                    <i class="bx bx-toggle-left me-2"></i>
                    Feature Controls
                </button> --}}
                <button class="nav-link text-start active" data-bs-toggle="pill"
                    data-bs-target="#website_layout_settings_2">
                    <i class="bx bx-message-square-detail me-2"></i>
                    Chat Layout Setting
                </button>
            </div>

            <!-- RIGHT CONTENT -->
            <div class="tab-content col-md-9">
                {{-- <div class="tab-pane fade show active" id="website_layout_settings_1">
                    <form method="POST" action="{{ route('admin.otherWebsiteLayout.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $featuresControlAddEditForm !!}
                                </div>
                                <input type="hidden" name="lang_id" id="lang_id" value="">
                                <input type="hidden" name="lang_code" id="lang_code" value="{{ _getDefaultLanguage() }}">
                                <button type="submit" class="btn btn-primary mt-3">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div> --}}
                <div class="tab-pane fade show active" id="website_layout_settings_2">
                    <form method="POST" action="{{ route('admin.otherWebsiteLayout.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $chatModuleAddEditForm !!}
                                </div>
                                <input type="hidden" name="lang_id" id="lang_id" value="">
                                <input type="hidden" name="lang_code" id="lang_code" value="{{ _getDefaultLanguage() }}">
                                <button type="submit" class="btn btn-primary mt-3">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Styling to make vertical tabs look more integrated */
        .nav-pills .nav-link {
            border-radius: 0.375rem;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            background: white;
            color: #697a8d;
            transition: all 0.2s;
        }

        .nav-pills .nav-link.active {
            background: #024959;
            color: white !important;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .tab-content {
                border-left: none;
                padding-left: 0;
                margin-top: 1.5rem;
            }
        }
    </style>
@endsection

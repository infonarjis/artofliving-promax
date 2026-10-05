@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    @php
        $activeTab = session('active_tab', 'affiliate_homepagesection_1');
    @endphp
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Language Change -->
        @if (_getConstant('LANGUAGE_MODE') == 'Enabled')
            @if (isset($mode) && $mode != 'add')
                <div class="row">
                    <div class="col-xl">
                        <div class="card mb-3">
                            <div class="card-body lang-dropdown">
                                <div class="row g-6">
                                    <div class="col-md-6">
                                        <div class="row mb-6">
                                            <label class="col-sm-5 col-form-label" for="basic-default-name">Language
                                                Change</label>
                                            <div class="col-sm-7">
                                                <select required="" class="form-select required" id="lang_change"
                                                    name="lang_change" aria-label="Select Lang Change">
                                                    @foreach ($languageDataArr as $key => $value)
                                                        <option
                                                            data-action="{{ route('admin.affiliateHomepage.getLangData') }}"
                                                            data-id="{{ $id }}" value="{{ $value->lang_code }}">
                                                            {{ $value->lang_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
        @include('admin.message')
        <div class="d-flex align-items-start row">
            <!-- LEFT TABS -->
            <div class="nav flex-column nav-pills px-3 py-2 col-md-3 mb-3" role="tablist">
                <button class="nav-link text-start {{ $activeTab === 'affiliate_homepagesection_1' ? 'active' : '' }}" data-bs-toggle="pill"
                    data-bs-target="#affiliate_homepagesection_1">
                    Banner Section
                </button>
                <button class="nav-link text-start {{ $activeTab === 'affiliate_homepagesection_2' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#affiliate_homepagesection_2">
                    How Its Works Section
                </button>
                <button class="nav-link text-start {{ $activeTab === 'affiliate_homepagesection_3' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#affiliate_homepagesection_3">
                    Why Choose Us
                </button>
                <button class="nav-link text-start {{ $activeTab === 'affiliate_homepagesection_4' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#affiliate_homepagesection_4">
                    Why Affiliate With Us
                </button>
                <button class="nav-link text-start {{ $activeTab === 'affiliate_homepagesection_5' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#affiliate_homepagesection_5">
                    Affiliate Features
                </button>
                <button class="nav-link text-start {{ $activeTab === 'affiliate_homepagesection_6' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#affiliate_homepagesection_6">
                    Testimonial
                </button>
            </div>

            <!-- RIGHT CONTENT -->
            <div class="tab-content col-md-9">
                <div class="tab-pane fade {{ $activeTab === 'affiliate_homepagesection_1' ? 'show active' : '' }}" id="affiliate_homepagesection_1">
                    <form method="POST" action="{{ route('admin.affiliateHomepage.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="active_tab" value="affiliate_homepagesection_1">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $bannerSection !!}
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
                <div class="tab-pane fade {{ $activeTab === 'affiliate_homepagesection_2' ? 'show active' : '' }}" id="affiliate_homepagesection_2">
                    <form method="POST" action="{{ route('admin.affiliateHomepage.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="active_tab" value="affiliate_homepagesection_2">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $affiliateHowItsWorks !!}
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
                <div class="tab-pane fade {{ $activeTab === 'affiliate_homepagesection_3' ? 'show active' : '' }}" id="affiliate_homepagesection_3">
                    <form method="POST" action="{{ route('admin.affiliateHomepage.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="active_tab" value="affiliate_homepagesection_3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $affiliatewhychooseus !!}
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
                <div class="tab-pane fade {{ $activeTab === 'affiliate_homepagesection_4' ? 'show active' : '' }}" id="affiliate_homepagesection_4">
                    <form method="POST" action="{{ route('admin.affiliateHomepage.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="active_tab" value="affiliate_homepagesection_4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $affiliatewhyaffiliatewithus !!}
                                </div>
                                <input type="hidden" name="lang_id" id="lang_id" value="">
                                <input type="hidden" name="lang_code" id="lang_code"
                                    value="{{ _getDefaultLanguage() }}">
                                <button type="submit" class="btn btn-primary mt-3">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade {{ $activeTab === 'affiliate_homepagesection_5' ? 'show active' : '' }}" id="affiliate_homepagesection_5">
                    <form method="POST" action="{{ route('admin.affiliateHomepage.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="active_tab" value="affiliate_homepagesection_5">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $affiliatefeatures !!}
                                </div>
                                <input type="hidden" name="lang_id" id="lang_id" value="">
                                <input type="hidden" name="lang_code" id="lang_code"
                                    value="{{ _getDefaultLanguage() }}">
                                <button type="submit" class="btn btn-primary mt-3">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade {{ $activeTab === 'affiliate_homepagesection_6' ? 'show active' : '' }}" id="affiliate_homepagesection_6">
                    <form method="POST" action="{{ route('admin.affiliateHomepage.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="active_tab" value="affiliate_homepagesection_6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $affiliateTestimonials !!}
                                </div>
                                <input type="hidden" name="lang_id" id="lang_id" value="">
                                <input type="hidden" name="lang_code" id="lang_code"
                                    value="{{ _getDefaultLanguage() }}">
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

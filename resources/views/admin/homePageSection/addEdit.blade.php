@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')
    @php
        $activeTab = session('active_tab', 'homepage_section_1');
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
                                                            data-action="{{ route('admin.homePageSection.getLangData') }}"
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
                <button class="nav-link text-start {{ $activeTab === 'homepage_section_1' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#homepage_section_1">
                    Header Section
                </button>
                <button class="nav-link text-start {{ $activeTab === 'homepage_section_2' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#homepage_section_2">
                    Why Choose Us Section
                </button>
                <button class="nav-link text-start {{ $activeTab === 'homepage_section_3' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#homepage_section_3">
                    About Us Section
                </button>
                <button class="nav-link text-start {{ $activeTab === 'homepage_section_4' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#homepage_section_4">
                    Mobile Banner & Text Section
                </button>
                <button class="nav-link text-start {{ $activeTab === 'homepage_section_5' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#homepage_section_5">
                    Personalize Section
                </button>
                <button class="nav-link text-start {{ $activeTab === 'homepage_section_6' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#homepage_section_6">
                    Other Section Title & Text
                </button>
            </div>

            <!-- RIGHT CONTENT -->
            <div class="tab-content col-md-9">
                <div class="tab-pane fade {{ $activeTab === 'homepage_section_1' ? 'show active' : '' }}" id="homepage_section_1">
                    <form method="POST" action="{{ route('admin.homePageSection.update') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="active_tab" value="homepage_section_1">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $homepageTextAddEditForm !!}
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
                <div class="tab-pane fade {{ $activeTab === 'homepage_section_2' ? 'show active' : '' }}" id="homepage_section_2">
                    <form method="POST" action="{{ route('admin.homePageSection.update') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="active_tab" value="homepage_section_2">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $whyChooseUsAddEditForm !!}
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
                <div class="tab-pane fade {{ $activeTab === 'homepage_section_3' ? 'show active' : '' }}" id="homepage_section_3">
                    <form method="POST" action="{{ route('admin.homePageSection.update') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="active_tab" value="homepage_section_3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $aboutUsAddEditForm !!}
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
                <div class="tab-pane fade {{ $activeTab === 'homepage_section_4' ? 'show active' : '' }}" id="homepage_section_4">
                    <form method="POST" action="{{ route('admin.homePageSection.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="active_tab" value="homepage_section_4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $mobileSectionAddEditForm !!}
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
                <div class="tab-pane fade {{ $activeTab === 'homepage_section_5' ? 'show active' : '' }}" id="homepage_section_5">
                    <form method="POST" action="{{ route('admin.homePageSection.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="active_tab" value="homepage_section_5">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $personalizeSectionAddEditForm !!}
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
                <div class="tab-pane fade {{ $activeTab === 'homepage_section_6' ? 'show active' : '' }}" id="homepage_section_6">
                    <form method="POST" action="{{ route('admin.homePageSection.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="active_tab" value="homepage_section_6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $otherSectionAddEditForm !!}
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
@endsection

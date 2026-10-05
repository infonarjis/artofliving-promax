@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')
    @php
        $tabKeys = array_keys($formsByTab);
        $activeTab = session('active_tab', $tabKeys[0] ?? null);
    @endphp
    <div class="container-xxl flex-grow-1 container-p-y">
        @if (_getConstant('LANGUAGE_MODE') == 'Enabled')
            <div class="row">
                <div class="col-xl">
                    <div class="card mb-3">
                        <div class="card-body lang-dropdown">
                            <div class="row g-6">
                                <div class="col-md-6">
                                    <div class="row mb-6">
                                        <label class="col-sm-5 col-form-label">Language Change</label>
                                        <div class="col-sm-7">
                                            <select required class="form-select required" id="lang_change" name="lang_change">
                                                @foreach ($languageDataArr as $value)
                                                    <option
                                                        data-action="{{ route('admin.homePageDesign.getLangData') }}"
                                                        data-id="{{ $design->id }}" value="{{ $value->lang_code }}">
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

        @include('admin.message')

        {{-- <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('admin.homePageDesign.manageFields', $design->id) }}" class="btn btn-outline-primary btn-sm">
                <i class="bx bx-list-plus"></i> Manage Fields
            </a>
        </div> --}}

        <div class="d-flex align-items-start row">
            <div class="nav flex-column nav-pills px-3 py-2 col-md-3 mb-3" role="tablist">
                @foreach($formsByTab as $tabKey => $tab)
                    <button class="nav-link text-start {{ $activeTab === $tabKey ? 'active' : '' }}"
                        data-bs-toggle="pill" data-bs-target="#tab_{{ $tabKey }}">
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </div>

            <div class="tab-content col-md-9">
                @foreach($formsByTab as $tabKey => $tab)
                    <div class="tab-pane fade {{ $activeTab === $tabKey ? 'show active' : '' }}" id="tab_{{ $tabKey }}">
                        <form method="POST" action="{{ route('admin.homePageDesign.update', $design->id) }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="active_tab" value="{{ $tabKey }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        {!! $tab['html'] !!}
                                    </div>
                                    <input type="hidden" name="lang_id" value="">
                                    <input type="hidden" name="lang_code" class="lang_code_field" value="{{ _getDefaultLanguage() }}">
                                    <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

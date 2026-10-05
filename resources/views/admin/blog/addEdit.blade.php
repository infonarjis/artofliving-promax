@extends(_getConstant('dir_path.ADMIN_DIR_PATH').'.admin_layout')
@section('admin_content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Language Change -->
    @if(_getConstant('LANGUAGE_MODE') == 'Enabled')
        @if(isset($mode) && $mode != 'add')
            <div class="row">
                <div class="col-xl">
                    <div class="card mb-3">
                        <div class="card-body lang-dropdown">
                            <div class="row g-6">
                                <div class="col-md-6">
                                    <div class="row mb-6">
                                        <label class="col-sm-5 col-form-label" for="basic-default-name">Language Change</label>
                                        <div class="col-sm-7">
                                            <select required="" class="form-select required" id="lang_change" name="lang_change" aria-label="Select Lang Change">
                                                @foreach($languageDataArr as $key=>$value)
                                                    <option data-action="{{ route('admin.blog.getLangData') }}" data-id="{{ $id }}" value="{{ $value->lang_code }}">{{ $value->lang_name }}</option>
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
    <!-- Language Change -->
    @include('admin.message')
    <!-- Basic Layout -->
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-body">
                    <form id="{{ $formId }}" name="{{ $formName }}" action="{{ route($formUrl) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row g-6">
                            <?php echo $fromHtml; ?>
                        </div>
                        <input type="hidden" name="lang_id" id="lang_id" value="">
                        <input type="hidden" name="lang_code" id="lang_code" value="{{ _getDefaultLanguage() }}">
                        <button type="submit" class="btn btn-primary {{ $formSubmitBtnClass }}"
                            id="{{ $formSubmitBtnId }}">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

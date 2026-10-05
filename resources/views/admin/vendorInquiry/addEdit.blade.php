@extends(_getConstant('dir_path.ADMIN_DIR_PATH').'.admin_layout')
@section('admin_content')
<div class="container-xxl flex-grow-1 container-p-y">
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
                        <button type="submit" class="btn btn-primary {{ $formSubmitBtnClass }}"
                            id="{{ $formSubmitBtnId }}">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

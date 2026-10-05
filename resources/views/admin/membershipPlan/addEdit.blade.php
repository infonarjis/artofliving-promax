@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
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
@push('scripts')
    <script>
        $(document).ready(function() {
            function togglePlanTypeFields() {
                const planType = $('input[name="plan_type"]:checked').val();
                if (planType === 'FREE') {
                    $('.currency_code, .plan_amount, .plan_discount').hide();
                    // Remove required attribute for FREE plan
                    $('#currency_code, #plan_amount, #plan_discount').prop('required', false).val('');
                    // Reset Select2
                    $('#currency_code').val('').trigger('change');
                } else {
                    $('.currency_code, .plan_amount, .plan_discount').show();
                    // Add required attribute for PAID plan
                    $('#currency_code, #plan_amount, #plan_discount').prop('required', true);
                }
            }
            // On page load
            togglePlanTypeFields();
            // On plan type change
            $('input[name="plan_type"]').on('change', function() {
                togglePlanTypeFields();
            });

        });
    </script>
@endpush

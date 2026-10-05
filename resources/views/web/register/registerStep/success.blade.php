@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <!-- Event checkout Success section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="common-bgwhite-main p-2 p-lg-4">
                        <div class="checkout_mainpage">
                            <div class="payment-innnerstyle text-center py-2">
                                <img src="{{ asset('storage/web/') }}/assets/images/register-done.png" alt="Payment Success" class="paymet-icon">
                                <h4 class="mt-3 fts-20 fw-7 white-color-n">{{ __('messages.lbl_registration_successful') }} </h4>
                                <p class="mt-1 fw-4 white-color-n fts-14">
                                    {{ __('messages.lbl_registration_successful_description') }}
                                </p>
                                <div class="d-flex justify-content-center mt-2">
                                    <a href="{{ route('web.login.index') }}" class="comman-bg-btn fts-14">{{ __('messages.lbl_login') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

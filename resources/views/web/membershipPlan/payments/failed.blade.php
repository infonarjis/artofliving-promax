@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('title', 'Payment Failed')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    
    <!-- Event checkout Success section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="common-bgwhite-main p-2 p-lg-4">
                        <div class="checkout_mainpage">
                            <div class="payment-innnerstyle text-center py-2">
                                <img src="{{ asset('storage/web/') }}/assets/images/payment-fail-icon.png" alt=""
                                    class="paymet-icon">
                                <div class="mt-3 fts-18 fw-7 white-color-n">{{ __('messages.lbl_payment_failed') }}</div>
                                <div class="mt-1 white-color70-n fw-4 fts-14">
                                    {{ $error ?? __('messages.lbl_something_went_wrong_with_your_payment') }}
                                </div>
                                <a href="{{ route('web.membershipPlan.index') }}" class="paymentbuttons-check fail">
                                    {{ __('messages.lbl_back_to_plans') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

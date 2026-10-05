@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('title', 'Payment Success')
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
                                <img src="{{ asset('storage/web/') }}/assets/images/payment-success-icon.png" alt=""
                                    class="paymet-icon">
                                <div class="mt-3 fts-18 fw-7 white-color-n">{{ __('messages.lbl_payment_successful') }}
                                </div>
                                <div class="mt-1 fw-4 white-color70-n fts-14">
                                    <p>{{ __('messages.lbl_payment_successful_msg') }}</p>
                                </div>

                                <div class="bank-details-content mt-3 mt-lg-4">
                                    <p class="fts-14 white-color-n fw-4 mb-2 mb-lg-3">
                                        {{ __('messages.lbl_plan_name') }} : {{ $payment->plan_name }}
                                    </p>
                                    <p class="fts-14 white-color-n fw-4 mb-2 mb-lg-3">
                                        {{ __('messages.lbl_paid_amount') }} : {{ $payment->grand_total }}
                                    </p>
                                    <p class="fts-14 white-color-n fw-4 mb-2 mb-lg-3">
                                        {{ __('messages.lbl_transaction_id') }} : {{ $payment->transaction_id }}
                                    </p>
                                    <p class="fts-14 white-color-n fw-4 mb-2 mb-lg-3">
                                        {{ __('messages.lbl_valid_until') }} :
                                        {{ \Carbon\Carbon::parse($payment->plan_expiry_date)->format('d M Y') }}
                                    </p>
                                </div>

                                <a href="{{ route('web.currentPlan.index') }}"
                                    class="paymentbuttons-check mt-3">{{ __('messages.lbl_view_my_plan') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

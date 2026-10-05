@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    <section class="common-section-bg py-4 py-lg-5">
        <div class="event-checkout-page">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-6 col-lg-7 col-md-9 col-sm-10 col-11 px-2 mx-auto">
                        <div class="common-bgwhite-main p-3 p-lg-4">
                            <div class="right-myprofile-dashed">

                                {{-- Title --}}
                                <div class="event-checktitle pt-1 text-center mb-2 pb-2">
                                    <div class="fts-22 fw-7 white-color-n">
                                        {{ __('messages.lbl_selected_event') }} — {{ $event->title }}
                                    </div>
                                </div>

                                {{-- Order summary --}}
                                <div class="event-checkout-content-py px-sm-3 px-lg-4">
                                    <div
                                        class="single-pay-checkout d-flex justify-content-between align-items-sm-center py-2">
                                        <h4 class="fts-16 fw-6 white-color-n w-50">{{ __('messages.lbl_event_name') }}</h4>
                                        <p class="fts-16 fw-4 white-color-n w-50">: {{ $event->title }}</p>
                                    </div>
                                    <div
                                        class="single-pay-checkout d-flex justify-content-between align-items-sm-center py-2">
                                        <h4 class="fts-16 fw-6 white-color-n w-50">{{ __('messages.lbl_name') }}</h4>
                                        <p class="fts-16 fw-4 white-color-n w-50">: {{ $registration->name }}</p>
                                    </div>
                                    <div
                                        class="single-pay-checkout d-flex justify-content-between align-items-sm-center py-2">
                                        <h4 class="fts-16 fw-6 white-color-n w-50">{{ __('messages.lbl_email') }}</h4>
                                        <p class="fts-16 fw-4 white-color-n w-50">: {{ $registration->email }}</p>
                                    </div>
                                    <div
                                        class="single-pay-checkout d-flex justify-content-between align-items-sm-center py-2">
                                        <h4 class="fts-16 fw-6 white-color-n w-50">{{ __('messages.lbl_tickets') }}</h4>
                                        <p class="fts-16 fw-4 white-color-n w-50">: {{ $registration->tickets_qty }}</p>
                                    </div>
                                    <div
                                        class="single-pay-checkout d-flex justify-content-between align-items-sm-center py-2">
                                        <h4 class="fts-16 fw-6 white-color-n w-50">
                                            {{ __('messages.lbl_per_ticket_price') }}</h4>
                                        <p class="fts-16 fw-4 white-color-n w-50">
                                            : {{ $registration->currency }}
                                            {{ number_format($registration->ticket_price, 2) }}
                                        </p>
                                    </div>
                                    @if ($registration->tax_percentage > 0)
                                        <div
                                            class="single-pay-checkout d-flex justify-content-between align-items-sm-center py-2">
                                            <h4 class="fts-16 fw-6 white-color-n w-50">
                                                {{ $registration->tax_name ?? 'Tax' }}
                                                ({{ $registration->tax_percentage }}%)
                                            </h4>
                                            <p class="fts-16 fw-4 white-color-n w-50">
                                                : {{ $registration->currency }}
                                                {{ number_format($registration->tax_amount, 2) }}
                                            </p>
                                        </div>
                                    @endif
                                    <div
                                        class="single-pay-checkout d-flex justify-content-between align-items-sm-center py-2 border-top mt-1">
                                        <h4 class="fts-18 fw-7 white-color-n w-50">{{ __('messages.lbl_total_payment') }}
                                        </h4>
                                        <p class="fts-18 fw-7 white-color-n w-50">
                                            : {{ $registration->currency }}
                                            {{ number_format($registration->grand_total, 2) }}/-
                                        </p>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('web.event.createOrder') }}">
                                    @csrf
                                    <input type="hidden" name="registration_id" value="{{ $registration->id }}">
                                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                                    {{-- Pay Now button --}}
                                    <div class="text-center mt-2 mt-lg-3">
                                        <button type="submit" class="comman-bg-btn fts-15 mx-auto">
                                            {{ __('messages.lbl_pay_now') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

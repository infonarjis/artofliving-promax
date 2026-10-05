@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')

@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    <section class="common-section-bg py-5">
        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-lg-7 col-md-9 col-sm-11 px-2 mx-auto">
                    <div class="common-bgwhite-main p-3 p-lg-4 text-center">
                        @if ($status === 'Success')
                            <div class="payment-innnerstyle text-center py-2">
                                <img src="{{ asset('storage/web/') }}/assets/images/payment-success-icon.png" alt=""
                                    class="paymet-icon">
                                <h4 class="mt-3 fts-20 fw-7 white-color-n">{{ __('messages.lbl_booking_confirmed') }}</h4>
                                <p class="mt-1 fw-4 white-color-n fts-14">
                                    {{ __('messages.lbl_booking_confirmed_msg') }}
                                </p>
                            </div>
                            @if (!empty($registration))
                                <div class="common-bgtransparent-main p-3 p-lg-4 text-start mb-4">
                                    <div class="row">
                                        <div class="col-6 py-1">
                                            <p class="fts-14 fw-6 white-color-n">{{ __('messages.lbl_event') }}</p>
                                            <p class="fts-14 fw-4 white-color70-n">{{ $registration->event->title ?? '—' }}
                                            </p>
                                        </div>
                                        <div class="col-6 py-1">
                                            <p class="fts-14 fw-6 white-color-n">{{ __('messages.lbl_name') }}</p>
                                            <p class="fts-14 fw-4 white-color70-n">{{ $registration->name }}</p>
                                        </div>
                                        <div class="col-6 py-1">
                                            <p class="fts-14 fw-6 white-color-n">{{ __('messages.lbl_email') }}</p>
                                            <p class="fts-14 fw-4 white-color70-n">{{ $registration->email }}</p>
                                        </div>
                                        <div class="col-6 py-1">
                                            <p class="fts-14 fw-6 white-color-n">{{ __('messages.lbl_tickets') }}</p>
                                            <p class="fts-14 fw-4 white-color70-n">{{ $registration->tickets_qty }}</p>
                                        </div>
                                        <div class="col-6 py-1">
                                            <p class="fts-14 fw-6 white-color-n">{{ __('messages.lbl_total_payment') }}</p>
                                            <p class="fts-14 fw-4 white-color70-n">
                                                {{ $registration->currency }}
                                                {{ number_format($registration->grand_total, 2) }}
                                            </p>
                                        </div>
                                        @if (!empty($registration->transaction_id))
                                            <div class="col-6 py-1">
                                                <p class="fts-14 fw-6 white-color-n">
                                                    {{ __('messages.lbl_transaction_id') }}</p>
                                                <p class="fts-13 fw-4 white-color70-n">{{ $registration->transaction_id }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    {{-- Download Invoice Button --}}
                                    <div class="d-flex gap-3 justify-content-center flex-wrap mt-3">
                                        <a href="{{ route('web.event.invoice.download', $registration->id) }}"
                                            class="comman-bg-btn fts-15 d-inline-flex align-items-center gap-2"
                                            target="_blank">
                                            <iconify-icon icon="hugeicons:download-02"></iconify-icon>
                                            {{ __('messages.lbl_download_invoice') }}
                                        </a>
                                        <a href="{{ route('web.event.index') }}"
                                            class="comman-bg-btn fts-15 d-inline-block">
                                            {{ __('messages.lbl_browse_more_events') }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="payment-innnerstyle text-center py-2">
                                <img src="{{ asset('storage/web/') }}/assets/images/payment-fail-icon.png"
                                    alt="Payment Failed" class="paymet-icon">
                                <div class="mt-3 fts-18 fw-7 white-color-n">{{ __('messages.lbl_payment_failed') }}</div>
                                <div class="mt-1 white-color70-n fw-4 fts-14">
                                    {{ $error ?? __('messages.lbl_payment_failed_msg') }}
                                </div>
                                @if ($registration)
                                    <a href="{{ route('web.event.paynow', [
                                        'id' => $registration->event_id,
                                        'registration_id' => $registration->id,
                                    ]) }}"
                                        class="paymentbuttons-check fail">
                                        {{ __('messages.lbl_try_again') }}
                                    </a>
                                @else
                                    <a href="{{ route('web.event.index') }}" class="paymentbuttons-check fail">
                                        {{ __('messages.lbl_try_again') }}
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

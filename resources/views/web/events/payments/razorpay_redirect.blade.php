@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('title', 'Redirecting to Payment')
@section('web_content')
    @php
        $siteName = $configArr['web_name'];
        $siteLogo = _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'];
    @endphp
    @push('styles')

        <style>
            .rzp-redirect-wrapper {
                min-height: 70vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 40px 20px;
                background:
                    radial-gradient(circle at 15% 20%, rgba(108, 76, 224, 0.10), transparent 40%),
                    radial-gradient(circle at 85% 80%, rgba(232, 62, 140, 0.08), transparent 40%),
                    #fafaff;
            }

            .rzp-redirect-card {
                width: 100%;
                max-width: 420px;
                background: #ffffff;
                border-radius: 20px;
                padding: 44px 36px 32px;
                text-align: center;
                box-shadow: 0 10px 40px rgba(20, 20, 60, 0.08), 0 2px 8px rgba(20, 20, 60, 0.04);
                border: 1px solid rgba(108, 76, 224, 0.08);
                animation: rzp-fade-up .5s ease both;
            }

            .rzp-logo-row {
                margin-bottom: 22px;
            }

            .rzp-site-logo {
                height: 34px;
                object-fit: contain;
            }

            .rzp-spinner {
                position: relative;
                width: 84px;
                height: 84px;
                margin: 0 auto 26px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .rzp-spinner-ring {
                position: absolute;
                inset: 0;
                border-radius: 50%;
                border: 3px solid rgba(108, 76, 224, 0.12);
                border-top-color: #6C4CE0;
                animation: rzp-spin 1s linear infinite;
            }

            .rzp-lock-icon {
                width: 30px;
                height: 30px;
                color: #6C4CE0;
            }

            .rzp-title {
                font-size: 19px;
                font-weight: 600;
                color: #1a1a2e;
                margin: 0 0 8px;
            }

            .rzp-dots span {
                animation: rzp-blink 1.4s infinite;
                opacity: 0;
            }

            .rzp-dots span:nth-child(2) {
                animation-delay: .2s;
            }

            .rzp-dots span:nth-child(3) {
                animation-delay: .4s;
            }

            .rzp-subtitle {
                font-size: 13.5px;
                color: #7a7a8c;
                line-height: 1.5;
                margin: 0 0 24px;
            }

            .rzp-progress-track {
                width: 100%;
                height: 4px;
                background: rgba(108, 76, 224, 0.10);
                border-radius: 4px;
                overflow: hidden;
                margin-bottom: 22px;
            }

            .rzp-progress-bar {
                height: 100%;
                width: 40%;
                border-radius: 4px;
                background: linear-gradient(90deg, #6C4CE0, #E83E8C);
                animation: rzp-progress 1.6s ease-in-out infinite;
            }

            .rzp-secure-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 12px;
                font-weight: 500;
                color: #4a4a5a;
                background: #f4f3ff;
                padding: 6px 14px;
                border-radius: 30px;
            }

            .rzp-secure-badge svg {
                color: #6C4CE0;
            }

            @keyframes rzp-spin {
                to {
                    transform: rotate(360deg);
                }
            }

            @keyframes rzp-blink {

                0%,
                80%,
                100% {
                    opacity: 0;
                }

                40% {
                    opacity: 1;
                }
            }

            @keyframes rzp-progress {
                0% {
                    width: 10%;
                    margin-left: 0%;
                }

                50% {
                    width: 60%;
                    margin-left: 20%;
                }

                100% {
                    width: 10%;
                    margin-left: 90%;
                }
            }

            @keyframes rzp-fade-up {
                from {
                    opacity: 0;
                    transform: translateY(12px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
        
    @endpush('styles')

    <div class="rzp-redirect-wrapper">
        <div class="rzp-redirect-card">
            <div class="rzp-logo-row">
                <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="rzp-site-logo">
            </div>

            <div class="rzp-spinner">
                <div class="rzp-spinner-ring"></div>
                <svg class="rzp-lock-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="5" y="11" width="14" height="9" rx="2" stroke="currentColor"
                        stroke-width="1.6" />
                    <path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                    <circle cx="12" cy="15.5" r="1.4" fill="currentColor" />
                </svg>
            </div>

            <h3 class="rzp-title">{{ __('messages.lbl_redirecting_to_razorpay') }}<span
                    class="rzp-dots"><span>.</span><span>.</span><span>.</span></span></h3>
            <p class="rzp-subtitle">{{ __('messages.lbl_msg_redirecting_to_payment_gateway') }}</p>

            <div class="rzp-progress-track">
                <div class="rzp-progress-bar"></div>
            </div>

            <div class="rzp-secure-badge">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="14" height="14">
                    <path d="M12 2l7 3v6c0 5-3.4 8.5-7 11-3.6-2.5-7-6-7-11V5l7-3z" stroke="currentColor" stroke-width="1.6"
                        stroke-linejoin="round" />
                    <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                {{ __('messages.lbl_secured_by_razorpay') }}
            </div>
        </div>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <form id="paymentForm" action="{{ route('web.event.payment.handle') }}" method="POST">
            @csrf
            <input type="hidden" name="registration_id" value="{{ $registration->id }}">
            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
            <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
            <input type="hidden" name="razorpay_signature" id="razorpay_signature">
        </form>

        <script>
            var options = {
                key: "{{ $gateway->client_id }}",
                amount: "{{ $order['amount'] }}",
                currency: "{{ $order['currency'] }}",
                order_id: "{{ $order['id'] }}",
                name: "{{ $configArr['web_name'] }}",
                prefill: {
                    name: "{{ $registration->name }}",
                    email: "{{ $registration->email }}",
                    contact: "{{ $registration->mobile }}",
                },
                theme: {
                    color: "#6c4aed"
                },
                handler: function(response) {
                    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                    document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                    document.getElementById('razorpay_signature').value = response.razorpay_signature;
                    document.getElementById('paymentForm').submit();
                },
                modal: {
                    ondismiss: function() {
                        console.log('Payment modal closed by user.');
                        window.location.href = "{{ $cancelUrl }}";
                    }
                }
            };

            new Razorpay(options).open();
        </script>
    </div>

@endsection

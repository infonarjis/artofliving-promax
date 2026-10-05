<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MembershipPayment;
use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\UpgradeMembershipPlanService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Razorpay\Api\Api as RazorpayApi;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentController extends Controller
{
    protected UpgradeMembershipPlanService $assignPlanService;

    public function __construct(UpgradeMembershipPlanService $assignPlanService)
    {
        $this->assignPlanService = $assignPlanService;
    }

    public function createOrder(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required|exists:membership_plans,id',
                'package_ids' => 'nullable|array',
                'coupon_code' => 'nullable|string',
            ]);

            $authUser = auth()->guard('web')->user();
            $gateway = PaymentMethod::active()->latest()->first();
            if (!$gateway) return $this->failed('Payment gateway not configured.');

            $plan = MembershipPlan::findOrFail($request->plan_id);

            $calculated = $this->assignPlanService->calculatePlanAmount(
                $plan,
                [
                    'package_ids' => $request->package_ids ?? [],
                    'coupon_code' => $request->coupon_code ?? null,
                ]
            );

            $payment = MembershipPayment::create([
                'user_id' => $authUser->id,
                'plan_id' => $plan->id,
                'package_ids' => $request->package_ids ? json_encode($request->package_ids) : null,
                'coupon_code' => $request->coupon_code ?? null,
                'amount' => $calculated['grand_total'],
                'gateway' => $gateway->name,
                'status' => 'pending'
            ]);

            $gatewayName = strtolower(trim($gateway->name));

            if ($gatewayName === 'razorpay') {
                $api = new RazorpayApi($gateway->client_id, $gateway->client_secret);
                $order = $api->order->create([
                    'receipt' => 'MP_' . $payment->id,
                    'amount' => (int) round($payment->amount * 100),
                    'currency' => 'INR',
                    'notes' => ['payment_id' => $payment->id, 'user_id' => $authUser->id],
                ]);
                return view('web.membershipPlan.payments.razorpay_redirect', compact('order', 'gateway', 'payment'));
            }

            if ($gatewayName === 'paypal') {
                $url = $gateway->payment_mode === 'Test' ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
                ## Create Tocken :
                $response = Http::withBasicAuth($gateway->client_id, $gateway->client_secret)->asForm()
                    ->post($url . '/v1/oauth2/token', [
                        'grant_type' => 'client_credentials'
                    ]);
                if (!$response->successful()) {
                    return $this->failed();
                }
                $accessToken =  $response->json()['access_token'] ?? null;

                $response = Http::withToken($accessToken)->post($url . '/v2/checkout/orders', [
                    "intent" => "CAPTURE",
                    "purchase_units" => [
                        [
                            "reference_id" => $payment->id,
                            "amount" => [
                                "currency_code" => "USD", // INR
                                "value" => number_format($payment->amount, 2, '.', '')
                            ]
                        ]
                    ],
                    'application_context' => [
                        "return_url" => route('web.membership.paypalSuccess', [
                            'payment_id' => $payment->id
                        ]),
                        'cancel_url' => route('web.membership.paypalCancel'),
                    ]
                ]);

                $responseData = $response->json();
                $approvalUrl = collect($responseData['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;
                return redirect()->away($approvalUrl);
            }

            // ==================== STRIPE ====================
            if ($gatewayName === 'stripe') {
                return $this->initiateStripeOrder($gateway, $payment);
            }

            // ==================== CASHFREE ====================
            if ($gatewayName === 'cashfree') {
                return $this->initiateCashfreeOrder($gateway, $payment, $authUser);
            }

            // ==================== PHONEPE ====================
            if ($gatewayName === 'phonepe') {
                return $this->initiatePhonePeOrder($gateway, $payment, $authUser);
            }
        } catch (Exception $e) {
            Log::error('Payment verification failed: ' . $e->getMessage());
            return $this->failed('Payment gateway not supported.');
        }

        return $this->failed('Payment gateway not supported.');
    }

    public function razorpaySuccess(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $gateway = PaymentMethod::active()->latest()->first();
        if (!$gateway) return $this->failed('Payment gateway not configured.');

        $api = new RazorpayApi($gateway->client_id, $gateway->client_secret);

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ]);

            $order = $api->order->fetch($request->razorpay_order_id);
            $payment = MembershipPayment::findOrFail($order->notes['payment_id']);
            $plan = MembershipPlan::findOrFail($payment->plan_id);

            $assignPlan = $this->assignPlanService->assign($payment->user, $plan, $payment, [
                'package_ids' => $payment->package_ids ?? [],
                'coupon_code' => $payment->coupon_code ?? null,
                'transaction_id' => $request->razorpay_payment_id,
                'payment_mode' => 'Razorpay',
            ]);

            return redirect()->route('web.membership.success', $assignPlan->id);
        } catch (Exception $e) {
            Log::error('Payment verification failed: ' . $e->getMessage());
            return $this->failed('Payment verification failed.');
        }
    }

    public function paypalSuccess(Request $request)
    {
        try {
            $orderId = $request->input('token'); // PayPal Order ID
            if (!$orderId) {
                return $this->failed();
            }

            $paymentId = $request->payment_id;
            $payment = MembershipPayment::findOrFail($paymentId);
            $gateway = PaymentMethod::active()->latest()->first();

            $baseUrl = $gateway->payment_mode === 'Test' ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

            $clientId = $gateway->client_id;
            $clientSecret = $gateway->client_secret;

            ## STEP 1 — Get Access Token :
            $tokenResponse = Http::withBasicAuth($clientId, $clientSecret)->asForm()
                ->post($baseUrl . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);
            if (!$tokenResponse->successful()) {
                return $this->failed();
            }
            $accessToken = $tokenResponse->json('access_token');

            ## STEP 2 — Capture Payment :
            $captureResponse = Http::withToken($accessToken)->withBody('', 'application/json')->post($baseUrl . "/v2/checkout/orders/{$orderId}/capture");
            if (!$captureResponse->successful()) {
                Log::error('PayPal capture failed', ['response' => $captureResponse->json()]);
                return $this->failed();
            }

            $captureData = $captureResponse->json();

            ## STEP 3 — Verify Payment Status :
            $orderStatus = data_get($captureData, 'status');

            if ($orderStatus !== 'COMPLETED') {
                Log::warning('PayPal payment not completed', ['response' => $captureData]);
                return $this->failed('PayPal payment failed');
            }

            $transactionId = data_get(
                $captureData,
                'purchase_units.0.payments.captures.0.id'
            );

            if (!$transactionId) {
                Log::error('PayPal Transaction ID not found', [
                    'response' => $captureData
                ]);
                return $this->failed('Transaction ID not found');
            }

            $plan = MembershipPlan::findOrFail(
                $payment->plan_id
            );
            $assignPlan = $this->assignPlanService->assign(
                $payment->user,
                $plan,
                $payment,
                [
                    'package_ids' => $payment->package_ids ? json_decode($payment->package_ids, true) : [],
                    'coupon_code' => $payment->coupon_code,
                    'transaction_id' => $transactionId,
                    'payment_mode' => 'Paypal',
                ]
            );
            return redirect()->route('web.membership.success', $assignPlan->id);
        } catch (Exception $e) {
            Log::error(
                'PayPal Verification Failed : ' . $e->getMessage()
            );
            return $this->failed('PayPal payment failed.');
        }
    }

    public function paypalCancel()
    {
        return $this->failed('Payment cancelled by user.');
    }

    public function success(Payment $payment)
    {
        return view('web.membershipPlan.payments.success', compact('payment'));
    }

    public function failed($msg = 'Payment failed')
    {
        return view('web.membershipPlan.payments.failed', ['error' => $msg]);
    }

    public function createAddOnOrder(Request $request)
    {
        try {
            $request->validate([
                // 'add_on_package_id' => 'required|array',
                'add_on_package_id.*' => 'exists:add_on_packages,id',
            ]);

            $authUser = auth()->guard('web')->user();

            $currentPayment = Payment::where('member_id', $authUser->id)
                ->where('current_plan', 'Yes')
                ->first();

            if (!$currentPayment) {
                return $this->failed(__('messages.msg_no_active_plan_found'));
            }

            $gateway = PaymentMethod::active()->latest()->first();
            if (!$gateway) return $this->failed('Payment gateway not configured.');

            $addOnPackageIds = (array) $request->add_on_package_id;

            $calculated = $this->assignPlanService->calculateAddOnAmount([
                'package_ids' => $addOnPackageIds,
                'coupon_code' => $request->coupon_code ?? null,
            ]);

            $payment = MembershipPayment::create([
                'user_id' => $authUser->id,
                'plan_id' => $currentPayment->id,
                'type' => 'addon',
                'current_payment_id' => $currentPayment->id,
                'package_ids' => json_encode($addOnPackageIds),
                'coupon_code' => $request->coupon_code ?? null,
                'amount' => $calculated['grand_total'],
                'gateway' => $gateway->name,
                'status' => 'pending',
            ]);

            $gatewayName = strtolower(trim($gateway->name));

            if ($gatewayName === 'razorpay') {
                $api = new RazorpayApi($gateway->client_id, $gateway->client_secret);
                $order = $api->order->create([
                    'receipt' => 'ADDON_' . $payment->id,
                    'amount' => (int) round($payment->amount * 100),
                    'currency' => 'INR',
                    'notes' => ['payment_id' => $payment->id, 'user_id' => $authUser->id],
                ]);
                return view('web.membershipPlan.payments.razorpay_redirect', compact('order', 'gateway', 'payment'));
            }

            if ($gatewayName === 'paypal') {
                $url = $gateway->payment_mode === 'Test' ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

                $response = Http::withBasicAuth($gateway->client_id, $gateway->client_secret)->asForm()
                    ->post($url . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);

                if (!$response->successful()) {
                    return $this->failed();
                }
                $accessToken = $response->json()['access_token'] ?? null;

                $response = Http::withToken($accessToken)->post($url . '/v2/checkout/orders', [
                    "intent" => "CAPTURE",
                    "purchase_units" => [[
                        "reference_id" => $payment->id,
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => number_format($payment->amount, 2, '.', '')
                        ]
                    ]],
                    'application_context' => [
                        "return_url" => route('web.membership.paypalSuccess', ['payment_id' => $payment->id]),
                        'cancel_url' => route('web.membership.paypalCancel'),
                    ]

                ]);

                $responseData = $response->json();
                $approvalUrl = collect($responseData['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;
                return redirect()->away($approvalUrl);
            }

            // STRIPE
            if ($gatewayName === 'stripe') {
                return $this->initiateStripeOrder($gateway, $payment);
            }

            // CASHFREE
            if ($gatewayName === 'cashfree') {
                return $this->initiateCashfreeOrder($gateway, $payment, $authUser);
            }

            // PHONEPE
            if ($gatewayName === 'phonepe') {
                return $this->initiatePhonePeOrder($gateway, $payment, $authUser);
            }

            return $this->failed('Payment gateway not supported.');
        } catch (Throwable $e) {
            Log::error('AddOn order creation failed: ' . $e->getMessage());
            return $this->failed('Payment gateway not supported.');
        }
    }

    private function initiateStripeOrder(PaymentMethod $gateway, MembershipPayment $payment)
    {
        $reference = ($payment->type === 'addon' ? 'ADDON_' : 'MP_') . $payment->id;

        $cancelUrl = route('web.membership.paypalCancel');
        $successUrl = route('web.membership.stripeSuccess', ['payment_id' => $payment->id])
            . '&session_id={CHECKOUT_SESSION_ID}';

        $response = Http::withToken($gateway->client_secret)->asForm()->post(
            'https://api.stripe.com/v1/checkout/sessions',
            [
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'client_reference_id' => $reference,
                'metadata' => [
                    'payment_id' => $payment->id,
                ],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'inr',
                            'product_data' => [
                                'name' => $payment->type === 'addon' ? 'Membership Add-On' : 'Membership Plan',
                            ],
                            'unit_amount' => (int) round($payment->amount * 100),
                        ],
                        'quantity' => 1,
                    ],
                ],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]
        );

        if (!$response->successful()) {
            Log::error('Stripe session creation failed', ['response' => $response->json()]);
            return $this->failed();
        }

        $checkoutUrl = $response->json('url');
        if (!$checkoutUrl) {
            return $this->failed();
        }

        return redirect()->away($checkoutUrl);
    }

    public function stripeSuccess(Request $request)
    {
        try {
            $sessionId = $request->query('session_id');
            $paymentId = $request->query('payment_id');

            if (!$sessionId || !$paymentId) {
                return $this->failed();
            }

            $payment = MembershipPayment::findOrFail($paymentId);
            $gateway = PaymentMethod::active()->latest()->first();
            if (!$gateway) return $this->failed('Payment gateway not configured.');

            $response = Http::withToken($gateway->client_secret)
                ->get('https://api.stripe.com/v1/checkout/sessions/' . $sessionId);

            if (!$response->successful()) {
                Log::error('Stripe session fetch failed', ['response' => $response->json()]);
                return $this->failed();
            }

            $session = $response->json();

            if (data_get($session, 'payment_status') !== 'paid') {
                Log::warning('Stripe payment not completed', ['response' => $session]);
                return $this->failed('Payment not completed.');
            }

            // Confirm this session actually belongs to this payment record.
            $expectedReference = ($payment->type === 'addon' ? 'ADDON_' : 'MP_') . $payment->id;
            if (data_get($session, 'client_reference_id') !== $expectedReference) {
                Log::error('Stripe reference mismatch', ['response' => $session]);
                return $this->failed('Payment verification failed.');
            }

            $transactionId = data_get($session, 'payment_intent');
            if (!$transactionId) {
                return $this->failed('Transaction ID not found');
            }

            $plan = MembershipPlan::findOrFail($payment->plan_id);

            $assignPlan = $this->assignPlanService->assign($payment->user, $plan, $payment, [
                'package_ids' => $payment->package_ids ? json_decode($payment->package_ids, true) : [],
                'coupon_code' => $payment->coupon_code,
                'transaction_id' => $transactionId,
                'payment_mode' => 'Stripe',
            ]);

            return redirect()->route('web.membership.success', $assignPlan->id);
        } catch (Exception $e) {
            Log::error('Stripe verification failed: ' . $e->getMessage());
            return $this->failed('Payment verification failed.');
        }
    }

    private function cashfreeBaseUrl(PaymentMethod $gateway): string
    {
        return $gateway->payment_mode === 'Test'
            ? 'https://sandbox.cashfree.com/pg'
            : 'https://api.cashfree.com/pg';
    }

    private function cashfreeHeaders(PaymentMethod $gateway): array
    {
        return [
            'x-client-id' => $gateway->client_id,
            'x-client-secret' => $gateway->client_secret,
            'x-api-version' => '2023-08-01',
            'Content-Type' => 'application/json',
        ];
    }

    private function initiateCashfreeOrder(PaymentMethod $gateway, MembershipPayment $payment, $authUser)
    {
        $orderId = ($payment->type === 'addon' ? 'ADDON_' : 'MP_') . $payment->id;

        $returnUrl = route('web.membership.cashfreeSuccess', ['payment_id' => $payment->id])
            . '&order_id={order_id}';

        $response = Http::withHeaders($this->cashfreeHeaders($gateway))->post(
            $this->cashfreeBaseUrl($gateway) . '/orders',
            [
                'order_id' => $orderId,
                'order_amount' => (float) number_format($payment->amount, 2, '.', ''),
                'order_currency' => 'INR',
                'customer_details' => [
                    'customer_id' => (string) $authUser->id,
                    'customer_name' => $authUser->name ?? 'Member',
                    'customer_email' => $authUser->email ?? 'noemail@example.com',
                    'customer_phone' => $authUser->phone ?? '9999999999',
                ],
                'order_meta' => [
                    'return_url' => $returnUrl,
                ],
            ]
        );

        if (!$response->successful()) {
            Log::error('Cashfree order creation failed', ['response' => $response->json()]);
            return $this->failed();
        }

        $order = $response->json();
        $paymentSessionId = data_get($order, 'payment_session_id');

        if (!$paymentSessionId) {
            return $this->failed();
        }

        return view('web.membershipPlan.payments.cashfree_redirect', [
            'paymentSessionId' => $paymentSessionId,
            'gateway' => $gateway,
            'payment' => $payment,
            'mode' => $gateway->payment_mode === 'Test' ? 'sandbox' : 'production',
        ]);
    }

    public function cashfreeSuccess(Request $request)
    {
        try {
            $paymentId = $request->query('payment_id');
            if (!$paymentId) {
                return $this->failed();
            }

            $payment = MembershipPayment::findOrFail($paymentId);
            $gateway = PaymentMethod::active()->latest()->first();
            if (!$gateway) return $this->failed('Payment gateway not configured.');

            // Always re-derive our own order_id rather than trusting the
            // querystring, then verify status directly with Cashfree.
            $orderId = ($payment->type === 'addon' ? 'ADDON_' : 'MP_') . $payment->id;

            $response = Http::withHeaders($this->cashfreeHeaders($gateway))
                ->get($this->cashfreeBaseUrl($gateway) . '/orders/' . $orderId);

            if (!$response->successful()) {
                Log::error('Cashfree order fetch failed', ['response' => $response->json()]);
                return $this->failed();
            }

            $order = $response->json();

            if (data_get($order, 'order_status') !== 'PAID') {
                Log::warning('Cashfree payment not completed', ['response' => $order]);
                return $this->failed('Payment not completed.');
            }

            // Fetch payments for this order to pull the cf_payment_id.
            $paymentsResponse = Http::withHeaders($this->cashfreeHeaders($gateway))
                ->get($this->cashfreeBaseUrl($gateway) . '/orders/' . $orderId . '/payments');

            $transactionId = data_get($paymentsResponse->json(), '0.cf_payment_id');

            if (!$transactionId) {
                Log::error('Cashfree Transaction ID not found', ['response' => $paymentsResponse->json()]);
                return $this->failed('Transaction ID not found');
            }

            $plan = MembershipPlan::findOrFail($payment->plan_id);

            $assignPlan = $this->assignPlanService->assign($payment->user, $plan, $payment, [
                'package_ids' => $payment->package_ids ? json_decode($payment->package_ids, true) : [],
                'coupon_code' => $payment->coupon_code,
                'transaction_id' => $transactionId,
                'payment_mode' => 'Cashfree',
            ]);

            return redirect()->route('web.membership.success', $assignPlan->id);
        } catch (Exception $e) {
            Log::error('Cashfree verification failed: ' . $e->getMessage());
            return $this->failed('Payment verification failed.');
        }
    }

    private function phonePeBaseUrl(PaymentMethod $gateway): string
    {
        return $gateway->payment_mode === 'Test'
            ? 'https://api-preprod.phonepe.com/apis/pg-sandbox'
            : 'https://api.phonepe.com/apis/hermes';
    }

    private function phonePeSalt(PaymentMethod $gateway): array
    {
        $parts = explode('|', (string) $gateway->client_secret);
        $saltKey = $parts[0] ?? '';
        $saltIndex = $parts[1] ?? '1';

        return [$saltKey, $saltIndex];
    }

    private function initiatePhonePeOrder(PaymentMethod $gateway, MembershipPayment $payment, $authUser)
    {
        [$saltKey, $saltIndex] = $this->phonePeSalt($gateway);

        $merchantTransactionId = ($payment->type === 'addon' ? 'ADDON_' : 'MP_') . $payment->id;
        $endpoint = '/pg/v1/pay';

        $redirectUrl = route('web.membership.phonepeSuccess', ['payment_id' => $payment->id]);

        $payload = [
            'merchantId' => $gateway->client_id,
            'merchantTransactionId' => $merchantTransactionId,
            'merchantUserId' => 'USER_' . $authUser->id,
            'amount' => (int) round($payment->amount * 100),
            'redirectUrl' => $redirectUrl,
            'redirectMode' => 'POST',
            'callbackUrl' => $redirectUrl,
            'paymentInstrument' => [
                'type' => 'PAY_PAGE',
            ],
        ];

        $base64Payload = base64_encode(json_encode($payload));
        $checksum = hash('sha256', $base64Payload . $endpoint . $saltKey) . '###' . $saltIndex;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-VERIFY' => $checksum,
        ])->post($this->phonePeBaseUrl($gateway) . $endpoint, [
            'request' => $base64Payload,
        ]);

        if (!$response->successful()) {
            Log::error('PhonePe order creation failed', ['response' => $response->json()]);
            return $this->failed();
        }

        $data = $response->json();
        $payUrl = data_get($data, 'data.instrumentResponse.redirectInfo.url');

        if (!$payUrl) {
            Log::error('PhonePe redirect URL missing', ['response' => $data]);
            return $this->failed();
        }

        return redirect()->away($payUrl);
    }

    public function phonepeSuccess(Request $request)
    {
        try {
            $paymentId = $request->query('payment_id');
            if (!$paymentId) {
                return $this->failed();
            }

            $payment = MembershipPayment::findOrFail($paymentId);
            $gateway = PaymentMethod::active()->latest()->first();
            if (!$gateway) return $this->failed('Payment gateway not configured.');

            [$saltKey, $saltIndex] = $this->phonePeSalt($gateway);

            $merchantTransactionId = ($payment->type === 'addon' ? 'ADDON_' : 'MP_') . $payment->id;
            $merchantId = $gateway->client_id;
            $endpoint = "/pg/v1/status/{$merchantId}/{$merchantTransactionId}";

            $checksum = hash('sha256', $endpoint . $saltKey) . '###' . $saltIndex;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $checksum,
                'X-MERCHANT-ID' => $merchantId,
            ])->get($this->phonePeBaseUrl($gateway) . $endpoint);

            if (!$response->successful()) {
                Log::error('PhonePe status check failed', ['response' => $response->json()]);
                return $this->failed();
            }

            $data = $response->json();

            if (data_get($data, 'code') !== 'PAYMENT_SUCCESS') {
                Log::warning('PhonePe payment not completed', ['response' => $data]);
                return $this->failed('Payment not completed.');
            }

            $transactionId = data_get($data, 'data.transactionId');
            if (!$transactionId) {
                Log::error('PhonePe Transaction ID not found', ['response' => $data]);
                return $this->failed('Transaction ID not found');
            }

            $plan = MembershipPlan::findOrFail($payment->plan_id);

            $assignPlan = $this->assignPlanService->assign($payment->user, $plan, $payment, [
                'package_ids' => $payment->package_ids ? json_decode($payment->package_ids, true) : [],
                'coupon_code' => $payment->coupon_code,
                'transaction_id' => $transactionId,
                'payment_mode' => 'PhonePe',
            ]);

            return redirect()->route('web.membership.success', $assignPlan->id);
        } catch (Exception $e) {
            Log::error('PhonePe verification failed: ' . $e->getMessage());
            return $this->failed('Payment verification failed.');
        }
    }
}
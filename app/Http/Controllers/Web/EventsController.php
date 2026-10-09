<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegister;
use App\Models\PaymentMethod;
use App\Services\EmailSendService;
use App\Services\SeoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api as RazorpayApi;

class EventsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | index — List all approved events
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Event::where('status', 'APPROVED')
            ->whereRaw("
                TIMESTAMP(event_date, COALESCE(event_time, '23:59:59')) >= ?
            ", [now()]);

        if ($request->filled('keywords')) {
            $query->where(function ($q) use ($request) {
                $q->where('title',       'like', '%' . $request->keywords . '%')
                    ->orWhere('description', 'like', '%' . $request->keywords . '%')
                    ->orWhere('venue',      'like', '%' . $request->keywords . '%');
            });
        }

        $events = $query->orderBy('event_date', 'asc')->paginate(9)->withQueryString();

        if ($request->ajax()) {
            return view(
                _getConstant('dir_path.WEB_DIR_PATH') . '.events.ajax_result',
                compact('events')
            )->render();
        }

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.events.index', compact('events'));
    }

    /*
    |--------------------------------------------------------------------------
    | show — Event detail page
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $event = $this->getEventOrFail($id);

        ## Seo Data:
        $eventImg = '';
        if (_checkStorageFileExists('upload_path.EVENT_IMAGE_URL', $event->image)) {
            $eventImg = _assetUrl('upload_path.EVENT_IMAGE_URL') . $event->image;
        }
        $seoData = [
            'title'       => $event->title,
            'description' => $event->description,
            'image'       => $eventImg
        ];
        $seoManagement = SeoService::getPageSeo('success-story-detail', $seoData);

        ## Current Login User :
        $authUser = auth()->user();
        $allUsersRegisteredMatriId = [];
        if ($authUser) {
            // Check if the user has already registered for any of the displayed events
            $alreadyRegistered = EventRegister::where('event_id', $event->id)
                ->where(function ($query) use ($authUser) {
                    $query->where('member_id', $authUser->id)
                        ->orWhere('matri_id', $authUser->matri_id);
                })
                ->first();
            if ($alreadyRegistered) {
                $allUsersRegisteredMatriId = EventRegister::where('event_id', $event->id)
                    ->pluck('matri_id')
                    ->toArray();
            }
        }

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.events.show', compact('event', 'seoManagement', 'allUsersRegisteredMatriId'));
    }

    /*
    |--------------------------------------------------------------------------
    | checkout — Confirmation-details form
    |--------------------------------------------------------------------------
    */
    public function checkout(Request $request, $id)
    {
        $event = $this->getEventOrFail($id);

        // qty & totals passed from the detail page
        $qty          = (int) $request->input('qty', 1);
        $ticketPrice  = (float) $event->ticket_price;
        $subtotal     = $ticketPrice * $qty;

        // Tax from event (if stored), fallback to 0
        $taxPercentage = (float) ($event->tax_percentage ?? 0);
        $taxAmount     = round($subtotal * $taxPercentage / 100, 2);
        $grandTotal    = round($subtotal + $taxAmount, 2);

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.events.checkout', compact(
            'event',
            'qty',
            'ticketPrice',
            'subtotal',
            'taxPercentage',
            'taxAmount',
            'grandTotal'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | storeCheckout — Save registration & redirect to payment
    |--------------------------------------------------------------------------
    */
    public function storeCheckout(Request $request, $id)
    {
        $event = $this->getEventOrFail($id);
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'mobile'        => 'required|digits_between:7,15',
            'country_code' => [
                'required',
                'string',
                'exists:country_master,country_code',
            ],
            'hear_about_us' => 'required|string|max:255',
            'ticket_qty'    => 'required|integer|min:1|max:' . ($event->total_tickets ?? 100),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $qty           = (int) $request->ticket_qty;
        $ticketPrice   = (float) $event->ticket_price;
        $subtotal      = $ticketPrice * $qty;
        $taxPercentage = (float) ($event->tax_percentage ?? 0);
        $taxAmount     = round($subtotal * $taxPercentage / 100, 2);
        $grandTotal    = round($subtotal + $taxAmount, 2);

        // Store registration (pending payment)
        $registration = EventRegister::create([
            'event_id'       => $event->id,
            'member_id'      => $user->id ?? null,
            'matri_id'       => $user->matri_id ?? null,
            'name'           => $request->name,
            'email'          => $request->email,
            'mobile'         => ($request->country_code ?? '') . $request->mobile,
            'hear_about_us'  => $request->hear_about_us,
            'currency'       => $event->currency,
            'ticket_price'   => $ticketPrice,
            'tickets_qty'    => $qty,
            'payment_mode'   => 'Pending',
            'tax_applicable' => $taxPercentage > 0 ? 'Yes' : 'No',
            'tax_name'       => $event->tax_name   ?? null,
            'tax_percentage' => $taxPercentage,
            'tax_amount'     => $taxAmount,
            'grand_total'    => $grandTotal,
            'created_at'     => now()
        ]);

        return response()->json([
            'status' => true,
            'redirect_url' => route('web.event.paynow', [
                'id' => $event->id,
                'registration_id' => $registration->id,
            ])
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | paynow — Payment gateway page
    |--------------------------------------------------------------------------
    */
    public function paynow(Request $request, $id)
    {
        $event = $this->getEventOrFail($id);
        $registrationId = $request->input('registration_id');

        $registration = EventRegister::where('id', $registrationId)
            ->where('event_id', $event->id)
            ->firstOrFail();

        $paymentMethod = PaymentMethod::where('status', 'APPROVED')->first();

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.events.paynow', compact(
            'event',
            'registration',
            'paymentMethod'
        ));
    }

    public function createOrder(Request $request)
    {
        try {
            $request->validate([
                'registration_id' => 'required',
                'event_id' => 'required',
            ]);

            $registration = EventRegister::with('event')->findOrFail($request->registration_id);

            if ($registration->payment_mode == EventRegister::PAYMENT_ONLINE) {
                return redirect()->route('web.event.success', [
                    'registration' => $registration->id
                ]);
            }

            $gateway = PaymentMethod::active()->latest()->first();

            if (!$gateway) {
                return redirect()->route('web.event.failed')->with('error', 'Payment gateway not configured.');
            }

            $gatewayName = strtolower(trim($gateway->name));
            $cancelUrl = route('web.event.failed', [
                'registration_id' => $registration->id
            ]);
            /*
            |--------------------------------------------------------------------------
            | Razorpay
            |--------------------------------------------------------------------------
            */
            if ($gatewayName === 'razorpay') {
                $api = new RazorpayApi($gateway->client_id, $gateway->client_secret);

                $order = $api->order->create([
                    'receipt' => 'EVT_' . $registration->id,
                    'amount' => (int) round($registration->grand_total * 100),
                    'currency' => 'INR',
                    'notes' => [
                        'registration_id' => $registration->id,
                        'event_id' => $registration->event_id,
                    ]
                ]);
                return view(
                    _getConstant('dir_path.WEB_DIR_PATH') . '.events.payments.razorpay_redirect',
                    compact('order', 'gateway', 'registration', 'cancelUrl')
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Paypal
            |--------------------------------------------------------------------------
            */
            if ($gatewayName === 'paypal') {
                $baseUrl = $gateway->payment_mode === 'Test' ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
                $tokenResponse = Http::withBasicAuth($gateway->client_id, $gateway->client_secret)->asForm()->post(
                    $baseUrl . '/v1/oauth2/token',
                    [
                        'grant_type' => 'client_credentials'
                    ]
                );
                if (!$tokenResponse->successful()) {
                    throw new Exception('Unable to generate PayPal token.');
                }
                $accessToken = $tokenResponse->json()['access_token'];
                $orderResponse = Http::withToken($accessToken)
                    ->post($baseUrl . '/v2/checkout/orders', [
                        "intent" => "CAPTURE",
                        "purchase_units" => [
                            [
                                "reference_id" => $registration->id,
                                "amount" => [
                                    "currency_code" => "USD",
                                    "value" => number_format(
                                        $registration->grand_total,
                                        2,
                                        '.',
                                        ''
                                    )
                                ]
                            ]
                        ],
                        'application_context' => [
                            'return_url' => route(
                                'web.event.payment.handle',
                                [
                                    'registration_id' => $registration->id
                                ]
                            ),
                            'cancel_url' => $cancelUrl,
                        ]
                    ]);
                $response = $orderResponse->json();
                $approvalUrl = collect($response['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;

                if (!$approvalUrl) {
                    throw new Exception('Paypal approval URL not found.');
                }
                return redirect()->away($approvalUrl);
            }

            /*
            |--------------------------------------------------------------------------
            | Stripe
            |--------------------------------------------------------------------------
            */
            if ($gatewayName === 'stripe') {
                return $this->initiateStripeEventOrder($gateway, $registration, $cancelUrl);
            }

            /*
            |--------------------------------------------------------------------------
            | Cashfree
            |--------------------------------------------------------------------------
            */
            if ($gatewayName === 'cashfree') {
                return $this->initiateCashfreeEventOrder($gateway, $registration);
            }

            /*
            |--------------------------------------------------------------------------
            | PhonePe
            |--------------------------------------------------------------------------
            */
            if ($gatewayName === 'phonepe') {
                return $this->initiatePhonePeEventOrder($gateway, $registration);
            }

            return redirect()->route('web.event.failed')->with('error', 'Unsupported payment gateway.');
        } catch (Exception $e) {
            Log::error('Event Payment Error : ' . $e->getMessage());
            return redirect()->route('web.event.failed')->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | handlePayment — gateway callback (GET OR POST)
    |--------------------------------------------------------------------------
    */
    public function handlePayment(Request $request)
    {
        try {
            $gateway = PaymentMethod::active()->latest()->first();
            if (!$gateway) {
                return redirect()->route('web.event.failed')->with('error', 'Payment gateway not configured.');
            }

            $gatewayName = strtolower(trim($gateway->name));

            /*
            |--------------------------------------------------------------------------
            | Razorpay Verification
            |--------------------------------------------------------------------------
            */
            if ($gatewayName === 'razorpay') {
                $request->validate([
                    'registration_id'      => 'required|exists:events_register,id',
                    'razorpay_payment_id'  => 'required',
                    'razorpay_order_id'    => 'required',
                    'razorpay_signature'   => 'required',
                ]);

                $registration = EventRegister::with('event')->findOrFail($request->registration_id);

                if ($registration->payment_mode === EventRegister::PAYMENT_ONLINE) {
                    return redirect()->route('web.event.success', [
                        'registration' => $registration->id
                    ]);
                }

                $api = new RazorpayApi($gateway->client_id, $gateway->client_secret);

                $attributes = [
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature,
                ];

                $api->utility->verifyPaymentSignature($attributes);

                $this->completeEventRegistration(
                    $registration,
                    $request->razorpay_payment_id,
                    'razorpay',
                    $request->all()
                );

                return redirect()->route('web.event.success', [
                    'registration' => $registration->id
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 
            |--------------------------------------------------------------------------
            */
            if ($gatewayName === 'paypal') {
                $registration = EventRegister::with('event')->findOrFail($request->registration_id);
                if ($registration->payment_mode === EventRegister::PAYMENT_ONLINE) {
                    return redirect()->route('web.event.success', [
                        'registration' => $registration->id
                    ]);
                }

                $orderId = $request->get('token');
                if (!$orderId) {
                    return redirect()->route('web.event.failed');
                }
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
                    return redirect()->route('web.event.failed');
                }
                $accessToken = $tokenResponse->json('access_token');

                ## STEP 2 — Capture Payment :
                $captureResponse = Http::withToken($accessToken)->withBody('', 'application/json')->post($baseUrl . "/v2/checkout/orders/{$orderId}/capture");
                if (!$captureResponse->successful()) {
                    Log::error('PayPal capture failed', ['response' => $captureResponse->json()]);
                    return redirect()->route('web.event.failed')->with('error', 'PayPal capture failed.');
                }

                $captureData = $captureResponse->json();

                ## STEP 3 — Verify Payment Status :
                $orderStatus = data_get($captureData, 'status');

                if ($orderStatus !== 'COMPLETED') {
                    Log::warning('PayPal payment not completed', ['response' => $captureData]);
                    return redirect()->route('web.event.failed')->with('error', 'PayPal payment not completed.');
                }

                $transactionId = data_get(
                    $captureData,
                    'purchase_units.0.payments.captures.0.id'
                );

                if (!$transactionId) {
                    Log::error('PayPal Transaction ID not found', [
                        'response' => $captureData
                    ]);
                    return redirect()->route('web.event.failed')->with('error', 'Transaction ID not found.');
                }

                $this->completeEventRegistration(
                    $registration,
                    $request->token,
                    'paypal',
                    $request->all()
                );

                return redirect()->route('web.event.success', [
                    'registration' => $registration->id
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Stripe Verification
            |--------------------------------------------------------------------------
            */
            if ($gatewayName === 'stripe') {
                $registration = EventRegister::with('event')->findOrFail($request->registration_id);

                if ($registration->payment_mode === EventRegister::PAYMENT_ONLINE) {
                    return redirect()->route('web.event.success', [
                        'registration' => $registration->id
                    ]);
                }

                $sessionId = $request->query('session_id');
                if (!$sessionId) {
                    return redirect()->route('web.event.failed')->with('error', 'Missing Stripe session.');
                }

                $response = Http::withToken($gateway->client_secret)
                    ->get('https://api.stripe.com/v1/checkout/sessions/' . $sessionId);

                if (!$response->successful()) {
                    Log::error('Stripe session fetch failed', ['response' => $response->json()]);
                    return redirect()->route('web.event.failed')->with('error', 'Stripe verification failed.');
                }

                $session = $response->json();

                if (data_get($session, 'payment_status') !== 'paid') {
                    Log::warning('Stripe payment not completed', ['response' => $session]);
                    return redirect()->route('web.event.failed')->with('error', 'Payment not completed.');
                }

                $expectedReference = 'EVT_' . $registration->id;
                if (data_get($session, 'client_reference_id') !== $expectedReference) {
                    Log::error('Stripe reference mismatch', ['response' => $session]);
                    return redirect()->route('web.event.failed')->with('error', 'Payment verification failed.');
                }

                $transactionId = data_get($session, 'payment_intent');
                if (!$transactionId) {
                    return redirect()->route('web.event.failed')->with('error', 'Transaction ID not found.');
                }

                $this->completeEventRegistration(
                    $registration,
                    $transactionId,
                    'stripe',
                    $session
                );

                return redirect()->route('web.event.success', [
                    'registration' => $registration->id
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Cashfree Verification
            |--------------------------------------------------------------------------
            */
            if ($gatewayName === 'cashfree') {
                $registration = EventRegister::with('event')->findOrFail($request->registration_id);

                if ($registration->payment_mode === EventRegister::PAYMENT_ONLINE) {
                    return redirect()->route('web.event.success', [
                        'registration' => $registration->id
                    ]);
                }

                // Always re-derive our own order_id rather than trusting the querystring.
                $orderId = 'EVT_' . $registration->id;

                $response = Http::withHeaders($this->cashfreeHeaders($gateway))
                    ->get($this->cashfreeBaseUrl($gateway) . '/orders/' . $orderId);

                if (!$response->successful()) {
                    Log::error('Cashfree order fetch failed', ['response' => $response->json()]);
                    return redirect()->route('web.event.failed')->with('error', 'Cashfree verification failed.');
                }

                $order = $response->json();

                if (data_get($order, 'order_status') !== 'PAID') {
                    Log::warning('Cashfree payment not completed', ['response' => $order]);
                    return redirect()->route('web.event.failed')->with('error', 'Payment not completed.');
                }

                $paymentsResponse = Http::withHeaders($this->cashfreeHeaders($gateway))
                    ->get($this->cashfreeBaseUrl($gateway) . '/orders/' . $orderId . '/payments');

                $transactionId = data_get($paymentsResponse->json(), '0.cf_payment_id');

                if (!$transactionId) {
                    Log::error('Cashfree Transaction ID not found', ['response' => $paymentsResponse->json()]);
                    return redirect()->route('web.event.failed')->with('error', 'Transaction ID not found.');
                }

                $this->completeEventRegistration(
                    $registration,
                    $transactionId,
                    'cashfree',
                    $order
                );

                return redirect()->route('web.event.success', [
                    'registration' => $registration->id
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | PhonePe Verification
            |--------------------------------------------------------------------------
            */
            if ($gatewayName === 'phonepe') {
                $registration = EventRegister::with('event')->findOrFail($request->registration_id);

                if ($registration->payment_mode === EventRegister::PAYMENT_ONLINE) {
                    return redirect()->route('web.event.success', [
                        'registration' => $registration->id
                    ]);
                }

                [$saltKey, $saltIndex] = $this->phonePeSalt($gateway);

                $merchantTransactionId = 'EVT_' . $registration->id;
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
                    return redirect()->route('web.event.failed')->with('error', 'PhonePe verification failed.');
                }

                $data = $response->json();

                if (data_get($data, 'code') !== 'PAYMENT_SUCCESS') {
                    Log::warning('PhonePe payment not completed', ['response' => $data]);
                    return redirect()->route('web.event.failed')->with('error', 'Payment not completed.');
                }

                $transactionId = data_get($data, 'data.transactionId');
                if (!$transactionId) {
                    Log::error('PhonePe Transaction ID not found', ['response' => $data]);
                    return redirect()->route('web.event.failed')->with('error', 'Transaction ID not found.');
                }

                $this->completeEventRegistration(
                    $registration,
                    $transactionId,
                    'phonepe',
                    $data
                );

                return redirect()->route('web.event.success', [
                    'registration' => $registration->id
                ]);
            }

            return redirect()->route('web.event.failed')->with('error', 'Unsupported payment gateway.');
        } catch (\Exception $e) {

            Log::error(
                'Event Payment Verification Failed: ' .
                    $e->getMessage()
            );

            return redirect()
                ->route('web.event.failed')
                ->with(
                    'error',
                    'Payment verification failed.'
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Stripe — order initiation
    |--------------------------------------------------------------------------
    | PaymentMethod field mapping: client_secret => Stripe secret key.
    */
    private function initiateStripeEventOrder(PaymentMethod $gateway, EventRegister $registration, string $cancelUrl)
    {
        $reference = 'EVT_' . $registration->id;

        $successUrl = route('web.event.payment.handle', ['registration_id' => $registration->id])
            . '&session_id={CHECKOUT_SESSION_ID}';

        $response = Http::withToken($gateway->client_secret)->asForm()->post(
            'https://api.stripe.com/v1/checkout/sessions',
            [
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'client_reference_id' => $reference,
                'metadata' => [
                    'registration_id' => $registration->id,
                ],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'inr',
                            'product_data' => [
                                'name' => $registration->event->title ?? 'Event Ticket',
                            ],
                            'unit_amount' => (int) round($registration->grand_total * 100),
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
            throw new Exception('Unable to create Stripe session.');
        }

        $checkoutUrl = $response->json('url');
        if (!$checkoutUrl) {
            throw new Exception('Stripe checkout URL not found.');
        }

        return redirect()->away($checkoutUrl);
    }

    /*
    |--------------------------------------------------------------------------
    | Cashfree — order initiation & shared helpers
    |--------------------------------------------------------------------------
    | PaymentMethod field mapping:
    |   client_id     => Cashfree App ID     (x-client-id)
    |   client_secret => Cashfree Secret Key (x-client-secret)
    |   payment_mode  => 'Test' => sandbox, else production
    */
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

    private function initiateCashfreeEventOrder(PaymentMethod $gateway, EventRegister $registration)
    {
        $orderId = 'EVT_' . $registration->id;

        $returnUrl = route('web.event.payment.handle', ['registration_id' => $registration->id])
            . '&order_id={order_id}';

        $response = Http::withHeaders($this->cashfreeHeaders($gateway))->post(
            $this->cashfreeBaseUrl($gateway) . '/orders',
            [
                'order_id' => $orderId,
                'order_amount' => (float) number_format($registration->grand_total, 2, '.', ''),
                'order_currency' => 'INR',
                'customer_details' => [
                    'customer_id' => 'EVT_CUST_' . $registration->id,
                    'customer_name' => $registration->name,
                    'customer_email' => $registration->email,
                    'customer_phone' => $registration->mobile ?: '9999999999',
                ],
                'order_meta' => [
                    'return_url' => $returnUrl,
                ],
            ]
        );

        if (!$response->successful()) {
            Log::error('Cashfree order creation failed', ['response' => $response->json()]);
            throw new Exception('Unable to create Cashfree order.');
        }

        $order = $response->json();
        $paymentSessionId = data_get($order, 'payment_session_id');

        if (!$paymentSessionId) {
            throw new Exception('Cashfree payment session not found.');
        }

        return view(
            _getConstant('dir_path.WEB_DIR_PATH') . '.events.payments.cashfree_redirect',
            [
                'paymentSessionId' => $paymentSessionId,
                'gateway' => $gateway,
                'registration' => $registration,
                'mode' => $gateway->payment_mode === 'Test' ? 'sandbox' : 'production',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PhonePe — order initiation & shared helpers
    |--------------------------------------------------------------------------
    | PaymentMethod field mapping:
    |   client_id     => PhonePe Merchant ID
    |   client_secret => "SALT_KEY|SALT_INDEX" (defaults index to 1 if omitted)
    |   payment_mode  => 'Test' => UAT/sandbox host, else production
    |
    | NOTE: implements the classic X-VERIFY "Standard Checkout" flow.
    | Confirm against your current PhonePe dashboard/docs before going live.
    */
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

    private function initiatePhonePeEventOrder(PaymentMethod $gateway, EventRegister $registration)
    {
        [$saltKey, $saltIndex] = $this->phonePeSalt($gateway);

        $merchantTransactionId = 'EVT_' . $registration->id;
        $endpoint = '/pg/v1/pay';

        $redirectUrl = route('web.event.payment.handle', ['registration_id' => $registration->id]);

        $payload = [
            'merchantId' => $gateway->client_id,
            'merchantTransactionId' => $merchantTransactionId,
            'merchantUserId' => 'EVT_USER_' . $registration->id,
            'amount' => (int) round($registration->grand_total * 100),
            'redirectUrl' => $redirectUrl,
            'redirectMode' => 'POST',
            'callbackUrl' => $redirectUrl,
            'mobileNumber' => preg_replace('/\D/', '', (string) $registration->mobile) ?: null,
            'paymentInstrument' => [
                'type' => 'PAY_PAGE',
            ],
        ];

        $base64Payload = base64_encode(json_encode(array_filter($payload, fn ($v) => $v !== null)));
        $checksum = hash('sha256', $base64Payload . $endpoint . $saltKey) . '###' . $saltIndex;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-VERIFY' => $checksum,
        ])->post($this->phonePeBaseUrl($gateway) . $endpoint, [
            'request' => $base64Payload,
        ]);

        if (!$response->successful()) {
            Log::error('PhonePe order creation failed', ['response' => $response->json()]);
            throw new Exception('Unable to create PhonePe order.');
        }

        $data = $response->json();
        $payUrl = data_get($data, 'data.instrumentResponse.redirectInfo.url');

        if (!$payUrl) {
            Log::error('PhonePe redirect URL missing', ['response' => $data]);
            throw new Exception('PhonePe redirect URL not found.');
        }

        return redirect()->away($payUrl);
    }

    private function completeEventRegistration(EventRegister $registration, string $transactionId, string $gateway, array $response = [])
    {
        DB::transaction(function () use ($registration, $transactionId, $gateway, $response) {

            $registration->update([
                'gateway_name'     => $gateway,
                'payment_mode'     => EventRegister::PAYMENT_ONLINE,
                'transaction_id'   => $transactionId,
                'payment_response' => json_encode($response),
            ]);

            Event::where('id', $registration->event_id)->increment('sold_tickets', $registration->tickets_qty);

            $replaceArr = [
                'user_name'        => $registration->name,
                'event_name'       => $registration->event->title,
                'event_ticket_qty' => $registration->tickets_qty,
                'event_date'       => _displayDate(
                    $registration->event->event_date,
                    'j F, Y'
                ),
                'event_venue'      => $registration->event->venue ?? '',
            ];

            app(EmailSendService::class)->send('Event Registration', $registration->email, $replaceArr);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | success — Booking confirmed page
    |--------------------------------------------------------------------------
    */
    public function success(Request $request)
    {
        $registration = EventRegister::with('event')->findOrFail($request->registration);

        $status = 'Success';

        return view(
            _getConstant('dir_path.WEB_DIR_PATH') . '.events.status',
            compact('status', 'registration')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | failed — Booking failed page
    |--------------------------------------------------------------------------
    */
    public function failed(Request $request)
    {
        $status = 'Failed';

        $error = session(
            'error',
            'Your payment could not be processed. Please try again.'
        );

        $registration = null;

        if ($request->filled('registration_id')) {
            $registration = EventRegister::find($request->registration_id);
        }

        return view(
            _getConstant('dir_path.WEB_DIR_PATH') . '.events.status',
            compact('status', 'error', 'registration')
        );
    }

    public function downloadInvoice(Request $request, $registrationId)
    {
        $registration = EventRegister::with('event')->where('id', $registrationId)->firstOrFail();

        // Only allow download for paid bookings
        if ($registration->payment_mode !== EventRegister::PAYMENT_ONLINE) {
            abort(403, 'Invoice is only available for confirmed paid bookings.');
        }

        $invoiceNumber = 'INV-' . str_pad($registration->id, 6, '0', STR_PAD_LEFT);
        $invoiceDate   = $registration->created_at->format('d M, Y');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            _getConstant('dir_path.WEB_DIR_PATH') . '.events.invoice',
            compact('registration', 'invoiceNumber', 'invoiceDate')
        )
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
            ]);

        $filename = 'Invoice-' . $invoiceNumber . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Load an approved, non-deleted event from
     */
    private function getEventOrFail(int|string $id): Event
    {
        return Event::where('id', $id)->where('status', 'APPROVED')->firstOrFail();
    }
}
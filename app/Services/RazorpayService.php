<?php

namespace App\Services;

use Razorpay\Api\Api;

class RazorpayService
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    public function createOrder($amount, $receipt)
    {
        return $this->api->order->create([
            'receipt' => $receipt,
            'amount' => $amount * 100,
            'currency' => 'INR'
        ]);
    }

    public function verifySignature($data)
    {
        $this->api->utility->verifyPaymentSignature($data);
    }
}
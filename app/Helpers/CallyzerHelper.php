<?php

namespace App\Helpers;

use App\Models\CallyzerApiSetting;
use Illuminate\Support\Facades\Http;

class CallyzerHelper
{
    public static function getCallyzerApiRequest($type, $payloadUrl, $payload)
    {
        $callyzerApiSetting = CallyzerApiSetting::active()->first();

        if (!$callyzerApiSetting) {
            return [
                'status' => false,
                'message' => 'Callyzer API settings not configured or inactive.',
            ];
        }

        if ($callyzerApiSetting->api_mode === 'Live') {
            $apiUrl = 'https://api1.callyzer.co/v2.1/';
        } else {
            $apiUrl = 'https://sandbox.api.callyzer.co/api/v2.1/';
        }

        // API URL
        $apiUrl .= $payloadUrl;

        // Token
        $bearerToken = $callyzerApiSetting->api_key;

        if (blank($bearerToken)) {
            return [
                'status' => false,
                'message' => 'Callyzer API key is not configured.',
            ];
        }

        $request = Http::withToken($bearerToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);

        // Send request based on method type
        if ($type === 'POST') {
            $response = $request->post($apiUrl, $payload);
        } else {
            $response = $request
                ->withBody(json_encode($payload), 'application/json')
                ->get($apiUrl);
        }

        return $response->json();
    }
}

<?php

namespace App\Services;

use App\Models\MemberAlertSetting;
use App\Models\Register;
use App\Models\SmsTemplate;
use Illuminate\Support\Facades\Log;

class SmsSendService
{
    public function sendTemplate(string $templateName, Register $member, array $replacements = []): void
    {
        $memberId = $member->id;
        $mobile = $member->mobile;

        if (blank($mobile)) {
            return;
        }

        $template = SmsTemplate::active()
            ->where('template_name', $templateName)
            ->first();

        if (!$template) {
            return;
        }

        ## CHECK USER SMS SETTING
        $isEnabled = MemberAlertSetting::isEnabled(
            $memberId,
            $template->id,
            'sms'
        );
        if (!empty($isEnabled)) {
            return;
        }

        $settings = _getSiteSetting();

        $replacements = array_merge($replacements, [
            'web_frienly_name' => $settings['web_frienly_name'] ?? '',
            'web_name'         => $settings['web_name'] ?? '',
        ]);

        $message = $template->parseContent($replacements);

        $this->sendRawSms(
            mobile: $mobile,
            message: $message,
            templateId: $template->template_id
        );
    }

    public function sendRawSms(
        string $mobile,
        string $message,
        ?string $templateId = null
    ): void {
        $settings = _getSiteSetting();

        // Check whether SMS is approved/enabled
        if (($settings['sms_api_status'] ?? '') !== 'APPROVED') {
            return;
        }

        // Format and validate Indian mobile number
        $formattedNumber = $this->formatIndianNumber($mobile);

        if (!$formattedNumber) {
            Log::warning('Pinnacle SMS skipped: Invalid mobile number.');
            return;
        }

        $apiUrl = $settings['sms_api_url'] ?? '';
        $apiKey = $settings['sms_api'] ?? '';
        $senderId = $settings['sms_api_sender_id'] ?? '';

        if (empty($apiKey)) {
            Log::error('Pinnacle SMS failed: API key is missing.');
            return;
        }

        $curl = curl_init($apiUrl);

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => '{
            "sender":"'.$senderId.'",
            "message":[
                    {
                        "number":"' . $formattedNumber . '",
                        "text":"' . $message . '"
                    }
                ],
                "messagetype":"TXT",
                "dlttempid":"' . $templateId . '"
            }',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'apikey: ' . $apiKey,
            ],
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($curl);
        $curlError = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($response === false) {
            Log::error('Pinnacle SMS connection failed', [
                'error' => $curlError,
                'http_code' => $httpCode,
            ]);

            return;
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            Log::error('Pinnacle SMS HTTP error', [
                'http_code' => $httpCode,
                'response' => $response,
            ]);

            return;
        }

        Log::info('Pinnacle SMS API response received', [
            'http_code' => $httpCode,
            'response' => $response,
        ]);
    }

    private function formatIndianNumber(string $mobile): ?string
    {
        // Expected: +91-9876543210
        if (!str_contains($mobile, '-')) {
            return null;
        }

        [$countryCode, $number] = explode('-', $mobile);

        if ($countryCode !== '+91') {
            return null;
        }

        return '91' . $number;
    }

    private function buildApiUrl(string $apiTemplate, string $mobile, string $message, ?string $templateId): string
    {
        $url = str_replace('##contacts##', $mobile, $apiTemplate);
        $url = str_replace('##sms_text##', urlencode($message), $url);

        if ($templateId) {
            $url = str_replace('##template_id##', $templateId, $url);
        }

        return $url;
    }

    public function sendCustomTemplate(
        string $templateName,
        string $mobile,
        array $replacements = []
    ): void {
        $template = SmsTemplate::active()->where('template_name', $templateName)->first();

        if (!$template) {
            return;
        }

        $message = $template->parseContent($replacements);

        $this->sendRawSms(
            mobile: $mobile,
            message: $message,
            templateId: $template->template_id
        );
    }
}

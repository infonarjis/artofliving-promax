<?php

namespace App\Services;

use App\Models\MemberAlertSetting;
use App\Models\Register;
use App\Models\SmsTemplate;

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

    public function sendRawSms(string $mobile, string $message, ?string $templateId = null): void
    {
        $settings = _getSiteSetting();

        if (($settings['sms_api_status'] ?? '') !== 'APPROVED') {
            return;
        }

        $formattedNumber = $this->formatIndianNumber($mobile);

        if (!$formattedNumber) {
            return;
        }

        $apiUrl = $this->buildApiUrl(
            apiTemplate: $settings['sms_api'],
            mobile: $formattedNumber,
            message: $message,
            templateId: $templateId
        );

        $curl = curl_init($apiUrl);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 5,
        ]);

        curl_exec($curl);
        curl_close($curl);
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

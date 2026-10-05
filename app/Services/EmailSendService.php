<?php

namespace App\Services;

use App\Mail\DynamicTemplateMail;
use App\Models\EmailTemplate;
use App\Models\MemberAlertSetting;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EmailSendService
{
    public function send(string $templateName, ?string $toEmail, array $replacements = [], array $options = []): void
    {
        try {
            // Handles null / empty / invalid emails
            if (empty($toEmail) || !$this->isValidEmail($toEmail)) {
                Log::warning("Invalid or empty email, skipping send.", [
                    'template' => $templateName,
                    'email'    => $toEmail,
                ]);
                return;
            }

            $template = EmailTemplate::active()
                ->where('template_name', $templateName)
                ->first();

            if (!$template) {
                Log::error("Email template not found: {$templateName}");
                return;
            }

            ## CHECK USER EMAIL SETTING :
            $member = $options['memberData'] ?? null;
            if ($member !== null) {
                $isEnabled = MemberAlertSetting::isEnabled(
                    $member->id,
                    $template->id,
                    'email'
                );
                if (!empty($isEnabled)) {
                    return;
                }
            }

            ## Merge system variables :
            $replacements = $this->defaultReplacements($replacements);

            $subject = $this->replaceSubjectVariables($template->email_subject, $replacements);
            $replacements['email_subject'] = $subject;

            $content = $this->replaceVariables($template->email_content, $replacements);

            ## Send Mail :
            $this->sendMail($toEmail, $subject, $content, $options);
        } catch (Exception $e) {
            ## Ignore all errors :
            Log::error('Email Send failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile()
            ]);

            return;
        }
    }

    public function sendMail(?string $toEmail, string $subject, string $content, array $options = []): void
    {
        if (empty($toEmail) || !$this->isValidEmail($toEmail)) {
            return;
        }

        Mail::to($toEmail)
            ->queue(new DynamicTemplateMail(
                subject: $subject,
                content: $content,
                cc: $options['cc'] ?? [],
                bcc: $options['bcc'] ?? [],
                files: $options['files'] ?? []
            ));
    }

    private function isValidEmail(string $email): bool
    {
        $email = trim((string) $email);

        if ($email === '') {
            return false;
        }

        return Validator::make(
            ['email' => $email],
            ['email' => 'required|email']
        )->passes();
    }

    private function replaceVariables(string $content, array $replacements): string
    {
        if (!empty($replacements)) {
            $replace = [];
            foreach ($replacements as $key => $value) {
                $replace["##{$key}##"] = $value;
            }
            return strtr($content, $replace);
        }
        return $content;
    }

    private function replaceSubjectVariables(string $content, array $replacements): string
    {
        if (!empty($replacements)) {
            $replace = [];
            foreach ($replacements as $key => $value) {
                $replace["##{$key}##"] = $value;
            }
            return strtr($content, $replace);
        }
        return $content;
    }

    private function defaultReplacements(array $replacements): array
    {
        $configArr = _getSiteSetting();

        $logoUrl = _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'];
        $templateImageUrl = _assetUrl('upload_path.EMAIL_TEMPLATES');

        return array_merge($replacements, [
            'web_url' => url('/'),
            'logo_url' => $logoUrl,
            'web_friendly_name' => $configArr['web_name'],

            'contact_number' => $configArr['contact_no'],
            'contact_email' => $configArr['contact_email'],

            'privacy_policy_url' => route('web.cmsPages.index', 'terms-condition'),
            'contact_us_url' => route('web.contactUs.index'),

            'login_url' => route('web.login.index'),
            'membership_url' => route('web.membershipPlan.index'),
            'matches_url' => route('web.matches.recommended'),
            'succes_story_url' => route('web.successStory.index'),
            'search_url' => route('web.search.searchResult'),

            'play_store_url' => $configArr['android_app_link'],
            'ios_store_url' => $configArr['ios_app_link'],

            'instagram_link' => $configArr['instagram_link'],
            'facebook_link' => $configArr['facebook_link'],
            'youtube_link' => $configArr['youtube_link'],
            'twitter_link' => $configArr['twitter_link'],

            'footer_copywright_text' => $configArr['footer_text'],

            'template_image_url' => $templateImageUrl,
        ]);
    }
}

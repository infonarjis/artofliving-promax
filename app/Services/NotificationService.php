<?php

namespace App\Services;

use App\Models\MemberAlertSetting;
use App\Models\MemberNotification;
use App\Models\NotificationTemplate;
use Google\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotificationService
{
    protected array $credentials;

    public function __construct()
    {
        $this->credentials = $this->getCredentials();
    }

    /**
     * Load Firebase JSON from DB and cache forever
     */
    public static function getCredentials(): array
    {
        return Cache::rememberForever('firebase_credentials', function (): array {
            $configArr = _getSiteSetting();

            if (empty($configArr['firebase_json'])) {
                return [];
            }

            $credentials = json_decode($configArr['firebase_json'], true);

            return is_array($credentials) ? $credentials : [];
        });
    }

    /**
     * Cache OAuth access token for 55 minutes
     */
    protected function getAccessToken(): string
    {
        return Cache::remember('firebase_access_token', 3300, function () {
            $client = new Client();
            $client->setAuthConfig($this->credentials);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $token = $client->fetchAccessTokenWithAssertion();

            return $token['access_token'];
        });
    }

    protected function endpoint(): string
    {
        return "https://fcm.googleapis.com/v1/projects/{$this->credentials['project_id']}/messages:send";
    }

    protected function send(array $target, string $title, string $body, array $data)
    {
        try {
            $payload = [
                'message' => array_merge($target, [
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => $data,
                ]),
            ];

            $response = Http::withToken($this->getAccessToken())
                ->timeout(10)
                ->post($this->endpoint(), $payload);

            return $response->json();
        } catch (Throwable $e) {
            // Ignore all errors
            return null;
        }
    }

    public function sendNotification($viewer, $receiver, string $notiType, $dataArr = [], int $insert = 1)
    {
        if (!$receiver) {
            Log::warning('Notification receiver is null.', [
                'notification_type' => $notiType,
                'viewer_id' => $viewer?->id,
            ]);

            return null;
        }

        ## Collect ALL available device tokens instead of picking just one :
        $tokens = array_filter([
            'web'     => $receiver->web_device_id ?? null,
            'android' => $receiver->android_device_id ?? null,
            'ios'     => $receiver->ios_device_id ?? null,
        ]);

        $template = NotificationTemplate::where([
            'notification_type' => $notiType,
            'status' => 'APPROVED'
        ])->first();

        if (!$template) {
            return;
        }

        $data = array_merge([
            'viewer_id'   => $viewer?->matri_id,
            'receiver_id' => $receiver?->matri_id,
        ], $dataArr);

        $title   = $this->parseNotificationTemplate($template->title, $data);
        $message = $this->parseNotificationTemplate($template->description, $data);

        $payload = [
            'type'      => $notiType,
            'viewer_id' => (string) $viewer->id,
            // 'viewer_image' => ''
        ];
        if (isset($dataArr['conversation_id'])) {
            $payload['conversation_id'] = $dataArr['conversation_id'];
        }

        ## ALWAYS STORE IN DATABASE (In-app notification) :
        if ($insert === 1) {
            MemberNotification::create([
                'sender_member_id'   => $viewer->id ?? null,
                'receiver_member_id' => $receiver->id ?? null,
                'sender_matri_id'    => $viewer->matri_id,
                'receiver_matri_id'  => $receiver->matri_id,
                'title'              => $title,
                'message'            => $message,
                'action'             => $notiType,
                'notification_by'    => 0,
                'image'              => null,
                'is_read'            => 0,
                'status'             => 1,
                'created_at'         => _getCurrentDate()
            ]);
        }

        ## CHECK USER SETTING ONLY FOR PUSH NOTIFICATION :
        $isEnabled = MemberAlertSetting::isEnabled(
            $viewer->id,
            $template->id,
            'notification'
        );

        if (!empty($isEnabled)) {
            return null;
        } elseif (empty($tokens)) {
            return null;
        }

        ## SEND PUSH NOTIFICATION (FCM) TO EVERY LOGGED-IN DEVICE:
        $results = [];
        foreach ($tokens as $platform => $token) {
            $results[$platform] = $this->send(
                ['token' => $token],
                $title,
                $message,
                $payload
            );
        }

        return $results;
    }

    function parseNotificationTemplate(string $template, array $data = []): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    }

    public function sendToMember($member, string $title, string $message, $notiType = '')
    {
        ## Collect ALL available device tokens instead of picking just one :
        $tokens = array_filter([
            'web'     => $member->web_device_id ?? null,
            'android' => $member->android_device_id ?? null,
            'ios'     => $member->ios_device_id ?? null,
        ]);

        if (empty($tokens)) {
            return;
        }

        $results = [];
        foreach ($tokens as $platform => $token) {
            $results[$platform] = $this->send(
                ['token' => $token],
                $title,
                $message,
                ['type' => $notiType ?? 'bulk']
            );
        }

        return $results;
    }

    public function sendToTopic(string $topic, string $title, string $body, array $data = [])
    {
        try {
            return $this->send(['topic' => $topic], $title, $body, $data);
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Call when admin updates Firebase JSON
     */
    public static function clearCache(): void
    {
        Cache::forget('firebase_credentials');
        Cache::forget('firebase_access_token');
    }
}

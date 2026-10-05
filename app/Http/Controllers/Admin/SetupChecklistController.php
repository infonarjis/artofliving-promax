<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\QueueHeartbeatJob;
use App\Models\CallyzerApiSetting;
use App\Models\MembershipPlan;
use App\Models\PaymentMethod;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SetupChecklistController extends Controller
{
    ## Cache keys used by the scheduler / queue heartbeat mechanism :
    private const SCHEDULER_CACHE_KEY = 'setup_checklist_scheduler_heartbeat';
    private const QUEUE_CACHE_KEY = 'setup_checklist_queue_heartbeat';
    private const HEARTBEAT_FRESH_MINUTES = 10;

    private array $manualKeys = [
        'payment_gateway',
        'membership_plan',
    ];

    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
    }

    ## Main Checklist Page :
    public function index()
    {
        $groups = $this->buildChecklist();
        $counts = $this->countProgress($groups);

        $dataArr = [
            'pageName' => 'Setup / Installation Checklist',
            'groups' => $groups,
            'totalItems' => $counts['total'],
            'completedItems' => $counts['done'],
            'percentDone' => $counts['percent'],
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/setupChecklist/index', $dataArr);
    }

    ## Re-Check Everything Via AJAX (optional, used by "Re-Check Status" button) :
    public function statusJson()
    {
        $groups = $this->buildChecklist();
        $counts = $this->countProgress($groups);

        return response()->json([
            'status' => 'success',
            'groups' => $groups,
            'totalItems' => $counts['total'],
            'completedItems' => $counts['done'],
            'percentDone' => $counts['percent'],
        ], 200);
    }

    ## Toggle A Manually-Verified Item (Payment Gateway, Membership Plan, etc.) :
    public function toggleManualStatus(Request $request)
    {
        $responseArr = ['status' => 'error', 'msg' => 'Invalid item.'];

        $key = $request->input('key');
        if (!in_array($key, $this->manualKeys, true)) {
            return response()->json($responseArr, 200);
        }

        $current = (bool) Cache::get('setup_checklist_manual_' . $key, false);
        Cache::forever('setup_checklist_manual_' . $key, !$current);

        $responseArr['status'] = 'success';
        $responseArr['msg'] = 'Status updated.';
        $responseArr['newStatus'] = !$current;
        return response()->json($responseArr, 200);
    }

    ## Dispatch A Job To Test If The Queue Worker Is Actually Running :
    public function testQueue()
    {
        QueueHeartbeatJob::dispatch();

        return response()->json([
            'status' => 'success',
            'msg' => 'Test job dispatched. Refresh this page in a few seconds — if your queue worker is running, "Queue Worker Running" will turn green.',
        ], 200);
    }

    private function buildChecklist(): array
    {
        $settings = function_exists('_getSiteSetting')
            ? (array) _getSiteSetting()
            : (array) (SiteSetting::find(1)?->toArray() ?? []);

        $groups = [];

        ## SSl Check:
        $sslInfo = $this->checkSslCertificate();
        $sslStatus = $sslInfo['secure'];
        $sslHint = 'Force HTTPS via hosting SSL settings or .htaccess';

        if ($sslInfo['secure'] && $sslInfo['expires_at']) {
            $expiresLabel = $sslInfo['expires_at']->format('d M Y');

            if ($sslInfo['days_remaining'] <= 0) {
                $sslStatus = false;
                $sslHint = "Certificate EXPIRED on {$expiresLabel} — renew immediately";
            } elseif ($sslInfo['days_remaining'] <= 15) {
                $sslStatus = false;
                $sslHint = "Certificate expires {$expiresLabel} ({$sslInfo['days_remaining']} days left) — renew soon";
            } else {
                $sslHint = "Certificate valid until {$expiresLabel} ({$sslInfo['days_remaining']} days remaining)";
            }
        } elseif ($sslInfo['secure']) {
            $sslHint = 'HTTPS active, but could not read certificate expiry (blocked on localhost/firewall?)';
        }

        ## 1) Server & Environment
        $groups['Server & Environment'] = [
            'items' => [
                $this->item('app_key', 'Application Key (APP_KEY) Set', !empty(config('app.key')), null, 'Run: php artisan key:generate'),
                $this->item('app_debug', 'Debug Mode Disabled On Production', $this->checkDebugMode(), null, 'Set APP_DEBUG=false in .env for production'),
                // $this->item('ssl', 'HTTPS / SSL Active', request()->isSecure(), null, 'Force HTTPS via hosting SSL settings or .htaccess'),
                $this->item('ssl', 'HTTPS / SSL Active', $sslStatus, null, $sslHint),
                $this->item('storage_link', 'Storage Link Created', $this->checkStorageLink(), null, 'Run: php artisan storage:link'),
                $this->item('scheduler', 'Task Scheduler Running In Cron', $this->checkHeartbeat(self::SCHEDULER_CACHE_KEY), null, 'Add to server crontab: * * * * * php ' . base_path() . '/artisan schedule:run >> /dev/null 2>&1'),
                $this->item('queue', 'Queue Worker Running', $this->checkHeartbeat(self::QUEUE_CACHE_KEY), null, 'Run: php artisan queue:work (or set up Supervisor) — click "Test Queue Worker" to verify'),
            ],
        ];

        ## 2) SEO
        $groups['SEO'] = [
            'items' => [
                $this->item('sitemap', 'Sitemap Generated', File::exists(public_path('sitemap.xml')), 'admin.seoSettings.index', 'Generate from SEO Settings page'),
                $this->item('robots', 'robots.txt Configured', File::exists(public_path('robots.txt')) && filesize(public_path('robots.txt')) > 0, 'admin.seoSettings.index', 'Update from SEO Settings page'),
            ],
        ];

        ## 3) Basic Site Setup
        $groups['Basic Site Setup'] = [
            'items' => [
                $this->item(
                    'basic_site_setting',
                    'Basic Site Setting (Name, Contact, Address)',
                    !empty($settings['web_name'] ?? null) && !empty($settings['contact_no'] ?? null) && !empty($settings['full_address'] ?? null),
                    'admin.siteSetting',
                    'Fill the Basic Site Setting form'
                ),
                $this->item(
                    'logo_favicon',
                    'Logo & Favicon Uploaded',
                    !empty($settings['upload_logo'] ?? null) && !empty($settings['upload_favicon'] ?? null),
                    'admin.siteSetting',
                    'Upload from Basic Site Setting > Logo & Favicon tab'
                ),
                $this->item(
                    'currency_country',
                    'Default Currency & Country Code Set',
                    !empty($settings['default_currency'] ?? null) && !empty($settings['default_country_code'] ?? null),
                    'admin.siteSetting',
                    'Set from Basic Site Setting form'
                ),
            ],
        ];

        ## 4) Communication
        $groups['Communication'] = [
            'items' => [
                $this->item(
                    'email_smtp',
                    'Email Configuration (SMTP)',
                    (($settings['mail_send_status'] ?? null) === 'Enabled')
                        && !empty($settings['mail_host'] ?? null)
                        && !empty($settings['mail_username'] ?? null)
                        && !empty($settings['mail_password'] ?? null),
                    'admin.thirdPartySetting.index',
                    'Fill SMTP details, then send a test email from the Email tab'
                ),
                $this->item(
                    'sms',
                    'SMS Integration',
                    (($settings['sms_api_status'] ?? null) === 'APPROVED') && !empty($settings['sms_api'] ?? null),
                    'admin.smsConfigurationAddEditForm',
                    'Add SMS API key from SMS Api Configuration page'
                ),
                $this->item(
                    'whatsapp',
                    'WhatsApp Integration',
                    (($settings['whatsapp_api_status'] ?? null) === 'APPROVED') && !empty($settings['whatsapp_api'] ?? null),
                    'admin.whatsappConfigurationAddEditForm',
                    'Add WhatsApp API key from Whatsapp Configuration page'
                ),
            ],
        ];

        ## 5) Push Notifications & Realtime Calling
        $groups['Push Notifications & Calling'] = [
            'items' => [
                $this->item(
                    'firebase',
                    'Firebase Push Notification Setting',
                    (($settings['firebase_status'] ?? null) === 'APPROVED')
                        && !empty($settings['firebase_project_id'] ?? null)
                        && !empty($settings['firebase_vapid_key'] ?? null)
                        && !empty($settings['firebase_json'] ?? null),
                    'admin.thirdPartySetting.index',
                    'Configure from Third Party Settings > Firebase tab'
                ),
                $this->item(
                    'zego_video',
                    'Zego Cloud - Video Call Enabled',
                    (($settings['zego_video_call_setting'] ?? null) === 'APPROVED')
                        && !empty($settings['zegocloud_appid'] ?? null)
                        && !empty($settings['zegocloud_server_secret_key'] ?? null),
                    'admin.thirdPartySetting.index',
                    'Configure from Third Party Settings > Zego Cloud tab'
                ),
                $this->item(
                    'zego_voice',
                    'Zego Cloud - Voice Call Enabled',
                    (($settings['zego_voice_call_setting'] ?? null) === 'APPROVED')
                        && !empty($settings['zegocloud_appid'] ?? null)
                        && !empty($settings['zegocloud_server_secret_key'] ?? null),
                    'admin.thirdPartySetting.index',
                    'Configure from Third Party Settings > Zego Cloud tab'
                ),
            ],
        ];

        ## 6) AI & Analytics
        $groups['AI & Analytics'] = [
            'items' => [
                $this->item(
                    'gemini',
                    'Gemini AI Api Key',
                    (($settings['gemini_api_status'] ?? null) === 'APPROVED') && !empty($settings['gemini_api_key'] ?? null),
                    'admin.thirdPartySetting.index',
                    'Add key from Third Party Settings > AI Api Keys tab'
                ),
                $this->item(
                    'google_analytics',
                    'Google Analytics Code',
                    !empty($settings['google_analytics_code'] ?? null),
                    'admin.siteSetting',
                    'Add code from Basic Site Setting > Google Analytics tab'
                ),
            ],
        ];

        ## 7) Payments
        $groups['Payments'] = [
            'items' => [
                $this->manualOrAutoItem(
                    'payment_gateway',
                    'Payment Gateway Added / Enabled (Razorpay, PayPal, Stripe, etc.)',
                    $this->checkPaymentGateway(),
                    'admin.paymentOptions.index',
                    'Add & approve at least one gateway from Payment Options page'
                ),
            ],
        ];

        ## 8) Third-Party / Extra
        $groups['Third-Party / Extra'] = [
            'items' => [
                $this->item(
                    'callyzer',
                    'Callyzer Api Setting',
                    $this->checkCallyzer(),
                    'admin.callyzerApiSetting.index',
                    'Add & approve an API key from Callyzer Api Settings page'
                ),
                $this->manualOrAutoItem(
                    'membership_plan',
                    'At Least One Membership Plan Created',
                    $this->checkMembershipPlan(),
                    'admin.membershipPlan.index',
                    'Create a membership plan from Membership Plan page'
                ),
            ],
        ];

        ## Attach A Clickable URL To Every Item That Has A Route :
        foreach ($groups as $groupKey => $group) {
            foreach ($group['items'] as $idx => $item) {
                $groups[$groupKey]['items'][$idx]['url'] = $item['route'] ? route($item['route']) : null;
            }
        }

        return $groups;
    }

    private function countProgress(array $groups): array
    {
        $total = 0;
        $done = 0;
        foreach ($groups as $group) {
            foreach ($group['items'] as $item) {
                $total++;
                if ($item['status'] === true) {
                    $done++;
                }
            }
        }
        return [
            'total' => $total,
            'done' => $done,
            'percent' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
        ];
    }

    private function item(string $key, string $label, bool $status, ?string $route, string $hint): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'status' => $status,
            'route' => $route,
            'hint' => $hint,
            'type' => 'auto',
        ];
    }

    private function manualOrAutoItem(string $key, string $label, ?bool $autoStatus, ?string $route, string $hint): array
    {
        if ($autoStatus !== null) {
            return $this->item($key, $label, $autoStatus, $route, $hint);
        }

        return [
            'key' => $key,
            'label' => $label,
            'status' => (bool) Cache::get('setup_checklist_manual_' . $key, false),
            'route' => $route,
            'hint' => $hint . ' — could not auto-detect this on your schema, toggle manually once verified.',
            'type' => 'manual',
        ];
    }

    private function checkDebugMode(): bool
    {
        if (config('app.env') !== 'production') {
            return true;
        }
        return !config('app.debug');
    }

    private function checkStorageLink(): bool
    {
        return is_link(public_path('storage')) || File::exists(public_path('storage'));
    }

    private function checkHeartbeat(string $cacheKey): bool
    {
        $last = Cache::get($cacheKey);
        if (!$last) {
            return false;
        }
        try {
            return now()->diffInMinutes($last) <= self::HEARTBEAT_FRESH_MINUTES;
        } catch (Throwable $e) {
            return false;
        }
    }

    ## Returns true/false when detectable, or null when the underlying model/table
    private function checkPaymentGateway(): ?bool
    {
        try {
            if (class_exists(PaymentMethod::class) && Schema::hasTable('payment_method')) {
                return PaymentMethod::where('status', 'APPROVED')->exists();
            }
        } catch (Throwable $e) {
            // fall through to manual
        }
        return null;
    }

    private function checkCallyzer(): bool
    {
        try {
            return CallyzerApiSetting::where('status', 'APPROVED')->exists();
        } catch (Throwable $e) {
            return false;
        }
    }

    private function checkMembershipPlan(): ?bool
    {
        try {
            if (class_exists(MembershipPlan::class)) {
                return MembershipPlan::count() > 0;
            }
        } catch (Throwable $e) {
            // fall through to manual
        }
        return null;
    }

    private function checkSslCertificate(): array
    {
        $result = [
            'secure' => request()->isSecure(),
            'expires_at' => null,
            'days_remaining' => null,
        ];

        if (!$result['secure']) {
            return $result;
        }

        // Cache for 6 hours — this does a real network handshake, so we
        // don't want it running on every single checklist page load.
        return Cache::remember('setup_checklist_ssl_info', now()->addHours(6), function () use ($result) {
            try {
                $host = request()->getHost();

                $context = stream_context_create([
                    'ssl' => [
                        'capture_peer_cert' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]);

                $client = @stream_socket_client(
                    'ssl://' . $host . ':443',
                    $errno,
                    $errstr,
                    5,
                    STREAM_CLIENT_CONNECT,
                    $context
                );

                if ($client) {
                    $params = stream_context_get_params($client);
                    $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
                    fclose($client);

                    if ($cert && isset($cert['validTo_time_t'])) {
                        $expiresAt = \Illuminate\Support\Carbon::createFromTimestamp($cert['validTo_time_t']);
                        $result['expires_at'] = $expiresAt;
                        $result['days_remaining'] = (int) now()->diffInDays($expiresAt, false);
                    }
                }
            } catch (Throwable $e) {
                // can't reach port 443 (localhost, firewall, etc.) — leave as unknown
            }

            return $result;
        });
    }
}

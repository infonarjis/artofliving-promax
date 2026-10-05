<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Throwable;

class FirebaseAuthService
{
    protected ?Auth $auth = null;

    public function __construct()
    {
        $this->auth = $this->buildAuth();
    }

    /**
     * Build Firebase Auth client from service account JSON
     * stored in site settings.
     */
    private function buildAuth(): ?Auth
    {
        try {
            $config = _getSiteSetting();

            // NOTE: must be the service-account JSON, NOT firebase_configuration
            $serviceAccount = $config['firebase_json'] ?? null;
        
            if (empty($serviceAccount)) {
                Log::error('Firebase service account setting is empty');
                return null;
            }

            if (is_string($serviceAccount)) {
                $serviceAccount = json_decode($serviceAccount, true, 512, JSON_THROW_ON_ERROR);
            }

            if (!is_array($serviceAccount) || ($serviceAccount['type'] ?? null) !== 'service_account') {
                Log::error('Firebase service account JSON is invalid or wrong type', [
                    'received_keys' => is_array($serviceAccount) ? array_keys($serviceAccount) : 'not-an-array'
                ]);
                return null;
            }

            return (new Factory())
                ->withServiceAccount($serviceAccount)
                ->createAuth();
        } catch (Throwable $e) {
            Log::error('Firebase buildAuth failed', ['message' => $e->getMessage()]);
            report($e);
            return null;
        }
    }

    /**
     * Verify Firebase ID token and return Firebase phone number.
     *
     * Example:
     * +919876543210
     */
    public function verifyIdToken(string $idToken): ?string
    {
        if (!$this->auth || empty($idToken)) {
            Log::error('Firebase Auth is not configured or token is empty');

            return null;
        }

        try {
            $verifiedToken = $this->auth->verifyIdToken($idToken, true);

            return $verifiedToken->claims()->get('phone_number');
        } catch (FailedToVerifyToken $e) {
            Log::error('Firebase token verification failed', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return null;
        } catch (Throwable $e) {
            Log::error('Firebase unexpected error', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return null;
        }
    }

    /**
     * Check whether Firebase Auth is configured.
     */
    public function isConfigured(): bool
    {
        return $this->auth !== null;
    }
}

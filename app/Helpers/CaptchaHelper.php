<?php

namespace App\Helpers;

class CaptchaHelper
{
    /**
     * Generate a random captcha string and store it in session.
     *
     * @param string $sessionKey
     * @param int    $length
     * @return string
     */
    public static function generate(string $sessionKey = 'captcha_code', int $length = 6): string
    {
        $characters = '0123456789';
        $captcha    = '';

        for ($i = 0; $i < $length; $i++) {
            $captcha .= $characters[random_int(0, strlen($characters) - 1)];
        }

        session([$sessionKey => $captcha]);

        return $captcha;
    }

    /**
     * Validate user-submitted captcha against the session value.
     *
     * @param string $userInput
     * @param string $sessionKey
     * @param bool   $caseSensitive
     * @return bool
     */
    public static function validate(
        string $userInput,
        string $sessionKey = 'captcha_code',
        bool   $caseSensitive = false
    ): bool {
        $stored = session($sessionKey);

        if (!$stored) {
            return false;
        }

        // Invalidate after one use
        session()->forget($sessionKey);

        return $caseSensitive
            ? $userInput === $stored
            : strtolower($userInput) === strtolower($stored);
    }

    /**
     * Check if a captcha session exists (not yet used).
     */
    public static function exists(string $sessionKey = 'captcha_code'): bool
    {
        return session()->has($sessionKey);
    }
}

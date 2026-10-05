<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;


class DynamicMailConfigServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Skip during artisan / composer / migrations / cache clear
        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            $config = _getSiteSetting();

            if (!$config) {
                return;
            }

            Config::set('mail.mailers.smtp.host', $config['mail_host'] ?? null);
            Config::set('mail.mailers.smtp.port', $config['mail_port'] ?? null);
            Config::set('mail.mailers.smtp.username', $config['mail_username'] ?? null);
            Config::set('mail.mailers.smtp.password', $config['mail_password'] ?? null);
            Config::set('mail.mailers.smtp.encryption', $config['mail_encryption'] ?? null);
            Config::set('mail.from.address', $config['mail_from_address'] ?? null);
            Config::set('mail.from.name', $config['mail_from_name'] ?? null);
            $config = _getSiteSetting();
            Config::set('mail.from.name', $config['mail_title']);
        } catch (\Throwable $e) {
            // Never break Laravel boot
        }
    }
}

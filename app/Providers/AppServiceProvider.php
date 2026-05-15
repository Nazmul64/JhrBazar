<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (\Schema::hasTable('genaral_settings')) {
                $gs = \App\Models\GenaralSetting::first();
                \View::share('gs', $gs);
            }

            if (\Schema::hasTable('mail_configurations')) {
                $config = \App\Models\MailConfiguration::first();
                if ($config) {
                    \Config::set('mail.default', $config->mail_mailer);
                    \Config::set('mail.mailers.' . $config->mail_mailer . '.host',       $config->mail_host);
                    \Config::set('mail.mailers.' . $config->mail_mailer . '.port',       $config->mail_port);
                    \Config::set('mail.mailers.' . $config->mail_mailer . '.username',   $config->mail_username);
                    \Config::set('mail.mailers.' . $config->mail_mailer . '.password',   $config->mail_password);
                    \Config::set('mail.mailers.' . $config->mail_mailer . '.encryption', $config->mail_encryption === 'none' ? null : $config->mail_encryption);
                    \Config::set('mail.from.address', $config->mail_from_address);
                    \Config::set('mail.from.name',    $config->mail_from_name ?? config('app.name'));
                }
            }
        } catch (\Exception $e) {
            // Table might not exist yet during migration
        }
    }
}

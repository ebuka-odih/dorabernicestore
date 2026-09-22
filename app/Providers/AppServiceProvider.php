<?php

namespace App\Providers;

use App\Support\StripeSettings;
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
        // Let keys saved via Admin → Settings override the `.env` values,
        // so every existing `config('services.stripe.*')` reader picks up
        // the admin-configured credentials. Setting::get() already guards
        // against a not-yet-migrated database, so this is safe on fresh clones.
        if ($key = StripeSettings::publishableKey()) {
            config(['services.stripe.key' => $key]);
        }

        if ($secret = StripeSettings::secretKey()) {
            config(['services.stripe.secret' => $secret]);
        }

        if ($webhookSecret = StripeSettings::webhookSecret()) {
            config(['services.stripe.webhook_secret' => $webhookSecret]);
        }
    }
}

<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Resolves the Stripe credentials the store should use.
 *
 * Priority: value saved via Admin → Settings first, `.env`
 * (`STRIPE_KEY` / `STRIPE_SECRET` / `STRIPE_WEBHOOK_SECRET`) as fallback.
 * This lets a store owner paste test or live keys in the admin panel
 * without touching server files, while keeping env-based deploys working.
 */
class StripeSettings
{
    public const PUBLISHABLE_KEY = 'stripe.publishable_key';

    public const SECRET_KEY = 'stripe.secret_key';

    public const WEBHOOK_SECRET = 'stripe.webhook_secret';

    public static function publishableKey(): ?string
    {
        return static::value(self::PUBLISHABLE_KEY, (string) config('services.stripe.key'));
    }

    public static function secretKey(): ?string
    {
        return static::value(self::SECRET_KEY, (string) config('services.stripe.secret'));
    }

    public static function webhookSecret(): ?string
    {
        return static::value(self::WEBHOOK_SECRET, (string) config('services.stripe.webhook_secret'));
    }

    public static function isConfigured(): bool
    {
        return filled(static::publishableKey()) && filled(static::secretKey());
    }

    public static function isLiveMode(): ?bool
    {
        $key = static::publishableKey();

        if (! filled($key)) {
            return null;
        }

        return str_starts_with($key, 'pk_live_');
    }

    /**
     * Deep link to a payment in the Stripe Dashboard, pointing at the
     * test or live dashboard according to the configured key.
     */
    public static function paymentUrl(?string $paymentIntentId): ?string
    {
        if (! filled($paymentIntentId)) {
            return null;
        }

        $base = static::isLiveMode() === true
            ? 'https://dashboard.stripe.com'
            : 'https://dashboard.stripe.com/test';

        return $base.'/payments/'.urlencode($paymentIntentId);
    }

    /**
     * Where each effective value currently comes from — shown in the admin UI
     * so it is obvious whether the DB value or `.env` is winning.
     *
     * @return array<string, string> keyed by setting key, values 'admin' | 'env' | 'missing'
     */
    public static function sources(): array
    {
        return [
            self::PUBLISHABLE_KEY => static::source(self::PUBLISHABLE_KEY, (string) config('services.stripe.key')),
            self::SECRET_KEY => static::source(self::SECRET_KEY, (string) config('services.stripe.secret')),
            self::WEBHOOK_SECRET => static::source(self::WEBHOOK_SECRET, (string) config('services.stripe.webhook_secret')),
        ];
    }

    protected static function value(string $key, string $envFallback): ?string
    {
        $stored = Setting::get($key);

        if (filled($stored)) {
            return $stored;
        }

        return filled($envFallback) ? $envFallback : null;
    }

    protected static function source(string $key, string $envFallback): string
    {
        if (filled(Setting::get($key))) {
            return 'admin';
        }

        return filled($envFallback) ? 'env' : 'missing';
    }
}

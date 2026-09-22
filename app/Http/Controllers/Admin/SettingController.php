<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\StripeSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings', [
            'values' => [
                'publishable_key' => Setting::get(StripeSettings::PUBLISHABLE_KEY, ''),
                'secret_key' => Setting::get(StripeSettings::SECRET_KEY, ''),
                'webhook_secret' => Setting::get(StripeSettings::WEBHOOK_SECRET, ''),
            ],
            'sources' => StripeSettings::sources(),
            'effective' => [
                'publishable_key' => StripeSettings::publishableKey(),
                'webhook_configured' => filled(StripeSettings::webhookSecret()),
            ],
            'liveMode' => StripeSettings::isLiveMode(),
            'configured' => StripeSettings::isConfigured(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'publishable_key' => ['nullable', 'string', 'max:255', 'starts_with:pk_test_,pk_live_'],
            'secret_key' => ['nullable', 'string', 'max:255', 'starts_with:sk_test_,sk_live_,rk_test_,rk_live_'],
            'webhook_secret' => ['nullable', 'string', 'max:255', 'starts_with:whsec_'],
        ], [
            'publishable_key.starts_with' => 'The publishable key should start with pk_test_ or pk_live_.',
            'secret_key.starts_with' => 'The secret key should start with sk_test_, sk_live_ (or a restricted rk_ key).',
            'webhook_secret.starts_with' => 'The webhook signing secret should start with whsec_.',
        ]);

        Setting::set(StripeSettings::PUBLISHABLE_KEY, $data['publishable_key'] ?? null);
        Setting::set(StripeSettings::SECRET_KEY, $data['secret_key'] ?? null);
        Setting::set(StripeSettings::WEBHOOK_SECRET, $data['webhook_secret'] ?? null);

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', StripeSettings::isConfigured()
                ? 'Stripe settings saved — checkout is now accepting payments.'
                : 'Stripe settings saved. Add both a publishable and a secret key to enable checkout.');
    }
}

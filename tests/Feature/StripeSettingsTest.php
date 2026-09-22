<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Support\StripeSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_non_admins_cannot_open_settings(): void
    {
        $this->get(route('admin.settings.edit'))->assertRedirect(route('login'));

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get(route('admin.settings.edit'))->assertForbidden();
    }

    public function test_admin_can_view_and_save_stripe_keys(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('Stripe Payments');

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'publishable_key' => 'pk_test_123',
                'secret_key' => 'sk_test_123',
                'webhook_secret' => 'whsec_123',
            ])
            ->assertRedirect(route('admin.settings.edit'));

        $this->assertSame('pk_test_123', Setting::get(StripeSettings::PUBLISHABLE_KEY));
        $this->assertSame('sk_test_123', Setting::get(StripeSettings::SECRET_KEY));
        $this->assertSame('whsec_123', Setting::get(StripeSettings::WEBHOOK_SECRET));
        $this->assertTrue(StripeSettings::isConfigured());
    }

    public function test_invalid_key_formats_are_rejected(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'publishable_key' => 'not-a-key',
                'secret_key' => 'sk_test_123',
            ])
            ->assertSessionHasErrors('publishable_key');

        $this->assertNull(Setting::get(StripeSettings::PUBLISHABLE_KEY));
    }

    public function test_payment_url_points_at_test_or_live_dashboard(): void
    {
        $this->assertNull(StripeSettings::paymentUrl(null));

        config(['services.stripe.key' => 'pk_test_123']);
        $this->assertSame(
            'https://dashboard.stripe.com/test/payments/pi_123',
            StripeSettings::paymentUrl('pi_123')
        );

        Setting::set(StripeSettings::PUBLISHABLE_KEY, 'pk_live_123');
        $this->assertSame(
            'https://dashboard.stripe.com/payments/pi_123',
            StripeSettings::paymentUrl('pi_123')
        );
    }

    public function test_admin_saved_keys_take_precedence_over_env(): void
    {
        config(['services.stripe.key' => 'pk_test_env']);

        $this->assertSame('pk_test_env', StripeSettings::publishableKey());

        Setting::set(StripeSettings::PUBLISHABLE_KEY, 'pk_test_admin');

        $this->assertSame('pk_test_admin', StripeSettings::publishableKey());

        Setting::set(StripeSettings::PUBLISHABLE_KEY, null);

        $this->assertSame('pk_test_env', StripeSettings::publishableKey());
    }
}

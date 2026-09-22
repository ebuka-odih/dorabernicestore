<x-admin-layout :title="'Settings'">

    <div class="max-w-3xl">
        <div class="bg-white border border-ink-100 p-8 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h2 class="font-serif text-xl text-ink-900">Stripe Payments</h2>
                    <p class="text-sm text-ink-500 mt-1">
                        Keys saved here take effect immediately and override the
                        <code class="text-xs bg-ink-100 px-1">STRIPE_*</code> values in
                        <code class="text-xs bg-ink-100 px-1">.env</code>.
                        Find them under Developers → API keys in the
                        <a href="https://dashboard.stripe.com/test/apikeys" target="_blank" rel="noopener" class="text-gold-600 hover:underline">Stripe Dashboard</a>.
                    </p>
                </div>
                @if ($configured)
                    <span class="text-xs uppercase tracking-wide px-3 py-1 {{ $liveMode ? 'bg-emerald-100 text-emerald-700' : 'bg-gold-100 text-gold-700' }}">
                        {{ $liveMode ? 'Live mode' : 'Test mode' }}
                    </span>
                @else
                    <span class="text-xs uppercase tracking-wide px-3 py-1 bg-ink-100 text-ink-500">Not configured</span>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white border border-ink-100 p-8 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="publishable_key" value="Publishable key (pk_test_… / pk_live_…)" />
                <x-text-input id="publishable_key" name="publishable_key" type="text" class="mt-1 font-mono text-sm"
                    value="{{ old('publishable_key', $values['publishable_key']) }}"
                    placeholder="pk_test_…" autocomplete="off" />
                <p class="text-xs text-ink-400 mt-1">
                    Safe for the browser — this is the key the checkout page loads Stripe.js with.
                    Currently: <strong>{{ $sources[\App\Support\StripeSettings::PUBLISHABLE_KEY] === 'admin' ? 'set here in admin' : ($sources[\App\Support\StripeSettings::PUBLISHABLE_KEY] === 'env' ? 'coming from .env' : 'missing') }}</strong>
                </p>
                <x-input-error :messages="$errors->get('publishable_key')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="secret_key" value="Secret key (sk_test_… / sk_live_…)" />
                <x-text-input id="secret_key" name="secret_key" type="password" class="mt-1 font-mono text-sm"
                    value="{{ old('secret_key', $values['secret_key']) }}"
                    placeholder="sk_test_… — leave untouched to keep the saved key" autocomplete="off" />
                <p class="text-xs text-ink-400 mt-1">
                    Never share this. It stays on the server and creates the PaymentIntents behind checkout.
                    Currently: <strong>{{ $sources[\App\Support\StripeSettings::SECRET_KEY] === 'admin' ? 'set here in admin' : ($sources[\App\Support\StripeSettings::SECRET_KEY] === 'env' ? 'coming from .env' : 'missing') }}</strong>
                </p>
                <x-input-error :messages="$errors->get('secret_key')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="webhook_secret" value="Webhook signing secret (whsec_… — optional but recommended)" />
                <x-text-input id="webhook_secret" name="webhook_secret" type="password" class="mt-1 font-mono text-sm"
                    value="{{ old('webhook_secret', $values['webhook_secret']) }}"
                    placeholder="whsec_…" autocomplete="off" />
                <p class="text-xs text-ink-400 mt-1">
                    Lets the <code class="bg-ink-100 px-1">/stripe/webhook</code> endpoint verify events from Stripe,
                    so orders are recorded even if the customer closes their tab after paying.
                    Currently: <strong>{{ $effective['webhook_configured'] ? 'configured' : 'missing' }}</strong>
                </p>
                <x-input-error :messages="$errors->get('webhook_secret')" class="mt-2" />
            </div>

            <div class="flex items-center gap-4 pt-2">
                <x-primary-button>Save Stripe Settings</x-primary-button>
                <p class="text-xs text-ink-400">Clear a field and save to fall back to <code class="bg-ink-100 px-1">.env</code> for that key.</p>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-4"
              onsubmit="return confirm('Remove the admin-saved Stripe keys and fall back to .env?');">
            @csrf
            @method('PUT')
            <input type="hidden" name="publishable_key" value="">
            <input type="hidden" name="secret_key" value="">
            <input type="hidden" name="webhook_secret" value="">
            <button class="text-sm text-rose-600 hover:underline">Clear admin-saved keys (fall back to .env)</button>
        </form>
    </div>

</x-admin-layout>

<x-storefront-layout :title="'Checkout'">

    <section class="bg-ink-900 text-cream py-16 text-center">
        <p class="eyebrow text-gold-400">Almost There</p>
        <h1 class="font-serif text-4xl mt-2">Checkout</h1>
    </section>

    <section class="max-w-6xl mx-auto px-6 py-16">
        <form id="checkout-form" class="grid md:grid-cols-3 gap-16">
            @csrf

            <div class="md:col-span-2 space-y-10">
                <div>
                    <h2 class="font-serif text-2xl text-ink-900 mb-6">Contact &amp; Shipping</h2>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <x-input-label for="customer_name" value="Full Name" />
                            <x-text-input id="customer_name" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" required class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="customer_email" value="Email" />
                            <x-text-input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" required class="mt-1" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="customer_phone" value="Phone (optional)" />
                            <x-text-input id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" class="mt-1" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="shipping_address" value="Address" />
                            <x-text-input id="shipping_address" name="shipping_address" value="{{ old('shipping_address') }}" required class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="shipping_city" value="City" />
                            <x-text-input id="shipping_city" name="shipping_city" value="{{ old('shipping_city') }}" required class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="shipping_postcode" value="Postal Code" />
                            <x-text-input id="shipping_postcode" name="shipping_postcode" value="{{ old('shipping_postcode') }}" required class="mt-1" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="shipping_country" value="Country" />
                            <x-text-input id="shipping_country" name="shipping_country" value="{{ old('shipping_country') }}" required class="mt-1" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="notes" value="Order Notes (optional)" />
                            <textarea id="notes" name="notes" rows="3" class="w-full border-ink-200 focus:border-gold-500 focus:ring-gold-400 mt-1">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="font-serif text-2xl text-ink-900 mb-6">Payment</h2>

                    @if ($stripeConfigured)
                        <div id="payment-element" class="bg-white border border-ink-100 p-6"></div>
                        <div id="payment-errors" class="text-rose-600 text-sm mt-3" role="alert"></div>
                    @else
                        <div class="bg-gold-50 border border-gold-200 text-ink-600 text-sm p-6 leading-relaxed">
                            Payment processing is still being set up for this store — Stripe test keys haven't been added yet. Once they are, card payment will appear here.
                        </div>
                    @endif
                </div>

                <button type="submit" id="checkout-submit" class="btn-gold w-full sm:w-auto" @disabled(! $stripeConfigured)>
                    Pay &amp; Place Order
                </button>
                <p class="text-xs text-ink-400 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3Z"/></svg>
                    Payments are processed securely by Stripe — your card details never touch our servers.
                </p>
            </div>

            <div class="bg-white border border-ink-100 p-8 h-fit">
                <h2 class="font-serif text-xl text-ink-900 mb-6">Order Summary</h2>
                <div class="space-y-4">
                    @foreach ($items as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-ink-600">{{ $item->product->name }} &times; {{ $item->quantity }}</span>
                            <span class="text-ink-800">${{ number_format($item->lineTotal, 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-ink-100 mt-6 pt-6 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-ink-500">Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-ink-500">Shipping</span><span>{{ $shippingCost > 0 ? '$'.number_format($shippingCost, 2) : 'Free' }}</span></div>
                    <div class="flex justify-between font-medium text-ink-900 text-base pt-2 border-t border-ink-100">
                        <span>Total</span><span>${{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>
        </form>
    </section>

    @if ($stripeConfigured)
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            (function () {
                const stripe = Stripe(@json(config('services.stripe.key')));
                const elements = stripe.elements({
                    clientSecret: @json($clientSecret),
                    appearance: {
                        theme: 'stripe',
                        variables: {
                            colorPrimary: '#B08D4E',
                            colorBackground: '#ffffff',
                            colorText: '#28221D',
                            colorDanger: '#A8695C',
                            fontFamily: 'Jost, ui-sans-serif, system-ui, sans-serif',
                            borderRadius: '0px',
                            spacingUnit: '4px',
                        },
                    },
                });

                const paymentElement = elements.create('payment');
                paymentElement.mount('#payment-element');

                const form = document.getElementById('checkout-form');
                const submitBtn = document.getElementById('checkout-submit');
                const errorBox = document.getElementById('payment-errors');
                const returnUrl = @json(route('checkout.return'));

                function setBusy(busy) {
                    submitBtn.disabled = busy;
                    submitBtn.textContent = busy ? 'Processing…' : 'Pay & Place Order';
                }

                form.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    setBusy(true);
                    errorBox.textContent = '';

                    const detailsResponse = await fetch(@json(route('checkout.details')), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: new FormData(form),
                    });

                    if (!detailsResponse.ok) {
                        const body = await detailsResponse.json().catch(() => ({}));
                        const messages = body.errors ? Object.values(body.errors).flat() : [body.message || 'Please check the details above and try again.'];
                        errorBox.textContent = messages.join(' ');
                        setBusy(false);
                        return;
                    }

                    const { error, paymentIntent } = await stripe.confirmPayment({
                        elements,
                        confirmParams: { return_url: returnUrl },
                        redirect: 'if_required',
                    });

                    if (error) {
                        errorBox.textContent = error.message || 'Your payment could not be processed. Please try again.';
                        setBusy(false);
                        return;
                    }

                    if (paymentIntent && paymentIntent.status === 'succeeded') {
                        window.location.href = returnUrl;
                    } else {
                        setBusy(false);
                    }
                });
            })();
        </script>
    @endif

</x-storefront-layout>

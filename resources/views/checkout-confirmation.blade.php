<x-storefront-layout :title="'Order Confirmed'">

    <section class="max-w-3xl mx-auto px-6 py-24 text-center">
        <x-jewel-icon icon="diamond" class="w-14 h-14 mx-auto text-gold-500" />
        <p class="eyebrow mt-6">Thank You</p>
        <h1 class="font-serif text-4xl text-ink-900 mt-2">Your order is confirmed</h1>
        <p class="mt-4 text-ink-500">Order <span class="text-ink-800 font-medium">#{{ $order->order_number }}</span> has been placed.</p>
        @if ($order->payment_status === 'paid')
            <span class="inline-block mt-3 text-xs uppercase tracking-widest2 px-3 py-1 border border-gold-300 text-gold-700 bg-gold-50">Payment Received</span>
        @endif

        <div class="mt-12 bg-white border border-ink-100 p-8 text-left">
            <h2 class="font-serif text-xl text-ink-900 mb-6">Order Summary</h2>
            <div class="divide-y divide-ink-100">
                @foreach ($order->items as $item)
                    <div class="flex justify-between py-3 text-sm">
                        <span class="text-ink-600">{{ $item->product_name }} &times; {{ $item->quantity }}</span>
                        <span class="text-ink-800">${{ number_format($item->lineTotal(), 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-ink-100 mt-4 pt-4 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-ink-500">Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-ink-500">Shipping</span><span>${{ number_format($order->shipping_cost, 2) }}</span></div>
                <div class="flex justify-between font-medium text-ink-900 text-base pt-2 border-t border-ink-100"><span>Total</span><span>${{ number_format($order->total, 2) }}</span></div>
            </div>
            <div class="mt-6 pt-6 border-t border-ink-100 text-sm text-ink-500">
                <p>Shipping to {{ $order->customer_name }}, {{ $order->shipping_address }}, {{ $order->shipping_city }} {{ $order->shipping_postcode }}, {{ $order->shipping_country }}</p>
            </div>
        </div>

        <a href="{{ route('shop.index') }}" class="btn-gold-outline mt-12 inline-flex">Continue Shopping</a>
    </section>

</x-storefront-layout>

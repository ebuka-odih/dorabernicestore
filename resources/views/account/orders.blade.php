<x-storefront-layout :title="'Order History'">

    <section class="bg-ink-900 text-cream py-16 text-center">
        <p class="eyebrow text-gold-400">Your Account</p>
        <h1 class="font-serif text-4xl mt-2">Order History</h1>
    </section>

    <section class="max-w-4xl mx-auto px-6 py-16">
        <div class="flex gap-8 mb-10 text-sm">
            <a href="{{ route('account.orders') }}" class="text-gold-600 border-b-2 border-gold-500 pb-2">Orders</a>
            <a href="{{ route('profile.edit') }}" class="text-ink-500 hover:text-gold-600 pb-2">Profile Settings</a>
        </div>

        @if ($orders->isEmpty())
            <p class="text-ink-500">You haven't placed any orders yet.</p>
            <a href="{{ route('shop.index') }}" class="btn-gold-outline mt-8 inline-flex">Start Shopping</a>
        @else
            <div class="space-y-6">
                @foreach ($orders as $order)
                    <div class="border border-ink-100 bg-white p-6">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="font-medium text-ink-900">#{{ $order->order_number }}</p>
                                <p class="text-xs text-ink-400">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <span class="text-xs uppercase tracking-widest2 px-3 py-1 border border-gold-300 text-gold-700 bg-gold-50">{{ $order->status }}</span>
                            <p class="font-medium text-ink-800">${{ number_format($order->total, 2) }}</p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-ink-100 text-sm text-ink-500 space-y-1">
                            @foreach ($order->items as $item)
                                <p>{{ $item->product_name }} &times; {{ $item->quantity }}</p>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10">{{ $orders->links() }}</div>
        @endif
    </section>

</x-storefront-layout>

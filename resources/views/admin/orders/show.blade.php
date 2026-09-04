<x-admin-layout :title="'Order #'.$order->order_number">

    <div class="grid md:grid-cols-3 gap-8">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white border border-ink-100">
                <div class="px-6 py-4 border-b border-ink-100">
                    <h2 class="font-serif text-xl text-ink-900">Items</h2>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-widest2 text-ink-400 border-b border-ink-100">
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3">Price</th>
                            <th class="px-6 py-3">Qty</th>
                            <th class="px-6 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="px-6 py-4 text-ink-800">{{ $item->product_name }}</td>
                                <td class="px-6 py-4 text-ink-500">${{ number_format($item->price, 2) }}</td>
                                <td class="px-6 py-4 text-ink-500">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-right text-ink-800">${{ number_format($item->lineTotal(), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-ink-100 space-y-1 text-sm ml-auto max-w-xs">
                    <div class="flex justify-between"><span class="text-ink-500">Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-ink-500">Shipping</span><span>${{ number_format($order->shipping_cost, 2) }}</span></div>
                    <div class="flex justify-between font-medium text-ink-900 pt-1 border-t border-ink-100"><span>Total</span><span>${{ number_format($order->total, 2) }}</span></div>
                </div>
            </div>

            @if ($order->notes)
                <div class="bg-white border border-ink-100 p-6">
                    <h3 class="eyebrow text-ink-400 mb-2">Order Notes</h3>
                    <p class="text-ink-700 text-sm">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-ink-100 p-6">
                <h3 class="eyebrow text-ink-400 mb-4">Payment</h3>
                @if ($order->payment_status === 'paid')
                    <span class="text-xs uppercase tracking-wide px-2 py-1 bg-green-50 text-green-700">Paid</span>
                @else
                    <span class="text-xs uppercase tracking-wide px-2 py-1 bg-rose-50 text-rose-600">{{ ucfirst($order->payment_status) }}</span>
                @endif
                @if ($order->stripe_payment_intent_id)
                    <p class="text-xs text-ink-400 mt-2 font-mono">{{ $order->stripe_payment_intent_id }}</p>
                @endif
            </div>

            <div class="bg-white border border-ink-100 p-6">
                <h3 class="eyebrow text-ink-400 mb-4">Update Status</h3>
                <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="flex gap-2">
                    @csrf
                    @method('PUT')
                    <select name="status" class="flex-1 border-ink-200 focus:border-gold-500 focus:ring-gold-400 text-sm">
                        @foreach (\App\Models\Order::STATUSES as $status)
                            <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <button class="btn-gold text-xs px-4">Save</button>
                </form>
            </div>

            <div class="bg-white border border-ink-100 p-6">
                <h3 class="eyebrow text-ink-400 mb-4">Customer</h3>
                <p class="text-ink-800">{{ $order->customer_name }}</p>
                <p class="text-ink-500 text-sm">{{ $order->customer_email }}</p>
                @if ($order->customer_phone)<p class="text-ink-500 text-sm">{{ $order->customer_phone }}</p>@endif
            </div>

            <div class="bg-white border border-ink-100 p-6">
                <h3 class="eyebrow text-ink-400 mb-4">Shipping Address</h3>
                <p class="text-ink-700 text-sm leading-relaxed">
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_postcode }}<br>
                    {{ $order->shipping_country }}
                </p>
            </div>
        </div>
    </div>

</x-admin-layout>

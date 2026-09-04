<x-admin-layout :title="'Dashboard'">

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white border border-ink-100 p-6">
            <p class="eyebrow text-ink-400">Products</p>
            <p class="font-serif text-3xl text-ink-900 mt-2">{{ $stats['products'] }}</p>
        </div>
        <div class="bg-white border border-ink-100 p-6">
            <p class="eyebrow text-ink-400">Orders</p>
            <p class="font-serif text-3xl text-ink-900 mt-2">{{ $stats['orders'] }}</p>
        </div>
        <div class="bg-white border border-ink-100 p-6">
            <p class="eyebrow text-ink-400">Pending Orders</p>
            <p class="font-serif text-3xl text-ink-900 mt-2">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white border border-gold-300 bg-gold-50 p-6">
            <p class="eyebrow text-gold-700">Revenue</p>
            <p class="font-serif text-3xl text-ink-900 mt-2">${{ number_format($stats['revenue'], 2) }}</p>
        </div>
    </div>

    <div class="bg-white border border-ink-100">
        <div class="px-6 py-4 border-b border-ink-100 flex items-center justify-between">
            <h2 class="font-serif text-xl text-ink-900">Recent Orders</h2>
            <a href="{{ route('admin.orders.index') }}" class="nav-link">View All</a>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-widest2 text-ink-400 border-b border-ink-100">
                    <th class="px-6 py-3">Order</th>
                    <th class="px-6 py-3">Customer</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Total</th>
                    <th class="px-6 py-3">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @forelse ($recentOrders as $order)
                    <tr class="hover:bg-ink-50">
                        <td class="px-6 py-4"><a href="{{ route('admin.orders.show', $order) }}" class="text-gold-600 hover:underline">#{{ $order->order_number }}</a></td>
                        <td class="px-6 py-4">{{ $order->customer_name }}</td>
                        <td class="px-6 py-4"><span class="text-xs uppercase tracking-wide px-2 py-1 bg-ink-100 text-ink-600">{{ $order->status }}</span></td>
                        <td class="px-6 py-4">${{ number_format($order->total, 2) }}</td>
                        <td class="px-6 py-4 text-ink-400">{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-ink-400">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-admin-layout>

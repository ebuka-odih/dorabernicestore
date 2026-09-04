<x-admin-layout :title="'Orders'">

    <div class="flex gap-2 mb-6 flex-wrap">
        <a href="{{ route('admin.orders.index') }}" class="text-xs uppercase tracking-widest2 px-3 py-1.5 {{ ! request('status') ? 'bg-ink-900 text-cream' : 'bg-white border border-ink-200 text-ink-600' }}">All</a>
        @foreach (\App\Models\Order::STATUSES as $status)
            <a href="{{ route('admin.orders.index', ['status' => $status]) }}" class="text-xs uppercase tracking-widest2 px-3 py-1.5 {{ request('status') === $status ? 'bg-ink-900 text-cream' : 'bg-white border border-ink-200 text-ink-600' }}">{{ $status }}</a>
        @endforeach
    </div>

    <div class="bg-white border border-ink-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-widest2 text-ink-400 border-b border-ink-100">
                    <th class="px-6 py-3">Order</th>
                    <th class="px-6 py-3">Customer</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Payment</th>
                    <th class="px-6 py-3">Total</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @forelse ($orders as $order)
                    <tr class="hover:bg-ink-50">
                        <td class="px-6 py-4 text-ink-800">#{{ $order->order_number }}</td>
                        <td class="px-6 py-4">{{ $order->customer_name }}<br><span class="text-xs text-ink-400">{{ $order->customer_email }}</span></td>
                        <td class="px-6 py-4"><span class="text-xs uppercase tracking-wide px-2 py-1 bg-ink-100 text-ink-600">{{ $order->status }}</span></td>
                        <td class="px-6 py-4">
                            @if ($order->payment_status === 'paid')
                                <span class="text-xs uppercase tracking-wide px-2 py-1 bg-green-50 text-green-700">Paid</span>
                            @else
                                <span class="text-xs uppercase tracking-wide px-2 py-1 bg-rose-50 text-rose-600">{{ ucfirst($order->payment_status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-ink-700">${{ number_format($order->total, 2) }}</td>
                        <td class="px-6 py-4 text-ink-400">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right"><a href="{{ route('admin.orders.show', $order) }}" class="text-gold-600 hover:underline">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-ink-400">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>

</x-admin-layout>

<x-storefront-layout :title="'Your Bag'">

    <section class="bg-ink-900 text-cream py-16 text-center">
        <p class="eyebrow text-gold-400">Review</p>
        <h1 class="font-serif text-4xl mt-2">Your Bag</h1>
    </section>

    <section class="max-w-5xl mx-auto px-6 py-16">
        @if ($items->isEmpty())
            <div class="text-center py-20">
                <x-jewel-icon icon="gift" class="w-16 h-16 mx-auto text-ink-300" />
                <p class="mt-6 text-ink-500">Your bag is currently empty.</p>
                <a href="{{ route('shop.index') }}" class="btn-gold-outline mt-8 inline-flex">Continue Shopping</a>
            </div>
        @else
            <div class="divide-y divide-ink-100 border-y border-ink-100">
                @foreach ($items as $item)
                    <div class="flex items-center gap-6 py-6">
                        <div class="w-20 h-20 bg-white border border-ink-100 flex items-center justify-center shrink-0">
                            <x-jewel-icon :icon="$item->product->icon" class="w-10 h-10 text-ink-800" />
                        </div>

                        <div class="flex-1">
                            <a href="{{ route('product.show', $item->product) }}" class="font-serif text-lg text-ink-900 hover:text-gold-600">{{ $item->product->name }}</a>
                            <p class="text-sm text-ink-400">${{ number_format($item->product->currentPrice(), 2) }} each</p>
                        </div>

                        <form action="{{ route('cart.update', $item->product) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="quantity" onchange="this.form.submit()" class="border-ink-200 text-sm focus:border-gold-500 focus:ring-gold-400">
                                @foreach (range(1, min(10, max($item->quantity, $item->product->stock))) as $qty)
                                    <option value="{{ $qty }}" @selected($qty === $item->quantity)>{{ $qty }}</option>
                                @endforeach
                            </select>
                        </form>

                        <p class="w-24 text-right font-medium text-ink-800">${{ number_format($item->lineTotal, 2) }}</p>

                        <form action="{{ route('cart.destroy', $item->product) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="text-ink-400 hover:text-rose-600 transition" aria-label="Remove">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/></svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 flex justify-end">
                <div class="w-full max-w-sm space-y-4">
                    <div class="flex justify-between text-ink-600">
                        <span>Subtotal</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <p class="text-xs text-ink-400">Shipping and taxes calculated at checkout.</p>
                    <a href="{{ route('checkout.create') }}" class="btn-gold w-full">Proceed to Checkout</a>
                    <a href="{{ route('shop.index') }}" class="block text-center nav-link">Continue Shopping</a>
                </div>
            </div>
        @endif
    </section>

</x-storefront-layout>

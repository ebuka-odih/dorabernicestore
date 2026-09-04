@props(['product'])

<div class="group">
    <a href="{{ route('product.show', $product) }}" class="block relative bg-white border border-ink-100 aspect-square flex items-center justify-center overflow-hidden">
        <x-jewel-icon :icon="$product->icon" class="w-24 h-24 text-ink-800 transition duration-500 group-hover:scale-110 group-hover:text-gold-600" />

        @if ($product->onSale())
            <span class="absolute top-3 right-3 w-12 h-12 rounded-full bg-gold-600 text-cream text-[11px] uppercase tracking-wide flex items-center justify-center">Sale</span>
        @endif

        <div class="absolute inset-x-0 bottom-0 translate-y-full group-hover:translate-y-0 transition duration-300 bg-ink-900/90 py-3 text-center">
            <span class="text-cream text-xs uppercase tracking-widest2">View Piece</span>
        </div>
    </a>

    <div class="mt-4 flex items-start justify-between gap-2">
        <div>
            <p class="eyebrow text-ink-400">{{ $product->category->name }}</p>
            <a href="{{ route('product.show', $product) }}" class="font-serif text-lg text-ink-900 hover:text-gold-600 transition">{{ $product->name }}</a>
        </div>
        <div class="text-right shrink-0">
            @if ($product->onSale())
                <p class="text-ink-400 line-through text-xs">${{ number_format($product->price, 2) }}</p>
                <p class="text-gold-600 font-medium">${{ number_format($product->sale_price, 2) }}</p>
            @else
                <p class="text-ink-800 font-medium">${{ number_format($product->price, 2) }}</p>
            @endif
        </div>
    </div>
</div>

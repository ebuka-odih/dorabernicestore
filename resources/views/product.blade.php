<x-storefront-layout :title="$product->name">

    <section class="max-w-7xl mx-auto px-6 py-16">
        <nav class="text-xs uppercase tracking-widest2 text-ink-400 mb-10">
            <a href="{{ route('home') }}" class="hover:text-gold-600">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="hover:text-gold-600">{{ $product->category->name }}</a>
            <span class="mx-2">/</span>
            <span class="text-ink-700">{{ $product->name }}</span>
        </nav>

        <div class="grid md:grid-cols-2 gap-16">
            @if ($product->images->isNotEmpty())
                <div
                    x-data="{
                        active: 0,
                        lightbox: false,
                        images: {{ Js::from($product->images->map(fn ($image) => $image->url)->values()) }},
                        next() { this.active = (this.active + 1) % this.images.length; },
                        prev() { this.active = (this.active - 1 + this.images.length) % this.images.length; },
                    }"
                    @keydown.escape.window="lightbox = false"
                    @keydown.arrow-right.window="if (lightbox) next()"
                    @keydown.arrow-left.window="if (lightbox) prev()"
                >
                    <button
                        type="button"
                        @click="lightbox = true"
                        class="block w-full bg-white border border-ink-100 aspect-square overflow-hidden cursor-zoom-in"
                        aria-label="View larger image"
                    >
                        @foreach ($product->images as $image)
                            <img
                                src="{{ $image->url }}"
                                alt="{{ $product->name }} — view {{ $loop->iteration }}"
                                x-show="active === {{ $loop->index }}"
                                class="w-full h-full object-cover"
                                {{ $loop->first ? '' : 'x-cloak' }}
                            />
                        @endforeach
                    </button>

                    @if ($product->images->count() > 1)
                        <div class="grid grid-cols-4 gap-3 mt-3">
                            @foreach ($product->images as $image)
                                <button
                                    type="button"
                                    @click="active = {{ $loop->index }}"
                                    class="bg-white border aspect-square overflow-hidden transition"
                                    :class="active === {{ $loop->index }} ? 'border-gold-600' : 'border-ink-100 hover:border-gold-400'"
                                    aria-label="Show image {{ $loop->iteration }}"
                                >
                                    <img src="{{ $image->url }}" alt="" class="w-full h-full object-cover" loading="lazy" />
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <div
                        x-show="lightbox"
                        x-cloak
                        class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/90 p-6"
                        @click.self="lightbox = false"
                        role="dialog"
                        aria-modal="true"
                        aria-label="Product image viewer"
                    >
                        <button type="button" @click="lightbox = false" class="absolute top-5 right-6 text-cream text-sm uppercase tracking-widest2 hover:text-gold-400" aria-label="Close viewer">Close &times;</button>

                        @if ($product->images->count() > 1)
                            <button type="button" @click.stop="prev()" class="absolute left-4 md:left-8 text-cream text-3xl hover:text-gold-400" aria-label="Previous image">&lsaquo;</button>
                            <button type="button" @click.stop="next()" class="absolute right-4 md:right-8 text-cream text-3xl hover:text-gold-400" aria-label="Next image">&rsaquo;</button>
                        @endif

                        @foreach ($product->images as $image)
                            <img
                                src="{{ $image->url }}"
                                alt="{{ $product->name }} — view {{ $loop->iteration }}"
                                x-show="active === {{ $loop->index }}"
                                class="max-h-[85vh] max-w-full object-contain border border-ink-700"
                                {{ $loop->first ? '' : 'x-cloak' }}
                            />
                        @endforeach

                        @if ($product->images->count() > 1)
                            <p class="absolute bottom-6 text-cream/80 text-sm" x-text="(active + 1) + ' / ' + images.length"></p>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white border border-ink-100 aspect-square flex items-center justify-center">
                    <x-jewel-icon :icon="$product->icon" class="w-64 h-64 text-ink-800" />
                </div>
            @endif

            <div>
                <p class="eyebrow">{{ $product->category->name }}</p>
                <h1 class="font-serif text-4xl text-ink-900 mt-3">{{ $product->name }}</h1>

                <div class="mt-5 flex items-center gap-3">
                    @if ($product->onSale())
                        <span class="text-ink-400 line-through">${{ number_format($product->price, 2) }}</span>
                        <span class="text-2xl text-gold-600 font-medium">${{ number_format($product->sale_price, 2) }}</span>
                    @else
                        <span class="text-2xl text-ink-800 font-medium">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>

                <p class="mt-6 text-ink-600 leading-relaxed">{{ $product->description ?: 'A timeless piece finished by hand, designed to be treasured for generations.' }}</p>

                <dl class="mt-8 space-y-2 text-sm">
                    @if ($product->material)
                        <div class="flex gap-2"><dt class="text-ink-400 w-24">Material</dt><dd class="text-ink-700">{{ $product->material }}</dd></div>
                    @endif
                    <div class="flex gap-2"><dt class="text-ink-400 w-24">SKU</dt><dd class="text-ink-700">{{ $product->sku }}</dd></div>
                    <div class="flex gap-2">
                        <dt class="text-ink-400 w-24">Availability</dt>
                        <dd class="{{ $product->stock > 0 ? 'text-ink-700' : 'text-rose-600' }}">
                            {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                        </dd>
                    </div>
                </dl>

                @if ($product->stock > 0)
                    <form action="{{ route('cart.store', $product) }}" method="POST" class="mt-10 flex items-center gap-4">
                        @csrf
                        <select name="quantity" class="border-ink-200 focus:border-gold-500 focus:ring-gold-400 text-sm">
                            @foreach (range(1, min(5, $product->stock)) as $qty)
                                <option value="{{ $qty }}">Qty {{ $qty }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-gold flex-1">Add to Bag</button>
                    </form>
                @else
                    <button disabled class="mt-10 w-full border border-ink-200 text-ink-400 px-8 py-3 text-xs uppercase tracking-widest2 cursor-not-allowed">Out of Stock</button>
                @endif
            </div>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="max-w-7xl mx-auto px-6 py-24 border-t border-ink-100">
            <div class="text-center mb-14">
                <p class="eyebrow">You May Also Like</p>
                <h2 class="font-serif text-4xl text-ink-900 mt-2">More From {{ $product->category->name }}</h2>
                <div class="section-divider mt-4"><x-jewel-icon icon="diamond" class="w-3 h-3" /></div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-14">
                @foreach ($related as $item)
                    <x-product-card :product="$item" />
                @endforeach
            </div>
        </section>
    @endif

</x-storefront-layout>

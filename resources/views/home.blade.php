<x-storefront-layout>

    <section class="relative bg-ink-900 text-cream overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 py-28 md:py-36 grid md:grid-cols-2 gap-12 items-center">
            <div class="text-center md:text-left order-2 md:order-1">
                <p class="eyebrow text-gold-400">Handcrafted Since 2012</p>
                <h1 class="font-serif text-5xl md:text-6xl mt-4 leading-tight">Jewelry made for<br>life's finest moments</h1>
                <p class="mt-6 text-ink-300 max-w-md mx-auto md:mx-0">Ethically sourced stones, timeless settings, and a promise you'll wear for a lifetime.</p>
                <div class="mt-10 flex justify-center md:justify-start gap-4">
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 bg-gold-600 px-8 py-3 text-xs uppercase tracking-widest2 text-cream transition hover:bg-gold-500">Shop the Collection</a>
                    <a href="{{ route('about') }}" class="inline-flex items-center gap-2 border border-ink-600 px-8 py-3 text-xs uppercase tracking-widest2 text-cream transition hover:border-gold-400">Our Story</a>
                </div>
            </div>
            <div class="order-1 md:order-2 flex justify-center">
                <x-jewel-icon icon="pendant" class="w-56 h-56 md:w-72 md:h-72 text-gold-500" />
            </div>
        </div>
        <div class="absolute inset-0 pointer-events-none opacity-10" style="background-image:radial-gradient(circle at 20% 20%, white 1px, transparent 1px);background-size:28px 28px;"></div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-24">
        <div class="text-center mb-14">
            <p class="eyebrow">Popular Collections</p>
            <h2 class="font-serif text-4xl text-ink-900 mt-2">Shop by Category</h2>
            <div class="section-divider mt-4"><x-jewel-icon icon="diamond" class="w-3 h-3" /></div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach ($categories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="group text-center bg-white border border-ink-100 px-6 py-12 transition hover:border-gold-400 hover:shadow-sm">
                    <x-jewel-icon :icon="$category->icon" class="w-14 h-14 mx-auto text-ink-800 transition group-hover:text-gold-600" />
                    <h3 class="font-serif text-xl text-ink-900 mt-6">{{ $category->name }}</h3>
                    <span class="text-xs uppercase tracking-widest2 text-gold-600 mt-2 inline-block">See the Collection</span>
                </a>
            @endforeach
        </div>
    </section>

    @if ($featured->isNotEmpty())
        <section class="max-w-7xl mx-auto px-6 py-24 border-t border-ink-100">
            <div class="text-center mb-14">
                <p class="eyebrow">New Arrivals</p>
                <h2 class="font-serif text-4xl text-ink-900 mt-2">Featured Pieces</h2>
                <div class="section-divider mt-4"><x-jewel-icon icon="diamond" class="w-3 h-3" /></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-14">
                @foreach ($featured as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <div class="text-center mt-16">
                <a href="{{ route('shop.index') }}" class="btn-gold-outline">View All Jewelry</a>
            </div>
        </section>
    @endif

    <section class="bg-ink-800 text-cream">
        <div class="max-w-4xl mx-auto px-6 py-24 text-center">
            <x-jewel-icon icon="diamond" class="w-10 h-10 mx-auto text-gold-500" />
            <p class="font-serif text-2xl md:text-3xl mt-6 leading-relaxed">"Every piece is finished by hand and inspected three times before it ever reaches a customer."</p>
            <a href="{{ route('about') }}" class="mt-8 inline-block nav-link text-gold-400">Read Our Story</a>
        </div>
    </section>

    @if ($newArrivals->isNotEmpty())
        <section class="max-w-7xl mx-auto px-6 py-24">
            <div class="text-center mb-14">
                <p class="eyebrow">Just In</p>
                <h2 class="font-serif text-4xl text-ink-900 mt-2">Latest Additions</h2>
                <div class="section-divider mt-4"><x-jewel-icon icon="diamond" class="w-3 h-3" /></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-14">
                @foreach ($newArrivals as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

</x-storefront-layout>

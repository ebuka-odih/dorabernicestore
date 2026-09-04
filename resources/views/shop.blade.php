<x-storefront-layout :title="'Shop'">

    <section class="bg-ink-900 text-cream py-16 text-center">
        <p class="eyebrow text-gold-400">The Collection</p>
        <h1 class="font-serif text-4xl mt-2">All Jewelry</h1>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-16">
        <div class="flex flex-col md:flex-row gap-12">

            <aside class="md:w-56 shrink-0 space-y-10">
                <div>
                    <h3 class="eyebrow text-ink-500 mb-4">Category</h3>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="{{ route('shop.index', request()->except('category')) }}" class="{{ request('category') ? 'text-ink-600 hover:text-gold-600' : 'text-gold-600' }}">All</a>
                        </li>
                        @foreach ($categories as $category)
                            <li>
                                <a href="{{ route('shop.index', array_merge(request()->except('category'), ['category' => $category->slug])) }}"
                                   class="{{ request('category') === $category->slug ? 'text-gold-600' : 'text-ink-600 hover:text-gold-600' }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h3 class="eyebrow text-ink-500 mb-4">Sort By</h3>
                    <ul class="space-y-2 text-sm">
                        @foreach (['' => 'Newest', 'price-asc' => 'Price: Low to High', 'price-desc' => 'Price: High to Low', 'name' => 'Name'] as $value => $label)
                            <li>
                                <a href="{{ route('shop.index', array_merge(request()->except('sort'), $value ? ['sort' => $value] : [])) }}"
                                   class="{{ request('sort', '') === $value ? 'text-gold-600' : 'text-ink-600 hover:text-gold-600' }}">
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            <div class="flex-1">
                @if ($products->isEmpty())
                    <p class="text-ink-500">No pieces match your filters just yet.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-14">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    <div class="mt-16">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>

</x-storefront-layout>

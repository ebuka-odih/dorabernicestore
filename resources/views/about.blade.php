<x-storefront-layout :title="'Our Story'">

    <section class="bg-ink-900 text-cream py-24 text-center">
        <p class="eyebrow text-gold-400">Since 2012</p>
        <h1 class="font-serif text-5xl mt-3">Our Story</h1>
    </section>

    <section class="max-w-4xl mx-auto px-6 py-24 text-center">
        <x-jewel-icon icon="pendant" class="w-16 h-16 mx-auto text-gold-500" />
        <p class="font-serif text-2xl md:text-3xl text-ink-900 leading-relaxed mt-8">
            Dora Bernice began at a small workbench with a simple belief — that fine jewelry should be honest in its making and meant to last a lifetime.
        </p>
        <p class="mt-8 text-ink-600 leading-relaxed max-w-2xl mx-auto">
            Every ring, necklace, and pair of earrings we sell is designed in-house and finished by hand, using ethically sourced stones and recycled precious metals wherever possible. We work with a small circle of artisans across the world who share our obsession with detail — from the first sketch to the final polish.
        </p>
    </section>

    <section class="bg-ink-800 text-cream">
        <div class="max-w-6xl mx-auto px-6 py-24 grid md:grid-cols-3 gap-12 text-center">
            <div>
                <x-jewel-icon icon="diamond" class="w-10 h-10 mx-auto text-gold-500" />
                <h3 class="font-serif text-xl mt-6">Responsibly Sourced</h3>
                <p class="mt-3 text-ink-300 text-sm leading-relaxed">Conflict-free stones and recycled metals, traced back to their origin.</p>
            </div>
            <div>
                <x-jewel-icon icon="ring" class="w-10 h-10 mx-auto text-gold-500" />
                <h3 class="font-serif text-xl mt-6">Handcrafted Detail</h3>
                <p class="mt-3 text-ink-300 text-sm leading-relaxed">Each piece is set, polished, and inspected by hand before it leaves our studio.</p>
            </div>
            <div>
                <x-jewel-icon icon="gift" class="w-10 h-10 mx-auto text-gold-500" />
                <h3 class="font-serif text-xl mt-6">Made to Last</h3>
                <p class="mt-3 text-ink-300 text-sm leading-relaxed">Timeless settings built for everyday wear, backed by our craftsmanship promise.</p>
            </div>
        </div>
    </section>

    <section class="max-w-4xl mx-auto px-6 py-24 text-center">
        <p class="eyebrow">Visit Us</p>
        <h2 class="font-serif text-3xl text-ink-900 mt-2">We'd love to meet you</h2>
        <a href="{{ route('contact') }}" class="btn-gold-outline mt-8 inline-flex">Get In Touch</a>
    </section>

</x-storefront-layout>

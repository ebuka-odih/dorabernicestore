<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-2 bg-ink-900 border border-ink-900 px-6 py-3 text-xs uppercase tracking-widest2 text-cream transition hover:bg-gold-600 hover:border-gold-600 focus:outline-none focus:ring-2 focus:ring-gold-400 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>

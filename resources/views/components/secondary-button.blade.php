<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center gap-2 border border-ink-300 px-6 py-3 text-xs uppercase tracking-widest2 text-ink-700 transition hover:border-gold-500 hover:text-gold-600 focus:outline-none focus:ring-2 focus:ring-gold-300 focus:ring-offset-2 disabled:opacity-40']) }}>
    {{ $slot }}
</button>

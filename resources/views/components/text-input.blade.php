@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full border-ink-200 bg-white text-ink-800 focus:border-gold-500 focus:ring-gold-400 rounded-none']) }}>

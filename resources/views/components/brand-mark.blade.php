@props(['class' => 'text-ink-900'])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 '.$class]) }}>
    <x-application-logo class="w-6 h-6 text-gold-500 shrink-0" />
    <span class="font-serif text-2xl tracking-wide leading-none">Dora Bernice</span>
</span>

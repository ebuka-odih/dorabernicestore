@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs uppercase tracking-widest2 text-ink-500 mb-1']) }}>
    {{ $value ?? $slot }}
</label>

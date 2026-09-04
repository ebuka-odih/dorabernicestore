@props(['icon' => 'diamond', 'class' => 'w-8 h-8'])

@php
    $common = 'fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"';
@endphp

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
@switch($icon)
    @case('ring')
        <circle cx="50" cy="60" r="24" />
        <path d="M38 38 L50 18 L62 38 L54 44 H46 Z" />
        <path d="M46 38 L50 30 L54 38" />
        @break

    @case('necklace')
        <path d="M20 20 C20 55 35 68 50 68 C65 68 80 55 80 20" />
        <path d="M42 58 L50 78 L58 58" />
        <circle cx="50" cy="78" r="6" />
        @break

    @case('earrings')
        <circle cx="30" cy="26" r="8" />
        <path d="M30 34 C30 50 22 54 22 66 C22 74 38 74 38 66 C38 54 30 50 30 34" />
        <circle cx="70" cy="26" r="8" />
        <path d="M70 34 C70 50 62 54 62 66 C62 74 78 74 78 66 C78 54 70 50 70 34" />
        @break

    @case('bracelet')
        <ellipse cx="50" cy="52" rx="30" ry="22" />
        <path d="M23 46 L20 40" /><path d="M28 34 L24 27" /><path d="M77 46 L80 40" /><path d="M72 34 L76 27" />
        <path d="M40 30 L44 22 L48 30" />
        <path d="M52 30 L56 22 L60 30" />
        @break

    @case('pendant')
        <path d="M25 22 C25 32 35 36 50 36 C65 36 75 32 75 22" />
        <path d="M42 34 L50 40 L58 34" />
        <path d="M35 55 L50 40 L65 55 L50 82 Z" />
        @break

    @case('watch')
        <circle cx="50" cy="50" r="22" />
        <circle cx="50" cy="50" r="16" />
        <path d="M50 38 L50 50 L58 55" />
        <path d="M42 28 L58 28 L54 18 L46 18 Z" />
        <path d="M42 72 L58 72 L54 82 L46 82 Z" />
        @break

    @case('gift')
        <rect x="22" y="42" width="56" height="40" />
        <path d="M22 58 H78" />
        <path d="M50 42 V82" />
        <path d="M50 42 C40 26 26 30 30 40 C33 46 44 46 50 42 Z" />
        <path d="M50 42 C60 26 74 30 70 40 C67 46 56 46 50 42 Z" />
        @break

    @default
        <path d="M32 40 L50 16 L68 40 L50 84 Z" />
        <path d="M32 40 H68" />
        <path d="M42 40 L50 16 L58 40" />
        <path d="M42 40 L50 84" />
        <path d="M58 40 L50 84" />
@endswitch
</svg>

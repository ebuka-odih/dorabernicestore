<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' — Dora Bernice' : 'Dora Bernice — Fine Jewelry' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream text-ink-800 font-sans">

    <div class="bg-ink-900 text-cream text-center text-[11px] uppercase tracking-widest2 py-2">
        Complimentary shipping on orders over $250
    </div>

    <header class="border-b border-ink-100 bg-cream/95 backdrop-blur sticky top-0 z-40" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-20">
                <button class="md:hidden text-ink-800" @click="open = !open" aria-label="Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>

                <a href="{{ route('home') }}" class="md:flex-none flex-1 flex justify-center md:justify-start">
                    <x-brand-mark />
                </a>

                <nav class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'text-gold-600' : '' }}">Home</a>
                    <a href="{{ route('shop.index') }}" class="nav-link {{ request()->routeIs('shop.*') ? 'text-gold-600' : '' }}">Shop</a>
                    <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'text-gold-600' : '' }}">Our Story</a>
                    <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'text-gold-600' : '' }}">Contact</a>
                </nav>

                <div class="flex items-center gap-5">
                    @auth
                        <a href="{{ route('dashboard') }}" class="nav-link hidden sm:inline">{{ auth()->user()->name }}</a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link hidden sm:inline">Login</a>
                    @endauth

                    <a href="{{ route('cart.index') }}" class="relative text-ink-800 hover:text-gold-600 transition" aria-label="Bag">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12l1 13H5L6 7Z"/><path stroke-linecap="round" d="M9 7a3 3 0 1 1 6 0"/></svg>
                        @if (\App\Support\Cart::count() > 0)
                            <span class="absolute -top-2 -right-2 bg-gold-600 text-cream text-[10px] w-4 h-4 rounded-full flex items-center justify-center">{{ \App\Support\Cart::count() }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        <div x-show="open" x-cloak class="md:hidden border-t border-ink-100 bg-cream px-6 py-4 space-y-4">
            <a href="{{ route('home') }}" class="block nav-link">Home</a>
            <a href="{{ route('shop.index') }}" class="block nav-link">Shop</a>
            <a href="{{ route('about') }}" class="block nav-link">Our Story</a>
            <a href="{{ route('contact') }}" class="block nav-link">Contact</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block nav-link">{{ auth()->user()->name }}</a>
            @else
                <a href="{{ route('login') }}" class="block nav-link">Login</a>
            @endauth
        </div>
    </header>

    @if (session('status'))
        <div class="max-w-7xl mx-auto px-6 pt-6">
            <div class="border border-gold-300 bg-gold-50 text-ink-700 text-sm px-4 py-3">{{ session('status') }}</div>
        </div>
    @endif

    <main>
        {{ $slot }}
    </main>

    <footer class="mt-24 bg-ink-900 text-ink-200">
        <div class="max-w-7xl mx-auto px-6 py-16">
            <div class="text-center mb-14">
                <p class="eyebrow text-gold-500">Newsletter</p>
                <h3 class="font-serif text-3xl text-cream mt-2">We promise only to send the good things</h3>
                <form class="mt-6 max-w-md mx-auto flex" onsubmit="event.preventDefault()">
                    <input type="email" placeholder="Your email address" class="flex-1 bg-transparent border border-ink-600 px-4 py-3 text-cream placeholder:text-ink-400 focus:border-gold-500 focus:ring-0" />
                    <button class="bg-gold-600 px-6 text-cream text-xs uppercase tracking-widest2 hover:bg-gold-500 transition">Send</button>
                </form>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-10 border-t border-ink-700 pt-12 text-sm">
                <div>
                    <h4 class="font-serif text-lg text-cream mb-4">Dora Bernice</h4>
                    <p class="text-ink-400 leading-relaxed">Fine jewelry designed and finished by hand, sourced with care from artisans we trust.</p>
                </div>
                <div>
                    <h4 class="eyebrow text-ink-300 mb-4">Shop</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('shop.index') }}" class="hover:text-gold-400">All Jewelry</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-gold-400">Our Story</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-gold-400">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="eyebrow text-ink-300 mb-4">Account</h4>
                    <ul class="space-y-2">
                        @auth
                            <li><a href="{{ route('account.orders') }}" class="hover:text-gold-400">Order History</a></li>
                            <li><a href="{{ route('profile.edit') }}" class="hover:text-gold-400">Preferences</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-gold-400">Sign In</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-gold-400">Create Account</a></li>
                        @endauth
                        <li><a href="{{ route('cart.index') }}" class="hover:text-gold-400">Your Bag</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="eyebrow text-ink-300 mb-4">Customer Care</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('contact') }}" class="hover:text-gold-400">Help &amp; Contact</a></li>
                        <li><a href="{{ route('shop.index') }}" class="hover:text-gold-400">Shipping &amp; Returns</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-ink-700 mt-12 pt-8 text-center text-xs text-ink-500">
                &copy; {{ now()->year }} Dora Bernice. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>

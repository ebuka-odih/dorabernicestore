@props(['title' => 'Admin'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — Dora Bernice Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-ink-50 text-ink-800 font-sans" x-data="{ open: false }">
    <div class="min-h-screen md:flex">

        <aside class="bg-ink-900 text-ink-200 md:w-64 shrink-0 md:min-h-screen">
            <div class="flex items-center justify-between px-6 py-6 border-b border-ink-700">
                <a href="{{ route('admin.dashboard') }}" class="text-cream">
                    <x-brand-mark class="text-cream" />
                </a>
                <button class="md:hidden text-cream" @click="open = !open">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
            </div>

            <nav class="px-4 py-6 space-y-1" :class="{ 'block': open, 'hidden md:block': !open }">
                @php
                    $links = [
                        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'pattern' => 'admin.dashboard'],
                        ['route' => 'admin.products.index', 'label' => 'Products', 'pattern' => 'admin.products.*'],
                        ['route' => 'admin.categories.index', 'label' => 'Categories', 'pattern' => 'admin.categories.*'],
                        ['route' => 'admin.orders.index', 'label' => 'Orders', 'pattern' => 'admin.orders.*'],
                    ];
                @endphp
                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}"
                       class="block px-4 py-2.5 text-sm uppercase tracking-wide transition {{ request()->routeIs($link['pattern']) ? 'bg-gold-600 text-cream' : 'text-ink-300 hover:bg-ink-800 hover:text-cream' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach

                <div class="pt-6 mt-6 border-t border-ink-700">
                    <a href="{{ route('home') }}" class="block px-4 py-2.5 text-sm text-ink-400 hover:text-gold-400">&larr; View Store</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full text-left px-4 py-2.5 text-sm text-ink-400 hover:text-gold-400">Log Out</button>
                    </form>
                </div>
            </nav>
        </aside>

        <div class="flex-1">
            <header class="bg-white border-b border-ink-100 px-8 py-5 flex items-center justify-between">
                <h1 class="font-serif text-2xl text-ink-900">{{ $title }}</h1>
                <span class="text-sm text-ink-500">{{ auth()->user()->name }}</span>
            </header>

            <main class="p-8">
                @if (session('status'))
                    <div class="mb-6 border border-gold-300 bg-gold-50 text-ink-700 text-sm px-4 py-3">{{ session('status') }}</div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>

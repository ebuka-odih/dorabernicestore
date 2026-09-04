<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink-800 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center px-6 py-12 bg-cream">
            <a href="{{ route('home') }}">
                <x-brand-mark />
            </a>

            <div class="w-full sm:max-w-md mt-8 px-8 py-8 bg-white border border-ink-100 shadow-sm">
                {{ $slot }}
            </div>

            <a href="{{ route('home') }}" class="mt-6 nav-link">&larr; Back to the boutique</a>
        </div>
    </body>
</html>

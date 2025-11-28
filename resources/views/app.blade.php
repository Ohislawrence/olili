<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Olilearn') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="icon" href="{{ asset('favicon.PNG') }}" type="image/x-icon">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

        <meta property="og:title" content="{{ $page['props']['meta']['title'] ?? 'OliLearn' }}" />
        <meta property="og:description" content="{{ $page['props']['meta']['description'] ?? '' }}" />
        <meta property="og:image" content="{{ $page['props']['meta']['image'] ?? '' }}" />
        <meta property="og:url" content="{{ $page['props']['meta']['url'] ?? url()->current() }}" />
        <meta property="og:type" content="website" />

    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>

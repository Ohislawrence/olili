<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-X2H3H3XTFN"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-X2H3H3XTFN');
        </script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Olilearn') }}</title>

        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#6c9108ff"/>
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

        <!-- Manifest -->
        <link rel="manifest" href="/manifest.json">

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
        <meta property="og:logo" content="{{asset('logo-olilearn.PNG')}}" />


        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:site" content="@olilearn" />
        <meta name="twitter:creator" content="@olilearn" />
        <meta name="twitter:url" content="{{ $page['props']['meta']['url'] ?? url()->current() }}" />
        <meta name="twitter:image" content="{{ $page['props']['meta']['url'] ?? url()->current() }}" />
        <meta name="twitter:description" content="{{ $page['props']['meta']['description'] ?? '' }}" />
        <meta name="twitter:title" content="{{ $page['props']['meta']['title'] ?? 'OliLearn' }}" />


        <!-- PWA Script -->
        <script>
            // Register Service Worker
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js')
                        .then(function(registration) {
                            console.log('ServiceWorker registration successful with scope: ', registration.scope);
                        }, function(err) {
                            console.log('ServiceWorker registration failed: ', err);
                        });
                });
            }

            // Detect if app is installed
            window.addEventListener('appinstalled', (evt) => {
                console.log('App was installed.');
            });

            // Check if app is running in standalone mode
            function isRunningStandalone() {
                return window.matchMedia('(display-mode: standalone)').matches ||
                    window.navigator.standalone === true;
            }

            // Before install prompt
            let deferredPrompt;
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;

                // Show install button
                if (window.showInstallPromotion) {
                    window.showInstallPromotion();
                }
            });

            // Install function
            window.installPWA = async function() {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        console.log('User accepted the install prompt');
                    }
                    deferredPrompt = null;
                }
            };
        </script>

    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>

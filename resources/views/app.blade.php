<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Anti-flash : même clé que `COLOR_MODE_STORAGE_KEY` dans resources/js/theme/config.js --}}
        <script>
            (function () {
                var key = 'app-color-mode';
                try {
                    var stored = localStorage.getItem(key);
                    var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    var useDark = stored === 'dark' || ((stored === 'auto' || stored === null) && prefersDark);
                    document.documentElement.classList.toggle('dark', !!useDark);
                } catch (e) {}
            })();
        </script>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>

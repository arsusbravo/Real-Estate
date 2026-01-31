<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        {{-- Force light mode: prevent dark class from ever being added --}}
        <script>
            (function() {
                var html = document.documentElement;
                html.classList.remove('dark');
                var obs = new MutationObserver(function() {
                    if (html.classList.contains('dark')) {
                        html.classList.remove('dark');
                    }
                });
                obs.observe(html, { attributes: true, attributeFilter: ['class'] });
            })();
        </script>

        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead

        {{-- Override theme variables: emerald light theme for public pages --}}
        <style>
            :root, .dark {
                --background: hsl(0 0% 100%) !important;
                --foreground: hsl(215 25% 27%) !important;
                --card: hsl(0 0% 100%) !important;
                --card-foreground: hsl(215 25% 27%) !important;
                --popover: hsl(0 0% 100%) !important;
                --popover-foreground: hsl(215 25% 27%) !important;
                --primary: hsl(160 84% 39%) !important;
                --primary-foreground: hsl(0 0% 100%) !important;
                --secondary: hsl(152 76% 95%) !important;
                --secondary-foreground: hsl(160 84% 25%) !important;
                --muted: hsl(152 40% 97%) !important;
                --muted-foreground: hsl(215 16% 47%) !important;
                --accent: hsl(152 76% 95%) !important;
                --accent-foreground: hsl(160 84% 25%) !important;
                --destructive: hsl(0 84.2% 60.2%) !important;
                --destructive-foreground: hsl(0 0% 98%) !important;
                --border: hsl(152 20% 90%) !important;
                --input: hsl(152 20% 88%) !important;
                --ring: hsl(160 84% 39%) !important;
                --radius: 0.5rem;
                --sidebar-background: hsl(0 0% 98%) !important;
                --sidebar-foreground: hsl(240 5.3% 26.1%) !important;
                --sidebar-primary: hsl(160 84% 39%) !important;
                --sidebar-primary-foreground: hsl(0 0% 98%) !important;
                --sidebar-accent: hsl(152 76% 95%) !important;
                --sidebar-accent-foreground: hsl(160 84% 25%) !important;
                --sidebar-border: hsl(152 20% 90%) !important;
                --sidebar-ring: hsl(160 84% 39%) !important;
                --chart-1: hsl(160 84% 39%) !important;
                --chart-2: hsl(173 58% 39%) !important;
                --chart-3: hsl(197 37% 24%) !important;
                --chart-4: hsl(43 74% 66%) !important;
                --chart-5: hsl(27 87% 67%) !important;
            }

            html {
                background-color: hsl(0 0% 100%) !important;
                color-scheme: light !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>

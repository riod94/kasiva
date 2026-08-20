<!DOCTYPE html>
<html lang="id" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <!-- Universal Theme Engine (Anti-FOUC & Global Toggle) -->
    <script>
        (function() {
            const stored = localStorage.getItem('kasiva_theme');
            const system = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
            const theme = stored || system;
            if (theme === 'light') {
                document.documentElement.classList.add('light');
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            }
        })();

        window.toggleKasivaTheme = function() {
            const isLight = document.documentElement.classList.contains('light');
            const newTheme = isLight ? 'dark' : 'light';
            localStorage.setItem('kasiva_theme', newTheme);
            if (newTheme === 'light') {
                document.documentElement.classList.add('light');
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            }
            window.dispatchEvent(new CustomEvent('kasiva-theme-changed', { detail: newTheme }));
        };
    </script>

    <title>{{ $title ?? 'Kasiva POS — Sistem POS SaaS Modern Multi-Platform' }}</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <link rel="icon" type="image/png" href="{{ asset('images/kasiva-logo-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/kasiva-logo-icon.png') }}">
    <meta name="theme-color" content="#1E1B4B">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased selection:bg-[#4338CA]/30 selection:text-[#3EDAD7]">
    {{ $slot }}
    @livewireScripts
</body>
</html>

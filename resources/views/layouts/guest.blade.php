<!DOCTYPE html>
<html lang="id" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Universal Theme Engine (Anti-FOUC & Global Toggle) --}}
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

    {{-- ═══ SEO & Metadata (halaman publik — wajib lengkap) ═══ --}}
    <title>{{ $title ?? 'Kasiva POS — Sistem Kasir Pintar & Manajemen Inventaris F&B' }}</title>
    <meta name="description" content="{{ $description ?? 'Kasiva POS adalah aplikasi kasir pintar modern (Point of Sale) khusus bisnis F&B dan retail dengan kalkulasi HPP otomatis, manajemen resep, dan integrasi kanal online.' }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Kasiva POS">
    <meta property="og:locale" content="id_ID">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'Kasiva POS — Sistem Kasir Pintar F&B' }}">
    <meta property="og:description" content="{{ $description ?? 'Sistem Kasir Pintar Modern dengan kalkulasi HPP otomatis, laporan laba bersih, dan dukungan platform online.' }}">
    <meta property="og:image" content="{{ asset('images/kasiva-logo-full.png') }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? 'Kasiva POS — Modern Point of Sale' }}">
    <meta name="twitter:description" content="{{ $description ?? 'Sistem Kasir Pintar Modern untuk UMKM dan Bisnis F&B Indonesia.' }}">
    <meta name="twitter:image" content="{{ asset('images/kasiva-logo-full.png') }}">

    {{-- Structured Data: Organization --}}
    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => 'Kasiva POS', 'url' => url('/'), 'logo' => asset('images/kasiva-logo-icon.png'), 'description' => 'Sistem kasir pintar dan manajemen inventaris F&B dengan kalkulasi HPP otomatis.'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

    <link rel="icon" type="image/png" href="{{ asset('images/kasiva-logo-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/kasiva-logo-icon.png') }}">
    <meta name="theme-color" content="#272D48">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased selection:bg-[#00AAA6]/30 selection:text-[#3EDAD7]">
    {{ $slot }}
    @livewireScripts
</body>
</html>

@props([
    'title' => 'Kasiva POS — Sistem Kasir Pintar & Manajemen Inventaris F&B',
    'description' => 'Kasiva POS adalah aplikasi kasir untuk bisnis F&B dan retail dengan kalkulasi HPP, inventaris, dan laporan laba.',
    'robots' => 'index, follow',
    'canonical' => null,
])
<!DOCTYPE html>
<html lang="id" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="{{ $robots }}">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Kasiva POS">
    <meta property="og:locale" content="id_ID">
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:image" content="{{ asset('images/kasiva-social-preview.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Kasiva POS — Kasir bukan cuma mencatat, Kasiva menjaga profit">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ asset('images/kasiva-social-preview.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/kasiva-logo-icon-128.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/kasiva-logo-icon-128.png') }}">
    <meta name="theme-color" content="#F8FAFC" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#171C31" media="(prefers-color-scheme: dark)">
    <script>
        (() => document.documentElement.classList.add(localStorage.getItem('kasiva_theme') || (matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark')))();
        window.toggleKasivaTheme = () => {
            const next = document.documentElement.classList.contains('light') ? 'dark' : 'light';
            document.documentElement.classList.remove('light', 'dark'); document.documentElement.classList.add(next); localStorage.setItem('kasiva_theme', next);
        };
    </script>
    @php($schemaContext = '@'.'context')
    <script type="application/ld+json">{!! json_encode([$schemaContext => 'https://schema.org', '@type' => 'Organization', 'name' => 'Kasiva POS', 'url' => url('/'), 'logo' => asset('images/kasiva-logo-icon-128.png')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-dvh bg-[var(--app-bg)] font-sans text-[var(--text-main)] antialiased">
    {{ $slot }}
    @livewireScripts
</body>
</html>

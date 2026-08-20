<!DOCTYPE html>
<html lang="id" class="h-full bg-[#0F172A] text-slate-100 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasiva POS — Sistem POS SaaS Modern Multi-Platform</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-[#0F172A] text-slate-100 font-sans antialiased selection:bg-[#4338CA]/30 selection:text-[#F97316]">
    {{ $slot }}
    @livewireScripts
</body>
</html>

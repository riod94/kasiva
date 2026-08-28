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

    <!-- SEO & Metadata -->
    <title>{{ $title ?? 'Kasiva POS — Sistem Kasir Pintar & Manajemen Inventaris F&B' }}</title>
    <meta name="description" content="Kasiva POS adalah aplikasi kasir pintar modern (Point of Sale) khusus bisnis F&B dan retail dengan kalkulasi HPP otomatis, manajemen resep, dan integrasi kanal online.">
    <meta name="keywords" content="kasiva pos, aplikasi kasir, sistem pos indonesia, hpp kalkulator, point of sale fnb, kasir offline, struk thermal">
    <meta name="author" content="Kasiva POS Team">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'Kasiva POS — Sistem Kasir Pintar F&B' }}">
    <meta property="og:description" content="Sistem Kasir Pintar Modern dengan kalkulasi HPP otomatis, laporan laba bersih, dan dukungan platform online.">
    <meta property="og:image" content="{{ asset('images/kasiva-social-preview.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:alt" content="Kasiva POS — Kasir bukan cuma mencatat, Kasiva menjaga profit.">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="Kasiva POS — Modern Point of Sale">
    <meta property="twitter:description" content="Sistem Kasir Pintar Modern untuk UMKM dan Bisnis F&B Indonesia.">
    <meta property="twitter:image" content="{{ asset('images/kasiva-social-preview.png') }}">

    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/png" href="{{ asset('images/kasiva-logo-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/kasiva-logo-icon.png') }}">
    <meta name="theme-color" content="#1E1B4B">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body 
    class="h-full flex flex-col font-sans antialiased selection:bg-[#00AAA6]/30 selection:text-[#3EDAD7] min-h-screen"
    x-data="{
        theme: localStorage.getItem('kasiva_theme') || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark')
    }"
    @kasiva-theme-changed.window="theme = $event.detail"
>
    
    @php
        $user = auth()->user();
    @endphp

    <!-- ═══════════════ TOP NAVBAR (Header Desktop & Mobile) ═══════════════ -->
    <header role="banner" class="sticky top-0 z-40 w-full border-b border-[#2E2A68] bg-[#1E1B4B]/95 backdrop-blur-xl shadow-sm print:hidden">
        <div class="flex h-14 items-center px-4 md:px-8 max-w-7xl mx-auto justify-between gap-3">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <a href="{{ route('pos.cashier') }}" class="flex items-center gap-2.5 active:scale-95 transition-all">
                    <img src="/images/kasiva-logo-icon.png" alt="Kasiva POS" class="h-8 w-8 object-contain bg-white/95 p-1 rounded-xl shadow-sm">
                    <span class="font-black text-base text-white tracking-tight">Kasiva</span>
                </a>
                <span class="hidden sm:inline-flex text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-full bg-[#00AAA6]/30 text-[#8696ED] border border-[#00AAA6]/50">
                    {{ $user?->role?->name ?? 'POS' }}
                </span>
            </div>

            <!-- Desktop Navigation Links (Only on md+ screens with RBAC filtering) -->
            <nav class="hidden md:flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-slate-300">
                @if(!$user || $user->hasPermission('POS_ACCESS'))
                    <a href="{{ route('pos.cashier') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('pos.cashier') ? 'bg-[#00AAA6] text-white shadow-md' : 'hover:text-white hover:bg-[#2A3155]' }}">
                        <x-icon name="store" class="w-4 h-4" />
                        <span>Kasir</span>
                    </a>
                @endif

                @if($user && ($user->hasPermission('VIEW_PRODUCTS') || $user->hasPermission('VIEW_MATERIALS') || $user->hasPermission('MANAGE_PRODUCTS')))
                    <a href="{{ route('inventory.index') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('inventory.*') ? 'bg-[#00AAA6] text-white shadow-md' : 'hover:text-white hover:bg-[#2A3155]' }}">
                        <x-icon name="package" class="w-4 h-4" />
                        <span>Inventaris</span>
                    </a>
                @endif

                @if($user && ($user->hasPermission('MANAGE_PROMOS') || $user->hasPermission('VIEW_MEMBERS') || $user->hasPermission('MANAGE_LOYALTY')))
                    <a href="{{ route('marketing.index') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('marketing.*') ? 'bg-[#00AAA6] text-white shadow-md' : 'hover:text-white hover:bg-[#2A3155]' }}">
                        <x-icon name="megaphone" class="w-4 h-4" />
                        <span>Pemasaran</span>
                    </a>
                @endif

                @if($user && $user->hasPermission('VIEW_TRANSACTIONS'))
                    <a href="{{ route('history.index') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('history.index') || request()->routeIs('history.backdate') ? 'bg-[#00AAA6] text-white shadow-md' : 'hover:text-white hover:bg-[#2A3155]' }}">
                        <x-icon name="receipt" class="w-4 h-4" />
                        <span>Riwayat</span>
                    </a>
                @endif

                @if($user && $user->hasPermission('MANAGE_EXPENSES'))
                    <a href="{{ route('expenses.index') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('expenses.index') ? 'bg-[#00AAA6] text-white shadow-md' : 'hover:text-white hover:bg-[#2A3155]' }}">
                        <x-icon name="wallet" class="w-4 h-4" />
                        <span>Pengeluaran</span>
                    </a>
                @endif

                @if($user && $user->hasPermission('VIEW_REPORTS'))
                    <a href="{{ route('reports.index') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('reports.index') ? 'bg-[#00AAA6] text-white shadow-md' : 'hover:text-white hover:bg-[#2A3155]' }}">
                        <x-icon name="chart-bar" class="w-4 h-4" />
                        <span>Laporan</span>
                    </a>
                @endif

                @if($user && ($user->isOwner() || $user->hasPermission('MANAGE_OUTLET') || $user->hasPermission('MANAGE_STAFF') || $user->hasPermission('MANAGE_ROLES') || $user->hasPermission('MANAGE_PAYMENTS') || $user->hasPermission('MANAGE_PRINTER')))
                    <a href="{{ route('settings.index') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('settings.*') ? 'bg-[#00AAA6] text-white shadow-md' : 'hover:text-white hover:bg-[#2A3155]' }}">
                        <x-icon name="settings" class="w-4 h-4" />
                        <span>Pengaturan</span>
                    </a>
                @endif
            </nav>
            
            <!-- Right Actions: Theme Switcher, Sync Status & Profile -->
            <div class="flex items-center gap-2">
                <!-- Theme Toggle Button -->
                <button 
                    type="button"
                    onclick="window.toggleKasivaTheme()"
                    class="w-9 h-9 rounded-xl bg-[#16192E] border border-[#2E2A68] hover:border-[#00AAA6] text-slate-300 hover:text-white flex items-center justify-center transition active:scale-95 shadow-sm"
                    title="Ganti Tema (Dark / Light)"
                >
                    <template x-if="theme === 'dark'">
                        <x-icon name="sun" class="w-4 h-4 text-amber-400" />
                    </template>
                    <template x-if="theme === 'light'">
                        <x-icon name="moon" class="w-4 h-4 text-[#8696ED]" />
                    </template>
                </button>

                <!-- Sync Status Badge -->
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-bold bg-emerald-500/10 text-emerald-300 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="hidden sm:inline">Online</span>
                </div>

                <!-- Profile Avatar -->
                <a href="{{ route('profile.show') }}" class="h-9 w-9 rounded-xl bg-[#00AAA6] border border-[#00AAA6]/60 flex items-center justify-center text-xs font-black text-white shadow-sm hover:scale-105 active:scale-95 transition" title="Profil Staf: {{ $user?->name ?? 'Kasir' }}">
                    {{ strtoupper(substr($user?->name ?? 'K', 0, 1)) }}
                </a>
            </div>
        </div>
    </header>

    <!-- ═══════════════ MAIN CONTENT CONTAINER ═══════════════ -->
    <main class="flex-1 max-w-7xl w-full mx-auto pb-24 md:pb-8 px-4 md:px-8 py-5">
        {{ $slot }}
    </main>

    <!-- ═══════════════ BOTTOM NAVIGATION BAR (Mobile Viewport with RBAC filtering) ═══════════════ -->
    <nav role="navigation" aria-label="Navigasi utama" class="md:hidden fixed bottom-0 left-0 right-0 bg-[#1E1B4B]/95 backdrop-blur-xl border-t border-[#2E2A68] z-50 shadow-[0_-8px_30px_rgba(0,0,0,0.5)] print:hidden">
        <div class="flex items-center justify-around h-16 max-w-md mx-auto px-2">
            
            <!-- 1. Kasir -->
            @if(!$user || $user->hasPermission('POS_ACCESS'))
                <a href="{{ route('pos.cashier') }}" 
                   class="flex flex-col items-center justify-center flex-1 h-full gap-0.5 transition-all focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3EDAD7] focus-visible:rounded-xl {{ request()->routeIs('pos.cashier') ? 'text-[#3EDAD7]' : 'text-slate-400 hover:text-slate-200' }}">
                    <div class="p-1 rounded-xl transition-transform {{ request()->routeIs('pos.cashier') ? 'bg-[#00AAA6]/30 scale-105' : '' }}">
                        <x-icon name="store" class="w-5 h-5 {{ request()->routeIs('pos.cashier') ? 'text-[#3EDAD7]' : '' }}" />
                    </div>
                    <span class="text-[10px] uppercase tracking-wider leading-none {{ request()->routeIs('pos.cashier') ? 'font-black text-[#3EDAD7]' : 'font-bold' }}">
                        Kasir
                    </span>
                </a>
            @endif

            <!-- 2. Inventaris -->
            @if($user && ($user->hasPermission('VIEW_PRODUCTS') || $user->hasPermission('VIEW_MATERIALS') || $user->hasPermission('MANAGE_PRODUCTS')))
                <a href="{{ route('inventory.index') }}" 
                   class="flex flex-col items-center justify-center flex-1 h-full gap-0.5 transition-all focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3EDAD7] focus-visible:rounded-xl {{ request()->routeIs('inventory.*') ? 'text-[#3EDAD7]' : 'text-slate-400 hover:text-slate-200' }}">
                    <div class="p-1 rounded-xl transition-transform {{ request()->routeIs('inventory.*') ? 'bg-[#00AAA6]/30 scale-105' : '' }}">
                        <x-icon name="package" class="w-5 h-5 {{ request()->routeIs('inventory.*') ? 'text-[#3EDAD7]' : '' }}" />
                    </div>
                    <span class="text-[10px] uppercase tracking-wider leading-none {{ request()->routeIs('inventory.*') ? 'font-black text-[#3EDAD7]' : 'font-bold' }}">
                        Katalog
                    </span>
                </a>
            @endif

            <!-- 3. Riwayat / Pemasaran -->
            @if($user && $user->hasPermission('VIEW_TRANSACTIONS'))
                <a href="{{ route('history.index') }}" 
                   class="flex flex-col items-center justify-center flex-1 h-full gap-0.5 transition-all focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3EDAD7] focus-visible:rounded-xl {{ request()->routeIs('history.*') ? 'text-[#3EDAD7]' : 'text-slate-400 hover:text-slate-200' }}">
                    <div class="p-1 rounded-xl transition-transform {{ request()->routeIs('history.*') ? 'bg-[#00AAA6]/30 scale-105' : '' }}">
                        <x-icon name="receipt" class="w-5 h-5 {{ request()->routeIs('history.*') ? 'text-[#3EDAD7]' : '' }}" />
                    </div>
                    <span class="text-[10px] uppercase tracking-wider leading-none {{ request()->routeIs('history.*') ? 'font-black text-[#3EDAD7]' : 'font-bold' }}">
                        Riwayat
                    </span>
                </a>
            @endif

            <!-- 4. Laporan -->
            @if($user && $user->hasPermission('VIEW_REPORTS'))
                <a href="{{ route('reports.index') }}" 
                   class="flex flex-col items-center justify-center flex-1 h-full gap-0.5 transition-all focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3EDAD7] focus-visible:rounded-xl {{ request()->routeIs('reports.index') ? 'text-[#3EDAD7]' : 'text-slate-400 hover:text-slate-200' }}">
                    <div class="p-1 rounded-xl transition-transform {{ request()->routeIs('reports.index') ? 'bg-[#00AAA6]/30 scale-105' : '' }}">
                        <x-icon name="chart-bar" class="w-5 h-5 {{ request()->routeIs('reports.index') ? 'text-[#3EDAD7]' : '' }}" />
                    </div>
                    <span class="text-[10px] uppercase tracking-wider leading-none {{ request()->routeIs('reports.index') ? 'font-black text-[#3EDAD7]' : 'font-bold' }}">
                        Laporan
                    </span>
                </a>
            @endif

            <!-- 5. Pengaturan -->
            @if($user && ($user->isOwner() || $user->hasPermission('MANAGE_OUTLET') || $user->hasPermission('MANAGE_STAFF') || $user->hasPermission('MANAGE_ROLES') || $user->hasPermission('MANAGE_PAYMENTS') || $user->hasPermission('MANAGE_PRINTER')))
                <a href="{{ route('settings.index') }}" 
                   class="flex flex-col items-center justify-center flex-1 h-full gap-0.5 transition-all focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3EDAD7] focus-visible:rounded-xl {{ request()->routeIs('settings.*') ? 'text-[#3EDAD7]' : 'text-slate-400 hover:text-slate-200' }}">
                    <div class="p-1 rounded-xl transition-transform {{ request()->routeIs('settings.*') ? 'bg-[#00AAA6]/30 scale-105' : '' }}">
                        <x-icon name="settings" class="w-5 h-5 {{ request()->routeIs('settings.*') ? 'text-[#3EDAD7]' : '' }}" />
                    </div>
                    <span class="text-[10px] uppercase tracking-wider leading-none {{ request()->routeIs('settings.*') ? 'font-black text-[#3EDAD7]' : 'font-bold' }}">
                        Setelan
                    </span>
                </a>
            @endif
        </div>
    </nav>

    @livewireScripts
</body>
</html>

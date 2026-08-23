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

    <title>@yield('code') — @yield('title') · Kasiva POS</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/kasiva-logo-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/kasiva-logo-icon.png') }}">
    <meta name="theme-color" content="#272D48">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body 
    class="min-h-screen bg-[#0F172A] text-slate-100 flex flex-col justify-between p-4 sm:p-6 md:p-8 font-sans antialiased selection:bg-[#00AAA6]/30 selection:text-[#3EDAD7] relative overflow-x-hidden"
    x-data="{
        theme: localStorage.getItem('kasiva_theme') || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark'),
        showDetails: false
    }"
    @kasiva-theme-changed.window="theme = $event.detail"
>
    <!-- Background Ambient Glow & Mesh Elements -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0" aria-hidden="true">
        <div class="absolute -top-32 -left-32 w-96 h-96 @yield('glow_color', 'bg-[#00AAA6]')/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 @yield('glow_color_secondary', 'bg-[#00AAA6]')/15 rounded-full blur-3xl"></div>
    </div>

    <!-- ═══════════════ HEADER BAR ═══════════════ -->
    <header class="w-full max-w-5xl mx-auto flex items-center justify-between relative z-10">
        <a href="{{ route('pos.cashier') }}" class="flex items-center gap-2.5 active:scale-95 transition-all">
            <img src="/images/kasiva-logo-full.png" alt="Kasiva POS" class="h-9 md:h-10 object-contain bg-white/95 p-1.5 rounded-2xl shadow-md">
        </a>

        <div class="flex items-center gap-2">
            <!-- Theme Toggle -->
            <button 
                type="button"
                onclick="window.toggleKasivaTheme()"
                class="w-10 h-10 rounded-2xl bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#00AAA6] text-slate-300 hover:text-white flex items-center justify-center transition active:scale-95 shadow-md cursor-pointer"
                title="Ganti Tema (Dark / Light)"
            >
                <x-icon name="sun" class="w-4 h-4 text-amber-400 block dark:hidden" />
                <x-icon name="moon" class="w-4 h-4 text-[#8696ED] hidden dark:block" />
            </button>

            <!-- Status Indicator -->
            <div class="hidden sm:flex items-center gap-2 px-3.5 py-2 rounded-2xl text-xs font-black uppercase tracking-wider bg-[#1E1B4B] border border-[#2E2A68] text-slate-300 shadow-sm">
                <span class="w-2 h-2 rounded-full @yield('badge_indicator_color', 'bg-rose-500') animate-ping"></span>
                <span>Error @yield('code')</span>
            </div>
        </div>
    </header>

    <!-- ═══════════════ MAIN HERO ERROR CARD ═══════════════ -->
    <main class="my-auto py-8 sm:py-12 max-w-2xl w-full mx-auto text-center relative z-10">
        <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-[36px] p-6 sm:p-10 shadow-2xl space-y-6 relative overflow-hidden backdrop-blur-xl">
            
            <!-- Top Accent Line -->
            <div class="absolute top-0 left-0 right-0 h-1.5 @yield('accent_bar', 'bg-gradient-to-r from-rose-500 via-[#00AAA6] to-[#00AAA6]')"></div>

            <!-- Big Icon Badge -->
            <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto rounded-3xl @yield('icon_bg', 'bg-rose-500/15 text-rose-400 border border-rose-500/30') flex items-center justify-center shadow-inner relative group">
                <div class="scale-125 transition-transform group-hover:scale-135">
                    @yield('icon')
                </div>
            </div>

            <!-- Code & Category Badge -->
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest @yield('badge_style', 'bg-rose-500/20 text-rose-300 border border-rose-500/40')">
                    <span>KODE STATUS @yield('code')</span>
                    <span>•</span>
                    <span>@yield('category', 'SISTEM POS')</span>
                </div>

                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-white tracking-tight leading-tight">
                    @yield('title')
                </h1>
            </div>

            <!-- Descriptive Context & Solution Paragraph -->
            <p class="text-xs sm:text-sm text-slate-300 font-medium leading-relaxed max-w-lg mx-auto px-2">
                @yield('message')
            </p>

            <!-- Technical details accordion (if any exception message exists) -->
            @hasSection('details')
                <div class="pt-2">
                    <button 
                        type="button"
                        @click="showDetails = !showDetails"
                        class="text-[11px] font-bold text-slate-400 hover:text-white transition flex items-center justify-center gap-1.5 mx-auto px-3 py-1.5 rounded-xl bg-[#16192E] border border-[#2E2A68]"
                    >
                        <span x-text="showDetails ? 'Sembunyikan Rincian Teknis' : 'Tampilkan Rincian Teknis'"></span>
                        <x-icon name="chevron-down" class="w-3.5 h-3.5 transition-transform" ::class="showDetails ? 'rotate-180' : ''" />
                    </button>

                    <div x-show="showDetails" x-collapse class="mt-3 p-4 rounded-2xl bg-[#16192E] border border-[#2E2A68] text-left text-xs font-mono text-slate-300 space-y-1.5 break-all shadow-inner">
                        @yield('details')
                    </div>
                </div>
            @endif

            <!-- Action Buttons Grid -->
            <div class="pt-4 border-t border-[#2E2A68] flex flex-col sm:flex-row items-center justify-center gap-3">
                @section('actions')
                    <a 
                        href="{{ route('pos.cashier') }}" 
                        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider bg-[#00AAA6] hover:bg-[#008F8C] text-white shadow-xl shadow-[#00AAA6]/30 flex items-center justify-center gap-2 active:scale-95 transition"
                    >
                        <x-icon name="store" class="w-4 h-4" />
                        <span>Kembali ke Kasir POS</span>
                    </a>

                    <button 
                        type="button" 
                        onclick="window.history.back()"
                        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-wider bg-[#16192E] hover:bg-[#2A3155] text-slate-200 border border-[#2E2A68] flex items-center justify-center gap-2 active:scale-95 transition"
                    >
                        <x-icon name="arrow-left" class="w-4 h-4" />
                        <span>Halaman Sebelumnya</span>
                    </button>
                @show
            </div>
        </div>
    </main>

    <!-- ═══════════════ FOOTER BAR ═══════════════ -->
    <footer class="w-full max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 font-medium py-3 border-t border-[#2E2A68]/60 gap-2 relative z-10">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>Kasiva POS Engine — v1.0.0</span>
        </div>
        <div>
            <span>Butuh bantuan akses? Hubungi Admin / Owner Outlet</span>
        </div>
    </footer>
</body>
</html>

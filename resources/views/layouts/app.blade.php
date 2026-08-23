<!DOCTYPE html>
<html lang="id" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <meta name="theme-color" content="#272D48">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Kasiva POS' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/kasiva-logo-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/kasiva-logo-icon.png') }}">
    <script>
        (() => {
            const theme = localStorage.getItem('kasiva_theme') || (matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
            document.documentElement.classList.add(theme);
        })();
        window.toggleKasivaTheme = () => {
            const next = document.documentElement.classList.contains('light') ? 'dark' : 'light';
            document.documentElement.classList.remove('light', 'dark');
            document.documentElement.classList.add(next);
            localStorage.setItem('kasiva_theme', next);
            window.dispatchEvent(new CustomEvent('kasiva-theme-changed', { detail: next }));
        };
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-dvh bg-[var(--app-bg)] font-sans text-[var(--text-main)] antialiased">
    @php($user = auth()->user())
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-[#00AAA6] focus:px-4 focus:py-2 focus:text-white">Lewati ke konten utama</a>

    <header class="sticky top-0 z-40 border-b border-[var(--border-color)] bg-[var(--header-bg)] backdrop-blur-xl print:hidden" role="banner">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-3 px-4 pt-[env(safe-area-inset-top)] md:px-8">
            <a href="{{ route('pos.cashier') }}" class="flex min-h-11 items-center gap-2.5 rounded-xl focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3EDAD7]">
                <img src="{{ asset('images/kasiva-logo-icon.png') }}" alt="Kasiva POS" class="h-8 w-8 rounded-xl bg-white p-1 shadow-sm">
                <span class="font-black tracking-tight">Kasiva</span>
            </a>

            <nav class="hidden items-center gap-1 lg:flex" aria-label="Navigasi utama desktop">
                @php($nav = [
                    ['route' => 'pos.cashier', 'label' => 'Kasir', 'icon' => 'store', 'show' => !$user || $user->hasPermission('POS_ACCESS')],
                    ['route' => 'inventory.index', 'label' => 'Inventaris', 'icon' => 'package', 'show' => $user && ($user->hasPermission('VIEW_PRODUCTS') || $user->hasPermission('VIEW_MATERIALS') || $user->hasPermission('MANAGE_PRODUCTS'))],
                    ['route' => 'history.index', 'label' => 'Riwayat', 'icon' => 'receipt', 'show' => $user && $user->hasPermission('VIEW_TRANSACTIONS')],
                    ['route' => 'expenses.index', 'label' => 'Pengeluaran', 'icon' => 'wallet', 'show' => $user && $user->hasPermission('MANAGE_EXPENSES')],
                    ['route' => 'reports.index', 'label' => 'Laporan', 'icon' => 'chart-bar', 'show' => $user && $user->hasPermission('VIEW_REPORTS')],
                    ['route' => 'settings.index', 'label' => 'Setelan', 'icon' => 'settings', 'show' => $user && ($user->isOwner() || $user->hasPermission('MANAGE_OUTLET') || $user->hasPermission('MANAGE_STAFF') || $user->hasPermission('MANAGE_ROLES') || $user->hasPermission('MANAGE_PAYMENTS') || $user->hasPermission('MANAGE_PRINTER'))],
                ])
                @foreach($nav as $item)
                    @if($item['show'])
                        @php($active = request()->routeIs($item['route']))
                        <a href="{{ route($item['route']) }}" @if($active) aria-current="page" @endif class="flex min-h-11 items-center gap-1.5 rounded-xl px-3 text-xs font-bold transition {{ $active ? 'bg-[#00AAA6] text-white' : 'text-[var(--text-muted)] hover:bg-[var(--card-sub-bg)] hover:text-[var(--text-main)]' }}">
                            <x-icon :name="$item['icon']" class="h-4 w-4" />{{ $item['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <button type="button" onclick="window.toggleKasivaTheme()" class="flex h-11 w-11 items-center justify-center rounded-xl border border-[var(--border-color)] bg-[var(--card-sub-bg)] text-[var(--text-main)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3EDAD7]" aria-label="Ganti tema gelap atau terang">
                    <x-icon name="sun" class="h-4 w-4" />
                </button>
                <div id="kasiva-network-badge" role="status" aria-live="polite" class="hidden min-h-11 items-center rounded-xl border px-3 text-xs font-bold sm:flex"></div>
                <a href="{{ route('profile.show') }}" class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#00AAA6] text-sm font-black text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3EDAD7]" aria-label="Buka profil {{ $user?->name ?? 'Kasir' }}">{{ strtoupper(substr($user?->name ?? 'K', 0, 1)) }}</a>
            </div>
        </div>
    </header>

    <main id="main-content" class="mx-auto w-full max-w-7xl flex-1 px-4 py-5 pb-[calc(4.75rem+env(safe-area-inset-bottom))] md:px-8 lg:pb-8">
        {{ $slot }}
    </main>

    <nav class="fixed inset-x-0 bottom-0 z-50 border-t border-[var(--border-color)] bg-[var(--header-bg)] pb-[env(safe-area-inset-bottom)] backdrop-blur-xl print:hidden lg:hidden" aria-label="Navigasi utama mobile">
        <div class="mx-auto flex h-16 max-w-md items-stretch">
            @foreach(array_slice($nav, 0, 1) as $item)
                @if($item['show']) @php($active = request()->routeIs($item['route']))
                <a href="{{ route($item['route']) }}" @if($active) aria-current="page" @endif class="flex min-w-0 flex-1 flex-col items-center justify-center gap-1 text-[10px] font-bold {{ $active ? 'text-[#00AAA6]' : 'text-[var(--text-muted)]' }}"><x-icon :name="$item['icon']" class="h-5 w-5" />{{ $item['label'] }}</a>
                @endif
            @endforeach
            @foreach(array_slice($nav, 2, 4) as $item)
                @if($item['show']) @php($active = request()->routeIs($item['route']))
                <a href="{{ route($item['route']) }}" @if($active) aria-current="page" @endif class="flex min-w-0 flex-1 flex-col items-center justify-center gap-1 text-[10px] font-bold {{ $active ? 'text-[#00AAA6]' : 'text-[var(--text-muted)]' }}"><x-icon :name="$item['icon']" class="h-5 w-5" />{{ $item['label'] }}</a>
                @endif
            @endforeach
        </div>
    </nav>

    <div id="kasiva-connection-status" role="status" aria-live="polite" class="fixed bottom-[calc(5rem+env(safe-area-inset-bottom))] left-4 z-[60] hidden rounded-xl px-3 py-2 text-xs font-bold text-white lg:bottom-4"></div>
    @livewireScripts
    @stack('scripts')
</body>
</html>

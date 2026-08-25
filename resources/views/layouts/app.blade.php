<!DOCTYPE html>
<html lang="id" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <meta name="theme-color" content="#F8FAFC" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#171C31" media="(prefers-color-scheme: dark)">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Kasiva POS' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/kasiva-logo-icon-128.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/kasiva-logo-icon-128.png') }}">
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
    @php
        $user = auth()->user();
        $nav = [
            ['route' => 'pos.cashier', 'pattern' => 'pos.cashier', 'label' => 'Kasir', 'mobile' => 'Kasir', 'icon' => 'store', 'show' => !$user || $user->hasPermission('POS_ACCESS')],
            ['route' => 'inventory.index', 'pattern' => 'inventory.*', 'label' => 'Inventaris', 'mobile' => 'Katalog', 'icon' => 'package', 'show' => $user && ($user->hasPermission('VIEW_PRODUCTS') || $user->hasPermission('VIEW_MATERIALS') || $user->hasPermission('MANAGE_PRODUCTS'))],
            ['route' => 'marketing.index', 'pattern' => 'marketing.*', 'label' => 'Pemasaran', 'mobile' => 'Promo', 'icon' => 'megaphone', 'show' => $user && ($user->hasPermission('MANAGE_PROMOS') || $user->hasPermission('VIEW_MEMBERS') || $user->hasPermission('MANAGE_LOYALTY'))],
            ['route' => 'history.index', 'pattern' => 'history.*', 'label' => 'Riwayat', 'mobile' => 'Riwayat', 'icon' => 'receipt', 'show' => $user && $user->hasPermission('VIEW_TRANSACTIONS')],
            ['route' => 'expenses.index', 'pattern' => 'expenses.*', 'label' => 'Pengeluaran', 'mobile' => 'Beban', 'icon' => 'wallet', 'show' => $user && $user->hasPermission('MANAGE_EXPENSES')],
            ['route' => 'reports.index', 'pattern' => 'reports.*', 'label' => 'Laporan', 'mobile' => 'Laporan', 'icon' => 'chart-bar', 'show' => $user && $user->hasPermission('VIEW_REPORTS')],
            ['route' => 'settings.index', 'pattern' => 'settings.*', 'label' => 'Setelan', 'mobile' => 'Setelan', 'icon' => 'settings', 'show' => $user && ($user->isOwner() || $user->hasPermission('MANAGE_OUTLET') || $user->hasPermission('MANAGE_STAFF') || $user->hasPermission('MANAGE_ROLES') || $user->hasPermission('MANAGE_PAYMENTS') || $user->hasPermission('MANAGE_PRINTER'))],
        ];
        $visibleNav = array_values(array_filter($nav, fn (array $item): bool => $item['show']));
        $mobileNav = count($visibleNav) > 5 ? array_values(array_filter($visibleNav, fn (array $item): bool => in_array($item['route'], ['pos.cashier', 'inventory.index', 'history.index', 'reports.index', 'settings.index']))) : $visibleNav;
    @endphp

    <a href="#main-content" class="ks-focus fixed left-4 top-4 z-[100] -translate-y-24 rounded-xl bg-[#00AAA6] px-4 py-2.5 text-sm font-extrabold text-white transition-transform focus:translate-y-0">Lewati ke konten utama</a>

    <header class="sticky top-0 z-40 border-b border-[var(--border-color)] bg-[var(--header-bg)] backdrop-blur-xl print:hidden" role="banner">
        <div class="mx-auto flex min-h-16 max-w-[1440px] items-center justify-between gap-3 px-4 pt-[env(safe-area-inset-top)] md:px-6 xl:px-8">
            <a href="{{ route('pos.cashier') }}" class="ks-focus flex min-h-11 shrink-0 items-center gap-2.5 rounded-xl" aria-label="Kasiva POS, buka kasir">
                <img src="{{ asset('images/kasiva-logo-icon-128.png') }}" alt="" width="36" height="36" class="h-9 w-9 rounded-xl bg-white p-1 shadow-sm">
                <span class="hidden font-black tracking-tight sm:block">Kasiva</span>
                <span class="hidden rounded-full border border-[#00AAA6]/30 bg-[#00AAA6]/10 px-2 py-1 text-[9px] font-black uppercase tracking-widest text-[#00AAA6] xl:block">{{ $user?->role?->name ?? 'POS' }}</span>
            </a>

            <nav class="hidden min-w-0 items-center gap-1 lg:flex" aria-label="Navigasi utama desktop">
                @foreach($visibleNav as $item)
                    @php($active = request()->routeIs($item['pattern']))
                    <a href="{{ route($item['route']) }}" @if($active) aria-current="page" @endif class="ks-focus flex min-h-11 items-center gap-1.5 rounded-xl px-2.5 text-xs font-bold transition xl:px-3 {{ $active ? 'bg-[#00AAA6] text-white shadow-md shadow-[#00AAA6]/20' : 'text-[var(--text-muted)] hover:bg-[var(--card-sub-bg)] hover:text-[var(--text-main)]' }}">
                        <x-icon :name="$item['icon']" class="h-4 w-4" /><span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="flex shrink-0 items-center gap-2">
                <button type="button" onclick="window.toggleKasivaTheme()" class="ks-focus flex h-11 w-11 items-center justify-center rounded-xl border border-[var(--border-color)] bg-[var(--card-sub-bg)] text-[var(--text-main)] transition hover:border-[#00AAA6]" aria-label="Ganti tema terang atau gelap">
                    <x-icon name="sun" class="hidden h-4 w-4 dark:block" /><x-icon name="moon" class="h-4 w-4 dark:hidden" />
                </button>
                <div id="kasiva-network-badge" role="status" aria-live="polite" class="hidden min-h-11 items-center rounded-xl border px-3 text-xs font-bold sm:flex"></div>
                <a href="{{ route('profile.show') }}" class="ks-focus flex h-11 w-11 items-center justify-center rounded-xl bg-[#00AAA6] text-sm font-black text-white shadow-md shadow-[#00AAA6]/20" aria-label="Buka profil {{ $user?->name ?? 'Kasir' }}">{{ strtoupper(substr($user?->name ?? 'K', 0, 1)) }}</a>
            </div>
        </div>
    </header>

    <main id="main-content" class="mx-auto w-full max-w-[1440px] flex-1 px-4 py-5 pb-[calc(5.25rem+env(safe-area-inset-bottom))] md:px-6 xl:px-8 lg:pb-8">
        {{ $slot }}
    </main>

    <nav class="fixed inset-x-0 bottom-0 z-50 border-t border-[var(--border-color)] bg-[var(--header-bg)] pb-[env(safe-area-inset-bottom)] backdrop-blur-xl print:hidden lg:hidden" aria-label="Navigasi utama mobile">
        <div class="mx-auto flex h-[68px] max-w-lg items-stretch px-1">
            @foreach($mobileNav as $item)
                @php($active = request()->routeIs($item['pattern']))
                <a href="{{ route($item['route']) }}" @if($active) aria-current="page" @endif class="ks-focus flex min-w-0 flex-1 flex-col items-center justify-center gap-1 rounded-xl text-[10px] font-bold {{ $active ? 'text-[#00AAA6]' : 'text-[var(--text-muted)]' }}">
                    <span class="flex h-7 w-9 items-center justify-center rounded-xl {{ $active ? 'bg-[#00AAA6]/15' : '' }}"><x-icon :name="$item['icon']" class="h-5 w-5" /></span><span class="truncate">{{ $item['mobile'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    <div id="kasiva-connection-status" role="status" aria-live="polite" class="fixed bottom-[calc(5.5rem+env(safe-area-inset-bottom))] left-4 z-[60] hidden rounded-xl px-3 py-2 text-xs font-bold text-white lg:bottom-4"></div>
    @livewireScripts
    @stack('scripts')
</body>
</html>

@props(['mobileOpen' => false])

<header class="lp-header sticky top-0 z-50 border-b lp-border backdrop-blur-xl" x-data="{ open: @js((bool) $mobileOpen) }" x-on:keydown.escape.window="open = false">
    <div class="mx-auto flex h-[72px] max-w-7xl items-center justify-between gap-4 px-4 md:px-8">
        <a href="{{ route('landing') }}" class="lp-focus flex min-h-11 items-center gap-2.5 rounded-xl" aria-label="Kasiva POS, kembali ke beranda">
            <img src="{{ asset('images/kasiva-logo-icon-128.png') }}" alt="" width="40" height="40" class="h-10 w-10 rounded-xl bg-white p-1 shadow-sm">
            <span class="text-xl font-black tracking-tight">Kasiva</span>
        </a>
        <nav class="hidden items-center gap-7 text-sm font-bold lg:!flex" aria-label="Navigasi utama">
            <a href="#fitur" class="lp-focus lp-muted rounded-lg transition hover:text-[#00AAA6]">Fitur</a>
            <a href="#keunggulan" class="lp-focus lp-muted rounded-lg transition hover:text-[#00AAA6]">Keunggulan</a>
            <a href="#cara-kerja" class="lp-focus lp-muted rounded-lg transition hover:text-[#00AAA6]">Cara kerja</a>
            <a href="#harga" class="lp-focus lp-muted rounded-lg transition hover:text-[#00AAA6]">Harga</a>
            <a href="#faq" class="lp-focus lp-muted rounded-lg transition hover:text-[#00AAA6]">FAQ</a>
        </nav>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.toggleKasivaTheme()" class="lp-focus flex h-11 w-11 items-center justify-center rounded-xl border lp-border lp-surface transition hover:border-[#00AAA6]" aria-label="Ganti tema terang atau gelap" title="Ganti tema">
                <x-icon name="sun" class="hidden h-5 w-5 dark:block" />
                <x-icon name="moon" class="h-5 w-5 dark:hidden" />
            </button>
            <a href="{{ route('login') }}" class="lp-focus hidden min-h-11 items-center rounded-xl px-3 text-sm font-bold lg:!flex">Masuk</a>
            <a href="{{ route('register') }}" class="lp-focus inline-flex min-h-11 items-center gap-2 rounded-xl bg-[#00AAA6] px-4 text-sm font-extrabold text-white shadow-lg shadow-[#00AAA6]/20 transition hover:bg-[#008F8C]">Mulai gratis <x-icon name="arrow-right" class="h-4 w-4" /></a>
            <button type="button" x-on:click="open = ! open" class="lp-focus flex h-11 w-11 items-center justify-center rounded-xl border lp-border lp-surface lg:!hidden" aria-label="Buka menu navigasi" x-bind:aria-expanded="open ? 'true' : 'false'" aria-controls="mobile-nav">
                <span class="relative block h-4 w-4" aria-hidden="true">
                    <span class="absolute left-0 top-0 h-0.5 w-4 bg-current transition" x-bind:class="open ? 'translate-y-1.5 rotate-45' : ''"></span>
                    <span class="absolute left-0 top-1.5 h-0.5 w-4 bg-current transition" x-bind:class="open ? 'opacity-0' : ''"></span>
                    <span class="absolute left-0 top-3 h-0.5 w-4 bg-current transition" x-bind:class="open ? '-translate-y-1.5 -rotate-45' : ''"></span>
                </span>
            </button>
        </div>
    </div>

    <nav id="mobile-nav" class="border-t lp-border lp-surface lg:!hidden" aria-label="Navigasi utama (mobile)" x-show="open" x-transition x-cloak>
        <div class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-3 text-sm font-bold">
            <a href="#fitur" x-on:click="open = false" class="lp-focus lp-muted rounded-lg px-2 py-3">Fitur</a>
            <a href="#keunggulan" x-on:click="open = false" class="lp-focus lp-muted rounded-lg px-2 py-3">Keunggulan</a>
            <a href="#cara-kerja" x-on:click="open = false" class="lp-focus lp-muted rounded-lg px-2 py-3">Cara kerja</a>
            <a href="#harga" x-on:click="open = false" class="lp-focus lp-muted rounded-lg px-2 py-3">Harga</a>
            <a href="#faq" x-on:click="open = false" class="lp-focus lp-muted rounded-lg px-2 py-3">FAQ</a>
            <a href="{{ route('login') }}" class="lp-focus lp-muted rounded-lg px-2 py-3">Masuk</a>
        </div>
    </nav>
</header>

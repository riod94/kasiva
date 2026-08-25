<style>
    @keyframes kasiva-float { 0%,100% { transform: translateY(0) } 50% { transform: translateY(-10px) } }
    .kasiva-float { animation: kasiva-float 6s ease-in-out infinite; }
    .kasiva-float-slow { animation: kasiva-float 8s ease-in-out infinite; animation-delay: -3s; }
    @media (prefers-reduced-motion: reduce) {
        .kasiva-float, .kasiva-float-slow { animation: none; }
    }
    .kasiva-dotgrid {
        background-image: radial-gradient(rgb(134 150 237 / 0.14) 1px, transparent 1px);
        background-size: 26px 26px;
        mask-image: radial-gradient(ellipse 80% 70% at 50% 30%, black 30%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 80% 70% at 50% 30%, black 30%, transparent 75%);
    }
</style>

<div class="relative flex min-h-screen flex-col overflow-x-clip bg-[#272D48] text-white selection:bg-[#00AAA6]/30 selection:text-[#3EDAD7]">

    {{-- ══════════════ ANNOUNCEMENT BAR ══════════════ --}}
    <div class="border-b border-[#505B93]/60 bg-[#222842]">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-center gap-x-3 gap-y-1 px-4 py-2 text-center text-[11px] font-bold text-slate-300 md:px-8">
            <span class="inline-flex items-center gap-1.5 uppercase tracking-widest text-[#3EDAD7]">
                <x-icon name="sparkles" class="h-3.5 w-3.5" />
                Kasiva v1.0 Telah Rilis
            </span>
            <span class="hidden text-slate-500 sm:inline">•</span>
            <span>Gratis digunakan untuk outlet Anda</span>
            <span class="hidden text-slate-500 sm:inline">•</span>
            <span>Tanpa biaya setup</span>
        </div>
    </div>

    {{-- ══════════════ HEADER ══════════════ --}}
    <header class="sticky top-0 z-50 border-b border-[#505B93]/60 bg-[#272D48]/90 backdrop-blur-xl">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 md:px-8">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5" aria-label="Kasiva POS — beranda">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white p-1 shadow-md">
                    <img src="{{ asset('images/kasiva-logo-icon.png') }}" alt="" class="h-full w-full object-contain">
                </span>
                <span class="text-lg font-black tracking-tight">KASIVA</span>
                <span class="hidden rounded-full border border-[#00AAA6]/50 bg-[#00AAA6]/20 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-widest text-[#3EDAD7] sm:inline-block">Point of Sale</span>
            </a>

            <nav class="hidden items-center gap-7 text-sm font-bold text-slate-300 md:flex" aria-label="Navigasi landing">
                <a href="#fitur" class="transition-colors hover:text-white">Fitur</a>
                <a href="#hpp" class="transition-colors hover:text-white">Keunggulan HPP</a>
                <a href="#cara-mulai" class="transition-colors hover:text-white">Cara Mulai</a>
                <a href="{{ route('about') }}" class="transition-colors hover:text-white">Tentang</a>
            </nav>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('login') }}" class="hidden px-3 py-2 text-sm font-bold text-slate-300 transition-colors hover:text-white sm:block">Masuk</a>
                <a href="{{ route('pos.cashier') }}" class="inline-flex h-11 items-center gap-2 rounded-xl bg-[#00AAA6] px-5 text-sm font-extrabold text-white shadow-lg shadow-[#00AAA6]/25 transition-all hover:scale-105 hover:bg-[#008F8C] active:scale-95">
                    <span>Buka Kasir</span>
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>
        </div>
    </header>

    <main class="flex-1">

        {{-- ══════════════ HERO — split editorial dengan mockup produk ══════════════ --}}
        <section class="relative overflow-hidden">
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute -top-32 right-[-10%] h-[480px] w-[480px] rounded-full bg-[#00AAA6]/15 blur-3xl"></div>
                <div class="absolute bottom-[-20%] left-[-10%] h-[420px] w-[420px] rounded-full bg-[#8696ED]/10 blur-3xl"></div>
                <div class="kasiva-dotgrid absolute inset-0"></div>
            </div>

            <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 pb-20 pt-16 md:px-8 md:pt-24 lg:grid-cols-[1.05fr_0.95fr] lg:gap-16">

                {{-- Kiri: narasi --}}
                <div class="space-y-7 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2.5 rounded-full border border-[#505B93] bg-[#222842] px-4 py-2 shadow-inner">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                        </span>
                        <span class="text-xs font-black uppercase tracking-[0.18em] text-emerald-300">Sistem Kasir Pintar F&amp;B Indonesia</span>
                    </div>

                    <h1 class="text-4xl font-black leading-[1.06] tracking-tighter md:text-6xl xl:text-7xl">
                        Satu Aplikasi Kasir,
                        <span class="bg-gradient-to-r from-[#8696ED] via-[#3EDAD7] to-[#00AAA6] bg-clip-text text-transparent">Kendali Penuh Profit.</span>
                    </h1>

                    <p class="mx-auto max-w-xl text-base font-medium leading-relaxed text-slate-300 md:text-lg lg:mx-0">
                        Kasiva menghitung HPP resep secara otomatis, mengamankan modal restok, tetap berjalan saat internet mati, dan merekam setiap setoran GoFood, GrabFood, maupun ShopeeFood secara akurat.
                    </p>

                    <div class="flex flex-col items-center justify-center gap-3.5 sm:flex-row lg:justify-start">
                        <a href="{{ route('pos.cashier') }}" class="inline-flex h-14 w-full items-center justify-center gap-2 rounded-2xl bg-[#00AAA6] px-8 text-base font-black text-white shadow-xl shadow-[#00AAA6]/30 transition-all hover:scale-[1.02] hover:bg-[#008F8C] active:scale-95 sm:w-auto">
                            <span>Mulai Transaksi Kasir</span>
                            <x-icon name="arrow-right" class="h-5 w-5" />
                        </a>
                        <a href="#cara-mulai" class="inline-flex h-14 w-full items-center justify-center gap-2 rounded-2xl border border-[#505B93] bg-[#222842] px-8 text-base font-black text-white shadow-sm transition-all hover:bg-[#303858] active:scale-95 sm:w-auto">
                            <x-icon name="store" class="h-5 w-5 text-[#3EDAD7]" />
                            <span>Lihat Cara Kerja</span>
                        </a>
                    </div>

                    <ul class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2.5 text-xs font-bold text-slate-300 lg:justify-start">
                        <li class="flex items-center gap-2">
                            <x-icon name="check" class="h-4 w-4 text-emerald-400" />
                            HPP terhitung otomatis
                        </li>
                        <li class="flex items-center gap-2">
                            <x-icon name="check" class="h-4 w-4 text-emerald-400" />
                            Mode offline penuh
                        </li>
                        <li class="flex items-center gap-2">
                            <x-icon name="check" class="h-4 w-4 text-emerald-400" />
                            Struk thermal &amp; WhatsApp
                        </li>
                    </ul>
                </div>

                {{-- Kanan: mockup layar kasir --}}
                <div class="relative mx-auto w-full max-w-lg lg:max-w-none">
                    <div class="relative rounded-[28px] border border-[#505B93] bg-[#222842] p-2 shadow-2xl shadow-black/40">
                        <div class="overflow-hidden rounded-[22px] border border-[#505B93]/70 bg-[#272D48]">
                            {{-- Window chrome --}}
                            <div class="flex items-center justify-between border-b border-[#505B93]/70 bg-[#222842] px-4 py-2.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full bg-rose-400/80"></span>
                                    <span class="h-2.5 w-2.5 rounded-full bg-amber-400/80"></span>
                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-400/80"></span>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kasiva POS — Kasir</span>
                                <span class="inline-flex items-center gap-1 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2 py-0.5 text-[9px] font-black uppercase tracking-wider text-emerald-300">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                    Online
                                </span>
                            </div>

                            <div class="grid grid-cols-[1.4fr_1fr] gap-3 p-3.5">
                                {{-- Grid produk --}}
                                <div class="grid grid-cols-2 content-start gap-2.5">
                                    <div class="rounded-2xl border border-[#505B93]/70 bg-[#303858] p-2.5">
                                        <div class="mb-2 flex h-14 items-center justify-center rounded-xl bg-[#272D48]">
                                            <x-icon name="coffee" class="h-6 w-6 text-[#3EDAD7]" />
                                        </div>
                                        <p class="text-[10px] font-black leading-tight">Kopi Susu Gula Aren</p>
                                        <p class="text-[11px] font-black tabular-nums text-[#3EDAD7]">Rp 18.000</p>
                                    </div>
                                    <div class="rounded-2xl border border-[#505B93]/70 bg-[#303858] p-2.5">
                                        <div class="mb-2 flex h-14 items-center justify-center rounded-xl bg-[#272D48]">
                                            <x-icon name="coffee" class="h-6 w-6 text-[#8696ED]" />
                                        </div>
                                        <p class="text-[10px] font-black leading-tight">Americano Dingin</p>
                                        <p class="text-[11px] font-black tabular-nums text-[#3EDAD7]">Rp 16.000</p>
                                    </div>
                                    <div class="rounded-2xl border border-[#505B93]/70 bg-[#303858] p-2.5">
                                        <div class="mb-2 flex h-14 items-center justify-center rounded-xl bg-[#272D48]">
                                            <x-icon name="tag" class="h-6 w-6 text-[#8696ED]" />
                                        </div>
                                        <p class="text-[10px] font-black leading-tight">Croissant Butter</p>
                                        <p class="text-[11px] font-black tabular-nums text-[#3EDAD7]">Rp 21.000</p>
                                    </div>
                                    <div class="rounded-2xl border border-[#505B93]/70 bg-[#303858] p-2.5">
                                        <div class="mb-2 flex h-14 items-center justify-center rounded-xl bg-[#272D48]">
                                            <x-icon name="shopping-bag" class="h-6 w-6 text-[#3EDAD7]" />
                                        </div>
                                        <p class="text-[10px] font-black leading-tight">Paket Hemat Kombo</p>
                                        <p class="text-[11px] font-black tabular-nums text-[#3EDAD7]">Rp 28.000</p>
                                    </div>
                                </div>

                                {{-- Rail keranjang --}}
                                <div class="flex flex-col gap-2.5 rounded-2xl border border-[#505B93]/70 bg-[#303858] p-3">
                                    <p class="border-b border-[#505B93]/70 pb-2 text-[9px] font-black uppercase tracking-widest text-slate-400">Keranjang</p>
                                    <div class="space-y-1.5 text-[10px] font-bold">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="truncate text-slate-300">Kopi Susu ×2</span>
                                            <span class="shrink-0 tabular-nums text-white">36.000</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="truncate text-slate-300">Croissant ×1</span>
                                            <span class="shrink-0 tabular-nums text-white">21.000</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-2 text-emerald-300">
                                            <span class="truncate">Diskon Bundle</span>
                                            <span class="shrink-0 tabular-nums">−4.000</span>
                                        </div>
                                    </div>
                                    <div class="mt-auto space-y-2 border-t border-[#505B93]/70 pt-2">
                                        <div class="flex items-baseline justify-between">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Total</span>
                                            <span class="text-base font-black tabular-nums text-[#3EDAD7]">Rp 53.000</span>
                                        </div>
                                        <div class="flex items-center justify-between text-[9px] font-bold text-slate-400">
                                            <span>HPP modal</span>
                                            <span class="tabular-nums text-amber-300">Rp 18.400</span>
                                        </div>
                                        <div class="flex items-center justify-between text-[9px] font-bold">
                                            <span class="text-slate-400">Margin</span>
                                            <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-1.5 py-0.5 text-emerald-300">65% Sehat</span>
                                        </div>
                                        <div class="flex h-9 items-center justify-center gap-1.5 rounded-xl bg-[#00AAA6] text-[10px] font-black uppercase tracking-widest text-white shadow-md shadow-[#00AAA6]/30">
                                            <x-icon name="wallet" class="h-3.5 w-3.5" />
                                            Bayar
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Strip metode pembayaran --}}
                            <div class="flex flex-wrap items-center gap-1.5 border-t border-[#505B93]/70 bg-[#222842] px-3.5 py-2.5">
                                <span class="mr-1 text-[9px] font-black uppercase tracking-widest text-slate-500">Pembayaran</span>
                                @foreach(['CASH', 'QRIS', 'SPLIT', 'GOFOOD', 'GRABFOOD', 'SHOPEEFOOD'] as $method)
                                    <span class="rounded-md border border-[#505B93] bg-[#272D48] px-1.5 py-0.5 text-[8px] font-black tracking-wider text-slate-300">{{ $method }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Floating insight chips --}}
                    <div class="kasiva-float absolute -left-3 top-10 hidden items-center gap-2 rounded-2xl border border-[#505B93] bg-[#222842]/95 px-3.5 py-2.5 shadow-xl backdrop-blur md:flex lg:-left-8">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#00AAA6]/20 text-[#3EDAD7]">
                            <x-icon name="trending-up" class="h-4 w-4" />
                        </span>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Laba Bersih Hari Ini</p>
                            <p class="text-sm font-black tabular-nums text-white">Rp 1.240.000</p>
                        </div>
                    </div>
                    <div class="kasiva-float-slow absolute -right-3 bottom-14 hidden items-center gap-2 rounded-2xl border border-[#505B93] bg-[#222842]/95 px-3.5 py-2.5 shadow-xl backdrop-blur md:flex lg:-right-6">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400">
                            <x-icon name="shield" class="h-4 w-4" />
                        </span>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Stok Terkunci</p>
                            <p class="text-sm font-black text-white">Race-condition proof</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ══════════════ STATS STRIP ══════════════ --}}
        <section class="border-y border-[#505B93]/60 bg-[#222842]">
            <dl class="mx-auto grid max-w-7xl grid-cols-2 px-4 text-center md:grid-cols-4 md:divide-x md:divide-[#505B93]/60 md:px-8">
                <div class="px-4 py-8">
                    <dd class="mb-1 text-3xl font-black tabular-nums text-[#3EDAD7] md:text-4xl">7</dd>
                    <dt class="text-[11px] font-black uppercase tracking-widest text-slate-400">Metode Pembayaran</dt>
                </div>
                <div class="px-4 py-8">
                    <dd class="mb-1 text-3xl font-black tabular-nums text-[#3EDAD7] md:text-4xl">3</dd>
                    <dt class="text-[11px] font-black uppercase tracking-widest text-slate-400">Kanal Delivery</dt>
                </div>
                <div class="px-4 py-8">
                    <dd class="mb-1 text-3xl font-black tabular-nums text-[#3EDAD7] md:text-4xl">4</dd>
                    <dt class="text-[11px] font-black uppercase tracking-widest text-slate-400">Tingkat Margin</dt>
                </div>
                <div class="px-4 py-8">
                    <dd class="mb-1 text-3xl font-black tabular-nums text-[#3EDAD7] md:text-4xl">100%</dd>
                    <dt class="text-[11px] font-black uppercase tracking-widest text-slate-400">Siap Offline</dt>
                </div>
            </dl>
        </section>

        {{-- ══════════════ FITUR — 4 pilar dengan bullet ══════════════ --}}
        <section id="fitur" class="py-20 md:py-28">
            <div class="mx-auto max-w-7xl px-4 md:px-8">
                <div class="mx-auto mb-14 max-w-2xl space-y-4 text-center md:mb-16">
                    <span class="inline-block text-xs font-black uppercase tracking-[0.22em] text-[#3EDAD7]">Fitur Lengkap</span>
                    <h2 class="text-3xl font-black tracking-tight md:text-5xl">Semua yang Outlet Anda Butuhkan</h2>
                    <p class="text-sm font-medium leading-relaxed text-slate-300 md:text-base">
                        Dari transaksi kasir tercepat hingga pembukuan laba tiga level — dirancang khusus untuk ritme operasional F&amp;B dan retail Indonesia.
                    </p>
                </div>

                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    @foreach([
                        [
                            'icon' => 'store', 'tint' => 'text-[#3EDAD7] bg-[#00AAA6]/15 border-[#00AAA6]/40',
                            'title' => 'Kasir Lebih Cepat',
                            'desc' => 'Antarmuka sentuh responsif yang dirancang untuk kecepatan jam sibuk.',
                            'points' => ['Multi-cart 3 slot + hold', 'Pencarian menu debounce', 'Multi-varian & opsi tambahan'],
                        ],
                        [
                            'icon' => 'package', 'tint' => 'text-[#8696ED] bg-[#8696ED]/15 border-[#8696ED]/40',
                            'title' => 'Modal Terkendali',
                            'desc' => 'HPP moving average menjaga uang modal restok tetap terpisah dari uang bebas.',
                            'points' => ['Kalkulasi HPP resep otomatis', 'Restok dengan stok terkunci', 'Penyesuaian setoran platform'],
                        ],
                        [
                            'icon' => 'gift', 'tint' => 'text-emerald-400 bg-emerald-500/15 border-emerald-500/40',
                            'title' => 'Pelanggan Kembali',
                            'desc' => 'Program loyalitas yang memberi pelanggan alasan untuk kembali.',
                            'points' => ['Kartu stempel digital', 'Member QR & database', 'Struk digital via WhatsApp'],
                        ],
                        [
                            'icon' => 'chart-bar', 'tint' => 'text-[#3EDAD7] bg-[#3EDAD7]/15 border-[#3EDAD7]/40',
                            'title' => 'Keputusan Berbasis Data',
                            'desc' => 'Laporan keuangan tiga level profit tanpa perlu spreadsheet manual.',
                            'points' => ['Omset → laba kotor → laba bersih', 'Indikator margin 4-tier', 'Export XLSX & PDF'],
                        ],
                    ] as $pillar)
                        <div class="group flex flex-col rounded-3xl border border-[#505B93] bg-[#222842] p-6 shadow-xl transition-all hover:-translate-y-1 hover:border-[#00AAA6]">
                            <span class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl border {{ $pillar['tint'] }}">
                                <x-icon name="{{ $pillar['icon'] }}" class="h-6 w-6" />
                            </span>
                            <h3 class="mb-2 text-lg font-black leading-snug">{{ $pillar['title'] }}</h3>
                            <p class="mb-4 text-xs font-medium leading-relaxed text-slate-300">{{ $pillar['desc'] }}</p>
                            <ul class="mt-auto space-y-2 border-t border-[#505B93]/60 pt-4 text-xs font-bold text-slate-300">
                                @foreach($pillar['points'] as $point)
                                    <li class="flex items-start gap-2">
                                        <x-icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-[#3EDAD7]" />
                                        <span>{{ $point }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ══════════════ KEUNGGULAN HPP & MARGIN ══════════════ --}}
        <section id="hpp" class="border-y border-[#505B93]/60 bg-[#222842] py-20 md:py-28">
            <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 md:px-8 lg:grid-cols-2 lg:gap-16">
                <div class="space-y-6">
                    <span class="inline-block text-xs font-black uppercase tracking-[0.22em] text-[#3EDAD7]">Struktur Finansial Outlet</span>
                    <h2 class="text-3xl font-black leading-tight tracking-tight md:text-5xl">
                        Bedah Setiap Rupiah,
                        <span class="text-[#3EDAD7]">Kendalikan Modal Restok.</span>
                    </h2>
                    <p class="text-sm font-medium leading-relaxed text-slate-300 md:text-base">
                        Kasiva memisahkan setiap penjualan menjadi modal bahan baku (HPP) dan profit murni. Uang modal tidak lagi tercampur dengan uang konsumsi pribadi.
                    </p>

                    <div class="space-y-2.5">
                        @foreach([
                            ['label' => 'Kritis', 'range' => '&lt; 30% Margin', 'note' => 'Biaya modal bahan baku terlalu tinggi', 'tone' => 'border-red-500/30 bg-red-500/20 text-red-400'],
                            ['label' => 'Tipis', 'range' => '30–44% Margin', 'note' => 'Batas aman minimal operasional', 'tone' => 'border-amber-500/30 bg-amber-500/20 text-amber-400'],
                            ['label' => 'Sehat', 'range' => '45–71% Margin', 'note' => 'Struktur HPP ideal untuk ekspansi', 'tone' => 'border-emerald-500/30 bg-emerald-500/20 text-emerald-400'],
                            ['label' => 'Optimal', 'range' => '≥ 72% Margin', 'note' => 'Profitabilitas sangat tinggi', 'tone' => 'border-[#8696ED]/30 bg-[#8696ED]/20 text-[#8696ED]'],
                        ] as $tier)
                            <div class="flex items-center gap-3.5 rounded-2xl border {{ $tier['tone'] }} p-3.5 pl-4">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $tier['tone'] }} font-black">{{ strtoupper(substr($tier['label'], 0, 1)) }}</span>
                                <div>
                                    <p class="text-xs font-black text-white">{{ $tier['label'] }} ({{ $tier['range'] }})</p>
                                    <p class="text-[11px] font-medium text-slate-400">{{ $tier['note'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Kartu simulasi pembukuan --}}
                <div class="space-y-4 rounded-[28px] border border-[#505B93] bg-[#272D48] p-6 shadow-2xl md:p-8">
                    <div class="flex items-center justify-between border-b border-[#505B93]/70 pb-4">
                        <h3 class="flex items-center gap-2 text-sm font-black">
                            <x-icon name="chart-bar" class="h-4 w-4 text-[#8696ED]" />
                            Simulasi Pembukuan Finansial
                        </h3>
                        <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-emerald-300">Sehat</span>
                    </div>

                    <dl class="space-y-3 text-xs font-bold">
                        <div class="flex items-center justify-between border-b border-[#505B93]/50 pb-3">
                            <dt class="text-slate-400">Total Omset Penjualan</dt>
                            <dd class="font-black tabular-nums text-white">Rp 2.450.000</dd>
                        </div>
                        <div class="flex items-center justify-between border-b border-[#505B93]/50 pb-3">
                            <dt class="text-slate-400">Alokasi Modal HPP (wajib putar)</dt>
                            <dd class="font-black tabular-nums text-amber-400">Rp 890.000</dd>
                        </div>
                        <div class="flex items-center justify-between border-b border-[#505B93]/50 pb-3">
                            <dt class="text-slate-400">Laba Kotor (Gross Profit)</dt>
                            <dd class="font-black tabular-nums text-emerald-400">Rp 1.560.000</dd>
                        </div>
                        <div class="flex items-center justify-between border-b border-[#505B93]/50 pb-3">
                            <dt class="text-slate-400">Beban Operasional Toko</dt>
                            <dd class="font-black tabular-nums text-rose-400">Rp 320.000</dd>
                        </div>
                        <div class="flex items-center justify-between pt-1 text-sm font-black">
                            <dt class="text-white">Profit Murni (Uang Bebas)</dt>
                            <dd class="tabular-nums text-[#3EDAD7]">Rp 1.240.000</dd>
                        </div>
                    </dl>

                    {{-- Bar proporsi visual --}}
                    <div class="space-y-2">
                        <div class="flex h-3.5 w-full overflow-hidden rounded-full border border-[#505B93]/70" role="img" aria-label="Proporsi omset: 36 persen HPP modal, 64 persen laba kotor">
                            <div class="h-full bg-amber-400/80" style="width:36%"></div>
                            <div class="h-full bg-[#00AAA6]" style="width:64%"></div>
                        </div>
                        <p class="text-center text-[11px] font-bold text-slate-400">23 Transaksi Tercatat • Margin Rata-rata: 63.7%</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ══════════════ CARA MULAI — timeline 3 langkah ══════════════ --}}
        <section id="cara-mulai" class="py-20 md:py-28">
            <div class="mx-auto max-w-7xl px-4 md:px-8">
                <div class="mx-auto mb-14 max-w-2xl space-y-4 text-center md:mb-16">
                    <span class="inline-block text-xs font-black uppercase tracking-[0.22em] text-[#3EDAD7]">Cara Mulai</span>
                    <h2 class="text-3xl font-black tracking-tight md:text-5xl">Tiga Langkah, Langsung Jualan</h2>
                    <p class="text-sm font-medium leading-relaxed text-slate-300 md:text-base">
                        Tanpa instalasi rumit — outlet Anda bisa menerima transaksi pertama dalam hitungan menit.
                    </p>
                </div>

                <ol class="relative grid gap-10 md:grid-cols-3 md:gap-6">
                    {{-- Garis koneksi desktop --}}
                    <div class="absolute left-0 right-0 top-7 hidden h-px bg-gradient-to-r from-transparent via-[#505B93] to-transparent md:block" aria-hidden="true"></div>

                    @foreach([
                        ['step' => '01', 'icon' => 'settings', 'title' => 'Daftar & Setup Outlet', 'desc' => 'Buat akun, isi nama outlet, dan atur peran staf beserta permission masing-masing.'],
                        ['step' => '02', 'icon' => 'package', 'title' => 'Input Produk & Resep', 'desc' => 'Masukkan menu, bahan baku, dan resep — Kasiva otomatis menghitung HPP per porsi.'],
                        ['step' => '03', 'icon' => 'wallet', 'title' => 'Mulai Terima Order', 'desc' => 'Transaksi tunai, QRIS, split, hingga pesanan platform delivery tercatat presisi.'],
                    ] as $index => $item)
                        <li class="relative flex flex-col items-center space-y-4 text-center">
                            <div class="relative z-10 flex h-14 w-14 items-center justify-center rounded-2xl border border-[#00AAA6]/50 bg-[#222842] shadow-lg shadow-[#00AAA6]/10">
                                <x-icon name="{{ $item['icon'] }}" class="h-6 w-6 text-[#3EDAD7]" />
                            </div>
                            <div class="space-y-2">
                                <span class="text-[11px] font-black uppercase tracking-[0.25em] text-[#8696ED]">Langkah {{ $item['step'] }}</span>
                                <h3 class="text-lg font-black">{{ $item['title'] }}</h3>
                                <p class="mx-auto max-w-xs text-xs font-medium leading-relaxed text-slate-300">{{ $item['desc'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>

                <div class="mt-12 text-center">
                    <a href="{{ route('register') }}" class="inline-flex h-13 items-center gap-2 rounded-2xl bg-[#00AAA6] px-8 py-3.5 text-sm font-black text-white shadow-xl shadow-[#00AAA6]/30 transition-all hover:scale-105 hover:bg-[#008F8C] active:scale-95">
                        <span>Daftar Gratis Sekarang</span>
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                    <p class="mt-3 text-[11px] font-bold text-slate-400">Tanpa biaya setup • Data transaksi tersimpan lokal di perangkat Anda</p>
                </div>
            </div>
        </section>

        {{-- ══════════════ JENIS USAHA ══════════════ --}}
        <section class="border-y border-[#505B93]/60 bg-[#222842] py-20 md:py-24">
            <div class="mx-auto max-w-7xl px-4 md:px-8">
                <div class="mx-auto mb-12 max-w-2xl space-y-4 text-center md:mb-14">
                    <span class="inline-block text-xs font-black uppercase tracking-[0.22em] text-[#3EDAD7]">Dibuat Untuk</span>
                    <h2 class="text-3xl font-black tracking-tight md:text-4xl">Familiar di Segala Jenis Usaha F&amp;B</h2>
                </div>

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach([
                        ['icon' => 'coffee', 'label' => 'Kedai Kopi'],
                        ['icon' => 'store', 'label' => 'Warung Makan'],
                        ['icon' => 'tag', 'label' => 'Bakery'],
                        ['icon' => 'shopping-bag', 'label' => 'Retail'],
                        ['icon' => 'bike', 'label' => 'Dark Kitchen'],
                        ['icon' => 'gift', 'label' => 'Jajanan & Snack'],
                    ] as $business)
                        <div class="group flex flex-col items-center gap-3 rounded-3xl border border-[#505B93] bg-[#272D48] p-6 text-center transition-all hover:-translate-y-1 hover:border-[#00AAA6]">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#00AAA6]/15 text-[#3EDAD7] transition-transform group-hover:scale-110">
                                <x-icon name="{{ $business['icon'] }}" class="h-6 w-6" />
                            </span>
                            <span class="text-sm font-black text-slate-200">{{ $business['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ══════════════ CTA AKHIR ══════════════ --}}
        <section class="relative overflow-hidden py-20 md:py-28">
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute left-1/2 top-1/2 h-[420px] w-[720px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#00AAA6]/15 blur-3xl"></div>
            </div>

            <div class="relative mx-auto max-w-3xl space-y-6 px-4 text-center md:px-8">
                <h2 class="text-3xl font-black tracking-tight md:text-5xl">
                    Mulai Kelola Profit
                    <span class="bg-gradient-to-r from-[#3EDAD7] to-[#00AAA6] bg-clip-text text-transparent">Outlet Anda Hari Ini.</span>
                </h2>
                <p class="mx-auto max-w-xl text-sm font-medium leading-relaxed text-slate-300 md:text-base">
                    Satu aplikasi untuk kasir, stok, loyalitas, dan laporan laba — dengan transparansi penuh atas setiap rupiah yang masuk dan keluar.
                </p>
                <div class="flex flex-col items-center justify-center gap-3.5 pt-2 sm:flex-row">
                    <a href="{{ route('pos.cashier') }}" class="inline-flex h-14 items-center gap-2 rounded-2xl bg-[#00AAA6] px-8 text-base font-black text-white shadow-xl shadow-[#00AAA6]/30 transition-all hover:scale-105 hover:bg-[#008F8C] active:scale-95">
                        <span>Buka Layar Kasir POS</span>
                        <x-icon name="arrow-right" class="h-5 w-5" />
                    </a>
                    <a href="{{ route('onboarding') }}" class="inline-flex h-14 items-center gap-2 rounded-2xl border border-[#505B93] bg-[#222842] px-8 text-base font-black text-white shadow-sm transition-all hover:bg-[#303858] active:scale-95">
                        <x-icon name="store" class="h-5 w-5 text-[#3EDAD7]" />
                        <span>Panduan Mobile App</span>
                    </a>
                </div>
                <p class="text-[11px] font-bold text-slate-400">Gratis • Tanpa biaya setup • Mendukung operasional offline</p>
            </div>
        </section>
    </main>

    {{-- ══════════════ FOOTER ══════════════ --}}
    <footer class="border-t border-[#505B93]/60 bg-[#222842] px-6 py-10 text-xs text-slate-400">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-6 md:flex-row">
            <div class="space-y-2 text-center md:text-left">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white p-1 shadow-md">
                    <img src="{{ asset('images/kasiva-logo-icon.png') }}" alt="Kasiva POS" class="h-full w-full object-contain">
                </span>
                <p class="text-[11px] font-bold text-slate-300">Sistem Kasir Pintar &amp; Manajemen Inventaris F&amp;B</p>
            </div>

            <nav class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 font-bold text-slate-300" aria-label="Navigasi footer">
                <a href="#fitur" class="transition-colors hover:text-white">Fitur</a>
                <a href="{{ route('pos.cashier') }}" class="transition-colors hover:text-white">Kasir</a>
                <a href="{{ route('about') }}" class="transition-colors hover:text-white">Tentang Kami</a>
                <a href="{{ route('privacy') }}" class="transition-colors hover:text-white">Kebijakan Privasi</a>
                <a href="{{ route('terms') }}" class="transition-colors hover:text-white">Syarat &amp; Ketentuan</a>
            </nav>

            <p class="text-[11px] font-bold text-slate-500">© 2026 Kasiva POS. Hak Cipta Dilindungi.</p>
        </div>
    </footer>
</div>

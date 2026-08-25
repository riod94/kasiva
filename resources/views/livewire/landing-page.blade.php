@php
    $schemaContext = '@'.'context';
    $features = [
        ['icon' => 'store', 'title' => 'Kasir secepat antrean Anda', 'desc' => 'Multi-cart, pencarian instan, varian produk, dan checkout sentuh yang tetap responsif saat jam sibuk.', 'accent' => 'teal'],
        ['icon' => 'package', 'title' => 'HPP resep yang selalu akurat', 'desc' => 'Harga bahan, moving average, dan pemakaian resep dihitung otomatis untuk setiap produk terjual.', 'accent' => 'violet'],
        ['icon' => 'chart-bar', 'title' => 'Profit yang benar-benar terlihat', 'desc' => 'Pisahkan omzet, modal restok, laba kotor, beban, dan profit bersih tanpa spreadsheet manual.', 'accent' => 'cyan'],
        ['icon' => 'gift', 'title' => 'Pelanggan punya alasan kembali', 'desc' => 'Member QR, stempel digital, reward, promo, dan struk WhatsApp dalam satu alur transaksi.', 'accent' => 'violet'],
        ['icon' => 'shield', 'title' => 'Kontrol staf tanpa celah', 'desc' => 'Peran Owner, Manager, dan Kasir dilindungi permission untuk setiap area operasional penting.', 'accent' => 'teal'],
        ['icon' => 'wifi-off', 'title' => 'Internet mati, jualan tetap jalan', 'desc' => 'Mode offline-first menyimpan operasi lokal dan menyinkronkannya kembali saat koneksi tersedia.', 'accent' => 'cyan'],
    ];
    $faqs = [
        ['q' => 'Apakah Kasiva bisa dipakai tanpa internet?', 'a' => 'Bisa. Alur kasir utama dirancang offline-first. Data penting disimpan pada perangkat dan antrean perubahan dapat disinkronkan kembali ketika internet tersedia.'],
        ['q' => 'Bagaimana Kasiva menghitung HPP produk?', 'a' => 'Kasiva menghubungkan produk dengan resep dan bahan baku. Harga rata-rata bahan diperbarui saat restok, lalu pemakaian resep dikurangi otomatis ketika checkout berhasil.'],
        ['q' => 'Apakah pembayaran QRIS dan platform delivery didukung?', 'a' => 'Ya. Kasiva mendukung tunai, QRIS, split payment, serta pencatatan GoFood, GrabFood, dan ShopeeFood berikut penyesuaian markup atau diskonnya.'],
        ['q' => 'Apakah staf dapat dibatasi aksesnya?', 'a' => 'Ya. Sistem role dan permission membatasi menu serta tindakan sensitif sesuai tanggung jawab Owner, Manager, atau Kasir.'],
        ['q' => 'Bisakah saya mengirim struk digital?', 'a' => 'Bisa. Transaksi dapat dibuatkan struk untuk printer thermal maupun dibagikan secara digital melalui WhatsApp.'],
    ];
@endphp

<script type="application/ld+json">{!! json_encode([
    $schemaContext => 'https://schema.org',
    '@type' => 'SoftwareApplication',
    'name' => 'Kasiva POS',
    'applicationCategory' => 'BusinessApplication',
    'operatingSystem' => 'Web, Windows, macOS',
    'url' => route('landing'),
    'image' => asset('images/kasiva-logo-full.png'),
    'description' => 'Aplikasi kasir offline-first untuk F&B dan retail Indonesia dengan HPP resep, stok bahan, loyalitas, pembayaran, dan laporan profit.',
    'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IDR', 'availability' => 'https://schema.org/InStock'],
    'featureList' => array_column($features, 'title'),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode([
    $schemaContext => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(fn ($faq) => [
        '@type' => 'Question',
        'name' => $faq['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
    ], $faqs),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

<style>
    html { scroll-behavior: smooth; scroll-padding-top: 5.5rem; }
    .landing-shell {
        --lp-bg: #f7f9fc; --lp-surface: #ffffff; --lp-surface-2: #eef2f8; --lp-text: #172036;
        --lp-muted: #64708a; --lp-border: #dce3ef; --lp-header: rgb(247 249 252 / .84);
        background: var(--lp-bg); color: var(--lp-text);
    }
    html.dark .landing-shell {
        --lp-bg: #171c31; --lp-surface: #222842; --lp-surface-2: #272d48; --lp-text: #f7f9ff;
        --lp-muted: #aeb9d1; --lp-border: #3e496f; --lp-header: rgb(23 28 49 / .82);
    }
    .lp-surface { background: var(--lp-surface); }
    .lp-surface-2 { background: var(--lp-surface-2); }
    .lp-muted { color: var(--lp-muted); }
    .lp-border { border-color: var(--lp-border); }
    .lp-header { background: var(--lp-header); }
    .lp-grid { background-image: radial-gradient(rgb(80 91 147 / .17) 1px, transparent 1px); background-size: 24px 24px; }
    .lp-gradient-text { background: linear-gradient(100deg, #505b93 0%, #00aaa6 53%, #008f8c 100%); -webkit-background-clip: text; color: transparent; }
    html.dark .lp-gradient-text { background: linear-gradient(100deg, #a9b4ff 0%, #3edad7 53%, #00aaa6 100%); -webkit-background-clip: text; color: transparent; }
    .lp-card { background: var(--lp-surface); border: 1px solid var(--lp-border); box-shadow: 0 18px 55px rgb(39 45 72 / .08); }
    html.dark .lp-card { box-shadow: 0 18px 60px rgb(0 0 0 / .22); }
    .lp-focus:focus-visible { outline: 3px solid #3edad7; outline-offset: 3px; }
    .landing-shell .lp-always-dark { color: #fff; }
    html.light .landing-shell .lp-always-dark h2,
    html.light .landing-shell .lp-always-dark .text-white { color: #fff !important; }
    html.light .landing-shell .lp-always-dark .text-slate-300 { color: #cbd5e1 !important; }
    html.light .landing-shell .lp-always-dark .text-slate-400 { color: #94a3b8 !important; }
    footer nav a, details summary { min-height: 44px; }
    footer nav a { display: inline-flex; align-items: center; padding-inline: 0.5rem; margin-inline: -0.5rem; }
    .lp-float { animation: lp-float 6s ease-in-out infinite; }
    @keyframes lp-float { 50% { transform: translateY(-9px); } }
    @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } .lp-float { animation: none; } }
</style>

<div class="landing-shell min-h-screen overflow-x-clip selection:bg-[#00AAA6]/25">
    <a href="#konten-utama" class="lp-focus fixed left-4 top-4 z-[100] -translate-y-24 rounded-xl bg-[#00AAA6] px-4 py-3 text-sm font-extrabold text-white transition-transform focus:translate-y-0">Lewati ke konten utama</a>

    <div class="border-b lp-border lp-surface-2">
        <div class="mx-auto flex min-h-10 max-w-7xl items-center justify-center gap-2 px-4 py-2 text-center text-[11px] font-bold sm:text-xs">
            <span class="inline-flex items-center gap-1.5 text-[#008F8C] dark:text-[#3EDAD7]"><x-icon name="sparkles" class="h-3.5 w-3.5" /> Kasiva v1.0 tersedia</span>
            <span class="lp-muted">• Tanpa biaya setup • Siap offline</span>
        </div>
    </div>

    <header class="lp-header sticky top-0 z-50 border-b lp-border backdrop-blur-xl" x-data="{ open: false }">
        <div class="mx-auto flex h-[72px] max-w-7xl items-center justify-between gap-4 px-4 md:px-8">
            <a href="{{ route('landing') }}" class="lp-focus flex min-h-11 items-center gap-2.5 rounded-xl" aria-label="Kasiva POS, kembali ke beranda">
                <img src="{{ asset('images/kasiva-logo-icon-128.png') }}" alt="" width="40" height="40" class="h-10 w-10 rounded-xl bg-white p-1 shadow-sm">
                <span class="text-xl font-black tracking-tight">Kasiva</span>
            </a>
            <nav class="hidden items-center gap-7 text-sm font-bold lg:flex" aria-label="Navigasi utama">
                <a href="#fitur" class="lp-focus lp-muted rounded-lg transition hover:text-[#00AAA6]">Fitur</a>
                <a href="#keunggulan" class="lp-focus lp-muted rounded-lg transition hover:text-[#00AAA6]">Keunggulan</a>
                <a href="#harga" class="lp-focus lp-muted rounded-lg transition hover:text-[#00AAA6]">Harga</a>
                <a href="#faq" class="lp-focus lp-muted rounded-lg transition hover:text-[#00AAA6]">FAQ</a>
            </nav>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.toggleKasivaTheme()" class="lp-focus flex h-11 w-11 items-center justify-center rounded-xl border lp-border lp-surface transition hover:border-[#00AAA6]" aria-label="Ganti tema terang atau gelap" title="Ganti tema">
                    <x-icon name="sun" class="hidden h-5 w-5 dark:block" /><x-icon name="moon" class="h-5 w-5 dark:hidden" />
                </button>
                <a href="{{ route('login') }}" class="lp-focus hidden min-h-11 items-center rounded-xl px-3 text-sm font-bold sm:flex">Masuk</a>
                <a href="{{ route('register') }}" class="lp-focus inline-flex min-h-11 items-center gap-2 rounded-xl bg-[#00AAA6] px-4 text-sm font-extrabold text-white shadow-lg shadow-[#00AAA6]/20 transition hover:bg-[#008F8C]">Mulai gratis <x-icon name="arrow-right" class="h-4 w-4" /></a>
            </div>
        </div>
    </header>

    <main id="konten-utama">
        <section class="relative isolate overflow-hidden pb-20 pt-16 md:pb-28 md:pt-24">
            <div class="lp-grid pointer-events-none absolute inset-0 -z-20 opacity-70 [mask-image:linear-gradient(to_bottom,black,transparent_88%)]"></div>
            <div class="pointer-events-none absolute -right-40 -top-48 -z-10 h-[520px] w-[520px] rounded-full bg-[#00AAA6]/15 blur-3xl"></div>
            <div class="pointer-events-none absolute -left-48 bottom-0 -z-10 h-96 w-96 rounded-full bg-[#8696ED]/15 blur-3xl"></div>
            <div class="mx-auto grid max-w-7xl items-center gap-14 px-4 md:px-8 lg:grid-cols-[.9fr_1.1fr]">
                <div class="text-center lg:text-left">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border lp-border lp-surface px-3.5 py-2 text-xs font-extrabold shadow-sm">
                        <span class="relative flex h-2 w-2"><span class="absolute h-full w-full animate-ping rounded-full bg-[#00AAA6] opacity-60"></span><span class="relative h-2 w-2 rounded-full bg-[#00AAA6]"></span></span>
                        Dibangun untuk F&amp;B dan retail Indonesia
                    </div>
                    <h1 class="text-4xl font-black leading-[1.04] tracking-[-.045em] sm:text-5xl md:text-6xl xl:text-[68px]">
                        Kasir bukan cuma mencatat. <span class="lp-gradient-text">Kasiva menjaga profit.</span>
                    </h1>
                    <p class="lp-muted mx-auto mt-6 max-w-2xl text-base font-medium leading-8 md:text-lg lg:mx-0">
                        Kelola transaksi, resep, stok, loyalitas, dan laporan dalam satu POS yang menghitung HPP otomatis—bahkan ketika internet sedang tidak bersahabat.
                    </p>
                    <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row lg:justify-start">
                        <a href="{{ route('register') }}" class="lp-focus inline-flex min-h-14 items-center justify-center gap-2 rounded-2xl bg-[#00AAA6] px-7 text-base font-extrabold text-white shadow-xl shadow-[#00AAA6]/25 transition hover:-translate-y-0.5 hover:bg-[#008F8C]">Coba Kasiva gratis <x-icon name="arrow-right" class="h-5 w-5" /></a>
                        <a href="#cara-kerja" class="lp-focus inline-flex min-h-14 items-center justify-center gap-2 rounded-2xl border lp-border lp-surface px-7 text-base font-extrabold transition hover:border-[#00AAA6]"><x-icon name="sparkles" class="h-5 w-5 text-[#00AAA6]" /> Lihat cara kerja</a>
                    </div>
                    <ul class="lp-muted mt-7 flex flex-wrap justify-center gap-x-5 gap-y-2 text-xs font-bold lg:justify-start" aria-label="Keuntungan utama">
                        @foreach(['Tanpa kartu kredit', 'Setup dalam hitungan menit', 'Data milik outlet Anda'] as $benefit)
                            <li class="flex items-center gap-1.5"><x-icon name="check" class="h-4 w-4 text-[#00AAA6]" />{{ $benefit }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="relative mx-auto w-full max-w-2xl" aria-label="Pratinjau dashboard Kasiva POS">
                    <div class="rounded-[28px] border lp-border bg-[#171C31] p-2 shadow-2xl shadow-[#272D48]/30">
                        <div class="overflow-hidden rounded-[21px] bg-[#272D48] text-white">
                            <div class="flex h-11 items-center justify-between border-b border-[#505B93]/70 bg-[#222842] px-4">
                                <div class="flex gap-1.5" aria-hidden="true"><span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span></div>
                                <span class="text-[10px] font-extrabold uppercase tracking-[.18em] text-slate-400">Kasiva • Dashboard Hari Ini</span>
                                <span class="rounded-full bg-emerald-400/15 px-2 py-1 text-[9px] font-black text-emerald-300">● ONLINE</span>
                            </div>
                            <div class="grid gap-3 p-4 sm:grid-cols-[1.3fr_.7fr]">
                                <div>
                                    <div class="grid grid-cols-3 gap-2">
                                        @foreach([['Omzet','Rp2,45jt','trending-up'],['Profit','Rp1,24jt','wallet'],['Transaksi','23','receipt']] as $metric)
                                            <div class="rounded-xl border border-[#505B93]/70 bg-[#303858] p-3"><x-icon name="{{ $metric[2] }}" class="mb-3 h-4 w-4 text-[#3EDAD7]" /><p class="text-[9px] font-bold uppercase text-slate-400">{{ $metric[0] }}</p><p class="mt-1 text-sm font-black tabular-nums">{{ $metric[1] }}</p></div>
                                        @endforeach
                                    </div>
                                    <div class="mt-3 rounded-2xl border border-[#505B93]/70 bg-[#303858] p-4">
                                        <div class="flex items-center justify-between"><div><p class="text-[10px] font-bold text-slate-400">Tren penjualan</p><p class="mt-1 text-xl font-black">+18,4%</p></div><span class="rounded-full bg-[#00AAA6]/15 px-2 py-1 text-[9px] font-black text-[#3EDAD7]">7 HARI</span></div>
                                        <div class="mt-5 flex h-24 items-end gap-2" aria-hidden="true">@foreach([38,52,44,68,59,81,94] as $bar)<span class="flex-1 rounded-t-md bg-gradient-to-t from-[#00AAA6] to-[#3EDAD7]" style="height:{{ $bar }}%"></span>@endforeach</div>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-[#505B93]/70 bg-[#303858] p-4">
                                    <div class="flex items-center justify-between"><p class="text-[10px] font-bold uppercase text-slate-400">Komposisi omzet</p><x-icon name="chart-bar" class="h-4 w-4 text-[#8696ED]" /></div>
                                    <div class="mx-auto my-5 flex h-28 w-28 items-center justify-center rounded-full" style="background:conic-gradient(#00AAA6 0 64%,#8696ED 64% 82%,#fbbf24 82% 100%)"><div class="flex h-20 w-20 flex-col items-center justify-center rounded-full bg-[#303858]"><span class="text-[9px] text-slate-400">Margin</span><strong class="text-lg">64%</strong></div></div>
                                    <div class="space-y-2 text-[10px] font-bold"><p class="flex justify-between"><span class="text-slate-400">Laba kotor</span><span class="text-[#3EDAD7]">64%</span></p><p class="flex justify-between"><span class="text-slate-400">Operasional</span><span class="text-[#a9b4ff]">18%</span></p><p class="flex justify-between"><span class="text-slate-400">HPP modal</span><span class="text-amber-300">18%</span></p></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lp-card lp-float absolute -bottom-6 -left-2 hidden items-center gap-3 rounded-2xl p-3 sm:flex lg:-left-8"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#00AAA6]/15 text-[#00AAA6]"><x-icon name="refresh" class="h-5 w-5" /></span><div><p class="text-[10px] font-bold uppercase lp-muted">Koneksi terputus?</p><p class="text-xs font-black">Transaksi tetap aman</p></div></div>
                </div>
            </div>
        </section>

        <section class="border-y lp-border lp-surface" aria-label="Ringkasan kemampuan Kasiva">
            <dl class="mx-auto grid max-w-7xl grid-cols-2 px-4 md:grid-cols-4 md:px-8">
                @foreach([['7','Metode pembayaran'],['3','Kanal delivery'],['3','Level akses staf'],['100%','Siap offline']] as $stat)
                    <div class="border-b lp-border px-3 py-7 text-center even:border-l md:border-b-0 md:border-l"><dd class="text-2xl font-black text-[#00AAA6] md:text-3xl">{{ $stat[0] }}</dd><dt class="lp-muted mt-1 text-[10px] font-extrabold uppercase tracking-widest">{{ $stat[1] }}</dt></div>
                @endforeach
            </dl>
        </section>

        <section id="fitur" class="py-20 md:py-28">
            <div class="mx-auto max-w-7xl px-4 md:px-8">
                <div class="mx-auto mb-14 max-w-3xl text-center"><p class="text-xs font-black uppercase tracking-[.22em] text-[#00AAA6]">Satu sistem, operasional utuh</p><h2 class="mt-4 text-3xl font-black tracking-tight md:text-5xl">Detail kecil yang membuat outlet <span class="lp-gradient-text">bekerja lebih tenang.</span></h2><p class="lp-muted mx-auto mt-5 max-w-2xl leading-7">Setiap fitur Kasiva dirancang untuk mengurangi kerja berulang, mencegah selisih, dan memberi pemilik angka yang bisa dipercaya.</p></div>
                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($features as $feature)
                        <article class="lp-card group rounded-3xl p-6 transition duration-300 hover:-translate-y-1 hover:border-[#00AAA6]">
                            <span class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl {{ $feature['accent'] === 'teal' ? 'bg-[#00AAA6]/15 text-[#00AAA6]' : ($feature['accent'] === 'violet' ? 'bg-[#8696ED]/15 text-[#8696ED]' : 'bg-[#3EDAD7]/15 text-[#008F8C] dark:text-[#3EDAD7]') }}"><x-icon name="{{ $feature['icon'] }}" class="h-6 w-6" /></span>
                            <h3 class="text-lg font-black">{{ $feature['title'] }}</h3><p class="lp-muted mt-3 text-sm font-medium leading-6">{{ $feature['desc'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="keunggulan" class="border-y lp-border lp-surface py-20 md:py-28">
            <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 md:px-8 lg:grid-cols-2 lg:gap-20">
                <div><p class="text-xs font-black uppercase tracking-[.22em] text-[#00AAA6]">Keunggulan finansial</p><h2 class="mt-4 text-3xl font-black leading-tight md:text-5xl">Tahu mana omzet. Tahu mana <span class="lp-gradient-text">uang yang boleh dipakai.</span></h2><p class="lp-muted mt-5 leading-7">Kasiva membedah transaksi hingga tingkat resep. Modal restok tidak lagi terlihat seperti profit, sehingga keputusan harga dan belanja lebih rasional.</p>
                    <ul class="mt-7 space-y-4">@foreach(['HPP bergerak mengikuti harga restok terbaru','Stok bahan berkurang otomatis dari resep','Margin ditandai kritis, tipis, sehat, atau optimal','Biaya operasional dipisahkan dari laba kotor'] as $point)<li class="flex gap-3 text-sm font-bold"><span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#00AAA6]/15 text-[#00AAA6]"><x-icon name="check" class="h-3.5 w-3.5" /></span>{{ $point }}</li>@endforeach</ul>
                </div>
                <div class="lp-card rounded-[30px] p-6 md:p-8">
                    <div class="flex items-center justify-between border-b lp-border pb-5"><div><p class="lp-muted text-[10px] font-extrabold uppercase tracking-widest">Ringkasan hari ini</p><h3 class="mt-1 text-lg font-black">Aliran uang outlet</h3></div><span class="rounded-full bg-emerald-500/15 px-3 py-1.5 text-[10px] font-black text-emerald-600 dark:text-emerald-300">MARGIN SEHAT</span></div>
                    <dl class="mt-5 space-y-4">@foreach([['Omzet','Rp 2.450.000',''],['Modal restok (HPP)','Rp 890.000','text-amber-500'],['Laba kotor','Rp 1.560.000','text-[#00AAA6]'],['Beban operasional','Rp 320.000','text-rose-500']] as $row)<div class="flex items-center justify-between gap-4 text-sm"><dt class="lp-muted font-medium">{{ $row[0] }}</dt><dd class="font-black tabular-nums {{ $row[2] }}">{{ $row[1] }}</dd></div>@endforeach</dl>
                    <div class="mt-6 rounded-2xl bg-[#272D48] p-5 text-white"><p class="text-xs font-bold text-slate-400">Profit bersih yang dapat digunakan</p><div class="mt-2 flex items-end justify-between gap-4"><strong class="text-2xl font-black text-[#3EDAD7] md:text-3xl">Rp 1.240.000</strong><span class="text-xs font-bold text-emerald-300">↑ 18,4%</span></div><div class="mt-4 flex h-2 overflow-hidden rounded-full"><span class="bg-amber-400" style="width:36%"></span><span class="bg-[#00AAA6]" style="width:64%"></span></div></div>
                </div>
            </div>
        </section>

        <section id="cara-kerja" class="py-20 md:py-28">
            <div class="mx-auto max-w-7xl px-4 md:px-8"><div class="mx-auto mb-14 max-w-2xl text-center"><p class="text-xs font-black uppercase tracking-[.22em] text-[#00AAA6]">Mulai tanpa kerumitan</p><h2 class="mt-4 text-3xl font-black md:text-5xl">Dari daftar hingga transaksi pertama.</h2></div>
                <ol class="grid gap-5 md:grid-cols-3">@foreach([['01','store','Buat outlet','Daftar, isi profil outlet, dan undang staf sesuai perannya.'],['02','package','Susun menu & resep','Masukkan produk, bahan, harga beli, varian, dan resep per porsi.'],['03','wallet','Mulai berjualan','Terima pembayaran dan lihat stok serta profit berubah secara otomatis.']] as $step)<li class="lp-card relative rounded-3xl p-6"><span class="absolute right-6 top-5 text-4xl font-black text-[#8696ED]/25">{{ $step[0] }}</span><span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#00AAA6]/15 text-[#00AAA6]"><x-icon name="{{ $step[1] }}" class="h-6 w-6" /></span><h3 class="mt-6 text-lg font-black">{{ $step[2] }}</h3><p class="lp-muted mt-2 text-sm leading-6">{{ $step[3] }}</p></li>@endforeach</ol>
            </div>
        </section>

        <section id="harga" class="border-y lp-border lp-surface py-20 md:py-28">
            <div class="mx-auto max-w-5xl px-4 md:px-8"><div class="mx-auto mb-12 max-w-2xl text-center"><p class="text-xs font-black uppercase tracking-[.22em] text-[#00AAA6]">Harga transparan</p><h2 class="mt-4 text-3xl font-black md:text-5xl">Mulai tanpa beban biaya.</h2><p class="lp-muted mt-4 leading-7">Versi awal Kasiva tersedia gratis untuk membantu outlet membangun operasional yang lebih sehat.</p></div>
                <div class="lp-card mx-auto grid max-w-4xl overflow-hidden rounded-[32px] md:grid-cols-[.8fr_1.2fr]">
                    <div class="bg-[#272D48] p-7 text-white md:p-9"><span class="rounded-full bg-[#00AAA6]/20 px-3 py-1 text-[10px] font-black tracking-widest text-[#3EDAD7]">EARLY ACCESS</span><p class="mt-6 text-sm font-bold text-slate-300">Kasiva Starter</p><p class="mt-2 text-4xl font-black">Rp 0</p><p class="mt-1 text-xs text-slate-400">untuk versi awal</p><a href="{{ route('register') }}" class="lp-focus mt-7 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#00AAA6] px-5 text-sm font-black text-white hover:bg-[#008F8C]">Mulai sekarang <x-icon name="arrow-right" class="h-4 w-4" /></a></div>
                    <div class="p-7 md:p-9"><h3 class="text-lg font-black">Yang Anda dapatkan</h3><ul class="mt-5 grid gap-3 sm:grid-cols-2">@foreach(['POS multi-cart','Produk & resep','Stok bahan baku','HPP otomatis','Laporan profit','Member & loyalitas','Pembayaran lengkap','Mode offline'] as $item)<li class="flex items-center gap-2 text-sm font-bold"><x-icon name="check" class="h-4 w-4 text-[#00AAA6]" />{{ $item }}</li>@endforeach</ul><p class="lp-muted mt-6 border-t lp-border pt-5 text-xs leading-5">Tidak ada biaya setup atau kartu kredit. Informasi paket dapat berubah saat Kasiva keluar dari fase early access dan akan diinformasikan secara transparan.</p></div>
                </div>
            </div>
        </section>

        <section id="faq" class="py-20 md:py-28">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 md:px-8 lg:grid-cols-[.7fr_1.3fr] lg:gap-20"><div><p class="text-xs font-black uppercase tracking-[.22em] text-[#00AAA6]">Pertanyaan umum</p><h2 class="mt-4 text-3xl font-black md:text-5xl">Hal penting sebelum mulai.</h2><p class="lp-muted mt-5 leading-7">Belum menemukan jawaban? Masuk ke Kasiva dan jelajahi alur outlet langsung.</p></div><div class="space-y-3">@foreach($faqs as $index => $faq)<details class="lp-card group rounded-2xl p-5" @if($index === 0) open @endif><summary class="lp-focus flex cursor-pointer list-none items-center justify-between gap-5 rounded-lg font-black"><span>{{ $faq['q'] }}</span><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#00AAA6]/15 text-[#00AAA6] transition group-open:rotate-45" aria-hidden="true">+</span></summary><p class="lp-muted mt-4 border-t lp-border pt-4 text-sm leading-7">{{ $faq['a'] }}</p></details>@endforeach</div></div>
        </section>

        <section class="px-4 pb-20 md:px-8 md:pb-28"><div class="lp-always-dark relative mx-auto max-w-7xl overflow-hidden rounded-[32px] bg-[#272D48] px-6 py-14 text-center text-white md:px-12 md:py-20"><div class="lp-grid absolute inset-0 opacity-30"></div><div class="absolute -right-20 -top-24 h-72 w-72 rounded-full bg-[#00AAA6]/25 blur-3xl"></div><div class="relative mx-auto max-w-3xl"><p class="text-xs font-black uppercase tracking-[.22em] text-[#3EDAD7]">Operasional lebih jernih dimulai hari ini</p><h2 class="mt-4 text-3xl font-black md:text-5xl">Berhenti menebak profit outlet Anda.</h2><p class="mx-auto mt-5 max-w-2xl text-sm leading-7 text-slate-300 md:text-base">Biarkan Kasiva mencatat transaksi, menggerakkan stok, dan menghitung HPP. Anda fokus melayani pelanggan dan mengembangkan usaha.</p><div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row"><a href="{{ route('register') }}" class="lp-focus inline-flex min-h-14 items-center justify-center gap-2 rounded-2xl bg-[#00AAA6] px-7 font-black text-white hover:bg-[#008F8C]">Mulai gratis <x-icon name="arrow-right" class="h-5 w-5" /></a><a href="{{ route('pos.cashier') }}" class="lp-focus inline-flex min-h-14 items-center justify-center rounded-2xl border border-[#505B93] bg-white/5 px-7 font-black text-white hover:bg-white/10">Lihat layar kasir</a></div></div></div></section>
    </main>

    <footer class="border-t lp-border lp-surface">
        <div class="mx-auto max-w-7xl px-4 py-12 md:px-8"><div class="grid gap-10 md:grid-cols-[1.4fr_1fr_1fr]"><div><a href="{{ route('landing') }}" class="lp-focus inline-flex min-h-11 items-center gap-3 rounded-xl"><img src="{{ asset('images/kasiva-logo-icon-128.png') }}" alt="" width="40" height="40" class="h-10 w-10 rounded-xl bg-white p-1"><span class="text-xl font-black">Kasiva</span></a><p class="lp-muted mt-4 max-w-sm text-sm leading-6">POS pintar untuk F&amp;B dan retail Indonesia—dari transaksi pertama hingga profit bersih.</p></div><div><h2 class="text-sm font-black">Produk</h2><nav class="lp-muted mt-4 flex flex-col items-start gap-3 text-sm font-bold" aria-label="Navigasi produk"><a href="#fitur" class="hover:text-[#00AAA6]">Fitur</a><a href="#harga" class="hover:text-[#00AAA6]">Harga</a><a href="{{ route('onboarding') }}" class="hover:text-[#00AAA6]">Panduan mobile</a><a href="{{ route('login') }}" class="hover:text-[#00AAA6]">Masuk</a></nav></div><div><h2 class="text-sm font-black">Perusahaan &amp; legal</h2><nav class="lp-muted mt-4 flex flex-col items-start gap-3 text-sm font-bold" aria-label="Navigasi perusahaan"><a href="{{ route('about') }}" class="hover:text-[#00AAA6]">Tentang Kasiva</a><a href="{{ route('privacy') }}" class="hover:text-[#00AAA6]">Kebijakan privasi</a><a href="{{ route('terms') }}" class="hover:text-[#00AAA6]">Syarat &amp; ketentuan</a></nav></div></div><div class="lp-muted mt-10 flex flex-col justify-between gap-3 border-t lp-border pt-6 text-xs font-medium sm:flex-row"><p>© {{ date('Y') }} Kasiva POS. Hak cipta dilindungi.</p><p>Dibuat untuk pertumbuhan bisnis Indonesia.</p></div></div>
    </footer>
</div>

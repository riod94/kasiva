@php
    $schemaContext = '@'.'context';
    $featureTitles = array_column($features, 'title');
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
    'featureList' => $featureTitles,
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

<script type="application/ld+json">{!! json_encode([
    $schemaContext => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => route('landing')],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

<script type="application/ld+json">{!! json_encode([
    $schemaContext => 'https://schema.org',
    '@type' => 'HowTo',
    'name' => 'Cara memulai Kasiva POS',
    'step' => array_map(fn ($s) => [
        '@type' => 'HowToStep',
        'position' => (int) $s['number'],
        'name' => $s['title'],
        'text' => $s['desc'],
    ], $howItWorks),
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

    <x-landing.nav :mobile-open="$mobileNavOpen" />

    <main id="konten-utama">
        <x-landing.hero />

        <x-landing.feature-grid />

        <x-landing.offline-strip />

        <x-landing.how-it-works />

        <x-landing.multi-platform />

        <x-landing.pricing />

        <x-landing.testimonials />

        <x-landing.faq />

        <x-landing.cta />
    </main>

    <x-landing.footer />
</div>

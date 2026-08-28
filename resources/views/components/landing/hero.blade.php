<section class="relative isolate overflow-hidden pb-20 pt-16 md:pb-28 md:pt-24" aria-labelledby="hero-title">
    <div class="lp-grid pointer-events-none absolute inset-0 -z-20 opacity-70 [mask-image:linear-gradient(to_bottom,black,transparent_88%)]"></div>
    <div class="pointer-events-none absolute -right-40 -top-48 -z-10 h-[520px] w-[520px] rounded-full bg-[#00AAA6]/15 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-48 bottom-0 -z-10 h-96 w-96 rounded-full bg-[#8696ED]/15 blur-3xl"></div>

    <div class="mx-auto grid max-w-7xl items-center gap-14 px-4 md:px-8 lg:grid-cols-[.9fr_1.1fr]">
        <div class="text-center lg:text-left">
            <div class="mb-6 inline-flex items-center gap-2 rounded-full border lp-border lp-surface px-3.5 py-2 text-xs font-extrabold shadow-sm">
                <span class="relative flex h-2 w-2" aria-hidden="true">
                    <span class="absolute h-full w-full animate-ping rounded-full bg-[#00AAA6] opacity-60"></span>
                    <span class="relative h-2 w-2 rounded-full bg-[#00AAA6]"></span>
                </span>
                Dibangun untuk F&amp;B dan retail Indonesia
            </div>

            <h1 id="hero-title" class="text-4xl font-black leading-[1.04] tracking-[-.045em] sm:text-5xl md:text-6xl xl:text-[68px]">
                Kasir bukan cuma mencatat. <span class="lp-gradient-text">Kasiva menjaga profit.</span>
            </h1>
            <p class="lp-muted mx-auto mt-6 max-w-2xl text-base font-medium leading-8 md:text-lg lg:mx-0">
                Kelola transaksi, resep, stok, loyalitas, dan laporan dalam satu POS yang menghitung HPP otomatis—bahkan ketika internet sedang tidak bersahabat.
            </p>
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row lg:justify-start">
                <a href="{{ route('register') }}" class="lp-focus inline-flex min-h-14 items-center justify-center gap-2 rounded-2xl bg-[#00AAA6] px-7 text-base font-extrabold text-white shadow-xl shadow-[#00AAA6]/25 transition hover:-translate-y-0.5 hover:bg-[#008F8C]">Coba Kasiva gratis <x-icon name="arrow-right" class="h-5 w-5" /></a>
                <a href="{{ route('login') }}" class="lp-focus inline-flex min-h-14 items-center justify-center gap-2 rounded-2xl border lp-border lp-surface px-7 text-base font-extrabold transition hover:border-[#00AAA6]">Lihat layar kasir</a>
            </div>

            <ul class="lp-muted mx-auto mt-8 flex max-w-xl flex-wrap items-center justify-center gap-x-4 gap-y-2 text-xs font-bold lg:mx-0 lg:justify-start">
                @foreach (['Tanpa setup', 'Tanpa kartu kredit', 'RBAC 3-tier', 'Mode offline 100%'] as $trust)
                    <li class="inline-flex items-center gap-1.5"><x-icon name="check" class="h-3.5 w-3.5 text-[#00AAA6]" />{{ $trust }}</li>
                @endforeach
            </ul>
        </div>

        {{-- Mockup POS — SVG/HTML murni, tidak ada image statis. --}}
        <div class="relative mx-auto w-full max-w-xl" aria-hidden="true">
            {{-- Struk mengambang --}}
            <div class="lp-float absolute -right-2 top-6 hidden w-44 rounded-2xl lp-card p-4 text-xs sm:block">
                <p class="text-[10px] font-black uppercase tracking-widest lp-muted">Struk KSV</p>
                <p class="mt-1 font-extrabold">KSV-20260827-9F2A</p>
                <div class="mt-3 space-y-1">
                    <div class="flex justify-between"><span>Kopi Susu</span><span>Rp 22.000</span></div>
                    <div class="flex justify-between"><span>Croissant</span><span>Rp 18.000</span></div>
                    <div class="flex justify-between border-t pt-1 font-extrabold"><span>Total</span><span>Rp 40.000</span></div>
                </div>
            </div>

            {{-- Layar kasir mini --}}
            <div class="lp-card relative overflow-hidden rounded-[28px] p-5 shadow-2xl">
                <div class="flex items-center justify-between pb-4">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#FF5F57]"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-[#FEBC2E]"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-[#28C840]"></span>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest lp-muted">Kasiva · kasir</span>
                </div>

                <div class="grid gap-4 md:grid-cols-[1.1fr_1fr]">
                    <div>
                        <div class="mb-3 flex items-center gap-2 rounded-xl lp-surface-2 px-3 py-2">
                            <x-icon name="search" class="h-4 w-4 lp-muted" />
                            <span class="text-xs lp-muted">Cari produk...</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach (['Kopi Susu', 'Croissant', 'Matcha', 'Donat'] as $i => $item)
                                <div class="rounded-xl lp-surface-2 p-3 text-left text-xs">
                                    <div class="font-extrabold">{{ $item }}</div>
                                    <div class="lp-muted mt-0.5 text-[10px]">Stok {{ 20 - $i * 3 }}</div>
                                    <div class="mt-2 text-[#00AAA6] font-extrabold">Rp {{ number_format(18000 + $i * 4000, 0, ',', '.') }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-2xl lp-surface-2 p-4">
                        <p class="text-[10px] font-black uppercase tracking-widest lp-muted">Keranjang</p>
                        <div class="mt-3 space-y-2 text-xs">
                            <div class="flex justify-between"><span>Kopi Susu ×1</span><span>Rp 22.000</span></div>
                            <div class="flex justify-between"><span>Croissant ×2</span><span>Rp 36.000</span></div>
                            <div class="flex justify-between lp-muted text-[10px]"><span>Diskon member</span><span>-Rp 3.000</span></div>
                            <div class="flex justify-between border-t pt-2 text-sm font-black"><span>Total</span><span class="text-[#3EDAD7]">Rp 55.000</span></div>
                        </div>
                        <button type="button" class="mt-3 inline-flex w-full min-h-10 items-center justify-center gap-2 rounded-xl bg-[#00AAA6] text-xs font-black text-white">Bayar <x-icon name="arrow-right" class="h-4 w-4" /></button>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between rounded-xl bg-[#272D48] px-4 py-3 text-white">
                    <div class="flex items-center gap-2 text-[11px] font-extrabold">
                        <span class="relative flex h-2 w-2"><span class="absolute h-full w-full animate-ping rounded-full bg-[#3EDAD7] opacity-60"></span><span class="relative h-2 w-2 rounded-full bg-[#3EDAD7]"></span></span>
                        Offline-ready
                    </div>
                    <span class="text-[10px] lp-muted text-slate-300">HPP otomatis · margin 47%</span>
                </div>
            </div>
        </div>
    </div>
</section>

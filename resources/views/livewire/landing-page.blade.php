<div class="flex flex-col min-h-screen bg-[#0F172A] text-slate-100 selection:bg-[#00AAA6]/30 selection:text-[#3EDAD7]">
    <!-- ═══════════════ HEADER ═══════════════ -->
    <header class="flex items-center justify-between p-4 md:px-8 md:py-4 border-b border-[#2E2A68] backdrop-blur-md sticky top-0 z-50 bg-[#0F172A]/90 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('landing') }}" class="flex items-center gap-2">
                <img src="/images/kasiva-logo-full.png" alt="Kasiva POS" class="h-9 md:h-10 object-contain bg-white/95 p-1.5 rounded-xl shadow-sm">
            </a>
            <span class="hidden sm:inline-flex text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-[#00AAA6]/30 text-[#8696ED] border border-[#00AAA6]/50">
                Point of Sale
            </span>
        </div>

        <!-- Desktop Nav -->
        <nav class="hidden md:flex items-center gap-8">
            <a href="#fitur" class="text-sm font-bold text-slate-300 hover:text-white transition-colors">Fitur</a>
            <a href="#analytics" class="text-sm font-bold text-slate-300 hover:text-white transition-colors">Kalkulator HPP</a>
            <a href="{{ route('about') }}" class="text-sm font-bold text-slate-300 hover:text-white transition-colors">Tentang Kami</a>
            <a href="{{ route('terms') }}" class="text-sm font-bold text-slate-300 hover:text-white transition-colors">Ketentuan</a>
        </nav>

        <div class="flex items-center gap-2 md:gap-3">
            <!-- Theme Toggle Button -->
            <button 
                type="button"
                onclick="window.toggleKasivaTheme()"
                class="w-9 h-9 rounded-xl bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#00AAA6] text-slate-300 hover:text-white flex items-center justify-center transition active:scale-95 shadow-sm cursor-pointer"
                title="Ganti Tema (Dark / Light)"
            >
                <x-icon name="sun" class="w-4 h-4 text-amber-400 block dark:hidden" />
                <x-icon name="moon" class="w-4 h-4 text-[#8696ED] hidden dark:block" />
            </button>

            <a href="{{ route('login') }}" class="text-xs font-bold text-slate-300 hover:text-white transition px-3 py-2">Masuk</a>
            <a href="{{ route('pos.cashier') }}" class="inline-flex items-center gap-2 rounded-xl font-extrabold px-5 py-2.5 shadow-lg bg-[#00AAA6] hover:bg-[#008F8C] text-white text-xs transition-all hover:scale-105 active:scale-95">
                <span>Buka Kasir</span>
                <x-icon name="arrow-right" class="w-3.5 h-3.5" />
            </a>
        </div>
    </header>

    <main class="flex-1">
        <!-- ═══════════════ HERO ═══════════════ -->
        <section class="relative pt-16 md:pt-24 pb-20 md:pb-32 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-96 h-96 bg-[#00AAA6]/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-[#00AAA6]/15 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-6xl mx-auto px-4 md:px-6 flex flex-col items-center text-center relative z-10 space-y-6">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 bg-[#1E1B4B] px-4 py-2 rounded-full border border-[#2E2A68] shadow-inner">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs font-black uppercase tracking-widest text-emerald-300">
                        Sistem Kasir Pintar & Manajemen HPP Presisi
                    </span>
                </div>

                <!-- Headline -->
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-black tracking-tighter leading-[1.08] max-w-5xl text-white">
                    Kelola Transaksi Outlet, <br class="hidden sm:block">
                    <span class="bg-gradient-to-r from-[#8696ED] via-[#3EDAD7] to-[#00AAA6] text-transparent bg-clip-text">
                        Kendalikan Profit Murni.
                    </span>
                </h1>

                <!-- Subheadline -->
                <p class="text-base md:text-lg lg:text-xl font-semibold text-slate-300 max-w-2xl leading-relaxed">
                    Sistem POS khusus outlet F&B dan retail Indonesia. Menghitung HPP resep otomatis,
                    mengamankan modal restok, dan mendukung kanal delivery online secara akurat.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 w-full justify-center pt-4">
                    <a href="{{ route('pos.cashier') }}" class="h-14 px-8 rounded-2xl font-black text-base w-full sm:w-auto shadow-xl shadow-[#00AAA6]/30 bg-[#00AAA6] hover:bg-[#008F8C] text-white transition-all hover:scale-[1.02] flex items-center justify-center gap-2">
                        <span>Mulai Transaksi Kasir</span>
                        <x-icon name="arrow-right" class="w-5 h-5" />
                    </a>
                    <a href="{{ route('onboarding') }}" class="h-14 px-8 rounded-2xl font-black text-base w-full sm:w-auto border border-[#2E2A68] bg-[#1E1B4B] hover:bg-[#2A3155] text-white transition-all shadow-sm flex items-center justify-center gap-2">
                        <x-icon name="store" class="w-5 h-5 text-[#8696ED]" />
                        <span>Panduan Mobile App</span>
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="flex flex-wrap justify-center gap-6 pt-4 text-xs font-bold text-slate-300">
                    <div class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-emerald-400" />
                        <span>Kalkulasi HPP Resep Real-Time</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-emerald-400" />
                        <span>Dukungan Struk Thermal ESC/POS</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-emerald-400" />
                        <span>Integrasi GoFood, Grab & Shopee</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════ CORE FEATURES ═══════════════ -->
        <section id="fitur" class="py-16 md:py-24 bg-[#16192E] border-y border-[#2E2A68]">
            <div class="max-w-6xl mx-auto px-4 md:px-6">
                <div class="text-center mb-12 md:mb-16 space-y-3">
                    <span class="inline-block text-xs font-black uppercase tracking-widest text-[#3EDAD7]">
                        Fitur Utama
                    </span>
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-black tracking-tight text-white">
                        Solusi Lengkap Operasional Outlet
                    </h2>
                    <p class="text-slate-300 font-medium max-w-2xl mx-auto text-sm md:text-base">
                        Dari pencatatan transaksi cepat hingga analisis laba bersih dan alokasi modal restok.
                    </p>
                </div>

                <!-- Feature Grid -->
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6">
                    <div class="bg-[#1E1B4B] p-6 rounded-3xl border border-[#2E2A68] shadow-xl hover:border-[#00AAA6] transition group">
                        <div class="w-12 h-12 bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded-2xl flex items-center justify-center mb-4 text-xl font-bold group-hover:scale-105 transition-transform">
                            <x-icon name="store" class="w-6 h-6 text-blue-400" />
                        </div>
                        <h3 class="font-black text-base md:text-lg mb-2 text-white">Kasir Layar Sentuh Responsif</h3>
                        <p class="text-xs text-slate-300 leading-relaxed font-medium">Desain antarmuka mobile-first dengan touch target luas untuk transaksi cepat, pencarian menu debounced, dan seleksi varian.</p>
                    </div>

                    <div class="bg-[#1E1B4B] p-6 rounded-3xl border border-[#2E2A68] shadow-xl hover:border-[#00AAA6] transition group">
                        <div class="w-12 h-12 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-2xl flex items-center justify-center mb-4 text-xl font-bold group-hover:scale-105 transition-transform">
                            <x-icon name="chart-bar" class="w-6 h-6 text-emerald-400" />
                        </div>
                        <h3 class="font-black text-base md:text-lg mb-2 text-white">Kalkulator HPP & Resep (BOM)</h3>
                        <p class="text-xs text-slate-300 leading-relaxed font-medium">Perhitungan moving average cost bahan baku per porsi menu secara akurat. Indikator kesehatan margin 4-tier dari kritis hingga optimal.</p>
                    </div>

                    <div class="bg-[#1E1B4B] p-6 rounded-3xl border border-[#2E2A68] shadow-xl hover:border-[#00AAA6] transition group">
                        <div class="w-12 h-12 bg-orange-500/20 text-orange-400 border border-orange-500/30 rounded-2xl flex items-center justify-center mb-4 text-xl font-bold group-hover:scale-105 transition-transform">
                            <x-icon name="bike" class="w-6 h-6 text-orange-400" />
                        </div>
                        <h3 class="font-black text-base md:text-lg mb-2 text-white">Penyesuaian Setoran Platform</h3>
                        <p class="text-xs text-slate-300 leading-relaxed font-medium">Input uang bersih (net received) untuk pesanan GoFood, GrabFood, dan ShopeeFood demi pembukuan omset yang jujur dan presisi.</p>
                    </div>

                    <div class="bg-[#1E1B4B] p-6 rounded-3xl border border-[#2E2A68] shadow-xl hover:border-[#00AAA6] transition group">
                        <div class="w-12 h-12 bg-purple-500/20 text-purple-400 border border-purple-500/30 rounded-2xl flex items-center justify-center mb-4 text-xl font-bold group-hover:scale-105 transition-transform">
                            <x-icon name="sparkles" class="w-6 h-6 text-purple-400" />
                        </div>
                        <h3 class="font-black text-base md:text-lg mb-2 text-white">AI Financial Advisor</h3>
                        <p class="text-xs text-slate-300 leading-relaxed font-medium">Rekomendasi cerdas pemisahan alokasi modal belanja bahan dengan uang bebas, serta proyeksi omset bulanan berbasis run-rate.</p>
                    </div>

                    <div class="bg-[#1E1B4B] p-6 rounded-3xl border border-[#2E2A68] shadow-xl hover:border-[#00AAA6] transition group">
                        <div class="w-12 h-12 bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 rounded-2xl flex items-center justify-center mb-4 text-xl font-bold group-hover:scale-105 transition-transform">
                            <x-icon name="printer" class="w-6 h-6 text-cyan-400" />
                        </div>
                        <h3 class="font-black text-base md:text-lg mb-2 text-white">Struk Thermal & WhatsApp</h3>
                        <p class="text-xs text-slate-300 leading-relaxed font-medium">Cetak struk ke printer Bluetooth thermal 58mm/80mm fisik serta opsi pembagian struk digital langsung ke WhatsApp pelanggan.</p>
                    </div>

                    <div class="bg-[#1E1B4B] p-6 rounded-3xl border border-[#2E2A68] shadow-xl hover:border-[#00AAA6] transition group">
                        <div class="w-12 h-12 bg-[#8696ED]/20 text-[#8696ED] border border-[#8696ED]/30 rounded-2xl flex items-center justify-center mb-4 text-xl font-bold group-hover:scale-105 transition-transform">
                            <x-icon name="gift" class="w-6 h-6 text-[#8696ED]" />
                        </div>
                        <h3 class="font-black text-base md:text-lg mb-2 text-white">Loyalitas Stempel & Bundle</h3>
                        <p class="text-xs text-slate-300 leading-relaxed font-medium">Program kartu stempel pelanggan digital, database member, dan pembuatan paket hemat kombo menu dengan kalkulator HPP otomatis.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════ MARGIN ANALYTICS ═══════════════ -->
        <section id="analytics" class="py-16 md:py-24">
            <div class="max-w-6xl mx-auto px-4 md:px-6">
                <div class="grid md:grid-cols-2 gap-10 items-center">
                    <div class="space-y-5">
                        <span class="inline-block text-xs font-black uppercase tracking-widest text-emerald-400">
                            Struktur Finansial Outlet
                        </span>
                        <h2 class="text-3xl md:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                            Kontrol Margin & <span class="text-emerald-400">Alokasi Modal Restok.</span>
                        </h2>
                        <p class="text-slate-300 font-medium leading-relaxed text-sm md:text-base">
                            Kasiva membedah setiap rupiah penjualan menjadi modal bahan baku (HPP) dan profit murni. Uang modal tidak lagi terpakai untuk konsumsi pribadi.
                        </p>

                        <div class="space-y-2.5">
                            <div class="flex items-center gap-3 p-3.5 bg-[#1E1B4B] rounded-2xl border border-red-500/30">
                                <span class="w-8 h-8 rounded-xl bg-red-500/20 text-red-400 font-black text-xs flex items-center justify-center">K</span>
                                <div>
                                    <span class="font-black text-xs text-white">Kritis (&lt; 30% Margin)</span>
                                    <p class="text-[10px] text-slate-400">Biaya modal bahan baku terlalu tinggi</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3.5 bg-[#1E1B4B] rounded-2xl border border-amber-500/30">
                                <span class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 font-black text-xs flex items-center justify-center">T</span>
                                <div>
                                    <span class="font-black text-xs text-white">Tipis (30–44% Margin)</span>
                                    <p class="text-[10px] text-slate-400">Batas aman minimal operasional</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3.5 bg-[#1E1B4B] rounded-2xl border border-emerald-500/30">
                                <span class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 font-black text-xs flex items-center justify-center">S</span>
                                <div>
                                    <span class="font-black text-xs text-white">Sehat (45–71% Margin)</span>
                                    <p class="text-[10px] text-slate-400">Struktur HPP ideal untuk ekspansi outlet</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3.5 bg-[#1E1B4B] rounded-2xl border border-[#8696ED]/30">
                                <span class="w-8 h-8 rounded-xl bg-[#8696ED]/20 text-[#8696ED] font-black text-xs flex items-center justify-center">O</span>
                                <div>
                                    <span class="font-black text-xs text-white">Optimal (≥ 72% Margin)</span>
                                    <p class="text-[10px] text-slate-400">Profitabilitas sangat tinggi</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Report Preview Card -->
                    <div class="bg-[#1E1B4B] rounded-3xl p-6 md:p-8 border border-[#2E2A68] shadow-2xl space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-[#2E2A68]">
                            <h3 class="font-black text-sm text-white flex items-center gap-2">
                                <x-icon name="chart-bar" class="w-4 h-4 text-[#8696ED]" />
                                <span>Simulasi Pembukuan Finansial</span>
                            </h3>
                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/30">Sehat</span>
                        </div>
                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between items-center pb-2 border-b border-[#2E2A68]">
                                <span class="text-slate-400">Total Omset Penjualan</span>
                                <span class="font-black text-white">Rp 2.450.000</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-[#2E2A68]">
                                <span class="text-slate-400">Alokasi Modal HPP (Wajib Putar)</span>
                                <span class="font-black text-amber-400">Rp 890.000</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-[#2E2A68]">
                                <span class="text-slate-400">Laba Kotor (Gross Profit)</span>
                                <span class="font-black text-emerald-400">Rp 1.560.000</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-[#2E2A68]">
                                <span class="text-slate-400">Beban Operasional Toko</span>
                                <span class="font-black text-rose-400">Rp 320.000</span>
                            </div>
                            <div class="flex justify-between items-center pt-1 font-black text-sm">
                                <span class="text-white">Profit Murni (Uang Bebas)</span>
                                <span class="text-[#3EDAD7]">Rp 1.240.000</span>
                            </div>
                        </div>
                        <div class="p-3 bg-[#16192E] rounded-xl border border-[#2E2A68] text-center">
                            <p class="text-[10px] font-bold text-slate-400">
                                23 Transaksi Tercatat • Margin Rata-rata: 63.7%
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════ BOTTOM CTA ═══════════════ -->
        <section class="py-16 md:py-20 bg-gradient-to-br from-[#00AAA6] via-[#008F8C] to-[#1E1B4B] text-center border-t border-[#00AAA6]/40">
            <div class="max-w-3xl mx-auto px-4 space-y-5">
                <h2 class="text-2xl md:text-4xl font-black tracking-tight text-white">
                    Tingkatkan Performa Outlet Anda Sekarang
                </h2>
                <p class="text-sm md:text-base text-slate-200 font-medium max-w-xl mx-auto">
                    Aplikasi kasir terintegrasi yang memudahkan operasional staf dan memberikan transparansi keuangan bisnis F&B.
                </p>
                <div class="pt-2">
                    <a href="{{ route('pos.cashier') }}" class="inline-flex items-center gap-2 h-14 px-8 rounded-2xl font-black text-sm bg-white text-[#00AAA6] hover:bg-slate-100 transition-all shadow-xl hover:scale-105 active:scale-95">
                        <span>Buka Layar Kasir POS</span>
                        <x-icon name="arrow-right" class="w-4 h-4 text-[#00AAA6]" />
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- ═══════════════ FOOTER ═══════════════ -->
    <footer class="border-t border-[#2E2A68] bg-[#16192E] py-8 px-6 text-xs text-slate-400">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="space-y-1 text-center md:text-left">
                <img src="/images/kasiva-logo-full.png" alt="Kasiva POS" class="h-7 w-auto object-contain bg-white/95 p-1 rounded-lg mx-auto md:mx-0">
                <p class="text-slate-500 text-[11px]">Sistem Kasir Pintar & Manajemen Inventaris F&B</p>
            </div>
            <div class="flex flex-wrap gap-5 font-bold text-slate-300">
                <a href="{{ route('pos.cashier') }}" class="hover:text-white transition">Kasir</a>
                <a href="{{ route('about') }}" class="hover:text-white transition">Tentang Kami</a>
                <a href="{{ route('privacy') }}" class="hover:text-white transition">Kebijakan Privasi</a>
                <a href="{{ route('terms') }}" class="hover:text-white transition">Syarat & Ketentuan</a>
            </div>
            <div class="text-[11px] text-slate-500">
                © 2026 Kasiva POS. Hak Cipta Dilindungi.
            </div>
        </div>
    </footer>
</div>

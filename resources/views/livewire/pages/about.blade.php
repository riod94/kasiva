<x-layouts.guest>
<div class="flex flex-col min-h-screen bg-[#0F172A] text-slate-100 selection:bg-[#00AAA6]/30 selection:text-[#3EDAD7]">
    <!-- Header -->
    <header class="flex items-center justify-between p-4 md:px-8 border-b border-[#2E2A68] backdrop-blur-md sticky top-0 z-50 bg-[#1E1B4B]/90 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
                <img src="/images/kasiva-logo-icon.png" alt="Kasiva POS" class="h-8 md:h-10 object-contain bg-white/95 p-1 rounded-xl shadow-sm">
                <span class="font-black text-lg text-white tracking-tight">Kasiva</span>
            </a>
        </div>
        <a href="{{ route('pos.cashier') }}" class="rounded-xl font-extrabold px-5 py-2.5 border border-[#2E2A68] text-xs md:text-sm bg-[#00AAA6] hover:bg-[#008F8C] text-white transition flex items-center gap-2 shadow-sm">
            <x-icon name="store" class="w-4 h-4" />
            <span>Buka Kasir POS</span>
        </a>
    </header>

    <main class="flex-1">
        <!-- ═══════════════ HERO ═══════════════ -->
        <section class="py-14 md:py-24 bg-gradient-to-br from-[#00AAA6]/15 via-[#1E1B4B] to-[#0F172A] border-b border-[#2E2A68]/60 text-center px-4">
            <div class="max-w-3xl mx-auto space-y-4">
                <div class="inline-flex items-center gap-2 bg-[#00AAA6]/20 px-4 py-1.5 rounded-full border border-[#00AAA6]/40 text-[#8696ED] text-xs font-black uppercase tracking-widest">
                    <span>Tentang Kasiva POS</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight leading-tight">
                    Solusi Kasir & Manajemen Finansial <span class="text-[#3EDAD7]">F&B Indonesia.</span>
                </h1>
                <p class="text-slate-300 font-medium text-sm md:text-base max-w-2xl mx-auto leading-relaxed">
                    Kasiva adalah sistem POS cerdas yang dirancang untuk operasional kedai kopi, restoran, dan bisnis ritel. Dilengkapi kalkulator HPP otomatis, struktur laba bersih, dan dukungan perangkat kasir lengkap.
                </p>
            </div>
        </section>

        <!-- ═══════════════ MISSION & VISION ═══════════════ -->
        <section class="py-12 md:py-16 max-w-4xl mx-auto px-4 space-y-4">
            <div class="grid md:grid-cols-3 gap-4">
                <div class="bg-[#1E1B4B] p-6 rounded-3xl border border-[#2E2A68] shadow-md space-y-3">
                    <div class="w-10 h-10 bg-[#00AAA6]/20 border border-[#00AAA6]/40 rounded-2xl flex items-center justify-center text-[#8696ED]">
                        <x-icon name="target" class="w-5 h-5 text-[#8696ED]" />
                    </div>
                    <h2 class="text-lg font-black text-white">Misi Kami</h2>
                    <p class="text-xs text-slate-300 leading-relaxed font-medium">
                        Membantu setiap pemilik outlet mengelola transaksi secara cepat dan memantau keuntungan bersih yang akurat dengan memisahkan modal restok bahan baku.
                    </p>
                </div>

                <div class="bg-[#1E1B4B] p-6 rounded-3xl border border-[#2E2A68] shadow-md space-y-3">
                    <div class="w-10 h-10 bg-emerald-500/20 border border-emerald-500/40 rounded-2xl flex items-center justify-center text-emerald-400">
                        <x-icon name="eye" class="w-5 h-5 text-emerald-400" />
                    </div>
                    <h2 class="text-lg font-black text-white">Visi Kami</h2>
                    <p class="text-xs text-slate-300 leading-relaxed font-medium">
                        Menjadi standar sistem POS yang andal bagi pelaku usaha kuliner di Indonesia, mulai dari kedai kopi independen hingga jaringan outlet kuliner.
                    </p>
                </div>

                <div class="bg-[#1E1B4B] p-6 rounded-3xl border border-[#2E2A68] shadow-md space-y-3">
                    <div class="w-10 h-10 bg-amber-500/20 border border-amber-500/40 rounded-2xl flex items-center justify-center text-amber-400">
                        <x-icon name="sparkles" class="w-5 h-5 text-amber-400" />
                    </div>
                    <h2 class="text-lg font-black text-white">Keunggulan Kasiva</h2>
                    <p class="text-xs text-slate-300 leading-relaxed font-medium">
                        Antarmuka cepat, touch target yang nyaman di perangkat tablet atau ponsel, pelacakan HPP moving average otomatis, dan pencatatan komisi platform delivery online.
                    </p>
                </div>
            </div>
        </section>

        <!-- ═══════════════ KEY FEATURES ═══════════════ -->
        <section class="py-12 md:py-16 bg-[#16192E] border-y border-[#2E2A68]">
            <div class="max-w-4xl mx-auto px-4">
                <div class="text-center mb-10 space-y-2">
                    <span class="text-xs font-black uppercase tracking-widest text-[#3EDAD7]">Fitur Utama</span>
                    <h2 class="text-2xl md:text-3xl font-black text-white">Kebutuhan Operasional Kasir Lengkap</h2>
                    <p class="text-xs md:text-sm text-slate-400 font-medium">Mulai dari pencatatan pesanan kasir, manajemen resep HPP, hingga pelaporan laba rugi.</p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    <div class="flex items-start gap-3.5 p-4 bg-[#1E1B4B] rounded-2xl border border-[#2E2A68]">
                        <div class="w-10 h-10 bg-blue-500/20 text-blue-400 rounded-xl flex items-center justify-center shrink-0">
                            <x-icon name="zap" class="w-5 h-5 text-blue-400" />
                        </div>
                        <div>
                            <h4 class="font-black text-sm text-white">Kasir Layar Sentuh</h4>
                            <p class="text-xs text-slate-300 mt-1 leading-relaxed">Antarmuka cepat dan responsif dengan pencarian menu kilat dan seleksi varian produk.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5 p-4 bg-[#1E1B4B] rounded-2xl border border-[#2E2A68]">
                        <div class="w-10 h-10 bg-[#00AAA6]/30 text-[#8696ED] rounded-xl flex items-center justify-center shrink-0">
                            <x-icon name="chart-bar" class="w-5 h-5 text-[#8696ED]" />
                        </div>
                        <div>
                            <h4 class="font-black text-sm text-white">Resep Bahan (BOM)</h4>
                            <p class="text-xs text-slate-300 mt-1 leading-relaxed">Hitung modal racikan per porsi menu dan pantau 4-tier indikator kesehatan margin.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5 p-4 bg-[#1E1B4B] rounded-2xl border border-[#2E2A68]">
                        <div class="w-10 h-10 bg-orange-500/20 text-orange-400 rounded-xl flex items-center justify-center shrink-0">
                            <x-icon name="layers" class="w-5 h-5 text-orange-400" />
                        </div>
                        <div>
                            <h4 class="font-black text-sm text-white">Varian & Modifiers</h4>
                            <p class="text-xs text-slate-300 mt-1 leading-relaxed">Atur ukuran cup, tingkat gula, topping tambahan, dan penyesuaian harga menu.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5 p-4 bg-[#1E1B4B] rounded-2xl border border-[#2E2A68]">
                        <div class="w-10 h-10 bg-emerald-500/20 text-emerald-400 rounded-xl flex items-center justify-center shrink-0">
                            <x-icon name="wallet" class="w-5 h-5 text-emerald-400" />
                        </div>
                        <div>
                            <h4 class="font-black text-sm text-white">Multi-Metode Pembayaran</h4>
                            <p class="text-xs text-slate-300 mt-1 leading-relaxed">Tunai, QRIS, serta penyesuaian nominal bersih untuk pesanan GoFood, GrabFood, & ShopeeFood.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5 p-4 bg-[#1E1B4B] rounded-2xl border border-[#2E2A68]">
                        <div class="w-10 h-10 bg-violet-500/20 text-violet-400 rounded-xl flex items-center justify-center shrink-0">
                            <x-icon name="printer" class="w-5 h-5 text-violet-400" />
                        </div>
                        <div>
                            <h4 class="font-black text-sm text-white">Printer Thermal ESC/POS</h4>
                            <p class="text-xs text-slate-300 mt-1 leading-relaxed">Cetak struk ke printer Bluetooth 58mm/80mm atau bagikan struk via WhatsApp pelanggan.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5 p-4 bg-[#1E1B4B] rounded-2xl border border-[#2E2A68]">
                        <div class="w-10 h-10 bg-rose-500/20 text-rose-400 rounded-xl flex items-center justify-center shrink-0">
                            <x-icon name="gift" class="w-5 h-5 text-rose-400" />
                        </div>
                        <div>
                            <h4 class="font-black text-sm text-white">Stempel & Loyalitas</h4>
                            <p class="text-xs text-slate-300 mt-1 leading-relaxed">Bangun retensi pelanggan tetap dengan program stempel reward digital otomatis.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════ BOTTOM CTA ═══════════════ -->
        <section class="py-14 md:py-20 bg-gradient-to-br from-[#00AAA6] via-[#008F8C] to-[#1E1B4B] text-center px-4 relative overflow-hidden border-t border-[#00AAA6]/40">
            <div class="max-w-xl mx-auto space-y-4 relative z-10">
                <h2 class="text-2xl md:text-3xl font-black text-white tracking-tight">Siap Memaksimalkan Penjualan?</h2>
                <p class="text-xs md:text-sm text-slate-100 font-medium">Gunakan Kasiva POS sekarang juga di laptop, tablet, maupun ponsel Anda.</p>
                <div class="pt-2">
                    <a href="{{ route('pos.cashier') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-white text-[#00AAA6] hover:bg-slate-100 font-black text-sm rounded-2xl shadow-xl transition active:scale-95 gap-2">
                        <span>Buka Layar Kasir Sekarang</span>
                        <x-icon name="arrow-right" class="w-4 h-4 text-[#00AAA6]" />
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-[#2E2A68] bg-[#16192E] py-8 px-4 md:px-8">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-400">
            <div class="flex items-center gap-2">
                <img src="/images/kasiva-logo-icon.png" alt="Kasiva" class="h-6 w-6 object-contain bg-white/95 p-0.5 rounded-lg">
                <span class="font-black text-sm text-white">Kasiva POS</span>
            </div>
            <div class="flex flex-wrap gap-5 font-bold text-slate-300">
                <a href="{{ route('privacy') }}" class="hover:text-white transition">Kebijakan Privasi</a>
                <a href="{{ route('terms') }}" class="hover:text-white transition">Syarat & Ketentuan</a>
                <a href="{{ route('about') }}" class="hover:text-white transition">Tentang Kami</a>
            </div>
            <p class="text-[11px] text-slate-500">© 2026 Kasiva POS. Hak Cipta Dilindungi Undang-Undang.</p>
        </div>
    </footer>
</div>
</x-layouts.guest>

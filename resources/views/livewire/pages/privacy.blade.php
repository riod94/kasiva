<x-layouts.guest>
<div class="flex flex-col min-h-screen bg-[#0F172A] text-slate-100 selection:bg-[#4338CA]/30 selection:text-[#3EDAD7]">
    <!-- Header -->
    <header class="flex items-center justify-between p-4 md:px-8 border-b border-[#2E2A68] backdrop-blur-md sticky top-0 z-50 bg-[#1E1B4B]/90 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
                <img src="/images/kasiva-logo-icon.png" alt="Kasiva POS" class="h-8 md:h-10 object-contain bg-white/95 p-1 rounded-xl shadow-sm">
                <span class="font-black text-lg text-white tracking-tight">Kasiva</span>
            </a>
        </div>
        <a href="{{ route('pos.cashier') }}" class="rounded-xl font-extrabold px-5 py-2.5 border border-[#2E2A68] text-xs md:text-sm bg-[#4338CA] hover:bg-[#3730A3] text-white transition flex items-center gap-2 shadow-sm">
            <x-icon name="store" class="w-4 h-4" />
            <span>Buka Kasir POS</span>
        </a>
    </header>

    <main class="flex-1">
        <!-- Hero Section -->
        <section class="py-14 md:py-20 bg-gradient-to-br from-[#4338CA]/15 via-[#1E1B4B] to-[#0F172A] border-b border-[#2E2A68]/60 text-center px-4">
            <div class="max-w-3xl mx-auto space-y-4">
                <div class="inline-flex items-center gap-2 bg-[#4338CA]/20 px-4 py-1.5 rounded-full border border-[#4338CA]/40 text-[#8696ED] text-xs font-black uppercase tracking-widest">
                    <span>Kebijakan Privasi</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight leading-tight">
                    Privasi Data & <span class="text-[#3EDAD7]">Keamanan Bisnis Anda.</span>
                </h1>
                <p class="text-slate-400 font-medium text-xs md:text-sm">
                    Terakhir diperbarui: 25 April 2026 • Standar Enkripsi & Perlindungan Multi-Tenancy Kasiva.
                </p>
            </div>
        </section>

        <!-- Content Sections -->
        <section class="py-12 md:py-16 max-w-4xl mx-auto px-4 space-y-6">
            <div class="bg-[#1E1B4B] p-6 md:p-8 rounded-3xl border border-[#2E2A68] shadow-md text-xs md:text-sm text-slate-300 leading-relaxed font-medium">
                Kasiva POS memprioritaskan kerahasiaan dan keamanan data transaksi finansial, takaran resep HPP, serta data pelanggan Anda. Dokumen ini merinci kebijakan kami dalam mengelola dan melindungi data usaha Anda.
            </div>

            <!-- Section 1 -->
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500/20 text-blue-400 rounded-2xl flex items-center justify-center font-black text-lg border border-blue-500/40">
                        <x-icon name="database" class="w-5 h-5 text-blue-400" />
                    </div>
                    <h2 class="text-lg md:text-xl font-black text-white">1. Pengumpulan & Kepemilikan Data</h2>
                </div>
                <div class="bg-[#1E1B4B] p-5 md:p-6 rounded-2xl border border-[#2E2A68] text-xs md:text-sm text-slate-300 leading-relaxed space-y-3">
                    <p>Kasiva dirancang dengan isolasi multi-tenant data outlet yang ketat:</p>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2"><x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" /> <span>Seluruh data penjualan dan katalog produk disimpan secara aman dan hanya dapat diakses oleh akun outlet Anda.</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" /> <span>Data keuangan, resep HPP, dan margin keuntungan 100% merupakan properti mutlak pemilik usaha.</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" /> <span>Kami tidak menjual atau membagikan data transaksi Anda kepada pihak ketiga mana pun.</span></li>
                    </ul>
                </div>
            </div>

            <!-- Section 2 -->
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-500/20 text-emerald-400 rounded-2xl flex items-center justify-center font-black text-lg border border-emerald-500/40">
                        <x-icon name="eye" class="w-5 h-5 text-emerald-400" />
                    </div>
                    <h2 class="text-lg md:text-xl font-black text-white">2. Tujuan Penggunaan Informasi</h2>
                </div>
                <div class="bg-[#1E1B4B] p-5 md:p-6 rounded-2xl border border-[#2E2A68] text-xs md:text-sm text-slate-300 leading-relaxed space-y-2">
                    <p>Informasi yang Anda catat hanya dipergunakan untuk:</p>
                    <p>1. Memproses transaksi kasir, kalkulasi pemotongan stok bahan baku, dan pembuatan struk.</p>
                    <p>2. Menghasilkan ringkasan laporan laba-rugi, pergerakan HPP, dan performa omset outlet.</p>
                    <p>3. Mengelola program loyalitas member WhatsApp dan kupon promo toko Anda.</p>
                </div>
            </div>

            <!-- Section 3 -->
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-violet-500/20 text-violet-400 rounded-2xl flex items-center justify-center font-black text-lg border border-violet-500/40">
                        <x-icon name="shield" class="w-5 h-5 text-violet-400" />
                    </div>
                    <h2 class="text-lg md:text-xl font-black text-white">3. Standar Keamanan & Enkripsi PIN</h2>
                </div>
                <div class="bg-[#1E1B4B] p-5 md:p-6 rounded-2xl border border-[#2E2A68] text-xs md:text-sm text-slate-300 leading-relaxed space-y-2">
                    <p>Seluruh kata sandi dan 6-digit PIN login kasir dienkripsi secara aman menggunakan algoritma standar industri. Data sensitif pada koneksi jaringan dikomunikasikan melalui protokol aman HTTPS/TLS.</p>
                </div>
            </div>

            <!-- Kontak Box -->
            <div class="bg-gradient-to-br from-[#4338CA]/20 to-[#1E1B4B] p-6 md:p-8 rounded-3xl border border-[#4338CA]/40 text-center space-y-3">
                <h3 class="text-base md:text-lg font-black text-white">Pertanyaan Mengenai Privasi & Keamanan Data?</h3>
                <p class="text-xs text-slate-300 max-w-md mx-auto">Kami berdedikasi menjaga keamanan data Anda. Hubungi tim kami:</p>
                <a href="mailto:privacy@kasiva.id" class="inline-flex items-center justify-center px-6 py-2.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-bold text-xs rounded-xl shadow transition">
                    privacy@kasiva.id
                </a>
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

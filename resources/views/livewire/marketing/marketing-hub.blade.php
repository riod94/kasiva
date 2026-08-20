<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div>
            <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                <span>Pusat Pemasaran & Loyalitas</span>
            </h1>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Tingkatkan retensi pelanggan dengan stempel loyalitas, paket hemat bundle, dan promo diskon</p>
        </div>
    </div>

    <!-- Marketing Modules Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- 1. Members & QR Code -->
        <a href="{{ route('marketing.members') }}" class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] p-5 rounded-3xl shadow-lg transition flex items-start gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/40 group-hover:scale-105 transition shrink-0">
                <x-icon name="users" class="w-6 h-6 text-emerald-400" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-white group-hover:text-emerald-300 transition">Database Member & QR</h3>
                    <x-icon name="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-white transition" />
                </div>
                <p class="text-xs text-slate-400 font-medium mt-1">Daftar pelanggan setia, kartu QR member digital, dan riwayat kunjungan.</p>
            </div>
        </a>

        <!-- 2. Program Loyalty & Stamp -->
        <a href="{{ route('marketing.loyalty') }}" class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] p-5 rounded-3xl shadow-lg transition flex items-start gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center border border-amber-500/40 group-hover:scale-105 transition shrink-0">
                <x-icon name="gift" class="w-6 h-6 text-amber-400" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-white group-hover:text-amber-300 transition">Loyalitas & Stempel Digital</h3>
                    <x-icon name="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-white transition" />
                </div>
                <p class="text-xs text-slate-400 font-medium mt-1">Atur target stempel digital, hadiah reward gratis, dan integrasi pesan WhatsApp.</p>
            </div>
        </a>

        <!-- 3. Bundle Produk (Paket Hemat) -->
        <a href="{{ route('marketing.bundles') }}" class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] p-5 rounded-3xl shadow-lg transition flex items-start gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center border border-indigo-500/40 group-hover:scale-105 transition shrink-0">
                <x-icon name="package" class="w-6 h-6 text-indigo-400" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-white group-hover:text-[#3EDAD7] transition">Paket Bundle Menu</h3>
                    <x-icon name="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-white transition" />
                </div>
                <p class="text-xs text-slate-400 font-medium mt-1">Buat paket hemat kombo menu dengan kalkulasi modal HPP otomatis.</p>
            </div>
        </a>

        <!-- 4. Diskon & Voucher Promo -->
        <a href="{{ route('marketing.discounts') }}" class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] p-5 rounded-3xl shadow-lg transition flex items-start gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-blue-500/20 text-blue-400 flex items-center justify-center border border-blue-500/40 group-hover:scale-105 transition shrink-0">
                <x-icon name="tag" class="w-6 h-6 text-blue-400" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-white group-hover:text-blue-300 transition">Diskon & Promo Menu</h3>
                    <x-icon name="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-white transition" />
                </div>
                <p class="text-xs text-slate-400 font-medium mt-1">Potongan harga persen (%), nominal rupiah (Rp), atau skema Buy X Get Y.</p>
            </div>
        </a>

        <!-- 5. Kampanye & Promosi Khusus -->
        <a href="{{ route('marketing.campaigns') }}" class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] p-5 rounded-3xl shadow-lg transition flex items-start gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-pink-500/20 text-pink-400 flex items-center justify-center border border-pink-500/40 group-hover:scale-105 transition shrink-0">
                <x-icon name="megaphone" class="w-6 h-6 text-pink-400" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-white group-hover:text-pink-300 transition">Kampanye & Promosi</h3>
                    <x-icon name="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-white transition" />
                </div>
                <p class="text-xs text-slate-400 font-medium mt-1">Kelola event musiman, promo flash sale, dan kampanye broadcast toko.</p>
            </div>
        </a>
    </div>
</div>

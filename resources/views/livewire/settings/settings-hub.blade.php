<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div>
            <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                <span>Pusat Pengaturan Outlet</span>
            </h1>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Konfigurasi outlet, format cetak struk thermal, QRIS, staf PIN, dan hak akses</p>
        </div>
    </div>

    <!-- Settings Navigation Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- 1. Informasi Outlet -->
        <a href="{{ route('settings.outlet') }}" class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] p-5 rounded-3xl shadow-lg transition flex items-start gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-orange-500/20 text-orange-400 flex items-center justify-center border border-orange-500/40 group-hover:scale-105 transition shrink-0">
                <x-icon name="store" class="w-6 h-6 text-orange-400" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-white group-hover:text-[#3EDAD7] transition">Informasi Outlet</h3>
                    <x-icon name="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-white transition" />
                </div>
                <p class="text-xs text-slate-400 font-medium mt-1">Nama toko di struk, alamat outlet, nomor telepon, dan pajak PB1.</p>
            </div>
        </a>

        <!-- 2. Pengaturan Struk Thermal -->
        <a href="{{ route('settings.receipt') }}" class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] p-5 rounded-3xl shadow-lg transition flex items-start gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center border border-cyan-500/40 group-hover:scale-105 transition shrink-0">
                <x-icon name="printer" class="w-6 h-6 text-cyan-400" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-white group-hover:text-[#3EDAD7] transition">Pengaturan Struk</h3>
                    <x-icon name="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-white transition" />
                </div>
                <p class="text-xs text-slate-400 font-medium mt-1">Kustomisasi logo toko, footer struk promosi, dan lebar kertas 58mm/80mm.</p>
            </div>
        </a>

        <!-- 3. Metode Pembayaran & QRIS -->
        <a href="{{ route('settings.payment') }}" class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] p-5 rounded-3xl shadow-lg transition flex items-start gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/40 group-hover:scale-105 transition shrink-0">
                <x-icon name="qr-code" class="w-6 h-6 text-emerald-400" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-white group-hover:text-emerald-300 transition">Metode Pembayaran</h3>
                    <x-icon name="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-white transition" />
                </div>
                <p class="text-xs text-slate-400 font-medium mt-1">Unggah QRIS statis outlet dan aktivasi kanal GoFood, GrabFood, ShopeeFood.</p>
            </div>
        </a>

        <!-- 4. Manajemen Staff & PIN -->
        <a href="{{ route('settings.staff') }}" class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] p-5 rounded-3xl shadow-lg transition flex items-start gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center border border-indigo-500/40 group-hover:scale-105 transition shrink-0">
                <x-icon name="users" class="w-6 h-6 text-indigo-400" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-white group-hover:text-[#3EDAD7] transition">Manajemen Staf</h3>
                    <x-icon name="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-white transition" />
                </div>
                <p class="text-xs text-slate-400 font-medium mt-1">Daftar operator kasir, email, nomor WhatsApp, dan autentikasi 6-digit PIN.</p>
            </div>
        </a>

        <!-- 5. Hak Akses & Peran (RBAC) -->
        <a href="{{ route('settings.roles') }}" class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] p-5 rounded-3xl shadow-lg transition flex items-start gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-purple-500/20 text-purple-400 flex items-center justify-center border border-purple-500/40 group-hover:scale-105 transition shrink-0">
                <x-icon name="shield" class="w-6 h-6 text-purple-400" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-white group-hover:text-purple-300 transition">Hak Akses & Peran</h3>
                    <x-icon name="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-white transition" />
                </div>
                <p class="text-xs text-slate-400 font-medium mt-1">Matriks izin modul dinamis untuk Owner, Supervisor, dan Kasir Outlet.</p>
            </div>
        </a>

        <!-- 6. Profil Akun & Keamanan -->
        <a href="{{ route('profile.show') }}" class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] p-5 rounded-3xl shadow-lg transition flex items-start gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center border border-amber-500/40 group-hover:scale-105 transition shrink-0">
                <x-icon name="settings" class="w-6 h-6 text-amber-400" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-white group-hover:text-amber-300 transition">Profil & Keamanan</h3>
                    <x-icon name="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-white transition" />
                </div>
                <p class="text-xs text-slate-400 font-medium mt-1">Informasi pengguna aktif, ganti password akun, dan pengaturan sesi.</p>
            </div>
        </a>
    </div>
</div>

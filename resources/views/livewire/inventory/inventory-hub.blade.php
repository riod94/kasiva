<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-[#1E1B4B] p-6 md:p-8 rounded-3xl border border-[#2E2A68] shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-[#3EDAD7]">
                <x-icon name="package" class="w-4 h-4 text-[#3EDAD7]" />
                <span>Pusat Inventaris & Resep HPP</span>
            </div>
            <h1 class="text-xl md:text-2xl font-black text-white mt-1">Manajemen Inventaris & Menu</h1>
            <p class="text-xs text-slate-400 font-medium mt-1">Kelola katalog produk, resep HPP, master bahan baku, kategori, dan grup varian</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('settings.products') }}" class="px-4 py-2.5 bg-[#4338CA] hover:bg-[#3730A3] text-white text-xs font-extrabold uppercase tracking-wider rounded-2xl shadow transition active:scale-95 flex items-center gap-1.5">
                <x-icon name="plus" class="w-3.5 h-3.5" />
                <span>Tambah Menu</span>
            </a>
            <a href="{{ route('inventory.materials') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold uppercase tracking-wider rounded-2xl shadow transition active:scale-95 flex items-center gap-1.5">
                <x-icon name="plus" class="w-3.5 h-3.5" />
                <span>Restok Bahan</span>
            </a>
        </div>
    </div>

    <!-- Metrics Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5">
        <div class="bg-[#1E1B4B] border border-[#2E2A68] p-5 rounded-3xl shadow">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Produk Aktif</p>
            <p class="text-2xl font-black text-white mt-1">{{ $totalProducts }} <span class="text-xs text-slate-400 font-medium">Menu</span></p>
        </div>
        <div class="bg-[#1E1B4B] border border-[#2E2A68] p-5 rounded-3xl shadow">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bahan Baku</p>
            <p class="text-2xl font-black text-white mt-1">{{ $totalMaterials }} <span class="text-xs text-slate-400 font-medium">Item</span></p>
        </div>
        <div class="bg-[#1E1B4B] border border-[#2E2A68] p-5 rounded-3xl shadow">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Stok Menipis</p>
            <p class="text-2xl font-black {{ $lowStockMaterials > 0 ? 'text-amber-400' : 'text-emerald-400' }} mt-1">
                {{ $lowStockMaterials }} <span class="text-xs text-slate-400 font-medium">Bahan</span>
            </p>
        </div>
        <div class="bg-[#1E1B4B] border border-[#2E2A68] p-5 rounded-3xl shadow">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kategori & Varian</p>
            <p class="text-2xl font-black text-[#3EDAD7] mt-1">{{ $totalCategories }} <span class="text-xs text-slate-400 font-medium">/ {{ $totalVariants }} Varian</span></p>
        </div>
    </div>

    <!-- Sub-Module Navigation Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- 1. Katalog Produk & Resep HPP -->
        <a href="{{ route('settings.products') }}" class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] p-5 rounded-3xl shadow-lg transition flex items-start gap-4 group">
            <div class="w-14 h-14 rounded-2xl bg-[#4338CA]/20 text-indigo-400 flex items-center justify-center border border-[#4338CA]/40 group-hover:scale-105 transition shrink-0">
                <x-icon name="shopping-bag" class="w-7 h-7 text-indigo-400" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-black text-white group-hover:text-[#3EDAD7] transition">Katalog Menu & Resep HPP</h3>
                    <x-icon name="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-white transition" />
                </div>
                <p class="text-xs text-slate-400 font-medium mt-1">Kelola harga jual, takaran bahan resep per porsi, margin 4-tier, dan foto menu produk.</p>
                <span class="inline-block mt-3 text-[10px] font-black px-2.5 py-1 rounded-full bg-[#16192E] text-slate-300 border border-[#2E2A68]">
                    {{ $totalProducts }} Menu Terdaftar
                </span>
            </div>
        </a>

        <!-- 2. Master Bahan Baku & Restok -->
        <a href="{{ route('inventory.materials') }}" class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] p-5 rounded-3xl shadow-lg transition flex items-start gap-4 group">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/40 group-hover:scale-105 transition shrink-0">
                <x-icon name="package" class="w-7 h-7 text-emerald-400" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-black text-white group-hover:text-emerald-300 transition">Library Bahan Baku & Restok</h3>
                    <x-icon name="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-white transition" />
                </div>
                <p class="text-xs text-slate-400 font-medium mt-1">Pantau stok bahan mentah, catat pembelian supplier, dan kalkulasi moving average HPP.</p>
                <span class="inline-block mt-3 text-[10px] font-black px-2.5 py-1 rounded-full {{ $lowStockMaterials > 0 ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' }}">
                    {{ $totalMaterials }} Bahan ({{ $lowStockMaterials }} Menipis)
                </span>
            </div>
        </a>

        <!-- 3. Master Kategori Menu -->
        <a href="{{ route('inventory.categories') }}" class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] p-5 rounded-3xl shadow-lg transition flex items-start gap-4 group">
            <div class="w-14 h-14 rounded-2xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center border border-indigo-500/40 group-hover:scale-105 transition shrink-0">
                <x-icon name="tag" class="w-7 h-7 text-indigo-400" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-black text-white group-hover:text-[#3EDAD7] transition">Master Kategori Menu</h3>
                    <x-icon name="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-white transition" />
                </div>
                <p class="text-xs text-slate-400 font-medium mt-1">Kelola pengelompokan menu makanan/minuman, ikon kategori, dan urutan tab di kasir.</p>
                <span class="inline-block mt-3 text-[10px] font-black px-2.5 py-1 rounded-full bg-[#16192E] text-slate-300 border border-[#2E2A68]">
                    {{ $totalCategories }} Kategori
                </span>
            </div>
        </a>

        <!-- 4. Master Template Varian & Modifiers -->
        <a href="{{ route('inventory.variations') }}" class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] p-5 rounded-3xl shadow-lg transition flex items-start gap-4 group">
            <div class="w-14 h-14 rounded-2xl bg-purple-500/20 text-purple-400 flex items-center justify-center border border-purple-500/40 group-hover:scale-105 transition shrink-0">
                <x-icon name="layers" class="w-7 h-7 text-purple-400" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-black text-white group-hover:text-purple-300 transition">Master Varian & Opsi</h3>
                    <x-icon name="chevron-right" class="w-4 h-4 text-slate-500 group-hover:text-white transition" />
                </div>
                <p class="text-xs text-slate-400 font-medium mt-1">Atur opsi ukuran (Regular/Large), level manis, suhu, dan penambahan harga otomatis.</p>
                <span class="inline-block mt-3 text-[10px] font-black px-2.5 py-1 rounded-full bg-[#16192E] text-slate-300 border border-[#2E2A68]">
                    {{ $totalVariants }} Grup Varian
                </span>
            </div>
        </a>
    </div>
</div>

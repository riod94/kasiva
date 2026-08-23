<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#2A3155] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <span>Bahan Baku & Inventaris Stok</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Pantau stok bahan mentah, moving average HPP, dan pencatatan restok</p>
            </div>
        </div>
        <button 
            wire:click="openCreateModal"
            class="px-5 py-3 bg-[#00AAA6] hover:bg-[#008F8C] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg flex items-center justify-center gap-2 active:scale-95 transition shrink-0"
        >
            <x-icon name="plus" class="w-4 h-4" />
            <span>Tambah Bahan</span>
        </button>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Search Input -->
    <div class="relative w-full sm:w-80">
        <x-icon name="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
        <input 
            type="text" 
            wire:model.live.debounce.250ms="search"
            placeholder="Cari nama bahan baku..."
            class="w-full pl-10 pr-4 py-2.5 bg-[#1E1B4B] border border-[#2E2A68] rounded-2xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-[#00AAA6] transition"
        >
    </div>

    <!-- Materials Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($materials as $material)
            @php
                $isLowStock = $material->current_stock <= $material->min_stock;
            @endphp
            <div class="bg-[#1E1B4B] border {{ $isLowStock ? 'border-amber-500/50' : 'border-[#2E2A68]' }} rounded-3xl p-5 flex flex-col justify-between gap-4 shadow-md hover:border-[#00AAA6] transition group">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h4 class="font-black text-sm text-white leading-tight">{{ $material->name }}</h4>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#16192E] text-slate-300 border border-[#2E2A68] mt-1 inline-block">
                                Satuan: {{ $material->unit }}
                            </span>
                        </div>
                        <span class="text-[10px] font-black px-2.5 py-1 rounded-full {{ $isLowStock ? 'bg-amber-500/10 text-amber-300 border border-amber-500/30' : 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/30' }}">
                            {{ $isLowStock ? 'Stok Menipis' : 'Stok Aman' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 bg-[#16192E] p-3 rounded-2xl border border-[#2E2A68] text-xs">
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Stok Saat Ini:</p>
                            <p class="font-black text-white text-sm mt-0.5">
                                {{ number_format($material->current_stock, 0, ',', '.') }} <span class="text-[10px] text-slate-400">{{ $material->unit }}</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">HPP Rata-Rata:</p>
                            <p class="font-black text-[#3EDAD7] text-sm mt-0.5">
                                Rp {{ number_format($material->avg_cost, 0, ',', '.') }}<span class="text-[9px] text-slate-400">/{{ $material->unit }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-3 border-t border-[#2E2A68]">
                    <button 
                        wire:click="openRestockModal('{{ $material->id }}')" 
                        class="flex-1 py-2 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white rounded-xl text-xs font-black border border-emerald-500/40 transition flex items-center justify-center gap-1.5 active:scale-95"
                    >
                        <x-icon name="plus" class="w-3.5 h-3.5" />
                        <span>Restok</span>
                    </button>
                    <button 
                        wire:click="openEditModal('{{ $material->id }}')" 
                        class="p-2 bg-[#16192E] hover:bg-[#00AAA6] text-slate-200 hover:text-white rounded-xl text-xs font-bold border border-[#2E2A68] transition"
                        title="Edit Bahan"
                    >
                        <x-icon name="edit" class="w-3.5 h-3.5" />
                    </button>
                    <button 
                        wire:click="deleteMaterial('{{ $material->id }}')" 
                        wire:confirm="Yakin ingin menghapus bahan baku ini?" 
                        class="p-2 bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white rounded-xl text-xs font-bold border border-rose-500/30 transition"
                        title="Hapus Bahan"
                    >
                        <x-icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-[#1E1B4B] rounded-3xl border border-[#2E2A68]">
                <x-icon name="package" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                <p class="text-sm font-bold text-slate-300">Belum ada bahan baku terdaftar</p>
                <p class="text-xs text-slate-500 mt-0.5">Tambahkan bahan baku untuk menghitung HPP resep otomatis</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $materials->links() }}
    </div>

    <!-- Restock Modal -->
    @if($showRestockModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-sm rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <div>
                        <h3 class="text-base font-black text-white">Catat Restok Masuk</h3>
                        <p class="text-xs text-slate-400 font-bold mt-0.5">{{ $restockMaterialName }} ({{ $restockUnit }})</p>
                    </div>
                    <button wire:click="$set('showRestockModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Jumlah Masuk ({{ $restockUnit }})</label>
                        <input type="number" step="0.01" wire:model="restockQuantity" placeholder="cth: 1000" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                        @error('restockQuantity') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Total Biaya Pembelian (Rp)</label>
                        <input type="number" wire:model="restockTotalCost" placeholder="cth: 150000" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                        @error('restockTotalCost') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Catatan Pembelian / Supplier</label>
                        <input type="text" wire:model="restockNotes" placeholder="cth: Beli di Toko Bahan Roti" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                    </div>
                </div>

                <button 
                    wire:click="processRestock"
                    class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95"
                >
                    Konfirmasi Restok Bahan
                </button>
            </div>
        </div>
    @endif

    <!-- Create/Edit Material Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-sm rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <h3 class="text-base font-black text-white">
                        {{ $materialId ? 'Ubah Bahan Baku' : 'Tambah Bahan Baku Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nama Bahan Baku</label>
                        <input type="text" wire:model="name" placeholder="cth: Biji Kopi Arabika" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                        @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Satuan Takar</label>
                            <select wire:model="unit" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white focus:outline-none focus:border-[#00AAA6]">
                                <option value="gram">Gram (g)</option>
                                <option value="ml">Mililiter (ml)</option>
                                <option value="pcs">Pieces (pcs)</option>
                                <option value="kg">Kilogram (kg)</option>
                                <option value="liter">Liter (l)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Stok Minimum</label>
                            <input type="number" wire:model="min_stock" placeholder="100" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                        </div>
                    </div>

                    @if(!$materialId)
                        <div>
                            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Stok Awal</label>
                            <input type="number" wire:model="current_stock" placeholder="1000" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                        </div>
                    @endif

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">HPP Awal per Satuan (Rp)</label>
                        <input type="number" wire:model="avg_cost" placeholder="cth: 180" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                        @error('avg_cost') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button 
                    wire:click="saveMaterial"
                    class="w-full py-3.5 bg-[#00AAA6] hover:bg-[#008F8C] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95"
                >
                    {{ $materialId ? 'Simpan Perubahan' : 'Terbitkan Bahan Baku' }}
                </button>
            </div>
        </div>
    @endif
</div>

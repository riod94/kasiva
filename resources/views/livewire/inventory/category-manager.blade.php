<div class="space-y-6">
    <!-- Header Page & Back Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#25215A] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <span>Kategori Menu</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Atur pengelompokan menu, label ikon, dan urutan tab kasir</p>
            </div>
        </div>
        <button 
            wire:click="openCreateModal"
            class="px-5 py-3 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg flex items-center justify-center gap-2 active:scale-95 transition shrink-0"
        >
            <x-icon name="plus" class="w-4 h-4" />
            <span>Tambah Kategori</span>
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
            placeholder="Cari nama kategori..."
            class="w-full pl-10 pr-4 py-2.5 bg-[#1E1B4B] border border-[#2E2A68] rounded-2xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-[#4338CA] transition"
        >
    </div>

    <!-- Category Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($categories as $category)
            <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-3xl p-5 flex items-center justify-between shadow-md hover:border-[#4338CA] transition group">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-[#16192E] text-2xl flex items-center justify-center border border-[#2E2A68] shadow-inner">
                        {{ $category->icon ?? '🏷️' }}
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-white">{{ $category->name }}</h4>
                        <p class="text-[11px] text-slate-400 font-mono mt-0.5">Urutan Tab: #{{ $category->order_index }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button 
                        wire:click="openEditModal('{{ $category->id }}')" 
                        class="p-2.5 bg-[#16192E] hover:bg-[#4338CA] text-slate-200 hover:text-white rounded-xl text-xs font-bold border border-[#2E2A68] transition"
                        title="Edit Kategori"
                    >
                        <x-icon name="edit" class="w-3.5 h-3.5" />
                    </button>
                    <button 
                        wire:click="deleteCategory('{{ $category->id }}')" 
                        wire:confirm="Yakin ingin menghapus kategori {{ $category->name }}?" 
                        class="p-2.5 bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white rounded-xl text-xs font-bold border border-rose-500/30 transition"
                        title="Hapus Kategori"
                    >
                        <x-icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-[#1E1B4B] rounded-3xl border border-[#2E2A68]">
                <x-icon name="tag" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                <p class="text-sm font-bold text-slate-300">Belum ada kategori menu</p>
                <p class="text-xs text-slate-500 mt-0.5">Tambahkan kategori untuk mengelompokkan menu di kasir</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>

    <!-- Category Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-sm rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <h3 class="text-base font-black text-white">
                        {{ $categoryId ? 'Ubah Kategori' : 'Tambah Kategori Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nama Kategori</label>
                        <input type="text" wire:model="name" placeholder="cth: Kopi & Espresso" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA]">
                        @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Preset Icon Picker -->
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Pilih Ikon Cepat</label>
                        <div class="grid grid-cols-6 gap-2 bg-[#16192E] p-2.5 rounded-2xl border border-[#2E2A68]">
                            @php
                                $presetIcons = ['☕', '🍵', '🧋', '🥤', '🥪', '🍰', '🍚', '🍔', '🍦', '🏷️', '⭐', '📦'];
                            @endphp
                            @foreach($presetIcons as $pIcon)
                                <button 
                                    type="button" 
                                    wire:click="$set('icon', '{{ $pIcon }}')"
                                    class="h-9 rounded-xl flex items-center justify-center text-base transition {{ $icon === $pIcon ? 'bg-[#4338CA] shadow-md scale-105' : 'hover:bg-[#25215A]' }}"
                                >
                                    {{ $pIcon }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Ikon Kustom</label>
                            <input type="text" wire:model="icon" placeholder="☕" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-center text-lg text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA]">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Urutan Indeks</label>
                            <input type="number" wire:model="order_index" placeholder="1" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA]">
                        </div>
                    </div>
                </div>

                <button 
                    wire:click="saveCategory"
                    class="w-full py-3.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95"
                >
                    {{ $categoryId ? 'Simpan Perubahan' : 'Terbitkan Kategori' }}
                </button>
            </div>
        </div>
    @endif
</div>

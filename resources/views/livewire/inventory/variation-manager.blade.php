<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#25215A] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <span>Master Varian & Opsi Tambahan</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Kelola opsi ukuran cup, level manis, topping, dan penambahan harga</p>
            </div>
        </div>
        <button 
            wire:click="openCreateModal"
            class="px-5 py-3 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg flex items-center justify-center gap-2 active:scale-95 transition shrink-0"
        >
            <x-icon name="plus" class="w-4 h-4" />
            <span>Tambah Varian</span>
        </button>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if(isset($templates) && $templates->count() > 0)
    <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-3xl p-5 shadow-md space-y-3">
        <h3 class="font-black text-xs uppercase tracking-widest text-slate-400">Template Library — Pakai Ulang Varian</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($templates as $tpl)
                <button wire:click="useTemplate('{{ $tpl->id }}')" class="px-3 py-2 rounded-xl bg-[#16192E] hover:bg-[#4338CA] border border-[#2E2A68] text-xs font-bold text-slate-200 hover:text-white transition flex items-center gap-1.5">
                    <span>{{ $tpl->name }}</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-white/10">{{ $tpl->selection_type }}</span>
                    <span class="text-[10px] opacity-70">{{ $tpl->options->count() }} opsi</span>
                </button>
            @endforeach
        </div>
        <p class="text-[10px] text-slate-500">Klik template untuk autopopulate form varian. Template dikelola via seeder atau admin.</p>
    </div>
    @endif

    <!-- Search Input -->
    <div class="relative w-full sm:w-80">
        <x-icon name="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
        <input 
            type="text" 
            wire:model.live.debounce.250ms="search"
            placeholder="Cari nama grup varian..."
            class="w-full pl-10 pr-4 py-2.5 bg-[#1E1B4B] border border-[#2E2A68] rounded-2xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-[#4338CA] transition"
        >
    </div>

    <!-- Variant List -->
    <div class="space-y-4">
        @forelse($variants as $variant)
            <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-3xl p-5 shadow-md hover:border-[#4338CA] transition space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-black text-sm text-white">{{ $variant->name }}</h4>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#4338CA]/30 text-indigo-300 border border-[#4338CA]/50">
                                {{ $variant->selection_type === 'SINGLE' ? 'Pilih Satu' : 'Multi Pilihan' }}
                            </span>
                            @if($variant->is_required)
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/40">
                                    Wajib
                                </span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-400 font-medium mt-1">
                            Menu Target: <span class="text-white font-bold">{{ $variant->product?->name ?? 'Semua Menu' }}</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button wire:click="openEditModal('{{ $variant->id }}')" class="px-3 py-1.5 bg-[#16192E] hover:bg-[#4338CA] text-slate-200 hover:text-white rounded-xl text-xs font-bold border border-[#2E2A68] transition flex items-center gap-1.5">
                            <x-icon name="edit" class="w-3.5 h-3.5" />
                            <span>Edit</span>
                        </button>
                        <button wire:click="deleteVariant('{{ $variant->id }}')" wire:confirm="Yakin ingin menghapus grup varian ini?" class="p-1.5 bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white rounded-xl text-xs font-bold border border-rose-500/30 transition" title="Hapus Varian">
                            <x-icon name="trash" class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>

                <!-- Variant Options Chips -->
                <div class="flex flex-wrap gap-2 pt-3 border-t border-[#2E2A68]">
                    @foreach($variant->options as $opt)
                        <div class="px-3 py-1.5 rounded-xl bg-[#16192E] border border-[#2E2A68] text-xs flex items-center gap-1.5">
                            <span class="font-bold text-white">{{ $opt->name }}</span>
                            @if($opt->price_modifier > 0)
                                <span class="text-[10px] font-black text-[#3EDAD7]">+Rp {{ number_format($opt->price_modifier, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="py-16 text-center text-slate-400 bg-[#1E1B4B] rounded-3xl border border-[#2E2A68]">
                <x-icon name="layers" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                <p class="text-sm font-semibold text-slate-300">Belum ada varian produk</p>
                <p class="text-xs text-slate-500 mt-0.5">Tambahkan grup varian seperti ukuran cup atau tingkat gula untuk menu outlet</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $variants->links() }}
    </div>

    <!-- Variant Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-md rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <h3 class="text-base font-black text-white">
                        {{ $variantId ? 'Ubah Varian' : 'Tambah Varian Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Produk Target</label>
                        <select wire:model="productId" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA]">
                            @forelse($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price, 0, ',', '.') }})</option>
                            @empty
                                <option value="">Semua Menu</option>
                            @endforelse
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nama Grup Varian</label>
                        <input type="text" wire:model="name" placeholder="cth: Ukuran Cup / Tingkat Manis" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA]">
                        @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Tipe Pilihan</label>
                            <select wire:model="selection_type" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA]">
                                <option value="SINGLE">Pilih Satu (Single)</option>
                                <option value="MULTIPLE">Pilih Banyak (Multiple)</option>
                            </select>
                        </div>
                        <div class="flex items-center pt-6 gap-2">
                            <input type="checkbox" id="isRequired" wire:model="is_required" class="w-4 h-4 rounded bg-[#16192E] border-[#2E2A68] text-[#4338CA] focus:ring-0">
                            <label for="isRequired" class="font-bold text-slate-300 cursor-pointer">Wajib Dipilih?</label>
                        </div>
                    </div>

                    <!-- Dynamic Options -->
                    <div class="space-y-3 pt-2 border-t border-[#2E2A68]">
                        <div class="flex items-center justify-between">
                            <label class="font-bold text-white uppercase tracking-wider text-[10px]">Daftar Pilihan / Opsi</label>
                            <button type="button" wire:click="addOption" class="text-[10px] font-black text-[#3EDAD7] hover:underline">+ Tambah Opsi</button>
                        </div>

                        @foreach($options as $index => $opt)
                            <div class="flex items-center gap-2 bg-[#16192E] p-2.5 rounded-2xl border border-[#2E2A68]">
                                <input type="text" wire:model="options.{{ $index }}.name" placeholder="Nama Opsi (cth: Large)" class="flex-1 px-3 py-2 bg-[#0F172A] border border-[#2E2A68] rounded-xl text-white text-xs placeholder-slate-500">
                                <input type="number" wire:model="options.{{ $index }}.price_modifier" placeholder="+Harga (0)" class="w-24 px-3 py-2 bg-[#0F172A] border border-[#2E2A68] rounded-xl text-white text-xs placeholder-slate-500 font-bold text-[#3EDAD7]">
                                <button type="button" wire:click="removeOption({{ $index }})" class="text-rose-400 hover:text-rose-300 font-bold px-2">✕</button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button 
                    wire:click="saveVariant"
                    class="w-full py-3.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95"
                >
                    {{ $variantId ? 'Simpan Perubahan' : 'Simpan Varian' }}
                </button>
            </div>
        </div>
    @endif
</div>

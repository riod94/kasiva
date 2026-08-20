<div class="space-y-6">
    <!-- Header Page & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#25215A] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition" title="Kembali ke Inventaris">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <span>Katalog Produk & Resep HPP</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Kelola daftar menu, foto produk, varian kustom, dan kalkulasi HPP otomatis</p>
            </div>
        </div>
        <button 
            wire:click="openCreateModal"
            class="px-5 py-3 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg flex items-center justify-center gap-2 active:scale-95 transition shrink-0"
        >
            <x-icon name="plus" class="w-4 h-4" />
            <span>Tambah Produk</span>
        </button>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Search & Category Filter Bar -->
    <div class="flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div class="relative w-full sm:w-80">
            <x-icon name="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
            <input 
                type="text" 
                wire:model.live.debounce.250ms="search"
                placeholder="Cari nama produk atau SKU..."
                class="w-full pl-10 pr-4 py-2.5 bg-[#1E1B4B] border border-[#2E2A68] rounded-2xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-[#4338CA] transition"
            >
        </div>

        <!-- Category Filter Pills -->
        <div class="flex gap-1.5 overflow-x-auto w-full sm:w-auto pb-1 scrollbar-hide">
            <button 
                wire:click="$set('categoryFilter', 'ALL')"
                class="px-4 py-2 rounded-xl font-black text-xs uppercase tracking-wider transition shrink-0 {{ $categoryFilter === 'ALL' ? 'bg-[#4338CA] text-white shadow-md' : 'bg-[#1E1B4B] text-slate-400 hover:text-white border border-[#2E2A68]' }}"
            >
                Semua Kategori
            </button>
            @foreach($categories as $cat)
                <button 
                    wire:click="$set('categoryFilter', '{{ $cat->id }}')"
                    class="px-4 py-2 rounded-xl font-black text-xs uppercase tracking-wider transition whitespace-nowrap shrink-0 {{ $categoryFilter == $cat->id ? 'bg-[#4338CA] text-white shadow-md' : 'bg-[#1E1B4B] text-slate-400 hover:text-white border border-[#2E2A68]' }}"
                >
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Product Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($products as $product)
            @php $tier = $product->margin_tier; @endphp
            <div class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] rounded-3xl p-4 shadow-md transition flex flex-col justify-between group">
                <div class="space-y-3">
                    <!-- Image & Status Row -->
                    <div class="relative aspect-square w-full rounded-2xl bg-[#0F172A] border border-[#2E2A68] overflow-hidden flex items-center justify-center">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="flex flex-col items-center justify-center gap-1 text-slate-500">
                                <x-icon name="package" class="w-8 h-8 opacity-40" />
                                <span class="text-[10px] font-bold uppercase tracking-wider">Tanpa Foto</span>
                            </div>
                        @endif

                        <button 
                            wire:click="toggleActive('{{ $product->id }}')" 
                            class="absolute top-2.5 right-2.5 text-[10px] font-black px-2.5 py-1 rounded-full shadow-md backdrop-blur-md transition {{ $product->is_active ? 'bg-emerald-500/80 text-white' : 'bg-slate-700/80 text-slate-300' }}"
                        >
                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </div>

                    <!-- Details -->
                    <div class="space-y-1">
                        <div class="flex items-start justify-between gap-1">
                            <h4 class="font-black text-sm text-white leading-tight line-clamp-1">{{ $product->name }}</h4>
                        </div>
                        <p class="text-[11px] text-slate-400 font-mono">
                            {{ $product->sku }} • {{ $product->category?->name ?? 'Umum' }}
                        </p>
                    </div>

                    <!-- Price & Margin Box -->
                    <div class="bg-[#16192E] p-3 rounded-2xl border border-[#2E2A68] space-y-1.5 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Harga Jual:</span>
                            <span class="font-black text-[#3EDAD7] text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-400">Modal HPP:</span>
                            <span class="font-bold text-amber-400">Rp {{ number_format($product->hpp, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[10px] pt-1.5 border-t border-[#2E2A68]">
                            <span class="text-slate-400">Margin:</span>
                            <span class="font-black px-2 py-0.5 rounded border {{ $tier['color'] }}">
                                {{ $tier['label'] }} ({{ $tier['margin_percent'] }}%)
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 pt-3 mt-3 border-t border-[#2E2A68]">
                    <button 
                        wire:click="openEditModal('{{ $product->id }}')" 
                        class="flex-1 py-2 bg-[#16192E] hover:bg-[#4338CA] text-slate-200 hover:text-white rounded-xl text-xs font-bold border border-[#2E2A68] transition flex items-center justify-center gap-1.5"
                    >
                        <x-icon name="edit" class="w-3.5 h-3.5" />
                        <span>Edit</span>
                    </button>
                    <button 
                        wire:click="deleteProduct('{{ $product->id }}')" 
                        wire:confirm="Yakin ingin menghapus produk {{ $product->name }}?" 
                        class="p-2 bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white rounded-xl text-xs font-bold border border-rose-500/30 transition"
                        title="Hapus Produk"
                    >
                        <x-icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-[#1E1B4B] rounded-3xl border border-[#2E2A68]">
                <x-icon name="package" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                <p class="text-sm font-bold text-slate-300">Tidak ada produk dalam kategori ini</p>
                <p class="text-xs text-slate-500 mt-0.5">Silakan tambahkan produk baru untuk memulai</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>

    <!-- ═══════════════ Create/Edit Product Modal ═══════════════ -->
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-lg rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <h3 class="text-base font-black text-white">
                        {{ $productId ? 'Ubah Data Produk & Resep' : 'Tambah Produk Menu Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- Image Upload Area -->
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Foto Produk</label>
                        
                        @if($image)
                            <div class="relative w-full h-36 bg-[#16192E] rounded-2xl overflow-hidden border border-[#2E2A68] flex items-center justify-center">
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                                <button type="button" wire:click="removeImage" class="absolute top-2 right-2 bg-rose-600 text-white p-1.5 rounded-xl text-xs font-bold shadow-md">
                                    <x-icon name="trash" class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        @elseif($image_url)
                            <div class="relative w-full h-36 bg-[#16192E] rounded-2xl overflow-hidden border border-[#2E2A68] flex items-center justify-center">
                                <img src="{{ $image_url }}" class="w-full h-full object-cover">
                                <button type="button" wire:click="removeImage" class="absolute top-2 right-2 bg-rose-600 text-white p-1.5 rounded-xl text-xs font-bold shadow-md">
                                    <x-icon name="trash" class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        @else
                            <label class="flex flex-col items-center justify-center gap-2 p-4 border-2 border-dashed border-[#2E2A68] hover:border-[#4338CA] rounded-2xl cursor-pointer bg-[#16192E] transition group">
                                <input type="file" wire:model="image" accept="image/*" class="hidden">
                                <x-icon name="upload" class="w-6 h-6 text-slate-400 group-hover:text-indigo-400 transition" />
                                <div class="text-center">
                                    <span class="text-xs font-bold text-slate-200">Unggah Gambar Produk</span>
                                    <p class="text-[10px] text-slate-400 mt-0.5">PNG, JPG, Maks 2MB</p>
                                </div>
                            </label>
                        @endif
                        @error('image') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- General Fields -->
                    <div>
                        <label class="block font-bold text-slate-300 mb-1">Nama Produk</label>
                        <input type="text" wire:model="name" placeholder="cth: Matcha Latte Premium" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA]">
                        @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-300 mb-1">Kategori Menu</label>
                            <select wire:model="category_id" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white focus:outline-none focus:border-[#4338CA]">
                                <option value="">Pilih Kategori...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-300 mb-1">SKU / Barcode</label>
                            <input type="text" wire:model="sku" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl font-mono text-white focus:outline-none focus:border-[#4338CA]">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-300 mb-1">Harga Jual (Rp)</label>
                            <input type="number" wire:model.live="price" placeholder="12000" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl focus:outline-none focus:border-[#4338CA] font-bold text-[#3EDAD7]">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-300 mb-1">Stok Awal</label>
                            <input type="number" wire:model="current_stock" placeholder="100" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white focus:outline-none focus:border-[#4338CA]">
                        </div>
                    </div>

                    <!-- Recipe BOM Builder (Auto HPP) -->
                    <div class="bg-[#16192E] p-4 rounded-2xl border border-[#2E2A68] space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="font-extrabold text-white">Komposisi Resep Bahan Baku (BOM)</label>
                            <button wire:click="addMaterialRow" type="button" class="text-[11px] font-bold text-[#3EDAD7] hover:underline flex items-center gap-1">
                                <x-icon name="plus" class="w-3 h-3" />
                                <span>Tambah Bahan</span>
                            </button>
                        </div>

                        <div class="space-y-2">
                            @foreach($selectedMaterials as $index => $matRow)
                                <div class="flex items-center gap-2">
                                    <select wire:model="selectedMaterials.{{ $index }}.material_id" wire:change="calculateHpp" class="flex-1 px-3 py-2 bg-[#0F172A] border border-[#2E2A68] rounded-xl text-white text-[11px] focus:outline-none focus:border-[#4338CA]">
                                        <option value="">Pilih Bahan...</option>
                                        @foreach($allMaterials as $m)
                                            <option value="{{ $m->id }}">{{ $m->name }} (Rp {{ number_format($m->avg_cost, 0) }}/{{ $m->unit }})</option>
                                        @endforeach
                                    </select>
                                    <input type="number" step="any" wire:model="selectedMaterials.{{ $index }}.quantity" wire:change="calculateHpp" placeholder="Takaran" class="w-20 px-3 py-2 bg-[#0F172A] border border-[#2E2A68] rounded-xl text-white text-[11px] focus:outline-none focus:border-[#4338CA]">
                                    <button wire:click="removeMaterialRow({{ $index }})" type="button" class="text-rose-400 p-1 hover:text-rose-300">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-between items-center text-xs font-bold pt-2 border-t border-[#2E2A68]">
                            <span class="text-slate-400">Total HPP Dihitung:</span>
                            <span class="text-amber-400 font-black">Rp {{ number_format($hpp, 0, ',', '.') }}</span>
                        </div>

                        <!-- Real-time Margin Preview -->
                        @if($price > 0)
                            @php $marginPct = round((($price - $hpp) / $price) * 100, 1); @endphp
                            <div class="flex justify-between items-center text-xs font-bold pt-1 text-slate-300">
                                <span>Estimasi Margin:</span>
                                <span class="{{ $marginPct >= 45 ? 'text-emerald-400' : ($marginPct >= 30 ? 'text-amber-400' : 'text-rose-400') }}">
                                    {{ $marginPct }}% ({{ $marginPct >= 72 ? 'Optimal' : ($marginPct >= 45 ? 'Sehat' : ($marginPct >= 30 ? 'Tipis' : 'Kritis')) }})
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <button 
                    wire:click="saveProduct"
                    class="w-full py-3.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95"
                >
                    {{ $productId ? 'Simpan Perubahan Produk' : 'Terbitkan Produk Baru' }}
                </button>
            </div>
        </div>
    @endif
</div>

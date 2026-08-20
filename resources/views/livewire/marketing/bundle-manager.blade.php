<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('marketing.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#25215A] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <span>Paket Bundle & Kombo Hemat</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Buat paket gabungan menu dengan kalkulasi modal HPP otomatis</p>
            </div>
        </div>
        <button 
            wire:click="openCreateModal"
            class="px-5 py-3 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg flex items-center justify-center gap-2 active:scale-95 transition shrink-0"
        >
            <x-icon name="plus" class="w-4 h-4" />
            <span>Buat Bundle</span>
        </button>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Bundles Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($bundles as $bundle)
            <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-3xl p-5 shadow-md hover:border-[#4338CA] transition space-y-4 flex flex-col justify-between group">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h4 class="font-black text-sm text-white">{{ $bundle->name }}</h4>
                            <p class="text-base font-black text-[#3EDAD7] mt-0.5">
                                Rp {{ number_format($bundle->price, 0, ',', '.') }}
                            </p>
                        </div>
                        <button 
                            wire:click="toggleActive('{{ $bundle->id }}')" 
                            class="text-[10px] font-black px-2.5 py-1 rounded-full shrink-0 transition {{ $bundle->is_active ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/30' : 'bg-slate-500/10 text-slate-400 border border-slate-500/30' }}"
                        >
                            {{ $bundle->is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </div>

                    <!-- Bundle Items List -->
                    <div class="bg-[#16192E] p-3 rounded-2xl border border-[#2E2A68] space-y-2 text-xs">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Komposisi Paket:</p>
                        @if(is_array($bundle->items))
                            @foreach($bundle->items as $item)
                                <div class="flex justify-between text-slate-300 text-[11px]">
                                    <span>• {{ $item['product_name'] ?? 'Produk' }}</span>
                                    <span class="font-bold text-white">{{ $item['quantity'] ?? 1 }}x</span>
                                </div>
                            @endforeach
                        @endif
                        <div class="pt-2 border-t border-[#2E2A68] flex justify-between text-[11px] font-bold text-amber-400">
                            <span>Estimasi HPP:</span>
                            <span>Rp {{ number_format($bundle->cogs, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-3 border-t border-[#2E2A68]">
                    <button wire:click="openEditModal('{{ $bundle->id }}')" class="flex-1 py-2 bg-[#16192E] hover:bg-[#4338CA] text-slate-200 hover:text-white rounded-xl text-xs font-bold border border-[#2E2A68] transition flex items-center justify-center gap-1.5">
                        <x-icon name="edit" class="w-3.5 h-3.5" />
                        <span>Edit</span>
                    </button>
                    <button wire:click="deleteBundle('{{ $bundle->id }}')" wire:confirm="Yakin ingin menghapus paket bundle ini?" class="p-2 bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white rounded-xl text-xs font-bold border border-rose-500/30 transition" title="Hapus Bundle">
                        <x-icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-[#1E1B4B] rounded-3xl border border-[#2E2A68]">
                <x-icon name="package" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                <p class="text-sm font-semibold text-slate-300">Belum ada paket bundle produk</p>
                <p class="text-xs text-slate-500 mt-0.5">Buat paket hemat untuk mendorong pembelian dengan nilai transaksi lebih tinggi</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $bundles->links() }}
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-md rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <h3 class="text-base font-black text-white">
                        {{ $bundleId ? 'Ubah Paket Bundle' : 'Buat Paket Bundle Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nama Paket Bundle</label>
                        <input type="text" wire:model="name" placeholder="cth: Paket Kombo Hemat (Matcha + Toast)" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA]">
                        @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Harga Jual Paket (Rp)</label>
                        <input type="number" wire:model="price" placeholder="25000" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-[#3EDAD7] font-black text-sm focus:outline-none focus:border-[#4338CA]">
                    </div>

                    <!-- Products in Bundle List -->
                    <div class="space-y-3 pt-2 border-t border-[#2E2A68]">
                        <div class="flex items-center justify-between">
                            <label class="font-bold text-white uppercase tracking-wider text-[10px]">Komposisi Menu</label>
                            <button type="button" wire:click="addItem" class="text-[10px] font-black text-[#3EDAD7] hover:underline">+ Tambah Menu</button>
                        </div>

                        @foreach($items as $index => $item)
                            <div class="flex items-center gap-2 bg-[#16192E] p-2.5 rounded-2xl border border-[#2E2A68]">
                                <select wire:model="items.{{ $index }}.product_id" wire:change="calculateBundleCogs" class="flex-1 px-3 py-2 bg-[#0F172A] border border-[#2E2A68] rounded-xl text-white text-xs">
                                    <option value="">Pilih Menu...</option>
                                    @foreach($allProducts as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price, 0) }})</option>
                                    @endforeach
                                </select>
                                <input type="number" min="1" wire:model="items.{{ $index }}.quantity" wire:change="calculateBundleCogs" class="w-16 px-2 py-2 bg-[#0F172A] border border-[#2E2A68] rounded-xl text-white text-xs text-center font-bold">
                                <button type="button" wire:click="removeItem({{ $index }})" class="text-rose-400 hover:text-rose-300 font-bold px-2">✕</button>
                            </div>
                        @endforeach

                        <div class="pt-2 flex justify-between text-xs font-bold text-amber-400 border-t border-[#2E2A68]">
                            <span>Estimasi HPP Total:</span>
                            <span>Rp {{ number_format($cogs, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <button 
                    wire:click="saveBundle"
                    class="w-full py-3.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95"
                >
                    {{ $bundleId ? 'Simpan Perubahan' : 'Terbitkan Paket Bundle' }}
                </button>
            </div>
        </div>
    @endif
</div>

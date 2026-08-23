<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <a href="{{ route('history.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#2A3155] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition shrink-0">
            <x-icon name="arrow-left" class="w-4 h-4" />
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-black text-white flex items-center gap-2">
                <span>Input Transaksi Lampau</span>
                <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30">BACKDATE</span>
            </h1>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Pilih produk via keranjang — HPP otomatis & tidak mengganggu stok (opsional)</p>
        </div>
        <div class="hidden sm:flex items-center gap-2 text-[11px] font-bold">
            <span class="px-3 py-2 rounded-xl bg-[#16192E] border border-[#2E2A68] text-slate-300">Total: <span class="text-[#3EDAD7]">Rp {{ number_format($cartTotal,0,',','.') }}</span></span>
            <span class="px-3 py-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300">HPP: Rp {{ number_format($cartHpp,0,',','.') }}</span>
        </div>
    </div>

    @error('cart') <div class="bg-rose-500/10 border border-rose-500/30 text-rose-300 px-4 py-3 rounded-2xl text-xs font-bold">{{ $message }}</div> @enderror

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <!-- Left: Product Picker -->
        <div class="lg:col-span-3 space-y-3">
            <div class="bg-[#1E1B4B] p-4 rounded-3xl border border-[#2E2A68] shadow-xl space-y-3">
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari produk (Matcha, Kopi, Cendol)..." class="w-full pl-10 pr-3 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-[#00AAA6]" />
                    </div>
                    <select wire:model.live="categoryFilter" class="px-3 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-xs font-bold text-slate-200">
                        <option value="all">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-[52vh] overflow-y-auto pr-1">
                    @forelse($products as $p)
                        <button wire:click="addToCart('{{ $p->id }}')" class="text-left bg-[#16192E] hover:bg-[#2A3155] border border-[#2E2A68] hover:border-[#00AAA6] rounded-2xl p-3 transition group active:scale-95">
                            <p class="text-xs font-black text-white group-hover:text-[#3EDAD7] line-clamp-2 leading-tight">{{ $p->name }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $p->category?->name ?? 'Tanpa Kategori' }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs font-black text-[#3EDAD7]">Rp {{ number_format($p->price,0,',','.') }}</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-[#1E1B4B] border border-[#2E2A68] text-slate-400">HPP {{ number_format($p->hpp,0,',','.') }}</span>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full py-10 text-center text-slate-500 text-xs">Tidak ada produk</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Cart + Date/Time + Payment -->
        <div class="lg:col-span-2 space-y-3">
            <div class="bg-[#1E1B4B] p-5 rounded-3xl border border-[#2E2A68] shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-white">Keranjang ({{ count($cart) }} item)</h3>
                    <span class="text-[10px] font-black px-2 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">{{ count($cart) ? 'Itemized HPP' : 'Kosong' }}</span>
                </div>

                @if(empty($cart))
                    <div class="py-8 text-center text-slate-500 bg-[#16192E] rounded-2xl border border-dashed border-[#2E2A68]">
                        <p class="text-xs font-bold">Belum ada item</p>
                        <p class="text-[11px] mt-1">Klik produk di kiri untuk menambah</p>
                    </div>
                @else
                    <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                        @foreach($cart as $idx => $item)
                            <div class="flex items-center gap-2 bg-[#16192E] border border-[#2E2A68] rounded-2xl p-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-black text-white truncate">{{ $item['name'] }}</p>
                                    <p class="text-[10px] text-slate-400">Rp {{ number_format($item['price'],0,',','.') }} • HPP {{ number_format($item['hpp'],0,',','.') }}</p>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button wire:click="decrementQty({{ $idx }})" class="w-7 h-7 rounded-xl bg-[#1E1B4B] border border-[#2E2A68] text-white font-black text-xs">−</button>
                                    <span class="w-7 text-center text-xs font-black text-white">{{ $item['qty'] }}</span>
                                    <button wire:click="incrementQty({{ $idx }})" class="w-7 h-7 rounded-xl bg-[#00AAA6] text-white font-black text-xs">+</button>
                                </div>
                                <button wire:click="removeFromCart({{ $idx }})" class="w-7 h-7 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs">×</button>
                            </div>
                        @endforeach
                    </div>
                    <div class="space-y-1.5 pt-3 border-t border-[#2E2A68] text-xs">
                        <div class="flex justify-between font-bold text-slate-300"><span>Subtotal</span><span class="text-white">Rp {{ number_format($cartTotal,0,',','.') }}</span></div>
                        <div class="flex justify-between font-bold text-slate-400"><span>Total HPP</span><span class="text-amber-300">Rp {{ number_format($cartHpp,0,',','.') }}</span></div>
                        <div class="flex justify-between font-black text-emerald-300"><span>Est. Profit</span><span>Rp {{ number_format($cartTotal - $cartHpp,0,',','.') }}</span></div>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-3 pt-2 border-t border-[#2E2A68]">
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Tanggal</label>
                        <input type="date" wire:model="transactionDate" class="w-full px-3 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-xs text-white font-bold focus:outline-none focus:border-[#00AAA6]">
                        @error('transactionDate') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Jam</label>
                        <input type="time" wire:model="transactionTime" class="w-full px-3 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-xs text-white font-bold focus:outline-none focus:border-[#00AAA6]">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Metode Pembayaran</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['CASH'=>'Tunai','QRIS'=>'QRIS','GOFOOD'=>'GoFood','GRABFOOD'=>'Grab','SHOPEEFOOD'=>'Shopee'] as $val=>$label)
                            <button type="button" wire:click="$set('paymentMethod','{{ $val }}')" class="py-2.5 rounded-xl border font-bold text-xs transition {{ $paymentMethod===$val ? 'bg-[#00AAA6] text-white border-[#00AAA6]' : 'bg-[#16192E] text-slate-400 border-[#2E2A68] hover:text-white' }}">{{ $label }}</button>
                        @endforeach
                    </div>
                </div>

                <label class="flex items-center gap-2 text-xs font-bold text-slate-300 cursor-pointer">
                    <input type="checkbox" wire:model.live="affectStock" class="rounded border-[#2E2A68] bg-[#16192E] text-[#00AAA6]">
                    <span>Pengaruhi stok saat ini (kurangi bahan baku)</span>
                </label>
                <p class="text-[10px] text-slate-500 -mt-2">Default <b>tidak</b> mengurangi stok — hanya catatan omset lampau.</p>

                <button wire:click="saveTransaction" @if(empty($cart)) disabled @endif class="w-full py-4 bg-[#00AAA6] hover:bg-[#008F8C] disabled:bg-slate-700 disabled:text-slate-400 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-xl transition active:scale-95 flex items-center justify-center gap-2">
                    Simpan Transaksi Lampau
                </button>
            </div>
        </div>
    </div>
</div>

<div class="space-y-4">
    <!-- ═══════════════ Header & Quick Shortcuts ═══════════════ -->
    <div class="flex flex-col gap-3.5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-black text-xl md:text-2xl tracking-tight text-white flex items-center gap-2">
                    <span>{{ $this->greeting }}, {{ auth()->user()?->name ?? 'Kasir' }}</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Sistem kasir siap melayani transaksi outlet</p>
            </div>
            <div class="hidden sm:flex items-center gap-2">
                <a href="{{ route('history.index') }}" class="px-3.5 py-2 bg-[#1E1B4B] hover:bg-[#4338CA] border border-[#2E2A68] text-xs font-bold rounded-xl text-slate-300 hover:text-white transition shadow-sm flex items-center gap-1.5">
                    <x-icon name="receipt" class="w-3.5 h-3.5" />
                    <span>Riwayat</span>
                </a>
                <a href="{{ route('expenses.index') }}" class="px-3.5 py-2 bg-[#1E1B4B] hover:bg-[#F97316] border border-[#2E2A68] text-xs font-bold rounded-xl text-slate-300 hover:text-white transition shadow-sm flex items-center gap-1.5">
                    <x-icon name="wallet" class="w-3.5 h-3.5" />
                    <span>Pengeluaran</span>
                </a>
            </div>
        </div>

        <!-- Mobile Quick Shortcuts (2 Grid Cards) -->
        <div class="grid grid-cols-2 gap-3 sm:hidden">
            <a href="{{ route('history.index') }}" class="flex items-center gap-3 p-3 rounded-2xl bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] transition text-left group shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-[#4338CA]/20 text-indigo-300 border border-[#4338CA]/40 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                    <x-icon name="receipt" class="w-4 h-4 text-indigo-400" />
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-[9px] font-black uppercase tracking-widest text-indigo-400 leading-none mb-1">Cek Data</span>
                    <span class="text-xs font-black text-white truncate leading-tight">Riwayat</span>
                </div>
            </a>

            <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 p-3 rounded-2xl bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#F97316] transition text-left group shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-orange-500/20 text-orange-400 border border-orange-500/40 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                    <x-icon name="wallet" class="w-4 h-4 text-orange-400" />
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-[9px] font-black uppercase tracking-widest text-orange-400 leading-none mb-1">Catat</span>
                    <span class="text-xs font-black text-white truncate leading-tight">Pengeluaran</span>
                </div>
            </a>
        </div>
    </div>

    <!-- ═══════════════ Search Box & Barcode Scanner ═══════════════ -->
    <div class="flex gap-2">
        <div class="relative flex-1">
            <x-icon name="search" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
            <input 
                type="text" 
                wire:model.live.debounce.250ms="searchQuery"
                placeholder="Cari produk menu atau masukkan SKU..."
                class="w-full h-12 pl-11 pr-4 bg-[#1E1B4B] border border-[#2E2A68] rounded-2xl text-xs md:text-sm text-white placeholder-slate-400 focus:outline-none focus:border-[#4338CA] shadow-inner"
            >
        </div>
    </div>

    <!-- ═══════════════ Category Filter Horizontal Scroll ═══════════════ -->
    <div class="flex gap-2 overflow-x-auto pb-1.5 -mx-4 px-4 md:-mx-0 md:px-0 scrollbar-hide">
        <button 
            wire:click="selectCategory('ALL')"
            class="px-4 py-2.5 rounded-2xl font-black text-xs uppercase tracking-wider whitespace-nowrap transition flex items-center gap-2 {{ $selectedCategory === 'ALL' ? 'bg-[#4338CA] text-white shadow-md' : 'bg-[#1E1B4B] text-slate-300 hover:bg-[#25215A] border border-[#2E2A68]' }}"
        >
            <x-icon name="store" class="w-3.5 h-3.5" />
            <span>Semua Menu</span>
        </button>

        @foreach($categories as $category)
            <button 
                wire:click="selectCategory('{{ $category->id }}')"
                class="px-4 py-2.5 rounded-2xl font-black text-xs uppercase tracking-wider whitespace-nowrap transition flex items-center gap-2 {{ $selectedCategory == $category->id ? 'bg-[#4338CA] text-white shadow-md' : 'bg-[#1E1B4B] text-slate-300 hover:bg-[#25215A] border border-[#2E2A68]' }}"
            >
                <span>{{ $category->name }}</span>
            </button>
        @endforeach
    </div>

    <!-- ═══════════════ Multi-Cart Tabs (3 slot + Hold) ═══════════════ -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        @foreach($this->carts as $idx => $c)
            <button
                wire:click="switchCart({{ $idx }})"
                class="px-3.5 py-2 rounded-xl text-xs font-black border whitespace-nowrap flex items-center gap-1.5 transition {{ $idx === $activeCartIndex ? 'bg-[#4338CA] text-white border-[#4338CA] shadow-md' : 'bg-[#1E1B4B] text-slate-300 border-[#2E2A68] hover:border-[#4338CA]' }}"
            >
                <span>{{ $c['name'] }}</span>
                @php $cnt = count($c['items'] ?? []); @endphp
                @if($cnt > 0)<span class="px-1.5 py-0.5 rounded-full bg-white/20 text-[10px]">{{ $cnt }}</span>@endif
            </button>
        @endforeach
        @if(count($carts) < 3)
            <button wire:click="createNewCart" class="px-3 py-2 rounded-xl text-xs font-black bg-[#16192E] text-slate-300 border border-dashed border-[#2E2A68] hover:border-[#4338CA] flex items-center gap-1">
                <x-icon name="plus" class="w-3.5 h-3.5" /> <span>Baru</span>
            </button>
        @endif
        <button wire:click="holdCart" class="ml-auto px-3 py-2 rounded-xl text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 hover:bg-amber-500/30 hidden sm:flex items-center gap-1">
            <span>Hold</span>
        </button>
        @if(count($carts) > 1)
            <button wire:click="closeCart({{ $activeCartIndex }})" class="px-2 py-2 rounded-xl text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30 hover:bg-rose-600 hover:text-white" title="Tutup cart aktif">✕</button>
        @endif
    </div>

    <!-- ═══ Member Loyalty — ringkas agar tidak bersaing dengan pencarian produk ═══ -->
    @php $lm = $this->linkedMember; @endphp
    <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-2xl p-3 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-9 h-9 rounded-xl bg-[#00AAA6]/15 border border-[#00AAA6]/30 text-[#3EDAD7] flex items-center justify-center shrink-0">
                    <x-icon name="users" class="w-4 h-4" />
                </span>
                <div class="min-w-0">
                    <p class="text-[10px] uppercase tracking-widest font-black text-slate-500">Member transaksi</p>
                    <p class="text-xs font-black text-white truncate">{{ $lm?->name ?: 'Belum dipilih' }}</p>
                    @if($lm && $memberProgress)
                        <p class="text-[10px] font-bold {{ $memberProgress['isEligibleForReward'] ? 'text-emerald-300' : 'text-slate-400' }}">{{ $memberProgress['currentStamps'] }}/{{ $memberProgress['targetStamps'] }} stamp</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                @if($lm)
                    <button wire:click="unlinkMember" class="px-3 py-2 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs font-black hover:bg-rose-600 hover:text-white">Lepas</button>
                @endif
                <button wire:click="$set('showMemberScanner', true)" class="px-3.5 py-2 rounded-xl bg-[#00AAA6] hover:bg-[#3EDAD7] text-[#272D48] text-xs font-black transition flex items-center gap-2" aria-label="Scan QR member">
                    <x-icon name="qr-code" class="w-4 h-4" />
                    <span>Scan QR Member</span>
                </button>
            </div>
        </div>

        @if($showMemberScanner)
            <div class="mt-3 pt-3 border-t border-[#2E2A68] flex gap-2" wire:transition>
                <div class="relative flex-1">
                    <x-icon name="qr-code" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-[#3EDAD7]" />
                    <input type="text" wire:model="memberScanInput" wire:keydown.enter="searchMember" placeholder="Scan QR member atau ketik nomor HP" aria-label="Cari member berdasarkan QR atau nomor HP" class="w-full pl-9 pr-3 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                </div>
                <button wire:click="searchMember" class="px-4 py-2.5 bg-[#00AAA6] hover:bg-[#3EDAD7] text-[#272D48] font-black text-xs rounded-xl">Temukan</button>
            </div>
        @endif
    </div>

    @if($lm && !empty($availableRewards))
        <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-2xl p-3 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
            <div class="flex items-center gap-2 text-emerald-300 font-black text-xs"><span>🎁 {{ count($availableRewards) }} Reward Tersedia</span>@if($appliedRewardId)<span class="px-2 py-1 rounded-full bg-emerald-500 text-white text-[10px]">Dipakai</span>@endif</div>
            <div class="flex gap-2 flex-wrap">
                @foreach($availableRewards as $rw)
                    <button wire:click="{{ ($appliedRewardId===$rw['id']) ? 'removeReward' : "applyReward('{$rw['id']}')" }}" class="px-3 py-2 rounded-xl text-xs font-black border {{ ($appliedRewardId===$rw['id']) ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-[#1E1B4B] text-emerald-300 border-emerald-500/30 hover:bg-emerald-500/20' }}">
                        {{ ($appliedRewardId===$rw['id']) ? 'Batalkan' : 'Pakai Reward' }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

        <!-- ═══════════════ Layout Grid: Products + Cart Sidebar ═══════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
        
        <!-- Product Grid -->
        <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
            @forelse($products as $product)
                <div 
                    wire:click="handleProductClick('{{ $product->id }}')"
                    class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] rounded-2xl p-3 flex flex-col justify-between cursor-pointer transition-all duration-200 active:scale-[0.97] group shadow-md hover:shadow-indigo-500/10 relative overflow-hidden"
                >
                    <!-- Product Image Area -->
                    <div class="aspect-square w-full rounded-xl bg-[#0F172A] flex items-center justify-center relative overflow-hidden mb-2.5 border border-[#2E2A68]/60">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <x-icon name="package" class="w-10 h-10 text-slate-600 opacity-50" />
                        @endif

                        @if($product->variants && $product->variants->isNotEmpty())
                            <span class="absolute top-1.5 right-1.5 text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full bg-[#1E1B4B]/90 text-[#3EDAD7] border border-[#3EDAD7]/40 shadow-sm">
                                Opsi
                            </span>
                        @endif

                        <button class="absolute bottom-2 right-2 w-7 h-7 rounded-full bg-white text-[#4338CA] group-hover:bg-[#4338CA] group-hover:text-white font-black text-sm flex items-center justify-center shadow-md transition">
                            <x-icon name="plus" class="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <!-- Product Info -->
                    <div class="space-y-1">
                        <h4 class="font-black text-xs text-white leading-tight line-clamp-2">{{ $product->name }}</h4>
                        <p class="font-black text-[#3EDAD7] text-xs">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </p>
                        
                        <div class="flex flex-wrap gap-1 pt-1">
                            @if($product->hpp > 0)
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md bg-[#16192E] text-slate-400 border border-[#2E2A68]">
                                    HPP {{ number_format($product->hpp / 1000, 0) }}k
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-slate-400 bg-[#1E1B4B] rounded-3xl border border-[#2E2A68]">
                    <x-icon name="search" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                    <p class="font-bold text-sm text-slate-200">Tidak ada produk ditemukan</p>
                    <p class="text-xs text-slate-400 mt-1">Coba kata kunci pencarian atau kategori lain</p>
                </div>
            @endforelse
        </div>

        <!-- ═══════════════ Cart Sidebar (Desktop) ═══════════════ -->
        <div class="hidden lg:flex flex-col bg-[#1E1B4B] border border-[#2E2A68] rounded-3xl p-5 shadow-xl sticky top-20 space-y-4">
            <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                <div class="flex items-center gap-2">
                    <x-icon name="shopping-bag" class="w-4 h-4 text-indigo-400" />
                    <h3 class="font-black text-sm text-white">Keranjang Pesanan</h3>
                </div>
                @if(count($cart) > 0)
                    <button wire:click="clearCart" class="text-[10px] font-black uppercase tracking-wider text-rose-400 hover:text-rose-300 transition">
                        Kosongkan
                    </button>
                @endif
            </div>

            @if($this->linkedMember)
                <div class="p-3 rounded-2xl bg-[#4338CA]/15 border border-[#4338CA]/30 flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-lg bg-[#4338CA] text-white flex items-center justify-center font-black text-[10px]">{{ strtoupper(substr($this->linkedMember->name ?: 'M',0,1)) }}</span>
                    <div class="flex-1 min-w-0"><p class="font-black text-xs text-white truncate">{{ $this->linkedMember->name ?: $this->linkedMember->qr_code }}</p><p class="text-[10px] text-indigo-200 truncate">{{ $this->linkedMember->qr_code }}</p></div>
                    <button wire:click="unlinkMember" class="text-slate-400 hover:text-white text-xs">✕</button>
                </div>
            @endif

            <!-- Cart Items List -->
            <div class="space-y-3 max-h-[380px] overflow-y-auto pr-1">
                @forelse($cart as $key => $item)
                    <div class="bg-[#16192E] p-3 rounded-2xl border border-[#2E2A68] flex items-center justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <h5 class="font-black text-xs text-white truncate">{{ $item['name'] }}</h5>
                            <p class="text-[11px] font-black text-[#3EDAD7] mt-0.5">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </p>
                        </div>

                        <!-- Quantity Stepper -->
                        <div class="flex items-center gap-1 bg-[#0F172A] p-1 rounded-xl border border-[#2E2A68]">
                            <button wire:click="updateQuantity('{{ $key }}', {{ $item['qty'] - 1 }})" class="w-6 h-6 rounded-lg bg-[#1E1B4B] hover:bg-rose-600 text-white font-black text-xs flex items-center justify-center transition">
                                -
                            </button>
                            <span class="w-6 text-center font-black text-xs text-white">{{ $item['qty'] }}</span>
                            <button wire:click="updateQuantity('{{ $key }}', {{ $item['qty'] + 1 }})" class="w-6 h-6 rounded-lg bg-[#4338CA] hover:bg-[#3730A3] text-white font-black text-xs flex items-center justify-center transition">
                                +
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 space-y-2">
                        <x-icon name="shopping-bag" class="w-8 h-8 mx-auto opacity-30" />
                        <p class="text-xs font-bold">Keranjang masih kosong</p>
                        <p class="text-[10px] text-slate-500">Pilih menu untuk menambahkan pesanan</p>
                    </div>
                @endforelse
            </div>

            <!-- Summary & Checkout Button -->
            @if(count($cart) > 0)
                <div class="border-t border-[#2E2A68] pt-3 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-400">
                        <span>Subtotal:</span>
                        <span class="font-bold text-white">Rp {{ number_format($subtotalAmount, 0, ',', '.') }}</span>
                    </div>
                    @if($discountTotal > 0)
                        <div class="flex justify-between text-emerald-300 bg-emerald-500/10 border border-emerald-500/20 rounded-xl px-2.5 py-1.5">
                            <span class="font-bold">Diskon {{ $discountNote ? '(' . $discountNote . ')' : '' }}</span>
                            <span class="font-black">-Rp {{ number_format($discountTotal, 0, ',', '.') }}</span>
                        </div>
                        @foreach($discountDetails as $d)
                            <div class="flex justify-between text-[11px] text-emerald-200/80 pl-1">
                                <span>• {{ $d['name'] }}</span><span>-{{ number_format($d['amount'],0,',','.') }}</span>
                            </div>
                        @endforeach
                    @endif
                    <div class="flex justify-between text-slate-300">
                        <span>Total HPP Modal:</span>
                        <span class="font-bold text-amber-400">Rp {{ number_format($totalHpp, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-white font-black text-sm pt-1 border-t border-[#2E2A68]">
                        <span>Total Pembayaran:</span>
                        <span class="text-[#3EDAD7] text-base">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>

                    <button 
                        wire:click="openCheckoutModal"
                        class="w-full py-3.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95 flex items-center justify-center gap-2 mt-2"
                    >
                        <span>Bayar Sekarang ({{ count($cart) }})</span>
                        <x-icon name="arrow-right" class="w-4 h-4" />
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- ═══════════════ Floating Cart Button (Mobile) ═══════════════ -->
    @if(count($cart) > 0)
        <div class="lg:hidden fixed bottom-20 left-4 right-4 z-40">
            <button 
                wire:click="openCheckoutModal"
                class="w-full py-3.5 px-5 bg-gradient-to-r from-[#4338CA] to-[#3730A3] text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-2xl flex items-center justify-between active:scale-95 transition border border-[#8696ED]/30"
            >
                <div class="flex items-center gap-2.5">
                    <span class="px-2 py-0.5 rounded-lg bg-white/20 text-white font-black text-xs">{{ count($cart) }}</span>
                    <span>Bayar Pesanan</span>
                </div>
                <span class="text-sm font-black text-[#3EDAD7]">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
            </button>
        </div>
    @endif

    <!-- ═══════════════ Variant Selection Modal ═══════════════ -->
    @if($showVariantModal && $selectedProductForVariant)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-md rounded-t-[32px] sm:rounded-3xl p-5 space-y-4 shadow-2xl max-h-[85vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <div>
                        <h3 class="text-sm font-black text-white">{{ $selectedProductForVariant->name }}</h3>
                        <p class="text-xs font-black text-[#3EDAD7] mt-0.5">Rp {{ number_format($selectedProductForVariant->price, 0, ',', '.') }}</p>
                    </div>
                    <button wire:click="$set('showVariantModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    @foreach($selectedProductForVariant->variants as $variant)
                        <div class="space-y-2">
                            <label class="block font-black text-slate-300 uppercase tracking-wider text-[10px]">{{ $variant->name }}</label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($variant->options as $option)
                                    @php $isSelected = ($selectedOptions[$variant->id] ?? null) == $option->id; @endphp
                                    <button 
                                        type="button" 
                                        wire:click="$set('selectedOptions.{{ $variant->id }}', '{{ $option->id }}')"
                                        class="p-3 rounded-2xl border text-left transition {{ $isSelected ? 'bg-[#4338CA]/30 border-[#4338CA] text-white shadow-sm' : 'bg-[#16192E] border-[#2E2A68] text-slate-400 hover:text-white' }}"
                                    >
                                        <p class="font-bold text-xs">{{ $option->name }}</p>
                                        @if($option->price_modifier > 0)
                                            <p class="text-[10px] text-[#3EDAD7] mt-0.5 font-bold">+Rp {{ number_format($option->price_modifier, 0) }}</p>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <button 
                    wire:click="confirmVariantSelection"
                    class="w-full py-3.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95"
                >
                    Tambahkan ke Keranjang
                </button>
            </div>
        </div>
    @endif

    <!-- ═══════════════ Multi-Step Checkout Modal ═══════════════ -->
    @if($showCheckoutModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-md rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl max-h-[90vh] overflow-y-auto">
                
                <!-- Step 1: METHOD_SELECT -->
                @if($checkoutStep === 'METHOD_SELECT')
                    <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                        <div>
                            <h3 class="text-base font-black text-white">Metode Pembayaran</h3>
                            <p class="text-xs text-slate-400 font-medium">Total: <strong class="text-[#3EDAD7]">Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong></p>
                        </div>
                        <button wire:click="$set('showCheckoutModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- Tunai -->
                        <button 
                            wire:click="selectCashMethod"
                            class="flex flex-col items-center justify-center gap-2 p-4 rounded-3xl border-2 border-[#2E2A68] bg-[#16192E] hover:border-emerald-500/50 hover:bg-emerald-500/10 transition group h-28 text-center"
                        >
                            <div class="w-10 h-10 bg-emerald-500/20 text-emerald-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                                <x-icon name="wallet" class="w-5 h-5 text-emerald-400" />
                            </div>
                            <span class="font-black text-xs uppercase tracking-widest text-white">Tunai</span>
                        </button>

                        <!-- QRIS -->
                        <button 
                            wire:click="selectQrisMethod"
                            class="flex flex-col items-center justify-center gap-2 p-4 rounded-3xl border-2 border-[#2E2A68] bg-[#16192E] hover:border-blue-500/50 hover:bg-blue-500/10 transition group h-28 text-center"
                        >
                            <div class="w-10 h-10 bg-blue-500/20 text-blue-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                                <x-icon name="qr-code" class="w-5 h-5 text-blue-400" />
                            </div>
                            <span class="font-black text-xs uppercase tracking-widest text-white">QRIS</span>
                        </button>

                        <!-- GoFood -->
                        @if($enableGoFood)
                            <button 
                                wire:click="selectPlatformMethod('GOFOOD')"
                                class="flex flex-col items-center justify-center gap-2 p-4 rounded-3xl border-2 border-[#2E2A68] bg-[#16192E] hover:border-emerald-500/50 hover:bg-emerald-500/10 transition group h-28 text-center"
                            >
                                <div class="w-10 h-10 bg-emerald-500/20 text-emerald-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                                    <x-icon name="bike" class="w-5 h-5 text-emerald-400" />
                                </div>
                                <span class="font-black text-xs uppercase tracking-widest text-white">GoFood</span>
                            </button>
                        @endif

                        <!-- GrabFood -->
                        @if($enableGrabFood)
                            <button 
                                wire:click="selectPlatformMethod('GRABFOOD')"
                                class="flex flex-col items-center justify-center gap-2 p-4 rounded-3xl border-2 border-[#2E2A68] bg-[#16192E] hover:border-green-500/50 hover:bg-green-500/10 transition group h-28 text-center"
                            >
                                <div class="w-10 h-10 bg-green-500/20 text-green-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                                    <x-icon name="truck" class="w-5 h-5 text-green-400" />
                                </div>
                                <span class="font-black text-xs uppercase tracking-widest text-white">GrabFood</span>
                            </button>
                        @endif

                        <!-- ShopeeFood -->
                        @if($enableShopeeFood)
                            <button 
                                wire:click="selectPlatformMethod('SHOPEEFOOD')"
                                class="flex flex-col items-center justify-center gap-2 p-4 rounded-3xl border-2 border-[#2E2A68] bg-[#16192E] hover:border-orange-500/50 hover:bg-orange-500/10 transition group h-28 text-center"
                            >
                                <div class="w-10 h-10 bg-orange-500/20 text-orange-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                                    <x-icon name="shopping-bag" class="w-5 h-5 text-orange-400" />
                                </div>
                                <span class="font-black text-xs uppercase tracking-widest text-[#F97316]">Shopee</span>
                            </button>
                        @endif

                        <!-- Split Payment (Tunai + QRIS) -->
                        <button 
                            wire:click="selectSplitMethod"
                            class="col-span-full flex items-center justify-center gap-3 p-3.5 rounded-2xl border-2 border-dashed border-[#2E2A68] bg-[#16192E] hover:border-[#4338CA] transition text-center"
                        >
                            <x-icon name="split" class="w-4 h-4 text-slate-300" />
                            <span class="font-black text-xs uppercase tracking-wider text-slate-300">Bayar Sebagian (Tunai + QRIS)</span>
                        </button>
                    </div>

                <!-- Step 2: CASH_FORM -->
                @elseif($checkoutStep === 'CASH_FORM')
                    <div class="flex items-center gap-3 border-b border-[#2E2A68] pb-3">
                        <button wire:click="$set('checkoutStep', 'METHOD_SELECT')" class="w-8 h-8 rounded-xl bg-[#16192E] border border-[#2E2A68] flex items-center justify-center text-slate-300 hover:text-white text-sm">
                            <x-icon name="arrow-left" class="w-4 h-4" />
                        </button>
                        <div>
                            <h3 class="text-base font-black text-white">Pembayaran Tunai</h3>
                            <p class="text-xs text-slate-400">Total Tagihan: <strong class="text-[#3EDAD7]">Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block font-black text-slate-300 text-xs mb-1.5 uppercase tracking-wider">Nominal Uang Diterima (Rp)</label>
                            <input 
                                type="number" 
                                wire:model.live="paidAmount" 
                                class="w-full h-14 bg-[#16192E] border-2 border-[#4338CA] rounded-2xl text-xl font-black text-white px-4 focus:outline-none focus:ring-4 focus:ring-[#4338CA]/20 transition text-center"
                            >
                        </div>

                        <!-- Quick Cash Shortcuts -->
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" wire:click="setCashNominal({{ $totalAmount }})" class="py-2.5 bg-[#16192E] hover:bg-[#4338CA] border border-[#2E2A68] rounded-xl text-xs font-black text-white transition">
                                Uang Pas
                            </button>
                            <button type="button" wire:click="setCashNominal({{ ceil($totalAmount / 10000) * 10000 }})" class="py-2.5 bg-[#16192E] hover:bg-[#4338CA] border border-[#2E2A68] rounded-xl text-xs font-black text-white transition">
                                Rp {{ number_format(ceil($totalAmount / 10000) * 10000, 0) }}
                            </button>
                            <button type="button" wire:click="setCashNominal({{ ceil($totalAmount / 50000) * 50000 }})" class="py-2.5 bg-[#16192E] hover:bg-[#4338CA] border border-[#2E2A68] rounded-xl text-xs font-black text-white transition">
                                Rp {{ number_format(ceil($totalAmount / 50000) * 50000, 0) }}
                            </button>
                        </div>

                        <!-- Kembalian Box -->
                        <div class="p-4 rounded-2xl bg-[#16192E] border border-[#2E2A68] flex justify-between items-center text-xs">
                            <span class="font-bold text-slate-400">Kembalian:</span>
                            <span class="font-black text-emerald-400 text-lg">Rp {{ number_format($changeAmount, 0, ',', '.') }}</span>
                        </div>

                        <button 
                            wire:click="processCheckout"
                            class="w-full py-4 bg-[#4338CA] hover:bg-[#3730A3] text-white font-black text-sm rounded-2xl shadow-xl transition active:scale-95"
                        >
                            Konfirmasi Pembayaran Tunai
                        </button>
                    </div>

                <!-- Step 3: PLATFORM_ADJUSTMENT (GoFood / GrabFood / ShopeeFood) -->
                @elseif($checkoutStep === 'PLATFORM_ADJUSTMENT')
                    <div class="flex items-center gap-3 border-b border-[#2E2A68] pb-3">
                        <button wire:click="$set('checkoutStep', 'METHOD_SELECT')" class="w-8 h-8 rounded-xl bg-[#16192E] border border-[#2E2A68] flex items-center justify-center text-slate-300 hover:text-white text-sm">
                            <x-icon name="arrow-left" class="w-4 h-4" />
                        </button>
                        <div>
                            <h3 class="text-base font-black text-white">Konfirmasi Setoran</h3>
                            <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Pesanan {{ $selectedPlatform }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-[#16192E] p-4 rounded-2xl border border-[#2E2A68] text-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Tagihan Kasir</p>
                            <p class="text-xl font-black text-slate-400 line-through">Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
                        </div>

                        <div>
                            <label class="block font-black text-[#3EDAD7] text-xs mb-1.5 uppercase tracking-wider">
                                Total Uang Net / Bersih Diterima (Rp)
                            </label>
                            <input 
                                type="number" 
                                wire:model.live="adjustedAmount" 
                                class="w-full h-14 bg-[#16192E] border-2 border-[#3EDAD7] rounded-2xl text-xl font-black text-white px-4 focus:outline-none focus:ring-4 focus:ring-[#3EDAD7]/20 transition text-center"
                            >
                            <p class="text-[10px] text-slate-400 mt-1">Masukkan nominal setelah dipotong komisi/diskon platform.</p>
                        </div>

                        @php $diff = $adjustedAmount - $totalAmount; @endphp
                        <div class="p-3.5 rounded-2xl bg-[#16192E] border border-[#2E2A68] flex justify-between items-center text-xs">
                            <span class="font-bold text-slate-400">Selisih Potongan / Margin:</span>
                            <span class="font-black text-sm {{ $diff >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $diff >= 0 ? '+' : '-' }} Rp {{ number_format(abs($diff), 0, ',', '.') }}
                            </span>
                        </div>

                        <button 
                            wire:click="processCheckout"
                            class="w-full py-4 bg-[#4338CA] hover:bg-[#3730A3] text-white font-black text-sm rounded-2xl shadow-xl transition active:scale-95"
                        >
                            Simpan Transaksi {{ $selectedPlatform }}
                        </button>
                    </div>

                <!-- Step 4: QRIS_DISPLAY -->
                @elseif($checkoutStep === 'QRIS_DISPLAY')
                    <div class="flex items-center gap-3 border-b border-[#2E2A68] pb-3">
                        <button wire:click="$set('checkoutStep', 'METHOD_SELECT')" class="w-8 h-8 rounded-xl bg-[#16192E] border border-[#2E2A68] flex items-center justify-center text-slate-300 hover:text-white text-sm">
                            <x-icon name="arrow-left" class="w-4 h-4" />
                        </button>
                        <div>
                            <h3 class="text-base font-black text-white">Pembayaran QRIS</h3>
                            <p class="text-xs text-slate-400 font-medium">Total: <strong class="text-[#3EDAD7]">Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong></p>
                        </div>
                    </div>

                    <div class="space-y-4 text-center">
                        <div class="w-60 h-60 bg-white p-3 rounded-2xl mx-auto flex items-center justify-center shadow-lg border border-[#2E2A68]">
                            <img src="{{ $qrisImage ?? '/images/kasiva-logo-full.png' }}" alt="Kode QRIS" class="w-full h-full object-contain">
                        </div>
                        <p class="text-xs text-slate-300 font-semibold">Tunjukkan kode QR kepada pelanggan untuk di-scan</p>

                        <button 
                            wire:click="processCheckout"
                            class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm rounded-2xl shadow-xl transition active:scale-95"
                        >
                            Konfirmasi Pembayaran Selesai
                        </button>
                    </div>

                <!-- Step 5: SPLIT_PAYMENT -->
                @elseif($checkoutStep === 'SPLIT_PAYMENT')
                    <div class="flex items-center gap-3 border-b border-[#2E2A68] pb-3">
                        <button wire:click="$set('checkoutStep', 'METHOD_SELECT')" class="w-8 h-8 rounded-xl bg-[#16192E] border border-[#2E2A68] flex items-center justify-center text-slate-300 hover:text-white text-sm">
                            <x-icon name="arrow-left" class="w-4 h-4" />
                        </button>
                        <div>
                            <h3 class="text-base font-black text-white">Bayar Sebagian (Split)</h3>
                            <p class="text-xs text-slate-400">Total: <strong class="text-[#3EDAD7]">Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong></p>
                        </div>
                    </div>

                    <div class="space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-300 mb-1">Porsi Tunai (Rp)</label>
                            <input type="number" wire:model.live="splitCashAmount" class="w-full px-4 py-3 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white font-black text-sm">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-300 mb-1">Porsi QRIS (Rp)</label>
                            <input type="number" wire:model.live="splitQrisAmount" class="w-full px-4 py-3 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white font-black text-sm">
                        </div>

                        <div class="p-3 bg-[#16192E] rounded-xl border border-[#2E2A68] flex justify-between font-bold">
                            <span class="text-slate-400">Total Pembayaran Split:</span>
                            <span class="text-[#3EDAD7]">Rp {{ number_format($splitCashAmount + $splitQrisAmount, 0, ',', '.') }}</span>
                        </div>

                        <button 
                            wire:click="processCheckout"
                            class="w-full py-4 bg-[#4338CA] hover:bg-[#3730A3] text-white font-black text-sm rounded-2xl shadow-xl transition active:scale-95"
                        >
                            Simpan Pembayaran Split
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- ═══════════════ Receipt Modal & WhatsApp Share ═══════════════ -->
    @if($showReceiptModal && $lastTransaction)
        <div class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-sm rounded-3xl p-6 space-y-4 shadow-2xl text-center animate-in zoom-in-95">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto border border-emerald-500/40">
                    <x-icon name="check" class="w-6 h-6 text-emerald-400" />
                </div>
                
                <div>
                    <h3 class="text-base font-black text-white">Transaksi Berhasil Dicatat</h3>
                    <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $lastTransaction->receipt_number }}</p>
                </div>

                <!-- Receipt Detail Box -->
                <div class="bg-[#16192E] p-4 rounded-2xl border border-[#2E2A68] text-left space-y-2 text-xs font-mono">
                    <div class="flex justify-between text-slate-400 text-[11px]">
                        <span>Metode:</span>
                        <span class="text-white font-bold">{{ $lastTransaction->payment_method }}</span>
                    </div>
                    <div class="flex justify-between text-slate-400 text-[11px]">
                        <span>Total:</span>
                        <span class="text-[#3EDAD7] font-bold">Rp {{ number_format($lastTransaction->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($lastTransaction->change_amount > 0)
                        <div class="flex justify-between text-slate-400 text-[11px]">
                            <span>Kembalian:</span>
                            <span class="text-emerald-400 font-bold">Rp {{ number_format($lastTransaction->change_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                <div class="space-y-2 pt-2">
                    <a 
                        href="{{ $this->whatsAppReceiptUrl }}" 
                        target="_blank" 
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider rounded-xl flex items-center justify-center gap-2 shadow-lg transition"
                    >
                        <span>Kirim Struk WhatsApp</span>
                    </a>

                     <button 
                        wire:click="closeReceiptModal"
                        class="w-full py-3 bg-[#16192E] hover:bg-[#25215A] text-slate-300 hover:text-white font-black text-xs rounded-xl border border-[#2E2A68] transition"
                    >
                        Selesai & Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showMemberScanner)
        <div class="fixed inset-0 z-[60] bg-slate-950/85 backdrop-blur flex items-center justify-center p-4" wire:ignore.self>
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-sm rounded-3xl p-5 space-y-4 shadow-2xl">
                <div class="flex items-center justify-between">
                    <h3 class="font-black text-sm text-white">Scan QR Member</h3>
                    <button wire:click="$set('showMemberScanner', false)" class="w-8 h-8 rounded-xl bg-[#16192E] border border-[#2E2A68] flex items-center justify-center text-slate-400">✕</button>
                </div>
                <div id="kasiva-member-reader" class="rounded-2xl overflow-hidden border border-[#2E2A68] bg-black min-h-[240px]"></div>
                <p class="text-[11px] text-slate-400 text-center">Arahkan kamera ke QR <span class="font-mono text-white">KSV-MBR-</span> / <span class="font-mono text-white">NGEPOS-MBR-</span></p>
            </div>
        </div>
    @endif

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            let html5Qr = null;
            Livewire.on('member-scanned', (e) => {});
            const startScanner = () => {
                const el = document.getElementById('kasiva-member-reader');
                if(!el || typeof Html5Qrcode === 'undefined') return;
                if(html5Qr){ try{ html5Qr.clear(); }catch(e){} }
                html5Qr = new Html5Qrcode("kasiva-member-reader");
                html5Qr.start({ facingMode: "environment" }, { fps: 10, qrbox: 220 },
                    (decoded) => { @this.call('scanMemberResult', decoded); try{ html5Qr.stop(); }catch(e){} },
                    () => {}
                ).catch(()=>{});
            };
            const stopScanner = () => { if(html5Qr){ try{ html5Qr.stop(); html5Qr.clear(); }catch(e){} html5Qr=null; } };
            // Poll visibility of scanner modal
            setInterval(()=>{
                const visible = document.getElementById('kasiva-member-reader') && document.body.innerHTML.includes('Scan QR Member');
                const hasEl = !!document.getElementById('kasiva-member-reader');
                if(hasEl && !html5Qr && @this.get('showMemberScanner')) startScanner();
                if(!@this.get('showMemberScanner')) stopScanner();
            }, 600);
            Livewire.hook('morph.updated', () => {
                if(@this.get('showMemberScanner') && document.getElementById('kasiva-member-reader') && !html5Qr) setTimeout(startScanner, 300);
                if(!@this.get('showMemberScanner')) stopScanner();
            });
        });
    </script>
    @endpush
</div>

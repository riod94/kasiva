<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div>
            <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                <span>Riwayat Transaksi Penjualan</span>
            </h1>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Daftar rekaman pesanan kasir, input omset lampau, dan cetak ulang struk</p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('history.backdate') }}" 
                class="px-4 py-2.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-md flex items-center gap-1.5 active:scale-95 transition"
            >
                <x-icon name="plus" class="w-3.5 h-3.5" />
                <span>Input Lampau</span>
            </a>
            <span class="text-xs font-black px-3.5 py-2.5 bg-[#16192E] text-indigo-300 rounded-2xl border border-[#2E2A68]">
                {{ $transactions->total() }} Transaksi
            </span>
        </div>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <x-icon name="search" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
            <input 
                type="text" 
                wire:model.live.debounce.250ms="search"
                placeholder="Cari nomor struk KSV..."
                class="w-full pl-11 pr-4 py-3 bg-[#1E1B4B] border border-[#2E2A68] rounded-2xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-[#4338CA] transition"
            >
        </div>
        <select 
            wire:model.live="paymentFilter"
            class="px-4 py-3 bg-[#1E1B4B] border border-[#2E2A68] rounded-2xl text-xs font-bold text-slate-200 focus:outline-none focus:border-[#4338CA] transition"
        >
            <option value="ALL">Semua Pembayaran</option>
            <option value="CASH">Tunai</option>
            <option value="QRIS">QRIS</option>
            <option value="GOFOOD">GoFood</option>
            <option value="GRABFOOD">GrabFood</option>
            <option value="SHOPEEFOOD">ShopeeFood</option>
        </select>
        <select 
            wire:model.live="dateRange"
            class="px-4 py-3 bg-[#1E1B4B] border border-[#2E2A68] rounded-2xl text-xs font-bold text-slate-200 focus:outline-none focus:border-[#4338CA] transition"
        >
            <option value="today">Hari Ini</option>
            <option value="7days">7 Hari Terakhir</option>
            <option value="month">Bulan Ini</option>
            <option value="all">Semua Waktu</option>
        </select>
    </div>

    <!-- Transactions List -->
    <div class="space-y-3">
        @forelse($transactions as $tx)
            <div 
                wire:click="showDetail('{{ $tx->id }}')"
                class="bg-[#1E1B4B] border border-[#2E2A68] rounded-2xl p-4 flex items-center justify-between shadow-md hover:border-[#4338CA] transition cursor-pointer group"
            >
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-mono font-bold text-xs text-[#8696ED] group-hover:text-[#3EDAD7] transition">{{ $tx->receipt_number }}</span>
                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-[#16192E] text-slate-300 border border-[#2E2A68]">
                            {{ $tx->payment_method }}
                        </span>
                        @if($tx->is_backdated)
                            <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                Lampau
                            </span>
                        @endif
                        @if(($tx->status ?? 'COMPLETED') === 'VOIDED')
                            <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30">VOID</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium">
                        {{ $tx->created_at->format('d M Y H:i') }} • {{ $tx->cashier_name }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <div class="text-right">
                        <p class="text-base font-black {{ ($tx->status ?? 'COMPLETED')==='VOIDED' ? 'text-slate-500 line-through' : 'text-white' }}">
                            Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                        </p>
                        <p class="text-[11px] {{ ($tx->status ?? 'COMPLETED')==='VOIDED' ? 'text-slate-500' : 'text-emerald-400' }} font-bold">
                            @if(($tx->status ?? 'COMPLETED')==='VOIDED') Dibatalkan @else Profit: Rp {{ number_format($tx->total_amount - $tx->total_hpp, 0, ',', '.') }} @endif
                        </p>
                    </div>
                    @if(($tx->status ?? 'COMPLETED')!=='VOIDED')
                        @can('VOID_TRANSACTION')
                        <button wire:click.stop="openVoidModal('{{ $tx->id }}')" class="w-8 h-8 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-300 flex items-center justify-center active:scale-95 transition" title="Batalkan Transaksi (Void)">×</button>
                        @endcan
                    @endif
                </div>
            </div>
        @empty
            <div class="py-16 text-center text-slate-400 bg-[#1E1B4B] rounded-3xl border border-[#2E2A68]">
                <x-icon name="receipt" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                <p class="text-sm font-semibold text-slate-300">Belum ada riwayat transaksi</p>
                <p class="text-xs text-slate-500 mt-0.5">Transaksi dari kasir akan tercatat secara otomatis di sini</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $transactions->links() }}
    </div>

    <!-- Transaction Detail Modal -->
    @if($showDetailModal && $selectedTransaction)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-sm rounded-t-[32px] sm:rounded-3xl p-6 space-y-4 shadow-2xl">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <h3 class="text-base font-black text-white flex items-center gap-2">
                        <span>Detail Struk Transaksi</span>
                    </h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-white font-black text-sm p-1">✕</button>
                </div>

                <div class="text-center space-y-1">
                    <p class="text-xs font-black text-[#8696ED] font-mono">{{ $selectedTransaction->receipt_number }}</p>
                    <p class="text-[10px] text-slate-400">{{ $selectedTransaction->created_at->format('d M Y H:i') }} • {{ $selectedTransaction->cashier_name }}</p>
                </div>

                <div class="max-h-48 overflow-y-auto space-y-2 border-y border-[#2E2A68] py-3">
                    @foreach($selectedTransaction->items as $item)
                        <div class="flex items-center justify-between text-xs">
                            <div>
                                <p class="font-bold text-white">{{ $item->product_name }}</p>
                                <p class="text-[10px] text-slate-400">Rp {{ number_format($item->unit_price, 0, ',', '.') }} x {{ $item->quantity }}</p>
                            </div>
                            <span class="font-black text-[#3EDAD7]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between font-bold text-white">
                        <span>Total Penjualan:</span>
                        <span class="font-black">Rp {{ number_format($selectedTransaction->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Dibayar ({{ $selectedTransaction->payment_method }}):</span>
                        <span>Rp {{ number_format($selectedTransaction->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($selectedTransaction->change_amount > 0)
                        <div class="flex justify-between text-[#3EDAD7] font-bold">
                            <span>Kembalian:</span>
                            <span>Rp {{ number_format($selectedTransaction->change_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex flex-col gap-2 pt-2 border-t border-[#2E2A68]">
                    <a 
                        href="{{ $this->whatsAppUrl }}" 
                        target="_blank" 
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow transition flex items-center justify-center gap-2 active:scale-95"
                    >
                        <span>Kirim Struk WhatsApp</span>
                    </a>

                    <button 
                        onclick="window.print()" 
                        class="w-full py-3 bg-[#16192E] hover:bg-[#25215A] text-slate-200 font-bold text-xs rounded-2xl border border-[#2E2A68] transition flex items-center justify-center gap-2 active:scale-95"
                    >
                        <x-icon name="printer" class="w-4 h-4" />
                        <span>Cetak Struk ESC/POS</span>
                    </button>

                    <button 
                        wire:click="closeModal"
                        class="w-full py-3 bg-[#4338CA] hover:bg-[#3730A3] text-white font-black text-xs rounded-2xl shadow transition active:scale-95"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

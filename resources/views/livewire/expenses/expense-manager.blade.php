<div class="space-y-6">
    <!-- Header Page & Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div>
            <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                <span>Pengeluaran Operasional Toko</span>
            </h1>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Pencatatan biaya bahan baku, sewa, gaji staf, dan utilitas</p>
        </div>
        <button 
            wire:click="openModal"
            class="px-5 py-3 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg flex items-center justify-center gap-2 active:scale-95 transition shrink-0"
        >
            <x-icon name="plus" class="w-4 h-4" />
            <span>Catat Pengeluaran</span>
        </button>
    </div>

    <!-- Monthly Summary Card -->
    <div class="bg-[#1E1B4B] border border-[#2E2A68] text-white rounded-3xl p-6 shadow-lg flex items-center justify-between">
        <div>
            <p class="text-xs text-[#8696ED] font-bold uppercase tracking-wider">Total Beban Operasional Bulan Ini</p>
            <p class="text-3xl font-black text-white mt-1">
                Rp {{ number_format($totalThisMonth, 0, ',', '.') }}
            </p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-[#16192E] text-orange-400 flex items-center justify-center border border-[#2E2A68]">
            <x-icon name="wallet" class="w-6 h-6 text-orange-400" />
        </div>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Expense List -->
    <div class="space-y-3">
        @forelse($expenses as $exp)
            <div class="bg-[#1E1B4B] border border-[#2E2A68] hover:border-[#4338CA] rounded-2xl p-4 flex items-center justify-between shadow-md transition">
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="font-bold text-xs text-white">{{ $exp->title }}</h4>
                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-[#16192E] text-[#8696ED] border border-[#2E2A68]">
                            {{ $exp->category }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                        {{ $exp->expense_date->format('d M Y H:i') }} {{ $exp->notes ? '• '.$exp->notes : '' }}
                    </p>
                </div>

                <span class="text-base font-black text-rose-400">
                    - Rp {{ number_format($exp->amount, 0, ',', '.') }}
                </span>
            </div>
        @empty
            <div class="py-16 text-center text-slate-400 bg-[#1E1B4B] rounded-3xl border border-[#2E2A68]">
                <x-icon name="wallet" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                <p class="text-sm font-semibold text-slate-300">Belum ada catatan pengeluaran</p>
                <p class="text-xs text-slate-500 mt-0.5">Catat pengeluaran rutin toko untuk analisis margin laba bersih yang presisi</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $expenses->links() }}
    </div>

    <!-- Create Expense Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-sm rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <h3 class="text-base font-black text-white">Catat Pengeluaran Baru</h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Judul Pengeluaran</label>
                        <input type="text" id="offline-expense-title" wire:model="title" placeholder="cth: Belanja Susu UHT & Gula Aren" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA]">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nominal (Rp)</label>
                        <input type="number" id="offline-expense-amount" wire:model="amount" placeholder="0" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA] font-bold text-rose-400">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Kategori Beban</label>
                        <select id="offline-expense-category" wire:model="category" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white font-semibold focus:outline-none focus:border-[#4338CA]">
                            <option value="RAW_MATERIAL">Bahan Baku (Restok)</option>
                            <option value="RENT">Sewa Tempat Outlet</option>
                            <option value="SALARY">Gaji / Upah Karyawan</option>
                            <option value="UTILITIES">Listrik, Air & Internet</option>
                            <option value="MARKETING">Promosi & Iklan</option>
                            <option value="OPERATIONAL">Peralatan & Kebersihan</option>
                            <option value="OTHER">Lain-lain</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Tanggal Transaksi</label>
                        <input type="datetime-local" id="offline-expense-date" wire:model="expense_date" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white focus:outline-none focus:border-[#4338CA]">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Catatan Tambahan</label>
                        <textarea id="offline-expense-notes" wire:model="notes" rows="2" placeholder="Catatan opsional..." class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA]"></textarea>
                    </div>
                </div>

                <button 
                    id="offline-save-expense" wire:click="saveExpense"
                    class="w-full py-3.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95"
                >
                    Simpan Pengeluaran
                </button>
            </div>
        </div>
    @endif
</div>

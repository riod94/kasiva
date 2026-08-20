<div class="max-w-md mx-auto space-y-6">
    <!-- Header Page -->
    <div class="flex items-center gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <a href="{{ route('history.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#25215A] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition">
            <x-icon name="arrow-left" class="w-4 h-4" />
        </a>
        <div>
            <h1 class="text-xl font-black text-white flex items-center gap-2">
                <span>Input Transaksi Lampau</span>
            </h1>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Pencatatan omset penjualan masa lalu ke pembukuan</p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-[#1E1B4B] p-6 rounded-3xl border border-[#2E2A68] shadow-xl space-y-4 text-xs">
        <div>
            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Total Omset Penjualan (Rp)</label>
            <input 
                type="number" 
                wire:model="totalAmount" 
                placeholder="50000"
                class="w-full h-14 bg-[#16192E] border-2 border-[#4338CA] rounded-2xl text-xl font-black text-[#3EDAD7] px-4 focus:outline-none focus:ring-4 focus:ring-[#4338CA]/20 transition text-center"
            >
            @error('totalAmount') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Tanggal Transaksi</label>
                <input 
                    type="date" 
                    wire:model="transactionDate" 
                    class="w-full px-3.5 py-3 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white font-bold focus:outline-none focus:border-[#4338CA]"
                >
            </div>
            <div>
                <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Waktu / Jam</label>
                <input 
                    type="time" 
                    wire:model="transactionTime" 
                    class="w-full px-3.5 py-3 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white font-bold focus:outline-none focus:border-[#4338CA]"
                >
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Metode Pembayaran</label>
            <div class="grid grid-cols-2 gap-2">
                <button 
                    type="button" 
                    wire:click="$set('paymentMethod', 'CASH')"
                    class="py-3 rounded-2xl border font-bold text-xs transition {{ $paymentMethod === 'CASH' ? 'bg-[#4338CA] text-white border-[#4338CA]' : 'bg-[#16192E] text-slate-400 border-[#2E2A68] hover:text-white' }}"
                >
                    Tunai (Cash)
                </button>
                <button 
                    type="button" 
                    wire:click="$set('paymentMethod', 'QRIS')"
                    class="py-3 rounded-2xl border font-bold text-xs transition {{ $paymentMethod === 'QRIS' ? 'bg-[#4338CA] text-white border-[#4338CA]' : 'bg-[#16192E] text-slate-400 border-[#2E2A68] hover:text-white' }}"
                >
                    QRIS
                </button>
            </div>
        </div>

        <div class="pt-3 border-t border-[#2E2A68]">
            <button 
                wire:click="saveTransaction"
                class="w-full py-4 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-xl transition active:scale-95 flex items-center justify-center gap-2"
            >
                Simpan Transaksi Lampau
            </button>
        </div>
    </div>
</div>

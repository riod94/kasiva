<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('settings.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#25215A] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <span>Profil Outlet & Pajak</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Identitas toko kasir, alamat pada struk, dan pengaturan tarif pajak PB1 / service</p>
            </div>
        </div>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Settings Form -->
    <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-3xl p-6 shadow-xl space-y-4 text-xs">
        <div>
            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nama Outlet / Brand</label>
            <input type="text" wire:model="name" class="w-full px-4 py-3 bg-[#16192E] border border-[#2E2A68] rounded-2xl text-white font-bold placeholder-slate-500 focus:outline-none focus:border-[#4338CA] transition">
            @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Alamat Lengkap (Tercetak di Struk)</label>
            <textarea wire:model="address" rows="3" class="w-full px-4 py-3 bg-[#16192E] border border-[#2E2A68] rounded-2xl text-white font-medium placeholder-slate-500 focus:outline-none focus:border-[#4338CA] transition"></textarea>
        </div>

        <div>
            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nomor Kontak / WhatsApp Toko</label>
            <input type="text" wire:model="phone" class="w-full px-4 py-3 bg-[#16192E] border border-[#2E2A68] rounded-2xl text-white font-medium placeholder-slate-500 focus:outline-none focus:border-[#4338CA] transition">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-[#2E2A68]">
            <div>
                <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Pajak PB1 / Restoran (%)</label>
                <input type="number" step="0.1" wire:model="tax_percentage" class="w-full px-4 py-3 bg-[#16192E] border border-[#2E2A68] rounded-2xl text-white font-black text-sm placeholder-slate-500 focus:outline-none focus:border-[#4338CA] transition">
                <p class="text-[10px] text-slate-400 mt-1">Opsional, isi 0 jika harga menu sudah nett.</p>
            </div>

            <div>
                <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Service Charge (%)</label>
                <input type="number" step="0.1" wire:model="service_charge_percentage" class="w-full px-4 py-3 bg-[#16192E] border border-[#2E2A68] rounded-2xl text-white font-black text-sm placeholder-slate-500 focus:outline-none focus:border-[#4338CA] transition">
                <p class="text-[10px] text-slate-400 mt-1">Biaya pelayanan resto atau cafe.</p>
            </div>
        </div>

        <div class="pt-4 border-t border-[#2E2A68]">
            <button 
                wire:click="saveSettings"
                class="w-full py-3.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95 flex items-center justify-center gap-2"
            >
                Simpan Perubahan Outlet
            </button>
        </div>
    </div>
</div>

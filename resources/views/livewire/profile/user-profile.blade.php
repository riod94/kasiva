<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('settings.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#2A3155] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <span>Profil & Keamanan Akun</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Informasi akun pengguna, pembaruan PIN kasir, dan sesi login</p>
            </div>
        </div>
        <button 
            wire:click="logout"
            class="px-4 py-2.5 bg-rose-600/10 hover:bg-rose-600 text-rose-300 hover:text-white font-black text-xs uppercase tracking-wider rounded-2xl border border-rose-500/30 transition flex items-center gap-2 active:scale-95 shrink-0"
        >
            <x-icon name="logout" class="w-4 h-4" />
            <span>Keluar</span>
        </button>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Profile Edit Form -->
    <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-3xl p-6 shadow-xl space-y-4 text-xs">
        <h3 class="text-sm font-black text-white flex items-center gap-2 border-b border-[#2E2A68] pb-3">
            <x-icon name="users" class="w-4 h-4 text-[#8696ED]" />
            <span>Data Pengguna</span>
        </h3>

        <div>
            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nama Lengkap</label>
            <input type="text" wire:model="name" class="w-full px-4 py-3 bg-[#16192E] border border-[#2E2A68] rounded-2xl text-white font-bold placeholder-slate-500 focus:outline-none focus:border-[#00AAA6] transition">
            @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Alamat Email</label>
            <input type="email" wire:model="email" class="w-full px-4 py-3 bg-[#16192E] border border-[#2E2A68] rounded-2xl text-white font-medium placeholder-slate-500 focus:outline-none focus:border-[#00AAA6] transition">
            @error('email') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nomor WhatsApp</label>
            <input type="text" wire:model="phone" class="w-full px-4 py-3 bg-[#16192E] border border-[#2E2A68] rounded-2xl text-white font-medium placeholder-slate-500 focus:outline-none focus:border-[#00AAA6] transition">
        </div>

        <button 
            wire:click="updateProfile"
            class="w-full py-3.5 bg-[#00AAA6] hover:bg-[#008F8C] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95 flex items-center justify-center gap-2"
        >
            Simpan Perubahan Profil
        </button>
    </div>

    <!-- Change PIN Form -->
    <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-3xl p-6 shadow-xl space-y-4 text-xs">
        <h3 class="text-sm font-black text-white flex items-center gap-2 border-b border-[#2E2A68] pb-3">
            <x-icon name="shield" class="w-4 h-4 text-emerald-400" />
            <span>Perbarui 6-Digit PIN Kasir</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">PIN Baru (6 Digit)</label>
                <input type="password" maxlength="6" wire:model="new_pin" placeholder="123456" class="w-full px-4 py-3 bg-[#16192E] border border-[#2E2A68] rounded-2xl text-white font-mono text-center tracking-widest focus:outline-none focus:border-[#00AAA6] transition">
                @error('new_pin') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Konfirmasi PIN Baru</label>
                <input type="password" maxlength="6" wire:model="new_pin_confirmation" placeholder="123456" class="w-full px-4 py-3 bg-[#16192E] border border-[#2E2A68] rounded-2xl text-white font-mono text-center tracking-widest focus:outline-none focus:border-[#00AAA6] transition">
            </div>
        </div>

        <button 
            wire:click="updatePin"
            class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95 flex items-center justify-center gap-2"
        >
            Perbarui PIN Keamanan
        </button>
    </div>
</div>

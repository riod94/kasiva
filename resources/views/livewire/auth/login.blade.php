<div class="min-h-screen flex items-center justify-center p-4 bg-[#0F172A] text-slate-100 selection:bg-[#4338CA]/30 selection:text-[#3EDAD7]">
    <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-sm rounded-3xl p-7 space-y-6 shadow-2xl backdrop-blur-xl">
        <div class="text-center space-y-2">
            <a href="{{ route('landing') }}" class="inline-block">
                <img src="/images/kasiva-logo-full.png" alt="Kasiva POS" class="h-10 w-auto object-contain mx-auto bg-white/95 p-1.5 rounded-xl shadow-sm hover:scale-105 transition">
            </a>
            <h1 class="text-xl font-black text-white tracking-tight">Masuk Akun Kasiva</h1>
            <p class="text-xs text-slate-400 font-medium">Akses sistem kasir outlet & dasbor manajemen</p>
        </div>

        <form wire:submit.prevent="login" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Alamat Email</label>
                <input type="email" wire:model="email" placeholder="kasir@kasiva.pos" class="w-full px-3.5 py-3 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA] transition">
                @error('email') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Kata Sandi / PIN</label>
                <input type="password" wire:model="password" placeholder="••••••••" class="w-full px-3.5 py-3 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA] transition">
                @error('password') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-between text-[11px] pt-1">
                <label class="flex items-center gap-2 text-slate-400 cursor-pointer font-medium hover:text-slate-300">
                    <input type="checkbox" wire:model="remember" class="rounded bg-[#16192E] border-[#2E2A68] text-[#4338CA] focus:ring-0">
                    Ingat saya
                </label>
                <span class="text-slate-500 text-[10px]">PIN default: 123456</span>
            </div>

            <button type="submit" class="w-full py-3.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95 flex items-center justify-center gap-2">
                <span>Masuk ke Kasir POS</span>
                <x-icon name="arrow-right" class="w-4 h-4" />
            </button>
        </form>

        <div class="pt-3 border-t border-[#2E2A68] text-center space-y-2">
            <p class="text-[11px] text-slate-400 font-medium">
                Belum punya akun outlet? <a href="{{ route('register') }}" class="text-[#3EDAD7] font-black hover:underline">Daftar Sekarang</a>
            </p>
            <div>
                <a href="{{ route('landing') }}" class="text-[10px] text-slate-500 hover:text-slate-300 transition flex items-center justify-center gap-1">
                    <x-icon name="arrow-left" class="w-3 h-3" />
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('settings.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#2A3155] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <span>Pengaturan Struk Cetak</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Kustomisasi tata letak, logo, dan pesan footer pada printer thermal ESC/POS</p>
            </div>
        </div>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Form Section -->
    <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-3xl p-6 shadow-xl space-y-5 text-xs">
        <!-- Toggle Logo -->
        <div class="flex items-center justify-between p-4 rounded-2xl bg-[#16192E] border border-[#2E2A68]">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#00AAA6]/20 text-[#3EDAD7] flex items-center justify-center">
                    <x-icon name="image" class="w-5 h-5 text-[#8696ED]" />
                </div>
                <div>
                    <h3 class="font-black text-sm text-white">Tampilkan Logo Toko</h3>
                    <p class="text-[10px] text-slate-400 font-medium mt-0.5">Cetak logo resmi pada bagian header struk</p>
                </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model="showLogo" class="sr-only peer">
                <div class="w-11 h-6 bg-[#0F172A] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#00AAA6] border border-[#2E2A68]"></div>
            </label>
        </div>

        <!-- Format Kertas -->
        <div>
            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Ukuran Lebar Kertas Printer</label>
            <div class="grid grid-cols-2 gap-3">
                <button 
                    type="button" 
                    wire:click="$set('paperWidth', '58mm')"
                    class="p-4 rounded-2xl border text-left transition {{ $paperWidth === '58mm' ? 'bg-[#00AAA6]/20 border-[#00AAA6] text-white shadow-sm' : 'bg-[#16192E] border-[#2E2A68] text-slate-400 hover:text-white' }}"
                >
                    <p class="font-black text-xs">Standard 58mm</p>
                    <p class="text-[10px] mt-0.5 opacity-80">Printer Bluetooth Portabel Mini</p>
                </button>
                <button 
                    type="button" 
                    wire:click="$set('paperWidth', '80mm')"
                    class="p-4 rounded-2xl border text-left transition {{ $paperWidth === '80mm' ? 'bg-[#00AAA6]/20 border-[#00AAA6] text-white shadow-sm' : 'bg-[#16192E] border-[#2E2A68] text-slate-400 hover:text-white' }}"
                >
                    <p class="font-black text-xs">Desktop 80mm</p>
                    <p class="text-[10px] mt-0.5 opacity-80">Printer Kasir Station USB / LAN</p>
                </button>
            </div>
        </div>

        <!-- Footer Text -->
        <div>
            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Pesan Footer Struk</label>
            <textarea 
                wire:model="footerText" 
                rows="3" 
                placeholder="— TERIMA KASIH —&#10;Follow IG: @kasiva.pos"
                class="w-full px-4 py-3 bg-[#16192E] border border-[#2E2A68] rounded-2xl text-white font-medium placeholder-slate-500 focus:outline-none focus:border-[#00AAA6] transition text-center"
            ></textarea>
        </div>

        <div class="p-4 rounded-2xl bg-[#16192E] border border-[#2E2A68] flex items-start gap-3 text-slate-300">
            <x-icon name="info" class="w-4 h-4 text-cyan-400 mt-0.5 shrink-0" />
            <p class="text-xs font-medium leading-relaxed">
                <strong class="text-white">Format {{ $paperWidth }}:</strong> Desain struk dioptimalkan secara otomatis untuk resolusi printer thermal. Pastikan opsi margin browser diatur ke <em>'None'</em> saat mencetak struk.
            </p>
        </div>

        <div class="pt-3 border-t border-[#2E2A68]">
            <button 
                wire:click="saveSettings"
                class="w-full py-3.5 bg-[#00AAA6] hover:bg-[#008F8C] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95 flex items-center justify-center gap-2"
            >
                Simpan Pengaturan Struk
            </button>
        </div>
    </div>
</div>

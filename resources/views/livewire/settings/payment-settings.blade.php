<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('settings.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#25215A] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <span>Metode Pembayaran</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">QRIS Statis & Aktivasi Kanal Penjualan Delivery Online</p>
            </div>
        </div>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div class="flex flex-col gap-6">
        <!-- 1. QRIS Statis Section -->
        <div class="bg-[#1E1B4B] p-6 rounded-3xl border border-[#2E2A68] shadow-sm flex flex-col gap-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-500/20 text-blue-400 flex items-center justify-center border border-blue-500/40 shrink-0">
                    <x-icon name="qr-code" class="w-6 h-6 text-blue-400" />
                </div>
                <div>
                    <h2 class="font-black text-base text-white tracking-tight leading-none">Kode QRIS Statis Outlet</h2>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-1 opacity-80">
                        Unggah QR Toko untuk Ditampilkan di Kasir
                    </p>
                </div>
            </div>

            <div class="h-px w-full bg-[#2E2A68]"></div>

            @if($qris_image)
                <div class="flex flex-col gap-4">
                    <div class="relative w-full aspect-square max-h-64 bg-[#16192E] rounded-3xl overflow-hidden border border-[#2E2A68] mx-auto flex items-center justify-center">
                        <img src="{{ $qris_image }}" alt="QRIS Code" class="w-full h-full object-contain p-4">
                    </div>
                    <div class="flex gap-2">
                        <label class="flex-1">
                            <input type="file" wire:model="qris_file" wire:change="uploadQris" accept="image/*" class="hidden">
                            <div class="w-full h-12 rounded-2xl border border-[#2E2A68] bg-[#16192E] hover:bg-[#25215A] text-slate-300 hover:text-white flex items-center justify-center gap-2 font-black text-xs cursor-pointer transition">
                                <x-icon name="upload" class="w-4 h-4" />
                                <span>Ganti Kode QR</span>
                            </div>
                        </label>
                        <button 
                            type="button"
                            wire:click="removeQris" 
                            wire:confirm="Hapus gambar QRIS statis?"
                            class="flex-1 h-12 rounded-2xl border border-rose-500/30 bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white font-black text-xs transition flex items-center justify-center gap-2"
                        >
                            <x-icon name="trash" class="w-4 h-4" />
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>
            @else
                <label class="flex flex-col items-center justify-center gap-3 p-8 border-2 border-dashed border-[#2E2A68] rounded-3xl cursor-pointer hover:border-[#4338CA] hover:bg-[#4338CA]/10 transition group">
                    <input type="file" wire:model="qris_file" wire:change="uploadQris" accept="image/*" class="hidden">
                    <div class="w-14 h-14 rounded-2xl bg-[#16192E] flex items-center justify-center border border-[#2E2A68] text-slate-400 group-hover:scale-110 group-hover:text-indigo-400 transition">
                        <x-icon name="upload" class="w-7 h-7" />
                    </div>
                    <div class="text-center">
                        <p class="font-black text-sm text-white">Ketuk untuk Mengunggah QRIS</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">
                            PNG, JPG, MAKS 2MB
                        </p>
                    </div>
                </label>
            @endif
        </div>

        <!-- 2. Delivery Platforms (Kanal Penjualan Online) -->
        <div class="bg-[#1E1B4B] p-6 rounded-3xl border border-[#2E2A68] shadow-sm flex flex-col gap-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-orange-500/20 text-orange-400 flex items-center justify-center border border-orange-500/40 shrink-0">
                    <x-icon name="bike" class="w-6 h-6 text-orange-400" />
                </div>
                <div>
                    <h2 class="font-black text-base text-white tracking-tight leading-none">Kanal Penjualan Online</h2>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-1 opacity-80">
                        Aktivasi Opsi Pembayaran Marketplace
                    </p>
                </div>
            </div>

            <div class="h-px w-full bg-[#2E2A68]"></div>

            <div class="flex flex-col border border-[#2E2A68] rounded-3xl overflow-hidden divide-y divide-[#2E2A68]">
                <!-- GoFood -->
                <div class="flex items-center justify-between p-4 bg-[#16192E]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                            <x-icon name="bike" class="w-5 h-5 text-emerald-400" />
                        </div>
                        <div>
                            <p class="font-black text-sm text-white">GoFood</p>
                            <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Layanan Delivery Online</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="enable_gofood" class="sr-only peer">
                        <div class="w-11 h-6 bg-[#0F172A] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#4338CA] border border-[#2E2A68]"></div>
                    </label>
                </div>

                <!-- GrabFood -->
                <div class="flex items-center justify-between p-4 bg-[#16192E]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-green-500/20 text-green-400 flex items-center justify-center">
                            <x-icon name="truck" class="w-5 h-5 text-green-400" />
                        </div>
                        <div>
                            <p class="font-black text-sm text-white">GrabFood</p>
                            <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Layanan Delivery Online</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="enable_grabfood" class="sr-only peer">
                        <div class="w-11 h-6 bg-[#0F172A] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#4338CA] border border-[#2E2A68]"></div>
                    </label>
                </div>

                <!-- ShopeeFood -->
                <div class="flex items-center justify-between p-4 bg-[#16192E]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-500/20 text-orange-400 flex items-center justify-center">
                            <x-icon name="shopping-bag" class="w-5 h-5 text-orange-400" />
                        </div>
                        <div>
                            <p class="font-black text-sm text-white">ShopeeFood</p>
                            <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Layanan Delivery Online</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="enable_shopeefood" class="sr-only peer">
                        <div class="w-11 h-6 bg-[#0F172A] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#4338CA] border border-[#2E2A68]"></div>
                    </label>
                </div>
            </div>

            <div class="bg-[#4338CA]/10 p-4 rounded-2xl border border-[#4338CA]/20">
                <p class="text-xs text-indigo-300 font-medium leading-relaxed">
                    <em>Kanal yang diaktifkan akan muncul sebagai pilihan metode pembayaran di kasir. Anda dapat menyesuaikan nominal uang bersih (net) yang diterima untuk setiap pesanan platform saat pembayaran.</em>
                </p>
            </div>
        </div>
    </div>
</div>

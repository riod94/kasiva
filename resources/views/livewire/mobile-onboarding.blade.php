<div class="min-h-screen bg-[#0F172A] flex flex-col justify-between p-6 text-white selection:bg-[#4338CA]/30 selection:text-[#3EDAD7]">
    <!-- Header Controls -->
    <div class="flex items-center justify-between pt-2">
        <a href="{{ route('landing') }}">
            <img src="/images/kasiva-logo-full.png" alt="Kasiva POS" class="h-8 w-auto bg-white/95 p-1.5 rounded-xl object-contain shadow-sm">
        </a>
        
        <a href="{{ route('pos.cashier') }}" class="text-xs font-bold text-slate-300 hover:text-white bg-[#1E1B4B] border border-[#2E2A68] px-3.5 py-1.5 rounded-full transition shadow-sm flex items-center gap-1">
            <span>Lewati</span>
            <x-icon name="arrow-right" class="w-3.5 h-3.5" />
        </a>
    </div>

    <!-- Active Slide Content Card -->
    <div class="my-auto py-8 text-center space-y-6 max-w-sm mx-auto">
        <div class="w-24 h-24 mx-auto rounded-3xl bg-gradient-to-br from-[#4338CA]/30 to-[#1E1B4B] border border-[#2E2A68] flex items-center justify-center shadow-2xl">
            <x-icon name="{{ $slides[$currentSlide]['icon'] }}" class="w-12 h-12 text-[#3EDAD7]" />
        </div>

        <span class="inline-block text-[11px] font-black px-3.5 py-1 rounded-full bg-[#4338CA]/20 text-[#8696ED] border border-[#4338CA]/40 uppercase tracking-widest">
            {{ $slides[$currentSlide]['badge'] }}
        </span>

        <h2 class="text-2xl font-black leading-tight text-white tracking-tight">
            {{ $slides[$currentSlide]['title'] }}
        </h2>

        <p class="text-xs text-slate-300 leading-relaxed px-2 font-medium">
            {{ $slides[$currentSlide]['subtitle'] }}
        </p>
    </div>

    <!-- Bottom Actions & Indicators -->
    <div class="space-y-6 pb-4 max-w-sm mx-auto w-full">
        <!-- Slide Indicators -->
        <div class="flex justify-center items-center gap-2">
            @foreach($slides as $index => $slide)
                <button 
                    wire:click="setSlide({{ $index }})" 
                    class="h-2 rounded-full transition-all duration-300 {{ $currentSlide === $index ? 'w-8 bg-[#4338CA]' : 'w-2 bg-[#2E2A68]' }}"
                    aria-label="Slide {{ $index + 1 }}">
                </button>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            <button 
                wire:click="nextSlide" 
                class="w-full py-3.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-xl shadow-[#4338CA]/30 transition active:scale-95 flex items-center justify-center gap-2">
                <span>{{ $currentSlide === count($slides) - 1 ? 'Masuk ke Kasir POS' : 'Lanjutkan' }}</span>
                <x-icon name="arrow-right" class="w-4 h-4" />
            </button>

            <a href="{{ route('register') }}" class="block text-center w-full py-3 bg-[#1E1B4B] hover:bg-[#25215A] text-slate-200 border border-[#2E2A68] font-bold text-xs rounded-2xl transition shadow-sm">
                Daftar Akun Baru
            </a>
        </div>
    </div>
</div>

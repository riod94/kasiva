<div class="relative min-h-dvh overflow-hidden bg-[var(--app-bg)] text-[var(--text-main)]" aria-live="polite">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_15%,rgb(0_170_166_/_0.17),transparent_34%),radial-gradient(circle_at_10%_90%,rgb(134_150_237_/_0.14),transparent_34%)]"></div>
    <div class="relative mx-auto flex min-h-dvh max-w-5xl flex-col px-4 py-5 sm:px-8">
        <header class="flex items-center justify-between gap-3">
            <a href="{{ route('landing') }}" class="ks-focus flex min-h-11 items-center gap-2.5 rounded-xl"><img src="{{ asset('images/kasiva-logo-icon-128.png') }}" alt="" width="40" height="40" class="h-10 w-10 rounded-xl bg-white p-1"><span class="font-black">Kasiva</span></a>
            <div class="flex items-center gap-2"><button type="button" onclick="window.toggleKasivaTheme()" class="ks-focus flex h-11 w-11 items-center justify-center rounded-xl border border-[var(--border-color)] bg-[var(--card-sub-bg)]" aria-label="Ganti tema terang atau gelap"><x-icon name="sun" class="hidden h-4 w-4 dark:block" /><x-icon name="moon" class="h-4 w-4 dark:hidden" /></button><a href="{{ route('pos.cashier') }}" class="ks-btn-secondary">Lewati <x-icon name="arrow-right" class="h-4 w-4" /></a></div>
        </header>

        <main class="flex flex-1 items-center justify-center py-8" id="main-content">
            <section class="w-full max-w-xl text-center" aria-labelledby="onboarding-title">
                <p class="ks-eyebrow">Langkah {{ $currentSlide + 1 }} dari {{ count($slides) }}</p>
                <div class="mx-auto mt-5 flex h-24 w-24 items-center justify-center rounded-[28px] border border-[#00AAA6]/30 bg-gradient-to-br from-[#00AAA6]/20 to-[#8696ED]/10 text-[#00AAA6] shadow-xl shadow-[#00AAA6]/10"><x-icon name="{{ $slides[$currentSlide]['icon'] }}" class="h-11 w-11" /></div>
                <span class="mt-6 inline-flex rounded-full border border-[#00AAA6]/30 bg-[#00AAA6]/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-[#00AAA6]">{{ $slides[$currentSlide]['badge'] }}</span>
                <h1 id="onboarding-title" class="mt-4 text-3xl font-black leading-tight sm:text-4xl">{{ $slides[$currentSlide]['title'] }}</h1>
                <p class="ks-muted mx-auto mt-4 max-w-lg text-sm leading-7 sm:text-base">{{ $slides[$currentSlide]['subtitle'] }}</p>
            </section>
        </main>

        <footer class="mx-auto w-full max-w-xl pb-[env(safe-area-inset-bottom)]">
            <div class="mb-6 flex items-center gap-2" role="progressbar" aria-label="Progres onboarding" aria-valuemin="1" aria-valuemax="{{ count($slides) }}" aria-valuenow="{{ $currentSlide + 1 }}">
                @foreach($slides as $index => $slide)
                    <button wire:click="setSlide({{ $index }})" class="ks-focus h-11 flex-1 rounded-xl p-0" aria-label="Buka langkah {{ $index + 1 }}: {{ $slide['title'] }}" @if($currentSlide === $index) aria-current="step" @endif><span class="block h-1.5 rounded-full transition-all {{ $currentSlide >= $index ? 'bg-[#00AAA6]' : 'bg-[var(--border-color)]' }}"></span></button>
                @endforeach
            </div>
            <div class="grid gap-3 sm:grid-cols-2"><a href="{{ route('register') }}" class="ks-btn-secondary order-2 sm:order-1">Daftar akun baru</a><button wire:click="nextSlide" class="ks-btn-primary order-1 sm:order-2"><span>{{ $currentSlide === count($slides) - 1 ? 'Masuk ke Kasir POS' : 'Lanjutkan' }}</span><x-icon name="arrow-right" class="h-4 w-4" /></button></div>
        </footer>
    </div>
</div>

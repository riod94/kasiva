@php
    use App\Support\LandingContent;
    $platforms = LandingContent::platforms();
@endphp

<section class="py-20 md:py-28" aria-labelledby="multi-platform-title">
    <div class="mx-auto max-w-7xl px-4 md:px-8">
        <div class="mx-auto mb-14 max-w-2xl text-center">
            <p class="text-xs font-black uppercase tracking-[.22em] text-[#00AAA6]">Multi-platform</p>
            <h2 id="multi-platform-title" class="mt-4 text-3xl font-black md:text-5xl">Satu akun, empat perangkat.</h2>
            <p class="lp-muted mt-4 leading-7">Mulai dari browser, perluas ke aplikasi native Android, iOS, dan desktop tanpa migrasi data.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($platforms as $p)
                <div class="lp-card group rounded-3xl p-5 transition hover:-translate-y-0.5">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#00AAA6]/15 text-[#00AAA6]">
                        <x-icon :name="$p['icon']" class="h-5 w-5" />
                    </span>
                    <h3 class="mt-5 text-base font-black">{{ $p['name'] }}</h3>
                    <p class="lp-muted mt-1 text-sm leading-6">{{ $p['desc'] }}</p>
                    <span class="mt-4 inline-flex rounded-full px-2.5 py-1 text-[10px] font-black {{ $p['status'] === 'Tersedia' ? 'bg-[#00AAA6]/15 text-[#00AAA6]' : 'bg-[#8696ED]/15 text-[#8696ED]' }}">{{ strtoupper($p['status']) }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

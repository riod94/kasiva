@php
    use App\Support\LandingContent;
    $steps = LandingContent::howItWorks();
@endphp

<section id="cara-kerja" class="py-20 md:py-28" aria-labelledby="cara-kerja-title">
    <div class="mx-auto max-w-7xl px-4 md:px-8">
        <div class="mx-auto mb-14 max-w-2xl text-center">
            <p class="text-xs font-black uppercase tracking-[.22em] text-[#00AAA6]">Mulai tanpa kerumitan</p>
            <h2 id="cara-kerja-title" class="mt-4 text-3xl font-black md:text-5xl">Dari daftar hingga transaksi pertama.</h2>
        </div>
        <ol class="grid gap-5 md:grid-cols-3">
            @foreach ($steps as $step)
                <li class="lp-card relative rounded-3xl p-6">
                    <span class="absolute right-6 top-5 text-4xl font-black text-[#8696ED]/25" aria-hidden="true">{{ $step['number'] }}</span>
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#00AAA6]/15 text-[#00AAA6]">
                        <x-icon :name="$step['icon']" class="h-6 w-6" />
                    </span>
                    <h3 class="mt-6 text-lg font-black">{{ $step['title'] }}</h3>
                    <p class="lp-muted mt-2 text-sm leading-6">{{ $step['desc'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

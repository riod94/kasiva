@php
    use App\Support\LandingContent;
    $packages = LandingContent::pricingPackages();
@endphp

<section id="harga" class="border-y lp-border lp-surface py-20 md:py-28" aria-labelledby="harga-title">
    <div class="mx-auto max-w-7xl px-4 md:px-8">
        <div class="mx-auto mb-14 max-w-2xl text-center">
            <p class="text-xs font-black uppercase tracking-[.22em] text-[#00AAA6]">Harga transparan</p>
            <h2 id="harga-title" class="mt-4 text-3xl font-black md:text-5xl">Mulai tanpa beban biaya.</h2>
            <p class="lp-muted mt-4 leading-7">Versi awal Kasiva tersedia gratis. Paket Pro dan Enterprise sedang disiapkan untuk fase berikutnya.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            @foreach ($packages as $pkg)
                <article class="lp-card relative flex flex-col rounded-3xl p-7 {{ $pkg['comingSoon'] ? 'opacity-90' : '' }}">
                    @if ($pkg['badge'])
                        <span class="absolute right-6 top-6 rounded-full bg-[#00AAA6]/20 px-3 py-1 text-[10px] font-black tracking-widest text-[#3EDAD7]">{{ $pkg['badge'] }}</span>
                    @elseif ($pkg['comingSoon'])
                        <span class="absolute right-6 top-6 rounded-full bg-[#8696ED]/20 px-3 py-1 text-[10px] font-black tracking-widest text-[#8696ED]">COMING SOON</span>
                    @endif

                    <h3 class="text-lg font-black">{{ $pkg['name'] }}</h3>
                    <p class="mt-3 text-3xl font-black {{ $pkg['comingSoon'] ? 'lp-muted' : 'lp-gradient-text' }}">{{ $pkg['price'] }}</p>
                    <p class="lp-muted mt-1 text-xs">{{ $pkg['priceSuffix'] }}</p>

                    <ul class="mt-6 flex-1 space-y-2 text-sm font-bold">
                        @foreach ($pkg['highlights'] as $h)
                            <li class="flex items-start gap-2">
                                <x-icon name="check" class="mt-0.5 h-4 w-4 text-[#00AAA6]" />
                                <span>{{ $h }}</span>
                            </li>
                        @endforeach
                    </ul>

                    @if ($pkg['note'])
                        <p class="lp-muted mt-6 border-t pt-4 text-xs leading-5">{{ $pkg['note'] }}</p>
                    @endif

                    <a href="{{ $pkg['comingSoon'] ? route('about') : route('register') }}" class="lp-focus mt-7 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl {{ $pkg['comingSoon'] ? 'border lp-border lp-surface-2' : 'bg-[#00AAA6] text-white hover:bg-[#008F8C]' }} text-sm font-extrabold transition">
                        {{ $pkg['cta'] }}
                        @if (! $pkg['comingSoon'])
                            <x-icon name="arrow-right" class="h-4 w-4" />
                        @endif
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>

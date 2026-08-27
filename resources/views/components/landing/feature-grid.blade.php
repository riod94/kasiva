@php
    use App\Support\LandingContent;
    $features = LandingContent::features();
    $accentMap = [
        'teal' => ['border' => 'border-[#00AAA6]/40', 'iconBg' => 'bg-[#00AAA6]/15', 'iconText' => 'text-[#00AAA6]', 'hoverShadow' => 'hover:shadow-[0_18px_50px_rgba(0,170,166,0.18)]'],
        'violet' => ['border' => 'border-[#8696ED]/40', 'iconBg' => 'bg-[#8696ED]/15', 'iconText' => 'text-[#8696ED]', 'hoverShadow' => 'hover:shadow-[0_18px_50px_rgba(134,150,237,0.22)]'],
        'cyan' => ['border' => 'border-[#3EDAD7]/40', 'iconBg' => 'bg-[#3EDAD7]/15', 'iconText' => 'text-[#3EDAD7]', 'hoverShadow' => 'hover:shadow-[0_18px_50px_rgba(62,218,215,0.22)]'],
    ];
@endphp

<section id="fitur" class="border-y lp-border lp-surface py-20 md:py-28" aria-labelledby="fitur-title">
    <div class="mx-auto max-w-7xl px-4 md:px-8">
        <div class="mx-auto mb-14 max-w-2xl text-center">
            <p class="text-xs font-black uppercase tracking-[.22em] text-[#00AAA6]">Fitur</p>
            <h2 id="fitur-title" class="mt-4 text-3xl font-black md:text-5xl">Semua yang dibutuhkan kasir F&amp;B dan retail.</h2>
            <p class="lp-muted mt-4 leading-7">Enam pilar utama yang membedakan Kasiva dari kasir biasa—dirancang untuk operasional nyata, bukan demo.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($features as $f)
                @php $a = $accentMap[$f['accent']]; @endphp
                <article class="lp-card group rounded-3xl p-6 transition hover:-translate-y-0.5 {{ $a['hoverShadow'] }} border {{ $a['border'] }}">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $a['iconBg'] }} {{ $a['iconText'] }}">
                        <x-icon :name="$f['icon']" class="h-6 w-6" />
                    </span>
                    <h3 class="mt-6 text-lg font-black">{{ $f['title'] }}</h3>
                    <p class="lp-muted mt-2 text-sm leading-6">{{ $f['desc'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

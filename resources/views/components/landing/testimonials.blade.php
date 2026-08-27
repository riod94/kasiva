@php
    use App\Support\LandingContent;
    $items = LandingContent::testimonials();
@endphp

<section class="py-20 md:py-28" aria-labelledby="studi-kasus-title">
    <div class="mx-auto max-w-7xl px-4 md:px-8">
        <div class="mx-auto mb-12 max-w-2xl text-center">
            <p class="text-xs font-black uppercase tracking-[.22em] text-[#00AAA6]">Studi kasus</p>
            <h2 id="studi-kasus-title" class="mt-4 text-3xl font-black md:text-5xl">Cerita dari mereka yang sudah mencoba.</h2>
            <p class="lp-muted mt-4 leading-7">Cuplikan penggunaan awal dari pemilik outlet F&amp;B dan retail di Indonesia.</p>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            @foreach ($items as $t)
                <figure class="lp-card relative flex h-full flex-col rounded-3xl p-6">
                    @if ($t['comingSoon'])
                        <span class="absolute right-5 top-5 rounded-full bg-[#8696ED]/15 px-2.5 py-1 text-[10px] font-black tracking-widest text-[#8696ED]">COMING SOON</span>
                    @endif
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[#00AAA6]/15 text-sm font-black text-[#00AAA6]" aria-hidden="true">{{ $t['initials'] }}</span>
                        <figcaption class="text-sm">
                            <p class="font-black">{{ $t['name'] }}</p>
                            <p class="lp-muted text-xs">{{ $t['role'] }}</p>
                        </figcaption>
                    </div>
                    <blockquote class="lp-muted mt-5 flex-1 text-sm leading-7">“{{ $t['quote'] }}”</blockquote>
                </figure>
            @endforeach
        </div>
    </div>
</section>

@php
    use App\Support\LandingContent;
    $faqs = LandingContent::faqs();
@endphp

<section id="faq" class="py-20 md:py-28" aria-labelledby="faq-title">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 md:px-8 lg:grid-cols-[.7fr_1.3fr] lg:gap-20">
        <div>
            <p class="text-xs font-black uppercase tracking-[.22em] text-[#00AAA6]">Pertanyaan umum</p>
            <h2 id="faq-title" class="mt-4 text-3xl font-black md:text-5xl">Hal penting sebelum mulai.</h2>
            <p class="lp-muted mt-5 leading-7">Belum menemukan jawaban? Kunjungi halaman <a href="{{ route('about') }}" class="font-bold underline-offset-4 hover:underline">Tentang Kasiva</a> untuk kontak tim.</p>
        </div>
        <div class="space-y-3">
            @foreach ($faqs as $index => $faq)
                <details id="faq-{{ $faq['slug'] }}" class="lp-card group rounded-2xl p-5" @if ($index === 0) open @endif>
                    <summary class="lp-focus flex cursor-pointer list-none items-center justify-between gap-5 rounded-lg font-black">
                        <span>{{ $faq['q'] }}</span>
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#00AAA6]/15 text-[#00AAA6] transition group-open:rotate-45" aria-hidden="true">+</span>
                    </summary>
                    <p class="lp-muted mt-4 border-t pt-4 text-sm leading-7">{{ $faq['a'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>

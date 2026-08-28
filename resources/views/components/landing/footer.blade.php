<footer class="border-t lp-border lp-surface">
    <div class="mx-auto max-w-7xl px-4 py-12 md:px-8">
        <div class="grid gap-10 md:grid-cols-[1.4fr_1fr_1fr_1fr]">
            <div>
                <a href="{{ route('landing') }}" class="lp-focus inline-flex min-h-11 items-center gap-3 rounded-xl"><img src="{{ asset('images/kasiva-logo-icon-128.png') }}" alt="" width="40" height="40" class="h-10 w-10 rounded-xl bg-white p-1"><span class="text-xl font-black">Kasiva</span></a>
                <p class="lp-muted mt-4 max-w-sm text-sm leading-6">POS pintar untuk F&amp;B dan retail Indonesia—dari transaksi pertama hingga profit bersih.</p>
            </div>
            <div>
                <h2 class="text-sm font-black">Produk</h2>
                <nav class="lp-muted mt-4 flex flex-col items-start gap-3 text-sm font-bold" aria-label="Navigasi produk">
                    <a href="#fitur" class="hover:text-[#00AAA6]">Fitur</a>
                    <a href="#harga" class="hover:text-[#00AAA6]">Harga</a>
                    <a href="{{ route('onboarding') }}" class="hover:text-[#00AAA6]">Panduan mobile</a>
                    <a href="{{ route('login') }}" class="hover:text-[#00AAA6]">Masuk</a>
                </nav>
            </div>
            <div>
                <h2 class="text-sm font-black">Sumber daya</h2>
                <nav class="lp-muted mt-4 flex flex-col items-start gap-3 text-sm font-bold" aria-label="Sumber daya">
                    <a href="#faq" class="hover:text-[#00AAA6]">FAQ</a>
                    <a href="{{ route('about') }}" class="hover:text-[#00AAA6]">Tentang</a>
                    <span class="cursor-not-allowed opacity-60" title="Segera hadir">Dokumentasi</span>
                    <span class="cursor-not-allowed opacity-60" title="Segera hadir">Changelog</span>
                </nav>
            </div>
            <div>
                <h2 class="text-sm font-black">Perusahaan &amp; legal</h2>
                <nav class="lp-muted mt-4 flex flex-col items-start gap-3 text-sm font-bold" aria-label="Perusahaan dan legal">
                    <a href="{{ route('about') }}" class="hover:text-[#00AAA6]">Tentang Kasiva</a>
                    <a href="{{ route('privacy') }}" class="hover:text-[#00AAA6]">Kebijakan privasi</a>
                    <a href="{{ route('terms') }}" class="hover:text-[#00AAA6]">Syarat &amp; ketentuan</a>
                </nav>
            </div>
        </div>
        <div class="lp-muted mt-10 flex flex-col justify-between gap-3 border-t pt-6 text-xs font-medium sm:flex-row">
            <p>© {{ date('Y') }} Kasiva POS. Hak cipta dilindungi.</p>
            <p>Dibuat untuk pertumbuhan bisnis Indonesia.</p>
        </div>
    </div>
</footer>

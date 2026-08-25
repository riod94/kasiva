<div class="relative min-h-dvh overflow-hidden bg-[var(--app-bg)] text-[var(--text-main)] selection:bg-[#00AAA6]/25">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_12%_12%,rgb(0_170_166_/_0.14),transparent_35%),radial-gradient(circle_at_88%_86%,rgb(134_150_237_/_0.15),transparent_35%)]"></div>
    <div class="relative mx-auto grid min-h-dvh max-w-7xl lg:grid-cols-[.9fr_1.1fr]">
        <aside class="hidden flex-col justify-between border-r border-[var(--border-color)] p-10 lg:flex xl:p-14">
            <a href="{{ route('landing') }}" class="ks-focus inline-flex min-h-11 w-fit items-center gap-3 rounded-xl"><img src="{{ asset('images/kasiva-logo-icon-128.png') }}" alt="" width="44" height="44" class="h-11 w-11 rounded-xl bg-white p-1"><span class="text-xl font-black">Kasiva</span></a>
            <div class="max-w-lg"><p class="ks-eyebrow">Mulai tanpa biaya setup</p><h1 class="mt-4 text-4xl font-black leading-tight xl:text-5xl">Bangun fondasi outlet yang <span class="text-[#00AAA6]">lebih tertib sejak hari pertama.</span></h1><p class="ks-muted mt-5 leading-7">Buat outlet, susun katalog, undang staf, dan terima transaksi pertama dalam satu alur yang jelas.</p>
                <ol class="mt-8 space-y-4">@foreach([['01','Buat akun pemilik'],['02','Lengkapi outlet dan produk'],['03','Mulai transaksi']] as $step)<li class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#00AAA6]/15 text-xs font-black text-[#00AAA6]">{{ $step[0] }}</span><span class="text-sm font-extrabold">{{ $step[1] }}</span></li>@endforeach</ol>
            </div>
            <p class="ks-muted text-xs">Gratis untuk versi awal • Data outlet tetap milik Anda</p>
        </aside>

        <main class="flex items-center justify-center px-4 py-8 sm:px-8 lg:px-12" id="main-content">
            <div class="w-full max-w-lg">
                <div class="mb-6 flex items-center justify-between lg:hidden"><a href="{{ route('landing') }}" class="ks-focus flex min-h-11 items-center gap-2.5 rounded-xl"><img src="{{ asset('images/kasiva-logo-icon-128.png') }}" alt="" width="40" height="40" class="h-10 w-10 rounded-xl bg-white p-1"><span class="text-lg font-black">Kasiva</span></a><button type="button" onclick="window.toggleKasivaTheme()" class="ks-focus flex h-11 w-11 items-center justify-center rounded-xl border border-[var(--border-color)] bg-[var(--card-sub-bg)]" aria-label="Ganti tema terang atau gelap"><x-icon name="sun" class="hidden h-4 w-4 dark:block" /><x-icon name="moon" class="h-4 w-4 dark:hidden" /></button></div>
                <section class="ks-card p-6 sm:p-8" aria-labelledby="register-title">
                    <div><p class="ks-eyebrow">Akun baru</p><h2 id="register-title" class="mt-2 text-2xl font-black">Daftarkan outlet</h2><p class="ks-muted mt-2 text-sm leading-6">Informasi ini dipakai untuk membuat akun pemilik pertama.</p></div>
                    <form wire:submit="register" class="mt-6 grid gap-4 sm:grid-cols-2" novalidate>
                        <div class="sm:col-span-2"><label for="outlet-name" class="ks-label">Nama outlet atau brand</label><input id="outlet-name" type="text" wire:model="outlet_name" autocomplete="organization" placeholder="Contoh: Kedai Kopi Kasiva" class="ks-input">@error('outlet_name')<p role="alert" class="mt-1.5 text-xs font-bold text-rose-500">{{ $message }}</p>@enderror</div>
                        <div><label for="owner-name" class="ks-label">Nama pemilik</label><input id="owner-name" type="text" wire:model="name" autocomplete="name" placeholder="Nama lengkap" class="ks-input">@error('name')<p role="alert" class="mt-1.5 text-xs font-bold text-rose-500">{{ $message }}</p>@enderror</div>
                        <div><label for="owner-phone" class="ks-label">Nomor WhatsApp</label><input id="owner-phone" type="tel" wire:model="phone" autocomplete="tel" inputmode="tel" placeholder="0812…" class="ks-input">@error('phone')<p role="alert" class="mt-1.5 text-xs font-bold text-rose-500">{{ $message }}</p>@enderror</div>
                        <div class="sm:col-span-2"><label for="owner-email" class="ks-label">Alamat email</label><input id="owner-email" type="email" wire:model="email" autocomplete="email" inputmode="email" placeholder="owner@outlet.com" class="ks-input">@error('email')<p role="alert" class="mt-1.5 text-xs font-bold text-rose-500">{{ $message }}</p>@enderror</div>
                        <div class="sm:col-span-2"><label for="owner-password" class="ks-label">Kata sandi</label><input id="owner-password" type="password" wire:model="password" autocomplete="new-password" placeholder="Minimal 8 karakter" class="ks-input"><p class="ks-muted mt-1.5 text-[11px]">Gunakan kombinasi yang tidak dipakai di layanan lain.</p>@error('password')<p role="alert" class="mt-1.5 text-xs font-bold text-rose-500">{{ $message }}</p>@enderror</div>
                        <button type="submit" class="ks-btn-primary mt-1 w-full sm:col-span-2" wire:loading.attr="disabled" wire:target="register"><span wire:loading.remove wire:target="register">Buat akun dan outlet</span><span wire:loading wire:target="register">Menyiapkan outlet…</span><x-icon name="arrow-right" class="h-4 w-4" /></button>
                    </form>
                    <div class="mt-6 border-t border-[var(--border-color)] pt-5 text-center text-sm"><p class="ks-muted">Sudah punya akun? <a href="{{ route('login') }}" class="ks-focus rounded font-extrabold text-[#00AAA6] hover:underline">Masuk sekarang</a></p><p class="ks-muted mt-3 text-[11px]">Dengan mendaftar, Anda menyetujui <a href="{{ route('terms') }}" class="underline">ketentuan</a> dan <a href="{{ route('privacy') }}" class="underline">kebijakan privasi</a>.</p></div>
                </section>
            </div>
        </main>
    </div>
</div>

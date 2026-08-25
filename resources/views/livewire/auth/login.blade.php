<div class="relative min-h-dvh overflow-hidden bg-[var(--app-bg)] text-[var(--text-main)] selection:bg-[#00AAA6]/25">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_10%_10%,rgb(134_150_237_/_0.14),transparent_34%),radial-gradient(circle_at_90%_85%,rgb(0_170_166_/_0.15),transparent_34%)]"></div>
    <div class="relative mx-auto grid min-h-dvh max-w-7xl items-stretch lg:grid-cols-[1.05fr_.95fr]">
        <aside class="hidden flex-col justify-between border-r border-[var(--border-color)] p-10 lg:flex xl:p-14" aria-label="Manfaat Kasiva">
            <a href="{{ route('landing') }}" class="ks-focus inline-flex min-h-11 w-fit items-center gap-3 rounded-xl">
                <img src="{{ asset('images/kasiva-logo-icon-128.png') }}" alt="" width="44" height="44" class="h-11 w-11 rounded-xl bg-white p-1 shadow-sm"><span class="text-xl font-black">Kasiva</span>
            </a>
            <div class="max-w-xl">
                <p class="ks-eyebrow">Selamat datang kembali</p>
                <h1 class="mt-4 text-4xl font-black leading-tight tracking-tight xl:text-5xl">Satu login untuk menjaga <span class="text-[#00AAA6]">seluruh ritme outlet.</span></h1>
                <p class="ks-muted mt-5 max-w-lg leading-7">Lanjutkan transaksi, pantau stok bahan, dan lihat profit outlet dari tempat terakhir Anda berhenti.</p>
                <div class="mt-8 grid gap-3 sm:grid-cols-3">
                    @foreach([['shield','Akses berbasis peran'],['refresh','Siap offline'],['chart-bar','Profit transparan']] as $item)
                        <div class="ks-panel p-4"><x-icon name="{{ $item[0] }}" class="h-5 w-5 text-[#00AAA6]" /><p class="mt-3 text-xs font-extrabold">{{ $item[1] }}</p></div>
                    @endforeach
                </div>
            </div>
            <p class="ks-muted text-xs">Kasiva POS • Dibuat untuk F&amp;B dan retail Indonesia</p>
        </aside>

        <main class="flex items-center justify-center px-4 py-10 sm:px-8 lg:px-12" id="main-content">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center justify-between lg:hidden">
                    <a href="{{ route('landing') }}" class="ks-focus flex min-h-11 items-center gap-2.5 rounded-xl"><img src="{{ asset('images/kasiva-logo-icon-128.png') }}" alt="" width="40" height="40" class="h-10 w-10 rounded-xl bg-white p-1"><span class="text-lg font-black">Kasiva</span></a>
                    <button type="button" onclick="window.toggleKasivaTheme()" class="ks-focus flex h-11 w-11 items-center justify-center rounded-xl border border-[var(--border-color)] bg-[var(--card-sub-bg)]" aria-label="Ganti tema terang atau gelap"><x-icon name="sun" class="hidden h-4 w-4 dark:block" /><x-icon name="moon" class="h-4 w-4 dark:hidden" /></button>
                </div>

                <section class="ks-card p-6 sm:p-8" aria-labelledby="login-title">
                    <div><p class="ks-eyebrow">Akun outlet</p><h2 id="login-title" class="mt-2 text-2xl font-black">Masuk ke Kasiva</h2><p class="ks-muted mt-2 text-sm leading-6">Gunakan email dan kata sandi akun Anda.</p></div>
                    @if(session('warning'))<div role="alert" class="mt-5 rounded-xl border border-amber-500/30 bg-amber-500/10 p-3 text-xs font-bold text-amber-600 dark:text-amber-300">{{ session('warning') }}</div>@endif
                    <form wire:submit="login" class="mt-6 space-y-5" novalidate>
                        <div><label for="login-email" class="ks-label">Alamat email</label><input id="login-email" type="email" wire:model="email" autocomplete="email" inputmode="email" placeholder="nama@outlet.com" class="ks-input" aria-describedby="login-email-error">@error('email')<p id="login-email-error" role="alert" class="mt-1.5 text-xs font-bold text-rose-500">{{ $message }}</p>@enderror</div>
                        <div><label for="login-password" class="ks-label">Kata sandi</label><input id="login-password" type="password" wire:model="password" autocomplete="current-password" placeholder="Masukkan kata sandi" class="ks-input" aria-describedby="login-password-error">@error('password')<p id="login-password-error" role="alert" class="mt-1.5 text-xs font-bold text-rose-500">{{ $message }}</p>@enderror</div>
                        <label class="ks-muted flex min-h-11 cursor-pointer items-center gap-2.5 text-sm font-semibold"><input type="checkbox" wire:model="remember" class="h-4 w-4 rounded border-[var(--border-color)] bg-[var(--card-sub-bg)] text-[#00AAA6] focus:ring-[#00AAA6]"> Tetap masuk di perangkat ini</label>
                        <button type="submit" class="ks-btn-primary w-full" wire:loading.attr="disabled" wire:target="login"><span wire:loading.remove wire:target="login">Masuk ke Kasiva</span><span wire:loading wire:target="login">Memeriksa akun…</span><x-icon name="arrow-right" class="h-4 w-4" /></button>
                    </form>
                    <div class="mt-6 border-t border-[var(--border-color)] pt-5 text-center text-sm"><p class="ks-muted">Belum punya akun? <a href="{{ route('register') }}" class="ks-focus rounded font-extrabold text-[#00AAA6] hover:underline">Daftar outlet gratis</a></p><a href="{{ route('landing') }}" class="ks-focus ks-muted mt-3 inline-flex min-h-11 items-center gap-1.5 rounded-lg text-xs font-bold hover:text-[var(--text-main)]"><x-icon name="arrow-left" class="h-3.5 w-3.5" /> Kembali ke beranda</a></div>
                </section>
            </div>
        </main>
    </div>
</div>

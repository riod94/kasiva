<div class="space-y-6">
<section class="ks-card p-6 md:p-8"><p class="ks-eyebrow">Konfigurasi dan keamanan</p><h1 class="mt-2 text-2xl font-black md:text-3xl">Pusat Pengaturan Outlet</h1><p class="ks-muted mt-2 max-w-2xl text-sm leading-6">Atur identitas outlet, struk, pembayaran, staf, hak akses, dan keamanan akun.</p></section>
<section aria-labelledby="settings-modules"><div class="mb-4 flex items-center justify-between"><h2 id="settings-modules" class="text-base font-black">Pengaturan tersedia</h2><span class="ks-muted text-xs">Sesuai hak akses Anda</span></div><div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
@foreach([
['settings.outlet','store','Informasi Outlet','Nama, alamat, kontak, pajak, dan biaya layanan.'],
['settings.receipt','printer','Pengaturan Struk','Logo, footer, dan ukuran kertas thermal.'],
['settings.payment','qr-code','Metode Pembayaran','QRIS dan kanal delivery online.'],
['settings.staff','users','Manajemen Staf','Akun operator, status, dan PIN kasir.'],
['settings.roles','shield','Hak Akses & Peran','Permission Owner, Manager, dan Kasir.'],
['profile.show','lock-closed','Profil & Keamanan','Profil, kata sandi, PIN, dan sesi akun.'],
] as $item)<a href="{{ route($item[0]) }}" class="ks-card ks-focus group flex min-h-40 items-start gap-4 p-5 transition hover:-translate-y-0.5 hover:border-[#00AAA6]"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-[#00AAA6]/25 bg-[#00AAA6]/10 text-[#00AAA6]"><x-icon name="{{ $item[1] }}" class="h-6 w-6"/></span><span class="min-w-0 flex-1"><span class="flex items-center justify-between gap-3"><strong class="text-sm font-black">{{ $item[2] }}</strong><x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-[var(--text-muted)] transition group-hover:translate-x-0.5 group-hover:text-[#00AAA6]"/></span><span class="ks-muted mt-2 block text-xs leading-5">{{ $item[3] }}</span></span></a>@endforeach
</div></section>
</div>

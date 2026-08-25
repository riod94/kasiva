<div class="space-y-6">
    <section class="ks-card relative overflow-hidden p-6 md:p-8"><div class="pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full bg-[#00AAA6]/10 blur-3xl"></div><div class="relative"><p class="ks-eyebrow">Retensi dan promosi</p><h1 class="mt-2 text-2xl font-black md:text-3xl">Pusat Pemasaran &amp; Loyalitas</h1><p class="ks-muted mt-2 max-w-2xl text-sm leading-6">Kelola member, stempel loyalitas, paket bundle, diskon, dan kampanye dari satu tempat.</p></div></section>
    <section aria-labelledby="marketing-modules"><div class="mb-4 flex items-center justify-between"><h2 id="marketing-modules" class="text-base font-black">Modul pemasaran</h2><span class="ks-muted text-xs">5 modul aktif</span></div><div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    @foreach([
      ['marketing.members','users','Database Member & QR','Profil pelanggan, QR member, dan riwayat kunjungan.','emerald'],
      ['marketing.loyalty','gift','Loyalitas & Stempel Digital','Target stempel, reward, dan pesan WhatsApp.','amber'],
      ['marketing.bundles','package','Paket Bundle Menu','Paket kombo dengan perhitungan HPP.','violet'],
      ['marketing.discounts','tag','Diskon & Promo Menu','Diskon persen, nominal, dan Buy X Get Y.','blue'],
      ['marketing.campaigns','megaphone','Kampanye & Promosi','Event musiman dan promosi terjadwal.','rose'],
    ] as $item)
      @php($tone = match($item[4]) {'emerald'=>'bg-emerald-500/10 text-emerald-500 border-emerald-500/25','amber'=>'bg-amber-500/10 text-amber-500 border-amber-500/25','violet'=>'bg-[#8696ED]/10 text-[#8696ED] border-[#8696ED]/25','blue'=>'bg-blue-500/10 text-blue-500 border-blue-500/25',default=>'bg-rose-500/10 text-rose-500 border-rose-500/25'})
      <a href="{{ route($item[0]) }}" class="ks-card ks-focus group flex min-h-40 items-start gap-4 p-5 transition hover:-translate-y-0.5 hover:border-[#00AAA6]"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border {{ $tone }}"><x-icon name="{{ $item[1] }}" class="h-6 w-6"/></span><span class="min-w-0 flex-1"><span class="flex items-center justify-between gap-3"><strong class="text-sm font-black">{{ $item[2] }}</strong><x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-[var(--text-muted)] transition group-hover:translate-x-0.5 group-hover:text-[#00AAA6]"/></span><span class="ks-muted mt-2 block text-xs leading-5">{{ $item[3] }}</span></span></a>
    @endforeach
    </div></section>
</div>

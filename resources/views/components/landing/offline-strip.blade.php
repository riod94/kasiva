<section id="keunggulan" class="border-y lp-border lp-surface-2 py-20 md:py-28" aria-labelledby="offline-title">
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 md:px-8 lg:grid-cols-2">
        <div>
            <p class="text-xs font-black uppercase tracking-[.22em] text-[#3EDAD7]">Offline-first</p>
            <h2 id="offline-title" class="mt-4 text-3xl font-black md:text-5xl">Wi-Fi mati, antrean tetap jalan.</h2>
            <p class="lp-muted mt-5 leading-7">Setiap transaksi, mutasi stok, dan percetakan struk tetap berfungsi tanpa internet. Saat koneksi kembali, Kasiva menyinkronkan perbedaan secara otomatis.</p>
            <ul class="mt-8 space-y-4 text-sm font-bold">
                @foreach ([
                    ['icon' => 'check', 'text' => 'Transaksi offline aman di IndexedDB + SQLite lokal'],
                    ['icon' => 'refresh', 'text' => 'Antrean sinkronisasi otomatis saat online'],
                    ['icon' => 'shield', 'text' => 'Tidak ada data hilang — retry dengan idempotency key'],
                    ['icon' => 'lock-closed', 'text' => 'Kredensial terenkripsi di perangkat (Crypt facade)'],
                ] as $b)
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-7 w-7 items-center justify-center rounded-lg bg-[#00AAA6]/15 text-[#00AAA6]"><x-icon :name="$b['icon']" class="h-4 w-4" /></span>
                        <span>{{ $b['text'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="relative" aria-hidden="true">
            <div class="lp-card rounded-3xl p-6">
                <div class="flex items-center justify-between border-b pb-4">
                    <div class="flex items-center gap-2 text-xs font-extrabold">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#3EDAD7]"></span>
                        Online — sinkron aktif
                    </div>
                    <span class="text-[10px] lp-muted font-bold">3 antrean tertunda</span>
                </div>
                <div class="mt-4 space-y-3 text-xs">
                    @foreach ([
                        ['time' => '13:42', 'status' => 'synced', 'text' => 'Transaksi #KSV-8421 terkirim'],
                        ['time' => '13:39', 'status' => 'synced', 'text' => 'Restok bahan kopi +2kg'],
                        ['time' => '13:30', 'status' => 'pending', 'text' => 'Pengeluaran listrik Agustus'],
                    ] as $row)
                        <div class="flex items-center justify-between rounded-xl lp-surface-2 px-3 py-2">
                            <span class="lp-muted">{{ $row['time'] }}</span>
                            <span class="flex-1 px-3 font-bold">{{ $row['text'] }}</span>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-black {{ $row['status'] === 'synced' ? 'bg-[#00AAA6]/15 text-[#00AAA6]' : 'bg-[#8696ED]/15 text-[#8696ED]' }}">{{ strtoupper($row['status']) }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="lp-muted mt-4 text-[11px] leading-5">Saat Wi-Fi outlet mati, mode lokal otomatis mengambil alih. Begitu koneksi kembali, antrean akan terkirim urut berdasarkan `client_operation_id`.</p>
            </div>
        </div>
    </div>
</section>

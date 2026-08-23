<div class="space-y-6">
    <!-- Header Page & Period Selector -->
    <div class="flex flex-col gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <span>Laporan Finansial & Analisis Cerdas</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Analisis pendapatan, modal HPP, efisiensi operasional, dan proyeksi bisnis</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('history.index') }}" class="px-3.5 py-2 bg-[#16192E] hover:bg-[#4338CA] text-slate-300 hover:text-white rounded-xl text-xs font-bold border border-[#2E2A68] transition flex items-center gap-1.5 shadow-sm">
                    <x-icon name="receipt" class="w-3.5 h-3.5" />
                    <span>Riwayat</span>
                </a>
                <a href="{{ route('expenses.index') }}" class="px-3.5 py-2 bg-[#16192E] hover:bg-[#F97316] text-slate-300 hover:text-white rounded-xl text-xs font-bold border border-[#2E2A68] transition flex items-center gap-1.5 shadow-sm">
                    <x-icon name="wallet" class="w-3.5 h-3.5" />
                    <span>Pengeluaran</span>
                </a>
            </div>
        </div>

        <!-- Period Filter Pills -->
        <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-[#2E2A68]">
            <button 
                wire:click="setPeriod('HARI_INI')"
                class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition {{ $period === 'HARI_INI' ? 'bg-[#4338CA] text-white shadow-md' : 'bg-[#16192E] text-slate-400 hover:text-white border border-[#2E2A68]' }}"
            >
                Hari Ini
            </button>
            <button 
                wire:click="setPeriod('BULAN_INI')"
                class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition {{ $period === 'BULAN_INI' ? 'bg-[#4338CA] text-white shadow-md' : 'bg-[#16192E] text-slate-400 hover:text-white border border-[#2E2A68]' }}"
            >
                Bulan Ini
            </button>
            <button 
                wire:click="setPeriod('SEMUA')"
                class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition {{ $period === 'SEMUA' ? 'bg-[#4338CA] text-white shadow-md' : 'bg-[#16192E] text-slate-400 hover:text-white border border-[#2E2A68]' }}"
            >
                Semua Data
            </button>
        </div>
    </div>

    <!-- ═══════════════ Hero: Net Profit Card ═══════════════ -->
    <div class="p-6 md:p-8 rounded-3xl relative overflow-hidden shadow-2xl text-white {{ $netProfit >= 0 ? 'bg-gradient-to-br from-emerald-600 to-teal-700 shadow-emerald-500/20' : 'bg-gradient-to-br from-rose-600 to-red-700 shadow-rose-500/20' }}">
        <div class="relative z-10 space-y-3">
            <span class="text-xs font-black uppercase tracking-widest opacity-80 block">
                Pendapatan Bersih (Net Profit)
            </span>
            <p class="text-3xl sm:text-5xl font-black tracking-tighter leading-none">
                {{ $netProfit < 0 ? '−' : '' }}Rp {{ number_format(abs($netProfit), 0, ',', '.') }}
            </p>
            <div class="flex flex-wrap gap-2 pt-2">
                <span class="text-xs font-black bg-black/20 px-3 py-1.5 rounded-xl uppercase tracking-wider backdrop-blur-sm flex items-center gap-1.5">
                    <x-icon name="shopping-bag" class="w-3.5 h-3.5" />
                    <span>{{ $txCount }} Transaksi</span>
                </span>
                <span class="text-xs font-black bg-black/20 px-3 py-1.5 rounded-xl uppercase tracking-wider backdrop-blur-sm flex items-center gap-1.5">
                    <x-icon name="wallet" class="w-3.5 h-3.5" />
                    <span>{{ $expenseCount }} Pos Beban</span>
                </span>
                <span class="text-xs font-black bg-black/20 px-3 py-1.5 rounded-xl uppercase tracking-wider backdrop-blur-sm flex items-center gap-1.5">
                    <x-icon name="trending-up" class="w-3.5 h-3.5" />
                    <span>Margin Bersih {{ $netMarginPercent }}%</span>
                </span>
            </div>
        </div>
    </div>

    <!-- ═══════════════ Section AI Advisor & Proyeksi Finansial ═══════════════ -->
    <div class="bg-[#1E1B4B] p-6 rounded-3xl border border-[#2E2A68] shadow-lg space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-2xl bg-purple-500/20 text-purple-400 border border-purple-500/40 flex items-center justify-center">
                    <x-icon name="sparkles" class="w-4 h-4 text-purple-400" />
                </div>
                <div>
                    <h3 class="font-black text-sm text-white">Analisis Finansial Cerdas & Proyeksi Omset</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Evaluasi HPP, estimasi target run-rate, dan rekomendasi performa</p>
                </div>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/40">
                Sistem Pintar
            </span>
        </div>

        <!-- Proyeksi Bulanan Run-Rate -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
            <div class="bg-[#16192E] p-4 rounded-2xl border border-[#2E2A68] space-y-1">
                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Proyeksi Omset Akhir Bulan</p>
                <p class="text-xl font-black text-[#3EDAD7]">
                    Rp {{ number_format($projectedMonthlyOmset, 0, ',', '.') }}
                </p>
                <p class="text-[10px] text-slate-500 font-medium">Estimasi berbasis run-rate penjualan harian</p>
            </div>

            <div class="bg-[#16192E] p-4 rounded-2xl border border-[#2E2A68] space-y-1">
                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Proyeksi Laba Bersih Akhir Bulan</p>
                <p class="text-xl font-black text-emerald-400">
                    Rp {{ number_format($projectedMonthlyNetProfit, 0, ',', '.') }}
                </p>
                <p class="text-[10px] text-slate-500 font-medium">Estimasi keuntungan bersih setelah biaya operasional</p>
            </div>
        </div>

        <!-- AI Insight Pills -->
        <div class="space-y-2.5 pt-2">
            @foreach($aiAdvices as $advice)
                <div class="p-4 rounded-2xl border text-xs flex items-start gap-3 {{ $advice['type'] === 'SUCCESS' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300' : ($advice['type'] === 'WARNING' ? 'bg-amber-500/10 border-amber-500/30 text-amber-300' : 'bg-rose-500/10 border-rose-500/30 text-rose-300') }}">
                    <x-icon name="{{ $advice['type'] === 'SUCCESS' ? 'check' : 'info' }}" class="w-4 h-4 mt-0.5 shrink-0" />
                    <div>
                        <h4 class="font-black text-white text-xs mb-0.5">{{ $advice['title'] }}</h4>
                        <p class="text-[11px] opacity-90 leading-relaxed">{{ $advice['message'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ═══════════════ Grid 3 Kolom: Penjualan, Biaya & Modal ═══════════════ -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- 1. Penjualan -->
        <div class="bg-[#1E1B4B] p-5 rounded-3xl border border-[#2E2A68] shadow-md space-y-3.5">
            <h3 class="font-black text-xs uppercase tracking-widest text-slate-400 flex items-center gap-2">
                <x-icon name="shopping-bag" class="w-4 h-4 text-indigo-400" />
                <span>Penjualan</span>
            </h3>

            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-300">Omset Net:</span>
                    <span class="font-black text-white">Rp {{ number_format($omset, 0, ',', '.') }}</span>
                </div>
                @if($platformAdjustment != 0)
                    <div class="flex justify-between text-[11px]">
                        <span class="text-slate-400">Penyesuaian Kanal:</span>
                        <span class="font-black {{ $platformAdjustment >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ $platformAdjustment >= 0 ? '+' : '-' }}Rp {{ number_format(abs($platformAdjustment), 0, ',', '.') }}
                        </span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-slate-300">Total HPP Modal:</span>
                    <span class="font-black text-rose-400">−Rp {{ number_format($cogsTotal, 0, ',', '.') }}</span>
                </div>
                <div class="pt-2 border-t border-[#2E2A68] flex justify-between font-black text-sm">
                    <span class="text-slate-200">Laba Kotor:</span>
                    <span class="text-emerald-400">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Biaya Operasional -->
        <div class="bg-[#1E1B4B] p-5 rounded-3xl border border-[#2E2A68] shadow-md space-y-3.5">
            <h3 class="font-black text-xs uppercase tracking-widest text-slate-400 flex items-center gap-2">
                <x-icon name="wallet" class="w-4 h-4 text-orange-400" />
                <span>Biaya Operasional (Opex)</span>
            </h3>

            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-300">Total Pengeluaran:</span>
                    <span class="font-black text-rose-400">−Rp {{ number_format($expenses, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-[11px]">
                    <span class="text-slate-400">Rasio Beban Opex:</span>
                    <span class="font-bold text-amber-400">{{ $opexRatio }}% dari Omset</span>
                </div>
                <div class="pt-2 border-t border-[#2E2A68] flex justify-between font-black text-sm">
                    <span class="text-slate-200">Laba Bersih:</span>
                    <span class="{{ $netProfit >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $netProfit < 0 ? '−' : '' }}Rp {{ number_format(abs($netProfit), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 3. Alokasi Modal & Profit Murni -->
        <div class="bg-[#1E1B4B] p-5 rounded-3xl border border-[#2E2A68] shadow-md space-y-3.5">
            <h3 class="font-black text-xs uppercase tracking-widest text-slate-400 flex items-center gap-2">
                <x-icon name="refresh" class="w-4 h-4 text-cyan-400" />
                <span>Alokasi Modal & Uang Bebas</span>
            </h3>

            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-300">Alokasi Modal (HPP):</span>
                    <span class="font-black text-amber-400">Rp {{ number_format($modalReturn, 0, ',', '.') }}</span>
                </div>
                <p class="text-[10px] text-slate-500 font-medium">Uang modal untuk diputar kembali belanja restok bahan baku.</p>
                <div class="pt-2 border-t border-[#2E2A68] flex justify-between font-black text-sm">
                    <span class="text-slate-200">Profit Murni (Uang Bebas):</span>
                    <span class="{{ $trueProfit >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $trueProfit < 0 ? '−' : '' }}Rp {{ number_format(abs($trueProfit), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions: Export -->
    <div class="flex flex-wrap gap-2">
        <button wire:click="exportExcel" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow flex items-center gap-1.5 active:scale-95">
            <x-icon name="receipt" class="w-4 h-4" />
            <span>Export Excel (CSV)</span>
        </button>
        <button wire:click="exportPdf" class="px-4 py-2.5 bg-[#16192E] hover:bg-[#25215A] text-slate-200 font-bold text-xs rounded-2xl border border-[#2E2A68] flex items-center gap-1.5">
            <x-icon name="printer" class="w-4 h-4" />
            <span>Export PDF</span>
        </button>
        <span class="text-[10px] text-slate-500 font-medium self-center">Void diexclude • HPP itemized</span>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-[#1E1B4B] p-5 rounded-3xl border border-[#2E2A68] shadow-lg space-y-3">
            <h3 class="font-black text-xs uppercase tracking-widest text-slate-400">Tren Omset vs HPP</h3>
            <canvas id="trendChart" height="160"></canvas>
            <p class="text-[10px] text-slate-500">Per jam (Hari Ini) atau per hari (Bulan Ini).</p>
        </div>
        <div class="bg-[#1E1B4B] p-5 rounded-3xl border border-[#2E2A68] shadow-lg space-y-3">
            <h3 class="font-black text-xs uppercase tracking-widest text-slate-400">Distribusi Metode Pembayaran</h3>
            <canvas id="paymentChart" height="160"></canvas>
        </div>
    </div>

    <!-- Distribution Grid -->
    <div class="bg-[#1E1B4B] p-5 rounded-3xl border border-[#2E2A68] shadow-lg space-y-3">
        <h3 class="font-black text-xs uppercase tracking-widest text-slate-400 flex items-center gap-2">
            <x-icon name="wallet" class="w-4 h-4 text-slate-400" />
            <span>Rincian per Metode</span>
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @forelse($paymentMethods as $pm)
                <div class="bg-[#16192E] p-3.5 rounded-2xl border border-[#2E2A68] space-y-1 text-center">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">{{ $pm['method'] }}</span>
                    <p class="text-sm font-black text-[#3EDAD7]">Rp {{ number_format($pm['total'], 0, ',', '.') }}</p>
                    <p class="text-[10px] text-slate-400 font-bold">{{ $pm['count'] }} Transaksi</p>
                </div>
            @empty
                <div class="col-span-full py-6 text-center text-slate-400 text-xs">Belum ada transaksi pada periode ini.</div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const labels = @json($trendLabels ?? []);
        const omset = @json($trendOmset ?? []);
        const cogs = @json($trendCogs ?? []);
        const pmLabels = @json(array_column($paymentMethods ?? [], 'method'));
        const pmTotals = @json(array_column($paymentMethods ?? [], 'total'));
        const ctx1 = document.getElementById('trendChart');
        if(ctx1 && window.Chart){
            new Chart(ctx1, { type:'line', data:{ labels, datasets:[{label:'Omset', data: omset, borderColor:'#3EDAD7', backgroundColor:'rgba(62,218,215,0.15)', tension:0.3, fill:true},{label:'HPP', data:cogs, borderColor:'#F472B6', backgroundColor:'rgba(244,114,182,0.1)', tension:0.3, fill:true}]}, options:{responsive:true, plugins:{legend:{labels:{color:'#94A3B8', font:{size:10}}}}, scales:{x:{ticks:{color:'#94A3B8', maxRotation:45}}, y:{ticks:{color:'#94A3B8'}, grid:{color:'rgba(46,42,104,0.5)'}}}}});
        }
        const ctx2 = document.getElementById('paymentChart');
        if(ctx2 && window.Chart){
            new Chart(ctx2, { type:'doughnut', data:{ labels: pmLabels, datasets:[{ data: pmTotals, backgroundColor:['#4338CA','#06B6D4','#F97316','#10B981','#E11D48','#6366F1'], borderWidth:0}]}, options:{responsive:true, plugins:{legend:{position:'bottom', labels:{color:'#94A3B8', font:{size:10}, padding:12}}}}});
        }
    });
    </script>
    @endpush
</div>

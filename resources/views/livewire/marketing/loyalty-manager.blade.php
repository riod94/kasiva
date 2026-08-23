<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('marketing.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#25215A] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <span>Program Loyalitas Stempel Digital</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Database nomor WhatsApp pelanggan, kartu stempel digital, dan reward gratis</p>
            </div>
        </div>
        <button 
            wire:click="openCreateMemberModal"
            class="px-5 py-3 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg flex items-center justify-center gap-2 active:scale-95 transition shrink-0"
        >
            <x-icon name="plus" class="w-4 h-4" />
            <span>Daftarkan Member</span>
        </button>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Program Config Cards -->
    <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-3xl p-5 shadow-md space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="font-black text-xs uppercase tracking-widest text-slate-400">Program Loyalitas Terkonfigurasi</h3>
            <button wire:click="openProgramModal()" class="px-3 py-2 bg-[#16192E] hover:bg-[#4338CA] text-slate-200 hover:text-white rounded-xl text-[11px] font-black border border-[#2E2A68] transition flex items-center gap-1.5">
                <x-icon name="plus" class="w-3.5 h-3.5" />
                <span>Program Baru</span>
            </button>
        </div>
        @forelse($programs as $pr)
            <div class="bg-[#16192E] border border-[#2E2A68] rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-black text-white">{{ $pr->name }} <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-[#4338CA]/30 text-indigo-300 border border-[#4338CA]/40">{{ $pr->reward_type ?? 'FREE_PRODUCT' }} {{ $pr->reward_value ? number_format($pr->reward_value,0,',','.') : '' }}</span></p>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $pr->target_stamps }} stempel → reward • Min. Rp {{ number_format($pr->min_transaction ?? $pr->minTransaction ?? 0,0,',','.') }} • Exp. {{ $pr->expiry_months ?? $pr->expiryMonths ?? 12 }} bulan • Klaim {{ $pr->reward_claim_days ?? 30 }} hari • {{ ($pr->allow_with_promo ?? $pr->allowWithPromo) ? 'Boleh promo' : 'Tanpa promo' }} • {{ ($pr->after_claim ?? $pr->afterClaim ?? 'RESET') === 'RESET' ? 'Reset setelah klaim' : 'Selesai' }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-[10px] font-black px-2.5 py-1 rounded-full {{ $pr->is_active ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-700 text-slate-400 border border-slate-600' }}">{{ $pr->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    <button wire:click="openProgramModal('{{ $pr->id }}')" class="px-2.5 py-1.5 bg-[#1E1B4B] hover:bg-[#4338CA] text-slate-300 hover:text-white rounded-xl text-[11px] font-bold border border-[#2E2A68]">Edit</button>
                </div>
            </div>
        @empty
            <p class="text-xs text-slate-500 text-center py-4">Belum ada program. Buat program e.g. 10 stempel gratis 1 menu.</p>
        @endforelse
    </div>

    <!-- Search Input -->
    <div class="relative w-full sm:w-80">
        <x-icon name="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
        <input 
            type="text" 
            wire:model.live.debounce.250ms="search"
            placeholder="Cari nama atau nomor WhatsApp..."
            class="w-full pl-10 pr-4 py-2.5 bg-[#1E1B4B] border border-[#2E2A68] rounded-2xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-[#4338CA] transition"
        >
    </div>

    <!-- Members Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($members as $member)
            @php
                $progress = null;
                $activeProgram = $programs->firstWhere('is_active', true) ?? $programs->first();
                if ($activeProgram && method_exists($member, 'stamps')) {
                    // compute expiry-aware count if possible
                    $threshold = $activeProgram->expiry_months ? now()->subMonths($activeProgram->expiry_months) : now()->subYear();
                    $validCount = $member->stamps()->where('created_at', '>', $threshold)->count();
                    $current = $validCount > 0 ? $validCount : (int)$member->stamps_count;
                } else {
                    $current = (int)$member->stamps_count;
                }
                $target = $activeProgram->target_stamps ?? 10;
            @endphp
            <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-3xl p-5 shadow-md hover:border-[#4338CA] transition space-y-4 flex flex-col justify-between group">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h4 class="font-black text-sm text-white">{{ $member->name }}</h4>
                            <p class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $member->phone }}</p>
                        </div>
                        <span class="text-[10px] font-black px-2.5 py-1 rounded-full bg-[#4338CA]/20 text-indigo-300 border border-[#4338CA]/40 shrink-0">
                            {{ $member->total_visits }}x Kunjungan
                        </span>
                    </div>

                    <!-- QR Code -->
                    <div class="bg-white p-2.5 rounded-2xl border border-slate-200 text-center">
                        <p class="text-[9px] font-black tracking-widest text-slate-500 uppercase">QR Member</p>
                        <p class="font-mono font-black text-[11px] text-[#1E1B4B] tracking-tight mt-0.5">{{ $member->qr_code }}</p>
                        <div class="mt-1.5 mx-auto w-fit p-1 bg-white rounded-xl border border-slate-200">
                            <div class="w-20 h-20 bg-[repeating-linear-gradient(45deg,#E2E8F0_0_4px,#fff_4px_8px)] rounded-lg flex items-center justify-center text-[8px] font-black text-slate-600">QR<br>{{ substr($member->qr_code, -4) }}</div>
                        </div>
                        <button onclick="navigator.clipboard.writeText('{{ $member->qr_code }}'); alert('QR disalin: {{ $member->qr_code }}')" class="mt-1.5 text-[10px] font-bold px-2 py-1 rounded-full bg-[#1E1B4B] text-white">Salin QR</button>
                    </div>

                    <!-- Stamps Progress Bar -->
                    <div class="bg-[#16192E] p-3.5 rounded-2xl border border-[#2E2A68] space-y-2.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-300">Stempel Loyalitas:</span>
                            <span class="font-black text-[#3EDAD7]">{{ $current }}/{{ $target }} Stempel</span>
                        </div>
                        <div class="grid gap-1.5" style="grid-template-columns: repeat({{ $target }}, minmax(0, 1fr))">
                            @for($i = 1; $i <= $target; $i++)
                                <div class="h-3 rounded-md {{ $i <= $current ? 'bg-[#3EDAD7] shadow-sm shadow-[#3EDAD7]/50' : 'bg-[#1E1B4B] border border-[#2E2A68]' }}"></div>
                            @endfor
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium">
                            {{ $current >= $target ? 'Berhak klaim reward!' : 'Kumpulkan ' . ($target - $current) . ' stempel lagi untuk reward.' }}
                        </p>
                    </div>
                </div>

                <div class="pt-2 border-t border-[#2E2A68]">
                    <button 
                        wire:click="openStampModal('{{ $member->id }}')" 
                        class="w-full py-2.5 bg-[#4338CA]/20 hover:bg-[#4338CA] text-indigo-200 hover:text-white font-extrabold text-xs uppercase tracking-wider rounded-xl border border-[#4338CA]/40 transition flex items-center justify-center gap-1.5 active:scale-95"
                    >
                        <x-icon name="gift" class="w-3.5 h-3.5" />
                        <span>Kelola Stempel</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-[#1E1B4B] rounded-3xl border border-[#2E2A68]">
                <x-icon name="gift" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                <p class="text-sm font-semibold text-slate-300">Belum ada member loyalitas terdaftar</p>
                <p class="text-xs text-slate-500 mt-0.5">Daftarkan pelanggan ke program kartu stempel untuk membangun retensi</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $members->links() }}
    </div>

    <!-- Create Member Modal -->
    @if($showMemberModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-sm rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <h3 class="text-base font-black text-white">Daftarkan Member Baru</h3>
                    <button wire:click="$set('showMemberModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>
                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nama Lengkap Pelanggan</label>
                        <input type="text" wire:model="memberName" placeholder="cth: Rian Pratama" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA]">
                        @error('memberName') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nomor WhatsApp</label>
                        <input type="text" wire:model="memberPhone" placeholder="cth: 081234567890" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA]">
                        @error('memberPhone') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <button 
                    wire:click="saveMember"
                    class="w-full py-3.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95"
                >
                    Simpan & Beri Stempel Awal
                </button>
            </div>
        </div>
    @endif

    <!-- Stamp / Redeem Modal -->
    @if($showStampModal && $selectedMember)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-sm rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <div>
                        <h3 class="text-base font-black text-white">Kelola Stempel Member</h3>
                        <p class="text-[10px] text-slate-400 font-bold mt-0.5">{{ $selectedMember->name }} ({{ $selectedMember->phone }})</p>
                    </div>
                    <button wire:click="$set('showStampModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>
                <div class="bg-[#16192E] p-4 rounded-2xl border border-[#2E2A68] text-center space-y-1">
                    <p class="text-xs text-slate-300 font-bold">Total Stempel Aktif:</p>
                    <p class="text-3xl font-black text-[#3EDAD7]">{{ $selectedMember->stamps_count }} <span class="text-sm text-slate-400 font-bold">/ {{ ($programs->firstWhere('is_active',true) ?? $programs->first())->target_stamps ?? 10 }}</span></p>
                </div>
                <div class="space-y-2.5">
                    <button 
                        wire:click="addStamp"
                        class="w-full py-3.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition flex items-center justify-center gap-1.5 active:scale-95"
                    >
                        <x-icon name="plus" class="w-4 h-4" />
                        <span>Tambah 1 Stempel Kunjungan</span>
                    </button>
                    @php $canRedeem = $selectedMember->stamps_count >= (($programs->firstWhere('is_active',true) ?? $programs->first())->target_stamps ?? 10); @endphp
                    @if($canRedeem)
                        <button 
                            wire:click="redeemReward"
                            class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition flex items-center justify-center gap-1.5 active:scale-95"
                        >
                            <x-icon name="gift" class="w-4 h-4" />
                            <span>Klaim Reward (Tukar Stempel)</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Program Modal -->
    @if($showProgramModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-lg rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl my-4">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <h3 class="text-base font-black text-white">{{ $programId ? 'Edit Program' : 'Program Baru' }}</h3>
                    <button wire:click="$set('showProgramModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>
                <div class="space-y-3 text-xs max-h-[60vh] overflow-y-auto pr-1">
                    <div><label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nama Program</label><input type="text" wire:model="programName" placeholder="10 Stempel Gratis 1 Kopi" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA]"></div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Target Stempel</label><input type="number" wire:model="targetStamps" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white"></div>
                        <div><label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Min. Transaksi (Rp)</label><input type="number" wire:model="minTransaction" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Masa Berlaku (bulan)</label><input type="number" wire:model="expiryMonths" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white"></div>
                        <div><label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Reward Claim (hari)</label><input type="number" wire:model="rewardClaimDays" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Tipe Reward</label><select wire:model="rewardType" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white"><option value="FREE_PRODUCT">Gratis Produk</option><option value="PERCENT_DISCOUNT">Diskon %</option><option value="FIXED_DISCOUNT">Diskon Rp</option></select></div>
                        <div><label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nilai Reward</label><input type="number" wire:model="rewardValue" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Setelah Klaim</label><select wire:model="afterClaim" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white"><option value="RESET">Reset stempel</option><option value="COMPLETE">Selesai</option></select></div>
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-300 self-end pb-2"><input type="checkbox" wire:model.live="allowWithPromo" class="rounded"> Boleh dengan promo</label>
                    </div>
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-300"><input type="checkbox" wire:model.live="programIsActive" class="rounded"> Aktif</label>
                </div>
                <button wire:click="saveProgram" class="w-full py-3.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95">{{ $programId ? 'Simpan Perubahan' : 'Buat Program' }}</button>
            </div>
        </div>
    @endif
</div>

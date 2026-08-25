<div class="space-y-6" x-data>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('marketing.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#2A3155] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white">Database Member</h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Loyalty System — QR KSV-MBR- tersinkron</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="$set('showBatchModal', true)" class="px-4 py-3 bg-white text-[#1E1B4B] font-black text-xs uppercase tracking-wider rounded-2xl shadow flex items-center gap-2 active:scale-95 transition">
                <x-icon name="qr-code" class="w-4 h-4" /> Generate QR
            </button>
            <button wire:click="openCreateModal" class="px-5 py-3 bg-[#00AAA6] hover:bg-[#008F8C] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg flex items-center gap-2 active:scale-95 transition">
                <x-icon name="plus" class="w-4 h-4" /> Tambah
            </button>
        </div>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Tabs + Search + Bulk actions -->
    <div class="flex flex-col gap-3">
        <div class="flex bg-[#1E1B4B] p-1.5 rounded-2xl border border-[#2E2A68] shadow-sm w-full sm:w-fit">
            @foreach(['ALL'=>'Semua','ASSIGNED'=>'Terdaftar','UNASSIGNED'=>'Kosong'] as $key=>$label)
                <button wire:click="setTab('{{ $key }}')" class="flex-1 sm:flex-none px-5 h-9 rounded-xl font-black text-xs uppercase tracking-wider transition {{ $activeTab===$key ? 'bg-[#00AAA6] text-white shadow' : 'text-slate-400 hover:text-white' }}">{{ $label }}</button>
            @endforeach
        </div>
        <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
            <div class="relative flex-1 sm:max-w-sm">
                <x-icon name="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari ID, Nama, HP atau QR..." class="w-full pl-10 pr-4 py-2.5 bg-[#1E1B4B] border border-[#2E2A68] rounded-2xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-[#00AAA6] transition">
            </div>
            @if($members->count() > 0)
                <button wire:click="toggleSelectAll" class="text-[10px] font-black uppercase tracking-widest text-[#8696ED] hover:text-[#8696ED] transition text-left sm:text-center">
                    {{ count(array_intersect($members->pluck('id')->toArray(), $selectedIds)) === $members->count() ? 'Batalkan Semua Halaman Ini' : 'Pilih Semua Halaman Ini' }}
                </button>
            @endif
        </div>
    </div>

    <!-- Members List — compact row like Ngepos -->
    <div class="flex flex-col gap-2.5">
        @forelse($members as $member)
            @php $isSel = in_array($member->id, $selectedIds, true); @endphp
            <div data-testid="member-card" wire:click="openProfile('{{ $member->id }}')" class="flex items-center gap-3 bg-[#1E1B4B] px-3.5 py-3 rounded-2xl border transition cursor-pointer shadow-sm {{ $isSel ? 'border-[#00AAA6] bg-[#00AAA6]/10 ring-1 ring-[#00AAA6]/20' : 'border-[#2E2A68] hover:border-[#00AAA6]/40' }}">
                <button wire:click.stop="toggleSelect('{{ $member->id }}')" class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 border bg-white overflow-hidden relative {{ $isSel ? 'border-[#00AAA6] ring-2 ring-[#00AAA6]/20' : 'border-slate-200' }}">
                    @if($isSel)
                        <span class="absolute inset-0 bg-[#00AAA6] flex items-center justify-center text-white"><x-icon name="check" class="w-5 h-5" /></span>
                    @else
                        <canvas class="member-qr-mini" data-qr="{{ $member->qr_code }}" width="48" height="48"></canvas>
                    @endif
                </button>
                <div class="flex-1 min-w-0">
                    <h3 class="font-black text-sm text-white truncate">{{ $member->name ?: 'UNREGISTERED' }}</h3>
                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                        <span class="text-[10px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded {{ ($member->status ?? 'ASSIGNED')==='ASSIGNED' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-700 text-slate-400' }}">{{ ($member->status ?? 'ASSIGNED')==='ASSIGNED' ? 'Member Aktif' : 'Belum Terdaftar' }}</span>
                        <span class="text-[10px] font-mono text-slate-500">#{{ strtoupper(substr($member->id, -6)) }}</span>
                    </div>
                </div>
                <span class="w-8 h-8 rounded-full bg-[#16192E] border border-[#2E2A68] flex items-center justify-center text-slate-500 shrink-0">›</span>
            </div>
        @empty
            <div class="py-16 text-center text-slate-400 bg-[#1E1B4B] rounded-3xl border border-[#2E2A68]">
                <x-icon name="users" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                <p class="text-sm font-semibold text-slate-300">Tidak ada member ditemukan</p>
                <p class="text-xs text-slate-500 mt-0.5">Generate QR kosong atau daftarkan member aktif</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $members->links() }}</div>

    <!-- Floating bulk bar -->
    @if(count($selectedIds) > 0)
        <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[60] w-[calc(100%-2rem)] max-w-md">
            <div class="bg-[#0F172A] text-white p-3.5 rounded-[22px] shadow-2xl flex items-center justify-between gap-4 border border-white/10">
                <div class="flex items-center gap-3 pl-2">
                    <span class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center font-black text-sm">{{ count($selectedIds) }}</span>
                    <span class="text-[10px] font-black uppercase tracking-widest opacity-60">Terpilih</span>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="openBulkPrint" class="h-10 px-4 rounded-xl bg-white text-[#0F172A] font-black text-[10px] uppercase tracking-widest flex items-center gap-2 active:scale-95 transition"><x-icon name="printer" class="w-4 h-4" /> Cetak</button>
                    <button wire:click="bulkDelete" wire:confirm="Hapus {{ count($selectedIds) }} member terpilih?" class="h-10 w-10 flex items-center justify-center rounded-xl bg-rose-500/20 text-rose-400 hover:bg-rose-600 hover:text-white transition"><x-icon name="trash" class="w-4 h-4" /></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-sm rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <h3 class="text-base font-black text-white">{{ $memberId ? 'Ubah Data Member' : 'Daftarkan Member Baru' }}</h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>
                <div class="space-y-4 text-xs">
                    <div><label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nama Lengkap</label><input type="text" wire:model="name" placeholder="cth: Rian Pratama" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]"> @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror</div>
                    <div><label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">No WhatsApp</label><input type="text" wire:model="phone" placeholder="081234567890" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]"> @error('phone') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror</div>
                    <div><label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Email (opsional)</label><input type="email" wire:model="email" placeholder="email@member.com" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]"></div>
                </div>
                <button wire:click="saveMember" class="w-full py-3.5 bg-[#00AAA6] hover:bg-[#008F8C] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95">{{ $memberId ? 'Simpan Perubahan' : 'Daftarkan Member' }}</button>
            </div>
        </div>
    @endif

    <!-- Batch Generate Dialog -->
    @if($showBatchModal)
        <div class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/40 backdrop-blur-[2px]" role="dialog" aria-modal="true" aria-labelledby="batch-qr-title">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-[320px] rounded-[24px] shadow-2xl p-6 space-y-6">
                <div class="text-center"><h2 id="batch-qr-title" class="text-lg font-black text-white uppercase tracking-tight">Generate QR</h2><p class="text-[10px] font-bold text-slate-400 mt-1">Pilih jumlah kartu member kosong</p></div>
                <div class="grid grid-cols-3 gap-2">
                    @foreach([5,10,15,25,50] as $num)
                        <button wire:click="$set('batchCount', {{ $num }})" class="h-10 rounded-xl border-2 font-black text-[10px] transition {{ $batchCount===$num ? 'border-[#00AAA6] bg-[#00AAA6]/20 text-white' : 'border-[#2E2A68] text-slate-400 hover:bg-[#16192E]' }}">{{ $num }} PCS</button>
                    @endforeach
                    <div class="relative h-10"><input type="number" min="1" max="50" wire:model.live="batchCount" placeholder="CSTM" class="w-full h-full rounded-xl border-2 px-2 text-center font-black text-[10px] outline-none bg-[#16192E] border-[#2E2A68] text-white"></div>
                </div>
                <div class="pt-2 space-y-2">
                    <button wire:click="generateBatch" class="w-full h-12 bg-[#00AAA6] text-white rounded-xl font-black text-[11px] uppercase tracking-widest shadow-lg active:scale-[0.98] transition">Generate & Print</button>
                    <button wire:click="$set('showBatchModal', false)" class="w-full h-10 text-[10px] font-black text-slate-400 uppercase tracking-widest">Batal</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Profile Sheet -->
    @if($showProfile && $profileMember)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end justify-center p-0">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-lg rounded-t-[32px] max-h-[96vh] flex flex-col overflow-hidden shadow-2xl">
                <div class="px-5 pt-6 pb-4 border-b border-[#2E2A68] flex items-center justify-between shrink-0">
                    <h3 class="font-black text-lg text-white">{{ $isEditing ? 'Edit Profil Member' : 'Profil Member' }}</h3>
                    <button wire:click="$set('showProfile', false)" class="w-8 h-8 rounded-full bg-[#16192E] border border-[#2E2A68] flex items-center justify-center text-slate-400 hover:text-white">✕</button>
                </div>
                <div class="flex-1 overflow-y-auto p-5 space-y-6">
                    <div class="p-6 bg-white rounded-[24px] border border-slate-200 text-center relative overflow-hidden">
                        <canvas id="profile-qr" data-qr="{{ $profileMember->qr_code }}" width="140" height="140" class="mx-auto rounded-xl"></canvas>
                        <h3 class="text-lg font-black tracking-tight uppercase text-slate-900 mt-3">{{ $profileMember->name ?: 'Member Baru' }}</h3>
                        <div class="flex items-center justify-center gap-2 mt-1">
                            <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-full {{ ($profileMember->status ?? 'ASSIGNED')==='ASSIGNED' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">{{ ($profileMember->status ?? 'ASSIGNED')==='ASSIGNED' ? 'Member Aktif' : 'Belum Terdaftar' }}</span>
                            <span class="text-[10px] font-mono text-slate-400">#{{ strtoupper(substr($profileMember->id, -6)) }}</span>
                        </div>
                        <p class="font-mono text-[10px] text-slate-500 mt-2 break-all">{{ $profileMember->qr_code }}</p>
                        <button onclick="navigator.clipboard.writeText('{{ $profileMember->qr_code }}')" class="mt-2 text-[10px] font-bold px-3 py-1 rounded-full bg-[#1E1B4B] text-white">Salin QR</button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-[#16192E] p-4 rounded-2xl border border-[#2E2A68]"><p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Mulai Member</p><p class="text-xs font-bold text-white mt-1 font-mono">{{ $profileMember->assigned_at ? $profileMember->assigned_at->format('d/m/Y') : '-' }}</p></div>
                        <div class="bg-[#16192E] p-4 rounded-2xl border border-[#2E2A68]"><p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Total Stamp</p><p class="text-xs font-black text-[#3EDAD7] mt-1">{{ $profileStampsCount }} / {{ $activeProgram?->target_stamps ?? 10 }}</p></div>
                    </div>

                    @if($isEditing)
                        <div class="space-y-4">
                            <div><label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Member</label><input type="text" wire:model="name" class="w-full h-11 px-4 mt-1 rounded-xl border border-[#2E2A68] bg-[#16192E] text-sm text-white"></div>
                            <div><label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">WhatsApp</label><input type="text" wire:model="phone" class="w-full h-11 px-4 mt-1 rounded-xl border border-[#2E2A68] bg-[#16192E] text-sm text-white"></div>
                            <div><label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email</label><input type="email" wire:model="email" class="w-full h-11 px-4 mt-1 rounded-xl border border-[#2E2A68] bg-[#16192E] text-sm text-white"></div>
                            <div class="flex gap-3 pt-2">
                                <button wire:click="cancelEditProfile" class="flex-1 h-11 bg-[#16192E] border border-[#2E2A68] text-slate-300 rounded-xl font-black text-xs uppercase">Batal</button>
                                <button wire:click="saveMember" class="flex-1 h-11 bg-[#00AAA6] text-white rounded-xl font-black text-xs uppercase">Simpan</button>
                            </div>
                        </div>
                    @else
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-4 rounded-2xl bg-[#16192E] border border-[#2E2A68]"><span class="w-10 h-10 rounded-xl bg-[#00AAA6]/20 flex items-center justify-center text-[#8696ED] text-xs font-black">Aa</span><div class="flex-1 min-w-0"><p class="text-[10px] font-black text-slate-500 uppercase">Nama Lengkap</p><p class="text-sm font-bold text-white truncate">{{ $profileMember->name ?: '-' }}</p></div></div>
                            <div class="flex items-center gap-3 p-4 rounded-2xl bg-[#16192E] border border-[#2E2A68]"><span class="w-10 h-10 rounded-xl bg-[#00AAA6]/20 flex items-center justify-center text-[#8696ED]"><x-icon name="phone" class="w-4 h-4" /></span><div class="flex-1 min-w-0"><p class="text-[10px] font-black text-slate-500 uppercase">WhatsApp</p><p class="text-sm font-bold text-white truncate">{{ $profileMember->phone ?: '-' }}</p></div></div>
                            <div class="flex items-center gap-3 p-4 rounded-2xl bg-[#16192E] border border-[#2E2A68]"><span class="w-10 h-10 rounded-xl bg-[#00AAA6]/20 flex items-center justify-center text-[#8696ED]"><x-icon name="mail" class="w-4 h-4" /></span><div class="flex-1 min-w-0"><p class="text-[10px] font-black text-slate-500 uppercase">Email</p><p class="text-sm font-bold text-white truncate">{{ $profileMember->email ?: '-' }}</p></div></div>
                        </div>
                        <!-- Stamp Progress Grid 5 columns -->
                        <div class="pt-2">
                            <div class="flex items-center justify-between px-1 mb-3">
                                <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Progress Loyalty</h4>
                                <span class="text-[10px] font-black text-[#3EDAD7] px-2.5 py-1 bg-[#00AAA6]/20 rounded-lg border border-[#00AAA6]/30">{{ $profileStampsCount }} / {{ $activeProgram?->target_stamps ?? 10 }} STAMP</span>
                            </div>
                            <div class="bg-[#16192E] rounded-[20px] border border-[#2E2A68] p-4">
                                <div class="grid grid-cols-5 gap-2">
                                    @for($i=0;$i<($activeProgram?->target_stamps ?? 10);$i++)
                                        <div class="aspect-square rounded-xl flex items-center justify-center border-2 text-[10px] font-black {{ $i < $profileStampsCount ? 'bg-[#00AAA6] border-[#00AAA6] text-white shadow' : 'bg-[#1E1B4B] border-[#2E2A68] text-slate-600' }}">{{ $i < $profileStampsCount ? '✓' : $i+1 }}</div>
                                    @endfor
                                </div>
                                @if($profileProgress)
                                    <p class="text-[10px] font-bold text-center mt-3 {{ $profileProgress['isEligibleForReward'] ? 'text-emerald-400' : 'text-slate-400' }}">{{ $profileProgress['isEligibleForReward'] ? '🎉 Reward Siap Diklaim!' : 'Kurang ' . (($profileProgress['targetStamps'] - $profileProgress['currentStamps'])) . ' stamp lagi' }}@if($profileProgress['expiresAt']) • Hangus {{ $profileProgress['expiresAt']->format('d/m/Y') }} @endif</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2.5 pt-2">
                            <button wire:click="startEditProfile" class="flex-1 h-12 bg-[#00AAA6] text-white rounded-2xl font-black text-xs uppercase tracking-widest">Edit Profil</button>
                            <button wire:click="openBulkPrint" class="w-12 h-12 bg-[#16192E] border border-[#2E2A68] text-slate-300 rounded-2xl flex items-center justify-center"><x-icon name="printer" class="w-5 h-5" /></button>
                        </div>
                        <button wire:click="deleteMember('{{ $profileMember->id }}')" wire:confirm="Hapus member ini permanen?" class="w-full py-3 text-[10px] font-black text-rose-400 uppercase tracking-widest">Hapus Selamanya</button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Print Preview Overlay -->
    @if($showPrintPreview)
        <div id="member-print-portal" class="fixed inset-0 z-[9999] bg-white overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="print-preview-title">
            <div class="no-print fixed top-0 left-0 right-0 h-16 bg-white/90 backdrop-blur border-b border-slate-200 flex items-center justify-between px-4 z-[10000]">
                <div class="flex items-center gap-3"><span class="w-9 h-9 rounded-xl bg-[#00AAA6]/10 flex items-center justify-center text-[#00AAA6]"><x-icon name="printer" class="w-5 h-5" /></span><div><p id="print-preview-title" class="font-black text-sm text-slate-900">Pratinjau Cetak</p><p class="text-[10px] font-bold text-slate-500">{{ $printMembers->count() }} Kartu Member</p></div></div>
                <div class="flex items-center gap-2">
                    <button onclick="window.print()" class="px-5 h-10 bg-[#00AAA6] text-white rounded-full font-black text-xs uppercase tracking-widest">Cetak</button>
                    <button wire:click="closePrintPreview" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center" aria-label="Tutup pratinjau cetak">✕</button>
                </div>
            </div>
            <div class="pt-20 pb-10 px-4 flex justify-center bg-slate-50 min-h-screen print:pt-0 print:bg-white">
                <div class="max-w-[800px] w-full">
                    <div class="grid gap-4 print:gap-3" style="grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));">
                        @foreach($printMembers as $pm)
                            <div class="bg-[#1E1B4B] text-white rounded-2xl p-5 flex flex-col items-center gap-3 border border-[#2E2A68] break-inside-avoid">
                                <p class="font-black text-xs uppercase tracking-widest text-[#8696ED]">Kasiva • Member Card</p>
                                <canvas class="print-qr bg-white p-2 rounded-xl" data-qr="{{ $pm->qr_code }}" width="120" height="120"></canvas>
                                <p class="font-mono font-black text-xs tracking-widest">{{ $pm->qr_code }}</p>
                                <p class="text-[10px] font-bold text-slate-400">{{ $pm->name ?: 'UNREGISTERED' }} • #{{ strtoupper(substr($pm->id, -6)) }}</p>
                                @if($activeProgram)
                                    <div class="grid grid-cols-5 gap-1.5 mt-1 w-full">
                                        @for($i=0;$i<$activeProgram->target_stamps;$i++)<div class="w-6 h-6 rounded-full border border-white/20 flex items-center justify-center text-[7px] font-black opacity-60">{{ $i+1 }}</div>@endfor
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <style>@media print{ .no-print{display:none!important} body{visibility:hidden!important; background:white!important} #member-print-portal, #member-print-portal *{visibility:visible!important} #member-print-portal{position:absolute!important; left:0; top:0; width:100%!important} @page{size:A4; margin:10mm} }</style>
        </div>
    @endif

    @push('scripts')
    <script>
        function renderQrs(){
            const QRCode = window.KasivaQRCode; if (!QRCode) return;
            document.querySelectorAll('canvas.member-qr-mini, canvas.print-qr, #profile-qr').forEach(c=>{
                const v=c.getAttribute('data-qr'); if(!v) return;
                QRCode.toCanvas(c, v, {width: parseInt(c.getAttribute('width')||'100'), margin:1, color:{dark:'#0F172A', light:'#ffffff'}}).catch(()=>{});
            });
        }
        renderQrs();
        document.addEventListener('kasiva:qrcode-ready', renderQrs);
        document.addEventListener('livewire:navigated', renderQrs);
        // re-render after Livewire updates
        if(window.Livewire) Livewire.hook('morph.updated', ()=> setTimeout(renderQrs, 50));
        else document.addEventListener('livewire:update', renderQrs);
    </script>
    @endpush
</div>

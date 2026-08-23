<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('marketing.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#2A3155] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <span>Kampanye & Promosi Khusus</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Kelola event promosi musiman, penawaran broadcast, dan diskon berkala</p>
            </div>
        </div>
        <button 
            wire:click="openCreateModal"
            class="px-5 py-3 bg-[#00AAA6] hover:bg-[#008F8C] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg flex items-center justify-center gap-2 active:scale-95 transition shrink-0"
        >
            <x-icon name="plus" class="w-4 h-4" />
            <span>Buat Kampanye</span>
        </button>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Campaign Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($campaigns as $campaign)
            <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-3xl p-5 shadow-md hover:border-[#00AAA6] transition space-y-4 flex flex-col justify-between group">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h4 class="font-black text-sm text-white">{{ $campaign->name }}</h4>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ $campaign->description ?? 'Event promosi' }}</p>
                        </div>
                        <button 
                            wire:click="toggleActive('{{ $campaign->id }}')" 
                            class="text-[10px] font-black px-2.5 py-1 rounded-full shrink-0 transition {{ $campaign->is_active ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/30' : 'bg-slate-500/10 text-slate-400 border border-slate-500/30' }}"
                        >
                            {{ $campaign->is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </div>

                    <div class="bg-[#16192E] p-3 rounded-2xl border border-[#2E2A68] space-y-1 text-xs">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Benefit Promosi:</p>
                        <p class="font-black text-[#3EDAD7] text-sm">
                            {{ $campaign->reward_type === 'PERCENT_DISCOUNT' ? $campaign->reward_value . '% Potongan Diskon' : 'Potongan Rp ' . number_format($campaign->reward_value, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-3 border-t border-[#2E2A68]">
                    <button wire:click="openEditModal('{{ $campaign->id }}')" class="flex-1 py-2 bg-[#16192E] hover:bg-[#00AAA6] text-slate-200 hover:text-white rounded-xl text-xs font-bold border border-[#2E2A68] transition flex items-center justify-center gap-1.5">
                        <x-icon name="edit" class="w-3.5 h-3.5" />
                        <span>Edit</span>
                    </button>
                    <button wire:click="deleteCampaign('{{ $campaign->id }}')" wire:confirm="Yakin ingin menghapus kampanye promo ini?" class="p-2 bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white rounded-xl text-xs font-bold border border-rose-500/30 transition" title="Hapus Kampanye">
                        <x-icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-[#1E1B4B] rounded-3xl border border-[#2E2A68]">
                <x-icon name="megaphone" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                <p class="text-sm font-semibold text-slate-300">Belum ada kampanye promosi</p>
                <p class="text-xs text-slate-500 mt-0.5">Buat event promo musiman untuk meningkatkan antusiasme pelanggan</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $campaigns->links() }}
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-sm rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <h3 class="text-base font-black text-white">
                        {{ $campaignId ? 'Ubah Kampanye Promo' : 'Buat Kampanye Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nama Kampanye</label>
                        <input type="text" wire:model="name" placeholder="cth: Festival Kopi Merdeka" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                        @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Deskripsi Singkat</label>
                        <input type="text" wire:model="description" placeholder="cth: Diskon khusus varian es kopi susu" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Tipe Reward</label>
                            <select wire:model="reward_type" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white focus:outline-none focus:border-[#00AAA6]">
                                <option value="PERCENT_DISCOUNT">Diskon Persen (%)</option>
                                <option value="FIXED_DISCOUNT">Diskon Rupiah (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nilai Benefit</label>
                            <input type="number" wire:model="reward_value" placeholder="10" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6] font-bold text-[#3EDAD7]">
                        </div>
                    </div>
                </div>

                <button 
                    wire:click="saveCampaign"
                    class="w-full py-3.5 bg-[#00AAA6] hover:bg-[#008F8C] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95"
                >
                    {{ $campaignId ? 'Simpan Perubahan' : 'Terbitkan Kampanye' }}
                </button>
            </div>
        </div>
    @endif
</div>

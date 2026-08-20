<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('marketing.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#25215A] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <span>Database Member & Pelanggan</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Database pelanggan loyal, stempel digital, dan frekuensi kunjungan outlet</p>
            </div>
        </div>
        <button 
            wire:click="openCreateModal"
            class="px-5 py-3 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg flex items-center justify-center gap-2 active:scale-95 transition shrink-0"
        >
            <x-icon name="plus" class="w-4 h-4" />
            <span>Tambah Member</span>
        </button>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="relative w-full sm:w-80">
        <x-icon name="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
        <input 
            type="text" 
            wire:model.live.debounce.250ms="search"
            placeholder="Cari nama atau no WhatsApp member..."
            class="w-full pl-10 pr-4 py-2.5 bg-[#1E1B4B] border border-[#2E2A68] rounded-2xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-[#4338CA] transition"
        >
    </div>

    <!-- Members Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($members as $member)
            <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-3xl p-5 shadow-md hover:border-[#4338CA] transition space-y-4 flex flex-col justify-between group">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-[#4338CA] text-white flex items-center justify-center text-sm font-black shrink-0 shadow-sm">
                            <x-icon name="users" class="w-5 h-5 text-white" />
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-black text-sm text-white truncate">{{ $member->name }}</h4>
                            <p class="text-[11px] text-slate-400 font-mono mt-0.5 truncate">{{ $member->phone }}</p>
                        </div>
                    </div>

                    <div class="bg-[#16192E] p-3 rounded-2xl border border-[#2E2A68] flex items-center justify-between text-xs">
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Stempel Aktif:</p>
                            <p class="font-black text-[#3EDAD7] text-xs mt-0.5">{{ $member->stamps_count }}/10 Stamp</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Kunjungan:</p>
                            <p class="font-black text-white text-xs mt-0.5">{{ $member->total_visits }} Kali</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-3 border-t border-[#2E2A68]">
                    <button wire:click="openEditModal('{{ $member->id }}')" class="flex-1 py-2 bg-[#16192E] hover:bg-[#4338CA] text-slate-200 hover:text-white rounded-xl text-xs font-bold border border-[#2E2A68] transition flex items-center justify-center gap-1.5">
                        <x-icon name="edit" class="w-3.5 h-3.5" />
                        <span>Edit</span>
                    </button>
                    <button wire:click="deleteMember('{{ $member->id }}')" wire:confirm="Yakin ingin menghapus member {{ $member->name }}?" class="p-2 bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white rounded-xl text-xs font-bold border border-rose-500/30 transition" title="Hapus Member">
                        <x-icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-[#1E1B4B] rounded-3xl border border-[#2E2A68]">
                <x-icon name="users" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                <p class="text-sm font-semibold text-slate-300">Belum ada pelanggan member terdaftar</p>
                <p class="text-xs text-slate-500 mt-0.5">Daftarkan member baru untuk mengaktifkan stempel reward dan loyalitas</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $members->links() }}
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-sm rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <h3 class="text-base font-black text-white">
                        {{ $memberId ? 'Ubah Data Member' : 'Daftarkan Member Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nama Lengkap Pelanggan</label>
                        <input type="text" wire:model="name" placeholder="cth: Rian Pratama" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA]">
                        @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nomor WhatsApp Pelanggan</label>
                        <input type="text" wire:model="phone" placeholder="cth: 081234567890" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#4338CA]">
                        @error('phone') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button 
                    wire:click="saveMember"
                    class="w-full py-3.5 bg-[#4338CA] hover:bg-[#3730A3] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95"
                >
                    {{ $memberId ? 'Simpan Perubahan' : 'Daftarkan Member' }}
                </button>
            </div>
        </div>
    @endif
</div>

<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('settings.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#2A3155] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <span>Manajemen Staf & PIN Kasir</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Kelola akun operator kasir, peran akses, dan keamanan 6-digit PIN login</p>
            </div>
        </div>
        <button 
            wire:click="openCreateModal"
            class="px-5 py-3 bg-[#00AAA6] hover:bg-[#008F8C] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg flex items-center justify-center gap-2 active:scale-95 transition shrink-0"
        >
            <x-icon name="plus" class="w-4 h-4" />
            <span>Tambah Staf</span>
        </button>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Search Input -->
    <div class="relative w-full sm:w-80">
        <x-icon name="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
        <input 
            type="text" 
            wire:model.live.debounce.250ms="search"
            placeholder="Cari nama staf atau email..."
            class="w-full pl-10 pr-4 py-2.5 bg-[#1E1B4B] border border-[#2E2A68] rounded-2xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-[#00AAA6] transition"
        >
    </div>

    <!-- Staff Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($staffMembers as $staff)
            <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-3xl p-5 shadow-md hover:border-[#00AAA6] transition space-y-4 flex flex-col justify-between group">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="h-11 w-11 rounded-2xl bg-[#00AAA6] flex items-center justify-center text-sm font-black text-white shrink-0 shadow-sm">
                                {{ strtoupper(substr($staff->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-black text-sm text-white truncate">{{ $staff->name }}</h4>
                                <p class="text-[11px] text-slate-400 font-medium truncate">{{ $staff->email }}</p>
                            </div>
                        </div>

                        <button 
                            wire:click="toggleStatus({{ $staff->id }})"
                            class="text-[10px] font-black px-2.5 py-1 rounded-full shrink-0 transition {{ $staff->is_active ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/30' : 'bg-slate-500/10 text-slate-400 border border-slate-500/30' }}"
                        >
                            {{ $staff->is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </div>

                    <div class="bg-[#16192E] p-3 rounded-2xl border border-[#2E2A68] flex items-center justify-between text-xs">
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Peran Akses:</p>
                            <p class="font-black text-[#3EDAD7] text-xs mt-0.5">{{ $staff->role?->name ?? 'Kasir Standar' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Keamanan:</p>
                            <span class="text-[10px] font-bold text-slate-300">
                                PIN 6-Digit
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-3 border-t border-[#2E2A68]">
                    <button wire:click="openEditModal({{ $staff->id }})" class="flex-1 py-2 bg-[#16192E] hover:bg-[#00AAA6] text-slate-200 hover:text-white rounded-xl text-xs font-bold border border-[#2E2A68] transition flex items-center justify-center gap-1.5">
                        <x-icon name="edit" class="w-3.5 h-3.5" />
                        <span>Edit</span>
                    </button>
                    <button wire:click="deleteStaff({{ $staff->id }})" wire:confirm="Yakin ingin menghapus staf {{ $staff->name }}?" class="p-2 bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white rounded-xl text-xs font-bold border border-rose-500/30 transition" title="Hapus Staf">
                        <x-icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-[#1E1B4B] rounded-3xl border border-[#2E2A68]">
                <x-icon name="users" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                <p class="text-sm font-semibold text-slate-300">Belum ada staf kasir terdaftar</p>
                <p class="text-xs text-slate-500 mt-0.5">Tambahkan staf untuk memberikan hak akses kasir</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $staffMembers->links() }}
    </div>

    <!-- Staff Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-sm rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <h3 class="text-base font-black text-white">
                        {{ $userId ? 'Ubah Data Staf' : 'Tambah Staf Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nama Lengkap</label>
                        <input type="text" wire:model="name" placeholder="cth: Budi Setiawan" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                        @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Alamat Email</label>
                        <input type="email" wire:model="email" placeholder="budi@outlet.id" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                        @error('email') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nomor WhatsApp</label>
                        <input type="text" wire:model="phone" placeholder="08123456789" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Peran / Role</label>
                        <select wire:model="roleId" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                            @forelse($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @empty
                                <option value="">Kasir Standar</option>
                            @endforelse
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">
                            {{ $userId ? 'Ganti PIN (6 Digit, Kosongkan jika tetap)' : 'PIN Kasir (6 Digit)' }}
                        </label>
                        <input type="password" maxlength="6" wire:model="pin" placeholder="123456" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white font-mono text-center tracking-widest placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                        @error('pin') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button 
                    wire:click="saveStaff"
                    class="w-full py-3.5 bg-[#00AAA6] hover:bg-[#008F8C] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95"
                >
                    {{ $userId ? 'Simpan Perubahan' : 'Daftarkan Staf' }}
                </button>
            </div>
        </div>
    @endif
</div>

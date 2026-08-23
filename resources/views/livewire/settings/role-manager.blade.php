<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#1E1B4B] p-5 md:p-6 rounded-3xl border border-[#2E2A68] shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('settings.index') }}" class="w-10 h-10 rounded-2xl bg-[#16192E] hover:bg-[#2A3155] text-slate-300 flex items-center justify-center font-bold text-sm border border-[#2E2A68] transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white flex items-center gap-2">
                    <span>Hak Akses & Peran (RBAC)</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Konfigurasi hak akses modul dan batasan fitur kasir secara dinamis</p>
            </div>
        </div>
        <button 
            wire:click="openCreateModal"
            class="px-5 py-3 bg-[#00AAA6] hover:bg-[#008F8C] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg flex items-center justify-center gap-2 active:scale-95 transition shrink-0"
        >
            <x-icon name="plus" class="w-4 h-4" />
            <span>Tambah Peran</span>
        </button>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="check" class="w-4 h-4 text-emerald-400 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="bg-rose-500/10 border border-rose-500/30 text-rose-300 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2.5">
            <x-icon name="info" class="w-4 h-4 text-rose-400 shrink-0" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($roles as $role)
            <div class="bg-[#1E1B4B] border border-[#2E2A68] rounded-3xl p-5 shadow-md hover:border-[#00AAA6] transition space-y-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center gap-3.5">
                        <div class="h-11 w-11 rounded-2xl {{ $role->slug === 'owner' ? 'bg-[#00AAA6] text-white' : 'bg-[#16192E] text-[#8696ED] border border-[#2E2A68]' }} flex items-center justify-center text-sm font-black shrink-0">
                            <x-icon name="shield" class="w-5 h-5 {{ $role->slug === 'owner' ? 'text-white' : 'text-[#8696ED]' }}" />
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-black text-sm text-white flex items-center gap-1.5 truncate">
                                {{ $role->name }}
                                @if($role->slug === 'owner')
                                    <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-[#8696ED]/30 text-[#8696ED] shrink-0">Sistem</span>
                                @endif
                            </h4>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5 line-clamp-1">{{ $role->description ?? 'Hak akses staf' }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-3 border-t border-[#2E2A68]">
                    <button wire:click="openEditModal('{{ $role->id }}')" class="flex-1 py-2 bg-[#16192E] hover:bg-[#00AAA6] text-slate-200 hover:text-white rounded-xl text-xs font-bold border border-[#2E2A68] transition flex items-center justify-center gap-1.5">
                        <x-icon name="edit" class="w-3.5 h-3.5" />
                        <span>Edit Izin</span>
                    </button>
                    @if($role->slug !== 'owner')
                        <button wire:click="deleteRole('{{ $role->id }}')" wire:confirm="Yakin ingin menghapus peran ini?" class="p-2 bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white rounded-xl text-xs font-bold border border-rose-500/30 transition" title="Hapus Peran">
                            <x-icon name="trash" class="w-3.5 h-3.5" />
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-[#1E1B4B] rounded-3xl border border-[#2E2A68]">
                <x-icon name="shield" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                <p class="text-sm font-semibold text-slate-300">Belum ada peran kustom</p>
                <p class="text-xs text-slate-500 mt-0.5">Tambahkan peran baru untuk mengontrol izin modul staf</p>
            </div>
        @endforelse
    </div>

    <!-- Role Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-[#1E1B4B] border border-[#2E2A68] w-full max-w-lg rounded-t-[32px] sm:rounded-3xl p-6 space-y-5 shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-[#2E2A68] pb-3">
                    <div>
                        <h3 class="text-base font-black text-white">
                            {{ $roleId ? 'Ubah Izin Peran' : 'Tambah Peran Baru' }}
                        </h3>
                        <p class="text-[10px] text-slate-400 font-medium">Pilih modul yang diizinkan untuk peran ini</p>
                    </div>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-white font-bold p-1">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Nama Peran / Jabatan</label>
                        <input type="text" wire:model="name" placeholder="cth: Supervisor Outlet / Leader Barista" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                        @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Deskripsi Tugas</label>
                        <input type="text" wire:model="description" placeholder="cth: Bertanggung jawab atas stok & tutup kasir" class="w-full px-3.5 py-2.5 bg-[#16192E] border border-[#2E2A68] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-[#00AAA6]">
                    </div>

                    <!-- Permission Matrix -->
                    <div class="space-y-4 pt-2 border-t border-[#2E2A68]">
                        @foreach($permissionCategories as $catKey => $catInfo)
                            <div class="space-y-2">
                                <div class="text-xs font-black text-[#3EDAD7] uppercase tracking-wider">
                                    {{ $catInfo['label'] }}
                                </div>
                                <div class="grid grid-cols-1 gap-1.5">
                                    @foreach(collect($allPermissions)->where('category', $catKey) as $perm)
                                        @php $isSelected = in_array($perm['id'], $selectedPermissions); @endphp
                                        <button 
                                            type="button" 
                                            wire:click="togglePermission('{{ $perm['id'] }}')"
                                            class="flex items-center justify-between p-3 rounded-2xl border text-left transition {{ $isSelected ? 'bg-[#00AAA6]/20 border-[#00AAA6] text-white shadow-sm' : 'bg-[#16192E] border-[#2E2A68] text-slate-400 hover:text-white' }}"
                                        >
                                            <span class="font-bold text-xs">{{ $perm['label'] }}</span>
                                            <div class="w-5 h-5 rounded-lg border flex items-center justify-center text-[10px] {{ $isSelected ? 'bg-[#00AAA6] border-[#00AAA6] text-white' : 'border-[#2E2A68]' }}">
                                                @if($isSelected)
                                                    <x-icon name="check" class="w-3.5 h-3.5 text-white" />
                                                @endif
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-2 border-t border-[#2E2A68]">
                    <button 
                        wire:click="saveRole"
                        class="w-full py-3.5 bg-[#00AAA6] hover:bg-[#008F8C] text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition active:scale-95"
                    >
                        Simpan Izin Peran
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

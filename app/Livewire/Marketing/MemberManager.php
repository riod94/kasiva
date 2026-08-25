<?php

namespace App\Livewire\Marketing;

use App\Models\LoyaltyMember;
use App\Models\LoyaltyProgram;
use App\Services\LoyaltyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Members & QR Code - Kasiva POS')]
class MemberManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $activeTab = 'ALL'; // ALL | ASSIGNED | UNASSIGNED

    public array $selectedIds = [];

    public bool $showModal = false;

    public ?string $memberId = null;

    public string $name = '';

    public string $phone = '';

    public string $email = '';

    // profile sheet
    public ?string $profileId = null;

    public bool $showProfile = false;

    public bool $isEditing = false;

    // batch
    public bool $showBatchModal = false;

    public int $batchCount = 10;

    // print
    public bool $showPrintPreview = false;

    public array $printIds = [];

    public function mount(): void
    {
        abort_unless(auth()->check() && (auth()->user()->hasPermission('VIEW_MEMBERS') || auth()->user()->hasPermission('MANAGE_MEMBERS')), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat data pelanggan.');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function toggleSelect(string $id): void
    {
        if (in_array($id, $this->selectedIds, true)) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        } else {
            $this->selectedIds[] = $id;
        }
    }

    public function toggleSelectAll(): void
    {
        $visibleIds = $this->getFilteredQuery()->pluck('id')->toArray();
        $allSelected = count(array_intersect($visibleIds, $this->selectedIds)) === count($visibleIds) && count($visibleIds) > 0;
        if ($allSelected) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, $visibleIds));
        } else {
            $this->selectedIds = array_values(array_unique(array_merge($this->selectedIds, $visibleIds)));
        }
    }

    public function openCreateModal(): void
    {
        $this->reset(['memberId', 'name', 'phone', 'email']);
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $m = LoyaltyMember::findOrFail($id);
        $this->memberId = $m->id;
        $this->name = $m->name ?? '';
        $this->phone = $m->phone ?? '';
        $this->email = $m->email ?? '';
        $this->showModal = true;
        $this->showProfile = false;
    }

    public function openProfile(string $id): void
    {
        $this->profileId = $id;
        $this->isEditing = false;
        $m = LoyaltyMember::findOrFail($id);
        $this->name = $m->name ?? '';
        $this->phone = $m->phone ?? '';
        $this->email = $m->email ?? '';
        $this->showProfile = true;
    }

    public function startEditProfile(): void
    {
        $this->isEditing = true;
    }

    public function cancelEditProfile(): void
    {
        $this->isEditing = false;
        if ($this->profileId) {
            $m = LoyaltyMember::findOrFail($this->profileId);
            $this->name = $m->name ?? '';
            $this->phone = $m->phone ?? '';
            $this->email = $m->email ?? '';
        }
    }

    public function saveMember(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:120',
        ]);

        if ($this->profileId && $this->showProfile && $this->isEditing) {
            $m = LoyaltyMember::findOrFail($this->profileId);
            $isAssigned = ! empty($this->name) || ! empty($this->phone);
            $m->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email ?: null,
                'status' => $isAssigned ? 'ASSIGNED' : 'UNASSIGNED',
                'assigned_at' => $isAssigned ? ($m->assigned_at ?? now()) : null,
            ]);
            $this->isEditing = false;
            session()->flash('message', 'Profil member diperbarui.');

            return;
        }

        if ($this->memberId) {
            $m = LoyaltyMember::findOrFail($this->memberId);
            $m->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email ?: null,
                'status' => 'ASSIGNED',
                'assigned_at' => $m->assigned_at ?? now(),
            ]);
        } else {
            LoyaltyMember::create([
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email ?: null,
                'status' => 'ASSIGNED',
                'assigned_at' => now(),
            ]);
        }

        $this->showModal = false;
        $this->reset(['memberId', 'name', 'phone', 'email']);
        session()->flash('message', 'Data pelanggan member berhasil disimpan.');
    }

    public function deleteMember(string $id): void
    {
        $m = LoyaltyMember::findOrFail($id);
        $m->stamps()->delete();
        $m->rewards()->delete();
        $m->delete();
        $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        if ($this->profileId === $id) {
            $this->showProfile = false;
        }
        session()->flash('message', 'Member berhasil dihapus.');
    }

    public function generateBatch(): void
    {
        $count = max(1, min(50, (int) $this->batchCount));
        $now = now();
        $rows = [];
        for ($i = 0; $i < $count; $i++) {
            $code = 'KSV-MBR-'.strtoupper(Str::random(8));
            while (LoyaltyMember::where('qr_code', $code)->exists() || isset($rows[$code])) {
                $code = 'KSV-MBR-'.strtoupper(Str::random(8));
            }
            $rows[] = [
                'id' => (string) Str::uuid(),
                'name' => null,
                'phone' => null,
                'qr_code' => $code,
                'status' => 'UNASSIGNED',
                'email' => null,
                'assigned_at' => null,
                'stamps_count' => 0,
                'total_visits' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('loyalty_members')->insert($rows);
        $this->showBatchModal = false;
        $this->printIds = array_column($rows, 'id');
        $this->showPrintPreview = true;
        session()->flash('message', $count.' QR Member berhasil dibuat!');
    }

    public function bulkDelete(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }
        $members = LoyaltyMember::whereIn('id', $this->selectedIds)->get();
        foreach ($members as $m) {
            $m->stamps()->delete();
            $m->rewards()->delete();
        }
        LoyaltyMember::whereIn('id', $this->selectedIds)->delete();
        $count = count($this->selectedIds);
        $this->selectedIds = [];
        session()->flash('message', $count.' member berhasil dihapus.');
    }

    public function openBulkPrint(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }
        $this->printIds = $this->selectedIds;
        $this->showPrintPreview = true;
    }

    public function closePrintPreview(): void
    {
        $this->showPrintPreview = false;
    }

    private function getFilteredQuery()
    {
        $q = LoyaltyMember::query();
        if ($this->activeTab === 'ASSIGNED') {
            $q->where('status', 'ASSIGNED');
        } elseif ($this->activeTab === 'UNASSIGNED') {
            $q->where('status', 'UNASSIGNED');
        }
        if (! empty($this->search)) {
            $s = $this->search;
            $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('qr_code', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }

        return $q;
    }

    public function render()
    {
        $members = $this->getFilteredQuery()->latest()->paginate(12);
        $printMembers = $this->showPrintPreview && ! empty($this->printIds)
            ? LoyaltyMember::whereIn('id', $this->printIds)->get()
            : collect();

        // progress for profile sheet
        $profileMember = $this->profileId ? LoyaltyMember::with('stamps')->find($this->profileId) : null;
        $activeProgram = LoyaltyProgram::where('is_active', true)->first();
        $profileProgress = null;
        $profileStampsCount = 0;
        if ($profileMember && $activeProgram) {
            try {
                $profileProgress = LoyaltyService::getCustomerProgress($profileMember, $activeProgram);
                $profileStampsCount = $profileProgress['currentStamps'];
            } catch (\Throwable $e) {
            }
        } elseif ($profileMember) {
            $profileStampsCount = $profileMember->stamps()->count();
        }

        return view('livewire.marketing.member-manager', [
            'members' => $members,
            'printMembers' => $printMembers,
            'profileMember' => $profileMember,
            'activeProgram' => $activeProgram,
            'profileProgress' => $profileProgress,
            'profileStampsCount' => $profileStampsCount,
        ]);
    }
}

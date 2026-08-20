<?php

namespace App\Livewire\Marketing;

use App\Models\LoyaltyMember;
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
    public bool $showModal = false;
    public ?string $memberId = null;
    public string $name = '';
    public string $phone = '';

    public function mount(): void
    {
        abort_unless(auth()->check() && (auth()->user()->hasPermission('VIEW_MEMBERS') || auth()->user()->hasPermission('MANAGE_MEMBERS')), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat data pelanggan.');
    }

    public function openCreateModal(): void
    {
        $this->reset(['memberId', 'name', 'phone']);
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $m = LoyaltyMember::findOrFail($id);
        $this->memberId = $m->id;
        $this->name = $m->name;
        $this->phone = $m->phone;
        $this->showModal = true;
    }

    public function saveMember(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
        ]);

        LoyaltyMember::updateOrCreate(
            ['id' => $this->memberId],
            [
                'name' => $this->name,
                'phone' => $this->phone,
            ]
        );

        $this->showModal = false;
        session()->flash('message', 'Data pelanggan member berhasil disimpan.');
    }

    public function deleteMember(string $id): void
    {
        LoyaltyMember::findOrFail($id)->delete();
        session()->flash('message', 'Member berhasil dihapus.');
    }

    public function render()
    {
        $members = LoyaltyMember::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(12);

        return view('livewire.marketing.member-manager', [
            'members' => $members,
        ]);
    }
}

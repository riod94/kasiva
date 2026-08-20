<?php

namespace App\Livewire\Marketing;

use App\Models\LoyaltyMember;
use App\Models\LoyaltyReward;
use App\Models\LoyaltyStamp;
use Livewire\Component;
use Livewire\WithPagination;

class LoyaltyManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showMemberModal = false;
    public string $memberName = '';
    public string $memberPhone = '';

    public ?string $selectedMemberId = null;
    public $selectedMember = null;
    public bool $showStampModal = false;

    protected $rules = [
        'memberName' => 'required|string|max:100',
        'memberPhone' => 'required|string|max:20|unique:loyalty_members,phone',
    ];

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasPermission('MANAGE_LOYALTY'), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola program loyalitas.');
    }

    public function openCreateMemberModal(): void
    {
        $this->reset(['memberName', 'memberPhone']);
        $this->showMemberModal = true;
    }

    public function saveMember(): void
    {
        $this->validate();

        LoyaltyMember::create([
            'name' => $this->memberName,
            'phone' => $this->memberPhone,
            'stamps_count' => 1,
            'total_visits' => 1,
        ]);

        $this->showMemberModal = false;
        $this->reset(['memberName', 'memberPhone']);
        session()->flash('message', 'Member loyalitas baru berhasil didaftarkan.');
    }

    public function openStampModal(string $id): void
    {
        $this->selectedMemberId = $id;
        $this->selectedMember = LoyaltyMember::findOrFail($id);
        $this->showStampModal = true;
    }

    public function addStamp(): void
    {
        if ($this->selectedMember) {
            $this->selectedMember->increment('stamps_count');
            $this->selectedMember->increment('total_visits');

            LoyaltyStamp::create([
                'loyalty_member_id' => $this->selectedMember->id,
                'stamps_earned' => 1,
            ]);

            session()->flash('message', '1 Stempel berhasil ditambahkan untuk ' . $this->selectedMember->name . '.');
            $this->showStampModal = false;
        }
    }

    public function redeemReward(): void
    {
        if ($this->selectedMember && $this->selectedMember->stamps_count >= 10) {
            $this->selectedMember->decrement('stamps_count', 10);
            session()->flash('message', 'Reward 1 Menu Gratis berhasil diklaim untuk ' . $this->selectedMember->name . '!');
            $this->showStampModal = false;
        }
    }

    public function render()
    {
        $members = LoyaltyMember::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.marketing.loyalty-manager', [
            'members' => $members,
        ])->layout('layouts.app');
    }
}

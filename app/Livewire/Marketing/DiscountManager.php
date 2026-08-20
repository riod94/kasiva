<?php

namespace App\Livewire\Marketing;

use App\Models\Promotion;
use Livewire\Component;
use Livewire\WithPagination;

class DiscountManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?string $promoId = null;
    public string $name = '';
    public string $type = 'PERCENTAGE'; // PERCENTAGE or FIXED_AMOUNT
    public float $discount_value = 10.0;
    public float $min_purchase = 0.0;
    public bool $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:100',
        'type' => 'required|in:PERCENTAGE,FIXED_AMOUNT',
        'discount_value' => 'required|numeric|min:0.01',
        'min_purchase' => 'required|numeric|min:0',
        'is_active' => 'boolean',
    ];

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasPermission('MANAGE_PROMOS'), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola diskon & promosi.');
    }

    public function openCreateModal(): void
    {
        $this->reset(['promoId', 'name', 'type', 'discount_value', 'min_purchase', 'is_active']);
        $this->type = 'PERCENTAGE';
        $this->discount_value = 10.0;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $promo = Promotion::findOrFail($id);
        $this->promoId = $promo->id;
        $this->name = $promo->name;
        $this->type = $promo->type;
        $this->discount_value = (float)$promo->discount_value;
        $this->min_purchase = (float)$promo->min_purchase;
        $this->is_active = (bool)$promo->is_active;
        $this->showModal = true;
    }

    public function savePromo(): void
    {
        $this->validate();

        if ($this->promoId) {
            $promo = Promotion::findOrFail($this->promoId);
            $promo->update([
                'name' => $this->name,
                'type' => $this->type,
                'discount_value' => $this->discount_value,
                'min_purchase' => $this->min_purchase,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Promo diskon berhasil diperbarui.');
        } else {
            Promotion::create([
                'name' => $this->name,
                'type' => $this->type,
                'discount_value' => $this->discount_value,
                'min_purchase' => $this->min_purchase,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Promo diskon baru berhasil dibuat.');
        }

        $this->showModal = false;
        $this->reset(['promoId', 'name', 'type', 'discount_value', 'min_purchase', 'is_active']);
    }

    public function toggleActive(string $id): void
    {
        $promo = Promotion::findOrFail($id);
        $promo->update(['is_active' => !$promo->is_active]);
        session()->flash('message', 'Status promo berhasil diubah.');
    }

    public function deletePromo(string $id): void
    {
        Promotion::findOrFail($id)->delete();
        session()->flash('message', 'Promo berhasil dihapus.');
    }

    public function render()
    {
        $promotions = Promotion::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate(10);

        return view('livewire.marketing.discount-manager', [
            'promotions' => $promotions,
        ])->layout('layouts.app');
    }
}

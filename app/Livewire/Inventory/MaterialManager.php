<?php

namespace App\Livewire\Inventory;

use App\Models\Material;
use App\Services\HppCalculatorService;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialManager extends Component
{
    use WithPagination;

    public string $search = '';

    // Create / Edit Modal State
    public bool $showModal = false;

    public ?string $materialId = null;

    public string $name = '';

    public string $unit = 'gram';

    public float $current_stock = 0.0;

    public float $min_stock = 100.0;

    public float $avg_cost = 0.0;

    // Restock Modal State
    public bool $showRestockModal = false;

    public ?string $restockMaterialId = null;

    public string $restockMaterialName = '';

    public string $restockUnit = '';

    public float $restockQuantity = 0.0;

    public float $restockTotalCost = 0.0;

    public string $restockNotes = '';

    protected $rules = [
        'name' => 'required|string|max:150',
        'unit' => 'required|string|max:30',
        'min_stock' => 'required|numeric|min:0',
        'avg_cost' => 'required|numeric|min:0',
    ];

    public function mount(): void
    {
        abort_unless(auth()->check() && (auth()->user()->hasPermission('VIEW_MATERIALS') || auth()->user()->hasPermission('MANAGE_MATERIALS')), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola stok bahan baku.');
    }

    public function openCreateModal(): void
    {
        $this->reset(['materialId', 'name', 'unit', 'current_stock', 'min_stock', 'avg_cost']);
        $this->unit = 'gram';
        $this->min_stock = 100.0;
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $material = Material::findOrFail($id);
        $this->materialId = $material->id;
        $this->name = $material->name;
        $this->unit = $material->unit;
        $this->current_stock = (float) $material->current_stock;
        $this->min_stock = (float) $material->min_stock;
        $this->avg_cost = (float) $material->avg_cost;
        $this->showModal = true;
    }

    public function saveMaterial(): void
    {
        $this->validate();

        if ($this->materialId) {
            $material = Material::findOrFail($this->materialId);
            $material->update([
                'name' => $this->name,
                'unit' => $this->unit,
                'min_stock' => $this->min_stock,
                'avg_cost' => $this->avg_cost,
            ]);
            session()->flash('message', 'Bahan baku berhasil diperbarui.');
        } else {
            Material::create([
                'name' => $this->name,
                'unit' => $this->unit,
                'current_stock' => $this->current_stock,
                'min_stock' => $this->min_stock,
                'avg_cost' => $this->avg_cost,
                'is_active' => true,
            ]);
            session()->flash('message', 'Bahan baku baru berhasil ditambahkan ke library.');
        }

        $this->showModal = false;
        $this->reset(['materialId', 'name', 'unit', 'current_stock', 'min_stock', 'avg_cost']);
    }

    public function openRestockModal(string $id): void
    {
        $material = Material::findOrFail($id);
        $this->restockMaterialId = $material->id;
        $this->restockMaterialName = $material->name;
        $this->restockUnit = $material->unit;
        $this->restockQuantity = 0.0;
        $this->restockTotalCost = 0.0;
        $this->restockNotes = 'Restok bahan baku dari supplier';
        $this->showRestockModal = true;
    }

    public function processRestock(): void
    {
        $this->validate([
            'restockQuantity' => 'required|numeric|min:0.01',
            'restockTotalCost' => 'required|numeric|min:0',
            'restockNotes' => 'nullable|string|max:255',
        ]);

        $material = Material::findOrFail($this->restockMaterialId);
        $incomingQty = (float) $this->restockQuantity;
        $incomingTotalCost = (float) $this->restockTotalCost;
        $unitPrice = $incomingQty > 0 ? $incomingTotalCost / $incomingQty : 0;

        app(HppCalculatorService::class)->recalculateMovingAverage(
            $material,
            $incomingQty,
            $unitPrice,
            $this->restockNotes ?: 'Restok bahan baku dari supplier'
        );

        $this->showRestockModal = false;
        $this->reset(['restockMaterialId', 'restockMaterialName', 'restockUnit', 'restockQuantity', 'restockTotalCost', 'restockNotes']);
        session()->flash('message', 'Restok bahan baku berhasil dicatat. Stok dan HPP rata-rata diperbarui otomatis.');
    }

    public function deleteMaterial(string $id): void
    {
        Material::findOrFail($id)->delete();
        session()->flash('message', 'Bahan baku berhasil dihapus dari library.');
    }

    public function render()
    {
        $materials = Material::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.inventory.material-manager', [
            'materials' => $materials,
        ])->layout('layouts.app');
    }
}

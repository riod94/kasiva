<?php

namespace App\Livewire\Marketing;

use App\Models\Bundle;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Bundle Produk - Kasiva POS')]
class BundleManager extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?string $bundleId = null;
    public string $name = '';
    public float $price = 0;
    public float $cogs = 0;
    public bool $is_active = true;
    public array $items = [];

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasPermission('MANAGE_PROMOS'), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola bundle produk.');
    }

    public function openCreateModal(): void
    {
        $this->reset(['bundleId', 'name', 'price', 'cogs', 'is_active', 'items']);
        $this->addItem();
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $bundle = Bundle::findOrFail($id);
        $this->bundleId = $bundle->id;
        $this->name = $bundle->name;
        $this->price = (float)$bundle->price;
        $this->cogs = (float)$bundle->cogs;
        $this->is_active = $bundle->is_active;
        $this->items = is_array($bundle->items) ? $bundle->items : [];
        $this->showModal = true;
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => '', 'quantity' => 1];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateBundleCogs();
    }

    public function calculateBundleCogs(): void
    {
        $totalCogs = 0;
        foreach ($this->items as $item) {
            if (!empty($item['product_id'])) {
                $p = Product::find($item['product_id']);
                if ($p) {
                    $qty = (int)($item['quantity'] ?? 1);
                    $totalCogs += ($p->hpp * $qty);
                }
            }
        }
        $this->cogs = $totalCogs;
    }

    public function saveBundle(): void
    {
        $this->validate([
            'name' => 'required|string|max:150',
            'price' => 'required|numeric|min:0',
        ]);

        $this->calculateBundleCogs();

        $formattedItems = [];
        foreach ($this->items as $item) {
            if (!empty($item['product_id'])) {
                $p = Product::find($item['product_id']);
                $formattedItems[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $p?->name ?? 'Produk',
                    'quantity' => (int)($item['quantity'] ?? 1),
                ];
            }
        }

        Bundle::updateOrCreate(
            ['id' => $this->bundleId],
            [
                'name' => $this->name,
                'price' => $this->price,
                'cogs' => $this->cogs,
                'is_active' => $this->is_active,
                'items' => $formattedItems,
            ]
        );

        $this->showModal = false;
        session()->flash('message', 'Paket bundle produk berhasil disimpan.');
    }

    public function toggleActive(string $id): void
    {
        $b = Bundle::findOrFail($id);
        $b->is_active = !$b->is_active;
        $b->save();
    }

    public function deleteBundle(string $id): void
    {
        Bundle::findOrFail($id)->delete();
        session()->flash('message', 'Bundle berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.marketing.bundle-manager', [
            'bundles' => Bundle::latest()->paginate(9),
            'allProducts' => Product::where('is_active', true)->get(),
        ]);
    }
}

<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantOption;
use Livewire\Component;
use Livewire\WithPagination;

class VariationManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?string $variantId = null;
    public ?string $productId = null;
    public string $name = '';
    public string $selection_type = 'SINGLE'; // SINGLE or MULTIPLE
    public bool $is_required = false;

    // Options dynamic list: [['name' => '', 'price_modifier' => 0, 'cogs_modifier' => 0]]
    public array $options = [];

    protected $rules = [
        'productId' => 'required|exists:products,id',
        'name' => 'required|string|max:100',
        'selection_type' => 'required|in:SINGLE,MULTIPLE',
        'is_required' => 'boolean',
        'options' => 'required|array|min:1',
        'options.*.name' => 'required|string|max:100',
        'options.*.price_modifier' => 'required|numeric',
        'options.*.cogs_modifier' => 'required|numeric',
    ];

    public function mount(): void
    {
        abort_unless(auth()->check() && (auth()->user()->hasPermission('VIEW_PRODUCTS') || auth()->user()->hasPermission('MANAGE_PRODUCTS')), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola varian produk.');
    }

    public function openCreateModal(): void
    {
        $this->reset(['variantId', 'productId', 'name', 'selection_type', 'is_required', 'options']);
        $firstProduct = Product::first();
        $this->productId = $firstProduct ? $firstProduct->id : null;
        $this->options = [
            ['name' => 'Regular', 'price_modifier' => 0, 'cogs_modifier' => 0],
            ['name' => 'Large (+3k)', 'price_modifier' => 3000, 'cogs_modifier' => 1000],
        ];
        $this->showModal = true;
    }

    public function addOption(): void
    {
        $this->options[] = ['name' => '', 'price_modifier' => 0, 'cogs_modifier' => 0];
    }

    public function removeOption(int $index): void
    {
        if (count($this->options) > 1) {
            unset($this->options[$index]);
            $this->options = array_values($this->options);
        }
    }

    public function openEditModal(string $id): void
    {
        $variant = ProductVariant::with('options')->findOrFail($id);
        $this->variantId = $variant->id;
        $this->productId = $variant->product_id;
        $this->name = $variant->name;
        $this->selection_type = $variant->selection_type;
        $this->is_required = (bool)$variant->is_required;
        $this->options = $variant->options->map(fn($opt) => [
            'name' => $opt->name,
            'price_modifier' => (float)$opt->price_modifier,
            'cogs_modifier' => (float)$opt->cogs_modifier,
        ])->toArray();
        $this->showModal = true;
    }

    public function saveVariant(): void
    {
        $this->validate();

        if ($this->variantId) {
            $variant = ProductVariant::findOrFail($this->variantId);
            $variant->update([
                'product_id' => $this->productId,
                'name' => $this->name,
                'selection_type' => $this->selection_type,
                'is_required' => $this->is_required,
            ]);

            // Recreate options
            $variant->options()->delete();
            foreach ($this->options as $opt) {
                VariantOption::create([
                    'product_variant_id' => $variant->id,
                    'name' => $opt['name'],
                    'price_modifier' => $opt['price_modifier'],
                    'cogs_modifier' => $opt['cogs_modifier'],
                ]);
            }

            session()->flash('message', 'Varian produk berhasil diperbarui.');
        } else {
            $variant = ProductVariant::create([
                'product_id' => $this->productId,
                'name' => $this->name,
                'selection_type' => $this->selection_type,
                'is_required' => $this->is_required,
            ]);

            foreach ($this->options as $opt) {
                VariantOption::create([
                    'product_variant_id' => $variant->id,
                    'name' => $opt['name'],
                    'price_modifier' => $opt['price_modifier'],
                    'cogs_modifier' => $opt['cogs_modifier'],
                ]);
            }

            session()->flash('message', 'Varian baru berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->reset(['variantId', 'productId', 'name', 'selection_type', 'is_required', 'options']);
    }

    public function deleteVariant(string $id): void
    {
        ProductVariant::findOrFail($id)->delete();
        session()->flash('message', 'Varian berhasil dihapus.');
    }

    public function render()
    {
        $variants = ProductVariant::with(['product', 'options'])
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate(10);

        $products = Product::orderBy('name')->get();

        return view('livewire.inventory.variation-manager', [
            'variants' => $variants,
            'products' => $products,
        ])->layout('layouts.app');
    }
}

<?php

namespace App\Livewire\Settings;

use App\Models\Category;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantOption;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Manajemen Produk & Resep HPP — Kasiva POS')]
class ProductManager extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public string $categoryFilter = 'ALL';

    public bool $showModal = false;

    public ?string $productId = null;

    // Form fields
    public string $name = '';

    public string $sku = '';

    public ?string $category_id = '';

    public float $price = 0;

    public float $hpp = 0;

    public float $current_stock = 100;

    public $image = null; // File upload object

    public ?string $image_url = null; // Stored path

    public bool $is_active = true;

    // Recipe Builder: [['material_id' => '...', 'quantity' => 1]]
    public array $selectedMaterials = [];

    // Variants Builder: [['name' => 'Level Gula', 'selection_type' => 'SINGLE', 'options' => [['name' => 'Normal', 'price_modifier' => 0]]]]
    public array $variants = [];

    public function mount(): void
    {
        abort_unless(auth()->check() && (auth()->user()->hasPermission('VIEW_PRODUCTS') || auth()->user()->hasPermission('MANAGE_PRODUCTS')), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola katalog produk.');
    }

    public function openCreateModal(): void
    {
        $this->reset(['productId', 'name', 'sku', 'category_id', 'price', 'hpp', 'current_stock', 'image', 'image_url', 'is_active', 'selectedMaterials', 'variants']);
        $this->sku = 'KSV-'.strtoupper(substr(md5((string) microtime()), 0, 6));
        $this->is_active = true;
        $this->current_stock = 100;
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $product = Product::with(['materials', 'variants.options'])->findOrFail($id);
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->sku = $product->sku ?? '';
        $this->category_id = $product->category_id;
        $this->price = (float) $product->price;
        $this->hpp = (float) $product->hpp;
        $this->current_stock = (float) $product->current_stock;
        $this->image = null;
        $this->image_url = $product->image_url;
        $this->is_active = (bool) $product->is_active;

        // Load existing recipes
        $this->selectedMaterials = [];
        foreach ($product->materials as $mat) {
            $this->selectedMaterials[] = [
                'material_id' => $mat->id,
                'quantity' => (float) $mat->pivot->quantity,
            ];
        }

        // Load existing variants
        $this->variants = [];
        foreach ($product->variants as $variant) {
            $opts = [];
            foreach ($variant->options as $opt) {
                $opts[] = [
                    'name' => $opt->name,
                    'price_modifier' => (float) $opt->price_modifier,
                ];
            }
            $this->variants[] = [
                'name' => $variant->name,
                'selection_type' => $variant->selection_type,
                'options' => $opts,
            ];
        }

        $this->calculateHpp();
        $this->showModal = true;
    }

    public function removeImage(): void
    {
        $this->image = null;
        $this->image_url = null;
    }

    public function addMaterialRow(): void
    {
        $this->selectedMaterials[] = ['material_id' => '', 'quantity' => 1];
    }

    public function removeMaterialRow(int $index): void
    {
        unset($this->selectedMaterials[$index]);
        $this->selectedMaterials = array_values($this->selectedMaterials);
        $this->calculateHpp();
    }

    public function calculateHpp(): void
    {
        $totalHpp = 0;
        foreach ($this->selectedMaterials as $item) {
            if (! empty($item['material_id']) && ! empty($item['quantity'])) {
                $material = Material::find($item['material_id']);
                if ($material) {
                    $totalHpp += ($material->avg_cost * (float) $item['quantity']);
                }
            }
        }
        $this->hpp = round($totalHpp, 2);
    }

    public function saveProduct(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $this->calculateHpp();

        if ($this->image) {
            $path = $this->image->store('products', 'public');
            $this->image_url = '/storage/'.$path;
        }

        $product = Product::updateOrCreate(
            ['id' => $this->productId],
            [
                'name' => $this->name,
                'sku' => $this->sku,
                'category_id' => $this->category_id ?: null,
                'price' => $this->price,
                'hpp' => $this->hpp,
                'current_stock' => $this->current_stock,
                'image_url' => $this->image_url,
                'is_active' => $this->is_active,
            ]
        );

        // Sync Recipe Materials
        $attachData = [];
        foreach ($this->selectedMaterials as $item) {
            if (! empty($item['material_id']) && ! empty($item['quantity'])) {
                $attachData[$item['material_id']] = ['quantity' => (float) $item['quantity']];
            }
        }
        $product->materials()->sync($attachData);

        // Sync Variants
        if (! empty($this->variants)) {
            ProductVariant::where('product_id', $product->id)->delete();
            foreach ($this->variants as $vIdx => $v) {
                if (! empty($v['name'])) {
                    $variantGroup = ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $v['name'],
                        'selection_type' => $v['selection_type'] ?? 'SINGLE',
                        'order_index' => $vIdx + 1,
                    ]);

                    foreach ($v['options'] as $opt) {
                        if (! empty($opt['name'])) {
                            VariantOption::create([
                                'product_variant_id' => $variantGroup->id,
                                'name' => $opt['name'],
                                'price_modifier' => (float) ($opt['price_modifier'] ?? 0),
                            ]);
                        }
                    }
                }
            }
        }

        $this->showModal = false;
        session()->flash('message', 'Katalog produk dan takaran resep berhasil diperbarui!');
    }

    public function toggleActive(string $id): void
    {
        $product = Product::findOrFail($id);
        $product->is_active = ! $product->is_active;
        $product->save();
    }

    public function deleteProduct(string $id): void
    {
        $product = Product::findOrFail($id);
        $product->materials()->detach();
        $product->variants()->delete();
        $product->delete();

        session()->flash('message', 'Produk telah dihapus dari sistem.');
    }

    public function render()
    {
        $query = Product::with(['category', 'materials', 'variants.options'])->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->categoryFilter !== 'ALL') {
            $query->where('category_id', $this->categoryFilter);
        }

        return view('livewire.settings.product-manager', [
            'products' => $query->paginate(12),
            'categories' => Category::orderBy('order_index')->get(),
            'allMaterials' => Material::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}

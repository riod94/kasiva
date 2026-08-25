<?php

namespace App\Livewire\Inventory;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryManager extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?string $categoryId = null;

    public string $name = '';

    public string $icon = '📦';

    public int $order_index = 0;

    protected $rules = [
        'name' => 'required|string|max:100',
        'icon' => 'nullable|string|max:50',
        'order_index' => 'required|integer',
    ];

    public function mount(): void
    {
        abort_unless(auth()->check() && (auth()->user()->hasPermission('MANAGE_CATEGORIES') || auth()->user()->hasPermission('MANAGE_PRODUCTS')), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola kategori produk.');
    }

    public function openCreateModal(): void
    {
        $this->reset(['categoryId', 'name', 'icon', 'order_index']);
        $this->icon = '📦';
        $this->order_index = Category::count() + 1;
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->icon = $category->icon ?? '📦';
        $this->order_index = $category->order_index;
        $this->showModal = true;
    }

    public function saveCategory(): void
    {
        $this->validate();

        if ($this->categoryId) {
            $category = Category::findOrFail($this->categoryId);
            $category->update([
                'name' => $this->name,
                'icon' => $this->icon,
                'order_index' => $this->order_index,
            ]);
            session()->flash('message', 'Kategori berhasil diperbarui.');
        } else {
            Category::create([
                'name' => $this->name,
                'icon' => $this->icon,
                'order_index' => $this->order_index,
            ]);
            session()->flash('message', 'Kategori baru berhasil ditambahkan.');
        }

        Cache::forget('kasiva:categories');
        $this->showModal = false;
        $this->reset(['categoryId', 'name', 'icon', 'order_index']);
    }

    public function deleteCategory(string $id): void
    {
        Category::findOrFail($id)->delete();
        Cache::forget('kasiva:categories');
        session()->flash('message', 'Kategori berhasil dihapus.');
    }

    public function render()
    {
        $categories = Category::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy('order_index')
            ->paginate(10);

        return view('livewire.inventory.category-manager', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}

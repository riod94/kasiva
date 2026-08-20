<?php

namespace App\Livewire\Inventory;

use App\Models\Category;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Component;

class InventoryHub extends Component
{
    public function mount(): void
    {
        $user = auth()->user();
        abort_unless(
            $user && (
                $user->isOwner() ||
                $user->hasPermission('VIEW_PRODUCTS') ||
                $user->hasPermission('VIEW_MATERIALS') ||
                $user->hasPermission('MANAGE_PRODUCTS')
            ),
            403,
            'Akses Ditolak: Anda tidak memiliki izin untuk melihat modul inventaris.'
        );
    }

    public function render()
    {
        $totalProducts = Product::where('is_active', true)->count();
        $totalCategories = Category::count();
        $totalMaterials = Material::count();
        $lowStockMaterials = Material::whereColumn('current_stock', '<=', 'min_stock')->count();
        $totalVariants = ProductVariant::count();

        return view('livewire.inventory.inventory-hub', [
            'totalProducts' => $totalProducts,
            'totalCategories' => $totalCategories,
            'totalMaterials' => $totalMaterials,
            'lowStockMaterials' => $lowStockMaterials,
            'totalVariants' => $totalVariants,
        ])->layout('layouts.app');
    }
}

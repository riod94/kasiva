<?php

use App\Models\Material;
use App\Models\Product;
use App\Services\HppCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('menghitung HPP moving average bahan baku saat restok dengan tepat', function () {
    $material = Material::create([
        'name' => 'Biji Kopi Arabika',
        'unit' => 'gram',
        'current_stock' => 10,
        'min_stock' => 5,
        'avg_cost' => 1000.0,
    ]);

    $service = new HppCalculatorService;
    $newAvgCost = $service->recalculateMovingAverage($material, 10, 2000.0);

    expect($newAvgCost)->toBe(1500.0);
    expect($material->fresh()->current_stock)->toBe(20.0);
    expect($material->fresh()->avg_cost)->toBe(1500.0);
});

test('menghitung total HPP produk dari resep bahan baku', function () {
    $coffee = Material::create([
        'name' => 'Biji Kopi',
        'unit' => 'gram',
        'current_stock' => 1000,
        'avg_cost' => 200.0, // 200/gram
    ]);

    $milk = Material::create([
        'name' => 'Susu Fresh Milk',
        'unit' => 'ml',
        'current_stock' => 5000,
        'avg_cost' => 20.0, // 20/ml
    ]);

    $product = Product::create([
        'name' => 'Kopi Aren Kasiva',
        'sku' => 'KSV-KOP-001',
        'price' => 18000.0,
        'hpp' => 0.0,
        'current_stock' => 50,
    ]);

    $product->materials()->attach([
        $coffee->id => ['quantity' => 18], // 18 * 200 = 3,600
        $milk->id => ['quantity' => 150],   // 150 * 20 = 3,000
    ]);

    $service = new HppCalculatorService;
    $totalHpp = $service->calculateProductHpp($product);

    expect($totalHpp)->toBe(6600.0);
    expect($product->fresh()->hpp)->toBe(6600.0);
});

test('mengurangi stok produk dan bahan resep setelah stok terkunci', function () {
    [$product, $material] = recipeProduct(productStock: 4, materialStock: 12, recipeQuantity: 3);

    app(HppCalculatorService::class)->deductRecipeStockForCheckout($product, 2);

    expect($product->fresh()->current_stock)->toBe(2.0)
        ->and($material->fresh()->current_stock)->toBe(6.0);
});

test('menolak checkout ketika stok bahan resep tidak mencukupi tanpa mengubah stok produk', function () {
    [$product, $material] = recipeProduct(productStock: 4, materialStock: 5, recipeQuantity: 3);

    expect(fn () => app(HppCalculatorService::class)->deductRecipeStockForCheckout($product, 2))
        ->toThrow(InvalidArgumentException::class, 'Stok bahan Bahan Uji tidak mencukupi.');

    expect($product->fresh()->current_stock)->toBe(4.0)
        ->and($material->fresh()->current_stock)->toBe(5.0);
});

test('uses the caller transaction without opening a nested transaction', function () {
    [$product, $material] = recipeProduct(productStock: 4, materialStock: 12, recipeQuantity: 3);

    DB::transaction(function () use ($product): void {
        $level = DB::transactionLevel();
        app(HppCalculatorService::class)->deductRecipeStockForCheckout($product, 2);
        expect(DB::transactionLevel())->toBe($level);
    });

    expect($product->fresh()->current_stock)->toBe(2.0)
        ->and($material->fresh()->current_stock)->toBe(6.0);
});

function recipeProduct(float $productStock, float $materialStock, float $recipeQuantity): array
{
    $material = Material::create([
        'name' => 'Bahan Uji',
        'unit' => 'gram',
        'current_stock' => $materialStock,
        'avg_cost' => 100.0,
    ]);

    $product = Product::create([
        'name' => 'Produk Uji',
        'sku' => 'SKU-'.uniqid(),
        'price' => 10000.0,
        'hpp' => 1000.0,
        'current_stock' => $productStock,
    ]);

    $product->materials()->attach($material->id, ['quantity' => $recipeQuantity]);

    return [$product, $material];
}

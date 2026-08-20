<?php

use App\Models\Material;
use App\Models\Product;
use App\Services\HppCalculatorService;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

test('menghitung HPP moving average bahan baku saat restok dengan tepat', function () {
    $material = Material::create([
        'name' => 'Biji Kopi Arabika',
        'unit' => 'gram',
        'current_stock' => 10,
        'min_stock' => 5,
        'avg_cost' => 1000.0,
    ]);

    $service = new HppCalculatorService();
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

    $service = new HppCalculatorService();
    $totalHpp = $service->calculateProductHpp($product);

    expect($totalHpp)->toBe(6600.0);
    expect($product->fresh()->hpp)->toBe(6600.0);
});

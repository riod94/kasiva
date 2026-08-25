<?php

use App\Models\Category;
use App\Models\Material;
use App\Models\Product;
use App\Services\HppCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('kalkulasi moving average cost bahan baku berfungsi presisi', function () {
    $calculator = new HppCalculatorService;

    $material = Material::create([
        'name' => 'Biji Kopi Arabika',
        'unit' => 'gram',
        'current_stock' => 1000,
        'avg_cost' => 200, // Total value = 200.000
    ]);

    // Restock 1000 gram dengan harga 300/gram (Total incoming value = 300.000)
    // New total stock = 2000 gram, New total value = 500.000, New avg cost = 250/gram
    $newAvgCost = $calculator->recalculateMovingAverage($material, 1000, 300);

    expect($newAvgCost)->toBe(250.0);
    expect($material->fresh()->current_stock)->toBe(2000.0);
    expect($material->fresh()->avg_cost)->toBe(250.0);
});

test('indikator kesehatan margin 4-tier dihitung dengan benar', function () {
    $cat = Category::create(['name' => 'Minuman', 'order_index' => 1]);

    // Produk 1: Kritis (< 30% margin) -> Price 10.000, HPP 8.000 = Margin 20%
    $p1 = Product::create([
        'category_id' => $cat->id,
        'name' => 'Produk Kritis',
        'sku' => 'SKU-001',
        'price' => 10000,
        'hpp' => 8000,
    ]);
    expect($p1->margin_tier['label'])->toBe('Kritis');

    // Produk 2: Tipis (30% - 44% margin) -> Price 10.000, HPP 6.500 = Margin 35%
    $p2 = Product::create([
        'category_id' => $cat->id,
        'name' => 'Produk Tipis',
        'sku' => 'SKU-002',
        'price' => 10000,
        'hpp' => 6500,
    ]);
    expect($p2->margin_tier['label'])->toBe('Tipis');

    // Produk 3: Sehat (45% - 71% margin) -> Price 10.000, HPP 4.000 = Margin 60%
    $p3 = Product::create([
        'category_id' => $cat->id,
        'name' => 'Produk Sehat',
        'sku' => 'SKU-003',
        'price' => 10000,
        'hpp' => 4000,
    ]);
    expect($p3->margin_tier['label'])->toBe('Sehat');

    // Produk 4: Optimal (>= 72% margin) -> Price 10.000, HPP 2.000 = Margin 80%
    $p4 = Product::create([
        'category_id' => $cat->id,
        'name' => 'Produk Optimal',
        'sku' => 'SKU-004',
        'price' => 10000,
        'hpp' => 2000,
    ]);
    expect($p4->margin_tier['label'])->toBe('Optimal');
});

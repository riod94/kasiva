<?php

use App\Livewire\Inventory\MaterialManager;
use App\Models\Material;
use App\Models\InventoryLog;
use App\Models\User;
use App\Services\HppCalculatorService;
use Livewire\Livewire;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

test('restok via MaterialManager membuat inventory_logs dan moving average benar', function () {
    $user = User::factory()->create();
    $mat = Material::create(['name'=>'Kopi Log Test','unit'=>'gram','current_stock'=>10,'min_stock'=>5,'avg_cost'=>1000]);
    $this->actingAs($user);
    Livewire::test(MaterialManager::class)
        ->call('openRestockModal', $mat->id)
        ->set('restockQuantity', 10)
        ->set('restockTotalCost', 20000)
        ->set('restockNotes','Restok supplier A')
        ->call('processRestock')
        ->assertHasNoErrors();
    $mat->refresh();
    expect($mat->current_stock)->toBe(20.0);
    expect($mat->avg_cost)->toBe(1500.0);
    expect(InventoryLog::where('material_id',$mat->id)->where('type','IN')->count())->toBe(1);
    expect(InventoryLog::where('material_id',$mat->id)->first()->quantity)->toBe(10.0);
});

test('HppCalculatorService recalculateMovingAverage atomic + log', function () {
    $mat = Material::create(['name'=>'Susu Log','unit'=>'ml','current_stock'=>5,'min_stock'=>2,'avg_cost'=>2000]);
    $svc = new HppCalculatorService();
    $avg = $svc->recalculateMovingAverage($mat, 5, 3000, 'Test notes');
    expect($avg)->toBe(2500.0);
    expect(InventoryLog::count())->toBe(1);
    expect(InventoryLog::first()->notes)->toBe('Test notes');
});

test('restoreStockForVoid mencatat ADJUST log', function () {
    $mat = Material::create(['name'=>'Mat Void Log','unit'=>'pcs','current_stock'=>100,'min_stock'=>10,'avg_cost'=>500]);
    $prod = \App\Models\Product::create(['name'=>'Prod Void Log','sku'=>'KSV-LOG-VOID','price'=>10000,'hpp'=>1000,'current_stock'=>10]);
    $prod->materials()->attach($mat->id, ['quantity'=>2]);
    $mat->decrement('current_stock', 4);
    $prod->decrement('current_stock', 2);
    $svc = new HppCalculatorService();
    $svc->restoreStockForVoid($prod, 2);
    expect($mat->fresh()->current_stock)->toBe(100.0);
    expect(InventoryLog::where('type','ADJUST')->count())->toBe(1);
});

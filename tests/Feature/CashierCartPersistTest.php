<?php

use App\Livewire\Pos\CashierScreen;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('cashier cart persist di session dan survive mount ulang', function () {
    $user = User::factory()->create();
    $cat = Category::create(['name' => 'Test']);
    $prod = Product::create(['category_id' => $cat->id, 'name' => 'Teh', 'sku' => 'TEH-01', 'price' => 10000, 'hpp' => 4000, 'current_stock' => 50, 'is_active' => true]);
    $this->actingAs($user);

    $comp = Livewire::test(CashierScreen::class);
    $comp->call('addToCart', $prod->id);
    expect($comp->get('cart'))->not->toBeEmpty();

    // simulasi request baru — session should keep carts
    $comp2 = Livewire::test(CashierScreen::class);
    expect($comp2->get('cart'))->not->toBeEmpty();
    expect($comp2->get('carts'))->toHaveCount(1);
});

test('cashier multi-cart 3 slot dan switch', function () {
    $user = User::factory()->create();
    $cat = Category::create(['name' => 'Test']);
    $p1 = Product::create(['category_id' => $cat->id, 'name' => 'Kopi', 'sku' => 'KPI-01', 'price' => 20000, 'hpp' => 8000, 'current_stock' => 50, 'is_active' => true]);
    $p2 = Product::create(['category_id' => $cat->id, 'name' => 'Teh', 'sku' => 'TEH-02', 'price' => 10000, 'hpp' => 4000, 'current_stock' => 50, 'is_active' => true]);
    $this->actingAs($user);

    $comp = Livewire::test(CashierScreen::class);
    $comp->call('addToCart', $p1->id);
    $comp->call('createNewCart');
    expect($comp->get('carts'))->toHaveCount(2);
    expect($comp->get('activeCartIndex'))->toBe(1);
    expect($comp->get('cart'))->toBeEmpty();

    $comp->call('addToCart', $p2->id);
    $comp->call('switchCart', 0);
    expect($comp->get('cart'))->not->toBeEmpty();

    $comp->call('createNewCart');
    expect($comp->get('carts'))->toHaveCount(3);
    // max 3
    $comp->call('createNewCart');
    expect($comp->get('carts'))->toHaveCount(3);
});

<?php

use App\Livewire\Pos\CashierScreen;
use App\Models\Category;
use App\Models\Material;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('komponen cashier screen dapat di-render dengan sukses setelah login', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/pos')
        ->assertStatus(200)
        ->assertSee('Kasiva POS');
});

test('kasir dapat menambah produk ke keranjang dan melakukan checkout tunai', function () {
    $user = User::factory()->create();
    $cat = Category::create(['name' => 'Kopi', 'order_index' => 1]);

    $beans = Material::create([
        'name' => 'Biji Kopi',
        'unit' => 'gram',
        'current_stock' => 1000,
        'avg_cost' => 200,
    ]);

    $product = Product::create([
        'category_id' => $cat->id,
        'name' => 'Kopi Aren Kasiva',
        'sku' => 'KSV-KOP-001',
        'price' => 18000,
        'hpp' => 3600,
        'current_stock' => 50,
    ]);

    $product->materials()->attach([$beans->id => ['quantity' => 18]]);

    $this->actingAs($user);

    Livewire::test(CashierScreen::class)
        ->call('addToCart', $product->id)
        ->assertSet('totalAmount', 18000.0)
        ->call('openCheckoutModal')
        ->set('paidAmount', 20000.0)
        ->call('processCheckout')
        ->assertSet('showReceiptModal', true);

    expect(Transaction::count())->toBe(1);

    $tx = Transaction::first();
    expect($tx->receipt_number)->toContain('KSV-');
    expect($tx->total_amount)->toBe(18000.0);
    expect($tx->paid_amount)->toBe(20000.0);
    expect($tx->change_amount)->toBe(2000.0);

    // Verify stock deduction
    expect($product->fresh()->current_stock)->toBe(49.0);
    expect($beans->fresh()->current_stock)->toBe(982.0); // 1000 - 18 = 982
});

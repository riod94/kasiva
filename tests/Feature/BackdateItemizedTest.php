<?php

use App\Livewire\History\BackdateTransaction;
use App\Models\Category;
use App\Models\Material;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('backdate itemized via cart menghitung HPP dan membuat transaction_items per produk', function () {
    $user = User::factory()->create();
    $cat = Category::create(['name' => 'Test Cat', 'icon' => '🧪']);
    $p1 = Product::create(['name' => 'Backdate Item A', 'sku' => 'KSV-BACK-A', 'price' => 12000, 'hpp' => 5000, 'current_stock' => 100, 'category_id' => $cat->id]);
    $p2 = Product::create(['name' => 'Backdate Item B', 'sku' => 'KSV-BACK-B', 'price' => 8000, 'hpp' => 3000, 'current_stock' => 100, 'category_id' => $cat->id]);
    $this->actingAs($user);
    $comp = Livewire::test(BackdateTransaction::class)
        ->call('addToCart', $p1->id)
        ->call('addToCart', $p2->id)
        ->call('addToCart', $p1->id); // qty p1 =2, p2=1
    expect($comp->get('cart'))->toHaveCount(2);
    expect($comp->get('totalAmount'))->toBe(32000.0);
    expect($comp->get('totalHpp'))->toBe(13000.0);

    $comp->set('transactionDate', now()->subDays(2)->toDateString())
        ->set('transactionTime', '09:30')
        ->set('paymentMethod', 'CASH')
        ->call('saveTransaction');

    $tx = Transaction::where('is_backdated', true)->first();
    expect($tx)->not->toBeNull();
    expect((float) $tx->total_amount)->toBe(32000.0);
    expect((float) $tx->total_hpp)->toBe(13000.0);
    expect($tx->items)->toHaveCount(2);
    // Not affecting stock by default
    expect($p1->fresh()->current_stock)->toBe(100.0);
});

test('backdate dengan affectStock true mengurangi stok bahan baku', function () {
    $user = User::factory()->create();
    $mat = Material::create(['name' => 'Biji Backdate', 'unit' => 'gram', 'current_stock' => 500, 'min_stock' => 10, 'avg_cost' => 10]);
    $p = Product::create(['name' => 'Backdate Stock Prod', 'sku' => 'KSV-BACK-S', 'price' => 10000, 'hpp' => 100, 'current_stock' => 20]);
    $p->materials()->attach($mat->id, ['quantity' => 10]);
    $this->actingAs($user);
    Livewire::test(BackdateTransaction::class)
        ->call('addToCart', $p->id)
        ->set('affectStock', true)
        ->set('transactionDate', now()->subDays(1)->toDateString())
        ->set('transactionTime', '10:00')
        ->call('saveTransaction');
    expect($mat->fresh()->current_stock)->toBe(490.0);
});

test('backdate validasi cart tidak boleh kosong', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    Livewire::test(BackdateTransaction::class)
        ->set('transactionDate', now()->toDateString())
        ->call('saveTransaction')
        ->assertHasErrors(['cart']);
});

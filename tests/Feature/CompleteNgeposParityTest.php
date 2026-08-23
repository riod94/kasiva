<?php

use App\Livewire\History\BackdateTransaction;
use App\Livewire\Pos\CashierScreen;
use App\Livewire\Settings\PaymentSettings;
use App\Livewire\Settings\ProductManager;
use App\Models\Bundle;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Material;
use App\Models\PaymentSetting;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Promotion;
use App\Models\LoyaltyMember;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Role;
use App\Models\Outlet;
use Database\Seeders\KasivaProductionSeeder;
use Livewire\Livewire;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

test('tamu belum login otomatis di redirect ke login saat mengakses rute terproteksi', function () {
    $this->get('/pos')->assertRedirect('/login');
    $this->get('/inventory')->assertRedirect('/login');
    $this->get('/marketing')->assertRedirect('/login');
    $this->get('/history')->assertRedirect('/login');
    $this->get('/history/backdate')->assertRedirect('/login');
    $this->get('/expenses')->assertRedirect('/login');
    $this->get('/reports')->assertRedirect('/login');
    $this->get('/settings')->assertRedirect('/login');
    $this->get('/profile')->assertRedirect('/login');
});

test('pengguna terautentikasi dapat mengakses seluruh rute inventaris', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/inventory')->assertStatus(200);
    $this->actingAs($user)->get('/inventory/categories')->assertStatus(200);
    $this->actingAs($user)->get('/inventory/materials')->assertStatus(200);
    $this->actingAs($user)->get('/inventory/variations')->assertStatus(200);
    $this->actingAs($user)->get('/inventory/products')->assertStatus(200);
});

test('pengguna terautentikasi dapat mengakses seluruh rute marketing dan loyalitas lengkap', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/marketing')->assertStatus(200);
    $this->actingAs($user)->get('/marketing/members')->assertStatus(200);
    $this->actingAs($user)->get('/marketing/loyalty')->assertStatus(200);
    $this->actingAs($user)->get('/marketing/bundles')->assertStatus(200);
    $this->actingAs($user)->get('/marketing/discounts')->assertStatus(200);
    $this->actingAs($user)->get('/marketing/campaigns')->assertStatus(200);
});

test('pengguna terautentikasi dapat mengakses seluruh rute settings dan profil lengkap', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/settings')->assertStatus(200);
    $this->actingAs($user)->get('/settings/outlet')->assertStatus(200);
    $this->actingAs($user)->get('/settings/receipt')->assertStatus(200);
    $this->actingAs($user)->get('/settings/payment')->assertStatus(200);
    $this->actingAs($user)->get('/settings/staff')->assertStatus(200);
    $this->actingAs($user)->get('/settings/roles')->assertStatus(200);
    $this->actingAs($user)->get('/profile')->assertStatus(200);
});

test('halaman legal publik about, privacy, dan terms dapat diakses tanpa login', function () {
    $this->get('/about')->assertStatus(200);
    $this->get('/privacy')->assertStatus(200);
    $this->get('/terms')->assertStatus(200);
});

test('kasiva production seeder memuat persis 5 kategori, 28 bahan baku, dan 19 produk resmi ngepos', function () {
    $this->seed(KasivaProductionSeeder::class);

    // 1. Verifikasi 5 Kategori
    expect(Category::count())->toBe(5);
    expect(Category::pluck('name')->toArray())->toContain(
        'Matcha',
        'Minuman 5.000',
        'Minuman 7.000',
        'Cendol',
        'Kopi'
    );

    // 2. Verifikasi 28 Bahan Baku
    expect(Material::count())->toBe(28);
    expect(Material::where('name', 'Hays Matcha')->first()->avg_cost)->toEqual(1040.0);
    expect(Material::where('name', 'Susu UHT')->first()->avg_cost)->toEqual(22.0);
    expect(Material::where('name', 'Gula Aren')->first()->avg_cost)->toEqual(25.0);

    // 3. Verifikasi 19 Produk Resmi & Resep HPP
    expect(Product::count())->toBe(19);

    $matchaOriginal = Product::where('name', 'Matcha Original')->first();
    expect($matchaOriginal)->not->toBeNull();
    expect((float)$matchaOriginal->price)->toBe(10000.0);
    expect((float)$matchaOriginal->hpp)->toBe(3785.0);
    expect($matchaOriginal->materials()->count())->toBe(7);

    $matchaLatte = Product::where('name', 'Matcha Latte')->first();
    expect($matchaLatte)->not->toBeNull();
    expect((float)$matchaLatte->price)->toBe(12000.0);
    expect((float)$matchaLatte->hpp)->toBe(7250.0);

    $kopiAren = Product::where('name', 'Kopi Susu Gula Aren')->first();
    expect($kopiAren)->not->toBeNull();
    expect((float)$kopiAren->price)->toBe(12000.0);
    expect((float)$kopiAren->hpp)->toBe(7205.0);

    // 4. Verifikasi Varian Default
    expect($matchaOriginal->variants()->count())->toBe(2);
    expect($matchaOriginal->variants()->pluck('name')->toArray())->toContain('Level Gula', 'Level Es');
});

test('product manager livewire component mendukung CRUD lengkap dan kalkulasi resep HPP', function () {
    $user = User::factory()->create();
    $mat = Material::create(['name' => 'Susu UHT Fresh', 'unit' => 'ml', 'avg_cost' => 20, 'current_stock' => 1000]);

    Livewire::actingAs($user)
        ->test(ProductManager::class)
        ->call('openCreateModal')
        ->set('name', 'Kopi Susu Spesial')
        ->set('sku', 'KSV-TEST-001')
        ->set('price', 15000)
        ->set('selectedMaterials', [
            ['material_id' => $mat->id, 'quantity' => 100]
        ])
        ->call('calculateHpp')
        ->assertSet('hpp', 2000.0)
        ->call('saveProduct');

    $product = Product::where('sku', 'KSV-TEST-001')->first();
    expect($product)->not->toBeNull();
    expect($product->name)->toBe('Kopi Susu Spesial');
    expect((float)$product->hpp)->toBe(2000.0);

    // Edit Product
    Livewire::actingAs($user)
        ->test(ProductManager::class)
        ->call('openEditModal', $product->id)
        ->set('price', 18000)
        ->call('saveProduct');

    expect((float)$product->fresh()->price)->toBe(18000.0);

    // Toggle Active
    Livewire::actingAs($user)
        ->test(ProductManager::class)
        ->call('toggleActive', $product->id);

    expect($product->fresh()->is_active)->toBeFalse();

    // Delete Product
    Livewire::actingAs($user)
        ->test(ProductManager::class)
        ->call('deleteProduct', $product->id);

    expect(Product::where('id', $product->id)->exists())->toBeFalse();
});

test('payment settings livewire component mendukung toggle kanal pihak ketiga', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PaymentSettings::class)
        ->call('togglePlatform', 'enable_gofood')
        ->assertSet('enable_gofood', false);

    expect(PaymentSetting::getValue('enable_gofood'))->toBe('false');
});

test('cashier screen mendukung platform adjustment untuk input net received amount', function () {
    $user = User::factory()->create();
    $p = Product::create([
        'name' => 'Matcha Latte Box',
        'price' => 20000,
        'hpp' => 8000,
        'sku' => 'KSV-MTC-BOX',
        'current_stock' => 100,
        'is_active' => true,
    ]);

    Livewire::actingAs($user)
        ->test(CashierScreen::class)
        ->call('addToCart', $p->id)
        ->call('openCheckoutModal')
        ->call('selectPlatformMethod', 'GOFOOD')
        ->assertSet('checkoutStep', 'PLATFORM_ADJUSTMENT')
        ->assertSet('totalAmount', 20000.0)
        // Kasir memasukkan nominal net received (misal setelah potongan 20% komisi = Rp 16.000)
        ->set('adjustedAmount', 16000.0)
        ->call('processCheckout')
        ->assertSet('showReceiptModal', true);

    $tx = Transaction::latest()->first();
    expect($tx)->not->toBeNull();
    expect($tx->payment_method)->toBe('GOFOOD');
    expect((float)$tx->total_amount)->toBe(16000.0);
});

test('backdate transaction mencatat transaksi manual masa lalu', function () {
    $user = User::factory()->create();
    $cat = Category::firstOrCreate(['name'=>'Test Backdate Cat'], ['icon'=>'🧪']);
    $prod = Product::firstOrCreate(['name'=>'Backdate Parity Prod'], ['sku'=>'KSV-BACK-PARITY','price'=>75000,'hpp'=>30000,'current_stock'=>50,'category_id'=>$cat->id,'is_active'=>true]);

    Livewire::actingAs($user)
        ->test(BackdateTransaction::class)
        ->call('addToCart', $prod->id)
        ->set('transactionDate', '2026-08-01')
        ->set('transactionTime', '14:30')
        ->set('paymentMethod', 'QRIS')
        ->call('saveTransaction');

    $tx = Transaction::whereDate('transaction_date', '2026-08-01')->orWhereDate('created_at','2026-08-01')->first();
    expect($tx)->not->toBeNull();
    expect((float)$tx->total_amount)->toBe(75000.0);
    expect($tx->payment_method)->toBe('QRIS');
    expect($tx->is_backdated)->toBeTrue();
});

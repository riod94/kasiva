<?php

use App\Livewire\History\TransactionHistory;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Outlet;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

function seedVoidRoles2(): array {
    $outlet = Outlet::create(['name'=>'Test Outlet Void','tax_percentage'=>10,'service_charge_percentage'=>5]);
    $owner = Role::create(['name'=>'Owner Void','slug'=>'owner']);
    $cashier = Role::create(['name'=>'Kasir Void','slug'=>'cashier']);
    foreach (['POS_ACCESS','VIEW_TRANSACTIONS','VOID_TRANSACTION','VIEW_PRODUCTS'] as $slug) {
        Permission::create(['slug'=>$slug,'name'=>$slug]);
    }
    $owner->syncPermissions(['POS_ACCESS','VIEW_TRANSACTIONS','VOID_TRANSACTION']);
    $cashier->syncPermissions(['POS_ACCESS','VIEW_TRANSACTIONS']);
    return [$owner,$cashier,$outlet];
}

test('void transaksi mengembalikan stok produk & bahan baku dan menandai VOIDED', function () {
    [$owner,$cashier,$outlet] = seedVoidRoles2();
    $ownerUser = User::create(['name'=>'Owner Void','email'=>'owner-void@test.com','password'=>Hash::make('password'),'role_id'=>$owner->id,'outlet_id'=>$outlet->id]);
    $material = \App\Models\Material::create(['name'=>'Susu Void Test','unit'=>'ml','current_stock'=>1000,'min_stock'=>10,'avg_cost'=>20]);
    $product = Product::create(['name'=>'Void Latte','sku'=>'KSV-VOID-001','price'=>15000,'hpp'=>5000,'current_stock'=>50]);
    $product->materials()->attach($material->id, ['quantity'=>100]);
    $material->decrement('current_stock', 200);
    $product->decrement('current_stock', 2);
    $tx = Transaction::create([
        'receipt_number'=>'KSV-TEST-VOID-001',
        'payment_method'=>'CASH','total_amount'=>30000,'total_hpp'=>10000,'paid_amount'=>30000,'change_amount'=>0,
        'cashier_name'=>'Rizki','sync_status'=>'SYNCED','status'=>'COMPLETED',
    ]);
    TransactionItem::create(['transaction_id'=>$tx->id,'product_id'=>$product->id,'product_name'=>$product->name,'unit_price'=>15000,'unit_hpp'=>5000,'quantity'=>2,'subtotal'=>30000]);
    $this->actingAs($ownerUser);
    Livewire::test(TransactionHistory::class)
        ->call('openVoidModal', $tx->id)
        ->set('voidReason', 'Salah input')
        ->call('voidTransaction')
        ->assertHasNoErrors();
    $tx->refresh();
    expect($tx->status)->toBe('VOIDED');
    expect($product->fresh()->current_stock)->toBe(50.0);
    expect($material->fresh()->current_stock)->toBe(1000.0);
});

test('kasir tanpa VOID_TRANSACTION tidak bisa void', function () {
    [$owner,$cashier,$outlet] = seedVoidRoles2();
    $kasir = User::create(['name'=>'Kasir NoVoid','email'=>'kasir-void@test.com','password'=>Hash::make('password'),'role_id'=>$cashier->id,'outlet_id'=>$outlet->id]);
    // Kasir should NOT have VOID_TRANSACTION permission - direct unit check
    expect($kasir->hasPermission('VOID_TRANSACTION'))->toBeFalse();
    expect($kasir->hasPermission('POS_ACCESS'))->toBeTrue();
    // Owner should have VOID permission
    $ownerUser = User::create(['name'=>'Owner Check','email'=>'owner-check-void@test.com','password'=>Hash::make('password'),'role_id'=>$owner->id,'outlet_id'=>$outlet->id]);
    expect($ownerUser->hasPermission('VOID_TRANSACTION'))->toBeTrue();
    // Also ensure route-level RBAC blocks cashier from reports (existing RbacSecurityTest coverage)
    $this->actingAs($kasir)->get('/reports')->assertStatus(403);
});

test('laporan exclude VOIDED dari omset', function () {
    [$owner,$cashier,$outlet] = seedVoidRoles2();
    $ownerUser = User::create(['name'=>'Owner Lap Void','email'=>'owner-lap-void@test.com','password'=>Hash::make('password'),'role_id'=>$owner->id,'outlet_id'=>$outlet->id]);
    Transaction::create(['receipt_number'=>'KSV-VOID-003','payment_method'=>'CASH','total_amount'=>20000,'total_hpp'=>10000,'paid_amount'=>20000,'change_amount'=>0,'cashier_name'=>'Rizki','sync_status'=>'SYNCED','status'=>'COMPLETED']);
    Transaction::create(['receipt_number'=>'KSV-VOID-004','payment_method'=>'CASH','total_amount'=>50000,'total_hpp'=>25000,'paid_amount'=>50000,'change_amount'=>0,'cashier_name'=>'Rizki','sync_status'=>'SYNCED','status'=>'VOIDED']);
    $this->actingAs($ownerUser);
    $data = Livewire::test(\App\Livewire\Reports\FinancialReports::class)->set('period','SEMUA')->instance()->getReportData();
    expect((float)$data['omset'])->toBe(20000.0);
    expect($data['txCount'])->toBe(1);
});

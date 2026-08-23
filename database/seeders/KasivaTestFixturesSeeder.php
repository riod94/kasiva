<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\LoyaltyMember;
use App\Models\LoyaltyProgram;
use App\Models\Material;
use App\Models\Outlet;
use App\Models\PaymentSetting;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VariantOption;
use App\Models\VariantTemplate;
use App\Models\VariantTemplateOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KasivaTestFixturesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Outlet
        $outlet = Outlet::firstOrCreate(
            ['name' => 'Kasiva Test Outlet'],
            [
                'address' => 'Jl. Test No. 1',
                'phone' => '0812-test',
                'tax_percentage' => 11.0,
                'service_charge_percentage' => 5.0,
            ]
        );

        // 2. Roles
        $roleOwner = Role::firstOrCreate(['slug' => 'owner'], ['name' => 'Owner']);
        $roleCashier = Role::firstOrCreate(['slug' => 'cashier'], ['name' => 'Cashier']);

        // 3. Users
        User::firstOrCreate(
            ['email' => 'test-owner@kasiva.pos'],
            [
                'name' => 'Test Owner',
                'password' => Hash::make('password'),
                'role_id' => $roleOwner->id,
                'outlet_id' => $outlet->id,
                'pin' => '123456',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'test-cashier@kasiva.pos'],
            [
                'name' => 'Test Cashier',
                'password' => Hash::make('password'),
                'role_id' => $roleCashier->id,
                'outlet_id' => $outlet->id,
                'pin' => '111111',
                'phone' => '081987654321',
                'is_active' => true,
            ]
        );

        // 4. Payment Settings
        PaymentSetting::setValue('qris_merchant_name', 'Kasiva Test');
        PaymentSetting::setValue('qris_image', '/images/kasiva-logo-full.png');
        PaymentSetting::setValue('enable_gofood', 'false');
        PaymentSetting::setValue('enable_grabfood', 'false');

        // 5. Categories
        $catBeverage = Category::firstOrCreate(['name' => 'Minuman'], ['icon' => '🥤', 'order_index' => 1]);
        $catFood = Category::firstOrCreate(['name' => 'Makanan'], ['icon' => '🍴', 'order_index' => 2]);

        // 6. Materials
        $matCoffee = Material::firstOrCreate(
            ['name' => 'Kopi Hitam'],
            ['unit' => 'gram', 'current_stock' => 1000, 'min_stock' => 100, 'avg_cost' => 50.0, 'is_active' => true]
        );
        $matMilk = Material::firstOrCreate(
            ['name' => 'Susu UHT'],
            ['unit' => 'ml', 'current_stock' => 5000, 'min_stock' => 500, 'avg_cost' => 22.0, 'is_active' => true]
        );
        $matSugar = Material::firstOrCreate(
            ['name' => 'Gula Cair'],
            ['unit' => 'ml', 'current_stock' => 2000, 'min_stock' => 200, 'avg_cost' => 20.0, 'is_active' => true]
        );
        $matCup = Material::firstOrCreate(
            ['name' => 'Cup 12oz'],
            ['unit' => 'pcs', 'current_stock' => 500, 'min_stock' => 50, 'avg_cost' => 500.0, 'is_active' => true]
        );

        // 7. Products with recipes
        $coffee = Product::firstOrCreate(
            ['name' => 'Espresso'],
            [
                'category_id' => $catBeverage->id,
                'sku' => 'KSV-TEST-001',
                'price' => 10000.0,
                'hpp' => 1500.0,
                'current_stock' => 100,
                'is_active' => true,
            ]
        );
        $coffee->materials()->sync([
            $matCoffee->id => ['quantity' => 10],
            $matCup->id => ['quantity' => 1],
        ]);

        $milkTea = Product::firstOrCreate(
            ['name' => 'Milk Tea'],
            [
                'category_id' => $catBeverage->id,
                'sku' => 'KSV-TEST-002',
                'price' => 12000.0,
                'hpp' => 3000.0,
                'current_stock' => 100,
                'is_active' => true,
            ]
        );
        $milkTea->materials()->sync([
            $matMilk->id => ['quantity' => 200],
            $matSugar->id => ['quantity' => 20],
            $matCup->id => ['quantity' => 1],
        ]);

        // 8. Loyalty Member
        LoyaltyMember::firstOrCreate(
            ['phone' => '081299998888'],
            [
                'name' => 'Test Member',
                'qr_code' => 'KSV-MBR-TEST01',
                'status' => 'ASSIGNED',
                'stamps_count' => 3,
                'total_visits' => 5,
            ]
        );

        // 9. Loyalty Program
        LoyaltyProgram::firstOrCreate(
            ['name' => '10 Stempel Gratis 1 Minuman'],
            [
                'target_stamps' => 10,
                'min_transaction' => 0,
                'expiry_months' => 12,
                'is_active' => true,
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use App\Models\Bundle;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Expense;
use App\Models\LoyaltyMember;
use App\Models\LoyaltyProgram;
use App\Models\LoyaltyReward;
use App\Models\Material;
use App\Models\Outlet;
use App\Models\PaymentSetting;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Promotion;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\VariantOption;
use App\Models\VariantTemplate;
use App\Models\VariantTemplateOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KasivaProductionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Outlet Standar Utama
        $outlet = Outlet::firstOrCreate(
            ['name' => 'Kasiva Coffee & Eatery — Standar Utama'],
            [
                'address' => 'Jl. Slamet Riyadi No. 124, Surakarta',
                'phone' => '0812-3456-7890',
                'tax_percentage' => 10.0,
                'service_charge_percentage' => 5.0,
            ]
        );

        // 2. Roles & Permissions (RBAC)
        $roleOwner = Role::firstOrCreate(
            ['slug' => 'owner'],
            ['name' => 'Owner / Super Admin', 'description' => 'Akses penuh seluruh modul sistem']
        );
        $roleManager = Role::firstOrCreate(
            ['slug' => 'manager'],
            ['name' => 'Manager Outlet', 'description' => 'Akses kelola produk, stok, dan laporan operasional']
        );
        $roleCashier = Role::firstOrCreate(
            ['slug' => 'cashier'],
            ['name' => 'Staf Kasir', 'description' => 'Akses kasir POS & transaksi penjualan']
        );

        $permissionsList = [
            ['slug' => 'POS_ACCESS', 'name' => 'Akses Kasir POS'],
            ['slug' => 'VIEW_TRANSACTIONS', 'name' => 'Lihat Riwayat Transaksi'],
            ['slug' => 'VOID_TRANSACTION', 'name' => 'Batalkan / Void Transaksi'],
            ['slug' => 'VIEW_PRODUCTS', 'name' => 'Lihat Katalog Produk'],
            ['slug' => 'MANAGE_PRODUCTS', 'name' => 'Kelola Produk & Varian'],
            ['slug' => 'VIEW_MATERIALS', 'name' => 'Lihat Stok Bahan Baku'],
            ['slug' => 'MANAGE_MATERIALS', 'name' => 'Kelola Bahan Baku & Restok'],
            ['slug' => 'MANAGE_CATEGORIES', 'name' => 'Kelola Kategori Produk'],
            ['slug' => 'MANAGE_PROMOS', 'name' => 'Kelola Diskon & Promo'],
            ['slug' => 'VIEW_MEMBERS', 'name' => 'Lihat Database Pelanggan'],
            ['slug' => 'MANAGE_MEMBERS', 'name' => 'Kelola Data Pelanggan'],
            ['slug' => 'MANAGE_LOYALTY', 'name' => 'Kelola Program Loyalitas'],
            ['slug' => 'VIEW_REPORTS', 'name' => 'Lihat Laporan Keuangan & HPP'],
            ['slug' => 'MANAGE_EXPENSES', 'name' => 'Kelola Biaya Pengeluaran'],
            ['slug' => 'MANAGE_OUTLET', 'name' => 'Kelola Informasi Outlet'],
            ['slug' => 'MANAGE_PRINTER', 'name' => 'Pengaturan Struk & Printer'],
            ['slug' => 'MANAGE_PAYMENTS', 'name' => 'Pengaturan Metode Pembayaran'],
            ['slug' => 'MANAGE_STAFF', 'name' => 'Kelola Staf & PIN Kasir'],
            ['slug' => 'MANAGE_ROLES', 'name' => 'Kelola Peran & Hak Akses'],
        ];

        foreach ($permissionsList as $permData) {
            Permission::firstOrCreate(['slug' => $permData['slug']], ['name' => $permData['name']]);
        }

        // Sync Default Role Permissions
        $allPermSlugs = array_column($permissionsList, 'slug');
        $roleOwner->syncPermissions($allPermSlugs);

        $roleManager->syncPermissions([
            'POS_ACCESS',
            'VIEW_TRANSACTIONS',
            'VIEW_PRODUCTS',
            'MANAGE_PRODUCTS',
            'VIEW_MATERIALS',
            'MANAGE_MATERIALS',
            'MANAGE_CATEGORIES',
            'MANAGE_PROMOS',
            'VIEW_MEMBERS',
            'MANAGE_MEMBERS',
            'MANAGE_LOYALTY',
            'VIEW_REPORTS',
            'MANAGE_EXPENSES',
            'MANAGE_PRINTER',
            'MANAGE_PAYMENTS',
        ]);

        $roleCashier->syncPermissions([
            'POS_ACCESS',
            'VIEW_TRANSACTIONS',
        ]);

        // 3. Users / Staff
        User::firstOrCreate(
            ['email' => 'owner@kasiva.pos'],
            [
                'name' => 'Maya Pratama (Owner)',
                'password' => Hash::make('password123'),
                'role_id' => $roleOwner->id,
                'outlet_id' => $outlet->id,
                'pin' => '123456',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'kasir@kasiva.pos'],
            [
                'name' => 'Rizki Kasir Utama',
                'password' => Hash::make('password123'),
                'role_id' => $roleCashier->id,
                'outlet_id' => $outlet->id,
                'pin' => '111111',
                'phone' => '081987654321',
                'is_active' => true,
            ]
        );

        // 4. Payment & Receipt Settings
        PaymentSetting::setValue('qris_merchant_name', 'KASIVA COFFEE & EATERY');
        PaymentSetting::setValue('qris_nmid', 'ID1020030040050');
        PaymentSetting::setValue('qris_image', '/images/kasiva-logo-full.png');
        PaymentSetting::setValue('gofood_markup_percent', '20');
        PaymentSetting::setValue('grabfood_markup_percent', '20');
        PaymentSetting::setValue('shopeefood_markup_percent', '20');
        PaymentSetting::setValue('receipt_show_logo', 'true');
        PaymentSetting::setValue('receipt_footer_text', "— TERIMA KASIH —\nFollow IG: @kasiva.pos\nWiFi Password: kasivakopi");

        // 5. Official 5 Categories (Identik Ngepos)
        $categoriesData = [
            'cat_matcha' => ['name' => 'Matcha', 'icon' => '🍵', 'order_index' => 1],
            'cat_minuman_5k' => ['name' => 'Minuman 5.000', 'icon' => '🥤', 'order_index' => 2],
            'cat_minuman_7k' => ['name' => 'Minuman 7.000', 'icon' => '☕', 'order_index' => 3],
            'cat_cendol' => ['name' => 'Cendol', 'icon' => '🧊', 'order_index' => 4],
            'cat_kopi' => ['name' => 'Kopi', 'icon' => '☕', 'order_index' => 5],
        ];

        $categories = [];
        foreach ($categoriesData as $key => $cat) {
            $categories[$key] = Category::firstOrCreate(
                ['name' => $cat['name']],
                ['icon' => $cat['icon'], 'order_index' => $cat['order_index']]
            );
        }

        // 6. Official 28 Raw Materials (Identik Ngepos)
        $materialsData = [
            'mat_hays_matcha' => ['name' => 'Hays Matcha', 'unit' => 'gram', 'cost' => 1040.0, 'stock' => 1000],
            'mat_nsw_matcha' => ['name' => 'NSW Matcha', 'unit' => 'gram', 'cost' => 75.0, 'stock' => 1000],
            'mat_hays_matcha_powder' => ['name' => 'Hays Matcha Powder', 'unit' => 'gram', 'cost' => 1120.0, 'stock' => 1000],
            'mat_matcha_crumble' => ['name' => 'Matcha Crumble', 'unit' => 'gram', 'cost' => 87.0, 'stock' => 1000],
            'mat_oreo_crumble' => ['name' => 'Oreo Crumble', 'unit' => 'gram', 'cost' => 55.0, 'stock' => 1000],
            'mat_taro_powder' => ['name' => 'Taro Powder', 'unit' => 'gram', 'cost' => 280.0, 'stock' => 1000],
            'mat_bubuk_frezzo' => ['name' => 'Bubuk Frezzo', 'unit' => 'gram', 'cost' => 60.0, 'stock' => 5000],
            'mat_skm_coklat' => ['name' => 'SKM Coklat', 'unit' => 'ml', 'cost' => 30.5, 'stock' => 2000],
            'mat_kopi_instant' => ['name' => 'Kopi Instant', 'unit' => 'gram', 'cost' => 1000.0, 'stock' => 1000],
            'mat_susu_uht' => ['name' => 'Susu UHT', 'unit' => 'ml', 'cost' => 22.0, 'stock' => 20000],
            'mat_gula_cair' => ['name' => 'Gula Cair', 'unit' => 'ml', 'cost' => 20.0, 'stock' => 10000],
            'mat_gula_aren' => ['name' => 'Gula Aren', 'unit' => 'ml', 'cost' => 25.0, 'stock' => 10000],
            'mat_strawberry_jam' => ['name' => 'Strawberry Jam', 'unit' => 'gram', 'cost' => 65.0, 'stock' => 2000],
            'mat_cream_cheese' => ['name' => 'Cream Cheese', 'unit' => 'ml', 'cost' => 65.0, 'stock' => 2000],
            'mat_krimer_cair' => ['name' => 'Krimer Cair', 'unit' => 'ml', 'cost' => 6.67, 'stock' => 10000],
            'mat_cendol_hijau' => ['name' => 'Cendol Hijau', 'unit' => 'gram', 'cost' => 35.71, 'stock' => 5000],
            'mat_cendol_pink' => ['name' => 'Cendol Pink', 'unit' => 'gram', 'cost' => 30.0, 'stock' => 5000],
            'mat_cendol_orange' => ['name' => 'Cendol Orange', 'unit' => 'gram', 'cost' => 22.86, 'stock' => 5000],
            'mat_cincau' => ['name' => 'Cincau', 'unit' => 'serving', 'cost' => 200.0, 'stock' => 500],
            'mat_sirup_pink' => ['name' => 'Sirup Pink', 'unit' => 'ml', 'cost' => 50.0, 'stock' => 3000],
            'mat_sirup_blue' => ['name' => 'Sirup Blue', 'unit' => 'ml', 'cost' => 38.0, 'stock' => 3000],
            'mat_cup_matcha_14_full' => ['name' => 'Cup 14oz + stiker + kertas + tutup', 'unit' => 'pack', 'cost' => 1350.0, 'stock' => 1000],
            'mat_cup_matcha_14_alas' => ['name' => 'Cup 14oz + stiker + alas', 'unit' => 'pack', 'cost' => 1350.0, 'stock' => 1000],
            'mat_cup_14_plain' => ['name' => 'Cup 14oz + kertas + tutup', 'unit' => 'pack', 'cost' => 850.0, 'stock' => 2000],
            'mat_cup_thinwall_12' => ['name' => 'Cup Thinwall 12oz', 'unit' => 'pack', 'cost' => 800.0, 'stock' => 1000],
            'mat_es_kristal' => ['name' => 'Es Kristal', 'unit' => 'serving', 'cost' => 500.0, 'stock' => 1000],
            'mat_sedotan' => ['name' => 'Sedotan', 'unit' => 'pcs', 'cost' => 100.0, 'stock' => 2000],
            'mat_plastik' => ['name' => 'Plastik', 'unit' => 'pcs', 'cost' => 120.0, 'stock' => 2000],
        ];

        $materials = [];
        foreach ($materialsData as $key => $mat) {
            $materials[$key] = Material::firstOrCreate(
                ['name' => $mat['name']],
                [
                    'unit' => $mat['unit'],
                    'current_stock' => $mat['stock'],
                    'min_stock' => 100,
                    'avg_cost' => $mat['cost'],
                    'is_active' => true,
                ]
            );
        }

        // 7. Official 19 Products & Exact Recipes (Identik Ngepos)
        $productsSpec = [
            // Matcha Series
            [
                'cat' => 'cat_matcha',
                'name' => 'Matcha Original',
                'sku' => 'KSV-MTC-001',
                'price' => 10000.0,
                'hpp' => 3785.0,
                'stock' => 100,
                'recipe' => [
                    'mat_hays_matcha' => 1,
                    'mat_nsw_matcha' => 5,
                    'mat_cup_matcha_14_full' => 1,
                    'mat_gula_cair' => 15,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],
            [
                'cat' => 'cat_matcha',
                'name' => 'Matcha Latte',
                'sku' => 'KSV-MTC-002',
                'price' => 12000.0,
                'hpp' => 7250.0,
                'stock' => 100,
                'recipe' => [
                    'mat_hays_matcha' => 3,
                    'mat_susu_uht' => 80,
                    'mat_cup_matcha_14_full' => 1,
                    'mat_gula_cair' => 15,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],
            [
                'cat' => 'cat_matcha',
                'name' => 'Matcha Strawberry',
                'sku' => 'KSV-MTC-003',
                'price' => 15000.0,
                'hpp' => 8550.0,
                'stock' => 100,
                'recipe' => [
                    'mat_hays_matcha' => 3,
                    'mat_susu_uht' => 80,
                    'mat_cup_matcha_14_full' => 1,
                    'mat_gula_cair' => 15,
                    'mat_strawberry_jam' => 20,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],
            [
                'cat' => 'cat_matcha',
                'name' => 'Matcha Cookies',
                'sku' => 'KSV-MTC-004',
                'price' => 16000.0,
                'hpp' => 9660.0,
                'stock' => 100,
                'recipe' => [
                    'mat_matcha_crumble' => 10,
                    'mat_susu_uht' => 80,
                    'mat_hays_matcha_powder' => 3,
                    'mat_cream_cheese' => 20,
                    'mat_gula_cair' => 15,
                    'mat_cup_matcha_14_alas' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],
            [
                'cat' => 'cat_matcha',
                'name' => 'Matcha Oreo Cookies',
                'sku' => 'KSV-MTC-005',
                'price' => 16000.0,
                'hpp' => 9340.0,
                'stock' => 100,
                'recipe' => [
                    'mat_oreo_crumble' => 10,
                    'mat_susu_uht' => 80,
                    'mat_hays_matcha_powder' => 3,
                    'mat_cream_cheese' => 20,
                    'mat_gula_cair' => 15,
                    'mat_cup_matcha_14_alas' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],
            [
                'cat' => 'cat_matcha',
                'name' => 'Taro Matcha Latte',
                'sku' => 'KSV-MTC-006',
                'price' => 16000.0,
                'hpp' => 8330.0,
                'stock' => 100,
                'recipe' => [
                    'mat_taro_powder' => 3,
                    'mat_susu_uht' => 80,
                    'mat_hays_matcha_powder' => 3,
                    'mat_gula_cair' => 15,
                    'mat_cup_matcha_14_alas' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],
            [
                'cat' => 'cat_matcha',
                'name' => 'Aren Matcha Latte',
                'sku' => 'KSV-MTC-007',
                'price' => 16000.0,
                'hpp' => 7815.0,
                'stock' => 100,
                'recipe' => [
                    'mat_gula_aren' => 25,
                    'mat_susu_uht' => 80,
                    'mat_hays_matcha_powder' => 3,
                    'mat_cup_matcha_14_alas' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],

            // Minuman 5.000 Series
            [
                'cat' => 'cat_minuman_5k',
                'name' => 'Thai Tea',
                'sku' => 'KSV-M5K-001',
                'price' => 5000.0,
                'hpp' => 3370.0,
                'stock' => 100,
                'recipe' => [
                    'mat_bubuk_frezzo' => 30,
                    'mat_cup_14_plain' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],
            [
                'cat' => 'cat_minuman_5k',
                'name' => 'Taro',
                'sku' => 'KSV-M5K-002',
                'price' => 5000.0,
                'hpp' => 3370.0,
                'stock' => 100,
                'recipe' => [
                    'mat_bubuk_frezzo' => 30,
                    'mat_cup_14_plain' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],
            [
                'cat' => 'cat_minuman_5k',
                'name' => 'Green Tea',
                'sku' => 'KSV-M5K-003',
                'price' => 5000.0,
                'hpp' => 3370.0,
                'stock' => 100,
                'recipe' => [
                    'mat_bubuk_frezzo' => 30,
                    'mat_cup_14_plain' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],
            [
                'cat' => 'cat_minuman_5k',
                'name' => 'Lemon Tea',
                'sku' => 'KSV-M5K-004',
                'price' => 5000.0,
                'hpp' => 3370.0,
                'stock' => 100,
                'recipe' => [
                    'mat_bubuk_frezzo' => 30,
                    'mat_cup_14_plain' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],
            [
                'cat' => 'cat_minuman_5k',
                'name' => 'Jasmine Tea',
                'sku' => 'KSV-M5K-005',
                'price' => 5000.0,
                'hpp' => 3370.0,
                'stock' => 100,
                'recipe' => [
                    'mat_bubuk_frezzo' => 30,
                    'mat_cup_14_plain' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],
            [
                'cat' => 'cat_minuman_5k',
                'name' => 'Coklat',
                'sku' => 'KSV-M5K-006',
                'price' => 5000.0,
                'hpp' => 3675.0,
                'stock' => 100,
                'recipe' => [
                    'mat_bubuk_frezzo' => 30,
                    'mat_skm_coklat' => 10,
                    'mat_cup_14_plain' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],

            // Minuman 7.000 Series
            [
                'cat' => 'cat_minuman_7k',
                'name' => 'Cappucino',
                'sku' => 'KSV-M7K-001',
                'price' => 7000.0,
                'hpp' => 4370.0,
                'stock' => 100,
                'recipe' => [
                    'mat_bubuk_frezzo' => 30,
                    'mat_kopi_instant' => 1,
                    'mat_cup_14_plain' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],
            [
                'cat' => 'cat_minuman_7k',
                'name' => 'Moccacino',
                'sku' => 'KSV-M7K-002',
                'price' => 7000.0,
                'hpp' => 4370.0,
                'stock' => 100,
                'recipe' => [
                    'mat_bubuk_frezzo' => 30,
                    'mat_kopi_instant' => 1,
                    'mat_cup_14_plain' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],

            // Cendol Series
            [
                'cat' => 'cat_cendol',
                'name' => 'Es Cendol Kecebong Ori',
                'sku' => 'KSV-CDL-001',
                'price' => 7000.0,
                'hpp' => 5020.0,
                'stock' => 100,
                'recipe' => [
                    'mat_gula_aren' => 50,
                    'mat_krimer_cair' => 120,
                    'mat_cendol_hijau' => 35,
                    'mat_cincau' => 1,
                    'mat_cup_thinwall_12' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],
            [
                'cat' => 'cat_cendol',
                'name' => 'Es Cendol Kecebong Pink',
                'sku' => 'KSV-CDL-002',
                'price' => 7000.0,
                'hpp' => 4820.0,
                'stock' => 100,
                'recipe' => [
                    'mat_sirup_pink' => 25,
                    'mat_krimer_cair' => 120,
                    'mat_cendol_pink' => 35,
                    'mat_cincau' => 1,
                    'mat_cup_thinwall_12' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],
            [
                'cat' => 'cat_cendol',
                'name' => 'Es Cendol Kecebong Blue',
                'sku' => 'KSV-CDL-003',
                'price' => 7000.0,
                'hpp' => 4270.0,
                'stock' => 100,
                'recipe' => [
                    'mat_sirup_blue' => 25,
                    'mat_krimer_cair' => 120,
                    'mat_cendol_orange' => 35,
                    'mat_cincau' => 1,
                    'mat_cup_thinwall_12' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],

            // Kopi Series
            [
                'cat' => 'cat_kopi',
                'name' => 'Kopi Susu Gula Aren',
                'sku' => 'KSV-KOP-001',
                'price' => 12000.0,
                'hpp' => 7205.0,
                'stock' => 100,
                'recipe' => [
                    'mat_kopi_instant' => 3,
                    'mat_gula_aren' => 35,
                    'mat_susu_uht' => 80,
                    'mat_cup_14_plain' => 1,
                    'mat_es_kristal' => 1,
                    'mat_sedotan' => 1,
                    'mat_plastik' => 1,
                ],
            ],
        ];

        $createdProducts = [];
        foreach ($productsSpec as $spec) {
            $cat = $categories[$spec['cat']];
            $prod = Product::firstOrCreate(
                ['name' => $spec['name']],
                [
                    'category_id' => $cat->id,
                    'sku' => $spec['sku'],
                    'price' => $spec['price'],
                    'hpp' => $spec['hpp'],
                    'current_stock' => $spec['stock'],
                    'is_active' => true,
                ]
            );

            // Attach Recipe
            $attachData = [];
            foreach ($spec['recipe'] as $matKey => $qty) {
                if (isset($materials[$matKey])) {
                    $attachData[$materials[$matKey]->id] = ['quantity' => $qty];
                }
            }
            $prod->materials()->sync($attachData);

            // Attach Default Variants (Level Gula & Level Es)
            if ($prod->variants()->count() === 0) {
                $vGula = ProductVariant::create([
                    'product_id' => $prod->id,
                    'name' => 'Level Gula',
                    'selection_type' => 'SINGLE',
                    'is_required' => true,
                    'order_index' => 1,
                ]);
                VariantOption::create(['product_variant_id' => $vGula->id, 'name' => 'Normal', 'price_modifier' => 0]);
                VariantOption::create(['product_variant_id' => $vGula->id, 'name' => 'Less Sugar', 'price_modifier' => 0]);
                VariantOption::create(['product_variant_id' => $vGula->id, 'name' => 'No Sugar', 'price_modifier' => 0]);
                VariantOption::create(['product_variant_id' => $vGula->id, 'name' => 'Extra Sugar', 'price_modifier' => 0]);

                $vEs = ProductVariant::create([
                    'product_id' => $prod->id,
                    'name' => 'Level Es',
                    'selection_type' => 'SINGLE',
                    'is_required' => true,
                    'order_index' => 2,
                ]);
                VariantOption::create(['product_variant_id' => $vEs->id, 'name' => 'Normal', 'price_modifier' => 0]);
                VariantOption::create(['product_variant_id' => $vEs->id, 'name' => 'Less Ice', 'price_modifier' => 0]);
                VariantOption::create(['product_variant_id' => $vEs->id, 'name' => 'No Ice', 'price_modifier' => 0]);
                VariantOption::create(['product_variant_id' => $vEs->id, 'name' => 'Extra Ice', 'price_modifier' => 0]);
            }

            $createdProducts[] = $prod;
        }

        // 8. Sample Bundles & Campaigns
        if ($pMatcha = Product::where('name', 'Matcha Latte')->first()) {
            if ($pKopi = Product::where('name', 'Kopi Susu Gula Aren')->first()) {
                Bundle::firstOrCreate(
                    ['name' => 'Paket Duo Santai (Matcha + Kopi Aren)'],
                    [
                        'price' => 21000.0, // Normal: 12k + 12k = 24k -> Hemat 3k
                        'cogs' => 14455.0,
                        'is_active' => true,
                        'items' => [
                            ['product_id' => $pMatcha->id, 'product_name' => $pMatcha->name, 'quantity' => 1],
                            ['product_id' => $pKopi->id, 'product_name' => $pKopi->name, 'quantity' => 1],
                        ],
                    ]
                );
            }
        }

        Campaign::firstOrCreate(
            ['name' => 'Promo Diskon Opening Outlet 20%'],
            [
                'description' => 'Potongan harga 20% untuk semua varian Matcha Series',
                'type' => 'BUNDLE',
                'is_active' => true,
                'priority' => 1,
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
                'reward_type' => 'PERCENT_DISCOUNT',
                'reward_value' => 20.0,
            ]
        );

        Promotion::firstOrCreate(
            ['name' => 'Voucher Kasiva 10 Ribu'],
            [
                'type' => 'FIXED_AMOUNT',
                'discount_value' => 10000.0,
                'min_purchase' => 50000.0,
                'is_active' => true,
            ]
        );

        // 9. Sample Expenses (7 Kategori Standar)
        $expenseCategories = ['RAW_MATERIAL', 'RENT', 'SALARY', 'UTILITIES', 'MARKETING', 'OPERATIONAL', 'OTHER'];
        Expense::firstOrCreate(
            ['title' => 'Sewa Lokasi Outlet Bulan Ini'],
            ['amount' => 3500000.0, 'category' => 'RENT', 'expense_date' => now()->subDays(5), 'notes' => 'Sewa tempat ruko']
        );
        Expense::firstOrCreate(
            ['title' => 'Tagihan Listrik PLN & Air PDAM'],
            ['amount' => 850000.0, 'category' => 'UTILITIES', 'expense_date' => now()->subDays(3), 'notes' => 'Token listrik & PDAM']
        );
        Expense::firstOrCreate(
            ['title' => 'Restok Susu UHT 20 Liter & Sirup'],
            ['amount' => 450000.0, 'category' => 'RAW_MATERIAL', 'expense_date' => now()->subDays(1), 'notes' => 'Supplier harian']
        );

        // 10. Sample Loyalty Members & Rewards
        $m1 = LoyaltyMember::firstOrCreate(
            ['phone' => '081299998888'],
            ['name' => 'Budi Santoso', 'stamps_count' => 8, 'total_visits' => 12]
        );
        $m2 = LoyaltyMember::firstOrCreate(
            ['phone' => '085711112222'],
            ['name' => 'Siti Rahma', 'stamps_count' => 10, 'total_visits' => 15]
        );

        LoyaltyReward::firstOrCreate(
            ['title' => 'Gratis 1 Menu Bebas Pilihan'],
            ['stamps_required' => 10, 'discount_amount' => 16000.0, 'is_active' => true]
        );

        // 10a. Loyalty Program Default
        if (class_exists(\App\Models\LoyaltyProgram::class)) {
            \App\Models\LoyaltyProgram::firstOrCreate(['name' => '10 Stempel Gratis 1 Menu'], ['target_stamps'=>10,'min_transaction'=>0,'expiry_months'=>12,'is_active'=>true]);
        }

        // 10b. Variant Template Library (for reusable variant options)
        if (class_exists(\App\Models\VariantTemplate::class)) {
            $tplPedas = \App\Models\VariantTemplate::firstOrCreate(['name' => 'Level Pedas'], ['selection_type'=>'SINGLE','is_required'=>false,'order_index'=>1]);
            \App\Models\VariantTemplateOption::firstOrCreate(['variant_template_id'=>$tplPedas->id,'name'=>'Tidak Pedas'], ['price_modifier'=>0,'cogs_modifier'=>0]);
            \App\Models\VariantTemplateOption::firstOrCreate(['variant_template_id'=>$tplPedas->id,'name'=>'Sedang'], ['price_modifier'=>0,'cogs_modifier'=>0]);
            \App\Models\VariantTemplateOption::firstOrCreate(['variant_template_id'=>$tplPedas->id,'name'=>'Pedas'], ['price_modifier'=>1000,'cogs_modifier'=>300]);

            $tplExtra = \App\Models\VariantTemplate::firstOrCreate(['name' => 'Extra Topping'], ['selection_type'=>'MULTIPLE','is_required'=>false,'order_index'=>2]);
            \App\Models\VariantTemplateOption::firstOrCreate(['variant_template_id'=>$tplExtra->id,'name'=>'Keju'], ['price_modifier'=>3000,'cogs_modifier'=>1000]);
            \App\Models\VariantTemplateOption::firstOrCreate(['variant_template_id'=>$tplExtra->id,'name'=>'Coklat'], ['price_modifier'=>3000,'cogs_modifier'=>1000]);
            \App\Models\VariantTemplateOption::firstOrCreate(['variant_template_id'=>$tplExtra->id,'name'=>'Boba'], ['price_modifier'=>5000,'cogs_modifier'=>1500]);

            $tplSugar = \App\Models\VariantTemplate::firstOrCreate(['name' => 'Level Gula Global'], ['selection_type'=>'SINGLE','is_required'=>true,'order_index'=>3]);
            \App\Models\VariantTemplateOption::firstOrCreate(['variant_template_id'=>$tplSugar->id,'name'=>'Normal'], ['price_modifier'=>0,'cogs_modifier'=>0]);
            \App\Models\VariantTemplateOption::firstOrCreate(['variant_template_id'=>$tplSugar->id,'name'=>'Less Sugar'], ['price_modifier'=>0,'cogs_modifier'=>0]);
            \App\Models\VariantTemplateOption::firstOrCreate(['variant_template_id'=>$tplSugar->id,'name'=>'No Sugar'], ['price_modifier'=>0,'cogs_modifier'=>0]);
        }

        // 11. Sample Transactions
        if (Transaction::count() === 0 && count($createdProducts) > 0) {
            $pSample = $createdProducts[0];
            for ($i = 1; $i <= 10; $i++) {
                $methods = ['CASH', 'QRIS', 'CASH', 'GOFOOD', 'GRABFOOD'];
                $method = $methods[$i % 5];
                $txDate = now()->subHours($i * 2);

                $tx = Transaction::create([
                    'receipt_number' => 'KSV-' . $txDate->format('Ymd') . '-' . sprintf('%04d', $i),
                    'payment_method' => $method,
                    'total_amount' => $pSample->price * 2,
                    'total_hpp' => $pSample->hpp * 2,
                    'paid_amount' => ($pSample->price * 2) + 5000,
                    'change_amount' => 5000.0,
                    'cashier_name' => 'Rizki Kasir Utama',
                    'sync_status' => 'SYNCED',
                    'created_at' => $txDate,
                    'updated_at' => $txDate,
                ]);

                TransactionItem::create([
                    'transaction_id' => $tx->id,
                    'product_id' => $pSample->id,
                    'product_name' => $pSample->name,
                    'unit_price' => $pSample->price,
                    'unit_hpp' => $pSample->hpp,
                    'quantity' => 2,
                    'subtotal' => $pSample->price * 2,
                ]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Outlet;
use App\Models\PaymentSetting;
use Illuminate\Database\Seeder;

class KasivaDevSeeder extends Seeder
{
    /**
     * Dev-only seed: panggil ProductionSeeder dulu lalu tambah data dummy
     * untuk pengembangan lokal / E2E tanpa mengotori production.
     */
    public function run(): void
    {
        $this->call(KasivaProductionSeeder::class);

        // Tambahan khusus dev: contoh kategori dev-only (idempotent)
        Category::firstOrCreate(
            ['name' => '[DEV] Sandbox'],
            ['icon' => '🧪', 'order_index' => 99]
        );

        // Tandai bahwa dev seed telah dijalankan (opsional: bisa dipakai guard)
        $outlet = Outlet::first();
        if ($outlet) {
            PaymentSetting::setValue('dev_seed_version', date('Y-m-d H:i:s'));
        }
    }
}

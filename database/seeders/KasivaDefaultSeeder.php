<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class KasivaDefaultSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(KasivaProductionSeeder::class);
    }
}

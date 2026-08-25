<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Production: KasivaProductionSeeder (must_change_password=true)
     * Dev/test: KasivaDevSeeder (extends production + sandbox data)
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->call(KasivaProductionSeeder::class);
        } else {
            $this->call(KasivaDevSeeder::class);
        }
    }
}

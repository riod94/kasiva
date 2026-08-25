<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // Testing (sqlite :memory:) — skip hardening sepenuhnya agar tidak bentrok dengan seed test yang bikin Role::create slug owner.
        // Backfill + NOT NULL hanya relevan untuk pgsql production.
        if ($driver === 'sqlite' || app()->environment('testing')) {
            return;
        }

        // Backfill: semua user tanpa role dianggap owner (sebelum diperketat)
        $ownerId = DB::table('roles')->where('slug', 'owner')->value('id');

        if (! $ownerId) {
            $ownerId = (string) Str::orderedUuid();
            DB::table('roles')->insert([
                'id' => $ownerId,
                'name' => 'Owner / Pemilik',
                'slug' => 'owner',
                'description' => 'Akses penuh seluruh modul',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('users')->whereNull('role_id')->update(['role_id' => $ownerId]);

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE users ALTER COLUMN role_id SET NOT NULL');
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->uuid('role_id')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite' || app()->environment('testing')) {
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE users ALTER COLUMN role_id DROP NOT NULL');
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->uuid('role_id')->nullable()->change();
            });
        }
    }
};

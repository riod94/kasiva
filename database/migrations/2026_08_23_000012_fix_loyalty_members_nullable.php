<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE loyalty_members ALTER COLUMN name DROP NOT NULL');
            DB::statement('ALTER TABLE loyalty_members ALTER COLUMN phone DROP NOT NULL');
            try { DB::statement('ALTER TABLE loyalty_members DROP CONSTRAINT loyalty_members_phone_unique'); } catch (\Throwable $e) {}
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS loyalty_members_phone_unique ON loyalty_members (phone) WHERE phone IS NOT NULL');
        }

        if (DB::getDriverName() === 'sqlite') {
            try { DB::statement('ALTER TABLE loyalty_members ALTER COLUMN name DROP NOT NULL'); } catch (\Throwable $e) {}
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE loyalty_members ALTER COLUMN name SET NOT NULL');
            DB::statement('ALTER TABLE loyalty_members ALTER COLUMN phone SET NOT NULL');
        }
    }
};

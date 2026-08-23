<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inventory_logs')) {
            Schema::create('inventory_logs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('material_id')->constrained('materials')->cascadeOnDelete();
                $table->enum('type', ['IN', 'OUT', 'ADJUST'])->default('IN');
                $table->decimal('quantity', 12, 2);
                $table->decimal('unit_cost', 12, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['material_id', 'created_at']);
            });
        }
        // Add voided status + voided_at to transactions if missing
        if (Schema::hasTable('transactions') && !Schema::hasColumn('transactions', 'status')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('status')->default('COMPLETED')->after('sync_status');
                $table->dateTime('voided_at')->nullable()->after('status');
                $table->text('void_reason')->nullable()->after('voided_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'status')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn(['status', 'voided_at', 'void_reason']);
            });
        }
        Schema::dropIfExists('inventory_logs');
    }
};

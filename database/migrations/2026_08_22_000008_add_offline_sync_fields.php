<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            if (!Schema::hasColumn('transactions', 'client_transaction_id')) {
                $table->uuid('client_transaction_id')->nullable()->unique()->after('sync_status');
            }
            if (!Schema::hasColumn('transactions', 'sync_error')) {
                $table->text('sync_error')->nullable()->after('client_transaction_id');
            }
            if (!Schema::hasColumn('transactions', 'payment_confirmed_manually')) {
                $table->boolean('payment_confirmed_manually')->default(false)->after('sync_error');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            foreach (['payment_confirmed_manually', 'sync_error', 'client_transaction_id'] as $column) {
                if (Schema::hasColumn('transactions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

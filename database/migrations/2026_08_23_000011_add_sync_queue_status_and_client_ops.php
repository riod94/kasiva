<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('sync_queue', 'status')) {
            Schema::table('sync_queue', function (Blueprint $table) {
                $table->string('status', 20)->default('PENDING')->after('last_error');
                $table->timestamp('sent_at')->nullable()->after('status');
            });

            // Backfill from processed_at: processed_at not null => SYNCED
            \DB::table('sync_queue')
                ->whereNotNull('processed_at')
                ->update(['status' => 'SYNCED']);
        }

        // Add client_operation_id column if missing (for idempotency)
        if (!Schema::hasColumn('sync_queue', 'client_operation_id')) {
            Schema::table('sync_queue', function (Blueprint $table) {
                $table->uuid('client_operation_id')->nullable()->after('operation');
            });
        }

        // Add sync_status column to transactions for local sync tracking
        if (!Schema::hasColumn('transactions', 'client_transaction_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('client_transaction_id')->nullable()->after('sync_status');
            });
        }
    }

    public function down(): void
    {
        Schema::table('sync_queue', function (Blueprint $table) {
            if (Schema::hasColumn('sync_queue', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('sync_queue', 'sent_at')) {
                $table->dropColumn('sent_at');
            }
            if (Schema::hasColumn('sync_queue', 'client_operation_id')) {
                $table->dropColumn('client_operation_id');
            }
        });
    }
};

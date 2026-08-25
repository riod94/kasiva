<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $t): void {
            if (! Schema::hasColumn('expenses', 'client_expense_id')) {
                $t->uuid('client_expense_id')->nullable()->unique()->after('id');
            } if (! Schema::hasColumn('expenses', 'sync_status')) {
                $t->string('sync_status')->default('SYNCED')->after('client_expense_id');
            } if (! Schema::hasColumn('expenses', 'sync_error')) {
                $t->text('sync_error')->nullable()->after('sync_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $t): void {
            foreach (['sync_error', 'sync_status', 'client_expense_id'] as $c) {
                if (Schema::hasColumn('expenses', $c)) {
                    $t->dropColumn($c);
                }
            }
        });
    }
};

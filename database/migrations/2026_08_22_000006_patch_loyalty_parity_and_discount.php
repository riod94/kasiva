<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Loyalty programs — tambah kolom parity Ngepos
        Schema::table('loyalty_programs', function (Blueprint $t) {
            if (! Schema::hasColumn('loyalty_programs', 'reward_type')) {
                $t->string('reward_type')->default('FREE_PRODUCT')->after('expiry_months');
            }
            if (! Schema::hasColumn('loyalty_programs', 'reward_value')) {
                $t->decimal('reward_value', 12, 2)->default(0)->after('reward_type');
            }
            if (! Schema::hasColumn('loyalty_programs', 'reward_claim_days')) {
                $t->integer('reward_claim_days')->default(30)->after('reward_value');
            }
            if (! Schema::hasColumn('loyalty_programs', 'after_claim')) {
                $t->string('after_claim')->default('RESET')->after('reward_claim_days');
            }
            if (! Schema::hasColumn('loyalty_programs', 'excluded_product_ids')) {
                $t->json('excluded_product_ids')->nullable()->after('after_claim');
            }
        });

        // Transactions — simpan diskon kampanye
        Schema::table('transactions', function (Blueprint $t) {
            if (! Schema::hasColumn('transactions', 'discount_total')) {
                $t->decimal('discount_total', 12, 2)->default(0)->after('total_hpp');
            }
            if (! Schema::hasColumn('transactions', 'discount_note')) {
                $t->string('discount_note')->nullable()->after('discount_total');
            }
            if (! Schema::hasColumn('transactions', 'loyalty_member_id')) {
                $t->foreignUuid('loyalty_member_id')->nullable()->after('discount_note')->constrained('loyalty_members')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $t) {
            if (Schema::hasColumn('transactions', 'loyalty_member_id')) {
                try {
                    $t->dropForeign(['loyalty_member_id']);
                } catch (Throwable $e) {
                }
                $t->dropColumn('loyalty_member_id');
            }
            foreach (['discount_note', 'discount_total'] as $col) {
                if (Schema::hasColumn('transactions', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
        Schema::table('loyalty_programs', function (Blueprint $t) {
            foreach (['excluded_product_ids', 'after_claim', 'reward_claim_days', 'reward_value', 'reward_type'] as $col) {
                if (Schema::hasColumn('loyalty_programs', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};

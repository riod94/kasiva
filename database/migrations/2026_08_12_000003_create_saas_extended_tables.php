<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Loyalty Rewards
        if (!Schema::hasTable('loyalty_rewards')) {
            Schema::create('loyalty_rewards', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->integer('stamps_required')->default(10);
                $table->decimal('discount_amount', 12, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Loyalty Stamps Log
        if (!Schema::hasTable('loyalty_stamps')) {
            Schema::create('loyalty_stamps', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('loyalty_member_id')->constrained('loyalty_members')->cascadeOnDelete();
                $table->foreignUuid('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
                $table->integer('stamps_earned')->default(1);
                $table->timestamps();
            });
        }

        // 3. Promotions & Discounts
        if (!Schema::hasTable('promotions')) {
            Schema::create('promotions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->enum('type', ['PERCENTAGE', 'FIXED_AMOUNT', 'BUY_X_GET_Y'])->default('PERCENTAGE');
                $table->decimal('discount_value', 12, 2);
                $table->decimal('min_purchase', 12, 2)->default(0);
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 4. Bundles (Paket Hemat)
        if (!Schema::hasTable('bundles')) {
            Schema::create('bundles', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->decimal('price', 12, 2);
                $table->decimal('cogs', 12, 2)->default(0);
                $table->string('image')->nullable();
                $table->boolean('is_active')->default(true);
                $table->json('items'); // Array of items [{product_id, quantity, variant_hash}]
                $table->timestamps();
            });
        }

        // 5. Campaigns (Kampanye Promosi)
        if (!Schema::hasTable('campaigns')) {
            Schema::create('campaigns', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->text('description')->nullable();
                $table->enum('type', ['BUNDLE', 'BUY_X_GET_Y', 'BULK_DISCOUNT'])->default('BUNDLE');
                $table->boolean('is_active')->default(true);
                $table->integer('priority')->default(0);
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->string('reward_type')->default('FIXED_DISCOUNT'); // FREE_PRODUCT, FIXED_DISCOUNT, PERCENT_DISCOUNT
                $table->decimal('reward_value', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        // 6. Audit Logs
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('user_name');
                $table->string('action'); // e.g. LOGIN, CHECKOUT, CREATE_PRODUCT, VOID_TRANSACTION
                $table->text('details')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('bundles');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('loyalty_stamps');
        Schema::dropIfExists('loyalty_rewards');
    }
};

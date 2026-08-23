<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('variant_templates')) {
            Schema::create('variant_templates', function(Blueprint $t){
                $t->uuid('id')->primary();
                $t->string('name');
                $t->enum('selection_type',['SINGLE','MULTIPLE'])->default('SINGLE');
                $t->boolean('is_required')->default(false);
                $t->integer('order_index')->default(0);
                $t->timestamps();
            });
        }
        if (!Schema::hasTable('variant_template_options')) {
            Schema::create('variant_template_options', function(Blueprint $t){
                $t->uuid('id')->primary();
                $t->foreignUuid('variant_template_id')->constrained('variant_templates')->cascadeOnDelete();
                $t->string('name');
                $t->decimal('price_modifier',12,2)->default(0);
                $t->decimal('cogs_modifier',12,2)->default(0);
                $t->timestamps();
            });
        }
        if (!Schema::hasTable('loyalty_programs')) {
            Schema::create('loyalty_programs', function(Blueprint $t){
                $t->uuid('id')->primary();
                $t->string('name');
                $t->integer('target_stamps')->default(10);
                $t->decimal('min_transaction',12,2)->default(0);
                $t->integer('expiry_months')->default(12);
                $t->foreignUuid('reward_product_id')->nullable()->constrained('products')->nullOnDelete();
                $t->boolean('allow_with_promo')->default(false);
                $t->boolean('is_active')->default(true);
                $t->timestamps();
            });
        }
        if (!Schema::hasTable('campaign_items')) {
            Schema::create('campaign_items', function(Blueprint $t){
                $t->uuid('id')->primary();
                $t->foreignUuid('campaign_id')->constrained('campaigns')->cascadeOnDelete();
                $t->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
                $t->integer('quantity')->default(1);
                $t->enum('role',['BUY','GET'])->default('BUY');
                $t->timestamps();
            });
        }
        if (!Schema::hasTable('campaign_rewards')) {
            Schema::create('campaign_rewards', function(Blueprint $t){
                $t->uuid('id')->primary();
                $t->foreignUuid('campaign_id')->constrained('campaigns')->cascadeOnDelete();
                $t->string('reward_type')->default('PERCENT_DISCOUNT');
                $t->decimal('reward_value',12,2)->default(0);
                $t->timestamps();
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('campaign_rewards');
        Schema::dropIfExists('campaign_items');
        Schema::dropIfExists('loyalty_programs');
        Schema::dropIfExists('variant_template_options');
        Schema::dropIfExists('variant_templates');
    }
};

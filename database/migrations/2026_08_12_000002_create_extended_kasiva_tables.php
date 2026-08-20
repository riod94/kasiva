<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Outlets
        Schema::create('outlets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('qris_image_path')->nullable();
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('service_charge_percentage', 5, 2)->default(0);
            $table->timestamps();
        });

        // 2. Expenses (Pengeluaran Operasional)
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->decimal('amount', 12, 2);
            $table->enum('category', [
                'RAW_MATERIAL',
                'RENT',
                'SALARY',
                'UTILITIES',
                'MARKETING',
                'OPERATIONAL',
                'OTHER'
            ])->default('OPERATIONAL');
            $table->dateTime('expense_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Product Variants & Variant Options
        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name'); // e.g. Ukuran, Suhu, Extra Topping
            $table->enum('selection_type', ['SINGLE', 'MULTIPLE'])->default('SINGLE');
            $table->boolean('is_required')->default(false);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        Schema::create('variant_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->string('name'); // e.g. Regular, Large, Less Ice
            $table->decimal('price_modifier', 12, 2)->default(0);
            $table->decimal('cogs_modifier', 12, 2)->default(0);
            $table->timestamps();
        });

        // 4. Payment & Platform Settings
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique(); // e.g. qris_image, gofood_markup_percent, grabfood_markup_percent
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // 5. Roles & Permissions (RBAC)
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // e.g. Owner, Manager, Kasir
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // e.g. View Reports, Manage Products, Edit Stock
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignUuid('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->timestamps();
        });

        // Add role_id and outlet_id to users if not exists
        if (!Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignUuid('role_id')->nullable()->after('password')->constrained('roles')->nullOnDelete();
                $table->foreignUuid('outlet_id')->nullable()->after('role_id')->constrained('outlets')->nullOnDelete();
                $table->string('pin', 6)->nullable()->after('outlet_id');
                $table->string('phone')->nullable()->after('pin');
                $table->boolean('is_active')->default(true)->after('phone');
            });
        }

        // Add platform adjustment & backdate to transactions table if missing
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'platform_discount')) {
                $table->decimal('platform_discount', 12, 2)->default(0)->after('change_amount');
            }
            if (!Schema::hasColumn('transactions', 'platform_markup')) {
                $table->decimal('platform_markup', 12, 2)->default(0)->after('platform_discount');
            }
            if (!Schema::hasColumn('transactions', 'is_backdated')) {
                $table->boolean('is_backdated')->default(false)->after('platform_markup');
            }
            if (!Schema::hasColumn('transactions', 'transaction_date')) {
                $table->dateTime('transaction_date')->nullable()->after('is_backdated');
            }
            if (!Schema::hasColumn('transactions', 'variant_details')) {
                $table->json('variant_summary')->nullable()->after('transaction_date');
            }
        });

        // 6. Loyalty Program
        Schema::create('loyalty_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('phone')->unique();
            $table->integer('stamps_count')->default(0);
            $table->integer('total_visits')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_members');
        
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['platform_discount', 'platform_markup', 'is_backdated', 'transaction_date', 'variant_summary']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['outlet_id']);
            $table->dropColumn(['role_id', 'outlet_id', 'pin', 'phone', 'is_active']);
        });

        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('payment_settings');
        Schema::dropIfExists('variant_options');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('outlets');
    }
};

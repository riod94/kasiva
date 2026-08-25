<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_devices', function (Blueprint $t): void {
            $t->uuid('id')->primary();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('device_name')->nullable();
            $t->string('platform')->default('web');
            $t->string('last_cursor')->nullable();
            $t->timestamp('last_synced_at')->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'id']);
        });
        Schema::create('sync_queue', function (Blueprint $t): void {
            $t->uuid('id')->primary();
            $t->uuid('device_id');
            $t->string('operation');
            $t->string('entity_type');
            $t->uuid('entity_id')->nullable();
            $t->json('payload');
            $t->unsignedInteger('attempts')->default(0);
            $t->timestamp('available_at')->nullable();
            $t->timestamp('processed_at')->nullable();
            $t->text('last_error')->nullable();
            $t->timestamps();
            $t->foreign('device_id')->references('id')->on('sync_devices')->cascadeOnDelete();
            $t->index(['device_id', 'processed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_queue');
        Schema::dropIfExists('sync_devices');
    }
};

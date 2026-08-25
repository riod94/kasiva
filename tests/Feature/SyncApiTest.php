<?php

use App\Models\LoyaltyProgram;
use App\Models\Product;
use App\Models\SyncDevice;
use App\Models\SyncQueue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);
it('registers a sync device for the authenticated user', function () {
    $user = User::factory()->create();
    $id = (string) Str::uuid();
    $this->actingAs($user)->postJson('/api/v1/sync/devices', ['device_id' => $id, 'platform' => 'web', 'device_name' => 'Browser Kasir'])->assertOk()->assertJsonPath('device_id', $id);
    expect(SyncDevice::where('id', $id)->where('user_id', $user->id)->exists())->toBeTrue();
});
it('pushes operations idempotently and isolates devices by user', function () {
    $user = User::factory()->create();
    $id = (string) Str::uuid();
    $this->actingAs($user)->postJson('/api/v1/sync/devices', ['device_id' => $id, 'platform' => 'android'])->assertOk();
    $op = (string) Str::uuid();
    $payload = ['device_id' => $id, 'operations' => [['id' => $op, 'operation' => 'UPSERT_EXPENSE', 'entity_type' => 'expense', 'payload' => ['title' => 'Listrik', 'amount' => 10000]]]];
    $this->actingAs($user)->postJson('/api/v1/sync/push', $payload)->assertOk()->assertJsonPath('results.0.status', 'SYNCED');
    $this->actingAs($user)->postJson('/api/v1/sync/push', $payload)->assertOk();
    expect(SyncQueue::whereKey($op)->count())->toBe(1);
    $other = User::factory()->create();
    $this->actingAs($other)->postJson('/api/v1/sync/push', $payload)->assertNotFound();
});
it('returns a pull cursor scoped to the authenticated device', function () {
    $user = User::factory()->create();
    $id = (string) Str::uuid();
    $this->actingAs($user)->postJson('/api/v1/sync/devices', ['device_id' => $id, 'platform' => 'ios'])->assertOk();
    $this->actingAs($user)->postJson('/api/v1/sync/pull', ['device_id' => $id])->assertOk()->assertJsonStructure(['cursor', 'changes', 'device_id'])->assertJsonPath('device_id', $id);
});

it('pulls changed products and loyalty programs after a cursor', function () {
    $user = User::factory()->create();
    $id = (string) Str::uuid();
    $this->actingAs($user)->postJson('/api/v1/sync/devices', ['device_id' => $id, 'platform' => 'web'])->assertOk();
    $product = Product::create(['name' => 'Pulled Product', 'sku' => 'PULL-1', 'price' => 12000, 'hpp' => 5000, 'current_stock' => 4, 'is_active' => true]);
    LoyaltyProgram::create(['id' => (string) Str::uuid(), 'name' => 'Pulled Program', 'target_stamps' => 5, 'min_transaction' => 0, 'is_active' => true]);
    $response = $this->actingAs($user)->postJson('/api/v1/sync/pull', ['device_id' => $id, 'cursor' => '2000-01-01T00:00:00Z'])->assertOk();
    $response->assertJsonPath('device_id', $id)->assertJsonCount(1, 'changes.products')->assertJsonCount(1, 'changes.loyalty_programs');
    expect($response->json('cursor'))->not->toBeNull();
});

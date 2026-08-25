<?php

use App\Models\LoyaltyMember;
use App\Models\LoyaltyProgram;
use App\Models\LoyaltyStamp;
use App\Models\Product;
use App\Models\SyncDevice;
use App\Models\SyncQueue;
use App\Models\Transaction;
use App\Models\User;
use App\Services\SyncQueueProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);
function syncPayload(string $productId, string $clientId, int $qty = 1): array
{
    return ['client_transaction_id' => $clientId, 'receipt_number' => 'KSV-SYNC-'.Str::upper(Str::random(6)), 'payment_method' => 'CASH', 'total_amount' => 10000, 'total_hpp' => 4000, 'paid_amount' => 10000, 'change_amount' => 0, 'cashier_name' => 'Sync', 'items' => [['product_id' => $productId, 'product_name' => 'Offline Product', 'unit_price' => 10000, 'unit_hpp' => 4000, 'quantity' => $qty, 'subtotal' => 10000 * $qty]]];
}
it('processes transaction items and decrements stock once on retry', function () {
    $user = User::factory()->create();
    $device = SyncDevice::create(['id' => (string) Str::uuid(), 'user_id' => $user->id, 'platform' => 'web']);
    $product = Product::create(['name' => 'Offline Product', 'sku' => 'OFF-1', 'price' => 10000, 'hpp' => 4000, 'current_stock' => 5, 'is_active' => true]);
    $payload = syncPayload($product->id, (string) Str::uuid());
    $entry = SyncQueue::create(['id' => (string) Str::uuid(), 'device_id' => $device->id, 'operation' => 'UPSERT_TRANSACTION', 'entity_type' => 'transaction', 'entity_id' => null, 'payload' => $payload]);
    $processor = app(SyncQueueProcessor::class);
    expect($processor->process($entry))->toBe('SYNCED');
    $entry->refresh();
    expect($processor->process($entry))->toBe('SYNCED');
    expect((float) $product->refresh()->current_stock)->toBe(4.0);
    expect($entry->fresh()->processed_at)->not->toBeNull();
});
it('marks insufficient stock as conflict without creating transaction', function () {
    $user = User::factory()->create();
    $device = SyncDevice::create(['id' => (string) Str::uuid(), 'user_id' => $user->id, 'platform' => 'android']);
    $product = Product::create(['name' => 'Low Stock', 'sku' => 'LOW-1', 'price' => 10000, 'hpp' => 4000, 'current_stock' => 0, 'is_active' => true]);
    $payload = syncPayload($product->id, (string) Str::uuid(), 1);
    $entry = SyncQueue::create(['id' => (string) Str::uuid(), 'device_id' => $device->id, 'operation' => 'UPSERT_TRANSACTION', 'entity_type' => 'transaction', 'payload' => $payload]);
    expect(app(SyncQueueProcessor::class)->process($entry))->toBe('CONFLICT');
    expect($entry->fresh()->processed_at)->toBeNull();
    expect($entry->fresh()->last_error)->toContain('STOCK_CONFLICT');
    expect(Transaction::where('client_transaction_id', $payload['client_transaction_id'])->exists())->toBeFalse();
});

it('syncs loyalty member and stamp idempotently without double counting', function () {
    $user = User::factory()->create();
    $device = SyncDevice::create(['id' => (string) Str::uuid(), 'user_id' => $user->id, 'platform' => 'web']);
    $memberId = (string) Str::uuid();
    $programId = (string) Str::uuid();
    LoyaltyProgram::create(['id' => $programId, 'name' => 'Offline Club', 'target_stamps' => 5, 'min_transaction' => 0, 'is_active' => true]);
    $memberEntry = SyncQueue::create(['id' => (string) Str::uuid(), 'device_id' => $device->id, 'operation' => 'UPSERT_MEMBER', 'entity_type' => 'member', 'payload' => ['client_member_id' => $memberId, 'name' => 'Offline Member', 'phone' => '08123456789', 'qr_code' => 'KSV-MBR-OFFLINE', 'status' => 'ASSIGNED', 'stamps_count' => 0, 'total_visits' => 0]]);
    $processor = app(SyncQueueProcessor::class);
    expect($processor->process($memberEntry))->toBe('SYNCED');
    $stampId = (string) Str::uuid();
    $stampEntry = SyncQueue::create(['id' => (string) Str::uuid(), 'device_id' => $device->id, 'operation' => 'ADD_LOYALTY_STAMP', 'entity_type' => 'stamp', 'payload' => ['client_stamp_id' => $stampId, 'loyalty_member_id' => $memberId, 'program_id' => $programId, 'stamps_earned' => 1, 'stamped_at' => now()->toISOString()]]);
    expect($processor->process($stampEntry))->toBe('SYNCED');
    $stampEntry->refresh();
    expect($processor->process($stampEntry))->toBe('SYNCED');
    expect(LoyaltyMember::find($memberId)->stamps_count)->toBe(1);
    expect(LoyaltyStamp::whereKey($stampId)->count())->toBe(1);
});

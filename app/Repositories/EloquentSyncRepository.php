<?php

namespace App\Repositories;

use App\Contracts\SyncRepositoryInterface;
use App\DTO\SyncOperation;
use App\Models\SyncQueue;
use Carbon\Carbon;

class EloquentSyncRepository implements SyncRepositoryInterface
{
    public function enqueueOperation(string $operation, string $entityType, ?string $entityId, array $payload, ?string $deviceId = null): string
    {
        $op = new SyncOperation(
            operation: $operation,
            entityType: $entityType,
            entityId: $entityId,
            payload: $payload,
        );

        $deviceId = $deviceId ?? \App\Models\SyncDevice::query()->first()?->id;

        $queue = SyncQueue::create([
            'device_id' => $deviceId,
            'operation' => $op->operation,
            'entity_type' => $op->entityType,
            'entity_id' => $op->entityId,
            'payload' => $op->payload,
            'client_operation_id' => $op->clientOperationId,
            'status' => SyncOperation::STATUS_PENDING,
            'attempts' => 0,
            'available_at' => now(),
        ]);

        return $queue->id;
    }

    public function getPendingOperations(): array
    {
        $rows = SyncQueue::whereIn('status', [
            SyncOperation::STATUS_PENDING,
            SyncOperation::STATUS_FAILED,
        ])
            ->where(function ($q) {
                $q->whereNull('available_at')
                    ->orWhere('available_at', '<=', now());
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return $rows->map(fn (SyncQueue $q) => $this->toDto($q))->all();
    }

    public function getOperation(string $operationId): ?array
    {
        $queue = SyncQueue::find($operationId);

        return $queue ? $this->toDto($queue)->toArray() : null;
    }

    public function updateOperationStatus(string $operationId, string $status, ?string $error = null): void
    {
        SyncQueue::where('id', $operationId)->update([
            'status' => $status,
            'last_error' => $error,
            'processed_at' => in_array($status, [SyncOperation::STATUS_SYNCED, SyncOperation::STATUS_CONFLICT])
                ? ($error ? now() : now())
                : null,
            'sent_at' => $status === SyncOperation::STATUS_SENDING ? now() : null,
        ]);
    }

    public function markAsSending(array $operationIds): void
    {
        SyncQueue::whereIn('id', $operationIds)->update([
            'status' => SyncOperation::STATUS_SENDING,
            'sent_at' => now(),
        ]);
    }

    public function markAsSynced(array $operationIds): void
    {
        SyncQueue::whereIn('id', $operationIds)->update([
            'status' => SyncOperation::STATUS_SYNCED,
            'processed_at' => now(),
            'last_error' => null,
        ]);
    }

    public function markAsConflict(string $operationId, string $error): void
    {
        SyncQueue::where('id', $operationId)->update([
            'status' => SyncOperation::STATUS_CONFLICT,
            'last_error' => $error,
        ]);
    }

    public function markAsFailed(string $operationId, string $error): void
    {
        SyncQueue::where('id', $operationId)->update([
            'status' => SyncOperation::STATUS_FAILED,
            'last_error' => $error,
            'available_at' => now()->addSeconds(pow(2, min($this->getAttempts($operationId) + 1, 6)) * 60),
        ]);

        $this->getSyncQueue($operationId)?->increment('attempts');
    }

    public function clearSynced(int $olderThanHours = 24): int
    {
        return SyncQueue::where('status', SyncOperation::STATUS_SYNCED)
            ->where('processed_at', '<', now()->subHours($olderThanHours))
            ->delete();
    }

    protected function toDto(SyncQueue $queue): SyncOperation
    {
        return new SyncOperation(
            operation: $queue->operation,
            entityType: $queue->entity_type,
            entityId: $queue->entity_id,
            payload: (array) $queue->payload,
            clientOperationId: $queue->client_operation_id,
            status: $queue->status ?? SyncOperation::STATUS_PENDING,
            attempts: (int) $queue->attempts,
            lastError: $queue->last_error,
            availableAt: $queue->available_at,
        );
    }

    protected function getSyncQueue(string $id): ?SyncQueue
    {
        return SyncQueue::find($id);
    }

    protected function getAttempts(string $operationId): int
    {
        return (int) SyncQueue::where('id', $operationId)->value('attempts');
    }
}

<?php

namespace App\Contracts;

interface SyncRepositoryInterface
{
    public function enqueueOperation(string $operation, string $entityType, ?string $entityId, array $payload, ?string $deviceId = null): string;

    public function getPendingOperations(): array;

    public function getOperation(string $operationId): ?array;

    public function updateOperationStatus(string $operationId, string $status, ?string $error = null): void;

    public function markAsSending(array $operationIds): void;

    public function markAsSynced(array $operationIds): void;

    public function markAsConflict(string $operationId, string $error): void;

    public function markAsFailed(string $operationId, string $error): void;

    public function clearSynced(int $olderThanHours = 24): int;
}

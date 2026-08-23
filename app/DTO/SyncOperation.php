<?php

namespace App\DTO;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SyncOperation
{
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_SENDING = 'SENDING';
    public const STATUS_SYNCED = 'SYNCED';
    public const STATUS_CONFLICT = 'CONFLICT';
    public const STATUS_FAILED = 'FAILED';

    public function __construct(
        public string $operation,
        public string $entityType,
        public ?string $entityId,
        public array $payload,
        public ?string $clientOperationId = null,
        public string $status = self::STATUS_PENDING,
        public int $attempts = 0,
        public ?string $lastError = null,
        public ?Carbon $availableAt = null,
    ) {
        $this->clientOperationId ??= (string) Str::uuid();
        $this->availableAt ??= now();
    }

    public function toArray(): array
    {
        return [
            'client_operation_id' => $this->clientOperationId,
            'operation' => $this->operation,
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'payload' => $this->payload,
            'status' => $this->status,
            'attempts' => $this->attempts,
            'available_at' => $this->availableAt?->toISOString(),
            'last_error' => $this->lastError,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            operation: $data['operation'],
            entityType: $data['entity_type'],
            entityId: $data['entity_id'] ?? null,
            payload: $data['payload'],
            clientOperationId: $data['client_operation_id'] ?? null,
            status: $data['status'] ?? self::STATUS_PENDING,
            attempts: $data['attempts'] ?? 0,
            lastError: $data['last_error'] ?? null,
            availableAt: isset($data['available_at']) ? Carbon::parse($data['available_at']) : null,
        );
    }

    public function isTerminalState(): bool
    {
        return in_array($this->status, [self::STATUS_SYNCED, self::STATUS_CONFLICT]);
    }

    public function incrementAttempts(): void
    {
        $this->attempts++;
    }
}

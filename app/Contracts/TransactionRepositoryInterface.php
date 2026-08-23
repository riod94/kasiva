<?php

namespace App\Contracts;

interface TransactionRepositoryInterface
{
    public function createTransaction(array $data): array;

    public function findById(string $id): ?array;

    public function findByClientTransactionId(string $clientTransactionId): ?array;

    public function findByReceiptNumber(string $receiptNumber): ?array;

    public function list(?int $limit = null, ?string $cursor = null): array;
}

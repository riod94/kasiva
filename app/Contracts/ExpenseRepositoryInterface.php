<?php

namespace App\Contracts;

interface ExpenseRepositoryInterface
{
    public function create(array $data): array;

    public function findById(string $id): ?array;

    public function list(?int $limit = null, ?string $cursor = null): array;
}

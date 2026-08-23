<?php

namespace App\Contracts;

interface CatalogRepositoryInterface
{
    public function list(?string $categoryId = null, ?string $search = null): array;

    public function findById(string $id): ?array;

    public function findBySku(string $sku): ?array;

    public function categories(): array;

    public function stockSnapshot(): array;

    public function bootstrapFromCloud(string $deviceId, string $cursor = null): int;
}

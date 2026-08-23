<?php

namespace App\Contracts;

interface MemberRepositoryInterface
{
    public function findById(string $id): ?array;

    public function findByQrCode(string $qrCode): ?array;

    public function findByPhone(string $phone): ?array;

    public function cacheMembers(array $members): int;

    public function list(int $limit = 50): array;
}

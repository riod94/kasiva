<?php

namespace App\Contracts;

interface LoyaltyRepositoryInterface
{
    public function getProgram(): ?array;

    public function getMemberProgress(string $memberId): ?array;

    public function addStamp(string $memberId, int $stampsEarned): void;

    public function checkRewardEligibility(string $memberId): array;

    public function cacheProgram(array $program): void;

    public function cacheMembers(array $members): int;
}

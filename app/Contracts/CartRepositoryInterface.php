<?php

namespace App\Contracts;

interface CartRepositoryInterface
{
    public function getMultiCart(): array;

    public function getActiveCart(): array;

    public function setActiveCartIndex(int $index): void;

    public function getActiveCartIndex(): int;

    public function addItem(array $item): void;

    public function updateQuantity(string $itemKey, int $qty): void;

    public function removeItem(string $itemKey): void;

    public function clearActiveCart(): void;

    public function saveCarts(array $carts): void;

    public function createNewCart(string $name = null): void;

    public function closeCart(int $index): void;
}

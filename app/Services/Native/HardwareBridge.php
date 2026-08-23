<?php

namespace App\Services\Native;

use Illuminate\Support\Facades\Log;

class HardwareBridge
{
    public function getNetworkState(): string
    {
        // In NativePHP/electron this would call native APIs; here we stub for testability.
        return 'unknown';
    }

    public function getLifecycleState(): string { return 'foreground'; }

    public function printEscPos(string $payload, ?string $printerName = null): bool
    {
        Log::info('HardwareBridge.printEscPos stub', ['printer'=>$printerName, 'bytes'=>strlen($payload)]);
        return true;
    }

    public function openCashDrawer(?string $printerName = null): bool
    {
        Log::info('HardwareBridge.openCashDrawer stub', ['printer'=>$printerName]); return true;
    }

    public function scanBarcode(?string $source = null): ?string
    {
        Log::info('HardwareBridge.scanBarcode stub'); return null;
    }

    public static function isNativeAvailable(): bool
    {
        return class_exists(\Native\Electron\Facades\Window::class) || class_exists(\Native\Mobile\Facades\Window::class);
    }
}

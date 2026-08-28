<?php

namespace App\Services;

use App\Models\InventoryLog;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class HppCalculatorService
{
    /**
     * Recalculates moving average cost of a raw material upon restocking.
     * Formula: ((currentStock * currentAvgCost) + (incomingQty * incomingPrice)) / (currentStock + incomingQty)
     */
    public function recalculateMovingAverage(Material $material, float $incomingQty, float $incomingPrice, ?string $notes = null, bool $log = true): float
    {
        if ($incomingQty <= 0) {
            return (float) $material->avg_cost;
        }

        return DB::transaction(function () use ($material, $incomingQty, $incomingPrice, $notes, $log) {
            $totalCurrentValue = (float) $material->current_stock * (float) $material->avg_cost;
            $totalIncomingValue = $incomingQty * $incomingPrice;
            $newTotalStock = (float) $material->current_stock + $incomingQty;

            if ($newTotalStock <= 0) {
                return 0.0;
            }

            $newAvgCost = round(($totalCurrentValue + $totalIncomingValue) / $newTotalStock, 2);

            $material->update([
                'current_stock' => $newTotalStock,
                'avg_cost' => $newAvgCost,
            ]);

            if ($log) {
                InventoryLog::create([
                    'material_id' => $material->id,
                    'type' => 'IN',
                    'quantity' => $incomingQty,
                    'unit_cost' => $incomingQty > 0 ? round($totalIncomingValue / $incomingQty, 2) : 0,
                    'notes' => $notes ?? 'Restok via HppCalculatorService',
                ]);
            }

            return $newAvgCost;
        });
    }

    public function restoreStockForVoid(Product $product, int $qty): void
    {
        DB::transaction(function () use ($product, $qty) {
            $product->loadMissing('materials');
            foreach ($product->materials as $material) {
                $restoreAmount = (float) $material->pivot->quantity * $qty;
                $material->increment('current_stock', $restoreAmount);
                InventoryLog::create([
                    'material_id' => $material->id,
                    'type' => 'ADJUST',
                    'quantity' => $restoreAmount,
                    'unit_cost' => (float) $material->avg_cost,
                    'notes' => 'Void transaksi: restore stok '.$product->name.' x'.$qty,
                ]);
            }
            $product->increment('current_stock', $qty);
        });
    }

    /**
     * Calculates the total COGS (HPP) for a product based on its ingredient recipes.
     */
    public function calculateProductHpp(Product $product): float
    {
        $product->loadMissing('materials');
        $totalHpp = 0.0;

        foreach ($product->materials as $material) {
            $qtyNeeded = (float) $material->pivot->quantity;
            $materialAvgCost = (float) $material->avg_cost;
            $totalHpp += ($qtyNeeded * $materialAvgCost);
        }

        $totalHpp = round($totalHpp, 2);

        $product->update(['hpp' => $totalHpp]);

        return $totalHpp;
    }

    /**
     * Deducts material stock atomically during checkout.
     */
    public function deductRecipeStockForCheckout(Product $product, int $qty): void
    {
        if ($qty <= 0) {
            throw new \InvalidArgumentException('Jumlah produk harus lebih dari nol.');
        }

        $deduct = function () use ($product, $qty): void {
            $lockedProduct = Product::query()->lockForUpdate()->findOrFail($product->id);
            $materials = $lockedProduct->materials()->lockForUpdate()->orderBy('materials.id')->get();

            if ((float) $lockedProduct->current_stock < $qty) {
                throw new \InvalidArgumentException("Stok {$lockedProduct->name} tidak mencukupi.");
            }

            foreach ($materials as $material) {
                $deductAmount = (float) $material->pivot->quantity * $qty;

                if ((float) $material->current_stock < $deductAmount) {
                    throw new \InvalidArgumentException("Stok bahan {$material->name} tidak mencukupi.");
                }
            }

            foreach ($materials as $material) {
                $material->decrement('current_stock', (float) $material->pivot->quantity * $qty);
            }

            $lockedProduct->decrement('current_stock', $qty);
        };

        if (DB::transactionLevel() > 0) {
            $deduct();

            return;
        }

        DB::transaction($deduct);
    }
}

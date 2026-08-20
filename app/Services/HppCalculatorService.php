<?php

namespace App\Services;

use App\Models\Material;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class HppCalculatorService
{
    /**
     * Recalculates moving average cost of a raw material upon restocking.
     * Formula: ((currentStock * currentAvgCost) + (incomingQty * incomingPrice)) / (currentStock + incomingQty)
     */
    public function recalculateMovingAverage(Material $material, float $incomingQty, float $incomingPrice): float
    {
        if ($incomingQty <= 0) {
            return $material->avg_cost;
        }

        $totalCurrentValue = $material->current_stock * $material->avg_cost;
        $totalIncomingValue = $incomingQty * $incomingPrice;
        $newTotalStock = $material->current_stock + $incomingQty;

        if ($newTotalStock <= 0) {
            return 0.0;
        }

        $newAvgCost = round(($totalCurrentValue + $totalIncomingValue) / $newTotalStock, 2);

        $material->update([
            'current_stock' => $newTotalStock,
            'avg_cost' => $newAvgCost,
        ]);

        return $newAvgCost;
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
        DB::transaction(function () use ($product, $qty) {
            $product->loadMissing('materials');

            foreach ($product->materials as $material) {
                $deductAmount = (float) $material->pivot->quantity * $qty;
                $material->decrement('current_stock', $deductAmount);
            }

            $product->decrement('current_stock', $qty);
        });
    }
}

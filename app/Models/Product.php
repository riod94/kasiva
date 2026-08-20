<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'price',
        'hpp',
        'current_stock',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'hpp' => 'float',
        'current_stock' => 'float',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'product_recipes')
                    ->using(ProductRecipe::class)
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(ProductRecipe::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('order_index');
    }

    /**
     * Hitung 4-Tier Margin (Kritis, Tipis, Sehat, Optimal)
     * Kritis: < 30%
     * Tipis: 30% - 44%
     * Sehat: 45% - 71%
     * Optimal: >= 72%
     */
    public function getMarginTierAttribute(): array
    {
        if ($this->price <= 0) {
            return ['label' => 'N/A', 'color' => 'bg-gray-100 text-gray-600', 'margin_percent' => 0];
        }

        $marginPercent = (($this->price - $this->hpp) / $this->price) * 100;

        if ($marginPercent < 30) {
            return ['label' => 'Kritis', 'color' => 'bg-red-100 text-red-700 border-red-200', 'margin_percent' => round($marginPercent, 1)];
        } elseif ($marginPercent < 45) {
            return ['label' => 'Tipis', 'color' => 'bg-amber-100 text-amber-700 border-amber-200', 'margin_percent' => round($marginPercent, 1)];
        } elseif ($marginPercent < 72) {
            return ['label' => 'Sehat', 'color' => 'bg-emerald-100 text-emerald-700 border-emerald-200', 'margin_percent' => round($marginPercent, 1)];
        } else {
            return ['label' => 'Optimal', 'color' => 'bg-indigo-100 text-indigo-700 border-indigo-200', 'margin_percent' => round($marginPercent, 1)];
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'unit',
        'current_stock',
        'min_stock',
        'avg_cost',
        'is_active',
    ];

    protected $casts = [
        'current_stock' => 'float',
        'min_stock' => 'float',
        'avg_cost' => 'float',
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_recipes')
            ->using(ProductRecipe::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }
}

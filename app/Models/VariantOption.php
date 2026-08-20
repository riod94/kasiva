<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantOption extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_variant_id',
        'name',
        'price_modifier',
        'cogs_modifier',
    ];

    protected $casts = [
        'price_modifier' => 'float',
        'cogs_modifier' => 'float',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}

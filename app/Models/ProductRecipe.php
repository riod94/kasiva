<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductRecipe extends Pivot
{
    use HasUuids;

    protected $table = 'product_recipes';
    public $incrementing = false;
    protected $keyType = 'string';
}

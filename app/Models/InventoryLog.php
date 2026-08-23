<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLog extends Model
{
    use HasUuids;

    protected $fillable = [
        'material_id',
        'type',
        'quantity',
        'unit_cost',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_cost' => 'float',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}

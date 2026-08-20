<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bundle extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'price',
        'cogs',
        'image',
        'is_active',
        'items',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cogs' => 'decimal:2',
            'is_active' => 'boolean',
            'items' => 'array',
        ];
    }
}

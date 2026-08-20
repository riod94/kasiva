<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'type',
        'is_active',
        'priority',
        'start_date',
        'end_date',
        'reward_type',
        'reward_value',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'priority' => 'integer',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'reward_value' => 'decimal:2',
        ];
    }
}

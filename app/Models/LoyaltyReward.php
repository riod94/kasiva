<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyReward extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'stamps_required',
        'discount_amount',
        'is_active',
    ];

    protected $casts = [
        'stamps_required' => 'integer',
        'discount_amount' => 'float',
        'is_active' => 'boolean',
    ];
}

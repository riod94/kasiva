<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyMember extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'phone',
        'stamps_count',
        'total_visits',
    ];

    protected $casts = [
        'stamps_count' => 'integer',
        'total_visits' => 'integer',
    ];
}

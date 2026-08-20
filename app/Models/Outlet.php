<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'qris_image_path',
        'tax_percentage',
        'service_charge_percentage',
    ];

    protected $casts = [
        'tax_percentage' => 'float',
        'service_charge_percentage' => 'float',
    ];
}

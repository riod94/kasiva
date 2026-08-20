<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'receipt_number',
        'payment_method',
        'total_amount',
        'total_hpp',
        'paid_amount',
        'change_amount',
        'platform_discount',
        'platform_markup',
        'is_backdated',
        'transaction_date',
        'variant_summary',
        'cashier_name',
        'sync_status',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'total_hpp' => 'float',
        'paid_amount' => 'float',
        'change_amount' => 'float',
        'platform_discount' => 'float',
        'platform_markup' => 'float',
        'is_backdated' => 'boolean',
        'transaction_date' => 'datetime',
        'variant_summary' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}

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
        'discount_total',
        'discount_note',
        'loyalty_member_id',
        'paid_amount',
        'change_amount',
        'platform_discount',
        'platform_markup',
        'is_backdated',
        'transaction_date',
        'variant_summary',
        'cashier_name',
        'sync_status',
        'client_transaction_id',
        'sync_error',
        'payment_confirmed_manually',
        'status',
        'voided_at',
        'void_reason',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'total_hpp' => 'float',
        'discount_total' => 'float',
        'paid_amount' => 'float',
        'change_amount' => 'float',
        'platform_discount' => 'float',
        'platform_markup' => 'float',
        'payment_confirmed_manually' => 'boolean',
        'is_backdated' => 'boolean',
        'transaction_date' => 'datetime',
        'variant_summary' => 'array',
        'voided_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function loyaltyMember()
    {
        return $this->belongsTo(LoyaltyMember::class, 'loyalty_member_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_name',
        'unit_price',
        'unit_hpp',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'float',
        'unit_hpp' => 'float',
        'quantity' => 'integer',
        'subtotal' => 'float',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

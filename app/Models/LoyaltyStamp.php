<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyStamp extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'loyalty_member_id',
        'program_id',
        'transaction_id',
        'stamps_earned',
        'stamped_at',
    ];

    protected $casts = [
        'stamps_earned' => 'integer',
        'stamped_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(LoyaltyMember::class, 'loyalty_member_id');
    }

    public function program()
    {
        return $this->belongsTo(LoyaltyProgram::class, 'program_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}

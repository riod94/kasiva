<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerReward extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'customer_rewards';

    protected $fillable = [
        'id',
        'loyalty_member_id',
        'program_id',
        'status',
        'available_at',
        'expires_at',
        'claimed_at',
        'claimed_transaction_id',
    ];

    protected $casts = [
        'available_at' => 'datetime',
        'expires_at' => 'datetime',
        'claimed_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(LoyaltyMember::class, 'loyalty_member_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(LoyaltyProgram::class, 'program_id');
    }

    public function claimedTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'claimed_transaction_id');
    }
}

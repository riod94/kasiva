<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignReward extends Model
{
    use HasUuids;

    protected $fillable = ['campaign_id', 'reward_type', 'reward_value'];

    protected $casts = ['reward_value' => 'float'];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}

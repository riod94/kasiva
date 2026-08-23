<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class LoyaltyProgram extends Model {
    use HasUuids;
    protected $table = 'loyalty_programs';
    protected $fillable = ['id','name','target_stamps','min_transaction','expiry_months','reward_type','reward_value','reward_product_id','reward_claim_days','after_claim','excluded_product_ids','allow_with_promo','is_active'];
    protected $casts = [
        'target_stamps'=>'integer','min_transaction'=>'float','expiry_months'=>'integer',
        'reward_value'=>'float','reward_claim_days'=>'integer','allow_with_promo'=>'boolean','is_active'=>'boolean',
        'excluded_product_ids'=>'array',
    ];
    public function rewardProduct(): BelongsTo { return $this->belongsTo(Product::class, 'reward_product_id'); }
    public function scopeActive($q){ return $q->where('is_active', true); }
}

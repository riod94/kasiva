<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class CampaignItem extends Model {
    use HasUuids;
    protected $fillable = ['campaign_id','product_id','quantity','role'];
    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}

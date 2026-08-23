<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class VariantTemplateOption extends Model {
    use HasUuids;
    protected $fillable = ['variant_template_id','name','price_modifier','cogs_modifier'];
    protected $casts = ['price_modifier'=>'float','cogs_modifier'=>'float'];
    public function template(): BelongsTo { return $this->belongsTo(VariantTemplate::class, 'variant_template_id'); }
}

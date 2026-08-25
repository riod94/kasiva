<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariantTemplate extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'selection_type', 'is_required', 'order_index'];

    protected $casts = ['is_required' => 'boolean'];

    public function options(): HasMany
    {
        return $this->hasMany(VariantTemplateOption::class);
    }
}

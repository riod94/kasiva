<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SyncDevice extends Model
{
    use HasUuids;

    protected $fillable = ['id', 'user_id', 'device_name', 'platform', 'last_cursor', 'last_synced_at'];

    protected $casts = ['last_synced_at' => 'datetime'];

    public function queue()
    {
        return $this->hasMany(SyncQueue::class, 'device_id');
    }
}

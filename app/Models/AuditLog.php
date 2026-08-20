<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_name',
        'action',
        'details',
        'ip_address',
    ];

    public static function log(string $action, string $details = '', ?string $userName = null): void
    {
        static::create([
            'user_name' => $userName ?: (auth()->user() ? auth()->user()->name : 'Kasir Utama'),
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }
}

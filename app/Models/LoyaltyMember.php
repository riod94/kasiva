<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LoyaltyMember extends Model
{
    use HasUuids;

    protected $fillable = ['id', 'name', 'phone', 'qr_code', 'status', 'email', 'notes', 'assigned_at', 'stamps_count', 'total_visits'];

    protected $casts = ['stamps_count' => 'integer', 'total_visits' => 'integer', 'assigned_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (LoyaltyMember $m) {
            $rawQr = $m->getAttributes()['qr_code'] ?? null;
            if (empty($rawQr)) {
                $code = 'KSV-MBR-'.strtoupper(Str::random(8));
                // ensure uniqueness in loop (rare collision)
                while (static::where('qr_code', $code)->exists()) {
                    $code = 'KSV-MBR-'.strtoupper(Str::random(8));
                }
                $m->setAttribute('qr_code', $code);
            }
            $rawStatus = $m->getAttributes()['status'] ?? null;
            if (empty($rawStatus)) {
                $m->setAttribute('status', ! empty($m->getAttributes()['name'] ?? null) ? 'ASSIGNED' : 'UNASSIGNED');
            }
            $rawAssigned = $m->getAttributes()['assigned_at'] ?? null;
            if (($m->getAttributes()['status'] ?? 'ASSIGNED') === 'ASSIGNED' && empty($rawAssigned)) {
                $m->setAttribute('assigned_at', now());
            }
        });
    }

    public function getQrCodeAttribute(?string $value): string
    {
        if (! empty($value)) {
            return $value;
        }
        // fallback for legacy rows before backfill / accessor compat
        $raw = $this->attributes['qr_code'] ?? null;
        if (! empty($raw)) {
            return $raw;
        }

        return 'KSV-MBR-'.strtoupper(substr(md5($this->phone ?? $this->id ?? Str::random(8)), 0, 8));
    }

    public function stamps()
    {
        return $this->hasMany(LoyaltyStamp::class);
    }

    public function rewards()
    {
        return $this->hasMany(CustomerReward::class, 'loyalty_member_id');
    }
}

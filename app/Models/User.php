<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'pin',
        'role_id',
        'outlet_id',
        'is_active',
        'must_change_password',
        'must_change_pin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'must_change_pin' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Check if the user is an owner / super admin.
     * HARDENING: empty(role_id) HANYA bypass bila user memang tanpa outlet/role (bootstrap),
     * bukan akun aktif. is_active=false tidak pernah owner.
     */
    public function isOwner(): bool
    {
        if ($this->is_active === false) {
            return false;
        }
        if ($this->role?->slug === 'owner') {
            return true;
        }
        // Legacy bypass: akun tanpa role dianggap owner HANYA jika outlet_id juga kosong/null (akun bootstrap awal)
        // Jika outlet_id terisi tapi role_id kosong -> data rusak -> jangan bypass, anggap bukan owner
        if (empty($this->role_id) && empty($this->outlet_id)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string|array $role): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        if (! $this->role) {
            return false;
        }

        if (is_array($role)) {
            return in_array($this->role->slug, $role);
        }

        return $this->role->slug === $role;
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Owner has absolute bypass
        if ($this->isOwner()) {
            return true;
        }

        if (! $this->role) {
            return false;
        }

        return $this->role->hasPermission($permissionSlug);
    }

    /**
     * Alias for hasPermission
     */
    public function canAccess(string $permissionSlug): bool
    {
        return $this->hasPermission($permissionSlug);
    }
}

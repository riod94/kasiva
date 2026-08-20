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
     * Check if the user is an owner / super admin
     */
    public function isOwner(): bool
    {
        return $this->role?->slug === 'owner' || empty($this->role_id);
    }

    /**
     * Check if user has a specific role
     *
     * @param string|array $role
     */
    public function hasRole(string|array $role): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        if (!$this->role) {
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

        if (!$this->role) {
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

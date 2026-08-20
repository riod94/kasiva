<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
                    ->using(RolePermission::class)
                    ->withTimestamps();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->slug === 'owner') {
            return true;
        }

        return $this->permissions->contains('slug', $permissionSlug);
    }

    /**
     * Sync permissions by array of slugs
     */
    public function syncPermissions(array $permissionSlugs): void
    {
        $permissionIds = Permission::whereIn('slug', $permissionSlugs)->pluck('id');
        $this->permissions()->sync($permissionIds);
    }

    /**
     * Give permission to role
     */
    public function givePermissionTo(string|Permission $permission): void
    {
        $permissionModel = is_string($permission)
            ? Permission::firstOrCreate(['slug' => $permission], ['name' => ucwords(str_replace('_', ' ', strtolower($permission)))])
            : $permission;

        if (!$this->permissions()->where('permissions.id', $permissionModel->id)->exists()) {
            $this->permissions()->attach($permissionModel->id);
        }
    }
}

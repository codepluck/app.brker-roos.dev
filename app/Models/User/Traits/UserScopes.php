<?php

namespace App\Models\User\Traits;

trait UserScopes
{
    /**
     * Scope to get active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope to get users by role.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByRole($query, string $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        });
    }

    /**
     * Scope to get users with a specific permission.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $permission
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithPermission($query, string $permission)
    {
        return $query->whereHas('permissions', function ($q) use ($permission) {
            $q->where('name', $permission);
        })->orWhereHas('roles.permissions', function ($q) use ($permission) {
            $q->where('name', $permission);
        });
    }
}

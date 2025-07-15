<?php

namespace App\Models\User\Traits;

trait UserAccess
{
    /**
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRoleAccess(string $role): bool
    {
        return $this->hasRole($role);
    }

    /**
     * Check if the user has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermissionAccess(string $permission): bool
    {
        return $this->can($permission);
    }

    /**
     * Check if the user is a broker managing a team member.
     *
     * @param \App\Models\User\User $teamMember
     * @return bool
     */
    public function isBrokerForTeamMember(self $teamMember): bool
    {
        return $this->hasRole('broker') && $teamMember->created_by === $this->id;
    }
}
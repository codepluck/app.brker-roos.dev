<?php

namespace App\Models\User\Traits;

use App\Models\User\Profile;

trait UserRelationships
{
    /**
     * Get the profile associated with the user.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the user who created this user.
     */
    public function creator()
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    /**
     * Get the user who updated this user.
     */
    public function updater()
    {
        return $this->belongsTo(self::class, 'updated_by');
    }

    /**
     * Get the user who deleted this user.
     */
    public function deleter()
    {
        return $this->belongsTo(self::class, 'deleted_by');
    }

    /**
     * Get the team members managed by this broker.
     */
    public function teamMembers()
    {
        return $this->hasMany(self::class, 'created_by')->whereHas('roles', function ($query) {
            $query->where('name', 'team_member');
        });
    }

    /**
     * Get the broker who manages this team member.
     */
    public function broker()
    {
        return $this->belongsTo(self::class, 'created_by');
    }
}
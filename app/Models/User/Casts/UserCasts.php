<?php

namespace App\Models\User\Casts;

class UserCasts
{
    /**
     * Get the castable attributes for the User model.
     *
     * @return array<string, string>
     */
    public static function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login' => 'datetime',
            'status' => 'boolean', // 1 = true, 0 = false
            'deleted_at' => 'datetime', // For soft deletes
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }
}

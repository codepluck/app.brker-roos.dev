<?php

namespace App\Models\User\Traits;

use App\Models\User\Casts\UserCasts;

trait UserAttributes
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return UserCasts::casts();
    }

    /**
     * Get the full name attribute.
     *
     * @return string|null
     */
    public function getFullNameAttribute(): ?string
    {
        return $this->profile ? "{$this->profile->first_name} {$this->profile->last_name}" : $this->name;
    }

    /**
     * Generate a new API key.
     *
     * @return string
     */
    public function generateApiKey(): string
    {
        $this->_key = \Illuminate\Support\Str::random(60);
        $this->save();
        return $this->_key;
    }
}

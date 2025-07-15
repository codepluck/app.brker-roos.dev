<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'username',
        'mobile',
        'gender',
        'date_of_birth',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
        'postal_code',
        'bio',
        'social_profiles',
        'avatar',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'social_profiles' => 'array', // JSON cast to array
        ];
    }

    /**
     * Get the user associated with the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

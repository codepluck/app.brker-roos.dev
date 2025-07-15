<?php

namespace App\Models\User;

use App\Models\User\Traits\UserAccess;
use App\Models\User\Traits\UserAttributes;
use App\Models\User\Traits\UserRelationships;
use App\Models\User\Traits\UserScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles, UserAccess, UserAttributes, UserRelationships, UserScopes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        '_key',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'status',
        'last_ip',
        'login_count',
        'last_login',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        '_key', // Hide sensitive API key
    ];
}
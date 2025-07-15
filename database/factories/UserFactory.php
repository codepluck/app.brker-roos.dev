<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            '_key' => Str::random(60), // Random string for API key or token
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => $this->faker->optional(0.8)->dateTimeThisYear(),
            'password' => Hash::make('password'), // Default password for testing
            'remember_token' => Str::random(10),
            'status' => $this->faker->randomElement([0, 1]),
            'role_id' => 1, // Default to role_id 1 (e.g., client); overridden in state
            'last_ip' => $this->faker->optional(0.9)->ipv4(),
            'login_count' => $this->faker->numberBetween(0, 100),
            'last_login' => $this->faker->optional(0.9)->dateTimeThisYear(),
            'created_by' => null, // Set in configure() for existing users
            'updated_by' => null,
            'deleted_by' => null,
            'created_at' => $this->faker->dateTimeThisYear(),
            'updated_at' => $this->faker->dateTimeThisYear(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            // Create associated profile
            Profile::factory()->create(['user_id' => $user->id]);

            // Assign Spatie role (admin, broker, client, team_member)
            $roles = ['admin', 'broker', 'client', 'team_member'];
            $role = Role::firstOrCreate(['name' => $this->faker->randomElement($roles)]);
            $user->assignRole($role->name);

            // Set created_by/updated_by to a random existing user (if available)
            if ($existingUser = User::inRandomOrder()->first()) {
                $user->update([
                    'created_by' => $existingUser->id,
                    'updated_by' => $existingUser->id,
                ]);
            }
        });
    }

    // Custom state for specific role
    public function withRole(string $role)
    {
        return $this->state(function (array $attributes) use ($role) {
            $roleModelumbre = Role::firstOrCreate(['name' => $role]);
            return ['role_id' => $roleModel->id];
        });
    }
}

<?php

namespace Database\Factories;

use App\Models\User\PasswordHistory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class PasswordHistoryFactory extends Factory
{
    protected $model = PasswordHistory::class;

    public function definition(): array
    {
        return [
            'user_id' => null, // Set in seeder
            'password' => Hash::make($this->faker->password()),
            'changed_at' => $this->faker->dateTimeThisYear(),
        ];
    }
}

<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // Default password
            'profile_picture' => $this->faker->imageUrl(200, 200, 'people'),
            'bio' => $this->faker->sentence(),
            'role' => 'user',
            'is_active' => $this->faker->boolean(90), // 90% chance to be active
            'remember_token' => Str::random(10),
        ];
    }
}

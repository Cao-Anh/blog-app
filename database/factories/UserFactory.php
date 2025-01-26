<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        $portrait=['portrait1.jpg','portrait2.jpg','portrait3.jpg','portrait4.jpg','default-image.png'];
        $imageName = $portrait[array_rand($portrait)];
                return [
            'name' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // Default password
            'profile_picture' => 'users/'.$imageName,
            'bio' => $this->faker->sentence(),
            'role' => 'user',
            'is_active' => $this->faker->boolean(90), // 90% chance to be active
            'remember_token' => Str::random(10),
        ];
    }
}

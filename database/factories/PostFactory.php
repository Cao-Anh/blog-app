<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(10),
            'user_id' => User::factory(),
            'likes' => $this->faker->numberBetween(0, 100),
        ];
    }
}

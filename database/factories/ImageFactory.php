<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;

class ImageFactory extends Factory
{
    public function definition(): array
{
    // List of local placeholder images
    $placeholders = ['placeholder1.jpg', 'placeholder2.jpg', 'placeholder3.jpg'];

    // Randomly select a placeholder
    $imageName = $placeholders[array_rand($placeholders)];

    return [
        'path' => 'posts/' . $imageName, // Store the path to the placeholder
        'post_id' => Post::factory(),
    ];
}
}
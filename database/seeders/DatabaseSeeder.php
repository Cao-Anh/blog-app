<?php

// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create 10 users
        \App\Models\User::factory(10)->create()->each(function ($user) {
            // Create 3 posts for each user
            \App\Models\Post::factory(3)->create(['user_id' => $user->id])->each(function ($post) use ($user) {
                // Create 5 comments for each post
                \App\Models\Comment::factory(5)->create([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                ]);

                // Add likes for each post
                $post->likes()->createMany(
                    \App\Models\User::inRandomOrder()->take(rand(1, 5))->get()->map(fn($user) => ['user_id' => $user->id])->toArray()
                );
            });
        });
    }
}


<?php
namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Create 10 tags
        Tag::factory(10)->create();
        // Create 10 users
        User::factory(10)->create()->each(function ($user) {
            // Create 3 posts for each user
            Post::factory(3)->create(['user_id' => $user->id])->each(function ($post) use ($user) {
                // Create 5 comments for each post
                Comment::factory(5)->create([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                ]);

                // Add likes for each post
                $post->likes()->createMany(
                    User::inRandomOrder()->take(rand(1, 5))->get()->map(fn($user) => ['user_id' => $user->id])->toArray()
                );

                // Attach 1 to 3 random tags to each post
                $tags = Tag::inRandomOrder()->take(rand(1, 3))->pluck('id');
                $post->tags()->attach($tags);
            });
        });

        
    }
}
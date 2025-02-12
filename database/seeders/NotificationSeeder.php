<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Notification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        
        $likes = Like::all();
        foreach ($likes as $like) {
            $post = $like->post;
            $user = $like->user;

            // Create a notification for the post owner
            if ($post->user_id !== $user->id) { // Don't notify if the user is liking their own post
                Notification::create([
                    'user_id' => $post->user_id, // Notify the post owner
                    'post_id' => $post->id,      // The post being liked
                    'type' => 'like',            // Notification type
                    'user_name' => $user->name,  // Name of the user who liked the post
                    'read' => false,             // Mark as unread
                ]);
            }
        }

        // Seed notifications for comments
        $comments = Comment::all();
        foreach ($comments as $comment) {
            $post = $comment->post;
            $user = $comment->user;

            // Create a notification for the post owner
            if ($post->user_id !== $user->id) { // Don't notify if the user is commenting on their own post
                Notification::create([
                    'user_id' => $post->user_id, // Notify the post owner
                    'post_id' => $post->id,       // The post being commented on
                    'type' => 'comment',         // Notification type
                    'user_name' => $user->name,  // Name of the user who commented
                    'read' => false,             // Mark as unread
                ]);
            }
        }
    }
}
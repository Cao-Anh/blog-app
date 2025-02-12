<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\Notification;
use App\Events\PostLiked; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggleLike(Post $post)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'You must be logged in to like a post.'], 401);
        }

        $user = Auth::user();
        $like = Like::where('post_id', $post->id)->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            return response()->json(['message' => 'Post unliked successfully.', 'liked' => false]);
        } else {
            
            Like::create([
                'post_id' => $post->id,
                'user_id' => $user->id,
            ]);

            
            if ($post->user_id !== Auth::id()) { 
                $notification=Notification::create([
                    'user_id' => $post->user_id, 
                    'post_id' => $post->id,      
                    'type' => 'like',            
                    'user_name' => Auth::user()->name, 
                    'read' => false,             
                ]);
            }

            event(new PostLiked($notification));

            return response()->json(['message' => 'Post liked successfully.', 'liked' => true]);
        }
    }
}
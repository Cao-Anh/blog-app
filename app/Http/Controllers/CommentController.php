<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Notification;
use App\Events\CommentAdded; // Import the CommentAdded event
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        // Create the comment
        $comment = $post->comments()->create([
            'content' => $request->input('content'),
            'user_id' => Auth::id(),
        ]);

        // Load the user relationship for the comment
        $comment->load('user');

        // Create a notification for the post author
        if ($post->user_id !== Auth::id()) { 
            $notification=Notification::create([
                'user_id' => $post->user_id,
                'post_id' => $post->id,      
                'type' => 'comment',        
                'user_name' => Auth::user()->name, 
                'read' => false,           
            ]);
        }

        // Trigger the CommentAdded event
        event(new CommentAdded($notification));

        if ($request->has('redirect')) {
            session()->put('url.intended', $request->input('redirect'));
        }
        
        return response()->json([
            'comment' => $comment,
            
            'message' => 'Comment added successfully!',
        ]);
    }

    // Delete a comment
    public function destroy(Comment $comment)
    {
        if (Auth::user() && (Auth::user()->role === 'admin' || $comment->user_id === Auth::id() || $comment->post->user_id === Auth::id())) {
            $comment->delete();
            return response()->json(['message' => 'Comment deleted successfully!'], 200);
        }

        return response()->json(['error' => 'You are not authorized to delete this comment.'], 403);
    }
}

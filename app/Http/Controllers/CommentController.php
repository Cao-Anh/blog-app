<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // Store a new comment
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $post->comments()->create([
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Comment added successfully!');
    }

    // Delete a comment
    public function destroy(Comment $comment)
    {
        // Check if the authenticated user is the author of the comment, the author of the post, or an admin
        if (Auth::user()->role === 'admin' || $comment->user_id === Auth::id() || $comment->post->user_id === Auth::id()){
            $comment->delete();
            return redirect()->back()->with('success', 'Comment deleted successfully!');
        }

        // If the user is not authorized
        return redirect()->back()->with('error', 'You are not authorized to delete this comment.');
    }
}
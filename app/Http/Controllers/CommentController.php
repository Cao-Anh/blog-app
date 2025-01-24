<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
{
    $request->validate([
        'content' => 'required|string|max:500',
    ]);

    $comment = $post->comments()->create([
        'content' => $request->input('content'),
        'user_id' => Auth::id(),
    ]);

    $comment->load('user');

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
        if (Auth::user()&&(Auth::user()->role === 'admin' || $comment->user_id === Auth::id() || $comment->post->user_id === Auth::id())){
            $comment->delete();
            return redirect()->back()->with('success', 'Comment deleted successfully!');
        }

        return redirect()->back()->with('error', 'You are not authorized to delete this comment.');
    }
}
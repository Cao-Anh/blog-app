<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('author')->latest()->get();
        return Inertia::render('Posts/Index', ['posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Posts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('posts.index')->with('success', 'Post created successfully!');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post, User $user)
    {
        return Inertia::render('Posts/Edit', [
            'user' => $user,
            'post' => $post,
            'errors' => session()->get('errors') ? session()->get('errors')->getBag('default')->getMessages() : [],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        if (Auth::user()->role === 'admin' || $post->user_id === Auth::id()) {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);

            return redirect()->route('posts.index')->with([
                'message' => 'Post edited successfully.',
                'type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'message' => 'You are not authorized to delete this post.',
            'type' => 'error'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        // if (Auth::user()->role === 'admin' || $post->user_id === Auth::id()) {
        //     $post->delete();
        //     return response()->json([
        //         'message' => 'Post deleted successfully.',
        //         'type' => 'success',
        //     ], 200); 
        // }

        // return response()->json([
        //     'message' => 'You are not authorized to delete this post.',
        //     'type' => 'error',
        // ], 403); 

        // {
        //     if (Auth::user()->role === 'admin' || $post->user_id === Auth::id()) {
        //         $post->delete();
        //         return response()->noContent(); // 204 status code (success)
        //     }

        //     return response()->json(['error' => 'You are not authorized to delete this post.'], 403); // 403 status code (error)
        // }


        if (Auth::user()->role === 'admin' || $post->author_id === Auth::id()) {
            $post->delete();
            return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
        }

        return redirect()->back()->with('error', 'You are not authorized to delete this post.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
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
        $posts = Post::with(['author', 'comments.user', 'likes', 'tags'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Posts/Index', [
            'posts' => $posts,
        ]);
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

        return redirect()->route('home')->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['author', 'comments.user', 'likes', 'tags']);

        return Inertia::render('Posts/Show', [
            'post' => $post,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        if (Auth::user() && (Auth::user()->role === 'admin' || $post->user_id === Auth::id())) {
            $post->load('tags');
            return Inertia::render('Posts/Edit', [
                'post' => $post,
                'errors' => session()->get('errors') ? session()->get('errors')->getBag('default')->getMessages() : [],
                'allTags' => Tag::all(),
            ]);
        }

        return redirect()->back()->with('error', 'You are not authorized to edit this post.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'array',
            'tags.*' => 'string|max:255',
        ]);

        $post->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        $tags = collect($request->input('tags'))->map(function ($tagName) {
            return Tag::firstOrCreate(['name' => $tagName])->id;
        });

        $post->tags()->sync($tags);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (Auth::user() && (Auth::user()->role === 'admin' || $post->user_id === Auth::id())) {
            $post->delete();
            return redirect()->route('home')->with('success', 'Post deleted successfully.');
        }
        return redirect()->back()->with('error', 'You are not authorized to delete this post.');
    }

    /**
     * Display all posts by a specific user.
     */
    public function userPosts(User $user)
    {
        $posts = $user->posts()->with(['author', 'comments.user', 'likes', 'tags'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Posts/UserPosts', [
            'posts' => $posts,
            'user' => $user,
        ]);
    }

    /**
     * Display all posts with a specific tag.
     */
    public function tagPosts(Tag $tag)
    {
        $posts = $tag->posts()->with(['author', 'comments.user', 'likes', 'tags'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Posts/TagPosts', [
            'posts' => $posts,
            'tag' => $tag,
        ]);
    }
}
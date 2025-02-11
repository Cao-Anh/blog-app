<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with(['author', 'tags', 'images', 'comments.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $allTags = Tag::all();
        $tag = $request->query('tag');
        // Return JSON for API requests
        if (request()->wantsJson()) {
            return response()->json([
                'data' => $posts->items(),
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ]);
        }

        // Return Inertia response for non-API requests
        return Inertia::render('Posts/Index', [
            'posts' => $posts,
            'allTags' => $allTags,
            'tag'=> $tag
        ]);
    }
    public function create()
    {
        return Inertia::render('Posts/Create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('posts', 'public');
                $post->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('home')->with('success', 'Post created successfully!');
    }

    public function show(Post $post)
    {
        $posts = Post::with(['author', 'tags', 'images', 'comments.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $allTags = Tag::all();
        $post->load(['author', 'comments.user', 'likes', 'tags', 'images']);
        // dd($post, $posts, $allTags);
        return Inertia::render('Posts/Show', [
            'post' => $post,
            'posts'=>$posts,
            'allTags'=>$allTags
        ]);
    }

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

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'array',
            'tags.*' => 'string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $post->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        $tags = collect($request->input('tags'))->map(function ($tagName) {
            return Tag::firstOrCreate(['name' => $tagName])->id;
        });
        $post->tags()->sync($tags);

        if ($request->hasFile('images')) {
            foreach ($post->images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
            foreach ($request->file('images') as $image) {
                $path = $image->store('posts', 'public');
                $post->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        if (Auth::user() && (Auth::user()->role === 'admin' || $post->user_id === Auth::id())) {
            foreach ($post->images as $image) {
                Storage::disk('public')->delete($image->path);
            }
            $post->delete();
            return redirect()->route('home')->with('success', 'Post deleted successfully.');
        }

        return redirect()->back()->with('error', 'You are not authorized to delete this post.');
    }

    public function userPosts(User $user)
    {
        $posts = $user->posts()->with(['author', 'comments.user', 'likes', 'tags', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $allTags = Tag::all();
        // Return JSON for API requests
        if (request()->wantsJson()) {
            return response()->json([
                'data' => $posts->items(),
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ]);
        }

        // Return Inertia response for non-API requests
        return Inertia::render('Posts/UserPosts', [
            'posts' => $posts,
            'user' => $user,
            'allTags' => $allTags,
        ]);
    }
    public function tagUserPosts(User $user,Tag $tag)
{
    $posts = $user->posts()
        ->whereHas('tags', function ($query) use ($tag) {
            $query->where('tags.id', $tag->id);
        })
        ->with(['author', 'comments.user', 'likes', 'tags', 'images'])
        ->orderBy('created_at', 'desc')
        ->paginate(5);

    $allTags = Tag::all();

    // If the request is an API call (e.g., from Vue axios)
    if (request()->wantsJson()) {
        return response()->json([
            'data' => $posts->items(),
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'per_page' => $posts->perPage(),
            'total' => $posts->total(),
        ]);
    }

    // If the request is from Inertia.js, return the Inertia response
    return Inertia::render('Posts/UserPosts', [
        'posts' => $posts,
        'allTags' => $allTags,
        'tag' => $tag,
        'user' => $user
    ]);
}


    public function tagPosts(Tag $tag)
{
    $posts = $tag->posts()->with(['author', 'comments.user', 'likes', 'tags', 'images'])
        ->orderBy('created_at', 'desc')
        ->paginate(5);

    $allTags = Tag::all();

    // If the request is an API call (e.g., from Vue axios)
    if (request()->wantsJson()) {
        return response()->json([
            'data' => $posts->items(),
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'per_page' => $posts->perPage(),
            'total' => $posts->total(),
        ]);
    }

    // If the request is from Inertia.js, return the Inertia response
    return Inertia::render('Posts/Index', [
        'posts' => $posts,
        'allTags' => $allTags,
        'tag'=> $tag
         
    ]);
}

    // public function index()
    // {
    //     $posts = Post::with(['author', 'tags', 'images', 'comments.user'])
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(5);

    //     $allTags = Tag::all();

    //     // Return JSON for API requests
    //     if (request()->wantsJson()) {
    //         return response()->json([
    //             'data' => $posts->items(),
    //             'current_page' => $posts->currentPage(),
    //             'last_page' => $posts->lastPage(),
    //             'per_page' => $posts->perPage(),
    //             'total' => $posts->total(),
    //         ]);
    //     }

    //     // Return Inertia response for non-API requests
    //     return Inertia::render('Posts/Index', [
    //         'posts' => $posts,
    //         'allTags' => $allTags,
    //     ]);
    // }
}

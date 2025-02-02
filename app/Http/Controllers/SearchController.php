<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
{
    $query = $request->input('query');
    
    $posts = Post::where('title', 'like', "%{$query}%")
        ->orWhere('content', 'like', "%{$query}%")
        ->orWhereHas('author', function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%");
        })
        ->orWhereHas('tags', function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%");
        })
        ->with(['author', 'comments.user', 'likes', 'tags', 'images']) 
        ->paginate(10); 
    
    return response()->json($posts);
}
}

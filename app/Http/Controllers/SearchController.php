<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
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
public function searchSingleUser(Request $request, User $user)
{
    $query = $request->input('query');
    
    $posts = Post::where('user_id', $user->id) 
        ->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
                ->orWhereHas('tags', function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                });
        })
        ->with(['author', 'comments.user', 'likes', 'tags', 'images'])
        ->paginate(10);

    return response()->json($posts);
}

}

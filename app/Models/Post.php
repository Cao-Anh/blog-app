<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory;

    // Fillable fields for mass assignment
    protected $fillable = [
        'title',
        'content',
        'user_id',
        
    ];

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }


    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id')->orderBy('created_at', 'desc');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'post_id');
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function getIsLikedAttribute()
    {
        if (Auth::check()) {
            return $this->likes()->where('user_id', Auth::id())->exists();
        }
        return false;
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    protected $appends = ['is_liked', 'likes_count'];
}

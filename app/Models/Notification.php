<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',       
        'post_id',      
        'type',         
        'user_name',   
        'read',             
    ];

    /**
     * Get the user who receives the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post related to the notification.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
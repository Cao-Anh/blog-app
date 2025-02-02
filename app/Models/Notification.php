<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'notifiable_id',
        'notifiable_type',
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
     * Get the related entity (e.g., Post or Comment).
     */
    public function notifiable()
    {
        return $this->morphTo();
    }
}

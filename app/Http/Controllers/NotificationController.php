<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller

{
    public function index(Request $request)
    {
        $userId = $request->query('user_id');

        
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    public function markAsRead(Request $request)
    { $userId = $request->input('user_id');

        Notification::where('user_id', $userId)->update(['read' => true]);
    
        return response()->json(['message' => 'Notifications marked as read']);
    }
}
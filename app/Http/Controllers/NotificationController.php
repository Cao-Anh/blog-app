<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller

{
    public function index(Request $request)
    {
        // Ensure the user is authenticated
        $user = Auth::user();
        

        $page = $request->query('page', 1); 
        $perPage = $request->query('per_page', 5); 

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($notifications);
    }

    public function markAsRead(Request $request)
    { $userId = $request->input('user_id');

        Notification::where('user_id', $userId)->update(['read' => true]);
    
        return response()->json(['message' => 'Notifications marked as read']);
    }
}
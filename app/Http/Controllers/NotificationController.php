<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Fetch recent notifications for AJAX dropdown
    public function fetch(Request $request)
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->take(20)->get();
        return response()->json(['notifications' => $notifications]);
    }

    // Mark all notifications as read
    public function markAllRead(Request $request)
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    // View all notifications page
    public function index(Request $request)
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(30);
        return view('notifications.index', compact('notifications'));
    }
} 
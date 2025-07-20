<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Get recipients sorted by last message time, with unread counts
    public function getRecipients()
    {
        $user = Auth::user();
        $role = $user->getPrimaryRoleName();
        $userId = $user->id;

        // Get all possible recipients
        if ($role === 'admin') {
            $recipients = User::where('id', '!=', $user->id)->get();
        } elseif ($role === 'supplier' || $role === 'retailer') {
            $recipients = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['admin', 'vendor']);
            })->get();
        } elseif ($role === 'vendor') {
            $recipients = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['admin', 'supplier', 'retailer', 'employee']);
            })->get();
        } elseif ($role === 'employee') {
            $recipients = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['admin', 'vendor']);
            })->get();
        } else {
            $recipients = collect();
        }

        // Get unread counts per user
        $unreadCounts = ChatMessage::where('receiver_id', $userId)
            ->where('is_read', false)
            ->selectRaw('sender_id, count(*) as unread_count')
            ->groupBy('sender_id')
            ->pluck('unread_count', 'sender_id');

        // Sort recipients by last message time (most recent first)
        $recipients = $recipients->map(function($recipient) use ($userId, $unreadCounts) {
            $recipient->profile_photo_url = $recipient->profile_photo_url;
            $recipient->unread_count = $unreadCounts[$recipient->id] ?? 0;
            $recipient->last_message_time = ChatMessage::where(function($q) use ($userId, $recipient) {
                $q->where('sender_id', $userId)->where('receiver_id', $recipient->id);
            })->orWhere(function($q) use ($userId, $recipient) {
                $q->where('sender_id', $recipient->id)->where('receiver_id', $userId);
            })->orderBy('created_at', 'desc')->value('created_at');
            return $recipient;
        })->sortByDesc(function($recipient) {
            return $recipient->last_message_time ? strtotime($recipient->last_message_time) : 0;
        })->values();

        return response()->json($recipients);
    }

    // Send a message
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }

    // Get all messages between two users
    public function getMessages(Request $request)
    {
        $withUserId = $request->with_user_id;
        $userId = Auth::id();

        $request->validate([
            'with_user_id' => 'required|exists:users,id',
        ]);

        $messages = ChatMessage::where(function($q) use ($userId, $withUserId) {
                $q->where('sender_id', $userId)->where('receiver_id', $withUserId);
            })->orWhere(function($q) use ($userId, $withUserId) {
                $q->where('sender_id', $withUserId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        ChatMessage::where('sender_id', $withUserId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json($messages);
    }

    // Mark all unread messages as read for the authenticated user
    public function markAllAsRead()
    {
        ChatMessage::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    // Mark messages from a specific user as read
    public function markRead(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        ChatMessage::where('sender_id', $request->user_id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    // Get unread message counts per user
    public function getUnreadCountsPerUser()
    {
        $userId = Auth::id();
        $counts = ChatMessage::where('receiver_id', $userId)
            ->where('is_read', false)
            ->selectRaw('sender_id, count(*) as unread_count')
            ->groupBy('sender_id')
            ->pluck('unread_count', 'sender_id');
        return response()->json($counts);
    }

    // Get total unread count for chat icon badge
    public function getUnreadTotal()
    {
        $count = ChatMessage::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();
        return response()->json(['unread_count' => $count]);
    }

    // Chat page
    public function index()
    {
        return view('chat');
    }
}

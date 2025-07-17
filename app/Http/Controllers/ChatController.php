<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Get recipients based on role
    public function getRecipients()
    {
        $user = Auth::user();
        $role = $user->getPrimaryRoleName();

        if ($role === 'admin') {
            // Admin can chat with everyone except self
            $recipients = User::where('id', '!=', $user->id)->get();
        } elseif ($role === 'supplier') {
            // Supplier can chat with admin and vendor only
            $recipients = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['admin', 'vendor']);
            })->get();
        } elseif ($role === 'retailer') {
            // Retailer can chat with admin and vendor only
            $recipients = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['admin', 'vendor']);
            })->get();
        } elseif ($role === 'vendor') {
            // Vendor can chat with admin, supplier, retailer, employee
            $recipients = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['admin', 'supplier', 'retailer', 'employee']);
            })->get();
        } elseif ($role === 'employee') {
            // Employee can chat with admin and vendor only
            $recipients = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['admin', 'vendor']);
            })->get();
        } else {
            $recipients = collect();
        }

        // Ensure profile_photo_url is included in the response and order by latest chat
        $userId = $user->id;
        $recipients = $recipients->map(function($user) {
            $user->profile_photo_url = $user->profile_photo_url;
            return $user;
        })->sortByDesc(function($recipient) use ($userId) {
            $latestMessage = \App\Models\ChatMessage::where(function($q) use ($userId, $recipient) {
                $q->where('sender_id', $userId)->where('receiver_id', $recipient->id);
            })->orWhere(function($q) use ($userId, $recipient) {
                $q->where('sender_id', $recipient->id)->where('receiver_id', $userId);
            })->orderBy('created_at', 'desc')->first();
            return $latestMessage ? strtotime($latestMessage->created_at) : 0;
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

    // Send a file message
    public function sendFileMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'file' => 'required|file|max:10240', // max 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('chat_files', 'public');

        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->input('message', '') ?? '',
            'is_read' => false,
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'original_name' => $file->getClientOriginalName(),
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }

    // Get unread message count for notification
    public function getUnreadCount()
    {
        $count = ChatMessage::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    // Get messages between two users
    public function getMessages(Request $request)
    {
        $withUserId = $request->with_user_id;
        $userId = Auth::id();

        if ($withUserId === 'all') {
            // Return all messages where the user is the receiver
            $messages = ChatMessage::where('receiver_id', $userId)->get();
            return response()->json($messages);
        }

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

    // Get unread message counts per user
    public function getUnreadCountsPerUser()
    {
        $userId = Auth::id();
        $counts = \App\Models\ChatMessage::where('receiver_id', $userId)
            ->where('is_read', false)
            ->selectRaw('sender_id, count(*) as unread_count')
            ->groupBy('sender_id')
            ->pluck('unread_count', 'sender_id');
        return response()->json($counts);
    }

    // Get unread chat messages grouped by sender (for notification dropdown)
    public function getUnreadMessagesGroupedBySender()
    {
        $userId = Auth::id();
        $unreadMessages = ChatMessage::where('receiver_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();

        $grouped = $unreadMessages->groupBy('sender_id')->map(function($messages, $senderId) {
            $sender = User::find($senderId);
            return [
                'sender_id' => $senderId,
                'sender_name' => $sender ? $sender->name : 'Unknown',
                'sender_avatar' => $sender ? $sender->profile_photo_url : null,
                'unread_count' => $messages->count(),
                'latest_message' => $messages->first()->message,
                'latest_message_time' => $messages->first()->created_at->toDateTimeString(),
            ];
        })->values();

        return response()->json($grouped);
    }

    // Get the authenticated user's chat background
    public function getChatBackground()
    {
        $user = Auth::user();
        return response()->json(['chat_background' => $user->chat_background]);
    }

    // Set the authenticated user's chat background
    public function setChatBackground(Request $request)
    {
        $request->validate([
            'chat_background' => 'nullable|string|max:65535',
        ]);
        $user = Auth::user();
        $user->chat_background = $request->chat_background;
        $user->save();
        return response()->json(['success' => true, 'chat_background' => $user->chat_background]);
    }

    // Securely serve a chat file if the user is sender or receiver
    public function downloadChatFile($id)
    {
        $userId = Auth::id();
        $msg = ChatMessage::findOrFail($id);
        if ($msg->sender_id !== $userId && $msg->receiver_id !== $userId) {
            abort(403, 'Unauthorized');
        }
        if (!$msg->file_path) {
            abort(404, 'File not found');
        }
        $storagePath = storage_path('app/public/' . $msg->file_path);
        if (!file_exists($storagePath)) {
            abort(404, 'File not found');
        }
        return response()->download($storagePath, $msg->original_name);
    }

    public function index()
    {
        return view('chat');
    }
} 
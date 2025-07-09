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
} 
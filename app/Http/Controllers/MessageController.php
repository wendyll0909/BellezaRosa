<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Notification;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    // Get conversations for the current user
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get distinct users that the current user has conversations with
        $conversations = Message::select('sender_id', 'receiver_id')
            ->where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->groupBy('sender_id', 'receiver_id')
            ->with(['sender', 'receiver'])
            ->get()
            ->map(function($message) use ($user) {
                $otherUser = $message->sender_id == $user->id ? $message->receiver : $message->sender;
                $lastMessage = Message::betweenUsers($user->id, $otherUser->id)
                    ->latest()
                    ->first();
                $unreadCount = Message::betweenUsers($user->id, $otherUser->id)
                    ->where('receiver_id', $user->id)
                    ->unread()
                    ->count();
                
                return [
                    'user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                    'last_message_time' => $lastMessage ? $lastMessage->created_at : null
                ];
            })
            ->sortByDesc('last_message_time')
            ->values();

        return view('messages.index', compact('conversations'));
    }

    // Get messages between current user and another user
    public function getConversation(User $user)
    {
        $currentUser = Auth::user();
        
        // Mark messages as read
        Message::betweenUsers($currentUser->id, $user->id)
            ->where('receiver_id', $currentUser->id)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        $messages = Message::betweenUsers($currentUser->id, $user->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Get common appointments for reference
        $appointments = Appointment::where(function($query) use ($currentUser, $user) {
            $query->where('customer_id', $currentUser->customer?->id)
                  ->orWhere('staff_id', $currentUser->staff?->id);
        })
        ->where(function($query) use ($currentUser, $user) {
            $query->where('customer_id', $user->customer?->id)
                  ->orWhere('staff_id', $user->staff?->id);
        })
        ->whereIn('status', ['scheduled', 'confirmed', 'in_progress'])
        ->orderBy('start_datetime', 'desc')
        ->get();

        return view('messages.conversation', compact('messages', 'user', 'appointments'));
    }

    // Send a message
    public function sendMessage(Request $request, User $receiver)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'appointment_id' => 'nullable|exists:appointments,id'
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiver->id,
            'appointment_id' => $request->appointment_id,
            'message' => $request->message
        ]);

        // Create notification for receiver
        Notification::createNotification(
            $receiver->id,
            'message',
            [
                'sender_id' => Auth::id(),
                'sender_name' => Auth::user()->full_name,
                'message_preview' => substr($request->message, 0, 100) . (strlen($request->message) > 100 ? '...' : ''),
                'message_id' => $message->id
            ]
        );

        // If this is an AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
                'html' => view('messages.partials.message', ['message' => $message])->render()
            ]);
        }

        return redirect()->back()->with('success', 'Message sent!');
    }

    // Get unread messages count (for notification bell)
    public function getUnreadCount()
    {
        $user = Auth::user();
        return response()->json([
            'unread_messages' => $user->unread_messages_count,
            'unread_notifications' => $user->unread_notifications_count,
            'total_unread' => $user->total_unread_count
        ]);
    }

    // Mark all messages as read
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        Message::where('receiver_id', $user->id)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        Notification::where('user_id', $user->id)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json(['success' => true]);
    }

    // Get notifications
    public function getNotifications()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('messages.notifications', compact('notifications'));
    }
}
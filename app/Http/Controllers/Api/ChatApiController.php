<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ChatSession;
use App\Models\ChatMessage;

class ChatApiController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'session_id'  => 'required|string',
            'receiver_id' => 'nullable|exists:users,id',
            'message'     => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if (!$request->message && !$request->hasFile('image')) {
            return response()->json(['success' => false, 'message' => 'Message or image is required.'], 400);
        }

        // Get authenticated user ID if any
        $userId = null;
        if (auth('sanctum')->check()) {
            $userId = auth('sanctum')->id();
        }

        $receiverId = $request->receiver_id; // null means Admin

        // Find or create session for this specific pair
        $query = ChatSession::where('session_id', $request->session_id)
            ->where('receiver_id', $receiverId);
            
        $chatSession = $query->first();
        
        if (!$chatSession) {
            $chatSession = ChatSession::create([
                'session_id'      => $request->session_id,
                'user_id'         => $userId,
                'receiver_id'     => $receiverId,
                'last_message'    => $request->message ?: 'Image Attachment',
                'last_message_at' => now(),
            ]);
        } else {
            // Update user_id if it was null and user is now logged in
            if (!$chatSession->user_id && $userId) {
                $chatSession->user_id = $userId;
            }
            $chatSession->last_message    = $request->message ?: 'Image Attachment';
            $chatSession->last_message_at = now();
            $chatSession->is_read_by_admin = false; // This flag now applies to whoever is the receiver (admin or seller)
            $chatSession->save();
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/chat');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            
            $file->move($destinationPath, $filename);
            $imagePath = 'uploads/chat/' . $filename;
        }

        // Create message
        $message = ChatMessage::create([
            'chat_session_id' => $chatSession->id,
            'sender_type'     => 'user',
            'message'         => $request->message,
            'image'           => $imagePath,
        ]);

        // Fix image URL for response
        if ($message->image) {
            $message->image = asset($message->image);
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent',
            'data'    => $message
        ]);
    }

    public function getMessages(Request $request)
    {
        $sessionId = $request->query('session_id');
        $receiverId = $request->query('receiver_id'); // nullable

        if (!$sessionId) {
            return response()->json(['success' => false, 'message' => 'Session ID is required'], 400);
        }

        $chatSession = ChatSession::where('session_id', $sessionId)
            ->where('receiver_id', $receiverId)
            ->first();

        // If user is logged in, try to link unlinked session
        if ($chatSession && !$chatSession->user_id && auth('sanctum')->check()) {
            $chatSession->update(['user_id' => auth('sanctum')->id()]);
        }

        if (!$chatSession) {
            return response()->json([
                'success' => true,
                'data'    => []
            ]);
        }

        // Mark as read by user
        if (!$chatSession->is_read_by_user) {
            $chatSession->update(['is_read_by_user' => true]);
        }

        $messages = $chatSession->messages()->orderBy('created_at', 'asc')->get()->map(function($msg) {
            if ($msg->image) {
                $msg->image = asset($msg->image);
            }
            return $msg;
        });

        return response()->json([
            'success' => true,
            'data'    => $messages
        ]);
    }

    public function getUnreadCount(Request $request)
    {
        $sessionId = $request->query('session_id');
        $receiverId = $request->query('receiver_id');

        if (!$sessionId) {
            return response()->json(['success' => true, 'count' => 0]);
        }

        $chatSession = ChatSession::where('session_id', $sessionId)
            ->where('receiver_id', $receiverId)
            ->first();
            
        if ($chatSession && !$chatSession->is_read_by_user) {
            return response()->json(['success' => true, 'count' => 1]);
        }

        return response()->json(['success' => true, 'count' => 0]);
    }
}

<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerChatController extends Controller
{
    /**
     * Display a listing of the chat sessions.
     */
    public function index()
    {
        $sellerId = Auth::id();
        $sessions = ChatSession::with(['customer', 'messages' => function($q) {
            $q->latest()->limit(1);
        }])
        ->where('receiver_id', $sellerId)
        ->orderBy('updated_at', 'desc')
        ->get();

        return view('seller.messages.index', compact('sessions'));
    }

    /**
     * Display the specified chat session.
     */
    public function show($sessionId)
    {
        $sellerId = Auth::id();
        $session = ChatSession::with(['customer', 'messages' => function($q) {
            $q->orderBy('created_at', 'asc');
        }])
        ->where('receiver_id', $sellerId)
        ->findOrFail($sessionId);

        // Mark session as read for admin/seller
        $session->update(['is_read_by_admin' => true]);

        return response()->json([
            'success' => true,
            'session' => $session,
            'messages' => $session->messages
        ]);
    }

    /**
     * Reply to a chat session.
     */
    public function reply(Request $request, $sessionId)
    {
        $request->validate([
            'message' => 'nullable|string',
            'image'   => 'nullable|image|max:2048',
        ]);

        if (!$request->message && !$request->hasFile('image')) {
            return response()->json(['success' => false, 'message' => 'Empty message'], 400);
        }

        $sellerId = Auth::id();
        $session = ChatSession::where('receiver_id', $sellerId)->findOrFail($sessionId);

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

        $message = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type'     => 'seller',
            'message'         => $request->message,
            'image'           => $imagePath,
        ]);

        $session->update([
            'last_message'    => $request->message ?: 'Image Attachment',
            'last_message_at' => now(),
            'is_read_by_user'  => false,
            'is_read_by_admin' => true,
        ]);

        if ($message->image) {
            $message->image = asset($message->image);
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}

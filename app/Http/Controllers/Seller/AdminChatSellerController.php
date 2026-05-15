<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class AdminChatSellerController extends Controller
{
    public function index()
    {
        $sellerId = Auth::id();
        
        // Find or create session with Admin (receiver_id is null/0)
        $session = ChatSession::where('user_id', $sellerId)
            ->where(function($q) {
                $q->whereNull('receiver_id')->orWhere('receiver_id', 0);
            })
            ->first();
            
        if (!$session) {
            $session = ChatSession::create([
                'user_id'         => $sellerId,
                'receiver_id'     => null,
                'last_message_at' => now(),
                'is_read_by_admin'=> true,
                'is_read_by_user' => true,
            ]);
        }

        return view('seller.admin_chat.index', compact('session'));
    }

    public function getMessages($id)
    {
        $session = ChatSession::where('user_id', Auth::id())->findOrFail($id);
        
        // Mark as read by user
        if (!$session->is_read_by_user) {
            $session->update(['is_read_by_user' => true]);
        }
        
        $messages = $session->messages()->orderBy('created_at', 'asc')->get()->map(function($msg) {
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

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'nullable|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);
        
        $session = ChatSession::where('user_id', Auth::id())->findOrFail($id);
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->extension();
            $dest = public_path('uploads/chatchat');
            if (!file_exists($dest)) { mkdir($dest, 0777, true); }
            $image->move($dest, $imageName);
            $imagePath = 'uploads/chatchat/' . $imageName;
        }
        
        $message = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type'     => 'user', // From user's perspective (Seller is a user here)
            'message'         => $request->message,
            'image'           => $imagePath,
        ]);
        
        $session->update([
            'last_message'    => $request->message ?: 'Image Attachment',
            'last_message_at' => now(),
            'is_read_by_admin'=> false,
        ]);
        
        if ($message->image) {
            $message->image = asset($message->image);
        }
        
        return response()->json([
            'success' => true,
            'data'    => $message
        ]);
    }
}

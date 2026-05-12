<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ChatSession;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    public function index()
    {
        $sessions = ChatSession::with('user')
            ->orderBy('last_message_at', 'desc')
            ->get();
            
        return view('admin.chat.index', compact('sessions'));
    }

    public function getSessions()
    {
        $sessions = ChatSession::with('user')
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id'                => $session->id,
                    'name'              => $session->user ? $session->user->name : 'Guest User',
                    'profile_image'     => $session->user && $session->user->profile_image
                        ? asset('uploads/profile_images/' . $session->user->profile_image)
                        : null,
                    'last_message_at'   => $session->last_message_at,
                    'is_read_by_admin'  => $session->is_read_by_admin,
                    'time_ago'          => \Carbon\Carbon::parse($session->last_message_at)->shortRelativeDiffForHumans(),
                ];
            });
            
        return response()->json(['success' => true, 'data' => $sessions]);
    }

    public function getChatMessages($id)
    {
        $session = ChatSession::findOrFail($id);
        
        // Mark as read by admin
        if (!$session->is_read_by_admin) {
            $session->update(['is_read_by_admin' => true]);
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
        
        $session = ChatSession::findOrFail($id);
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->extension();
            $image->move(public_path('uploads/chat'), $imageName);
            $imagePath = 'uploads/chat/' . $imageName;
        }
        
        $message = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type'     => 'admin',
            'message'         => $request->message,
            'image'           => $imagePath,
        ]);
        
        $session->update([
            'last_message_at' => now(),
            'is_read_by_user' => false,
        ]);
        
        return response()->json([
            'success' => true,
            'data'    => $message
        ]);
    }
}

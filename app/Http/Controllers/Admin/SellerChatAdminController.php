<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\User;

class SellerChatAdminController extends Controller
{
    public function index()
    {
        // Get sessions where the user is a seller
        $sessions = ChatSession::whereHas('user', function($q) {
                $q->where('role', 'seller');
            })
            ->with('user')
            ->orderBy('last_message_at', 'desc')
            ->get();
            
        return view('admin.seller_chat.index', compact('sessions'));
    }

    public function getSessions()
    {
        $sessions = ChatSession::whereHas('user', function($q) {
                $q->where('role', 'seller');
            })
            ->with('user')
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id'                => $session->id,
                    'name'              => $session->user ? $session->user->name : 'Unknown Seller',
                    'profile_image'     => $session->user ? $session->user->profile_image_url : null,
                    'last_message_at'   => $session->last_message_at,
                    'is_read_by_admin'  => $session->is_read_by_admin,
                    'time_ago'          => \Carbon\Carbon::parse($session->last_message_at)->shortRelativeDiffForHumans(),
                ];
            });
            
        return response()->json(['success' => true, 'data' => $sessions]);
    }

    public function getMessages($id)
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
            $dest = public_path('uploads/chatchat');
            if (!file_exists($dest)) { mkdir($dest, 0777, true); }
            $image->move($dest, $imageName);
            $imagePath = 'uploads/chatchat/' . $imageName;
        }
        
        $message = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type'     => 'admin',
            'message'         => $request->message,
            'image'           => $imagePath,
        ]);
        
        $session->update([
            'last_message'    => $request->message ?: 'Image Attachment',
            'last_message_at' => now(),
            'is_read_by_user' => false,
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

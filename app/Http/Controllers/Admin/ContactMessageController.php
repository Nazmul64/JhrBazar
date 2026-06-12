<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of contact messages.
     */
    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.contact_messages.index', compact('messages'));
    }

    /**
     * Remove the specified message.
     */
    public function destroy(string $id)
    {
        ContactMessage::findOrFail($id)->delete();

        return redirect()->route('admin.contact_messages.index')
                         ->with('success', 'Contact message deleted successfully.');
    }
}

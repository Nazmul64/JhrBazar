<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactApiController extends Controller
{
    /**
     * Get store contact details.
     */
    public function getContactInfo()
    {
        $contact = Contact::first();
        if ($contact) {
            $contact->contact_image_url = $contact->contact_image ? asset($contact->contact_image) : null;
        }
        return response()->json([
            'success' => true,
            'contact' => $contact
        ]);
    }

    /**
     * Submit contact form message.
     */
    public function submitMessage(Request $request)
    {
        $request->validate([
            'full_name'    => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'subject'      => 'required|string|max:255',
            'message'      => 'required|string',
        ]);

        ContactMessage::create([
            'full_name'    => $request->full_name,
            'phone_number' => $request->phone_number,
            'subject'      => $request->subject,
            'message'      => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your message has been submitted successfully.'
        ]);
    }
}

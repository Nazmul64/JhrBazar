<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display the contact info (singleton — always one record).
     */
    public function index()
    {
        $contact = Contact::first();
        return view('admin.contact.index', compact('contact'));
    }

    /**
     * Show the form for creating contact info.
     */
    public function create()
    {
        // If a record already exists, redirect to edit instead
        $contact = Contact::first();
        if ($contact) {
            return redirect()->route('admin.contact.edit', $contact->id);
        }
        return view('admin.contact.create');
    }

    /**
     * Store the contact info.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone_number'    => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'messenger_link'  => 'nullable|string|max:255',
            'email_address'   => 'nullable|email|max:255',
            'contact_image'   => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            'map_embed_code'  => 'nullable|string',
        ]);

        $data = $request->except(['_token', 'contact_image']);

        $uploadPath = public_path('uploads/contact');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if ($request->hasFile('contact_image')) {
            $file     = $request->file('contact_image');
            $filename = time() . '_contact_image.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $data['contact_image'] = 'uploads/contact/' . $filename;
        }

        Contact::create($data);

        return redirect()->route('admin.contact.index')
                         ->with('success', 'Contact information saved successfully.');
    }

    /**
     * Show the form for editing contact info.
     */
    public function edit(string $id)
    {
        $contact = Contact::findOrFail($id);
        return view('admin.contact.edit', compact('contact'));
    }

    /**
     * Update the contact info.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'phone_number'    => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'messenger_link'  => 'nullable|string|max:255',
            'email_address'   => 'nullable|email|max:255',
            'contact_image'   => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            'map_embed_code'  => 'nullable|string',
        ]);

        $contact = Contact::findOrFail($id);
        $data = $request->except(['_token', '_method', 'contact_image']);

        $uploadPath = public_path('uploads/contact');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if ($request->hasFile('contact_image')) {
            if ($contact->contact_image && file_exists(public_path($contact->contact_image))) {
                unlink(public_path($contact->contact_image));
            }
            $file     = $request->file('contact_image');
            $filename = time() . '_contact_image.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $data['contact_image'] = 'uploads/contact/' . $filename;
        }

        $contact->update($data);

        return redirect()->route('admin.contact.index')
                         ->with('success', 'Contact information updated successfully.');
    }

    /**
     * Remove the contact info.
     */
    public function destroy(string $id)
    {
        $contact = Contact::findOrFail($id);
        if ($contact->contact_image && file_exists(public_path($contact->contact_image))) {
            unlink(public_path($contact->contact_image));
        }
        $contact->delete();

        return redirect()->route('admin.contact.index')
                         ->with('success', 'Contact information deleted successfully.');
    }

    /**
     * Not used for this singleton resource.
     */
    public function show(string $id)
    {
        return redirect()->route('admin.contact.index');
    }
}

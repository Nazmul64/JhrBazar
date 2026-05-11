<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MembershipLogoController extends Controller
{
    public function index()
    {
        $logos = MembershipLogo::latest()->get();
        return view('admin.membership_logos.index', compact('logos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            'images'   => 'required|array',
            'name'     => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('images')) {
            $uploadPath = public_path('uploads/membership');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move($uploadPath, $filename);
                
                MembershipLogo::create([
                    'name'  => $request->name,
                    'image' => 'uploads/membership/' . $filename,
                    'is_active' => true
                ]);
            }
        }

        Cache::forget('footer_data_v2');
        Cache::forget('homepage_data_v2');


        return redirect()->back()->with('success', 'Logos added successfully.');
    }

    public function destroy(MembershipLogo $membershipLogo)
    {
        if ($membershipLogo->image && file_exists(public_path($membershipLogo->image))) {
            unlink(public_path($membershipLogo->image));
        }
        $membershipLogo->delete();
        Cache::forget('footer_data_v2');
        Cache::forget('homepage_data_v2');


        return redirect()->back()->with('success', 'Logo removed successfully.');
    }

    public function toggleStatus(MembershipLogo $membershipLogo)
    {
        $membershipLogo->update(['is_active' => !$membershipLogo->is_active]);
        Cache::forget('footer_data_v2');
        Cache::forget('homepage_data_v2');


        return redirect()->back()->with('success', 'Status updated.');
    }
}

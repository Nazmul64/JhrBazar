<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrivacyPolicy;

class PrivacyPolicyController extends Controller
{
    public function index()
    {
        $policy = PrivacyPolicy::first();
        return view('admin.privacy.index', compact('policy'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $policy = PrivacyPolicy::first() ?: new PrivacyPolicy();

        $policy->fill([
            'title'            => $request->title,
            'content'          => $request->content,
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
        ]);
        $policy->save();

        return redirect()->back()->with('success', 'Privacy Policy updated successfully.');
    }
}

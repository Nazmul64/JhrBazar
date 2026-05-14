<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AboutCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $about = AboutCompany::first();
        return view('admin.about.index', compact('about'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ]);

        $about = AboutCompany::first() ?: new AboutCompany();

        $imagePath = $about->image;
        if ($request->hasFile('image')) {
            if ($imagePath && File::exists(public_path($imagePath))) {
                File::delete(public_path($imagePath));
            }
            $fileName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/company'), $fileName);
            $imagePath = 'uploads/company/' . $fileName;
        }

        $about->fill([
            'title'            => $request->title,
            'content'          => $request->content,
            'image'            => $imagePath,
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
        ]);
        $about->save();

        return redirect()->back()->with('success', 'About Company updated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AboutCompany $aboutCompany)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AboutCompany $aboutCompany)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AboutCompany $aboutCompany)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AboutCompany $aboutCompany)
    {
        //
    }
}

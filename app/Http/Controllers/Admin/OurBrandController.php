<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OurBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OurBrandController extends Controller
{
    public function index()
    {
        $brands = OurBrand::orderBy('sort_order')->latest()->get();
        return view('admin.ourbrand.index', compact('brands'));
    }

    public function create()
    {
        return redirect()->route('admin.ourbrands.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        $imagePath = $this->saveImage($request->file('image'));

        OurBrand::create([
            'image'     => $imagePath,
            'is_active' => $request->boolean('is_active', true),
        ]);

        Cache::forget('home_data_v2');

        return redirect()->route('admin.ourbrands.index')
            ->with('success', 'Brand slider image uploaded successfully.');
    }

    public function edit(OurBrand $ourbrand)
    {
        return view('admin.ourbrand.edit', compact('ourbrand'));
    }

    public function update(Request $request, OurBrand $ourbrand)
    {
        $request->validate([
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $this->saveImage($request->file('image'));
            $ourbrand->image = $imagePath;
        }

        $ourbrand->update([
            'is_active' => $request->boolean('is_active', true),
        ]);

        Cache::forget('home_data_v2');

        return redirect()->route('admin.ourbrands.index')
            ->with('success', 'Brand slider image updated successfully.');
    }

    public function destroy(OurBrand $ourbrand)
    {
        if ($ourbrand->image && file_exists(public_path($ourbrand->image))) {
            @unlink(public_path($ourbrand->image));
        }

        $ourbrand->delete();

        Cache::forget('home_data_v2');

        return redirect()->route('admin.ourbrands.index')
            ->with('success', 'Brand slider image deleted successfully.');
    }

    public function toggleStatus(OurBrand $ourbrand)
    {
        $ourbrand->update(['is_active' => !$ourbrand->is_active]);

        Cache::forget('home_data_v2');

        return redirect()->back()->with('success', 'Status updated.');
    }

    protected function saveImage($file)
    {
        $directory = public_path('uploads/ourbrand');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->extension();
        $file->move($directory, $filename);

        return 'uploads/ourbrand/' . $filename;
    }
}

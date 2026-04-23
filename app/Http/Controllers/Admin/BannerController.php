<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    // ─────────────────────────────────────────────
    //  Helper: save file → public/uploads/banner/
    //  returns: "uploads/banner/filename.ext"
    // ─────────────────────────────────────────────
    private function saveFile($file): string
    {
        $dir = public_path('uploads/banner');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);
        return 'uploads/banner/' . $filename;
    }

    private function deleteFile(?string $path): void
    {
        if (!$path) return;
        $full = public_path($path);
        if (file_exists($full)) unlink($full);
    }

    // ──────────────────────────────────────────────
    //  Index
    // ──────────────────────────────────────────────
    public function index()
    {
        $banners = Banner::latest()->get();
        return view('admin.banner.index', compact('banners'));
    }

    // ──────────────────────────────────────────────
    //  Create
    // ──────────────────────────────────────────────
    public function create()
    {
        return view('admin.banner.create');
    }

    // ──────────────────────────────────────────────
    //  Store
    // ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $imagePath = $this->saveFile($request->file('image'));

        Banner::create([
            'title'        => $request->title,
            'image'        => $imagePath,
            'for_own_shop' => $request->boolean('for_own_shop'),
            'is_active'    => true,
        ]);

        return redirect()->route('admin.banner.index')
            ->with('success', 'Banner created successfully.');
    }

    // ──────────────────────────────────────────────
    //  Edit
    // ──────────────────────────────────────────────
    public function edit(string $id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banner.edit', compact('banner'));
    }

    // ──────────────────────────────────────────────
    //  Update
    // ──────────────────────────────────────────────
    public function update(Request $request, string $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $imagePath = $banner->image;
        if ($request->hasFile('image')) {
            $this->deleteFile($banner->image);
            $imagePath = $this->saveFile($request->file('image'));
        }

        $banner->update([
            'title'        => $request->title,
            'image'        => $imagePath,
            'for_own_shop' => $request->boolean('for_own_shop'),
        ]);

        return redirect()->route('admin.banner.index')
            ->with('success', 'Banner updated successfully.');
    }

    // ──────────────────────────────────────────────
    //  Destroy
    // ──────────────────────────────────────────────
    public function destroy(string $id)
    {
        $banner = Banner::findOrFail($id);
        $this->deleteFile($banner->image);
        $banner->delete();

        return redirect()->route('admin.banner.index')
            ->with('success', 'Banner deleted successfully.');
    }

    // ──────────────────────────────────────────────
    //  Toggle Status  (POST)
    // ──────────────────────────────────────────────
    public function toggleStatus(string $id)
    {
        $banner = Banner::findOrFail($id);
        $banner->update(['is_active' => !$banner->is_active]);

        return redirect()->back()
            ->with('success', 'Banner status updated.');
    }
}

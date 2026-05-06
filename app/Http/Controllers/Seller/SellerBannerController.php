<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SellerBannerController extends Controller
{
    private function saveFile($file): string
    {
        $dir = public_path('uploads/sellerbanner');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);
        return 'uploads/sellerbanner/' . $filename;
    }

    private function deleteFile(?string $path): void
    {
        if (!$path) return;
        $full = public_path($path);
        if (file_exists($full)) {
            unlink($full);
        }
    }

    public function index()
    {
        $banners = SellerBanner::where('seller_id', Auth::id())->latest()->get();
        return view('seller.banner.index', compact('banners'));
    }

    public function create()
    {
        return view('seller.banner.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imagePath = $this->saveFile($request->file('image'));

        SellerBanner::create([
            'seller_id' => Auth::id(),
            'title'     => $request->title,
            'image'     => $imagePath,
            'is_active' => true,
        ]);

        return redirect()->route('seller.banner.index')->with('success', 'Banner Created Successfully');
    }

    public function edit($id)
    {
        $banner = SellerBanner::where('seller_id', Auth::id())->findOrFail($id);
        return view('seller.banner.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $banner = SellerBanner::where('seller_id', Auth::id())->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imagePath = $banner->image;
        if ($request->hasFile('image')) {
            $this->deleteFile($banner->image);
            $imagePath = $this->saveFile($request->file('image'));
        }

        $banner->update([
            'title' => $request->title,
            'image' => $imagePath,
        ]);

        return redirect()->route('seller.banner.index')->with('success', 'Banner Updated Successfully');
    }

    public function destroy($id)
    {
        $banner = SellerBanner::where('seller_id', Auth::id())->findOrFail($id);
        $this->deleteFile($banner->image);
        $banner->delete();

        return redirect()->back()->with('success', 'Banner Deleted Successfully');
    }

    public function toggleStatus($id)
    {
        $banner = SellerBanner::where('seller_id', Auth::id())->findOrFail($id);
        $banner->update(['is_active' => !$banner->is_active]);

        return redirect()->back()->with('success', 'Banner Status Updated');
    }
}

<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerBanner;
use Illuminate\Http\Request;

class SellerBannerController extends Controller
{
    // Helper: store uploaded image and return path
    private function saveFile($file): string
    {
        $dir = public_path('uploads/sellerbanners');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);
        return 'uploads/sellerbanners/' . $filename;
    }

    private function deleteFile(?string $path): void
    {
        if (!$path) return;
        $full = public_path($path);
        if (file_exists($full)) unlink($full);
    }

    // List all banners for the logged‑in seller
    public function index()
    {
        $banners = SellerBanner::where('seller_id', auth()->id())->latest()->get();
        return view('seller.banners.index', compact('banners'));
    }

    // Show the create form
    public function create()
    {
        return view('seller.banners.create');
    }

    // Store new banner
    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'image'      => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'link'       => 'nullable|url',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'is_active'  => 'sometimes|boolean',
        ]);

        $imagePath = $this->saveFile($request->file('image'));

        SellerBanner::create([
            'seller_id'   => auth()->id(),
            'title'       => $request->title,
            'image'       => $imagePath,
            'link'        => $request->link,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'is_active'   => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('seller.banners.index')
            ->with('success', 'Banner created successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        $banner = SellerBanner::where('seller_id', auth()->id())->findOrFail($id);
        return view('seller.banners.edit', compact('banner'));
    }

    // Update existing banner
    public function update(Request $request, $id)
    {
        $banner = SellerBanner::where('seller_id', auth()->id())->findOrFail($id);
        $request->validate([
            'title'      => 'required|string|max:255',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'link'       => 'nullable|url',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'is_active'  => 'sometimes|boolean',
        ]);

        $imagePath = $banner->image;
        if ($request->hasFile('image')) {
            $this->deleteFile($banner->image);
            $imagePath = $this->saveFile($request->file('image'));
        }

        $banner->update([
            'title'       => $request->title,
            'image'       => $imagePath,
            'link'        => $request->link,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'is_active'   => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('seller.banners.index')
            ->with('success', 'Banner updated successfully.');
    }

    // Delete a banner
    public function destroy($id)
    {
        $banner = SellerBanner::where('seller_id', auth()->id())->findOrFail($id);
        $this->deleteFile($banner->image);
        $banner->delete();
        return redirect()->route('seller.banners.index')
            ->with('success', 'Banner deleted successfully.');
    }
}

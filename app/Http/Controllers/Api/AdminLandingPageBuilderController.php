<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Landingpage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminLandingPageBuilderController extends Controller
{
    private string $uploadDir = 'uploads/landingpage';

    /**
     * Get sections and metadata for page builder.
     */
    public function getSections($id)
    {
        $page = Landingpage::with(['product'])->findOrFail($id);

        // Fetch additional products if any
        $additionalProducts = [];
        if ($page->additional_product_ids && is_array($page->additional_product_ids)) {
            $additionalProducts = Product::whereIn('id', $page->additional_product_ids)
                ->where('is_active', true)
                ->get();
        }

        // Fetch all active products so the builder settings can let you search/select them
        $allProducts = Product::where('is_active', true)->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'style_template' => $page->style_template,
                'bg_color' => $page->bg_color ?: '#ffffff',
                'button_color' => $page->button_color ?: '#1e3a8a',
                'media_type' => $page->media_type,
                'image' => $page->image ? asset($page->image) : null,
                'feature_image' => $page->feature_image ? asset($page->feature_image) : null,
                'video_url' => $page->video_url,
                'checkout_image' => $page->checkout_image ? asset($page->checkout_image) : null,
                'status' => (bool) $page->status,
                'is_template' => (bool) $page->is_template,
                'sections' => $page->sections ?: [],
                'primary_product' => $page->product,
                'additional_products' => $additionalProducts,
                'all_products' => $allProducts,
            ]
        ]);
    }

    /**
     * Save sections for the landing page.
     */
    public function saveSections(Request $request, $id)
    {
        $page = Landingpage::findOrFail($id);

        $request->validate([
            'sections' => 'required|array'
        ]);

        $page->update([
            'sections' => $request->sections
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sections saved successfully!',
            'sections' => $page->sections
        ]);
    }

    /**
     * Save global page settings from the builder.
     */
    public function saveSettings(Request $request, $id)
    {
        $page = Landingpage::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'slug' => 'required|string|unique:landingpages,slug,' . $page->id,
            'bg_color' => 'nullable|string',
            'button_color' => 'nullable|string',
        ]);

        $title = $request->filled('title') ? $request->title : $page->title;

        $page->update([
            'title' => $title,
            'slug' => $request->slug,
            'bg_color' => $request->bg_color,
            'button_color' => $request->button_color,
            'product_id' => $request->filled('product_id') ? $request->product_id : null,
            'additional_product_ids' => $request->additional_product_ids ?: null,
            'status' => $request->status ? 1 : 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Page settings updated successfully!'
        ]);
    }

    /**
     * Upload dynamic images/assets inside builders (AJAX).
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        $path = public_path($this->uploadDir);
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $file = $request->file('image');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($path, $filename);
        $relativePath = $this->uploadDir . '/' . $filename;

        return response()->json([
            'success' => true,
            'path' => $relativePath,
            'url' => asset($relativePath)
        ]);
    }
}

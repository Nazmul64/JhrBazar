<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Landingpage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LandingPageController extends Controller
{
    private string $uploadDir = 'uploads/landingpage';

    // ──────────────────────────────────────────────────────────────
    //  INDEX
    // ──────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Landingpage::with('product')->latest();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where('title', 'like', "%{$s}%");
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $landingpages = $query->paginate(10)->withQueryString();

        return view('admin.landingpages.index', compact('landingpages'));
    }

    // ──────────────────────────────────────────────────────────────
    //  BUILDER (Admin Panel Embedded Builder)
    // ──────────────────────────────────────────────────────────────
    public function builder(Landingpage $landingpage)
    {
        return view('admin.landingpages.builder', compact('landingpage'));
    }

    // ──────────────────────────────────────────────────────────────
    //  CREATE  (Builder Interface)
    // ──────────────────────────────────────────────────────────────
    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.landingpages.create', compact('products'));
    }

    // ──────────────────────────────────────────────────────────────
    //  STORE  (Save entire landing page with sections)
    // ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'nullable|string|max:255',
            'product_id' => 'nullable|exists:products,id',
            'slug'       => 'nullable|string|unique:landingpages,slug',
        ]);

        $this->ensureDir();

        // Process sections from JSON
        $sections = json_decode($request->input('sections_data', '[]'), true);
        $sections = $this->processUploadedSectionImages($request, $sections);

        // Handle main feature image (name='image' in form)
        $featureImagePath = null;
        if ($request->hasFile('image')) {
            $featureImagePath = $this->uploadFile($request->file('image'));
        }

        $checkoutImagePath = null;
        if ($request->hasFile('checkout_review_image')) {
            $checkoutImagePath = $this->uploadFile($request->file('checkout_review_image'));
        }

        // Handle reviews
        $reviews = $this->processReviews($request);

        // Title fallback
        $title = $request->filled('title') 
            ? $request->title 
            : 'Landing Page #' . ((\App\Models\Landingpage::max('id') ?? 0) + 1);

        // Generate slug
        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($title) . '-' . Str::random(6);

        $landingpage = Landingpage::create([
            'title'                  => $title,
            'slug'                   => $slug,
            'product_id'             => $request->filled('product_id') ? $request->product_id : null,
            'additional_product_ids' => $request->input('additional_product_ids') ?: null,
            'style_template'         => $request->input('template', 'template3'),
            'media_type'             => $request->input('media_type', 'image'),
            'image'                  => $featureImagePath,
            'feature_image'          => $featureImagePath,
            'video_url'              => $request->video_url,
            'checkout_image'         => $checkoutImagePath,
            'bg_color'               => $request->input('bg_color', '#ffffff'),
            'button_color'           => $request->input('button_color', '#1e3a8a'),
            'reviews'                => $reviews ?: null,
            'short_description'      => $request->short_description,
            'description'            => $request->description,
            'sections'               => null,
            'status'                 => (int) $request->input('status', 1),
            'is_template'            => 0,
        ]);

        if ($request->input('action') === 'builder') {
            return redirect()->route('admin.landingpages.builder', $landingpage->id);
        }

        return redirect()->route('admin.landingpages.index')
            ->with('success', 'Landing page created successfully.');
    }

    // ──────────────────────────────────────────────────────────────
    //  EDIT  (Builder Interface)
    // ──────────────────────────────────────────────────────────────
    public function edit(Landingpage $landingpage)
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.landingpages.edit', compact('landingpage', 'products'));
    }

    // ──────────────────────────────────────────────────────────────
    //  PREVIEW
    // ──────────────────────────────────────────────────────────────
    public function preview(Landingpage $landingpage)
    {
        return view('admin.landingpages.preview', compact('landingpage'));
    }

    // ──────────────────────────────────────────────────────────────
    //  UPDATE
    // ──────────────────────────────────────────────────────────────
    public function update(Request $request, Landingpage $landingpage)
    {
        $request->validate([
            'title'      => 'nullable|string|max:255',
            'product_id' => 'nullable|exists:products,id',
            'slug'       => 'nullable|string|unique:landingpages,slug,' . $landingpage->id,
        ]);

        $this->ensureDir();

        // Process sections
        $sections = json_decode($request->input('sections_data', '[]'), true);
        $sections = $this->processUploadedSectionImages($request, $sections);

        // Main image
        $imagePath = $landingpage->image;
        if ($request->hasFile('image')) {
            $this->deleteFile($imagePath);
            $imagePath = $this->uploadFile($request->file('image'));
        }

        // Feature image
        $featureImagePath = $landingpage->feature_image;
        if ($request->hasFile('feature_image')) {
            $this->deleteFile($featureImagePath);
            $featureImagePath = $this->uploadFile($request->file('feature_image'));
        }

        // Checkout review image
        $checkoutImagePath = $landingpage->checkout_image;
        if ($request->hasFile('checkout_image')) {
            $this->deleteFile($checkoutImagePath);
            $checkoutImagePath = $this->uploadFile($request->file('checkout_image'));
        }

        // Reviews
        $reviews = $this->processReviews($request, $landingpage);

        // Title fallback
        $title = $request->filled('title') ? $request->title : $landingpage->title;

        // Generate slug
        $slug = $request->filled('slug') 
            ? Str::slug($request->slug) 
            : $landingpage->slug;

        $landingpage->update([
            'title'                  => $title,
            'slug'                   => $slug,
            'product_id'             => $request->filled('product_id') ? $request->product_id : null,
            'additional_product_ids' => $request->input('additional_product_ids') ?: null,
            'style_template'         => $request->input('style_template', 'Template 3 (Dynamic Builder)'),
            'media_type'             => $request->media_type ?? 'image',
            'image'                  => $imagePath,
            'feature_image'          => $featureImagePath,
            'video_url'              => $request->video_url,
            'checkout_image'         => $checkoutImagePath,
            'bg_color'               => $request->input('bg_color', '#ffffff'),
            'button_color'           => $request->input('button_color', '#1e3a8a'),
            'reviews'                => $reviews ?: null,
            'short_description'      => $request->short_description,
            'description'            => $request->description,
            'sections'               => $sections ?: null,
            'status'                 => $request->has('status') ? 1 : 0,
            'is_template'            => $request->has('is_template') ? 1 : 0,
        ]);

        if ($request->input('action') === 'builder') {
            return redirect('/landing-builder/' . $landingpage->id);
        }

        return redirect()->route('admin.landingpages.index')
            ->with('success', 'Landing page updated successfully.');
    }

    // ──────────────────────────────────────────────────────────────
    //  UPLOAD SECTION IMAGE (AJAX)
    // ──────────────────────────────────────────────────────────────
    public function uploadSectionImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        $this->ensureDir();
        $path = $this->uploadFile($request->file('image'));

        return response()->json([
            'success' => true,
            'path'    => $path,
            'url'     => asset($path),
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  DESTROY
    // ──────────────────────────────────────────────────────────────
    public function destroy(Landingpage $landingpage)
    {
        $this->deleteFile($landingpage->image);

        foreach ($landingpage->reviews ?? [] as $review) {
            $this->deleteFile($review['image'] ?? null);
        }

        // Delete section images
        foreach ($landingpage->sections ?? [] as $section) {
            $this->deleteSectionImages($section);
        }

        $landingpage->delete();

        return redirect()->route('admin.landingpages.index')
            ->with('success', 'Landing page deleted successfully.');
    }

    // ──────────────────────────────────────────────────────────────
    //  TOGGLE STATUS
    // ──────────────────────────────────────────────────────────────
    public function toggleStatus(Landingpage $landingpage)
    {
        $landingpage->update(['status' => !$landingpage->status]);

        return redirect()->route('admin.landingpages.index')
            ->with('success', 'Status updated successfully.');
    }

    // ══════════════════════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════

    private function ensureDir(): void
    {
        $path = public_path($this->uploadDir);
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    private function uploadFile(\Illuminate\Http\UploadedFile $file): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($this->uploadDir), $filename);
        return $this->uploadDir . '/' . $filename;
    }

    private function deleteFile(?string $relativePath): void
    {
        if (!$relativePath) return;
        $full = public_path($relativePath);
        if (File::exists($full)) {
            File::delete($full);
        }
    }

    private function processReviews(Request $request, ?Landingpage $existing = null): array
    {
        $existingReviews = $existing?->reviews ?? [];
        $reviews = [];

        if ($request->has('reviews') && is_array($request->reviews)) {
            foreach ($request->reviews as $i => $row) {
                $reviewText = trim($row['review'] ?? '');
                if ($reviewText === '') continue;

                $oldImg = $existingReviews[$i]['image'] ?? null;

                if (
                    $request->hasFile("reviews.{$i}.image") &&
                    $request->file("reviews.{$i}.image")->isValid()
                ) {
                    $this->deleteFile($oldImg);
                    $imgPath = $this->uploadFile($request->file("reviews.{$i}.image"));
                } else {
                    $imgPath = $oldImg;
                }

                $reviews[] = [
                    'review' => $reviewText,
                    'image'  => $imgPath,
                ];
            }
        }

        // Cleanup removed review images
        if ($existing) {
            for ($i = count($reviews); $i < count($existingReviews); $i++) {
                $this->deleteFile($existingReviews[$i]['image'] ?? null);
            }
        }

        return $reviews;
    }

    private function processUploadedSectionImages(Request $request, array $sections): array
    {
        foreach ($sections as $idx => &$section) {
            // Handle section image uploads
            $fileKey = "section_image_{$idx}";
            if ($request->hasFile($fileKey)) {
                $section['data']['image'] = $this->uploadFile($request->file($fileKey));
            }

            // Handle gallery images
            $galleryKey = "section_gallery_{$idx}";
            if ($request->hasFile($galleryKey)) {
                $gallery = [];
                foreach ($request->file($galleryKey) as $file) {
                    if ($file->isValid()) {
                        $gallery[] = $this->uploadFile($file);
                    }
                }
                $section['data']['gallery'] = array_merge($section['data']['gallery'] ?? [], $gallery);
            }
        }

        return $sections;
    }

    private function deleteSectionImages(array $section): void
    {
        $this->deleteFile($section['data']['image'] ?? null);

        foreach ($section['data']['gallery'] ?? [] as $img) {
            $this->deleteFile($img);
        }
    }
}

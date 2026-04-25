<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Landingpage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LandingPageController extends Controller
{
    // ── Upload directory (relative to public/) ──────────────────
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
    //  CREATE
    // ──────────────────────────────────────────────────────────────
    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.landingpages.create', compact('products'));
    }

    // ──────────────────────────────────────────────────────────────
    //  STORE
    // ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'title'             => 'required|string|max:255',
            'product_id'        => 'nullable|exists:products,id',
            'media_type'        => 'required|in:image,video',
            'image'             => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
            'short_description' => 'nullable|string',
            'description'       => 'nullable|string',

            // Reviews
            'reviews'             => 'nullable|array',
            'reviews.*.review'    => 'required_with:reviews.*.image|string|max:500',
            'reviews.*.image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        // ── Ensure upload directory exists ──────────────────────
        $this->ensureDir();

        // ── Main Image ──────────────────────────────────────────
        $imagePath = $this->uploadFile($request->file('image'));

        // ── Reviews ────────────────────────────────────────────
        $reviews = [];
        if ($request->has('reviews') && is_array($request->reviews)) {
            foreach ($request->reviews as $i => $row) {
                $reviewText = trim($row['review'] ?? '');
                if ($reviewText === '') continue;

                $imgPath = null;
                if (
                    isset($row['image']) &&
                    $request->hasFile("reviews.{$i}.image") &&
                    $request->file("reviews.{$i}.image")->isValid()
                ) {
                    $imgPath = $this->uploadFile($request->file("reviews.{$i}.image"));
                }

                $reviews[] = [
                    'review' => $reviewText,
                    'image'  => $imgPath,
                ];
            }
        }

        Landingpage::create([
            'title'             => $request->title,
            'product_id'        => $request->product_id,
            'media_type'        => $request->media_type,
            'image'             => $imagePath,
            'reviews'           => $reviews ?: null,
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'status'            => $request->has('status') ? 1 : 0,
        ]);

        return redirect()->route('admin.landingpages.index')
            ->with('success', 'Landing page created successfully.');
    }

    // ──────────────────────────────────────────────────────────────
    //  EDIT
    // ──────────────────────────────────────────────────────────────
    public function edit(Landingpage $landingpage)
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.landingpages.edit', compact('landingpage', 'products'));
    }

    // ──────────────────────────────────────────────────────────────
    //  UPDATE
    // ──────────────────────────────────────────────────────────────
    public function update(Request $request, Landingpage $landingpage)
    {
        $request->validate([
            'title'             => 'required|string|max:255',
            'product_id'        => 'nullable|exists:products,id',
            'media_type'        => 'required|in:image,video',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
            'short_description' => 'nullable|string',
            'description'       => 'nullable|string',

            'reviews'             => 'nullable|array',
            'reviews.*.review'    => 'nullable|string|max:500',
            'reviews.*.image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $this->ensureDir();

        // ── Main Image ──────────────────────────────────────────
        $imagePath = $landingpage->image;
        if ($request->hasFile('image')) {
            $this->deleteFile($imagePath);
            $imagePath = $this->uploadFile($request->file('image'));
        }

        // ── Reviews ────────────────────────────────────────────
        $existingReviews = $landingpage->reviews ?? [];
        $reviews = [];

        if ($request->has('reviews') && is_array($request->reviews)) {
            foreach ($request->reviews as $i => $row) {
                $reviewText = trim($row['review'] ?? '');
                if ($reviewText === '') continue;

                // Keep old image if no new one uploaded
                $oldImg = $existingReviews[$i]['image'] ?? null;

                if (
                    $request->hasFile("reviews.{$i}.image") &&
                    $request->file("reviews.{$i}.image")->isValid()
                ) {
                    // Delete old image for this review slot
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

        // Delete review images that were removed
        $oldCount = count($existingReviews);
        $newCount = count($reviews);
        for ($i = $newCount; $i < $oldCount; $i++) {
            $this->deleteFile($existingReviews[$i]['image'] ?? null);
        }

        $landingpage->update([
            'title'             => $request->title,
            'product_id'        => $request->product_id,
            'media_type'        => $request->media_type,
            'image'             => $imagePath,
            'reviews'           => $reviews ?: null,
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'status'            => $request->has('status') ? 1 : 0,
        ]);

        return redirect()->route('admin.landingpages.index')
            ->with('success', 'Landing page updated successfully.');
    }

    // ──────────────────────────────────────────────────────────────
    //  DESTROY
    // ──────────────────────────────────────────────────────────────
    public function destroy(Landingpage $landingpage)
    {
        // Delete main image
        $this->deleteFile($landingpage->image);

        // Delete all review images
        foreach ($landingpage->reviews ?? [] as $review) {
            $this->deleteFile($review['image'] ?? null);
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

    // ──────────────────────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────────

    /** Ensure the upload directory exists inside public/ */
    private function ensureDir(): void
    {
        $path = public_path($this->uploadDir);
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    /**
     * Upload a file to public/uploads/landingpage/
     * Returns the relative path: uploads/landingpage/filename.ext
     */
    private function uploadFile(\Illuminate\Http\UploadedFile $file): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($this->uploadDir), $filename);
        return $this->uploadDir . '/' . $filename;
    }

    /**
     * Delete a file from public/ by relative path.
     * Silently ignores if path is null or file doesn't exist.
     */
    private function deleteFile(?string $relativePath): void
    {
        if (!$relativePath) return;
        $full = public_path($relativePath);
        if (File::exists($full)) {
            File::delete($full);
        }
    }
}

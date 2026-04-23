<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Flashsale;
use Illuminate\Http\Request;

class FlashsaleController extends Controller
{
    // ─────────────────────────────────────────────
    //  Helper: save file → public/uploads/flashsale/
    //  returns: "uploads/flashsale/filename.ext"
    // ─────────────────────────────────────────────
    private function saveFile($file): string
    {
        $dir = public_path('uploads/flashsale');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);
        return 'uploads/flashsale/' . $filename;
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
        $flashsales = Flashsale::latest()->get();
        return view('admin.flashsale.index', compact('flashsales'));
    }

    // ──────────────────────────────────────────────
    //  Create
    // ──────────────────────────────────────────────
    public function create()
    {
        return view('admin.flashsale.create');
    }

    // ──────────────────────────────────────────────
    //  Store
    // ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'minimum_discount' => 'required|numeric|min:0|max:100',
            'start_date'       => 'required|date',
            'start_time'       => 'required',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'end_time'         => 'required',
            'description'      => 'required|string',
            'thumbnail'        => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $thumbnailPath = $this->saveFile($request->file('thumbnail'));

        Flashsale::create([
            'name'             => $request->name,
            'minimum_discount' => $request->minimum_discount,
            'start_date'       => $request->start_date,
            'start_time'       => date('H:i:s', strtotime($request->start_time)),
            'end_date'         => $request->end_date,
            'end_time'         => date('H:i:s', strtotime($request->end_time)),
            'description'      => $request->description,
            'thumbnail'        => $thumbnailPath,
            'is_active'        => true,
        ]);

        return redirect()->route('admin.flashsale.index')
            ->with('success', 'Flash Sale created successfully.');
    }

    // ──────────────────────────────────────────────
    //  Show
    // ──────────────────────────────────────────────
    public function show(string $id)
    {
        $flashsale = Flashsale::with('products.category')->findOrFail($id);
        return view('admin.flashsale.show', compact('flashsale'));
    }

    // ──────────────────────────────────────────────
    //  Edit
    // ──────────────────────────────────────────────
    public function edit(string $id)
    {
        $flashsale = Flashsale::findOrFail($id);
        return view('admin.flashsale.edit', compact('flashsale'));
    }

    // ──────────────────────────────────────────────
    //  Update
    // ──────────────────────────────────────────────
    public function update(Request $request, string $id)
    {
        $flashsale = Flashsale::findOrFail($id);

        $request->validate([
            'name'             => 'required|string|max:255',
            'minimum_discount' => 'required|numeric|min:0|max:100',
            'start_date'       => 'required|date',
            'start_time'       => 'required',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'end_time'         => 'required',
            'description'      => 'required|string',
            'thumbnail'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $thumbnailPath = $flashsale->thumbnail;
        if ($request->hasFile('thumbnail')) {
            $this->deleteFile($flashsale->thumbnail);
            $thumbnailPath = $this->saveFile($request->file('thumbnail'));
        }

        $flashsale->update([
            'name'             => $request->name,
            'minimum_discount' => $request->minimum_discount,
            'start_date'       => $request->start_date,
            'start_time'       => date('H:i:s', strtotime($request->start_time)),
            'end_date'         => $request->end_date,
            'end_time'         => date('H:i:s', strtotime($request->end_time)),
            'description'      => $request->description,
            'thumbnail'        => $thumbnailPath,
        ]);

        return redirect()->route('admin.flashsale.index')
            ->with('success', 'Flash Sale updated successfully.');
    }

    // ──────────────────────────────────────────────
    //  Destroy
    // ──────────────────────────────────────────────
    public function destroy(string $id)
    {
        $flashsale = Flashsale::findOrFail($id);
        $this->deleteFile($flashsale->thumbnail);
        $flashsale->delete();

        return redirect()->route('admin.flashsale.index')
            ->with('success', 'Flash Sale deleted successfully.');
    }

    // ──────────────────────────────────────────────
    //  Toggle Status
    // ──────────────────────────────────────────────
    public function toggleStatus(string $id)
    {
        $flashsale = Flashsale::findOrFail($id);
        $flashsale->update(['is_active' => !$flashsale->is_active]);

        return redirect()->back()
            ->with('success', 'Flash Sale status updated.');
    }
}

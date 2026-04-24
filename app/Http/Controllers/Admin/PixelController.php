<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pixel;
class PixelController extends Controller
{
   public function index(Request $request)
    {
        $query = Pixel::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('pixels_id', 'like', "%{$search}%");
        }

        $pixels = $query->latest()->paginate(10);

        return view('admin.pixels.index', compact('pixels'));
    }

    public function create()
    {
        return view('admin.pixels.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pixels_id' => 'required|string|unique:pixels,pixels_id|max:255',
        ]);

        Pixel::create([
            'pixels_id' => $request->pixels_id,
            'status'    => $request->has('status'),
        ]);

        return redirect()->route('admin.pixels.index')
            ->with('success', 'Pixel created successfully.');
    }

    public function edit(Pixel $pixel)
    {
        return view('admin.pixels.edit', compact('pixel'));
    }

    public function update(Request $request, Pixel $pixel)
    {
        $request->validate([
            'pixels_id' => 'required|string|max:255|unique:pixels,pixels_id,' . $pixel->id,
        ]);

        $pixel->update([
            'pixels_id' => $request->pixels_id,
            'status'    => $request->has('status'),
        ]);

        return redirect()->route('admin.pixels.index')
            ->with('success', 'Pixel updated successfully.');
    }

    public function destroy(Pixel $pixel)
    {
        $pixel->delete();

        return redirect()->route('admin.pixels.index')
            ->with('success', 'Pixel deleted successfully.');
    }

    public function toggleStatus(Pixel $pixel)
    {
        $pixel->update(['status' => !$pixel->status]);

        return redirect()->route('admin.pixels.index')
            ->with('success', 'Pixel status updated.');
    }
}

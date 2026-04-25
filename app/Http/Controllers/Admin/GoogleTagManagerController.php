<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Googletagmanager;
use Illuminate\Http\Request;

class GoogleTagManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tagmanagers = Googletagmanager::latest()->paginate(10);
        return view('admin.googletagmanager.index', compact('tagmanagers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.googletagmanager.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tag_manager_id' => 'required|string|unique:googletagmanagers,tag_manager_id|max:255',
        ], [
            'tag_manager_id.required' => 'Tag Manager ID is required.',
            'tag_manager_id.unique'   => 'This Tag Manager ID already exists.',
        ]);

        Googletagmanager::create([
            'tag_manager_id' => $request->tag_manager_id,
            'status'         => $request->has('status') ? true : false,
        ]);

        return redirect()->route('admin.googletagmanager.index')
            ->with('success', 'Tag Manager created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tagmanager = Googletagmanager::findOrFail($id);
        return view('admin.googletagmanager.edit', compact('tagmanager'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tagmanager = Googletagmanager::findOrFail($id);

        $request->validate([
            'tag_manager_id' => 'required|string|max:255|unique:googletagmanagers,tag_manager_id,' . $id,
        ], [
            'tag_manager_id.required' => 'Tag Manager ID is required.',
            'tag_manager_id.unique'   => 'This Tag Manager ID already exists.',
        ]);

        $tagmanager->update([
            'tag_manager_id' => $request->tag_manager_id,
            'status'         => $request->has('status') ? true : false,
        ]);

        return redirect()->route('admin.googletagmanager.index')
            ->with('success', 'Tag Manager updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tagmanager = Googletagmanager::findOrFail($id);
        $tagmanager->delete();

        return redirect()->route('admin.googletagmanager.index')
            ->with('success', 'Tag Manager deleted successfully.');
    }

    /**
     * Toggle status of the specified resource.
     */
    public function toggleStatus(string $id)
    {
        $tagmanager = Googletagmanager::findOrFail($id);
        $tagmanager->update(['status' => !$tagmanager->status]);

        return redirect()->route('admin.googletagmanager.index')
            ->with('success', 'Status updated successfully.');
    }
}

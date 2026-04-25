<?php
// app/Http/Controllers/Admin/DuplicateordersettingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Duplicateordersetting;
use Illuminate\Http\Request;

class DuplicateordersettingController extends Controller
{
    /**
     * INDEX - Settings form page
     */
    public function index()
    {
        $setting = Duplicateordersetting::instance();
        return view('admin.duplicateordersetting.index', compact('setting'));
    }

    /**
     * CREATE - Redirect to index (single-record, no separate create)
     */
    public function create()
    {
        return redirect()->route('admin.duplicateordersetting.index');
    }

    /**
     * STORE - Redirect to index
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.duplicateordersetting.index');
    }

    /**
     * SHOW - Redirect to index
     */
    public function show(string $id)
    {
        return redirect()->route('admin.duplicateordersetting.index');
    }

    /**
     * EDIT - Show edit form (same as index for single-record)
     */
    public function edit(string $id)
    {
        $setting = Duplicateordersetting::findOrFail($id);
        return view('admin.duplicateordersetting.edit', compact('setting'));
    }

    /**
     * UPDATE - Save settings
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'duplicate_check_type'    => 'required|string|in:Product + IP + Phone,Product + Phone,Product + IP,Phone Only,IP Only',
            'duplicate_time_limit'    => 'required|integer|min:1|max:9999',
            'duplicate_check_message' => 'nullable|string|max:2000',
        ]);

        $setting = Duplicateordersetting::findOrFail($id);

        $setting->update([
            'allow_duplicate_orders'  => $request->has('allow_duplicate_orders'),
            'duplicate_check_type'    => $validated['duplicate_check_type'],
            'duplicate_time_limit'    => $validated['duplicate_time_limit'],
            'duplicate_check_message' => $validated['duplicate_check_message'] ?? null,
        ]);

        return redirect()->route('admin.duplicateordersetting.index')
            ->with('success', 'Duplicate Order Settings updated successfully.');
    }

    /**
     * DESTROY - Not applicable for settings
     */
    public function destroy(string $id)
    {
        return redirect()->route('admin.duplicateordersetting.index')
            ->with('error', 'Settings cannot be deleted.');
    }

    /**
     * TOGGLE STATUS - Quick toggle for allow_duplicate_orders
     */
    public function toggleStatus(string $id)
    {
        $setting = Duplicateordersetting::findOrFail($id);
        $setting->update([
            'allow_duplicate_orders' => !$setting->allow_duplicate_orders,
        ]);

        return redirect()->route('admin.duplicateordersetting.index')
            ->with('success', 'Status toggled successfully.');
    }
}

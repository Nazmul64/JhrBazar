<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Themecolorssetting;
use Illuminate\Http\Request;

class ThemecolorssettingController extends Controller
{
    /**
     * Display / index page — always shows the single active record (or a default).
     */
    public function index()
    {
        $setting = Themecolorssetting::where('is_active', true)->latest()->first();

        if (!$setting) {
            // Seed a default so the view always has data
            $setting = Themecolorssetting::create([
                'primary_color'   => '#eb2e61',
                'secondary_color' => '#fbd5df',
                'palette_name'    => 'Default',
                'is_active'       => true,
            ]);
        }

        $palette = Themecolorssetting::generatePalette($setting->primary_color);

        return view('admin.themecolorssettings.index', compact('setting', 'palette'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $defaultPrimary = '#eb2e61';
        $palette        = Themecolorssetting::generatePalette($defaultPrimary);

        return view('admin.themecolorssettings.create', compact('palette', 'defaultPrimary'));
    }

    /**
     * Store a new setting.
     */
    public function store(Request $request)
    {
        $request->validate([
            'primary_color'   => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'secondary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'palette_name'    => 'nullable|string|max:255',
        ]);

        // Deactivate previous active setting
        Themecolorssetting::where('is_active', true)->update(['is_active' => false]);

        Themecolorssetting::create([
            'primary_color'   => strtolower($request->primary_color),
            'secondary_color' => strtolower($request->secondary_color),
            'palette_name'    => $request->palette_name,
            'is_active'       => true,
        ]);

        return redirect()->route('admin.themecolorssettings.index')
                         ->with('success', 'Theme colors saved successfully!');
    }

    /**
     * Show a single record.
     */
    public function show(string $id)
    {
        $setting = Themecolorssetting::findOrFail($id);
        $palette = Themecolorssetting::generatePalette($setting->primary_color);

        return view('admin.themecolorssettings.show', compact('setting', 'palette'));
    }

    /**
     * Show edit form.
     */
    public function edit(string $id)
    {
        $setting = Themecolorssetting::findOrFail($id);
        $palette = Themecolorssetting::generatePalette($setting->primary_color);

        return view('admin.themecolorssettings.edit', compact('setting', 'palette'));
    }

    /**
     * Update an existing setting.
     */
    public function update(Request $request, string $id)
    {
        $setting = Themecolorssetting::findOrFail($id);

        $request->validate([
            'primary_color'   => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'secondary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'palette_name'    => 'nullable|string|max:255',
        ]);

        $setting->update([
            'primary_color'   => strtolower($request->primary_color),
            'secondary_color' => strtolower($request->secondary_color),
            'palette_name'    => $request->palette_name,
        ]);

        return redirect()->route('admin.themecolorssettings.index')
                         ->with('success', 'Theme colors updated successfully!');
    }

    /**
     * Delete a setting.
     */
    public function destroy(string $id)
    {
        Themecolorssetting::findOrFail($id)->delete();

        return redirect()->route('admin.themecolorssettings.index')
                         ->with('success', 'Setting deleted.');
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(Themecolorssetting $Themecolorssetting)
    {
        // Deactivate all, then activate this one
        Themecolorssetting::where('is_active', true)->update(['is_active' => false]);
        $Themecolorssetting->update(['is_active' => true]);

        return back()->with('success', 'Active theme updated.');
    }

    /**
     * AJAX: generate palette from a hex color (used by the modal).
     */
    public function generatePalette(Request $request)
    {
        $request->validate([
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $palette   = Themecolorssetting::generatePalette($request->color);
        $secondary = Themecolorssetting::deriveSecondary($request->color);

        return response()->json([
            'palette'   => $palette,
            'secondary' => $secondary,
        ]);
    }
}

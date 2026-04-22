<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GenaralSetting;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $setting = GenaralSetting::first();
        return view('admin.generalsetting.index', compact('setting'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'website_name'          => 'nullable|string|max:255',
            'website_title'         => 'nullable|string|max:255',
            'default_currency'      => 'nullable|string|max:100',
            'currency_position'     => 'nullable|string|max:50',
            'logo'                  => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            'favicon'               => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp,ico|max:1024',
            'app_logo'              => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            'mobile_number'         => 'nullable|string|max:20',
            'email_address'         => 'nullable|email|max:255',
            'address'               => 'nullable|string|max:500',
            'google_playstore_link' => 'nullable|url|max:500',
            'apple_store_link'      => 'nullable|url|max:500',
            'hotline_number'        => 'nullable|string|max:20',
            'footer_text'           => 'nullable|string|max:500',
            'footer_logo'           => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            'footer_qr'             => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:1024',
        ]);

        $data = $request->except(['_token', 'logo', 'favicon', 'app_logo', 'footer_logo', 'footer_qr']);

        $data['show_download_app']   = $request->has('show_download_app') ? 1 : 0;
        $data['show_footer_section'] = $request->has('show_footer_section') ? 1 : 0;

        $uploadPath = public_path('uploads/generalsetting');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $imageFields = ['logo', 'favicon', 'app_logo', 'footer_logo', 'footer_qr'];
        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $file     = $request->file($field);
                $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                $data[$field] = 'uploads/generalsetting/' . $filename;
            }
        }

        GenaralSetting::create($data);

        return redirect()->route('admin.generalsettings.index')
            ->with('success', 'Settings saved successfully!');
    }

    public function edit(string $id)
    {
        $setting = GenaralSetting::findOrFail($id);
        return view('admin.generalsetting.edit', compact('setting'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'website_name'          => 'nullable|string|max:255',
            'website_title'         => 'nullable|string|max:255',
            'default_currency'      => 'nullable|string|max:100',
            'currency_position'     => 'nullable|string|max:50',
            'logo'                  => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            'favicon'               => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp,ico|max:1024',
            'app_logo'              => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            'mobile_number'         => 'nullable|string|max:20',
            'email_address'         => 'nullable|email|max:255',
            'address'               => 'nullable|string|max:500',
            'google_playstore_link' => 'nullable|url|max:500',
            'apple_store_link'      => 'nullable|url|max:500',
            'hotline_number'        => 'nullable|string|max:20',
            'footer_text'           => 'nullable|string|max:500',
            'footer_logo'           => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            'footer_qr'             => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:1024',
        ]);

        $setting = GenaralSetting::findOrFail($id);

        $data = $request->except(['_token', '_method', 'logo', 'favicon', 'app_logo', 'footer_logo', 'footer_qr']);

        $data['show_download_app']   = $request->has('show_download_app') ? 1 : 0;
        $data['show_footer_section'] = $request->has('show_footer_section') ? 1 : 0;

        $uploadPath = public_path('uploads/generalsetting');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $imageFields = ['logo', 'favicon', 'app_logo', 'footer_logo', 'footer_qr'];
        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                if ($setting->$field && file_exists(public_path($setting->$field))) {
                    unlink(public_path($setting->$field));
                }
                $file     = $request->file($field);
                $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                $data[$field] = 'uploads/generalsetting/' . $filename;
            }
        }

        $setting->update($data);

        return redirect()->route('admin.generalsettings.index')
            ->with('success', 'Settings updated successfully!');
    }

    public function toggleStatus(Request $request, string $id)
    {
        $setting = GenaralSetting::findOrFail($id);
        $field   = $request->input('field');

        $allowedFields = ['show_download_app', 'show_footer_section'];

        if (!in_array($field, $allowedFields)) {
            return response()->json(['error' => 'Invalid field'], 422);
        }

        $setting->$field = !$setting->$field;
        $setting->save();

        return response()->json([
            'success' => true,
            'status'  => $setting->$field,
        ]);
    }

    public function destroy(string $id)
    {
        $setting = GenaralSetting::findOrFail($id);

        $imageFields = ['logo', 'favicon', 'app_logo', 'footer_logo', 'footer_qr'];
        foreach ($imageFields as $field) {
            if ($setting->$field && file_exists(public_path($setting->$field))) {
                unlink(public_path($setting->$field));
            }
        }

        $setting->delete();

        return redirect()->route('admin.generalsettings.index')
            ->with('success', 'Settings deleted successfully!');
    }
}

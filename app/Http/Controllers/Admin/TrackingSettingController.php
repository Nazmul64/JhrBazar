<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrackingSetting;
use Illuminate\Http\Request;

class TrackingSettingController extends Controller
{
    /**
     * Display the Tracking Settings configuration page.
     */
    public function index()
    {
        $setting = TrackingSetting::first();

        if (!$setting) {
            $setting = TrackingSetting::create([
                'is_active' => true,
                'gtm_id' => 'GTM-T82G7W9F',
                'gtm_enabled' => true,
                'ga4_measurement_id' => 'G-ABC123XYZ',
                'ga4_enabled' => true,
                'fb_pixel_id' => '123456789012345',
                'fb_pixel_enabled' => true,
                'fb_access_token' => null,
                'tiktok_pixel_id' => 'C9ABCDEF1234567',
                'tiktok_pixel_enabled' => false,
                'datalayer_events' => TrackingSetting::defaultDataLayerEvents(),
                'cookie_lifetime_days' => 90,
                'track_utm' => true,
                'track_referrer' => true,
                'track_click_ids' => true,
                'track_direct' => true,
            ]);
        }

        return view('admin.tracking.settings', compact('setting'));
    }

    /**
     * Update tracking settings in database.
     */
    public function update(Request $request)
    {
        $request->validate([
            'gtm_id' => 'nullable|string|max:50',
            'ga4_measurement_id' => 'nullable|string|max:50',
            'fb_pixel_id' => 'nullable|string|max:50',
            'fb_access_token' => 'nullable|string|max:500',
            'tiktok_pixel_id' => 'nullable|string|max:50',
            'cookie_lifetime_days' => 'nullable|integer|min:1|max:365',
            'custom_head_script' => 'nullable|string',
            'custom_body_script' => 'nullable|string',
        ]);

        $setting = TrackingSetting::first();
        if (!$setting) {
            $setting = new TrackingSetting();
        }

        $setting->is_active = $request->has('is_active') ? (bool)$request->is_active : false;
        $setting->gtm_enabled = $request->has('gtm_enabled') ? (bool)$request->gtm_enabled : false;
        $setting->gtm_id = $request->gtm_id;

        $setting->ga4_enabled = $request->has('ga4_enabled') ? (bool)$request->ga4_enabled : false;
        $setting->ga4_measurement_id = $request->ga4_measurement_id;

        $setting->fb_pixel_enabled = $request->has('fb_pixel_enabled') ? (bool)$request->fb_pixel_enabled : false;
        $setting->fb_pixel_id = $request->fb_pixel_id;
        $setting->fb_access_token = $request->fb_access_token;

        $setting->tiktok_pixel_enabled = $request->has('tiktok_pixel_enabled') ? (bool)$request->tiktok_pixel_enabled : false;
        $setting->tiktok_pixel_id = $request->tiktok_pixel_id;

        // DataLayer Events Map
        $defaultEvents = TrackingSetting::defaultDataLayerEvents();
        $submittedEvents = $request->input('datalayer_events', []);
        $dataLayerMap = [];
        foreach ($defaultEvents as $eventKey => $defaultVal) {
            $dataLayerMap[$eventKey] = isset($submittedEvents[$eventKey]) && ($submittedEvents[$eventKey] == '1' || $submittedEvents[$eventKey] == 'on' || $submittedEvents[$eventKey] === true);
        }
        $setting->datalayer_events = $dataLayerMap;

        // Attribution & Cookie Settings
        $setting->cookie_lifetime_days = $request->input('cookie_lifetime_days', 90);
        $setting->track_utm = $request->has('track_utm') ? (bool)$request->track_utm : false;
        $setting->track_referrer = $request->has('track_referrer') ? (bool)$request->track_referrer : false;
        $setting->track_click_ids = $request->has('track_click_ids') ? (bool)$request->track_click_ids : false;
        $setting->track_direct = $request->has('track_direct') ? (bool)$request->track_direct : false;

        $setting->custom_head_script = $request->custom_head_script;
        $setting->custom_body_script = $request->custom_body_script;

        $setting->save();

        return redirect()->back()->with('success', 'Tracking & DataLayer settings updated successfully!');
    }

    /**
     * API endpoint to return public tracking configuration for React frontend.
     */
    public function getPublicConfig()
    {
        $setting = TrackingSetting::first();

        if (!$setting || !$setting->is_active) {
            return response()->json([
                'is_active' => false,
                'gtm_enabled' => false,
                'ga4_enabled' => false,
                'fb_pixel_enabled' => false,
                'tiktok_pixel_enabled' => false,
                'datalayer_events' => [],
                'cookie_lifetime_days' => 90,
            ]);
        }

        return response()->json([
            'is_active' => (bool)$setting->is_active,
            'gtm_enabled' => (bool)$setting->gtm_enabled,
            'gtm_id' => $setting->gtm_id,
            'ga4_enabled' => (bool)$setting->ga4_enabled,
            'ga4_measurement_id' => $setting->ga4_measurement_id,
            'fb_pixel_enabled' => (bool)$setting->fb_pixel_enabled,
            'fb_pixel_id' => $setting->fb_pixel_id,
            'tiktok_pixel_enabled' => (bool)$setting->tiktok_pixel_enabled,
            'tiktok_pixel_id' => $setting->tiktok_pixel_id,
            'datalayer_events' => $setting->datalayer_events ?? TrackingSetting::defaultDataLayerEvents(),
            'cookie_lifetime_days' => $setting->cookie_lifetime_days ?? 90,
            'track_utm' => (bool)$setting->track_utm,
            'track_referrer' => (bool)$setting->track_referrer,
            'track_click_ids' => (bool)$setting->track_click_ids,
            'track_direct' => (bool)$setting->track_direct,
        ]);
    }
}

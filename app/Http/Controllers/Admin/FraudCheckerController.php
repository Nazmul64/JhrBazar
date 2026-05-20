<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ipblockmanage;
use App\Models\CyberAlert;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FraudCheckerController extends Controller
{
    /**
     * Public API: Check if user's IP is blocked. If blocked, log a new cyber alert attempt.
     */
    public function checkIpBlocked(Request $request)
    {
        $ip = $request->ip();

        // Check if IP is blocked in our system
        $blocked = Ipblockmanage::where('ip_address', $ip)
            ->where('is_active', true)
            ->exists();

        if (!$blocked) {
            return response()->json([
                'blocked' => false
            ]);
        }

        // Detect device type
        $agent = $request->userAgent() ?? '';
        $deviceType = 'Desktop';
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $agent)) {
            $deviceType = 'Mobile';
        }

        // Fetch location and ISP dynamically
        $isp = 'Unknown Broadband WiFi';
        $location = 'Dhaka, Bangladesh';
        $lat = 23.6850;
        $lon = 90.3563;

        if ($ip === '127.0.0.1' || $ip === '::1') {
            $isp = 'Dot Internet (Broadband WiFi)';
            $location = 'Dhaka, Uttara, Bangladesh';
            $lat = 23.8759;
            $lon = 90.3795;
        } else {
            try {
                $ctx = stream_context_create(['http' => ['timeout' => 2]]);
                $res = @file_get_contents("http://ip-api.com/json/{$ip}", false, $ctx);
                if ($res) {
                    $data = json_decode($res, true);
                    if ($data && isset($data['status']) && $data['status'] === 'success') {
                        $isp = ($data['isp'] ?? 'Unknown ISP') . ' (Broadband WiFi)';
                        $location = ($data['city'] ?? '') . ', ' . ($data['regionName'] ?? '') . ', ' . ($data['country'] ?? '');
                        $lat = $data['lat'] ?? 23.6850;
                        $lon = $data['lon'] ?? 90.3563;
                    }
                }
            } catch (\Exception $e) {
                // Keep defaults
            }
        }

        // Create the cyber alert attempt logs
        $alert = CyberAlert::create([
            'ip_address'    => $ip,
            'wifi_provider' => $isp,
            'location'      => $location,
            'lat'           => $lat,
            'lon'           => $lon,
            'device_agent'  => $agent,
            'device_type'   => $deviceType,
            'attempted_at'  => now(),
        ]);

        return response()->json([
            'blocked' => true,
            'data'    => [
                'ip'            => $ip,
                'wifi_provider' => $isp,
                'location'      => $location,
                'lat'           => $lat,
                'lon'           => $lon,
                'device_agent'  => $agent,
                'device_type'   => $deviceType,
                'time'          => Carbon::parse($alert->attempted_at)->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Admin backend list view of cyber security alerts
     */
    public function index(Request $request)
    {
        $alerts = CyberAlert::latest()->paginate(15);
        return view('admin.fraud.alerts.cyber_alerts', compact('alerts'));
    }

    /**
     * Admin backend action: Delete alert
     */
    public function destroy($id)
    {
        $alert = CyberAlert::findOrFail($id);
        $alert->delete();

        return redirect()->back()->with('success', 'Alert entry deleted successfully.');
    }
}

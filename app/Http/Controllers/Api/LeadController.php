<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IncompleteOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    /**
     * Save an incomplete order (lead) via AJAX.
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'name'  => 'nullable|string',
            'email' => 'nullable|string',
            'total' => 'nullable|numeric',
            'payment_method' => 'nullable|string',
            'area' => 'nullable|string',
            'address' => 'nullable|string',
            'cart_items' => 'nullable|array',
            'url' => 'nullable|string',
            'shop_id' => 'nullable',
            'seller_id' => 'nullable',
            'device' => 'nullable|string',
            'browser' => 'nullable|string',
            'os' => 'nullable|string',
        ]);

        $phone = $request->phone;
        if (strlen($phone) < 11) return response()->json(['success' => false]);

        $shopId = $request->seller_id ?? $request->shop_id;

        $browser = $request->browser;
        if (!$browser) {
            $userAgent = $request->header('User-Agent');
            $browser = $request->os ? $request->os . ' (' . $userAgent . ')' : $userAgent;
        }

        $device = $request->device ?? $this->getDeviceType($request->header('User-Agent'));

        $lead = IncompleteOrder::updateOrCreate(
            ['phone' => $phone, 'shop_id' => $shopId],
            [
                'name'            => $request->name,
                'email'           => $request->email,
                'estimated_total' => $request->total,
                'payment_method'  => $request->payment_method,
                'area'            => $request->area,
                'address'         => $request->address,
                'cart_items'      => $request->cart_items, // Should be json/array
                'url'             => $request->url,
                'ip_address'      => $request->ip(),
                'browser'         => $browser,
                'device'          => $device,
                'status'          => 'incomplete'
            ]
        );

        // Set persistent cookies for returning customer tracking (1 year)
        try {
            \Illuminate\Support\Facades\Cookie::queue('customer_tracker_phone', $phone, 525600);
            if ($request->name) {
                \Illuminate\Support\Facades\Cookie::queue('customer_tracker_name', $request->name, 525600);
            }
        } catch (\Throwable $e) {
            // Ignore cookie queue failures in stateless environments
        }

        return response()->json(['success' => true, 'id' => $lead->id]);
    }

    private function getDeviceType($userAgent)
    {
        $userAgent = strtolower($userAgent ?? '');
        if (str_contains($userAgent, 'mobile') || str_contains($userAgent, 'android') || str_contains($userAgent, 'iphone')) {
            return 'mobile';
        }
        if (str_contains($userAgent, 'tablet') || str_contains($userAgent, 'ipad')) {
            return 'tablet';
        }
        return 'desktop';
    }
}

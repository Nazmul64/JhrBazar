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
            'shop_id' => 'nullable',
            'seller_id' => 'nullable',
        ]);

        $shopId = $request->shop_id;
        $phone = $request->phone;
        if (strlen($phone) < 11) return response()->json(['success' => false]);

        $shopId = $request->seller_id;
        
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
                'browser'         => $request->header('User-Agent'),
                'device'          => $request->header('Sec-Ch-Ua-Platform') ?? 'Unknown',
                'status'          => 'incomplete'
            ]
        );

        return response()->json(['success' => true, 'id' => $lead->id]);
    }

    private function getDeviceType($userAgent)
    {
        $userAgent = strtolower($userAgent);
        if (str_contains($userAgent, 'mobile') || str_contains($userAgent, 'android') || str_contains($userAgent, 'iphone')) {
            return 'mobile';
        }
        if (str_contains($userAgent, 'tablet') || str_contains($userAgent, 'ipad')) {
            return 'tablet';
        }
        return 'desktop';
    }
}

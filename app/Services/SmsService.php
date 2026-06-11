<?php

namespace App\Services;

use App\Models\SmsGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS using configured gateway.
     */
    public static function send($phone, $message)
    {
        $gateway = SmsGateway::where('status', true)->first();

        if (!$gateway) {
            return false;
        }

        // Mock bypass for local environment or mock/test gateway URLs
        if (config('app.env') === 'local' || str_contains($gateway->url, 'mock') || str_contains($gateway->url, '127.0.0.1') || str_contains($gateway->url, 'localhost')) {
            Log::info("SMS (MOCK) sent to $phone: $message");
            return true;
        }

        try {
            $response = Http::get($gateway->url, [
                'api_key'  => $gateway->api_key,
                'type'     => 'text',
                'contacts' => $phone,
                'senderid' => $gateway->sender_id,
                'msg'      => $message,
            ]);

            if ($response->successful()) {
                Log::info("SMS sent to $phone: $message");
                return true;
            } else {
                Log::error("Failed to send SMS to $phone. Status: " . $response->status());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("SMS Sending Error: " . $e->getMessage());
            return false;
        }
    }
}

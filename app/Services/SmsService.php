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
        $gateway = SmsGateway::first();

        if (!$gateway || !$gateway->status) {
            return false;
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

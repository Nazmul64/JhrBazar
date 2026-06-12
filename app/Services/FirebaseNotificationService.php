<?php

namespace App\Services;

use App\Models\FirebaseSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    /**
     * Send notification to one or more FCM tokens using FCM HTTP v1 API
     *
     * @param string|array $tokens Single FCM token or array of tokens
     * @param string $title Title of the notification
     * @param string $body Body text of the notification
     * @param string|null $imageUrl Optional image URL
     * @param array $data Optional custom data payload
     * @return array Array containing success count, failure count, and errors
     */
    public function sendNotification($tokens, string $title, string $body, ?string $imageUrl = null, array $data = []): array
    {
        $setting = FirebaseSetting::where('status', 1)->first();
        if (!$setting) {
            return [
                'success' => false,
                'message' => 'No active Firebase configuration found.',
                'success_count' => 0,
                'failure_count' => is_array($tokens) ? count($tokens) : 1,
                'errors' => ['Active configuration missing.']
            ];
        }

        $projectId = $setting->project_id;
        $serviceAccountJson = $setting->service_account_json;

        if (empty($projectId) || empty($serviceAccountJson)) {
            return [
                'success' => false,
                'message' => 'Firebase Project ID or Service Account Credentials missing.',
                'success_count' => 0,
                'failure_count' => is_array($tokens) ? count($tokens) : 1,
                'errors' => ['Credentials not fully configured.']
            ];
        }

        // Get Access Token
        $accessToken = $this->getAccessToken($serviceAccountJson);
        if (!$accessToken) {
            return [
                'success' => false,
                'message' => 'Failed to generate Google OAuth2 Access Token.',
                'success_count' => 0,
                'failure_count' => is_array($tokens) ? count($tokens) : 1,
                'errors' => ['Google OAuth2 token generation failed. Check your service account JSON.']
            ];
        }

        $fcmUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $tokens = is_array($tokens) ? array_filter($tokens) : [$tokens];
        
        $successCount = 0;
        $failureCount = 0;
        $errors = [];

        foreach ($tokens as $token) {
            if (empty($token)) continue;

            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                ]
            ];

            if ($imageUrl) {
                $payload['message']['notification']['image'] = $imageUrl;
            }

            if (!empty($data)) {
                // FCM HTTP v1 requires values in 'data' to be strings
                $stringData = [];
                foreach ($data as $key => $value) {
                    $stringData[(string)$key] = (string)$value;
                }
                $payload['message']['data'] = $stringData;
            }

            try {
                $response = Http::withToken($accessToken)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($fcmUrl, $payload);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $failureCount++;
                    $errors[] = "Token error: " . ($response->json('error.message') ?? $response->body());
                }
            } catch (\Exception $e) {
                $failureCount++;
                $errors[] = "Connection exception: " . $e->getMessage();
                Log::error("FCM Send Exception: " . $e->getMessage());
            }
        }

        return [
            'success' => $successCount > 0,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'errors' => $errors
        ];
    }

    /**
     * Generate Google OAuth2 access token using pure PHP JWT signature (RS256)
     */
    private function getAccessToken(string $serviceAccountJson): ?string
    {
        try {
            $data = json_decode($serviceAccountJson, true);
            if (!is_array($data) || !isset($data['private_key']) || !isset($data['client_email'])) {
                Log::error("FCM Service Account JSON invalid schema.");
                return null;
            }

            $privateKey = $data['private_key'];
            $clientEmail = $data['client_email'];

            $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
            $now = time();
            $payload = json_encode([
                'iss' => $clientEmail,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600
            ]);

            $base64UrlHeader = $this->base64UrlEncode($header);
            $base64UrlPayload = $this->base64UrlEncode($payload);

            $signatureInput = $base64UrlHeader . "." . $base64UrlPayload;
            $signature = '';

            if (!openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
                Log::error("FCM openssl_sign signature generation failed.");
                return null;
            }

            $base64UrlSignature = $this->base64UrlEncode($signature);
            $jwt = $signatureInput . "." . $base64UrlSignature;

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error("Google OAuth2 Token request failed: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("Google OAuth2 Access Token Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper to URL-safe Base64 encode
     */
    private function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}

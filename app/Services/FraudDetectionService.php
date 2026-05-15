<?php

namespace App\Services;

use App\Models\FraudCheck;
use App\Models\FraudRule;
use App\Models\FraudAlert;
use App\Models\FraudBlacklist;
use App\Models\FraudApi;
use Illuminate\Support\Facades\Http;

class FraudDetectionService
{
    private float $score         = 0;
    private array $triggeredRules = [];
    private array $flags          = [];

    /**
     * Call external courier check API
     */
    public function checkExternalCourierApi(string $phone)
    {
        $activeApi = FraudApi::where('is_active', true)->first();
        if (!$activeApi) {
            return ['success' => false, 'message' => 'No active Fraud API found.'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $activeApi->api_key,
                'Accept'        => 'application/json',
            ])->get($activeApi->api_url, [
                'phone' => $phone
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $response->json(),
                    'api_type' => $activeApi->type
                ];
            }

            return [
                'success' => false,
                'status'  => $response->status(),
                'message' => $response->body()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'API Connection Error: ' . $e->getMessage()
            ];
        }
    }

    // ─── Main Entry ────────────────────────────────────────────────────────────

    public function analyze(array $data): FraudCheck
    {
        $this->score          = 0;
        $this->triggeredRules = [];
        $this->flags          = [];

        // 1. Blacklist check
        $this->checkBlacklists($data);

        // 2. IP & Network analysis
        $networkData = $this->analyzeNetwork($data['ip_address'] ?? null);

        // 3. Email analysis
        $emailData = $this->analyzeEmail($data['customer_email'] ?? null);

        // 4. Phone analysis
        $phoneData = $this->analyzePhone($data['customer_phone'] ?? null);

        // 5. Transaction analysis
        $this->analyzeTransaction($data);

        // 6. Courier Success Analysis (Steadfast & Pathao)
        $courierData = $this->analyzeCourierHistory($data['customer_phone'] ?? null, $data['ip_address'] ?? null);

        // 7. Rule engine
        $this->runRuleEngine(array_merge($data, $networkData, $emailData, $phoneData, $courierData));

        // 7. Clamp 0–100
        $this->score = max(0, min(100, $this->score));

        // 8. Determine risk & status
        $riskLevel = FraudCheck::getRiskLevel($this->score);
        $status    = $this->determineStatus($riskLevel);

        // 9. Persist
        $check = FraudCheck::create([
            'check_id'             => FraudCheck::generateCheckId(),
            'type'                 => $data['type']                  ?? 'identity',
            'input_value'          => $data['input_value']           ?? ($data['customer_email'] ?? ''),
            'status'               => $status,
            'risk_score'           => $this->score,
            'risk_level'           => $riskLevel,
            'customer_name'        => $data['customer_name']         ?? null,
            'customer_email'       => $data['customer_email']        ?? null,
            'customer_phone'       => $data['customer_phone']        ?? null,
            'ip_address'           => $data['ip_address']            ?? null,
            'country'              => $networkData['country']        ?? null,
            'city'                 => $networkData['city']           ?? null,
            'vpn_detected'         => $networkData['vpn_detected']   ?? false,
            'proxy_detected'       => $networkData['proxy_detected'] ?? false,
            'tor_detected'         => $networkData['tor_detected']   ?? false,
            'email_valid'          => $emailData['email_valid']      ?? null,
            'email_disposable'     => $emailData['email_disposable'] ?? null,
            'email_domain'         => $emailData['email_domain']     ?? null,
            'email_domain_age'     => $emailData['email_domain_age'] ?? null,
            'social_profiles'      => $emailData['social_profiles']  ?? null,
            'phone_valid'          => $phoneData['phone_valid']      ?? null,
            'phone_carrier'        => $phoneData['phone_carrier']    ?? null,
            'phone_type'           => $phoneData['phone_type']       ?? null,
            'phone_country'        => $phoneData['phone_country']    ?? null,
            'transaction_amount'   => $data['transaction_amount']    ?? null,
            'transaction_currency' => $data['transaction_currency']  ?? 'BDT',
            'device_type'          => $data['device_type']           ?? null,
            'browser'              => $data['browser']               ?? null,
            'os'                   => $data['os']                    ?? null,
            'triggered_rules'      => $this->triggeredRules,
            'flags'                => $this->flags,
            'courier_data'         => $courierData,
            'notes'                => $data['notes']                 ?? null,
            'created_by'           => auth()->id(),
        ]);

        // 10. Auto-generate alerts for high/critical
        if (in_array($riskLevel, ['high', 'critical'])) {
            $this->generateAlerts($check);
        }

        return $check;
    }

    // ─── Blacklist Check ───────────────────────────────────────────────────────

    private function checkBlacklists(array $data): void
    {
        $targets = [
            'email' => $data['customer_email'] ?? null,
            'phone' => $data['customer_phone'] ?? null,
            'ip'    => $data['ip_address']     ?? null,
        ];

        foreach ($targets as $type => $value) {
            if ($value && FraudBlacklist::isBlacklisted($type, $value)) {
                $this->score += 80;
                $this->flags[]          = strtoupper($type) . '_BLACKLISTED';
                $this->triggeredRules[] = [
                    'rule'   => 'BLACKLIST_CHECK',
                    'name'   => ucfirst($type) . ' Blacklisted',
                    'field'  => $type,
                    'action' => 'decline',
                    'impact' => 80,
                ];
            }
        }
    }

    // ─── Network Analysis ──────────────────────────────────────────────────────

    private function analyzeNetwork(?string $ip): array
    {
        if (! $ip) {
            return [];
        }

        // In production: integrate ip-api.com, ipinfo.io, or IPQS
        $data = [
            'country'        => 'BD',
            'city'           => 'Dhaka',
            'vpn_detected'   => false,
            'proxy_detected' => false,
            'tor_detected'   => false,
        ];

        // Private/loopback IPs are safe
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return $data;
        }

        if ($data['vpn_detected']) {
            $this->score += 20;
            $this->flags[] = 'VPN_DETECTED';
        }
        if ($data['proxy_detected']) {
            $this->score += 25;
            $this->flags[] = 'PROXY_DETECTED';
        }
        if ($data['tor_detected']) {
            $this->score += 40;
            $this->flags[] = 'TOR_DETECTED';
        }

        return $data;
    }

    // ─── Email Analysis ────────────────────────────────────────────────────────

    private function analyzeEmail(?string $email): array
    {
        if (! $email) {
            return [];
        }

        $disposableDomains = [
            'mailinator.com', 'tempmail.com', 'guerrillamail.com',
            'throwaway.email', '10minutemail.com', 'yopmail.com',
            'sharklasers.com', 'trashmail.com', 'fakeinbox.com',
            'dispostable.com', 'maildrop.cc', 'spamgourmet.com',
        ];

        $domain       = strtolower(substr(strrchr($email, '@'), 1));
        $isDisposable = in_array($domain, $disposableDomains, true);
        $isValid      = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;

        // In production: use SEON / Hunter / ZeroBounce
        $knownDomains    = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
        $socialProfiles  = [];
        if (in_array($domain, $knownDomains, true)) {
            $socialProfiles = ['google' => true];
        }

        if ($isDisposable) {
            $this->score += 30;
            $this->flags[] = 'DISPOSABLE_EMAIL';
        }

        if (! $isValid) {
            $this->score += 25;
            $this->flags[] = 'INVALID_EMAIL';
        }

        return [
            'email_valid'      => $isValid,
            'email_disposable' => $isDisposable,
            'email_domain'     => $domain,
            'email_domain_age' => rand(30, 3650), // Replace with WHOIS in production
            'social_profiles'  => $socialProfiles,
        ];
    }

    // ─── Phone Analysis ────────────────────────────────────────────────────────

    private function analyzePhone(?string $phone): array
    {
        if (! $phone) {
            return [];
        }

        $digits    = preg_replace('/\D/', '', $phone);
        $isBD      = str_starts_with($phone, '+880') || str_starts_with($phone, '01');
        $isValid   = strlen($digits) >= 10;
        $isVoip    = ! $isBD;

        if ($isVoip) {
            $this->score += 15;
            $this->flags[] = 'VOIP_PHONE';
        }

        if (! $isValid) {
            $this->score += 10;
            $this->flags[] = 'INVALID_PHONE';
        }

        return [
            'phone_valid'   => $isValid,
            'phone_carrier' => $isBD ? 'GP / Robi / Banglalink' : 'Unknown',
            'phone_type'    => $isVoip ? 'voip' : 'mobile',
            'phone_country' => $isBD ? 'BD' : 'Unknown',
        ];
    }

    // ─── Courier Analysis ──────────────────────────────────────────────────────
    public function analyzeCourierHistory(?string $phone, ?string $ip = null): array
    {
        if (!$phone && !$ip) return [];

        $query = \Illuminate\Support\Facades\DB::table('pointofsalepos');
        if ($phone) $query->where('phone', $phone);
        if ($ip) $query->orWhere('ip_address', $ip);

        $orders = $query->get();
        if ($orders->isEmpty()) return [];

        $totalCount = $orders->count();
        $steadfastOrders = $orders->where('courier_name', 'steadfast');
        $pathaoOrders = $orders->where('courier_name', 'pathao');

        $steadfastRejected = $steadfastOrders->whereIn('courier_status', ['cancelled', 'rejected', 'returned'])->count();
        $pathaoRejected = $pathaoOrders->whereIn('courier_status', ['cancelled', 'rejected', 'returned'])->count();

        $steadfastSuccess = $steadfastOrders->where('courier_status', 'delivered')->count();
        $pathaoSuccess = $pathaoOrders->where('courier_status', 'delivered')->count();

        $sfRate = $steadfastOrders->count() > 0 ? ($steadfastSuccess / $steadfastOrders->count()) * 100 : 0;
        $ptRate = $pathaoOrders->count() > 0 ? ($pathaoSuccess / $pathaoOrders->count()) * 100 : 0;

        if ($sfRate < 50 && $steadfastOrders->count() > 2) {
            $this->score += 30;
            $this->flags[] = 'LOW_STEADFAST_SUCCESS_RATE';
        }

        if ($ptRate < 50 && $pathaoOrders->count() > 2) {
            $this->score += 30;
            $this->flags[] = 'LOW_PATHAO_SUCCESS_RATE';
        }

        return [
            'total_orders' => $totalCount,
            'sf_total' => $steadfastOrders->count(),
            'sf_rejected' => $steadfastRejected,
            'sf_success_rate' => $sfRate,
            'pt_total' => $pathaoOrders->count(),
            'pt_rejected' => $pathaoRejected,
            'pt_success_rate' => $ptRate,
        ];
    }

    // ─── Transaction Analysis ──────────────────────────────────────────────────

    private function analyzeTransaction(array $data): void
    {
        $amount = (float) ($data['transaction_amount'] ?? 0);

        if ($amount > 100_000) {
            $this->score += 20;
            $this->flags[] = 'HIGH_VALUE_TRANSACTION';
        }

        if ($amount > 500_000) {
            $this->score += 25;
            $this->flags[] = 'VERY_HIGH_VALUE_TRANSACTION';
        }
    }

    // ─── Rule Engine ───────────────────────────────────────────────────────────

    private function runRuleEngine(array $data): void
    {
        $rules = FraudRule::active()->orderByDesc('priority')->get();

        foreach ($rules as $rule) {
            if ($this->evaluateRule($rule, $data)) {
                $this->score          += $rule->score_impact;
                $this->triggeredRules[] = [
                    'rule'   => $rule->code,
                    'name'   => $rule->name,
                    'action' => $rule->action,
                    'impact' => $rule->score_impact,
                ];
                $rule->increment('triggered_count');
            }
        }
    }

    private function evaluateRule(FraudRule $rule, array $data): bool
    {
        $fieldValue = $data[$rule->condition_field] ?? null;

        if ($fieldValue === null) {
            return false;
        }

        return match ($rule->condition_operator) {
            'equals'       => (string) $fieldValue === (string) $rule->condition_value,
            'not_equals'   => (string) $fieldValue !== (string) $rule->condition_value,
            'contains'     => str_contains((string) $fieldValue, $rule->condition_value),
            'greater_than' => (float) $fieldValue > (float) $rule->condition_value,
            'less_than'    => (float) $fieldValue < (float) $rule->condition_value,
            'in'           => in_array($fieldValue, array_map('trim', explode(',', $rule->condition_value)), true),
            'is_true'      => (bool) $fieldValue === true,
            'is_false'     => (bool) $fieldValue === false,
            'regex'        => (bool) @preg_match($rule->condition_value, (string) $fieldValue),
            default        => false,
        };
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    private function determineStatus(string $riskLevel): string
    {
        return match ($riskLevel) {
            'critical' => 'declined',
            'high'     => 'review',
            'medium'   => 'review',
            'low'      => 'approved',
            default    => 'pending',
        };
    }

    private function generateAlerts(FraudCheck $check): void
    {
        $severity = $check->risk_level === 'critical' ? 'critical' : 'warning';

        FraudAlert::create([
            'alert_id'       => FraudAlert::generateAlertId(),
            'fraud_check_id' => $check->id,
            'severity'       => $severity,
            'type'           => 'rule_triggered',
            'title'          => "High Risk Detected — {$check->check_id}",
            'description'    => "Risk score: {$check->risk_score}/100. Flags: " . implode(', ', $check->flags ?? []),
            'status'         => 'open',
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\FraudRule;
use Illuminate\Database\Seeder;

class FraudRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            // ── Identity Rules ────────────────────────────────────
            [
                'name'               => 'Disposable Email Domain',
                'description'        => 'Flag accounts using temporary email services',
                'category'           => 'identity',
                'condition_field'    => 'email_disposable',
                'condition_operator' => 'is_true',
                'condition_value'    => 'true',
                'action'             => 'review',
                'score_impact'       => 30,
                'priority'           => 90,
            ],
            [
                'name'               => 'Invalid Email Format',
                'description'        => 'Email fails RFC validation',
                'category'           => 'identity',
                'condition_field'    => 'email_valid',
                'condition_operator' => 'is_false',
                'condition_value'    => 'false',
                'action'             => 'flag',
                'score_impact'       => 25,
                'priority'           => 95,
            ],
            [
                'name'               => 'New Email Domain (< 30 days)',
                'description'        => 'Email domain registered very recently',
                'category'           => 'identity',
                'condition_field'    => 'email_domain_age',
                'condition_operator' => 'less_than',
                'condition_value'    => '30',
                'action'             => 'review',
                'score_impact'       => 20,
                'priority'           => 70,
            ],

            // ── Network Rules ─────────────────────────────────────
            [
                'name'               => 'VPN / Tunnel Detected',
                'description'        => 'Customer is using a VPN service',
                'category'           => 'network',
                'condition_field'    => 'vpn_detected',
                'condition_operator' => 'is_true',
                'condition_value'    => 'true',
                'action'             => 'flag',
                'score_impact'       => 20,
                'priority'           => 75,
            ],
            [
                'name'               => 'Proxy Detected',
                'description'        => 'Customer is routed through a proxy',
                'category'           => 'network',
                'condition_field'    => 'proxy_detected',
                'condition_operator' => 'is_true',
                'condition_value'    => 'true',
                'action'             => 'review',
                'score_impact'       => 25,
                'priority'           => 80,
            ],
            [
                'name'               => 'Tor Network Detected',
                'description'        => 'Connection via Tor anonymization network',
                'category'           => 'network',
                'condition_field'    => 'tor_detected',
                'condition_operator' => 'is_true',
                'condition_value'    => 'true',
                'action'             => 'decline',
                'score_impact'       => 50,
                'priority'           => 100,
            ],

            // ── Transaction Rules ─────────────────────────────────
            [
                'name'               => 'High Value Transaction (>100k BDT)',
                'description'        => 'Transaction amount exceeds 1 lakh BDT',
                'category'           => 'transaction',
                'condition_field'    => 'transaction_amount',
                'condition_operator' => 'greater_than',
                'condition_value'    => '100000',
                'action'             => 'review',
                'score_impact'       => 20,
                'priority'           => 60,
            ],
            [
                'name'               => 'Very High Value Transaction (>500k BDT)',
                'description'        => 'Transaction amount exceeds 5 lakh BDT',
                'category'           => 'transaction',
                'condition_field'    => 'transaction_amount',
                'condition_operator' => 'greater_than',
                'condition_value'    => '500000',
                'action'             => 'decline',
                'score_impact'       => 40,
                'priority'           => 85,
            ],

            // ── Device Rules ──────────────────────────────────────
            [
                'name'               => 'VoIP Phone Number',
                'description'        => 'Phone number linked to VoIP service (not mobile)',
                'category'           => 'device',
                'condition_field'    => 'phone_type',
                'condition_operator' => 'equals',
                'condition_value'    => 'voip',
                'action'             => 'flag',
                'score_impact'       => 15,
                'priority'           => 65,
            ],
        ];

        foreach ($rules as $rule) {
            $rule['code'] = FraudRule::generateCode();
            FraudRule::create($rule);
        }
    }
}

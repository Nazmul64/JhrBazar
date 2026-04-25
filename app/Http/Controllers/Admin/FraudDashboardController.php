<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudCheck;
use App\Models\FraudAlert;
use App\Models\FraudRule;
use App\Models\FraudBlacklist;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FraudDashboardController extends Controller
{
    public function index(): View
    {
        // ── Overview Stats ────────────────────────────────────────────────────
        $stats = FraudCheck::getStats();

        // ── Today ─────────────────────────────────────────────────────────────
        $todayStats = [
            'checks'   => FraudCheck::whereDate('created_at', today())->count(),
            'declined' => FraudCheck::whereDate('created_at', today())->byStatus('declined')->count(),
            'review'   => FraudCheck::whereDate('created_at', today())->byStatus('review')->count(),
            'approved' => FraudCheck::whereDate('created_at', today())->byStatus('approved')->count(),
        ];

        // ── Risk Distribution ─────────────────────────────────────────────────
        $riskDistribution = FraudCheck::select('risk_level', DB::raw('COUNT(*) as total'))
            ->groupBy('risk_level')
            ->pluck('total', 'risk_level')
            ->toArray();

        // ── By Type ───────────────────────────────────────────────────────────
        $byType = FraudCheck::select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        // ── 14-Day Trend ──────────────────────────────────────────────────────
        $trend = FraudCheck::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "declined" THEN 1 ELSE 0 END) as declined'),
                DB::raw('ROUND(AVG(risk_score), 1) as avg_score')
            )
            ->where('created_at', '>=', now()->subDays(13))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // ── Recent High Risk ──────────────────────────────────────────────────
        $recentHighRisk = FraudCheck::highRisk()
            ->with('creator')
            ->latest()
            ->limit(6)
            ->get();

        // ── Open Alerts ───────────────────────────────────────────────────────
        $openAlerts = FraudAlert::open()
            ->with('fraudCheck')
            ->orderByRaw("FIELD(severity, 'critical', 'warning', 'info')")
            ->limit(5)
            ->get();

        // ── Top Triggered Rules ───────────────────────────────────────────────
        $topRules = FraudRule::orderByDesc('triggered_count')
            ->limit(5)
            ->get();

        // ── Blacklist Summary ─────────────────────────────────────────────────
        $blacklistCount = FraudBlacklist::active()->count();

        // ── Alert Stats ───────────────────────────────────────────────────────
        $alertStats = FraudAlert::getStats();

        return view('admin.fraud.dashboard', compact(
            'stats', 'todayStats', 'riskDistribution', 'byType',
            'trend', 'recentHighRisk', 'openAlerts', 'topRules',
            'blacklistCount', 'alertStats'
        ));
    }
}

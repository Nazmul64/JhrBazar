<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingAttribution;
use App\Models\Pointofsalepo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingAttributionController extends Controller
{
    /**
     * Display the Attribution & Channel Reports Dashboard
     */
    public function index(Request $request)
    {
        // Date range filtering
        $range = $request->get('range', '30_days');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($range === 'today') {
            $start = Carbon::today()->startOfDay();
            $end = Carbon::today()->endOfDay();
        } elseif ($range === '7_days') {
            $start = Carbon::now()->subDays(6)->startOfDay();
            $end = Carbon::now()->endOfDay();
        } elseif ($range === 'this_month') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfDay();
        } elseif ($range === 'last_month') {
            $start = Carbon::now()->subMonth()->startOfMonth();
            $end = Carbon::now()->subMonth()->endOfMonth();
        } elseif ($range === 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } else {
            // Default 30 days
            $range = '30_days';
            $start = Carbon::now()->subDays(29)->startOfDay();
            $end = Carbon::now()->endOfDay();
        }

        // Base Query in Date Range
        $baseQuery = MarketingAttribution::whereBetween('created_at', [$start, $end]);

        // 1. Overall Summary KPI Metrics
        $totalVisitors = (clone $baseQuery)->count();
        $totalOrders = (clone $baseQuery)->whereNotNull('order_id')->count();
        $totalRevenue = (clone $baseQuery)->whereNotNull('order_id')->sum('revenue');
        $conversionRate = $totalVisitors > 0 ? round(($totalOrders / $totalVisitors) * 100, 2) : 0;
        $aov = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        // Top Performing Channel
        $topChannelRow = (clone $baseQuery)
            ->select('traffic_source as detected_platform', DB::raw('SUM(revenue) as total_rev'))
            ->whereNotNull('order_id')
            ->groupBy('traffic_source')
            ->orderByDesc('total_rev')
            ->first();
        $topChannel = $topChannelRow ? $topChannelRow->detected_platform : 'Direct Visit';

        // 2. Timeline Chart (Daily Visitors & Revenue)
        $dates = [];
        $current = $start->copy();
        while ($current <= $end) {
            $dates[$current->format('Y-m-d')] = [
                'date' => $current->format('M d'),
                'visitors' => 0,
                'revenue' => 0,
                'orders' => 0,
            ];
            $current->addDay();
        }

        $dailyStats = (clone $baseQuery)
            ->select(
                DB::raw('DATE(created_at) as log_date'),
                DB::raw('COUNT(*) as visitors_count'),
                DB::raw('SUM(CASE WHEN order_id IS NOT NULL THEN revenue ELSE 0 END) as daily_revenue'),
                DB::raw('COUNT(order_id) as daily_orders')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        foreach ($dailyStats as $stat) {
            if (isset($dates[$stat->log_date])) {
                $dates[$stat->log_date]['visitors'] = (int)$stat->visitors_count;
                $dates[$stat->log_date]['revenue'] = (float)$stat->daily_revenue;
                $dates[$stat->log_date]['orders'] = (int)$stat->daily_orders;
            }
        }

        $chartCategories = array_column($dates, 'date');
        $chartVisitors = array_column($dates, 'visitors');
        $chartRevenue = array_column($dates, 'revenue');

        // 3. Platform Distribution (Donut Chart)
        $platformDistribution = (clone $baseQuery)
            ->select('traffic_source as detected_platform', DB::raw('COUNT(*) as visitor_count'))
            ->groupBy('traffic_source')
            ->orderByDesc('visitor_count')
            ->get();

        $donutLabels = [];
        $donutSeries = [];
        foreach ($platformDistribution as $p) {
            $donutLabels[] = $p->detected_platform ?: 'Direct / Unknown';
            $donutSeries[] = (int)$p->visitor_count;
        }

        // 4. Top Traffic Sources Table
        $trafficSources = (clone $baseQuery)
            ->select(
                'traffic_source as detected_platform',
                DB::raw("COALESCE(CONCAT(utm_source, ' / ', utm_medium), traffic_source, 'direct / (none)') as source_medium"),
                DB::raw('COUNT(*) as visitors'),
                DB::raw('COUNT(order_id) as orders'),
                DB::raw('SUM(CASE WHEN order_id IS NOT NULL THEN revenue ELSE 0 END) as revenue')
            )
            ->groupBy('traffic_source', 'utm_source', 'utm_medium')
            ->orderByDesc('revenue')
            ->orderByDesc('visitors')
            ->limit(15)
            ->get()
            ->map(function ($row) {
                $row->conversion_rate = $row->visitors > 0 ? round(($row->orders / $row->visitors) * 100, 2) : 0;
                $row->aov = $row->orders > 0 ? round($row->revenue / $row->orders, 2) : 0;
                return $row;
            });

        // 5. Top Marketing Campaigns Table
        $campaigns = (clone $baseQuery)
            ->whereNotNull('utm_campaign')
            ->where('utm_campaign', '!=', '')
            ->select(
                'utm_campaign',
                'utm_source',
                'utm_medium',
                DB::raw('COUNT(*) as visitors'),
                DB::raw('COUNT(order_id) as orders'),
                DB::raw('SUM(CASE WHEN order_id IS NOT NULL THEN revenue ELSE 0 END) as revenue')
            )
            ->groupBy('utm_campaign', 'utm_source', 'utm_medium')
            ->orderByDesc('revenue')
            ->orderByDesc('visitors')
            ->limit(15)
            ->get()
            ->map(function ($row) {
                $row->conversion_rate = $row->visitors > 0 ? round(($row->orders / $row->visitors) * 100, 2) : 0;
                $row->aov = $row->orders > 0 ? round($row->revenue / $row->orders, 2) : 0;
                return $row;
            });

        // 6. Platform Breakdown Report Table with Live Order Statuses
        $platforms = [
            'Facebook Ads',
            'Google Ads',
            'TikTok Ads',
            'Instagram Ads',
            'Facebook Organic',
            'Google Search',
            'YouTube',
            'Referral',
            'Direct Visit',
        ];

        $platformBreakdown = [];
        foreach ($platforms as $platformName) {
            $platQuery = (clone $baseQuery)->where('traffic_source', $platformName);
            $platVisitors = (clone $platQuery)->count();
            
            // Get all attributions for this platform with orders
            $orderAttributions = (clone $platQuery)
                ->whereNotNull('order_id')
                ->with('order')
                ->get();

            $platOrders = $orderAttributions->count();
            $platRevenue = $orderAttributions->sum('revenue');
            $platConv = $platVisitors > 0 ? round(($platOrders / $platVisitors) * 100, 2) : 0;
            $platAov = $platOrders > 0 ? round($platRevenue / $platOrders, 2) : 0;

            // Compute actual order statuses
            $deliveredCount = 0;
            $pendingCount = 0;
            $cancelledCount = 0;

            foreach ($orderAttributions as $attr) {
                $status = strtolower($attr->order->status ?? $attr->order_status ?? 'pending');
                if (in_array($status, ['complete', 'delivered', 'completed', 'success'])) {
                    $deliveredCount++;
                } elseif (in_array($status, ['cancelled', 'cancel', 'failed', 'returned'])) {
                    $cancelledCount++;
                } else {
                    $pendingCount++;
                }
            }

            // Only add if there are actual visitors or orders
            if ($platVisitors > 0 || $platOrders > 0) {
                $platformBreakdown[] = (object)[
                    'platform' => $platformName,
                    'visitors' => $platVisitors,
                    'orders' => $platOrders,
                    'conversion_rate' => $platConv,
                    'revenue' => $platRevenue,
                    'aov' => $platAov,
                    'delivered' => $deliveredCount,
                    'pending' => $pendingCount,
                    'cancelled' => $cancelledCount,
                ];
            }
        }

        // Also check any extra detected platform
        $otherPlatforms = (clone $baseQuery)
            ->whereNotIn('traffic_source', $platforms)
            ->whereNotNull('traffic_source')
            ->select('traffic_source as detected_platform')
            ->distinct()
            ->pluck('detected_platform');

        foreach ($otherPlatforms as $otherPlat) {
            $platQuery = (clone $baseQuery)->where('traffic_source', $otherPlat);
            $platVisitors = (clone $platQuery)->count();
            $orderAttributions = (clone $platQuery)->whereNotNull('order_id')->with('order')->get();
            $platOrders = $orderAttributions->count();
            $platRevenue = $orderAttributions->sum('revenue');
            $platConv = $platVisitors > 0 ? round(($platOrders / $platVisitors) * 100, 2) : 0;
            $platAov = $platOrders > 0 ? round($platRevenue / $platOrders, 2) : 0;

            $deliveredCount = 0;
            $pendingCount = 0;
            $cancelledCount = 0;
            foreach ($orderAttributions as $attr) {
                $status = strtolower($attr->order->status ?? $attr->order_status ?? 'pending');
                if (in_array($status, ['complete', 'delivered', 'completed', 'success'])) {
                    $deliveredCount++;
                } elseif (in_array($status, ['cancelled', 'cancel', 'failed', 'returned'])) {
                    $cancelledCount++;
                } else {
                    $pendingCount++;
                }
            }

            $platformBreakdown[] = (object)[
                'platform' => $otherPlat,
                'visitors' => $platVisitors,
                'orders' => $platOrders,
                'conversion_rate' => $platConv,
                'revenue' => $platRevenue,
                'aov' => $platAov,
                'delivered' => $deliveredCount,
                'pending' => $pendingCount,
                'cancelled' => $cancelledCount,
            ];
        }

        return view('admin.tracking.attribution_report', compact(
            'range',
            'start',
            'end',
            'totalVisitors',
            'totalOrders',
            'totalRevenue',
            'conversionRate',
            'aov',
            'topChannel',
            'chartCategories',
            'chartVisitors',
            'chartRevenue',
            'donutLabels',
            'donutSeries',
            'trafficSources',
            'campaigns',
            'platformBreakdown'
        ));
    }

    /**
     * API: Track visitor session & attribution payload from Frontend React
     */
    public function trackVisit(Request $request)
    {
        $sessionId = $request->input('session_id') ?: session()->getId();
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        $utmSource = $request->input('utm_source');
        $utmMedium = $request->input('utm_medium');
        $utmCampaign = $request->input('utm_campaign');
        $utmTerm = $request->input('utm_term');
        $utmContent = $request->input('utm_content');
        $clickId = $request->input('click_id');
        $clickIdType = $request->input('click_id_type');
        $referrerUrl = $request->input('referrer_url');
        $landingPage = $request->input('landing_page') ?: $request->input('landing_page_url') ?: $request->fullUrl();

        // Platform detection logic
        $detectedPlatform = $request->input('detected_platform') ?: $request->input('traffic_source');
        if (!$detectedPlatform) {
            $detectedPlatform = $this->detectPlatform($utmSource, $utmMedium, $clickIdType, $referrerUrl);
        }

        // Check if there is an existing session in the last 24 hours to avoid duplicate inflated hits
        $existing = MarketingAttribution::where('session_id', $sessionId)
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->first();

        if ($existing) {
            if ($utmSource && !$existing->utm_source) {
                $existing->update([
                    'utm_source' => $utmSource,
                    'utm_medium' => $utmMedium,
                    'utm_campaign' => $utmCampaign,
                    'utm_term' => $utmTerm,
                    'utm_content' => $utmContent,
                    'click_id' => $clickId,
                    'traffic_source' => $detectedPlatform,
                ]);
            }
            return response()->json(['status' => 'success', 'message' => 'Session refreshed', 'id' => $existing->id]);
        }

        $attribution = MarketingAttribution::create([
            'session_id' => $sessionId,
            'ip_address' => $ip,
            'utm_source' => $utmSource,
            'utm_medium' => $utmMedium,
            'utm_campaign' => $utmCampaign,
            'utm_term' => $utmTerm,
            'utm_content' => $utmContent,
            'click_id' => $clickId,
            'referrer_url' => $referrerUrl,
            'traffic_source' => $detectedPlatform,
            'landing_page_url' => $landingPage,
            'order_status' => 'visiting',
        ]);

        // Also trigger Customer Detector visit log if applicable
        try {
            if ($request->filled('page') || $request->filled('landing_page')) {
                app(\App\Http\Controllers\Admin\CustomerDetectorController::class)->trackVisit($request);
            }
        } catch (\Throwable $e) {
            // silent fail
        }

        return response()->json(['status' => 'success', 'id' => $attribution->id]);
    }

    /**
     * Helper to detect marketing channel/platform
     */
    private function detectPlatform($utmSource, $utmMedium, $clickIdType, $referrerUrl)
    {
        $src = strtolower($utmSource ?? '');
        $med = strtolower($utmMedium ?? '');
        $ref = strtolower($referrerUrl ?? '');
        $clk = strtolower($clickIdType ?? '');

        // 1. Click IDs
        if ($clk === 'fbclid' || (str_contains($ref, 'facebook.com') && in_array($med, ['cpc', 'paid', 'ads']))) {
            return 'Facebook Ads';
        }
        if ($clk === 'gclid' || (str_contains($src, 'google') && in_array($med, ['cpc', 'paid', 'ads']))) {
            return 'Google Ads';
        }
        if ($clk === 'ttclid' || (str_contains($src, 'tiktok') && in_array($med, ['cpc', 'paid', 'ads']))) {
            return 'TikTok Ads';
        }

        // 2. Source / Medium matching
        if (str_contains($src, 'facebook') || str_contains($src, 'fb')) {
            return in_array($med, ['cpc', 'paid', 'ads']) ? 'Facebook Ads' : 'Facebook Organic';
        }
        if (str_contains($src, 'instagram') || str_contains($src, 'ig')) {
            return 'Instagram Ads';
        }
        if (str_contains($src, 'tiktok')) {
            return 'TikTok Ads';
        }
        if (str_contains($src, 'google')) {
            return in_array($med, ['cpc', 'paid', 'ads']) ? 'Google Ads' : 'Google Search';
        }
        if (str_contains($src, 'youtube') || str_contains($ref, 'youtube.com') || str_contains($ref, 'youtu.be')) {
            return 'YouTube';
        }

        // 3. Referrer parsing
        if (str_contains($ref, 'facebook.com') || str_contains($ref, 'fb.me') || str_contains($ref, 'm.facebook.com')) {
            return 'Facebook Organic';
        }
        if (str_contains($ref, 'instagram.com')) {
            return 'Instagram Ads';
        }
        if (str_contains($ref, 'tiktok.com')) {
            return 'TikTok Ads';
        }
        if (str_contains($ref, 'google.com') || str_contains($ref, 'google.com.bd')) {
            return 'Google Search';
        }

        if (!empty($ref) && !str_contains($ref, request()->getHost())) {
            return 'Referral';
        }

        return 'Direct Visit';
    }

    /**
     * Export Attribution Report as CSV
     */
    public function exportCsv(Request $request)
    {
        $range = $request->get('range', '30_days');
        $start = Carbon::now()->subDays(30)->startOfDay();
        $end = Carbon::now()->endOfDay();

        $rows = MarketingAttribution::whereBetween('created_at', [$start, $end])
            ->orderByDesc('id')
            ->get();

        $filename = "attribution_report_" . date('Y_m_d_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Date', 'Session ID', 'Platform', 'UTM Source', 'UTM Medium', 'UTM Campaign', 'Order ID', 'Revenue (BDT)', 'Status', 'Landing Page'];

        $callback = function () use ($rows, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($rows as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->created_at->format('Y-m-d H:i:s'),
                    $row->session_id,
                    $row->traffic_source,
                    $row->utm_source,
                    $row->utm_medium,
                    $row->utm_campaign,
                    $row->order_id ?: 'None',
                    $row->revenue ?? 0,
                    $row->order_status,
                    $row->landing_page_url,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

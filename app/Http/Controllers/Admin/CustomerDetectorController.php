<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerVisit;
use App\Models\Pointofsalepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerDetectorController extends Controller
{
    /**
     * Render the Customer Detector Dashboard
     */
    public function index(Request $request)
    {
        // 1. Calculate Stats
        $todayVisits = CustomerVisit::whereDate('visited_at', today())->count();
        $last7DaysVisits = CustomerVisit::where('visited_at', '>=', now()->subDays(7))->count();
        $last30DaysVisits = CustomerVisit::where('visited_at', '>=', now()->subDays(30))->count();

        // 2. Fetch Top Visited Pages
        $topPages = CustomerVisit::select('page_visited', DB::raw('count(*) as total'))
            ->groupBy('page_visited')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // 3. Build Query for Visitor List
        $query = CustomerVisit::query();

        // Advanced Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('phone_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('visited_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        if ($request->filled('day_of_week')) {
            // MySQL DAYOFWEEK (1 = Sunday, 2 = Monday, ...)
            $query->whereRaw('DAYOFWEEK(visited_at) = ?', [$request->day_of_week]);
        }

        if ($request->filled('period')) {
            $period = $request->period;
            if ($period === 'today') {
                $query->whereDate('visited_at', today());
            } elseif ($period === 'yesterday') {
                $query->whereDate('visited_at', yesterday());
            } elseif ($period === '7days') {
                $query->where('visited_at', '>=', now()->subDays(7));
            } elseif ($period === '30days') {
                $query->where('visited_at', '>=', now()->subDays(30));
            }
        }

        // Fetch paginated visits
        $visits = $query->orderBy('visited_at', 'desc')->paginate(15);

        // Attach previous orders history and total visits for each unique visitor card
        foreach ($visits as $visit) {
            $visit->total_user_visits = CustomerVisit::where('phone_number', $visit->phone_number)->count();
            
            // Get orders placed by this customer phone number
            $visit->previous_orders = Pointofsalepo::where('phone', $visit->phone_number)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('admin.customer_detector.index', compact(
            'todayVisits',
            'last7DaysVisits',
            'last30DaysVisits',
            'topPages',
            'visits'
        ));
    }

    /**
     * Poll endpoint for real-time toaster notifications in admin hub
     */
    public function poll()
    {
        // Get all unread visits
        $unreadVisits = CustomerVisit::where('is_read', false)
            ->orderBy('visited_at', 'asc')
            ->get();

        // Mark them as read immediately so they aren't toasted again
        if ($unreadVisits->count() > 0) {
            CustomerVisit::whereIn('id', $unreadVisits->pluck('id'))->update(['is_read' => true]);
        }

        return response()->json($unreadVisits);
    }

    /**
     * API Endpoint to track page visits from React SPA frontend
     */
    public function trackVisit(Request $request)
    {
        $request->validate([
            'page' => 'required|string',
        ]);

        $page = $request->page;
        $phone = null;
        $name = null;

        // Resolve identity
        if (auth('sanctum')->check()) {
            $user = auth('sanctum')->user();
            $phone = $user->phone;
            $name = $user->name;
        }

        if (!$phone) {
            $phone = $request->cookie('customer_tracker_phone');
            $name = $request->cookie('customer_tracker_name') ?? 'Guest';
        }

        if (!$phone) {
            $ip = $request->ip();
            $lastOrder = Pointofsalepo::where('ip_address', $ip)
                ->whereNotNull('phone')
                ->latest()
                ->first();
            if ($lastOrder) {
                $phone = $lastOrder->phone;
                $name = 'Guest (IP Match)';
                if ($lastOrder->customer && $lastOrder->customer->user) {
                    $name = $lastOrder->customer->user->name;
                }
            }
        }

        if ($phone && $phone !== 'N/A' && !empty($phone)) {
            // Keep user logged via cookies
            if (!$request->cookie('customer_tracker_phone')) {
                \Illuminate\Support\Facades\Cookie::queue('customer_tracker_phone', $phone, 525600); // 1 year
                \Illuminate\Support\Facades\Cookie::queue('customer_tracker_name', $name ?? 'Guest', 525600);
            }

            // Throttling: Only log if no identical visit in the last 1 minute
            $recentVisit = CustomerVisit::where('phone_number', $phone)
                ->where('page_visited', $page)
                ->where('visited_at', '>=', now()->subMinute())
                ->first();

            if (!$recentVisit) {
                $hasPreviousVisits = CustomerVisit::where('phone_number', $phone)->exists();
                
                CustomerVisit::create([
                    'customer_name' => $name,
                    'phone_number'  => $phone,
                    'ip_address'    => $request->ip(),
                    'page_visited'  => $page,
                    'user_agent'    => $request->userAgent(),
                    'visited_at'    => now(),
                    'is_read'       => $hasPreviousVisits ? false : true,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Bulk delete visitor logs
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        if ($ids && is_array($ids)) {
            CustomerVisit::whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', 'Selected visitor logs deleted successfully.');
        }
        return redirect()->back()->with('error', 'Please select at least one record to delete.');
    }
}

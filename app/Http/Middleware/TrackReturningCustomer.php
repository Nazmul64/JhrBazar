<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\CustomerVisit;
use App\Models\Pointofsalepo;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class TrackReturningCustomer
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only run for frontend normal GET routes or API GET/POST requests that load pages/data
        // Skip assets, livewire, telescope, admin requests
        if ($request->is('admin*') || $request->is('assets*') || $request->is('_debugbar*') || $request->is('vendor*')) {
            return $next($request);
        }

        try {
            $phone = null;
            $name = null;

            // 1. Check if customer is authenticated
            if (auth()->check()) {
                $user = auth()->user();
                if ($user->role === 'customer' || $user->role === 'user') {
                    $phone = $user->phone;
                    $name = $user->name;
                }
            }

            // 2. Check for the persistent tracking cookie
            if (!$phone) {
                $phone = $request->cookie('customer_tracker_phone');
                $name = $request->cookie('customer_tracker_name') ?? 'Guest';
            }

            // 3. Fallback: Search prior completed/draft orders by current IP address
            if (!$phone) {
                $ip = $request->ip();
                if ($ip && $ip !== '127.0.0.1' && $ip !== '::1') {
                    $cachedPhone = \Illuminate\Support\Facades\Cache::remember('ip_phone_' . md5($ip), 300, function() use ($ip) {
                        $lastOrder = Pointofsalepo::where('ip_address', $ip)
                            ->whereNotNull('phone')
                            ->latest()
                            ->first();
                        return $lastOrder ? $lastOrder->phone : 'NONE';
                    });

                    if ($cachedPhone && $cachedPhone !== 'NONE') {
                        $phone = $cachedPhone;
                        $name = 'Guest (IP Match)';
                        
                        // Attempt to fetch user name dynamically if order customer exists
                        $lastOrder = Pointofsalepo::where('ip_address', $ip)
                            ->where('phone', $phone)
                            ->latest()
                            ->first();
                        if ($lastOrder && $lastOrder->customer && $lastOrder->customer->user) {
                            $name = $lastOrder->customer->user->name;
                        }
                    }
                }
            }

            // If we identified a returning customer phone number, log the visit!
            if ($phone && $phone !== 'N/A' && !empty($phone)) {
                // Keep user logged via cookies for future requests
                if (!$request->cookie('customer_tracker_phone')) {
                    Cookie::queue('customer_tracker_phone', $phone, 525600); // 1 year
                    Cookie::queue('customer_tracker_name', $name ?? 'Guest', 525600);
                }

                // Determine page label based on request path
                $path = $request->path();
                $pageLabel = 'Home Page';

                if ($path === '/' || $path === 'api/home-data') {
                    $pageLabel = 'Home Page';
                } elseif (str_contains($path, 'checkout') || str_contains($path, 'payment-gateways') || str_contains($path, 'shipping-charges')) {
                    $pageLabel = 'Checkout Page';
                } elseif (str_contains($path, 'cart')) {
                    $pageLabel = 'Cart Page';
                } elseif (str_contains($path, 'product/')) {
                    $slug = basename($path);
                    $pageLabel = 'Product: ' . ucfirst(str_replace('-', ' ', $slug));
                } elseif (str_contains($path, 'category/')) {
                    $pageLabel = 'Category Page';
                } elseif (str_contains($path, 'wishlist')) {
                    $pageLabel = 'Wishlist Page';
                } else {
                    $pageLabel = $path === '/' ? 'Home Page' : '/' . $path;
                }

                // Throttling: Only log if no identical visit logged in the last 1 minute
                $recentVisit = CustomerVisit::where('phone_number', $phone)
                    ->where('page_visited', $pageLabel)
                    ->where('visited_at', '>=', now()->subMinute())
                    ->first();

                if (!$recentVisit) {
                    CustomerVisit::create([
                        'customer_name' => $name,
                        'phone_number'  => $phone,
                        'ip_address'    => $request->ip(),
                        'page_visited'  => $pageLabel,
                        'user_agent'    => $request->userAgent(),
                        'visited_at'    => now(),
                        'is_read'       => false,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Silently log the database tracking error to prevent 500 internal server error crashes
            \Illuminate\Support\Facades\Log::error('TrackReturningCustomer middleware error: ' . $e->getMessage());
        }

        return $next($request);
    }
}

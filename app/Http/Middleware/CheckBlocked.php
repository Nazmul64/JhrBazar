<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBlocked
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // 1. IP Blacklist Check (Moved to checkout logic)
        /*
        if (\App\Models\FraudBlacklist::where('type', 'ip')->where('value', $ip)->where('is_active', true)->exists()) {
            abort(403, 'Your IP address (' . $ip . ') has been blocked by the administrator.');
        }
        */

        try {
            $user = null;
            if (auth('sanctum')->check()) {
                $user = auth('sanctum')->user();
            } elseif (Auth::check()) {
                $user = Auth::user();
            }

            if ($user) {
                // Update last_ip
                if ($user->last_ip !== $ip) {
                    $user->update(['last_ip' => $ip]);
                }

                if ($user->is_blocked) {
                    if ($request->expectsJson() || $request->is('api/*') || $request->is('v1/*') || $request->routeIs('api.*')) {
                        return response()->json([
                            'success' => false,
                            'blocked' => true,
                            'message' => 'Your account has been blocked. Please contact support.'
                        ], 403);
                    }

                    Auth::logout();

                    if ($request->hasSession()) {
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                    }

                    return redirect()->route('login')->with('error', 'Your account has been blocked. Please contact support.');
                }
            }
        } catch (\Throwable $e) {
            // Silently log block checker database error to prevent site-wide 500 internal server error crashes
            \Illuminate\Support\Facades\Log::error('CheckBlocked middleware error: ' . $e->getMessage());
        }

        return $next($request);
    }
}

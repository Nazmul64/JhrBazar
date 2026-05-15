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

        if (Auth::check()) {
            $user = Auth::user();

            // Update last_ip
            if ($user->last_ip !== $ip) {
                $user->update(['last_ip' => $ip]);
            }

            if ($user->is_blocked) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Your account has been blocked. Please contact support.');
            }
        }

        return $next($request);
    }
}

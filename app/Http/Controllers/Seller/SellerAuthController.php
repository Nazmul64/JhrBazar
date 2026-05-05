<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerAuthController extends Controller
{
    /**
     * Show seller login form
     */
    public function showLogin()
    {
        return view('auth.seller_login');
    }

    /**
     * Handle seller login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Block unapproved or suspended sellers
            if ($user->status !== 'active') {
                Auth::logout();
                $message = $user->status === 'pending' 
                    ? 'Your account is pending. Please wait for admin approval.' 
                    : 'Your account is inactive. Please contact admin for activation.';
                return back()->withErrors(['email' => $message]);
            }

            // Allow sellers (and admins for convenience)
            if (in_array($user->role, ['seller', 'admin'])) {
                $request->session()->regenerate();
                return redirect()->intended('seller/dashboard');
            }

            Auth::logout();
            return back()->withErrors(['email' => 'Access denied. Only sellers can login here.']);
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('seller.login');
    }
}

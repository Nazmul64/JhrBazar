<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerAuthController extends Controller
{
    /**
     * Show manager login form
     */
    public function showLogin()
    {
        return view('auth.manager_login');
    }

    /**
     * Handle manager login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Allow managers (and admins/employees for convenience if needed, but primarily managers)
            if (in_array($user->role, ['manager', 'admin'])) {
                $request->session()->regenerate();
                return redirect()->intended('manager/dashboard');
            }

            Auth::logout();
            return back()->withErrors(['email' => 'Access denied. Only managers can login here.']);
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
        return redirect()->route('manager.login');
    }
}

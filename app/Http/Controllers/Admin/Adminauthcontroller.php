<?php
// app/Http/Controllers/Admin/Adminauthcontroller.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ THIS WAS MISSING

class Adminauthcontroller extends Controller
{
    public function adminlogin()
    {
        // If already logged in as admin, skip login page
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function admin_login_submit(Request $request)
    {
        // Validate inputs
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            if (Auth::user()->role === 'admin') {
                $request->session()->regenerate(); // ✅ prevent session fixation
                return redirect()->route('admin.dashboard');
            }

            // Logged in but not admin — kick them out
            Auth::logout();
            return back()->withErrors(['email' => 'You are not an admin.']);
        }

        return back()->withErrors(['email' => 'Invalid email or password.']);
    }

    public function admin_logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}

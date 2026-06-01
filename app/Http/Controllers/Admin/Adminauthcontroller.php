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
        session(['forgot_password_role' => 'admin']);

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
            $user = Auth::user();
            $role = $user->role;

            if (in_array($role, ['admin', 'manager', 'employee'])) {
                $request->session()->regenerate();
                
                if ($role === 'admin') return redirect()->route('admin.dashboard');
                if ($role === 'manager') return redirect()->route('manager.dashboard');
                if ($role === 'employee') return redirect()->route('employee.dashboard');
            }

            // Logged in but not authorized for this login page
            Auth::logout();
            return back()->withErrors(['email' => 'You do not have access to this area.']);
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

    /**
     * Show the forgot password link request form.
     */
    public function showLinkRequestForm()
    {
        return view('admin.auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user || !in_array($user->role, ['admin', 'manager', 'employee'])) {
            return back()->withErrors(['email' => 'We could not find an administrator with that email address.']);
        }

        $response = \Illuminate\Support\Facades\Password::broker()->sendResetLink(
            $request->only('email')
        );

        return $response == \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with('status', trans($response))
            : back()->withErrors(['email' => trans($response)]);
    }
}

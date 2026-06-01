<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeAuthController extends Controller
{
    /**
     * Show employee login form
     */
    public function showLogin()
    {
        session(['forgot_password_role' => 'employee']);
        return view('auth.employee_login');
    }

    /**
     * Handle employee login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if user has employee/manager/admin role
            if (in_array($user->role, ['employee', 'manager', 'admin'])) {
                $request->session()->regenerate();
                return redirect()->intended('employee/dashboard');
            }

            Auth::logout();
            return back()->withErrors(['email' => 'Access denied. Only staff members can login here.']);
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
        return redirect()->route('employee.login');
    }

    /**
     * Show the forgot password link request form.
     */
    public function showLinkRequestForm()
    {
        return view('auth.employee_passwords.email');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user || !in_array($user->role, ['employee', 'admin', 'manager'])) {
            return back()->withErrors(['email' => 'We could not find an employee with that email address.']);
        }

        $response = \Illuminate\Support\Facades\Password::broker()->sendResetLink(
            $request->only('email')
        );

        return $response == \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with('status', trans($response))
            : back()->withErrors(['email' => trans($response)]);
    }
}

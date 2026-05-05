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
}

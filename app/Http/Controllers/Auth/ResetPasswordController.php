<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Models\User;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Display the password reset view for the given token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();
        $role = $user ? $user->role : 'customer';

        if (in_array($role, ['admin', 'super_admin'])) {
            return view('admin.auth.passwords.reset')->with(['token' => $token, 'email' => $email]);
        } elseif ($role === 'employee') {
            return view('auth.employee_passwords.reset')->with(['token' => $token, 'email' => $email]);
        } elseif ($role === 'manager') {
            return view('auth.manager_passwords.reset')->with(['token' => $token, 'email' => $email]);
        } elseif (in_array($role, ['seller', 'reseller'])) {
            return view('auth.seller_passwords.reset')->with(['token' => $token, 'email' => $email]);
        }

        return view('auth.passwords.reset')->with(['token' => $token, 'email' => $email]);
    }

    /**
     * Where to redirect users after resetting their password.
     *
     * @return string
     */
    public function redirectTo()
    {
        $role = auth()->user()->role;

        switch ($role) {
            case 'admin':
            case 'super_admin':
                return route('admin.dashboard');
            case 'employee':
                return route('employee.dashboard');
            case 'manager':
                return route('manager.dashboard');
            case 'seller':
            case 'reseller':
                return route('seller.dashboard');
            case 'customer':
            case 'user':
                return route('customer.dashboard');
            default:
                return '/';
        }
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        $role = session('forgot_password_role');

        if ($role === 'admin') {
            return redirect()->route('admin.password.request');
        } elseif ($role === 'employee') {
            return redirect()->route('employee.password.request');
        } elseif ($role === 'manager') {
            return redirect()->route('manager.password.request');
        } elseif ($role === 'seller') {
            return redirect()->route('seller.password.request');
        }

        return view('auth.passwords.email');
    }
}

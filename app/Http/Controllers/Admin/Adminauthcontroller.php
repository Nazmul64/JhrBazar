<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Adminauthcontroller extends Controller
{
    public function adminlogin()
    {
        return view('admin.auth.login');
    }
}

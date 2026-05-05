<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Admincontroller extends Controller
{
    public function dashboard()
    {
        if (auth()->user()->role === 'employee') {
            return redirect()->route('employee.dashboard');
        }
        if (auth()->user()->role === 'manager') {
            return redirect()->route('manager.dashboard');
        }
        return view('admin.index');
    }
}

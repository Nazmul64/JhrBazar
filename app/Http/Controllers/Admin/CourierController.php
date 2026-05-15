<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PathaoCourier;
use App\Models\SteadfastCourier;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function index()
    {
        $pathao = PathaoCourier::first();
        $steadfast = SteadfastCourier::first();
        
        return view('admin.courier.index', compact('pathao', 'steadfast'));
    }
}

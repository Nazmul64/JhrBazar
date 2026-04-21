<?php

use App\Http\Controllers\Admin\Admincontroller;
use Illuminate\Support\Facades\Route;

 Route::get('admin/dashboard',[Admincontroller::class, 'dashboard'])->name('admin.dashboard');



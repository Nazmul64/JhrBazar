<?php

use App\Http\Controllers\Admin\Adminauthcontroller;
use App\Http\Controllers\Admin\Admincontroller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
Auth::routes();



// Admin  Login Routes Start
Route::get('admin/login', [Adminauthcontroller::class, 'adminlogin'])->name('admin.login');



Route::get('admin/dashboard',[Admincontroller::class, 'dashboard'])->name('admin.dashboard');



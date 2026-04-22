<?php

use App\Http\Controllers\Admin\Adminauthcontroller;
use App\Http\Controllers\Admin\Admincontroller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
Auth::routes();



Route::get('admin/login',  [Adminauthcontroller::class, 'adminlogin'])->name('admin.login');
Route::post('admin/login', [Adminauthcontroller::class, 'admin_login_submit'])->name('admin.login.submit');
Route::post('admin/logout',[Adminauthcontroller::class, 'admin_logout'])->name('admin.logout');

// ── Admin Protected Routes ──────────────────────────────
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/dashboard', [Admincontroller::class, 'dashboard'])->name('admin.dashboard');
});




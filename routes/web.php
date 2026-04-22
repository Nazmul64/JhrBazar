<?php

use App\Http\Controllers\Admin\Adminauthcontroller;
use App\Http\Controllers\Admin\Admincontroller;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
Auth::routes();



Route::get('admin/login',  [Adminauthcontroller::class, 'adminlogin'])->name('admin.login');
Route::post('admin/login', [Adminauthcontroller::class, 'admin_login_submit'])->name('admin.login.submit');
Route::post('admin/logout',[Adminauthcontroller::class, 'admin_logout'])->name('admin.logout');

// ── Admin Protected Routes ──────────────────────────────
// routes/web.php — inside admin middleware group

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/dashboard', [Admincontroller::class, 'dashboard'])->name('admin.dashboard');
    // ── Customers ───────────────────────────────────────
    Route::resource('admincustomers', CustomerController::class) ->names('admin.customers');
    // ── Reset Password (separate route) ────────────────
    Route::post('admin/customers/{id}/reset-password',[CustomerController::class, 'resetPassword'])->name('admin.customers.reset-password');

    Route::resource('permission', PermissionController::class) ->names('admin.permissions');
    // routes/web.php — middleware group এর ভেতরে
    Route::get('admin/roles',                        [RoleController::class, 'index'])         ->name('admin.role.index');
    Route::post('admin/roles',                       [RoleController::class, 'store'])         ->name('admin.role.store');
    Route::post('admin/roles/{id}/update-name',      [RoleController::class, 'updateName'])    ->name('admin.role.updateName');
    Route::post('admin/roles/{id}/sync-permissions', [RoleController::class, 'syncPermissions'])->name('admin.role.syncPermissions');
    Route::delete('admin/roles/{id}',                [RoleController::class, 'destroy'])       ->name('admin.role.destroy');
    Route::get('admin/roles/{id}/permissions',       [RoleController::class, 'getPermissions'])->name('admin.role.permissions');
});




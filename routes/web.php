<?php

use App\Http\Controllers\Admin\Adminauthcontroller;
use App\Http\Controllers\Admin\Admincontroller;
use App\Http\Controllers\Admin\AipromptController;
use App\Http\Controllers\Admin\AlltaxesController;
use App\Http\Controllers\Admin\BusinessSettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\CurrencieController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\GenaralSettingController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductBrandController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\ThemecolorssettingController;
use App\Http\Controllers\Admin\VerificatiootpsettingsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('admin/login',  [Adminauthcontroller::class, 'adminlogin'])->name('admin.login');
Route::post('admin/login', [Adminauthcontroller::class, 'admin_login_submit'])->name('admin.login.submit');
Route::post('admin/logout',[Adminauthcontroller::class, 'admin_logout'])->name('admin.logout');

// ── Admin Protected Routes ──────────────────────────────
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/dashboard', [Admincontroller::class, 'dashboard'])->name('admin.dashboard');
    // ── Customers ───────────────────────────────────────
    Route::resource('admincustomers', CustomerController::class)->names('admin.customers');
    Route::post('admin/customers/{id}/reset-password', [CustomerController::class, 'resetPassword'])->name('admin.customers.reset-password');
    // ── Permissions ─────────────────────────────────────
    Route::resource('permission', PermissionController::class)->names('admin.permissions');
    // ── Roles ────────────────────────────────────────────
    Route::get('admin/roles',                        [RoleController::class, 'index'])          ->name('admin.role.index');
    Route::post('admin/roles',                       [RoleController::class, 'store'])          ->name('admin.role.store');
    Route::post('admin/roles/{id}/update-name',      [RoleController::class, 'updateName'])     ->name('admin.role.updateName');
    Route::post('admin/roles/{id}/sync-permissions', [RoleController::class, 'syncPermissions'])->name('admin.role.syncPermissions');
    Route::delete('admin/roles/{id}',                [RoleController::class, 'destroy'])        ->name('admin.role.destroy');
    Route::get('admin/roles/{id}/permissions',       [RoleController::class, 'getPermissions']) ->name('admin.role.permissions');

    // ── Employees ────────────────────────────────────────
    Route::resource('admin/employee', EmployeeController::class)->names('admin.employees');
    Route::get('admin/employee/{employee}/permission', [EmployeeController::class, 'permission'])->name('admin.employees.permission');
    Route::post('admin/employee/{employee}/permission', [EmployeeController::class, 'updatePermission'])->name('admin.employees.updatePermission');
    Route::post('admin/employee/{employee}/toggle-status',[EmployeeController::class, 'toggleStatus'])->name('admin.employees.toggleStatus');
    Route::post('admin/employee/{employee}/reset-password',[EmployeeController::class, 'resetPassword'])->name('admin.employees.resetPassword');
   // ── Suppliers ────────────────────────────────────────
   Route::resource('supplier', SupplierController::class)->names('admin.supplier');
   Route::post('supplier/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('admin.supplier.toggleStatus');
   Route::post('supplier/{supplier}/pay', [SupplierController::class, 'pay'])->name('admin.supplier.pay');

   // ── Category ────────────────────────────────────────
   Route::resource('category', CategoryController::class)->names('admin.categories');
   Route::post('category/{category}/toggle', [CategoryController::class, 'toggleStatus'])->name('admin.categories.toggle');
   // ── subCategory ────────────────────────────────────────
   Route::resource('subcategory', SubCategoryController::class)->names('admin.subcategory');
   Route::post('subcategory/{subcategory}/toggle', [SubCategoryController::class, 'toggleStatus'])->name('admin.subcategory.toggle');
   // ── Productbrand ────────────────────────────────────────
  Route::resource('productbrand', ProductBrandController::class)->names('admin.productbrands')->except(['create', 'show']);
  Route::post('productbrand/{productbrand}/toggle', [ProductBrandController::class, 'toggleStatus'])->name('admin.productbrands.toggle');
    // ── Color ────────────────────────────────────────
  Route::resource('color', ColorController::class)->names('admin.colors')->except(['show']);
Route::post('color/{color}/toggle', [ColorController::class, 'toggleStatus'])->name('admin.colors.toggle');
// ── Product Size ────────────────────────────────────────
Route::resource('size', SizeController::class)->names('admin.sizes')->except(['show', 'create']);
Route::post('size/{size}/toggle', [SizeController::class, 'toggleStatus'])->name('admin.sizes.toggle');
// ── Product Unit List ────────────────────────────────────────
Route::resource('unit', UnitController::class)->names('admin.units')->except(['show', 'create']); Route::post('unit/{unit}/toggle', [UnitController::class, 'toggleStatus'])->name('admin.units.toggle');
// ──  generalsetting List ────────────────────────────────────────
Route::resource('generalsetting', GeneralSettingController::class)->names('admin.generalsettings')->except(['show', 'create']);
Route::post('generalsetting/{generalsetting}/toggle', [GeneralSettingController::class, 'toggleStatus'])->name('admin.generalsettings.toggle');
// routes/web.php — Business Settings
Route::resource('businesssettings', BusinessSettingController::class)->names('admin.businesssettings') ->except(['show', 'create', 'edit']);
Route::post('businesssettings/{businesssettings}/toggle', [BusinessSettingController::class, 'toggleStatus']) ->name('admin.businesssettings.toggle');
// routes/web.php — Verification OTP Settings
Route::resource('verificationotpsettings', VerificatiootpsettingsController::class) ->names('admin.verificationotpsettings')->except(['show', 'create', 'edit', 'update', 'destroy']);
Route::post('verificationotpsettings/{verificationotpsettings}/toggle', [VerificatiootpsettingsController::class, 'toggleStatus'])->name('admin.verificationotpsettings.toggle');

// ── aiprompt ────────────────────────────────────────
Route::resource('aiprompt', AipromptController::class)->names('admin.aiprompt')->except(['show', 'create', 'edit', 'update', 'destroy']);
Route::post('aiprompt/update-product', [AipromptController::class, 'updateProduct'])->name('admin.aiprompt.update-product');
Route::post('aiprompt/update-page', [AipromptController::class, 'updatePage'])->name('admin.aiprompt.update-page');
Route::post('aiprompt/update-blog', [AipromptController::class, 'updateBlog'])->name('admin.aiprompt.update-blog');

// ── Currencies ────────────────────────────────────────
Route::resource('currency', CurrencieController::class)->names('admin.currencies');
Route::post('currency/{currency}/toggle', [CurrencieController::class, 'toggleStatus'])->name('admin.currencies.toggle');

// ── Alltaxes ────────────────────────────────────────
Route::resource('alltaxes',AlltaxesController::class)->names('admin.alltaxes');
Route::post('alltaxes/{alltaxes}/toggle', [AlltaxesController::class, 'toggleStatus'])->name('admin.alltaxes.toggle');
// Theme Colors Settings
Route::resource('themecolorssettings', ThemecolorssettingController::class)->names('admin.themecolorssettings');
Route::post('themecolorssettings/{Themecolorssetting}/toggle', [ThemecolorssettingController::class, 'toggleStatus'])->name('admin.themecolorssettings.toggle');
// AJAX palette generator
Route::post('themecolorssettings/generate-palette', [ThemecolorssettingController::class, 'generatePalette']) ->name('admin.themecolorssettings.generate-palette');
});


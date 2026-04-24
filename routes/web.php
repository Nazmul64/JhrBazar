<?php

use App\Http\Controllers\Admin\Adminauthcontroller;
use App\Http\Controllers\Admin\Admincontroller;
use App\Http\Controllers\Admin\AipromptController;
use App\Http\Controllers\Admin\AlltaxesController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BusinessSettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\CurrencieController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\FlashsaleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductBrandController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\PointOfSalePosController;
use App\Http\Controllers\Admin\ProductControllerController;
use App\Http\Controllers\Admin\PromocodeController;
use App\Http\Controllers\Admin\SociallinkListController;
use App\Http\Controllers\Admin\ThemecolorssettingController;
use App\Http\Controllers\Admin\VerificatiootpsettingsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

// ── Admin Auth (Public) ─────────────────────────────────────────────────────
Route::get('admin/login',  [Adminauthcontroller::class, 'adminlogin'])->name('admin.login');
Route::post('admin/login', [Adminauthcontroller::class, 'admin_login_submit'])->name('admin.login.submit');
Route::post('admin/logout',[Adminauthcontroller::class, 'admin_logout'])->name('admin.logout');

// ── Admin Protected Routes ──────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->group(function () {

    // ── Dashboard ───────────────────────────────────────────────────────────
    Route::get('admin/dashboard', [Admincontroller::class, 'dashboard'])->name('admin.dashboard');

    // ── Customers ───────────────────────────────────────────────────────────
    Route::resource('admincustomers', CustomerController::class)->names('admin.customers');
    Route::post('admin/customers/{id}/reset-password', [CustomerController::class, 'resetPassword'])->name('admin.customers.reset-password');

    // ── Permissions ─────────────────────────────────────────────────────────
    Route::resource('permission', PermissionController::class)->names('admin.permissions');

    // ── Roles ────────────────────────────────────────────────────────────────
    Route::get('admin/roles',                        [RoleController::class, 'index'])           ->name('admin.role.index');
    Route::post('admin/roles',                       [RoleController::class, 'store'])           ->name('admin.role.store');
    Route::post('admin/roles/{id}/update-name',      [RoleController::class, 'updateName'])      ->name('admin.role.updateName');
    Route::post('admin/roles/{id}/sync-permissions', [RoleController::class, 'syncPermissions']) ->name('admin.role.syncPermissions');
    Route::delete('admin/roles/{id}',                [RoleController::class, 'destroy'])         ->name('admin.role.destroy');
    Route::get('admin/roles/{id}/permissions',       [RoleController::class, 'getPermissions'])  ->name('admin.role.permissions');

    // ── Employees ────────────────────────────────────────────────────────────
    Route::resource('admin/employee', EmployeeController::class)->names('admin.employees');
    Route::get('admin/employee/{employee}/permission',     [EmployeeController::class, 'permission'])       ->name('admin.employees.permission');
    Route::post('admin/employee/{employee}/permission',    [EmployeeController::class, 'updatePermission']) ->name('admin.employees.updatePermission');
    Route::post('admin/employee/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])     ->name('admin.employees.toggleStatus');
    Route::post('admin/employee/{employee}/reset-password',[EmployeeController::class, 'resetPassword'])    ->name('admin.employees.resetPassword');

    // ── Suppliers ────────────────────────────────────────────────────────────
    Route::resource('supplier', SupplierController::class)->names('admin.supplier');
    Route::post('supplier/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('admin.supplier.toggleStatus');
    Route::post('supplier/{supplier}/pay',           [SupplierController::class, 'pay'])          ->name('admin.supplier.pay');

    // ── Categories ───────────────────────────────────────────────────────────
    Route::resource('category', CategoryController::class)->names('admin.categories');
    Route::post('category/{category}/toggle', [CategoryController::class, 'toggleStatus'])->name('admin.categories.toggle');

    // ── SubCategories ────────────────────────────────────────────────────────
    Route::resource('subcategory', SubCategoryController::class)->names('admin.subcategory');
    Route::post('subcategory/{subcategory}/toggle', [SubCategoryController::class, 'toggleStatus'])->name('admin.subcategory.toggle');

    // ── Product Brands ───────────────────────────────────────────────────────
    Route::resource('productbrand', ProductBrandController::class)->names('admin.productbrands')->except(['create', 'show']);
    Route::post('productbrand/{productbrand}/toggle', [ProductBrandController::class, 'toggleStatus'])->name('admin.productbrands.toggle');

    // ── Colors ───────────────────────────────────────────────────────────────
    Route::resource('color', ColorController::class)->names('admin.colors')->except(['show']);
    Route::post('color/{color}/toggle', [ColorController::class, 'toggleStatus'])->name('admin.colors.toggle');

    // ── Sizes ────────────────────────────────────────────────────────────────
    Route::resource('size', SizeController::class)->names('admin.sizes')->except(['show', 'create']);
    Route::post('size/{size}/toggle', [SizeController::class, 'toggleStatus'])->name('admin.sizes.toggle');

    // ── Units ────────────────────────────────────────────────────────────────
    Route::resource('unit', UnitController::class)->names('admin.units')->except(['show', 'create']);
    Route::post('unit/{unit}/toggle', [UnitController::class, 'toggleStatus'])->name('admin.units.toggle');

    // ── General Settings ─────────────────────────────────────────────────────
    Route::resource('generalsetting', GeneralSettingController::class)->names('admin.generalsettings')->except(['show', 'create']);
    Route::post('generalsetting/{generalsetting}/toggle', [GeneralSettingController::class, 'toggleStatus'])->name('admin.generalsettings.toggle');

    // ── Business Settings ────────────────────────────────────────────────────
    Route::resource('businesssettings', BusinessSettingController::class)->names('admin.businesssettings')->except(['show', 'create', 'edit']);
    Route::post('businesssettings/{businesssettings}/toggle', [BusinessSettingController::class, 'toggleStatus'])->name('admin.businesssettings.toggle');

    // ── Verification OTP Settings ────────────────────────────────────────────
    Route::resource('verificationotpsettings', VerificatiootpsettingsController::class)->names('admin.verificationotpsettings')->except(['show', 'create', 'edit', 'update', 'destroy']);
    Route::post('verificationotpsettings/{verificationotpsettings}/toggle', [VerificatiootpsettingsController::class, 'toggleStatus'])->name('admin.verificationotpsettings.toggle');

    // ── AI Prompt ────────────────────────────────────────────────────────────
    Route::resource('aiprompt', AipromptController::class)->names('admin.aiprompt')->except(['show', 'create', 'edit', 'update', 'destroy']);
    Route::post('aiprompt/update-product', [AipromptController::class, 'updateProduct'])->name('admin.aiprompt.update-product');
    Route::post('aiprompt/update-page',    [AipromptController::class, 'updatePage'])   ->name('admin.aiprompt.update-page');
    Route::post('aiprompt/update-blog',    [AipromptController::class, 'updateBlog'])   ->name('admin.aiprompt.update-blog');

    // ── Currencies ───────────────────────────────────────────────────────────
    Route::resource('currency', CurrencieController::class)->names('admin.currencies');
    Route::post('currency/{currency}/toggle', [CurrencieController::class, 'toggleStatus'])->name('admin.currencies.toggle');

    // ── VAT & Taxes ──────────────────────────────────────────────────────────
    Route::resource('alltaxes', AlltaxesController::class)->names('admin.alltaxes');
    Route::post('alltaxes/{alltaxes}/toggle', [AlltaxesController::class, 'toggleStatus'])->name('admin.alltaxes.toggle');

    // ── Theme Color Settings ─────────────────────────────────────────────────
    // ⚠️ generate-palette must come BEFORE the resource to avoid {themecolorssettings} conflict
    Route::post('themecolorssettings/generate-palette', [ThemecolorssettingController::class, 'generatePalette'])->name('admin.themecolorssettings.generate-palette');
    Route::resource('themecolorssettings', ThemecolorssettingController::class)->names('admin.themecolorssettings');
    Route::post('themecolorssettings/{Themecolorssetting}/toggle', [ThemecolorssettingController::class, 'toggleStatus'])->name('admin.themecolorssettings.toggle');

    // ── Social Links ─────────────────────────────────────────────────────────
    Route::resource('sociallinkList', SociallinkListController::class)->names('admin.sociallinkList')->except(['create', 'store', 'show', 'destroy']);
    Route::post('sociallinkList/{sociallinkList}/toggle', [SociallinkListController::class, 'toggleStatus'])->name('admin.sociallinkList.toggle');

    // ── Products ─────────────────────────────────────────────────────────────
    // ⚠️ Custom routes must come BEFORE resource to avoid wildcard conflicts
    Route::get('products/subcategories/{categoryId}', [ProductControllerController::class, 'getSubCategories'])->name('products.subcategories');
    Route::post('products/{product}/toggle',          [ProductControllerController::class, 'toggleStatus'])    ->name('products.toggle');
    Route::get('products/{product}/barcode',          [ProductControllerController::class, 'barcode'])         ->name('products.barcode');
    Route::resource('products', ProductControllerController::class)->names('products')->except(['show']);

    // ── Flash Sales ──────────────────────────────────────────────────────────
    Route::resource('flashsale', FlashsaleController::class)->names('admin.flashsale');
    Route::post('flashsale/{flashsale}/toggle', [FlashsaleController::class, 'toggleStatus'])->name('admin.flashsale.toggle');

    // ── Banners ──────────────────────────────────────────────────────────────
    // ⚠️ toggle must come BEFORE resource
    Route::post('banner/{id}/toggle', [BannerController::class, 'toggleStatus'])->name('admin.banner.toggle');
    Route::resource('banner', BannerController::class)->names('admin.banner');

    // ── Promo Codes ──────────────────────────────────────────────────────────
    // ⚠️ toggle must come BEFORE resource
    Route::post('promocode/{id}/toggle', [PromocodeController::class, 'toggleStatus'])->name('admin.promocode.toggle');
    Route::resource('promocode', PromocodeController::class)->names('admin.promocode');

    // ────────────────────────────────────────────────────────────────────────
    //  Point of Sale (POS)
    // ────────────────────────────────────────────────────────────────────────
    Route::prefix('pointofsalepos')->name('admin.pointofsalepos.')->group(function () {

        // ── Main POS terminal ────────────────────────────────────────────
        Route::get('/',  [PointOfSalePosController::class, 'index'])->name('index');

        // ── Invoice view ─────────────────────────────────────────────────
        Route::get('invoice/{invoice}', [PointOfSalePosController::class, 'invoice'])->name('invoice');

        // ── AJAX: product list (paginated + filterable) ──────────────────
        Route::get('products', [PointOfSalePosController::class, 'getProducts'])->name('products');

        // ── AJAX: customer type-ahead search ─────────────────────────────
        Route::get('customers/search', [PointOfSalePosController::class, 'searchCustomers'])->name('customers.search');

        // ── AJAX: quick-create a new customer ────────────────────────────
        Route::post('customers/store', [PointOfSalePosController::class, 'storeCustomer'])->name('customers.store');

        // ── AJAX: validate & apply coupon ────────────────────────────────
        Route::post('apply-coupon', [PointOfSalePosController::class, 'applyCoupon'])->name('apply.coupon');

        // ── AJAX: place / complete an order ──────────────────────────────
        Route::post('place-order', [PointOfSalePosController::class, 'placeOrder'])->name('place.order');

        // ── AJAX: save cart as draft ──────────────────────────────────────
        Route::post('draft', [PointOfSalePosController::class, 'draft'])->name('draft');

        // ── Sales History ─────────────────────────────────────────────────
        Route::get('sales',                     [PointOfSalePosController::class, 'salesIndex'])  ->name('sales.index');
        Route::get('sales/{invoice}',           [PointOfSalePosController::class, 'salesShow'])   ->name('sales.show');
        Route::patch('sales/{invoice}/status',  [PointOfSalePosController::class, 'updateStatus'])->name('sales.status');

        // ── Draft Orders ─────────────────────────────────────────────────
        // ⚠️ static segments must come BEFORE wildcard segments
        Route::get('draft-orders',              [PointOfSalePosController::class, 'draftIndex'])  ->name('draft.index');
        Route::get('draft-orders/{draft}/load', [PointOfSalePosController::class, 'getDraft'])    ->name('draft.load');
        Route::delete('draft-orders/{draft}',   [PointOfSalePosController::class, 'draftDestroy'])->name('draft.destroy');

    }); // end pointofsalepos group

}); // end middleware group

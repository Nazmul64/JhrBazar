<?php

 // Frontend SPA Root Route
 Route::get('/', function () {
     return view('react-test');
 });

 // Frontend SPA Catch-all Route
 Route::get('/{any}', function () {
     return view('react-test');
 })->where('any', '^(?!admin|api|employee|manager|customer|seller|register|login|logout|user-profile).*$');

use App\Http\Controllers\Admin\Adminauthcontroller;
use App\Http\Controllers\Admin\Admincontroller;
use App\Http\Controllers\Admin\AipromptController;
use App\Http\Controllers\Admin\AlltaxesController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BusinessSettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\ContactController;
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
use App\Http\Controllers\Admin\StripeGatewayController;
use App\Http\Controllers\Admin\PaypalGatewayController;
use App\Http\Controllers\Admin\RazorpayGatewayController;
use App\Http\Controllers\Admin\PaystackGatewayController;
use App\Http\Controllers\Admin\AamarpayGatewayController;
use App\Http\Controllers\Admin\BkashGatewayController;
use App\Http\Controllers\Admin\PaytabsGatewayController;
use App\Http\Controllers\Admin\QicardGatewayController;
use App\Http\Controllers\Admin\JazzcashGatewayController;
use App\Http\Controllers\Admin\SteadfastCourierController;
use App\Http\Controllers\Admin\BkashPaymentController;
use App\Http\Controllers\Admin\DuplicateordersettingController;
use App\Http\Controllers\Admin\GoogleTagManagerController;
use App\Http\Controllers\Admin\IpblockmanageController;
use App\Http\Controllers\Admin\LandingPageController;
use App\Http\Controllers\Admin\ShurjopayGatewayController;
use App\Http\Controllers\Admin\PathaoCourierController;
use App\Http\Controllers\Admin\SmsGatewayController;
use App\Http\Controllers\Admin\TwilioGatewayController;
use App\Http\Controllers\Admin\TelesignGatewayController;
use App\Http\Controllers\Admin\NexmoGatewayController;
use App\Http\Controllers\Admin\MessagebirdGatewayController;
use App\Http\Controllers\Admin\MailConfigurationController;
use App\Http\Controllers\Admin\PixelController;
use App\Http\Controllers\Admin\ShippingChargeController;
use App\Http\Controllers\Admin\FraudDashboardController;
use App\Http\Controllers\Admin\FraudCheckController;
use App\Http\Controllers\Admin\FraudRuleController;
use App\Http\Controllers\Admin\FraudAlertController;
use App\Http\Controllers\Admin\FraudBlacklistController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Seller\SellerDashboardController;
use App\Http\Controllers\Seller\SellerAuthController;
use App\Http\Controllers\Seller\SellerCategoryController;
use App\Http\Controllers\Seller\SellerBrandController;
use App\Http\Controllers\Seller\SellerColorController;
use App\Http\Controllers\Seller\SellerSizeController;
use App\Http\Controllers\Seller\SellerUnitController;
use App\Http\Controllers\Seller\SellerSupplierController;
use App\Http\Controllers\Admin\SellerApprovalController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Manager\ManagerDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();


use App\Http\Controllers\CustomAuthController;

// ── Admin Auth (Public) ─────────────────────────────────────────────────────
Route::get('admin/login',   [Adminauthcontroller::class, 'adminlogin'])        ->name('admin.login');
Route::post('admin/login',  [Adminauthcontroller::class, 'admin_login_submit'])->name('admin.login.submit');
Route::post('admin/logout', [Adminauthcontroller::class, 'admin_logout'])      ->name('admin.logout');

// ── Custom User Registration ────────────────────────────────────────────────
Route::get('register/customer', [CustomAuthController::class, 'showCustomerRegister'])->name('register.customer');
Route::post('register/customer', [CustomAuthController::class, 'registerCustomer'])->name('register.customer.submit');
Route::get('register/seller', [CustomAuthController::class, 'showSellerRegister'])->name('register.seller');
Route::post('register/seller', [CustomAuthController::class, 'registerSeller'])->name('register.seller.submit');

// ── Employee Login ──────────────────────────────────────────────────────────
Route::get('employee/login',  [App\Http\Controllers\Employee\EmployeeAuthController::class, 'showLogin'])->name('employee.login');
Route::post('employee/login', [App\Http\Controllers\Employee\EmployeeAuthController::class, 'login'])->name('employee.login.submit');
Route::post('employee/logout', [App\Http\Controllers\Employee\EmployeeAuthController::class, 'logout'])->name('employee.logout');

// ── Manager Login ───────────────────────────────────────────────────────────
Route::get('manager/login',  [App\Http\Controllers\Manager\ManagerAuthController::class, 'showLogin'])->name('manager.login');
Route::post('manager/login', [App\Http\Controllers\Manager\ManagerAuthController::class, 'login'])->name('manager.login.submit');
Route::post('manager/logout', [App\Http\Controllers\Manager\ManagerAuthController::class, 'logout'])->name('manager.logout');

// ── Seller Login ────────────────────────────────────────────────────────────
Route::get('seller/login',  [App\Http\Controllers\Seller\SellerAuthController::class, 'showLogin'])->name('seller.login');
Route::post('seller/login', [App\Http\Controllers\Seller\SellerAuthController::class, 'login'])->name('seller.login.submit');
Route::post('seller/logout', [App\Http\Controllers\Seller\SellerAuthController::class, 'logout'])->name('seller.logout');

// ── Admin Protected Routes ──────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // ── Dashboard ────────────────────────────────────────────────────────────
    Route::get('dashboard', [Admincontroller::class, 'dashboard'])->name('admin.dashboard');

    // ── Customers ────────────────────────────────────────────────────────────

    // ── Customers ────────────────────────────────────────────────────────────
    Route::resource('admincustomers', CustomerController::class)->names('admin.customers');
    Route::post('customers/{id}/reset-password', [CustomerController::class, 'resetPassword'])->name('admin.customers.reset-password');

    // ── Permissions ──────────────────────────────────────────────────────────
    Route::resource('permission', PermissionController::class)->names('admin.permissions');

    // ── Roles ─────────────────────────────────────────────────────────────────
    Route::middleware(['permission:employee.list'])->group(function () {
        Route::get('roles',                        [RoleController::class, 'index'])           ->name('admin.role.index');
        Route::post('roles',                       [RoleController::class, 'store'])           ->name('admin.role.store');
        Route::post('roles/{id}/update-name',      [RoleController::class, 'updateName'])      ->name('admin.role.updateName');
        Route::post('roles/{id}/sync-permissions', [RoleController::class, 'syncPermissions']) ->name('admin.role.syncPermissions');
        Route::delete('roles/{id}',                [RoleController::class, 'destroy'])         ->name('admin.role.destroy');
        Route::get('roles/{id}/permissions',       [RoleController::class, 'getPermissions'])  ->name('admin.role.permissions');
    });

    // ── Employees ─────────────────────────────────────────────────────────────
    Route::resource('employee', EmployeeController::class)->names('admin.employees');
    Route::get('employee/{employee}/permission',      [EmployeeController::class, 'permission'])       ->name('admin.employees.permission');
    Route::post('employee/{employee}/permission',     [EmployeeController::class, 'updatePermission']) ->name('admin.employees.updatePermission');
    Route::post('employee/{employee}/toggle-status',  [EmployeeController::class, 'toggleStatus'])     ->name('admin.employees.toggleStatus');
    Route::post('employee/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])    ->name('admin.employees.resetPassword');

    // ── Seller Approvals ──────────────────────────────────────────────────────
    Route::get('seller-approvals', [SellerApprovalController::class, 'index'])->name('admin.sellers.approvals');
    Route::post('seller-approvals/{id}/approve', [SellerApprovalController::class, 'approve'])->name('admin.sellers.approve');
    Route::post('seller-approvals/{id}/reject',  [SellerApprovalController::class, 'reject'])->name('admin.sellers.reject');

    // ── Bank Management ───────────────────────────────────────────────────────
    Route::resource('banks', BankController::class)->names('admin.banks');

    // ── Unified Users ─────────────────────────────────────────────────────────
    Route::middleware(['permission:employee.list'])->group(function () {
        Route::resource('users', UserController::class)->names('admin.users');
    });

    // ── Suppliers ─────────────────────────────────────────────────────────────
    Route::resource('supplier', SupplierController::class)->names('admin.supplier');
    Route::post('supplier/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('admin.supplier.toggleStatus');
    Route::post('supplier/{supplier}/pay',           [SupplierController::class, 'pay'])          ->name('admin.supplier.pay');

    // ── Categories ────────────────────────────────────────────────────────────
    Route::resource('category', CategoryController::class)->names('admin.categories');
    Route::post('category/{category}/toggle', [CategoryController::class, 'toggleStatus'])->name('admin.categories.toggle');

    // ── SubCategories ─────────────────────────────────────────────────────────
    Route::resource('subcategory', SubCategoryController::class)->names('admin.subcategory');
    Route::post('subcategory/{subcategory}/toggle', [SubCategoryController::class, 'toggleStatus'])->name('admin.subcategory.toggle');

    // ── Product Brands ────────────────────────────────────────────────────────
    Route::resource('productbrand', ProductBrandController::class)->names('admin.productbrands')->except(['create', 'show']);
    Route::post('productbrand/{productbrand}/toggle', [ProductBrandController::class, 'toggleStatus'])->name('admin.productbrands.toggle');

    // ── Colors ────────────────────────────────────────────────────────────────
    Route::resource('color', ColorController::class)->names('admin.colors')->except(['show']);
    Route::post('color/{color}/toggle', [ColorController::class, 'toggleStatus'])->name('admin.colors.toggle');

    // ── Sizes ─────────────────────────────────────────────────────────────────
    Route::resource('size', SizeController::class)->names('admin.sizes')->except(['show', 'create']);
    Route::post('size/{size}/toggle', [SizeController::class, 'toggleStatus'])->name('admin.sizes.toggle');

    // ── Units ─────────────────────────────────────────────────────────────────
    Route::resource('unit', UnitController::class)->names('admin.units')->except(['show', 'create']);
    Route::post('unit/{unit}/toggle', [UnitController::class, 'toggleStatus'])->name('admin.units.toggle');

    // ── General Settings ──────────────────────────────────────────────────────
    Route::resource('generalsetting', GeneralSettingController::class)->names('admin.generalsettings')->except(['show', 'create']);
    Route::post('generalsetting/{generalsetting}/toggle', [GeneralSettingController::class, 'toggleStatus'])->name('admin.generalsettings.toggle');

    // ── Business Settings ─────────────────────────────────────────────────────
    Route::resource('businesssettings', BusinessSettingController::class)->names('admin.businesssettings')->except(['show', 'create', 'edit']);
    Route::post('businesssettings/{businesssettings}/toggle', [BusinessSettingController::class, 'toggleStatus'])->name('admin.businesssettings.toggle');

    // ── Verification OTP Settings ─────────────────────────────────────────────
    Route::resource('verificationotpsettings', VerificatiootpsettingsController::class)->names('admin.verificationotpsettings')->except(['show', 'create', 'edit', 'update', 'destroy']);
    Route::post('verificationotpsettings/{verificationotpsettings}/toggle', [VerificatiootpsettingsController::class, 'toggleStatus'])->name('admin.verificationotpsettings.toggle');

    // ── AI Prompt ─────────────────────────────────────────────────────────────
    Route::resource('aiprompt', AipromptController::class)->names('admin.aiprompt')->except(['show', 'create', 'edit', 'update', 'destroy']);
    Route::post('aiprompt/update-product', [AipromptController::class, 'updateProduct'])->name('admin.aiprompt.update-product');
    Route::post('aiprompt/update-page',    [AipromptController::class, 'updatePage'])   ->name('admin.aiprompt.update-page');
    Route::post('aiprompt/update-blog',    [AipromptController::class, 'updateBlog'])   ->name('admin.aiprompt.update-blog');

    // ── Currencies ────────────────────────────────────────────────────────────
    Route::resource('currency', CurrencieController::class)->names('admin.currencies');
    Route::post('currency/{currency}/toggle', [CurrencieController::class, 'toggleStatus'])->name('admin.currencies.toggle');

    // ── VAT & Taxes ───────────────────────────────────────────────────────────
    Route::resource('alltaxes', AlltaxesController::class)->names('admin.alltaxes');
    Route::post('alltaxes/{alltaxes}/toggle', [AlltaxesController::class, 'toggleStatus'])->name('admin.alltaxes.toggle');

    // ── Theme Color Settings ──────────────────────────────────────────────────
    Route::post('themecolorssettings/generate-palette', [ThemecolorssettingController::class, 'generatePalette'])->name('admin.themecolorssettings.generate-palette');
    Route::resource('themecolorssettings', ThemecolorssettingController::class)->names('admin.themecolorssettings');
    Route::post('themecolorssettings/{Themecolorssetting}/toggle', [ThemecolorssettingController::class, 'toggleStatus'])->name('admin.themecolorssettings.toggle');

    // ── Social Links ──────────────────────────────────────────────────────────
    Route::resource('sociallinkList', SociallinkListController::class)->names('admin.sociallinkList')->except(['create', 'store', 'show', 'destroy']);
    Route::post('sociallinkList/{sociallinkList}/toggle', [SociallinkListController::class, 'toggleStatus'])->name('admin.sociallinkList.toggle');

    // ── Products ──────────────────────────────────────────────────────────────
    Route::get('products/subcategories/{categoryId}', [ProductControllerController::class, 'getSubCategories'])->name('products.subcategories');
    Route::post('products/{product}/toggle',          [ProductControllerController::class, 'toggleStatus'])    ->name('products.toggle');
    Route::get('products/{product}/barcode',          [ProductControllerController::class, 'barcode'])         ->name('products.barcode');
    Route::resource('products', ProductControllerController::class)->names('products')->except(['show']);

    // ── Flash Sales ───────────────────────────────────────────────────────────
    Route::resource('flashsale', FlashsaleController::class)->names('admin.flashsale');
    Route::post('flashsale/{flashsale}/toggle', [FlashsaleController::class, 'toggleStatus'])->name('admin.flashsale.toggle');

    // ── Banners ───────────────────────────────────────────────────────────────
    Route::post('banner/{id}/toggle', [BannerController::class, 'toggleStatus'])->name('admin.banner.toggle');
    Route::resource('banner', BannerController::class)->names('admin.banner');

    // ── Promo Codes ───────────────────────────────────────────────────────────
    Route::post('promocode/{id}/toggle', [PromocodeController::class, 'toggleStatus'])->name('admin.promocode.toggle');
    Route::resource('promocode', PromocodeController::class)->names('admin.promocode');

    // ── Point of Sale (POS) ───────────────────────────────────────────────────
    Route::prefix('pointofsalepos')->name('admin.pointofsalepos.')->group(function () {
        Route::get('/',                         [PointOfSalePosController::class, 'index'])          ->name('index');
        Route::get('invoice/{invoice}',         [PointOfSalePosController::class, 'invoice'])        ->name('invoice');
        Route::get('products',                  [PointOfSalePosController::class, 'getProducts'])    ->name('products');
        Route::get('customers/search',          [PointOfSalePosController::class, 'searchCustomers'])->name('customers.search');
        Route::post('customers/store',          [PointOfSalePosController::class, 'storeCustomer'])  ->name('customers.store');
        Route::post('apply-coupon',             [PointOfSalePosController::class, 'applyCoupon'])    ->name('apply.coupon');
        Route::post('place-order',              [PointOfSalePosController::class, 'placeOrder'])     ->name('place.order');
        Route::post('draft',                    [PointOfSalePosController::class, 'draft'])          ->name('draft');
        Route::get('sales',                     [PointOfSalePosController::class, 'salesIndex'])     ->name('sales.index');
        Route::get('sales/{invoice}',           [PointOfSalePosController::class, 'salesShow'])      ->name('sales.show');
        Route::patch('sales/{invoice}/status',  [PointOfSalePosController::class, 'updateStatus'])   ->name('sales.status');
        Route::get('draft-orders',              [PointOfSalePosController::class, 'draftIndex'])     ->name('draft.index');
        Route::get('draft-orders/{draft}/load', [PointOfSalePosController::class, 'getDraft'])       ->name('draft.load');
        Route::delete('draft-orders/{draft}',   [PointOfSalePosController::class, 'draftDestroy'])   ->name('draft.destroy');
    });

    // ── Contact ───────────────────────────────────────────────────────────────
    Route::post('contact/{contact}/toggle', [ContactController::class, 'toggleStatus'])->name('admin.contact.toggle');
    Route::resource('contact', ContactController::class)->names('admin.contact')->except(['show']);

    // ── Settings / Gateways Page ──────────────────────────────────────────────
    Route::get('/settings/gateways', function () {
        $data = [
            'stripe'    => \App\Models\StripeGateway::first(),
            'paypal'    => \App\Models\PaypalGateway::first(),
            'razorpay'  => \App\Models\RazorpayGateway::first(),
            'paystack'  => \App\Models\PaystackGateway::first(),
            'aamarpay'  => \App\Models\AamarpayGateway::first(),
            'bkash'     => \App\Models\BkashGateway::first(),
            'paytabs'   => \App\Models\PaytabsGateway::first(),
            'qicard'    => \App\Models\QicardGateway::first(),
            'jazzcash'  => \App\Models\JazzcashGateway::first(),
            'steadfast' => \App\Models\SteadfastCourier::first(),
            'bkashPay'  => \App\Models\BkashPayment::first(),
            'shurjopay' => \App\Models\ShurjopayGateway::first(),
            'pathao'    => \App\Models\PathaoCourier::first(),
            'sms'       => \App\Models\SmsGateway::first(),
        ];
        return view('admin.settings.gateways', $data);
    })->name('admin.settings.gateways');

    // ── Payment Gateways ──────────────────────────────────────────────────────
    Route::post('/settings/stripe/update',   [StripeGatewayController::class,   'update'])      ->name('admin.stripe.update');
    Route::post('/settings/stripe/toggle',   [StripeGatewayController::class,   'toggleStatus'])->name('admin.stripe.toggle');
    Route::post('/settings/paypal/update',   [PaypalGatewayController::class,   'update'])      ->name('admin.paypal.update');
    Route::post('/settings/paypal/toggle',   [PaypalGatewayController::class,   'toggleStatus'])->name('admin.paypal.toggle');
    Route::post('/settings/razorpay/update', [RazorpayGatewayController::class, 'update'])      ->name('admin.razorpay.update');
    Route::post('/settings/razorpay/toggle', [RazorpayGatewayController::class, 'toggleStatus'])->name('admin.razorpay.toggle');
    Route::post('/settings/paystack/update', [PaystackGatewayController::class, 'update'])      ->name('admin.paystack.update');
    Route::post('/settings/paystack/toggle', [PaystackGatewayController::class, 'toggleStatus'])->name('admin.paystack.toggle');
    Route::post('/settings/aamarpay/update', [AamarpayGatewayController::class, 'update'])      ->name('admin.aamarpay.update');
    Route::post('/settings/aamarpay/toggle', [AamarpayGatewayController::class, 'toggleStatus'])->name('admin.aamarpay.toggle');
    Route::post('/settings/bkash/update',    [BkashGatewayController::class,    'update'])      ->name('admin.bkash.update');
    Route::post('/settings/bkash/toggle',    [BkashGatewayController::class,    'toggleStatus'])->name('admin.bkash.toggle');
    Route::post('/settings/paytabs/update',  [PaytabsGatewayController::class,  'update'])      ->name('admin.paytabs.update');
    Route::post('/settings/paytabs/toggle',  [PaytabsGatewayController::class,  'toggleStatus'])->name('admin.paytabs.toggle');
    Route::post('/settings/qicard/update',   [QicardGatewayController::class,   'update'])      ->name('admin.qicard.update');
    Route::post('/settings/qicard/toggle',   [QicardGatewayController::class,   'toggleStatus'])->name('admin.qicard.toggle');
    Route::post('/settings/jazzcash/update', [JazzcashGatewayController::class, 'update'])      ->name('admin.jazzcash.update');
    Route::post('/settings/jazzcash/toggle', [JazzcashGatewayController::class, 'toggleStatus'])->name('admin.jazzcash.toggle');

    // ── Courier ───────────────────────────────────────────────────────────────
    Route::post('/settings/steadfast/update', [SteadfastCourierController::class, 'update'])      ->name('admin.steadfast.update');
    Route::post('/settings/steadfast/toggle', [SteadfastCourierController::class, 'toggleStatus'])->name('admin.steadfast.toggle');
    Route::post('/settings/pathao/update',    [PathaoCourierController::class,    'update'])      ->name('admin.pathao.update');
    Route::post('/settings/pathao/toggle',    [PathaoCourierController::class,    'toggleStatus'])->name('admin.pathao.toggle');

    // ── BD Payment ────────────────────────────────────────────────────────────
    Route::post('/settings/bkash-pay/update', [BkashPaymentController::class,     'update'])      ->name('admin.bkash-pay.update');
    Route::post('/settings/bkash-pay/toggle', [BkashPaymentController::class,     'toggleStatus'])->name('admin.bkash-pay.toggle');
    Route::post('/settings/shurjopay/update', [ShurjopayGatewayController::class, 'update'])      ->name('admin.shurjopay.update');
    Route::post('/settings/shurjopay/toggle', [ShurjopayGatewayController::class, 'toggleStatus'])->name('admin.shurjopay.toggle');

    // ── SMS Gateway ───────────────────────────────────────────────────────────
    Route::post('/settings/sms/update', [SmsGatewayController::class, 'update'])->name('admin.sms.update');

    // ── SMS Configuration Page ────────────────────────────────────────────────
    Route::get('/settings/configuration/sms-configuration', function () {
        $data = [
            'twilio'      => \App\Models\TwilioGateway::first(),
            'telesign'    => \App\Models\TelesignGateway::first(),
            'nexmo'       => \App\Models\NexmoGateway::first(),
            'messagebird' => \App\Models\MessagebirdGateway::first(),
        ];
        return view('admin.smsconfiguration.sms-configuration', $data);
    })->name('admin.sms.configuration');

    // ── Twilio / Telesign / Nexmo / MessageBird ───────────────────────────────
    Route::post('/settingsconfiguration/twilio/update',      [TwilioGatewayController::class,      'update'])      ->name('admin.twilio.update');
    Route::post('/settingsconfiguration/twilio/toggle',      [TwilioGatewayController::class,      'toggleStatus'])->name('admin.twilio.toggle');
    Route::post('/settingsconfiguration/telesign/update',    [TelesignGatewayController::class,    'update'])      ->name('admin.telesign.update');
    Route::post('/settingsconfiguration/telesign/toggle',    [TelesignGatewayController::class,    'toggleStatus'])->name('admin.telesign.toggle');
    Route::post('/settingsconfiguration/nexmo/update',       [NexmoGatewayController::class,       'update'])      ->name('admin.nexmo.update');
    Route::post('/settingsconfiguration/nexmo/toggle',       [NexmoGatewayController::class,       'toggleStatus'])->name('admin.nexmo.toggle');
    Route::post('/settingsconfiguration/messagebird/update', [MessagebirdGatewayController::class, 'update'])      ->name('admin.messagebird.update');
    Route::post('/settingsconfiguration/messagebird/toggle', [MessagebirdGatewayController::class, 'toggleStatus'])->name('admin.messagebird.toggle');

    // ── Mail Configuration ────────────────────────────────────────────────────
    Route::get('/mail-configuration',            [MailConfigurationController::class, 'index'])       ->name('admin.mailconfiguration.index');
    Route::post('/mail-configuration/update',    [MailConfigurationController::class, 'update'])      ->name('admin.mailconfiguration.update');
    Route::post('/mail-configuration/send-test', [MailConfigurationController::class, 'sendTestMail'])->name('admin.mailconfiguration.send-test');

    // ── Pixels ────────────────────────────────────────────────────────────────
    Route::resource('pixels', PixelController::class)->names('admin.pixels')->except(['show']);
    Route::patch('pixels/{pixel}/toggle-status', [PixelController::class, 'toggleStatus'])->name('admin.pixels.toggle-status');

    // ── Shipping Charge ───────────────────────────────────────────────────────
    Route::resource('shippingcharge', ShippingChargeController::class)->names('admin.shippingcharge')->except(['show']);
    Route::patch('shippingcharge/{shippingcharge}/toggle-status', [ShippingChargeController::class, 'toggleStatus'])->name('admin.shippingcharge.toggle-status');

    // ── Landing Pages ─────────────────────────────────────────────────────────
    Route::resource('landingpages', LandingPageController::class)->names('admin.landingpages')->except(['show']);
    Route::patch('landingpages/{landingpage}/toggle-status', [LandingPageController::class, 'toggleStatus'])->name('admin.landingpages.toggle-status');

    // ── Google Tag Manager ────────────────────────────────────────────────────
    Route::resource('googletagmanager', GoogleTagManagerController::class)->names('admin.googletagmanager')->except(['show']);
    Route::patch('googletagmanager/{googletagmanager}/toggle-status', [GoogleTagManagerController::class, 'toggleStatus'])->name('admin.googletagmanager.toggle-status');

    // ── Duplicate Order Setting ───────────────────────────────────────────────
    Route::resource('duplicateordersetting', DuplicateordersettingController::class)->names('admin.duplicateordersetting')->except(['show']);
    Route::patch('duplicateordersetting/{duplicateordersetting}/toggle-status', [DuplicateordersettingController::class, 'toggleStatus'])->name('admin.duplicateordersetting.toggleStatus');

    // ── IP Block Manage ───────────────────────────────────────────────────────
    Route::resource('Ipblockmanage', IpblockmanageController::class)->names('admin.Ipblockmanage')->except(['show', 'create']);
    Route::patch('Ipblockmanage/{Ipblockmanage}/toggle-status', [IpblockmanageController::class, 'toggleStatus'])->name('admin.Ipblockmanage.toggleStatus');

    // ── Page Management ───────────────────────────────────────────────────────
    Route::resource('pages', PageController::class)->names('admin.pages');

    // ── Shop Management ───────────────────────────────────────────────────────
    Route::resource('shops', ShopController::class)->names('admin.shops')->except(['show']);
    Route::patch('shops/{shop}/toggle-status', [ShopController::class, 'toggleStatus'])->name('admin.shops.toggle-status');

    // ════════════════════════════════════════════════════════════════════════
    //  Fraud Management
    // ════════════════════════════════════════════════════════════════════════
    Route::prefix('fraud')->name('admin.fraud.')->group(function () {

        // ── Fraud Dashboard ───────────────────────────────────────────────────
        Route::get('dashboard', [FraudDashboardController::class, 'index'])->name('dashboard');

        // ── Fraud Checks ──────────────────────────────────────────────────────
        Route::post('checks/bulk-action', [FraudCheckController::class, 'bulkAction'])->name('bulk-action');
        Route::get('checks/export/csv',   [FraudCheckController::class, 'export'])    ->name('export');
        Route::resource('checks', FraudCheckController::class)
            ->parameters(['checks' => 'fraudCheck'])
            ->names([
                'index'   => 'index',
                'create'  => 'create',
                'store'   => 'store',
                'show'    => 'show',
                'edit'    => 'edit',
                'update'  => 'update',
                'destroy' => 'destroy',
            ]);

        // ── Fraud Rules ───────────────────────────────────────────────────────
        Route::resource('rules', FraudRuleController::class)
            ->parameters(['rules' => 'fraudRule'])
            ->names([
                'index'   => 'rules.index',
                'create'  => 'rules.create',
                'store'   => 'rules.store',
                'show'    => 'rules.show',
                'edit'    => 'rules.edit',
                'update'  => 'rules.update',
                'destroy' => 'rules.destroy',
            ]);
        Route::patch('rules/{fraudRule}/toggle', [FraudRuleController::class, 'toggle'])->name('rules.toggle');

        // ── Fraud Alerts ──────────────────────────────────────────────────────
        Route::get('alerts',                        [FraudAlertController::class, 'index'])  ->name('alerts.index');
        Route::get('alerts/{fraudAlert}',           [FraudAlertController::class, 'show'])   ->name('alerts.show');
        Route::patch('alerts/{fraudAlert}/resolve', [FraudAlertController::class, 'resolve'])->name('alerts.resolve');
        Route::patch('alerts/{fraudAlert}/assign',  [FraudAlertController::class, 'assign']) ->name('alerts.assign');

        // ── Fraud Blacklist ───────────────────────────────────────────────────
        Route::get('blacklist',                           [FraudBlacklistController::class, 'index'])  ->name('blacklist.index');
        Route::post('blacklist',                          [FraudBlacklistController::class, 'store'])  ->name('blacklist.store');
        Route::patch('blacklist/{fraudBlacklist}/toggle', [FraudBlacklistController::class, 'toggle']) ->name('blacklist.toggle');
        Route::delete('blacklist/{fraudBlacklist}',       [FraudBlacklistController::class, 'destroy'])->name('blacklist.destroy');

    }); // end fraud group


});

// ── Role-Based Dashboards ────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    // Employee Dashboard
    Route::middleware(['role:employee'])->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    });

    // Customer Dashboard
    Route::middleware(['role:customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    });

    // Manager Dashboard
    Route::middleware(['role:manager'])->prefix('manager')->name('manager.')->group(function () {
        Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');
    });

    // Seller Dashboard
    Route::middleware(['role:seller'])->prefix('seller')->name('seller.')->group(function () {
        Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');

        // Category Management (Read Only)
        Route::get('/categories', [SellerCategoryController::class, 'categories'])->name('categories.index');
        Route::get('/sub-categories', [SellerCategoryController::class, 'subCategories'])->name('subcategories.index');
        Route::get('/child-categories', [SellerCategoryController::class, 'childCategories'])->name('childcategories.index');

        // Product Variant Management (Read Only)
        Route::get('/brands', [SellerBrandController::class, 'index'])->name('brands.index');
        Route::get('/colors', [SellerColorController::class, 'index'])->name('colors.index');
        Route::get('/sizes',  [SellerSizeController::class, 'index'])->name('sizes.index');
        Route::get('/units',  [SellerUnitController::class, 'index'])->name('units.index');

        // Supplier Management
        Route::get('/suppliers', [SellerSupplierController::class, 'index'])->name('supplier.index');
        Route::get('/suppliers/create', [SellerSupplierController::class, 'create'])->name('supplier.create');
        Route::post('/suppliers', [SellerSupplierController::class, 'store'])->name('supplier.store');
        Route::get('/suppliers/{id}', [SellerSupplierController::class, 'show'])->name('supplier.show');
        Route::get('/suppliers/{id}/edit', [SellerSupplierController::class, 'edit'])->name('supplier.edit');
        Route::post('/suppliers/{id}', [SellerSupplierController::class, 'update'])->name('supplier.update');
        Route::delete('/suppliers/{id}', [SellerSupplierController::class, 'destroy'])->name('supplier.destroy');
        Route::post('/suppliers/{id}/toggle-status', [SellerSupplierController::class, 'toggleStatus'])->name('supplier.toggleStatus');
        Route::post('/suppliers/{id}/pay', [SellerSupplierController::class, 'pay'])->name('supplier.pay');

        // Employee Management for Seller
        Route::get('/employees', [App\Http\Controllers\Seller\EmployeeSellerController::class, 'index'])->name('employeeseller.index');
        Route::get('/employees/create', [App\Http\Controllers\Seller\EmployeeSellerController::class, 'create'])->name('employeeseller.create');
        Route::post('/employees', [App\Http\Controllers\Seller\EmployeeSellerController::class, 'store'])->name('employeeseller.store');
        Route::get('/employees/{id}/edit', [App\Http\Controllers\Seller\EmployeeSellerController::class, 'edit'])->name('employeeseller.edit');
        Route::post('/employees/{id}', [App\Http\Controllers\Seller\EmployeeSellerController::class, 'update'])->name('employeeseller.update');
        Route::delete('/employees/{id}', [App\Http\Controllers\Seller\EmployeeSellerController::class, 'destroy'])->name('employeeseller.destroy');

        // Product Management (Seller Product)
        Route::get('/products', [App\Http\Controllers\Seller\SellerProductController::class, 'index'])->name('product.index');
        Route::get('/products/create', [App\Http\Controllers\Seller\SellerProductController::class, 'create'])->name('product.create');
        Route::post('/products', [App\Http\Controllers\Seller\SellerProductController::class, 'store'])->name('product.store');
        Route::get('/products/{id}/edit', [App\Http\Controllers\Seller\SellerProductController::class, 'edit'])->name('product.edit');
        Route::post('/products/{id}', [App\Http\Controllers\Seller\SellerProductController::class, 'update'])->name('product.update');
        Route::delete('/products/{id}', [App\Http\Controllers\Seller\SellerProductController::class, 'destroy'])->name('product.destroy');
        Route::post('/products/{id}/toggle', [App\Http\Controllers\Seller\SellerProductController::class, 'toggleStatus'])->name('product.toggle');
        Route::get('/products/{id}/show', [App\Http\Controllers\Seller\SellerProductController::class, 'show'])->name('product.show');
        Route::get('/products/{id}/barcode', [App\Http\Controllers\Seller\SellerProductController::class, 'barcode'])->name('product.barcode');

        // Digital Product Management (Seller Digital Product)
        Route::get('/digital-products', [App\Http\Controllers\Seller\SellerDigitalProductController::class, 'index'])->name('digital_product.index');
        Route::get('/digital-products/create', [App\Http\Controllers\Seller\SellerDigitalProductController::class, 'create'])->name('digital_product.create');
        Route::post('/digital-products', [App\Http\Controllers\Seller\SellerDigitalProductController::class, 'store'])->name('digital_product.store');
        Route::get('/digital-products/{id}/edit', [App\Http\Controllers\Seller\SellerDigitalProductController::class, 'edit'])->name('digital_product.edit');
        Route::post('/digital-products/{id}', [App\Http\Controllers\Seller\SellerDigitalProductController::class, 'update'])->name('digital_product.update');
        Route::delete('/digital-products/{id}', [App\Http\Controllers\Seller\SellerDigitalProductController::class, 'destroy'])->name('digital_product.destroy');
        Route::post('/digital-products/{id}/toggle', [App\Http\Controllers\Seller\SellerDigitalProductController::class, 'toggleStatus'])->name('digital_product.toggle');
        Route::get('/digital-products/{id}/show', [App\Http\Controllers\Seller\SellerDigitalProductController::class, 'show'])->name('digital_product.show');
        Route::get('/digital-products/{id}/barcode', [App\Http\Controllers\Seller\SellerDigitalProductController::class, 'barcode'])->name('digital_product.barcode');

        // Flash Sales (Read-Only)
        Route::get('/flash-sales', [App\Http\Controllers\Seller\SellerFlashSalesShowController::class, 'index'])->name('flashsales.index');
        Route::get('/flash-sales/{id}', [App\Http\Controllers\Seller\SellerFlashSalesShowController::class, 'show'])->name('flashsales.show');

        // Promo Code (Seller Voucher)
        Route::get('/promo-codes', [App\Http\Controllers\Seller\SellerVoucherController::class, 'index'])->name('promocode.index');
        Route::get('/promo-codes/create', [App\Http\Controllers\Seller\SellerVoucherController::class, 'create'])->name('promocode.create');
        Route::post('/promo-codes', [App\Http\Controllers\Seller\SellerVoucherController::class, 'store'])->name('promocode.store');
        Route::get('/promo-codes/{id}/edit', [App\Http\Controllers\Seller\SellerVoucherController::class, 'edit'])->name('promocode.edit');
        Route::post('/promo-codes/{id}', [App\Http\Controllers\Seller\SellerVoucherController::class, 'update'])->name('promocode.update');
        Route::delete('/promo-codes/{id}', [App\Http\Controllers\Seller\SellerVoucherController::class, 'destroy'])->name('promocode.destroy');
        Route::post('/promo-codes/{id}/toggle', [App\Http\Controllers\Seller\SellerVoucherController::class, 'toggleStatus'])->name('promocode.toggle');

        // Banner Setup (Seller Banner)
        Route::get('/banners', [App\Http\Controllers\Seller\SellerBannerController::class, 'index'])->name('banner.index');
        Route::get('/banners/create', [App\Http\Controllers\Seller\SellerBannerController::class, 'create'])->name('banner.create');
        Route::post('/banners', [App\Http\Controllers\Seller\SellerBannerController::class, 'store'])->name('banner.store');
        Route::get('/banners/{id}/edit', [App\Http\Controllers\Seller\SellerBannerController::class, 'edit'])->name('banner.edit');
        Route::post('/banners/{id}', [App\Http\Controllers\Seller\SellerBannerController::class, 'update'])->name('banner.update');
        Route::delete('/banners/{id}', [App\Http\Controllers\Seller\SellerBannerController::class, 'destroy'])->name('banner.destroy');
        Route::post('/banners/{id}/toggle', [App\Http\Controllers\Seller\SellerBannerController::class, 'toggleStatus'])->name('banner.toggle');

        // My Shop (Seller)
        Route::prefix('my-shop')->name('shop.')->group(function () {
            Route::get('/',        [App\Http\Controllers\Seller\SellerShopController::class, 'index'])->name('index');
            Route::get('/edit',    [App\Http\Controllers\Seller\SellerShopController::class, 'edit'])->name('edit');
            Route::post('/update', [App\Http\Controllers\Seller\SellerShopController::class, 'update'])->name('update');
        });

        // Customer Management (Seller)
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/',          [App\Http\Controllers\Seller\SellerCustomerController::class, 'index'])->name('index');
            Route::get('/create',    [App\Http\Controllers\Seller\SellerCustomerController::class, 'create'])->name('create');
            Route::post('/store',    [App\Http\Controllers\Seller\SellerCustomerController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [App\Http\Controllers\Seller\SellerCustomerController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [App\Http\Controllers\Seller\SellerCustomerController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [App\Http\Controllers\Seller\SellerCustomerController::class, 'destroy'])->name('destroy');
        });

        // POS Management (Seller POS)
        Route::prefix('pos')->name('pos.')->group(function () {
            Route::get('/',              [App\Http\Controllers\Seller\SellerPosController::class, 'index'])->name('index');
            Route::get('/products',      [App\Http\Controllers\Seller\SellerPosController::class, 'getProducts'])->name('products');
            Route::post('/customer',     [App\Http\Controllers\Seller\SellerPosController::class, 'storeCustomer'])->name('customer.store');
            Route::post('/coupon',       [App\Http\Controllers\Seller\SellerPosController::class, 'applyCoupon'])->name('coupon.apply');
            Route::post('/place-order',  [App\Http\Controllers\Seller\SellerPosController::class, 'placeOrder'])->name('place-order');
            Route::post('/draft',        [App\Http\Controllers\Seller\SellerPosController::class, 'draft'])->name('draft');
            Route::get('/sales-history', [App\Http\Controllers\Seller\SellerPosController::class, 'salesIndex'])->name('sales-history');
            Route::get('/drafts',        [App\Http\Controllers\Seller\SellerPosController::class, 'draftIndex'])->name('drafts');
            Route::get('/draft/{draft}', [App\Http\Controllers\Seller\SellerPosController::class, 'getDraft'])->name('draft.get');
            Route::get('/invoice/{id}',  [App\Http\Controllers\Seller\SellerPosController::class, 'printInvoice'])->name('invoice.print');
        });
    });

    // ── Shared Profile Management ─────────────────────────────────────────────
    Route::prefix('user-profile')->name('admin.profile.')->group(function () {
        Route::get('/',                 [ProfileController::class, 'index'])         ->name('index');
        Route::get('/create',           [ProfileController::class, 'create'])        ->name('create');
        Route::post('/store',           [ProfileController::class, 'store'])         ->name('store');
        Route::get('/edit',             [ProfileController::class, 'edit'])          ->name('edit');
        Route::post('/update',          [ProfileController::class, 'update'])        ->name('update');
        Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
    });
});


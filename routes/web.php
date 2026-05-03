<?php
 
 // Frontend SPA Root Route
 Route::get('/', function () {
     return view('react-test');
 });
 
 // Frontend SPA Catch-all Route
 Route::get('/{any}', function () {
     return view('react-test');
 })->where('any', '^(?!admin|api).*$');

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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

// Frontend SPA Root Route
Route::get('/', function () {
    return view('react-test');
});

// Frontend SPA Catch-all Route
Route::get('/{any}', function () {
    return view('react-test');
})->where('any', '^(?!admin|api).*$');

// ── Admin Auth (Public) ─────────────────────────────────────────────────────
Route::get('admin/login',   [Adminauthcontroller::class, 'adminlogin'])        ->name('admin.login');
Route::post('admin/login',  [Adminauthcontroller::class, 'admin_login_submit'])->name('admin.login.submit');
Route::post('admin/logout', [Adminauthcontroller::class, 'admin_logout'])      ->name('admin.logout');

// ── Admin Protected Routes ──────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->group(function () {

    // ── Dashboard ────────────────────────────────────────────────────────────
    Route::get('admin/dashboard', [Admincontroller::class, 'dashboard'])->name('admin.dashboard');

    // ════════════════════════════════════════════════════════════════════════
    //  ✅ PROFILE MANAGEMENT  (top-level — NOT inside any prefix group)
    //  Route names: admin.profile.index / .create / .store / .edit / .update
    // ════════════════════════════════════════════════════════════════════════
    Route::prefix('admin/profile')->name('admin.profile.')->group(function () {
        Route::get('/',        [ProfileController::class, 'index'])  ->name('index');
        Route::get('/create',  [ProfileController::class, 'create']) ->name('create');
        Route::post('/store',  [ProfileController::class, 'store'])  ->name('store');
        Route::get('/edit',    [ProfileController::class, 'edit'])   ->name('edit');
        Route::post('/update', [ProfileController::class, 'update']) ->name('update');
    });

    // ── Customers ────────────────────────────────────────────────────────────
    Route::resource('admincustomers', CustomerController::class)->names('admin.customers');
    Route::post('admin/customers/{id}/reset-password', [CustomerController::class, 'resetPassword'])->name('admin.customers.reset-password');

    // ── Permissions ──────────────────────────────────────────────────────────
    Route::resource('permission', PermissionController::class)->names('admin.permissions');

    // ── Roles ─────────────────────────────────────────────────────────────────
    Route::get('admin/roles',                        [RoleController::class, 'index'])           ->name('admin.role.index');
    Route::post('admin/roles',                       [RoleController::class, 'store'])           ->name('admin.role.store');
    Route::post('admin/roles/{id}/update-name',      [RoleController::class, 'updateName'])      ->name('admin.role.updateName');
    Route::post('admin/roles/{id}/sync-permissions', [RoleController::class, 'syncPermissions']) ->name('admin.role.syncPermissions');
    Route::delete('admin/roles/{id}',                [RoleController::class, 'destroy'])         ->name('admin.role.destroy');
    Route::get('admin/roles/{id}/permissions',       [RoleController::class, 'getPermissions'])  ->name('admin.role.permissions');

    // ── Employees ─────────────────────────────────────────────────────────────
    Route::resource('admin/employee', EmployeeController::class)->names('admin.employees');
    Route::get('admin/employee/{employee}/permission',      [EmployeeController::class, 'permission'])       ->name('admin.employees.permission');
    Route::post('admin/employee/{employee}/permission',     [EmployeeController::class, 'updatePermission']) ->name('admin.employees.updatePermission');
    Route::post('admin/employee/{employee}/toggle-status',  [EmployeeController::class, 'toggleStatus'])     ->name('admin.employees.toggleStatus');
    Route::post('admin/employee/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])    ->name('admin.employees.resetPassword');

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
    Route::resource('admin/pages', PageController::class)->names('admin.pages');

    // ── Shop Management ───────────────────────────────────────────────────────
    Route::resource('admin/shops', ShopController::class)->names('admin.shops')->except(['show']);
    Route::patch('admin/shops/{shop}/toggle-status', [ShopController::class, 'toggleStatus'])->name('admin.shops.toggle-status');

    // ════════════════════════════════════════════════════════════════════════
    //  Fraud Management
    // ════════════════════════════════════════════════════════════════════════
    Route::prefix('admin/fraud')->name('admin.fraud.')->group(function () {

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
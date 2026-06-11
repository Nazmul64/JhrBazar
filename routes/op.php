<?php
use Illuminate\Support\Facades\Route;

// API version 1 routes
Route::prefix('api/v1')->group(function () {
    // Public routes
    Route::post('auth/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('auth/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::get('products', [App\Http\Controllers\Api\ProductController::class, 'index']);
    Route::get('products/{id}', [App\Http\Controllers\Api\ProductController::class, 'show']);

    // Protected routes – JWT auth
    Route::middleware('jwt.auth')->group(function () {
        Route::post('cart', [App\Http\Controllers\Api\CartController::class, 'add']);
        Route::get('cart', [App\Http\Controllers\Api\CartController::class, 'show']);
        Route::delete('cart/{itemId}', [App\Http\Controllers\Api\CartController::class, 'remove']);

        Route::post('checkout', [App\Http\Controllers\Api\CheckoutController::class, 'process']);
        Route::get('orders', [App\Http\Controllers\Api\OrderController::class, 'index']);
        Route::get('orders/{id}', [App\Http\Controllers\Api\OrderController::class, 'show']);
        Route::post('payments', [App\Http\Controllers\Api\PaymentController::class, 'pay']);
        Route::get('user/profile', [App\Http\Controllers\Api\UserController::class, 'profile']);
        Route::post('auth/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    });
});

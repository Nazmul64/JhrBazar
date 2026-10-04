<?php
// bootstrap/app.php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: '/api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }
            if ($request->is('seller') || $request->is('seller/*')) {
                return route('seller.login');
            }
            if ($request->is('manager') || $request->is('manager/*')) {
                return route('manager.login');
            }
            if ($request->is('employee') || $request->is('employee/*')) {
                return route('employee.login');
            }
            return route('admin.login');
        });

        $middleware->alias([
            'admin'      => AdminMiddleware::class,
            'role'       => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'blocked'    => \App\Http\Middleware\CheckBlocked::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/payment/sslcommerz/success',
            '/payment/sslcommerz/fail',
            '/payment/sslcommerz/cancel',
            '/payment/sslcommerz/ipn',
        ]);

        $middleware->appendToGroup('web', [
            \App\Http\Middleware\CheckBlocked::class,
            \App\Http\Middleware\TrackReturningCustomer::class,
            \App\Http\Middleware\NormalizeImageUrls::class,
        ]);

        $middleware->appendToGroup('api', [
            \App\Http\Middleware\CheckBlocked::class,
            \App\Http\Middleware\TrackReturningCustomer::class,
            \App\Http\Middleware\NormalizeImageUrls::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

require base_path('routes/op.php');

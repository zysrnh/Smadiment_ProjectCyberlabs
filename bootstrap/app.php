<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register admin middleware alias
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // Append RememberSelectedProject ke semua web routes
        $middleware->web(append: [
            \App\Http\Middleware\RememberSelectedProject::class,
        ]);

        // ✅ Redirect authenticated users (away from guest-only routes like /login)
        $middleware->redirectUsersTo(function ($request) {
            if ($request->is('admin/*')) {
                return route('admin.dashboard');
            }
            return route('mk.dashboard');
        });

        // ✅ Redirect guests (to login)
        $middleware->redirectGuestsTo(function ($request) {
            if (!$request->expectsJson()) {
                session()->flash('warning', 'Sesi Anda telah habis. Silahkan login kembali.');
            }
            return route('user.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
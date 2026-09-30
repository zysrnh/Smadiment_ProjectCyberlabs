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
            'check.trial' => \App\Http\Middleware\CheckUserTrial::class,
        ]);

        // Append RememberSelectedProject ke semua web routes
        $middleware->web(append: [
            \App\Http\Middleware\RememberSelectedProject::class,
        ]);

        // ✅ Exclude login routes dari CSRF check
        // Ini aman karena login cuma pakai Auth::attempt() yang validate credentials
        // Mencegah loop "Sesi Berakhir" ketika token expired
        $middleware->validateCsrfTokens(except: [
            'user/login',
            'admin/login',
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
            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            return route('user.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // ── Tangkap ConnectionException (API timeout / server unreachable) ──
        $exceptions->render(function (\Illuminate\Http\Client\ConnectionException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Service unavailable — server API tidak dapat dijangkau.',
                    'code'    => 'CONN_TIMEOUT',
                ], 503);
            }
            return response()->view('errors.service-unavailable', [
                'errorCode' => 'CONN_TIMEOUT',
            ], 503);
        });

        // ── Tangkap RuntimeException dari MediaKernelsClient (ENV belum di-set / Token bermasalah) ──
        $exceptions->render(function (\RuntimeException $e, $request) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'MEDIAKERNELS') || str_contains($msg, 'belum di-set') || str_contains($msg, 'Token tidak ditemukan')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error'   => 'Layanan data (Media Kernels) sedang tidak tersedia.',
                        'code'    => 'MK_API_ERROR',
                    ], 503);
                }
                return response()->view('errors.service-unavailable', [
                    'errorCode' => 'MK_API_ERROR',
                ], 503);
            }
        });

        // ── Tangkap TokenMismatchException & 419 HttpException (CSRF Expired / Sesi Habis) ──
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error'   => 'Maaf, sesi Anda telah habis. Harap login kembali.',
                        'code'    => 'SESSION_EXPIRED',
                    ], 419);
                }

                if ($request->is('admin/*', 'admin/login*')) {
                    return redirect()->route('admin.login')->with('warning', 'Maaf, sesi Anda telah habis. Harap login kembali.');
                }

                return redirect()->route('user.login')->with('warning', 'Maaf, sesi Anda telah habis. Harap login kembali.');
            }
        });

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Maaf, sesi Anda telah habis. Harap login kembali.',
                    'code'    => 'SESSION_EXPIRED',
                ], 419);
            }

            if ($request->is('admin/*', 'admin/login*')) {
                return redirect()->route('admin.login')->with('warning', 'Maaf, sesi Anda telah habis. Harap login kembali.');
            }

            return redirect()->route('user.login')->with('warning', 'Maaf, sesi Anda telah habis. Harap login kembali.');
        });

    })->create();
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserTrial
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in via the default web guard
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            
            // If the trial is expired, log them out and redirect to login
            if (!$user->isTrialActive()) {
                Auth::guard('web')->logout();
                
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Akun anda sudah kadaluarsa, mohon hubungi admin.'
                    ], 403);
                }

                return redirect()->route('user.login')->withErrors([
                    'email' => 'Akun anda sudah kadaluarsa, mohon hubungi admin.',
                ]);
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RememberSelectedProject
{
    public function handle(Request $request, Closure $next)
    {
        // ── PROJECT ID ──────────────────────────────────────
        if ($request->query('project_id')) {
            session(['selected_project_id' => $request->query('project_id')]);
        }

        if (!$request->query('project_id') && session('selected_project_id')) {
            $request->merge(['project_id' => session('selected_project_id')]);
        }

        // ── DATE FILTER ─────────────────────────────────────
        if ($request->query('start_date') && $request->query('end_date')) {
            session([
                'selected_start_date' => $request->query('start_date'),
                'selected_end_date'   => $request->query('end_date'),
            ]);
        }

        if (!$request->query('start_date') && session('selected_start_date')) {
            $request->merge([
                'start_date' => session('selected_start_date'),
                'end_date'   => session('selected_end_date'),
            ]);
        }

        return $next($request);
    }
}
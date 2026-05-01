<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstitutionRole
{
    /**
     * handle request untuk memastikan user adalah instansi
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isInstitution()) {
            abort(403, 'Unauthorized. Halaman ini hanya untuk instansi.');
        }

        return $next($request);
    }
}
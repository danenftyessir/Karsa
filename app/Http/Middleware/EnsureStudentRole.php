<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentRole
{
    /**
     * handle request untuk memastikan user adalah mahasiswa
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isStudent()) {
            abort(403, 'Unauthorized. Halaman ini hanya untuk mahasiswa.');
        }

        return $next($request);
    }
}
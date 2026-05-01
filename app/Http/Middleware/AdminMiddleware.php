<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AdminMiddleware - Protects admin routes
 *
 * Ensures only authenticated users with user_type = 'admin' can access admin routes
 */
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Hanya admin yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}

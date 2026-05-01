<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SmoothPageTransition
{
    /**
     * middleware untuk menambahkan header yang mendukung smooth page transition
     * menggunakan custom JS implementation
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // tambahkan header untuk smooth navigation
        if ($response instanceof Response) {
            $response->headers->set('X-Smooth-Navigation', 'enabled');
            $response->headers->set('X-Transition-Duration', '300');
        }

        return $response;
    }
}
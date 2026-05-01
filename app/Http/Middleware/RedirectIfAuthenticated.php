<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * handle request untuk redirect user yang sudah login
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                
                // redirect berdasarkan user type
                $redirectPath = match ($user->user_type) {
                    'student' => '/student/dashboard',
                    'institution' => '/institution/dashboard',
                    'admin' => '/admin/dashboard',
                    default => '/',
                };

                return redirect($redirectPath);
            }
        }

        return $next($request);
    }
}
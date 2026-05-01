<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * handle incoming request
     */
    public function handle(Request $request, Closure $next, string $userType): Response
    {
        // cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'silakan login terlebih dahulu');
        }

        $user = auth()->user();

        // cek tipe user
        if ($user->user_type !== $userType) {
            // redirect ke dashboard sesuai role mereka
            $redirectRoute = match($user->user_type) {
                'student' => 'student.dashboard',
                'institution' => 'institution.dashboard',
                'company' => 'company.dashboard',
                default => 'home',
            };

            return redirect()->route($redirectRoute)
                ->with('error', 'anda tidak memiliki akses ke halaman ini');
        }

        // cek apakah user memiliki profile yang sesuai dengan tipe mereka
        $hasProfile = match($userType) {
            'student' => $user->student()->exists(),
            'institution' => $user->institution()->exists(),
            'company' => $user->company()->exists(),
            default => true,
        };

        if (!$hasProfile) {
            // user memiliki tipe yang benar tapi tidak ada profile data
            // ini adalah kondisi data yang tidak valid
            return redirect()->route('home')
                ->with('error', 'profil anda belum lengkap. silakan hubungi administrator.');
        }

        return $next($request);
    }
}
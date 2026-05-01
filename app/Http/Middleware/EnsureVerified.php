<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVerified
{
    /**
     * handle request untuk memastikan user sudah terverifikasi
     * 
     * middleware ini khusus untuk instansi yang perlu verifikasi admin
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // hanya cek verifikasi untuk instansi
        if ($user->isInstitution()) {
            if (!$user->is_verified) {
                // redirect ke dashboard dengan pesan
                return redirect()->route('institution.dashboard')
                    ->with('warning', 'Akun instansi anda sedang dalam proses verifikasi. Beberapa fitur mungkin terbatas.');
            }
        }

        return $next($request);
    }
}
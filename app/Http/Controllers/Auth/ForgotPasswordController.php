<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * tampilkan form request reset password link
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * kirim reset password link ke email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'email wajib diisi',
            'email.email' => 'format email tidak valid',
        ]);

        // kirim reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // cek status
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'link reset password telah dikirim ke email anda');
        }

        return back()->withErrors([
            'email' => 'email tidak ditemukan atau terjadi kesalahan. silakan coba lagi.',
        ]);
    }
}
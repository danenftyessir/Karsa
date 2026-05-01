<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\VerifyEmailMail;

class EmailVerificationController extends Controller
{
    /**
     * tampilkan halaman notice untuk verifikasi email
     */
    public function notice()
    {
        // jika sudah terverifikasi, redirect ke dashboard
        if (auth()->user()->email_verified_at) {
            // redirect ke dashboard yang sesuai
            $redirectRoute = match(auth()->user()->user_type) {
                'student' => 'student.dashboard',
                'institution' => 'institution.dashboard',
                default => 'home',
            };
            return redirect()->route($redirectRoute);
        }
        
        // kirim data pengguna yang sedang login ke view
        return view('auth.verify-email', [
            'user' => auth()->user()
        ]);
    }

    /**
     * verifikasi email dengan token
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // cek apakah hash valid
        if (!hash_equals((string) $hash, sha1($user->email))) {
            return redirect()
                ->route('verification.notice')
                ->withErrors(['email' => 'link verifikasi tidak valid']);
        }

        // cek apakah sudah terverifikasi
        if ($user->email_verified_at) {
            // redirect ke dashboard sesuai user type
            $redirectRoute = match($user->user_type) {
                'student' => 'student.dashboard',
                'institution' => 'institution.dashboard',
                default => 'home',
            };
            
            return redirect()
                ->route($redirectRoute)
                ->with('info', 'email sudah terverifikasi sebelumnya');
        }

        // tandai email sebagai terverifikasi
        $user->update([
            'email_verified_at' => Carbon::now(),
            'email_verification_token' => null
        ]);

        // log successful verification
        Log::info('email verified successfully', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        // redirect ke dashboard sesuai user type
        $redirectRoute = match($user->user_type) {
            'student' => 'student.dashboard',
            'institution' => 'institution.dashboard',
            default => 'home',
        };

        return redirect()
            ->route($redirectRoute)
            ->with('success', 'email berhasil diverifikasi! selamat bergabung di Karsa');
    }

    /**
     * kirim ulang email verifikasi
     */
    public function resend(Request $request)
    {
        $user = auth()->user();

        // cek apakah sudah terverifikasi
        if ($user->email_verified_at) {
            return back()->with('info', 'email anda sudah terverifikasi');
        }

        // cek rate limiting (max 1 request per menit)
        $lastSent = session('verification_last_sent');
        if ($lastSent && Carbon::parse($lastSent)->addMinute()->isFuture()) {
            $seconds = Carbon::parse($lastSent)->addMinute()->diffInSeconds(now());
            return back()->withErrors([
                'email' => "tunggu {$seconds} detik sebelum mengirim ulang"
            ]);
        }

        // generate token baru
        $token = sha1($user->email . now());
        $user->update(['email_verification_token' => $token]);

        // kirim ulang email verifikasi
        try {
            Mail::to($user->email)->send(new VerifyEmailMail($user));
            
            Log::info('verification email resent', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        } catch (\Exception $e) {
            Log::error('gagal mengirim ulang email verifikasi', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'gagal mengirim email. silakan coba lagi nanti.');
        }

        session(['verification_last_sent' => now()]);

        return back()->with('success', 'email verifikasi baru telah berhasil dikirim!');
    }
}
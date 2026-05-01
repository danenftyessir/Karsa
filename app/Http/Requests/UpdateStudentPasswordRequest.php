<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * request validation untuk update password mahasiswa
 * 
 * path: app/Http/Requests/UpdateStudentPasswordRequest.php
 */
class UpdateStudentPasswordRequest extends FormRequest
{
    /**
     * tentukan apakah user ter-autorisasi untuk request ini
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * aturan validasi untuk request
     */
    public function rules(): array
    {
        return [
            // validasi password lama dengan custom closure
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, auth()->user()->password)) {
                        $fail('Password Lama Yang Anda Masukkan Salah.');
                    }
                },
            ],
            // validasi password baru
            'password' => [
                'required', 
                'confirmed',
                'min:8',
                'different:current_password', // pastikan berbeda dengan password lama
            ],
            // konfirmasi password (otomatis divalidasi oleh 'confirmed')
            'password_confirmation' => 'required',
        ];
    }

    /**
     * pesan error kustom untuk validasi
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Password Lama Wajib Diisi.',
            'password.required' => 'Password Baru Wajib Diisi.',
            'password.confirmed' => 'Konfirmasi Password Tidak Cocok.',
            'password.min' => 'Password Baru Minimal 8 Karakter.',
            'password.different' => 'Password Baru Harus Berbeda Dengan Password Lama.',
            'password_confirmation.required' => 'Konfirmasi Password Wajib Diisi.',
        ];
    }

    /**
     * custom attribute names untuk pesan error
     */
    public function attributes(): array
    {
        return [
            'current_password' => 'password lama',
            'password' => 'password baru',
            'password_confirmation' => 'konfirmasi password',
        ];
    }
}
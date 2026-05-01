<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StudentRegisterRequest extends FormRequest
{
    /**
     * tentukan apakah user diizinkan untuk melakukan request ini
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * aturan validasi yang diterapkan pada request
     */
    public function rules(): array
    {
        return [
            'first_name' => [
                'required',
                'string',
                'max:50',
            ],
            'last_name' => [
                'required',
                'string',
                'max:50',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'username' => [
                'required',
                'string',
                'max:50',
                'unique:users,username',
                'min:4',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()    // wajib huruf besar dan kecil
                    ->numbers()      // wajib angka
                    ->symbols()      // wajib simbol
                    ->uncompromised(app()->isProduction() ? 3 : 0) // cek data breach hanya di production
            ],
            'university_id' => [
                'required',
                'exists:universities,id'
            ],
            'major' => [
                'required',
                'string',
                'max:100'
            ],
            'nim' => [
                'required',
                'string',
                'max:20',
                'unique:students,nim',
            ],
            'semester' => [
                'required',
                'integer',
                'min:1',
                'max:14'
            ],
            'whatsapp_number' => [
                'required',
                'string',
                'min:10',
                'max:20',
            ],
            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png',
                'max:2048' // 2MB
            ]
        ];
    }

    /**
     * pesan error kustom untuk validasi
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Nama Depan Wajib Diisi',
            'last_name.required' => 'Nama Belakang Wajib Diisi',
            'email.required' => 'Email Wajib Diisi',
            'email.email' => 'Format Email Tidak Valid',
            'email.unique' => 'Email Sudah Terdaftar',
            'username.required' => 'Username Wajib Diisi',
            'username.unique' => 'Username Sudah Digunakan',
            'username.min' => 'Username Minimal 4 Karakter',
            'password.required' => 'Password Wajib Diisi',
            'password.confirmed' => 'Konfirmasi Password Tidak Cocok',
            'password.min' => 'Password Minimal 8 Karakter',
            // TAMBAHAN: pesan error untuk aturan password baru
            'password.mixed_case' => 'Password Harus Mengandung Huruf Besar Dan Kecil',
            'password.numbers' => 'Password Harus Mengandung Angka',
            'password.symbols' => 'Password Harus Mengandung Simbol (@, #, $, !, %, *, ?, &, _)',
            'password.uncompromised' => 'Password Ini Terlalu Umum Dan Tidak Aman. Gunakan Password Yang Lebih Kuat',
            'university_id.required' => 'Universitas Wajib Dipilih',
            'university_id.exists' => 'Universitas Tidak Valid',
            'major.required' => 'Jurusan Wajib Diisi',
            'nim.required' => 'NIM Wajib Diisi',
            'nim.unique' => 'NIM Sudah Terdaftar',
            'semester.required' => 'Semester Wajib Dipilih',
            'semester.min' => 'Semester Minimal 1',
            'semester.max' => 'Semester Maksimal 14',
            'whatsapp_number.required' => 'Nomor WhatsApp Wajib Diisi',
            'whatsapp_number.min' => 'Nomor WhatsApp Minimal 10 Digit',
            'profile_photo.image' => 'File Harus Berupa Gambar',
            'profile_photo.mimes' => 'Foto Profil Harus Berformat JPEG, JPG, Atau PNG',
            'profile_photo.max' => 'Ukuran Foto Profil Maksimal 2MB'
        ];
    }

    /**
     * atribut kustom untuk pesan error
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'Nama Depan',
            'last_name' => 'Nama Belakang',
            'email' => 'Email',
            'username' => 'Username',
            'password' => 'Password',
            'university_id' => 'Universitas',
            'major' => 'Jurusan',
            'nim' => 'NIM',
            'semester' => 'Semester',
            'whatsapp_number' => 'Nomor WhatsApp',
            'profile_photo' => 'Foto Profil'
        ];
    }
}
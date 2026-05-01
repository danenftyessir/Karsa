<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class InstitutionRegisterRequest extends FormRequest
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
            'institution_name' => [
                'required',
                'string',
                'max:255',
                'min:3'
            ],
            'institution_type' => [
                'required',
                'in:pemerintah_desa,dinas,ngo,puskesmas,sekolah,perguruan_tinggi,lainnya'
            ],
            'address' => [
                'required',
                'string',
                'max:500'
            ],
            'province_id' => [
                'required',
                'exists:provinces,id'
            ],
            'regency_id' => [
                'required',
                'exists:regencies,id'
            ],
            'official_email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'username' => [
                'required',
                'string',
                'max:50',
                'unique:users,username',
                'regex:/^[a-zA-Z0-9._-]+$/',
                'min:4'
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(app()->isProduction() ? 3 : 0) // Cek hanya di produksi

            ],
            'pic_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s.]+$/'
            ],
            'pic_position' => [
                'required',
                'string',
                'max:100'
            ],
            'phone_number' => [
                'required',
                'string',
                'regex:/^(\+62|62|0)[0-9]{9,12}$/'
            ],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png',
                'max:2048' // 2MB
            ],
            'verification_document' => [
                'required',
                'file',
                'mimes:pdf',
                'max:5120' // 5MB
            ],
            'ktp' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png',
                'max:2048' // 2MB
            ],
            'npwp' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png',
                'max:2048' // 2MB
            ],
            'website' => [
                'nullable',
                'url',
                'max:255'
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ];
    }

    /**
     * pesan error kustom untuk validasi
     */
    public function messages(): array
    {
        return [
            'institution_name.required' => 'nama instansi wajib diisi',
            'institution_name.min' => 'nama instansi minimal 3 karakter',
            'institution_type.required' => 'jenis instansi wajib dipilih',
            'institution_type.in' => 'jenis instansi tidak valid',
            'address.required' => 'alamat lengkap wajib diisi',
            'province_id.required' => 'provinsi wajib dipilih',
            'province_id.exists' => 'provinsi tidak valid',
            'regency_id.required' => 'kabupaten/kota wajib dipilih',
            'regency_id.exists' => 'kabupaten/kota tidak valid',
            'official_email.required' => 'email resmi instansi wajib diisi',
            'official_email.email' => 'format email tidak valid',
            'official_email.unique' => 'email sudah terdaftar',
            'username.required' => 'username wajib diisi',
            'username.unique' => 'username sudah digunakan',
            'username.regex' => 'username hanya boleh berisi huruf, angka, titik, underscore, dan strip',
            'username.min' => 'username minimal 4 karakter',
            'password.required' => 'password wajib diisi',
            'password.confirmed' => 'konfirmasi password tidak cocok',
            'pic_name.required' => 'nama penanggung jawab wajib diisi',
            'pic_name.regex' => 'nama penanggung jawab hanya boleh berisi huruf',
            'pic_position.required' => 'jabatan penanggung jawab wajib diisi',
            'phone_number.required' => 'nomor telepon wajib diisi',
            'phone_number.regex' => 'format nomor telepon tidak valid',
            'logo.image' => 'file harus berupa gambar',
            'logo.mimes' => 'logo harus berformat jpeg, jpg, atau png',
            'logo.max' => 'ukuran logo maksimal 2MB',
            'verification_document.required' => 'dokumen verifikasi wajib diunggah',
            'verification_document.file' => 'file verifikasi tidak valid',
            'verification_document.mimes' => 'dokumen verifikasi harus berformat PDF',
            'verification_document.max' => 'ukuran dokumen maksimal 5MB',
            'ktp.required' => 'KTP wajib diunggah',
            'ktp.image' => 'file KTP harus berupa gambar',
            'ktp.mimes' => 'KTP harus berformat jpeg, jpg, atau png',
            'ktp.max' => 'ukuran KTP maksimal 2MB',
            'npwp.required' => 'NPWP wajib diunggah',
            'npwp.image' => 'file NPWP harus berupa gambar',
            'npwp.mimes' => 'NPWP harus berformat jpeg, jpg, atau png',
            'npwp.max' => 'ukuran NPWP maksimal 2MB',
            'website.url' => 'format url website tidak valid',
            'description.max' => 'deskripsi maksimal 1000 karakter'
        ];
    }

    /**
     * normalize data sebelum validasi
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'official_email' => strtolower($this->official_email ?? ''),
            'username' => strtolower($this->username ?? ''),
            'institution_name' => ucwords(strtolower($this->institution_name ?? '')),
            'pic_name' => ucwords(strtolower($this->pic_name ?? '')),
            // normalize nomor telepon
            'phone_number' => $this->normalizePhoneNumber($this->phone_number ?? '')
        ]);
    }

    /**
     * normalize nomor telepon
     */
    private function normalizePhoneNumber(string $phone): string
    {
        // hapus spasi dan karakter non-numeric
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // convert 08xx menjadi 628xx
        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 1);
        }
        
        // tambahkan + jika belum ada
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }
        
        return $phone;
    }

    /**
     * atribut kustom untuk pesan error
     */
    public function attributes(): array
    {
        return [
            'institution_name' => 'nama instansi',
            'institution_type' => 'jenis instansi',
            'address' => 'alamat',
            'province_id' => 'provinsi',
            'regency_id' => 'kabupaten/kota',
            'official_email' => 'email resmi',
            'username' => 'username',
            'password' => 'password',
            'pic_name' => 'nama penanggung jawab',
            'pic_position' => 'jabatan penanggung jawab',
            'phone_number' => 'nomor telepon',
            'logo' => 'logo instansi',
            'verification_document' => 'dokumen verifikasi',
            'ktp' => 'KTP',
            'npwp' => 'NPWP',
            'website' => 'website',
            'description' => 'deskripsi'
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CompanyRegisterRequest extends FormRequest
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
            'company_name' => [
                'required',
                'string',
                'max:255',
                'min:3'
            ],
            'industry' => [
                'required',
                'string',
                'max:100'
            ],
            'company_size' => [
                'required',
                'in:1-10,11-50,51-200,201-500,501-1000,1000+'
            ],
            'address' => [
                'nullable',
                'string',
                'max:500'
            ],
            'city' => [
                'nullable',
                'string',
                'max:100'
            ],
            'province_id' => [
                'nullable',
                'exists:provinces,id'
            ],
            'email' => [
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
                    ->uncompromised(app()->isProduction() ? 3 : 0)
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^(\+62|62|0)[0-9]{9,12}$/'
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
            ],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048' // 2MB
            ],
            'founded_year' => [
                'nullable',
                'integer',
                'min:1800',
                'max:' . date('Y')
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
            ]
        ];
    }

    /**
     * pesan error kustom untuk validasi
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'nama perusahaan wajib diisi',
            'company_name.min' => 'nama perusahaan minimal 3 karakter',
            'industry.required' => 'industri wajib diisi',
            'company_size.required' => 'ukuran perusahaan wajib dipilih',
            'company_size.in' => 'ukuran perusahaan tidak valid',
            'email.required' => 'email wajib diisi',
            'email.email' => 'format email tidak valid',
            'email.unique' => 'email sudah terdaftar',
            'username.required' => 'username wajib diisi',
            'username.unique' => 'username sudah digunakan',
            'username.regex' => 'username hanya boleh berisi huruf, angka, titik, underscore, dan strip',
            'username.min' => 'username minimal 4 karakter',
            'password.required' => 'password wajib diisi',
            'password.confirmed' => 'konfirmasi password tidak cocok',
            'phone.regex' => 'format nomor telepon tidak valid',
            'website.url' => 'format url website tidak valid',
            'description.max' => 'deskripsi maksimal 1000 karakter',
            'logo.image' => 'file harus berupa gambar',
            'logo.mimes' => 'logo harus berformat jpeg, jpg, png, atau webp',
            'logo.max' => 'ukuran logo maksimal 2MB',
            'founded_year.integer' => 'tahun didirikan harus berupa angka',
            'founded_year.min' => 'tahun didirikan tidak valid',
            'founded_year.max' => 'tahun didirikan tidak boleh melebihi tahun ini',
            'pic_name.required' => 'nama penanggung jawab wajib diisi',
            'pic_name.regex' => 'nama penanggung jawab hanya boleh berisi huruf',
            'pic_position.required' => 'jabatan penanggung jawab wajib diisi'
        ];
    }

    /**
     * normalize data sebelum validasi
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower($this->email ?? ''),
            'username' => strtolower($this->username ?? ''),
            'company_name' => ucwords(strtolower($this->company_name ?? '')),
            'pic_name' => ucwords(strtolower($this->pic_name ?? '')),
            // normalize nomor telepon jika ada
            'phone' => $this->phone ? $this->normalizePhoneNumber($this->phone) : null
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
            'company_name' => 'nama perusahaan',
            'industry' => 'industri',
            'company_size' => 'ukuran perusahaan',
            'address' => 'alamat',
            'city' => 'kota',
            'province_id' => 'provinsi',
            'email' => 'email',
            'username' => 'username',
            'password' => 'password',
            'phone' => 'nomor telepon',
            'website' => 'website',
            'description' => 'deskripsi',
            'logo' => 'logo perusahaan',
            'founded_year' => 'tahun didirikan',
            'pic_name' => 'nama penanggung jawab',
            'pic_position' => 'jabatan penanggung jawab'
        ];
    }
}

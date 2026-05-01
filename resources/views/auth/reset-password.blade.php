@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="bg-white rounded-2xl shadow-xl p-8 page-transition">
    <!-- icon -->
    <div class="flex justify-center mb-6">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
        </div>
    </div>

    <!-- header -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900">buat password baru</h2>
        <p class="text-gray-600 mt-2">
            masukkan password baru untuk akun anda
        </p>
    </div>

    <!-- form -->
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <!-- hidden token -->
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- email -->
        <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                email
            </label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ $email ?? old('email') }}"
                   class="input-field @error('email') border-red-500 @enderror"
                   placeholder="masukkan email anda"
                   required
                   autofocus>
            @error('email')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- password baru -->
        <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                password baru
            </label>
            <input type="password" 
                   id="password" 
                   name="password"
                   class="input-field @error('password') border-red-500 @enderror"
                   placeholder="masukkan password baru"
                   required>
            <p class="text-xs text-gray-500 mt-1">minimal 8 karakter dengan huruf besar, kecil, angka, dan simbol</p>
            @error('password')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- konfirmasi password -->
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                konfirmasi password baru
            </label>
            <input type="password" 
                   id="password_confirmation" 
                   name="password_confirmation"
                   class="input-field"
                   placeholder="masukkan ulang password baru"
                   required>
        </div>

        <!-- submit button -->
        <button type="submit" class="btn-primary w-full">
            reset password
        </button>
    </form>

    <!-- back to login -->
    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm text-primary-600 hover:text-primary-700 transition-colors">
            ← kembali ke login
        </a>
    </div>

    <!-- info -->
    <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <div class="text-sm text-yellow-900">
                <p class="font-medium mb-1">perhatian:</p>
                <ul class="list-disc ml-4 space-y-1">
                    <li>pastikan password yang anda buat kuat dan unik</li>
                    <li>jangan gunakan password yang sama dengan akun lain</li>
                    <li>simpan password anda dengan aman</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
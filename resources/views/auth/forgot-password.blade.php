@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
<div class="bg-white rounded-2xl shadow-xl p-8 page-transition">
    <!-- icon -->
    <div class="flex justify-center mb-6">
        <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center">
            <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
        </div>
    </div>

    <!-- header -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900">lupa password?</h2>
        <p class="text-gray-600 mt-2">
            masukkan email anda dan kami akan mengirimkan link untuk reset password
        </p>
    </div>

    <!-- success message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- form -->
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- email -->
        <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                email
            </label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email') }}"
                   class="input-field @error('email') border-red-500 @enderror"
                   placeholder="masukkan email anda"
                   required
                   autofocus>
            @error('email')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- submit button -->
        <button type="submit" class="btn-primary w-full">
            kirim link reset password
        </button>
    </form>

    <!-- back to login -->
    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm text-primary-600 hover:text-primary-700 transition-colors">
            ← kembali ke login
        </a>
    </div>

    <!-- info -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div class="text-sm text-blue-900">
                <p class="font-medium mb-1">tips:</p>
                <ul class="list-disc ml-4 space-y-1">
                    <li>pastikan email yang anda masukkan sudah terdaftar</li>
                    <li>periksa folder spam jika tidak menerima email</li>
                    <li>link reset password berlaku selama 60 menit</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
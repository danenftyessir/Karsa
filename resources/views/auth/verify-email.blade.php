@extends('layouts.auth')

@section('title', 'Verifikasi Email')

@section('content')
<div class="max-w-xl mx-auto py-12 px-4">
    <div class="bg-white rounded-2xl shadow-xl p-8 page-transition">
        {{-- icon --}}
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>

        {{-- header --}}
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Verifikasi Email Anda</h2>
            <p class="text-gray-600 mt-2">
                Hampir selesai! Kami telah mengirimkan link verifikasi ke email Anda.
            </p>
        </div>

        {{-- content --}}
        <div class="space-y-6">
            @if (session('success') || session('status') == 'verification-link-sent')
                <div class="bg-green-50 border border-green-200 text-sm text-green-800 rounded-lg p-4" role="alert">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('success', 'Link verifikasi baru telah dikirimkan ke alamat email Anda.') }}</span>
                    </div>
                </div>
            @endif

            {{-- email info --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm text-blue-900 font-medium">Email terkirim ke:</p>
                        <p class="text-sm text-blue-800 font-semibold mt-1">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>

            {{-- instruksi --}}
            <div class="text-sm text-gray-700 space-y-3">
                <p>Silakan buka inbox email Anda dan klik link verifikasi yang telah kami kirimkan untuk mengaktifkan akun Anda.</p>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                    <p class="text-xs text-gray-600 font-medium mb-2">Tips:</p>
                    <ul class="text-xs text-gray-600 space-y-1 list-disc list-inside">
                        <li>Periksa folder spam jika email tidak muncul di inbox.</li>
                        <li>Link verifikasi berlaku selama 60 menit.</li>
                    </ul>
                </div>
            </div>

            {{-- resend form --}}
            <div class="border-t pt-6">
                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    
                    <p class="text-sm text-gray-600 mb-3">
                        Tidak menerima email?
                    </p>

                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Kirim Ulang Email Verifikasi
                    </button>

                    @error('email')
                        <p class="text-red-600 text-sm mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </form>
            </div>

            {{-- logout --}}
            <div class="text-center pt-4 border-t">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 underline transition-colors">
                        Bukan Anda? Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
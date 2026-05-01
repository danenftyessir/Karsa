<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Karsa</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-yellow-50 via-white to-gray-100">
    <!-- navigation -->
    <div class="absolute top-6 left-6 right-6 flex items-center justify-between z-10">
        <a href="{{ route('home') }}" class="inline-flex items-center text-gray-800 hover:text-gray-900 transition-colors group">
            <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="font-medium">Back to Home</span>
        </a>
        <div class="flex items-center space-x-6">
            <a href="{{ route('contact') }}" class="text-gray-800 hover:text-gray-900 font-medium transition-colors">Contact</a>
            <a href="{{ route('about') }}" class="text-gray-800 hover:text-gray-900 font-medium transition-colors">About</a>
        </div>
    </div>

    <div class="flex items-center justify-center min-h-screen p-8">
        <div class="w-full max-w-2xl">
            <!-- logo -->
            <div class="flex justify-center mb-8 mt-12">
                <img src="{{ asset('karsa-logo.png') }}"
                     alt="Karsa - Karya Untuk Bangsa"
                     class="h-24 w-auto transform hover:scale-105 transition-transform">
            </div>

            <!-- main card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12">
                <!-- header -->
                <div class="text-center mb-10">
                    <h1 class="text-4xl font-bold text-gray-900 mb-3">Bergabung dengan Karsa</h1>
                    <p class="text-lg text-gray-600">Pilih jenis akun yang sesuai dengan anda</p>
                </div>

                <!-- pilihan user type -->
                <div class="space-y-4">
                    <!-- mahasiswa card -->
                    <a href="{{ route('register.student') }}"
                       class="group block p-6 border-2 border-gray-200 rounded-xl hover:border-yellow-400 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center">
                            <!-- icon -->
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center group-hover:bg-yellow-400 transition-colors">
                                    <svg class="w-8 h-8 text-yellow-600 group-hover:text-gray-900 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- content -->
                            <div class="ml-6 flex-1">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-yellow-600 transition-colors">
                                    Mahasiswa
                                </h3>
                                <p class="text-gray-600 leading-relaxed">
                                    Daftar sebagai mahasiswa untuk mencari dan mengikuti program KKN
                                </p>
                            </div>

                            <!-- arrow -->
                            <div class="ml-4">
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-yellow-600 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </a>

                    <!-- instansi card -->
                    <a href="{{ route('register.institution') }}"
                       class="group block p-6 border-2 border-gray-200 rounded-xl hover:border-gray-800 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center">
                            <!-- icon -->
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-gray-800 transition-colors">
                                    <svg class="w-8 h-8 text-gray-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- content -->
                            <div class="ml-6 flex-1">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-gray-800 transition-colors">
                                    Instansi
                                </h3>
                                <p class="text-gray-600 leading-relaxed">
                                    Daftar sebagai instansi untuk mempublikasikan program KKN
                                </p>
                            </div>

                            <!-- arrow -->
                            <div class="ml-4">
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-gray-800 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </a>

                    <!-- company card -->
                    <a href="{{ route('register.company') }}"
                       class="group block p-6 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center">
                            <!-- icon -->
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                                    <svg class="w-8 h-8 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- content -->
                            <div class="ml-6 flex-1">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                    Company
                                </h3>
                                <p class="text-gray-600 leading-relaxed">
                                    Daftar sebagai perusahaan untuk mencari talent dan posting lowongan
                                </p>
                            </div>

                            <!-- arrow -->
                            <div class="ml-4">
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-blue-600 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- login link -->
                <div class="mt-8 text-center">
                    <p class="text-gray-600">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-gray-800 hover:text-gray-900 font-semibold hover:underline transition-colors">
                            Masuk di sini
                        </a>
                    </p>
                </div>
            </div>


    <script>
        // smooth page transition
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.opacity = '0';
            setTimeout(() => {
                document.body.style.transition = 'opacity 0.3s ease-in-out';
                document.body.style.opacity = '1';
            }, 10);
        });
    </script>
</body>
</html>
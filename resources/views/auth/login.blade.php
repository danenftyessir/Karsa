<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Karsa</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .hero-bg {
            background-image: linear-gradient(135deg, rgba(59, 89, 152, 0.65) 0%, rgba(44, 95, 127, 0.62) 50%, rgba(30, 139, 115, 0.58) 100%),
                              url('{{ asset('login-register-view.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <!-- back to home -->
    <div class="absolute top-6 left-6 right-6 flex items-center justify-between z-10">
        <a href="{{ route('home') }}" class="inline-flex items-center text-gray-800 hover:text-gray-900 transition-colors group" style="text-shadow: 0 1px 3px rgba(255,255,255,0.8);">
            <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="font-medium">Back to Home</span>
        </a>
        <div class="flex items-center space-x-6">
            <a href="{{ route('contact') }}" class="text-gray-800 hover:text-gray-900 font-medium transition-colors" style="text-shadow: 0 1px 3px rgba(255,255,255,0.8);">Contact</a>
            <a href="{{ route('about') }}" class="text-gray-800 hover:text-gray-900 font-medium transition-colors" style="text-shadow: 0 1px 3px rgba(255,255,255,0.8);">About</a>
        </div>
    </div>

    <div class="flex min-h-screen">
        <!-- left side - promotional content with hero image -->
        <div class="hidden lg:flex lg:w-1/2 hero-bg text-white p-12 flex-col justify-center relative">
            <!-- overlay ringan untuk text contrast -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/10 via-transparent to-green-900/5"></div>
            
            <div class="max-w-xl mx-auto relative z-10">
                <!-- tagline -->
                <h1 class="text-5xl font-bold mb-4 leading-tight" style="text-shadow: 0 3px 12px rgba(0,0,0,0.5), 0 1px 4px rgba(0,0,0,0.3);">
                    Connect. Collaborate.<br>
                    <span class="text-lime-300">Create Impact.</span>
                </h1>
                
                <p class="text-xl text-white mb-16 leading-relaxed" style="text-shadow: 0 2px 10px rgba(0,0,0,0.5), 0 1px 4px rgba(0,0,0,0.3);">
                    Join the largest platform connecting students and institutions for meaningful community service programs across Indonesia's villages and communities.
                </p>

                <!-- statistics grid -->
                <div class="grid grid-cols-2 gap-8">
                    <div style="text-shadow: 0 3px 12px rgba(0,0,0,0.6), 0 1px 4px rgba(0,0,0,0.4);">
                        <div class="text-5xl font-bold mb-2">500+</div>
                        <div class="text-lg text-white">Active Projects</div>
                    </div>
                    <div style="text-shadow: 0 3px 12px rgba(0,0,0,0.6), 0 1px 4px rgba(0,0,0,0.4);">
                        <div class="text-5xl font-bold mb-2">50+</div>
                        <div class="text-lg text-white">Universities</div>
                    </div>
                    <div style="text-shadow: 0 3px 12px rgba(0,0,0,0.6), 0 1px 4px rgba(0,0,0,0.4);">
                        <div class="text-5xl font-bold mb-2">10.000+</div>
                        <div class="text-lg text-white">Students</div>
                    </div>
                    <div style="text-shadow: 0 3px 12px rgba(0,0,0,0.6), 0 1px 4px rgba(0,0,0,0.4);">
                        <div class="text-5xl font-bold mb-2">200+</div>
                        <div class="text-lg text-white">Institutions</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- right side - login form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md">
                <!-- logo -->
                <div class="flex justify-center mb-8 mt-8">
                    <img src="{{ asset('karsa-logo.png') }}"
                         alt="Karsa - Karya Untuk Bangsa"
                         class="h-20 w-auto">
                </div>
                
                <!-- header -->
                <div class="mb-8 text-center">
                    <h2 class="text-4xl font-bold text-gray-900 mb-3">Welcome Back!</h2>
                    <p class="text-gray-600 text-lg">
                        Sign in to your Karsa account and continue making impact in your community.
                    </p>
                </div>

                <!-- form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               placeholder="Email"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                               required
                               autofocus>
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="password" 
                                   name="password"
                                   placeholder="Masukkan password Anda"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all pr-12 @error('password') border-red-500 @enderror"
                                   required>
                            <button type="button" 
                                    onclick="togglePassword()"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- submit button -->
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3.5 rounded-lg font-semibold hover:bg-blue-700 transition-all flex items-center justify-center space-x-2 shadow-sm hover:shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Sign In</span>
                    </button>
                </form>

                <!-- register link -->
                <div class="mt-8 text-center">
                    <p class="text-gray-600">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                            Sign up here
                        </a>
                    </p>
                </div>

                <!-- security notice -->
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm text-blue-900">
                        Your login is secured with industry-standard encryption. We never store your password in plain text.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }

        // TAMBAHAN BARU: toast notification
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = 'fixed top-20 right-6 px-6 py-4 rounded-lg shadow-2xl text-white transform transition-all duration-300 z-[9999] flex items-center space-x-3';
            
            const colors = { success: 'bg-green-600', error: 'bg-red-600', info: 'bg-blue-600' };
            const icons = {
                success: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                error: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                info: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            };
            
            toast.classList.add(colors[type] || colors.info);
            toast.innerHTML = `
                <div class="flex-shrink-0">${icons[type]}</div>
                <p class="font-semibold">${message}</p>
                <button onclick="this.parentElement.remove()" class="ml-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100px)';
            document.body.appendChild(toast);
            
            requestAnimationFrame(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            });
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100px)';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // smooth page transition + tampilkan toast
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.opacity = '0';
            setTimeout(() => {
                document.body.style.transition = 'opacity 0.3s ease-in-out';
                document.body.style.opacity = '1';
            }, 10);

            // TAMPILKAN TOAST DARI FLASH MESSAGE
            @if(session('success'))
                setTimeout(() => showToast("{{ session('success') }}", 'success'), 300);
            @endif
            
            @if(session('error'))
                setTimeout(() => showToast("{{ session('error') }}", 'error'), 300);
            @endif
        });
    </script>
</body>
</html>
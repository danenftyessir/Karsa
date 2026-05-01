{{-- resources/views/auth/student-register.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Sebagai Mahasiswa - Karsa</title>
    
    @vite(['resources/css/app.css'])
    
    <style>
        /* background image dengan opacity */
        .register-container.student-register {
            position: relative;
            min-height: 100vh;
        }

        .register-container.student-register::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ asset('student-register-background.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.35;
            z-index: 0;
            pointer-events: none;
        }

        .register-container.student-register > * {
            position: relative;
            z-index: 1;
        }

        /* Form Styling */
        .form-field-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-label.required::after {
            content: ' *';
            color: #ef4444;
        }

        .form-input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            padding-right: 2.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.9375rem;
            transition: all 0.2s;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #FACC15;
            ring: 2px;
            ring-color: rgba(250, 204, 21, 0.3);
            box-shadow: 0 0 0 3px rgba(250, 204, 21, 0.1);
        }

        .form-input-icon {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.25rem;
            height: 1.25rem;
            color: #9ca3af;
            pointer-events: none;
        }

        .error-message {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #ef4444;
        }

        .error-message.hidden {
            display: none;
        }

        .error-message svg {
            width: 1rem;
            height: 1rem;
            flex-shrink: 0;
        }

        /* Step Indicator */
        .step-indicator-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 600px;
            margin: 0 auto;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        .step-circle {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.125rem;
            transition: all 0.3s;
            border: 2px solid #d1d5db;
            background: white;
            color: #9ca3af;
        }

        .step-circle.active {
            background: linear-gradient(135deg, #FDE047 0%, #FACC15 100%);
            border-color: transparent;
            color: #1f2937;
            box-shadow: 0 4px 12px rgba(250, 204, 21, 0.4);
            transform: scale(1.1);
        }

        .step-circle.completed {
            background: #FACC15;
            border-color: transparent;
            color: #1f2937;
        }

        .step-circle.inactive {
            background: #f3f4f6;
            border-color: #e5e7eb;
            color: #9ca3af;
        }

        .step-number {
            font-weight: 600;
        }

        .step-label {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #6b7280;
            text-align: center;
        }

        .step-item .step-circle.active ~ .step-label,
        .step-circle.active + .step-label {
            color: #FACC15;
            font-weight: 600;
        }

        .step-connector {
            flex: 1;
            height: 2px;
            background: #e5e7eb;
            margin: 0 0.5rem;
            position: relative;
            top: -1.5rem;
        }

        .step-connector.completed {
            background: #FACC15;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-size: 0.9375rem;
            border-radius: 0.5rem;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #FDE047 0%, #FACC15 100%);
            color: #1f2937;
            box-shadow: 0 4px 12px rgba(250, 204, 21, 0.3);
        }

        .btn-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, #FACC15 0%, #EAB308 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(250, 204, 21, 0.4);
        }

        .btn-primary:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: white;
            color: #6b7280;
            border: 2px solid #e5e7eb;
        }

        .btn-secondary:hover {
            background: #FEF9C3;
            border-color: #FACC15;
            color: #1f2937;
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: #6b7280;
        }

        .password-toggle .eye-icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        /* File Upload Area */
        .file-upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
            background: #f9fafb;
        }

        .file-upload-area:hover {
            border-color: #FACC15;
            background: #FEF9C3;
        }

        .file-upload-icon {
            margin-bottom: 0.5rem;
        }

        /* Hidden content */
        .hidden {
            display: none !important;
        }

        /* Step content */
        .step-content {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    {{-- navbar tetap --}}
    <nav class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-gray-700 hover:text-gray-900 transition-colors font-semibold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span class="font-medium">Kembali Ke Beranda</span>
            </a>
            
            <div class="flex items-center space-x-6">
                <a href="{{ route('contact') }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">Contact</a>
                <a href="{{ route('about') }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">About</a>
            </div>
        </div>
    </nav>

    <div class="register-container student-register" style="padding-top: 2rem;">
        <div class="relative z-10 flex items-center justify-center min-h-screen py-12 px-4">
            <div class="w-full max-w-4xl">
                {{-- logo & header --}}
                <div class="text-center mb-8">
                    <img src="{{ asset('karsa-logo.png') }}"
                         alt="Karsa - Karya Untuk Bangsa"
                         class="h-20 w-auto mx-auto mb-4">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Daftar Sebagai Mahasiswa</h1>
                    <p class="text-gray-600">Bergabunglah Dengan Platform KKN Terbesar Di Indonesia</p>
                </div>

                {{-- card form --}}
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                    {{-- step indicator --}}
                    <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 p-8 pb-6 border-b border-gray-100">
                        <div class="step-indicator-container">
                            <div class="step-item" id="step1-item">
                                <div class="step-circle active" id="step1-circle">
                                    <span class="step-number">1</span>
                                </div>
                                <span class="step-label">Data Pribadi</span>
                            </div>
                            
                            <div class="step-connector" id="connector1"></div>
                            
                            <div class="step-item" id="step2-item">
                                <div class="step-circle inactive" id="step2-circle">
                                    <span class="step-number">2</span>
                                </div>
                                <span class="step-label">Data Akademik</span>
                            </div>
                            
                            <div class="step-connector" id="connector2"></div>
                            
                            <div class="step-item" id="step3-item">
                                <div class="step-circle inactive" id="step3-circle">
                                    <span class="step-number">3</span>
                                </div>
                                <span class="step-label">Buat Akun</span>
                            </div>
                        </div>
                    </div>

                    {{-- form content --}}
                    <form method="POST" action="{{ route('register.student.submit') }}" 
                          enctype="multipart/form-data" 
                          id="studentRegisterForm"
                          class="p-8">
                        @csrf

                        {{-- step 1: data pribadi --}}
                        <div id="step1-content" class="step-content">
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-800 mb-2">Data Pribadi</h2>
                                <p class="text-gray-600">Isi Data Diri Kamu Dengan Lengkap Ya!</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- nama depan --}}
                                <div class="form-field-group">
                                    <label for="first_name" class="form-label required">Nama Depan</label>
                                    <div class="form-input-wrapper">
                                        <input type="text"
                                               id="first_name"
                                               name="first_name"
                                               value="{{ old('first_name') }}"
                                               placeholder="Contoh: Ahmad"
                                               class="form-input"
                                               autocomplete="given-name"
                                               required>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-first_name"></p>
                                    @error('first_name')
                                        <p class="error-message">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- nama belakang --}}
                                <div class="form-field-group">
                                    <label for="last_name" class="form-label required">Nama Belakang</label>
                                    <div class="form-input-wrapper">
                                        <input type="text"
                                               id="last_name"
                                               name="last_name"
                                               value="{{ old('last_name') }}"
                                               placeholder="Contoh: Hidayat"
                                               class="form-input"
                                               autocomplete="family-name"
                                               required>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-last_name"></p>
                                    @error('last_name')
                                        <p class="error-message">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- email --}}
                                <div class="form-field-group">
                                    <label for="email" class="form-label required">Email Universitas</label>
                                    <div class="form-input-wrapper">
                                        <input type="email"
                                               id="email"
                                               name="email"
                                               value="{{ old('email') }}"
                                               placeholder="Contoh: ahmad@student.university.ac.id"
                                               class="form-input"
                                               autocomplete="email"
                                               required>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-email"></p>
                                    @error('email')
                                        <p class="error-message">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- whatsapp --}}
                                <div class="form-field-group">
                                    <label for="whatsapp_number" class="form-label required">Nomor WhatsApp</label>
                                    <div class="form-input-wrapper">
                                        <input type="tel"
                                               id="whatsapp_number"
                                               name="whatsapp_number"
                                               value="{{ old('whatsapp_number') }}"
                                               placeholder="Contoh: 08123456789"
                                               class="form-input"
                                               autocomplete="tel"
                                               required>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-whatsapp_number"></p>
                                    @error('whatsapp_number')
                                        <p class="error-message">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- foto profil --}}
                            <div class="mt-6">
                                <label class="form-label">Foto Profil (Opsional)</label>
                                <div class="file-upload-area mt-2">
                                    <input type="file" 
                                           id="profile_photo" 
                                           name="profile_photo" 
                                           accept="image/*"
                                           onchange="previewImage(event)"
                                           class="hidden">
                                    <label for="profile_photo" class="cursor-pointer">
                                        <div class="file-upload-icon">
                                            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700 mt-2">Klik Untuk Upload Foto</p>
                                        <p class="text-xs text-gray-500" id="fileLabel">Format: JPG, PNG (Maks. 2MB)</p>
                                    </label>
                                    <div id="imagePreview" class="mt-4 hidden">
                                        <img src="" alt="Preview" class="mx-auto h-32 w-32 rounded-full object-cover">
                                    </div>
                                </div>
                                <p class="error-message hidden" id="error-profile_photo"></p>
                                @error('profile_photo')
                                    <p class="error-message mt-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- navigation --}}
                            <div class="mt-8 flex justify-end">
                                <button type="button" onclick="nextStep(2)" class="btn btn-primary">
                                    Lanjutkan
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- step 2: data akademik --}}
                        <div id="step2-content" class="step-content hidden">
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-800 mb-2">Data Akademik</h2>
                                <p class="text-gray-600">Informasi Tentang Universitas Dan Studimu</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- universitas --}}
                                <div class="form-field-group">
                                    <label for="university_id" class="form-label required">Universitas</label>
                                    <div class="form-input-wrapper">
                                        <select id="university_id"
                                                name="university_id"
                                                class="form-input"
                                                autocomplete="organization"
                                                required>
                                            <option value="">Pilih Universitas</option>
                                            @foreach($universities as $university)
                                                <option value="{{ $university->id }}" {{ old('university_id') == $university->id ? 'selected' : '' }}>
                                                    {{ $university->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-university_id"></p>
                                    @error('university_id')
                                        <p class="error-message">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- jurusan --}}
                                <div class="form-field-group">
                                    <label for="major" class="form-label required">Jurusan</label>
                                    <div class="form-input-wrapper">
                                        <input type="text"
                                               id="major"
                                               name="major"
                                               value="{{ old('major') }}"
                                               placeholder="Contoh: Teknik Informatika"
                                               class="form-input"
                                               autocomplete="off"
                                               required>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-major"></p>
                                    @error('major')
                                        <p class="error-message">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- nim --}}
                                <div class="form-field-group">
                                    <label for="nim" class="form-label required">NIM</label>
                                    <div class="form-input-wrapper">
                                        <input type="text"
                                               id="nim"
                                               name="nim"
                                               value="{{ old('nim') }}"
                                               placeholder="Contoh: 23051234567"
                                               class="form-input"
                                               autocomplete="off"
                                               required>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-nim"></p>
                                    @error('nim')
                                        <p class="error-message">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- semester --}}
                                <div class="form-field-group">
                                    <label for="semester" class="form-label required">Semester</label>
                                    <div class="form-input-wrapper">
                                        <select id="semester"
                                                name="semester"
                                                class="form-input"
                                                autocomplete="off"
                                                required>
                                            <option value="">Pilih Semester</option>
                                            @for($i = 1; $i <= 14; $i++)
                                                <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                                            @endfor
                                        </select>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-semester"></p>
                                    @error('semester')
                                        <p class="error-message">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- navigation --}}
                            <div class="mt-8 flex justify-between">
                                <button type="button" onclick="prevStep(1)" class="btn btn-secondary">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Kembali
                                </button>
                                <button type="button" onclick="nextStep(3)" class="btn btn-primary">
                                    Lanjutkan
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- step 3: buat akun --}}
                        <div id="step3-content" class="step-content hidden">
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-800 mb-2">Buat Akun</h2>
                                <p class="text-gray-600">Buat Username Dan Password Untuk Akunmu</p>
                            </div>

                            <div class="space-y-6">
                                {{-- username --}}
                                <div class="form-field-group">
                                    <label for="username" class="form-label required">Username</label>
                                    <div class="form-input-wrapper">
                                        <input type="text"
                                               id="username"
                                               name="username"
                                               value="{{ old('username') }}"
                                               placeholder="Contoh: ahmadfauzi123"
                                               class="form-input"
                                               autocomplete="username"
                                               required>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-username"></p>
                                    @error('username')
                                        <p class="error-message">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- announcement password requirement --}}
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-yellow-800 mb-2">Syarat Password:</p>
                                            <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                                                <li>Minimal 8 karakter</li>
                                                <li>Mengandung huruf besar (A-Z)</li>
                                                <li>Mengandung huruf kecil (a-z)</li>
                                                <li>Mengandung simbol (@, #, $, !, %, *, ?, &, _)</li>
                                            </ul>
                                            <p class="text-sm text-yellow-700 mt-3">
                                                <span class="font-semibold">Contoh password yang valid:</span>
                                                <code class="bg-yellow-100 px-2 py-1 rounded text-yellow-900 font-mono">Mahasiswa2024!</code> atau
                                                <code class="bg-yellow-100 px-2 py-1 rounded text-yellow-900 font-mono">Karsa#2024</code>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- password --}}
                                <div class="form-field-group">
                                    <label for="password" class="form-label required">Password</label>
                                    <div class="form-input-wrapper">
                                        <input type="password"
                                            id="password"
                                            name="password"
                                            placeholder="Minimal 8 Karakter"
                                            class="form-input"
                                            autocomplete="new-password"
                                            required>
                                        <button type="button" 
                                                onclick="togglePassword('password')" 
                                                class="password-toggle">
                                            <svg class="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="error-message hidden" id="error-password"></p>
                                    @error('password')
                                        <p class="error-message">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- konfirmasi password --}}
                                <div class="form-field-group">
                                    <label for="password_confirmation" class="form-label required">Konfirmasi Password</label>
                                    <div class="form-input-wrapper">
                                        <input type="password"
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            placeholder="Ketik Ulang Password"
                                            class="form-input"
                                            autocomplete="new-password"
                                            required>
                                        <button type="button" 
                                                onclick="togglePassword('password_confirmation')" 
                                                class="password-toggle">
                                            <svg class="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="error-message hidden" id="error-password_confirmation"></p>
                                    @error('password_confirmation')
                                        <p class="error-message">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- terms --}}
                                <div class="flex items-start">
                                    <input type="checkbox"
                                           id="terms"
                                           name="terms"
                                           class="mt-1 w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500"
                                           required>
                                    <label for="terms" class="ml-3 text-sm text-gray-600">
                                        Saya Setuju Dengan <a href="#" class="text-yellow-600 hover:text-yellow-700 font-semibold">Syarat Dan Ketentuan</a> Serta <a href="#" class="text-yellow-600 hover:text-yellow-700 font-semibold">Kebijakan Privasi</a> Karsa
                                    </label>
                                </div>
                            </div>

                            {{-- navigation --}}
                            <div class="mt-8 flex justify-between">
                                <button type="button" onclick="prevStep(2)" class="btn btn-secondary">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Kembali
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Daftar Sekarang
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- login link --}}
                    <div class="px-8 pb-8 text-center">
                        <p class="text-gray-600">
                            Sudah Punya Akun?
                            <a href="{{ route('login') }}" class="text-yellow-600 hover:text-yellow-700 font-semibold transition-colors">
                                Login Di Sini
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- loading overlay --}}
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg p-8 flex flex-col items-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-yellow-400"></div>
            <p class="mt-4 text-gray-700 font-semibold">Mendaftarkan Akun Anda...</p>
        </div>
    </div>
    <script>
    let currentStep = 1;

    // fungsi untuk handle error validasi
    function handleValidationErrors(errors) {
        // bersihkan semua error sebelumnya
        document.querySelectorAll('.error-message').forEach(el => {
            if (!el.classList.contains('hidden')) {
                el.classList.add('hidden');
                el.textContent = '';
            }
        });
        document.querySelectorAll('.form-input').forEach(el => {
            el.classList.remove('border-red-500');
        });

        // tampilkan error baru
        Object.keys(errors).forEach(field => {
            const errorEl = document.getElementById(`error-${field}`);
            const inputEl = document.getElementById(field);
            
            if (errorEl) {
                errorEl.classList.remove('hidden');
                errorEl.innerHTML = `
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    ${errors[field][0]}
                `;
            }
            if (inputEl) {
                inputEl.classList.add('border-red-500');
            }
        });
    }

    // fungsi untuk pindah ke step selanjutnya dengan validasi
    async function nextStep(step) {
        const loadingOverlay = document.getElementById('loadingOverlay');
        loadingOverlay.style.display = 'flex';

        // ambil data form untuk validasi
        const form = document.getElementById('studentRegisterForm');
        const formData = new FormData(form);
        formData.append('step', currentStep);

        try {
            // kirim request validasi ke backend
            const response = await fetch("{{ route('validation.student.step') }}", {
                method: 'POST',
                headers: { 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const data = await response.json();

            // jika ada error validasi
            if (response.status === 422) {
                handleValidationErrors(data.errors);
                loadingOverlay.style.display = 'none';
                return;
            }

            // jika validasi berhasil, lanjutkan alur visual
            handleValidationErrors({});
            
            // logika visual untuk pindah step
            const currentCircle = document.getElementById(`step${currentStep}-circle`);
            const currentContent = document.getElementById(`step${currentStep}-content`);
            
            currentContent.classList.add('hidden');
            currentCircle.classList.remove('active');
            currentCircle.classList.add('completed');
            
            const nextCircle = document.getElementById(`step${step}-circle`);
            const nextContent = document.getElementById(`step${step}-content`);
            
            nextContent.classList.remove('hidden');
            nextCircle.classList.remove('inactive');
            nextCircle.classList.add('active');
            
            if (currentStep < step) {
                document.getElementById(`connector${currentStep}`).classList.add('completed');
            }
            
            currentStep = step;
            window.scrollTo({ top: 0, behavior: 'smooth' });

        } catch (error) {
            console.error('Terjadi kesalahan saat validasi:', error);
            alert('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
        } finally {
            loadingOverlay.style.display = 'none';
        }
    }

    // fungsi untuk kembali ke step sebelumnya
    function prevStep(step) {
        handleValidationErrors({});
        const currentCircle = document.getElementById(`step${currentStep}-circle`);
        const currentContent = document.getElementById(`step${currentStep}-content`);
        
        currentContent.classList.add('hidden');
        currentCircle.classList.remove('active');
        currentCircle.classList.add('inactive');
        
        const prevCircle = document.getElementById(`step${step}-circle`);
        const prevContent = document.getElementById(`step${step}-content`);
        
        prevContent.classList.remove('hidden');
        prevCircle.classList.remove('completed');
        prevCircle.classList.add('active');
        
        if (currentStep > step) {
            document.getElementById(`connector${step}`).classList.remove('completed');
        }
        
        currentStep = step;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // fungsi utilitas
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');
        const label = document.getElementById('fileLabel');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.querySelector('img').src = e.target.result;
                preview.classList.remove('hidden');
                label.textContent = file.name;
            }
            reader.readAsDataURL(file);
        }
    }

    // event listener untuk submit form dengan AJAX
    document.getElementById('studentRegisterForm')?.addEventListener('submit', async function(e) {
        e.preventDefault(); // batalkan submit bawaan

        const loadingOverlay = document.getElementById('loadingOverlay');
        loadingOverlay.style.display = 'flex';
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch("{{ route('register.student.submit') }}", {
                method: 'POST',
                headers: { 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const data = await response.json();

            // jika backend mengembalikan error validasi saat submit akhir
            if (response.status === 422) {
                // cari tahu di langkah mana error pertama terjadi
                const errorFields = Object.keys(data.errors);
                const step1Fields = ['first_name', 'last_name', 'email', 'whatsapp_number', 'profile_photo'];
                const step2Fields = ['university_id', 'major', 'nim', 'semester'];

                let errorStep = 3;
                if (errorFields.some(field => step1Fields.includes(field))) {
                    errorStep = 1;
                } else if (errorFields.some(field => step2Fields.includes(field))) {
                    errorStep = 2;
                }
                
                // pindah ke step yang error dan tampilkan pesan
                if (currentStep !== errorStep) {
                    // reset semua step indicator
                    for(let i=1; i<=3; i++) {
                        document.getElementById(`step${i}-content`).classList.add('hidden');
                        document.getElementById(`step${i}-circle`).className = 'step-circle inactive';
                        if(i < 3) document.getElementById(`connector${i}`).classList.remove('completed');
                    }
                    // setup ulang ke step yang error
                    for(let i=1; i<errorStep; i++) {
                        document.getElementById(`step${i}-circle`).classList.add('completed');
                        if(i < 3) document.getElementById(`connector${i}`).classList.add('completed');
                    }
                    document.getElementById(`step${errorStep}-content`).classList.remove('hidden');
                    document.getElementById(`step${errorStep}-circle`).classList.add('active');
                    currentStep = errorStep;
                }

                handleValidationErrors(data.errors);
                loadingOverlay.style.display = 'none';
                return;
            }

            // jika sukses (status 200 dan success = true), redirect
            if (response.ok && data.success && data.redirect_url) {
                // redirect ke dashboard student
                window.location.href = data.redirect_url;
                // jangan sembunyikan loading karena halaman akan redirect
            } else {
                // handle error lainnya
                alert(data.message || 'Terjadi kesalahan saat pendaftaran.');
                loadingOverlay.style.display = 'none';
            }

        } catch(error) {
            console.error('Submit error:', error);
            alert('Terjadi Kesalahan Saat Mengirimkan Formulir. Periksa Koneksi Internet Anda Dan Coba Lagi.');
            loadingOverlay.style.display = 'none';
        }
    });

    // handle server errors di console untuk debugging
    window.addEventListener('error', function(e) {
        console.error('Global error:', e);
    });
    </script>
</body>
</html>
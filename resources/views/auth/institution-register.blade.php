{{-- resources/views/auth/institution-register.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Sebagai Instansi - Karsa</title>
    
    @vite(['resources/css/app.css'])
    
    {{-- tambahkan Alpine.js CDN untuk membuat dropdown dinamis bekerja --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    
    <style>
        /* background image dengan opacity */
        .register-container.institution-register {
            position: relative;
            min-height: 100vh;
        }

        .register-container.institution-register::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ asset('institution-register-background.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.35;
            z-index: 0;
            pointer-events: none;
        }

        .register-container.institution-register > * {
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
            border-color: #1f2937;
            ring: 2px;
            ring-color: rgba(31, 41, 55, 0.3);
            box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.1);
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
            max-width: 800px;
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
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            border-color: transparent;
            color: white;
            box-shadow: 0 4px 12px rgba(31, 41, 55, 0.4);
            transform: scale(1.1);
        }

        .step-circle.completed {
            background: #1f2937;
            border-color: transparent;
            color: white;
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
            color: #1f2937;
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
            background: #1f2937;
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
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(31, 41, 55, 0.3);
        }

        .btn-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(31, 41, 55, 0.4);
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
            background: #f3f4f6;
            border-color: #1f2937;
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
            border-color: #1f2937;
            background: #f3f4f6;
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
    <nav class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-gray-700 hover:text-gray-900 transition-colors font-semibold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span class="font-medium">Back</span>
            </a>
            
            <div class="flex items-center space-x-6">
                <a href="{{ route('contact') }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">Contact</a>
                <a href="{{ route('about') }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">About</a>
            </div>
        </div>
    </nav>

    <div class="register-container institution-register" style="padding-top: 5rem;">
        <div class="relative z-10 flex items-center justify-center min-h-screen py-12 px-4">
            <div class="w-full max-w-4xl">
                {{-- logo & header --}}
                <div class="text-center mb-8">
                    <img src="{{ asset('karsa-logo.png') }}" alt="Karsa - Karya Untuk Bangsa" class="h-24 mx-auto mb-6">
                    <h1 class="text-4xl font-bold text-gray-900 mb-3">Daftar Sebagai Instansi</h1>
                    <p class="text-gray-600 mb-4">Mulai Posting Masalah Dan Temukan Mahasiswa KKN Terbaik</p>
                    <div class="inline-flex items-center gap-2 text-sm">
                        <span class="text-gray-600">Sudah Punya Akun?</span>
                        <a href="{{ route('login') }}" class="text-green-600 hover:text-green-700 font-semibold transition-colors">Masuk Di Sini</a>
                    </div>
                </div>

                {{-- main form card --}}
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                    {{-- step indicator --}}
                    <div class="bg-gradient-to-r from-gray-100 to-gray-200 p-8 pb-6 border-b border-gray-100">
                        <div class="step-indicator-container">
                            <div class="step-item" id="step1-item">
                                <div class="step-circle active" id="step1-circle">
                                    <span class="step-number">1</span>
                                </div>
                                <span class="step-label">Data Instansi</span>
                            </div>
                            
                            <div class="step-connector" id="connector1"></div>
                            
                            <div class="step-item" id="step2-item">
                                <div class="step-circle inactive" id="step2-circle">
                                    <span class="step-number">2</span>
                                </div>
                                <span class="step-label">Lokasi</span>
                            </div>
                            
                            <div class="step-connector" id="connector2"></div>
                            
                            <div class="step-item" id="step3-item">
                                <div class="step-circle inactive" id="step3-circle">
                                    <span class="step-number">3</span>
                                </div>
                                <span class="step-label">Penanggung Jawab</span>
                            </div>
                            
                            <div class="step-connector" id="connector3"></div>
                            
                            <div class="step-item" id="step4-item">
                                <div class="step-circle inactive" id="step4-circle">
                                    <span class="step-number">4</span>
                                </div>
                                <span class="step-label">Akun & Verifikasi</span>
                            </div>
                        </div>
                    </div>

                    {{-- form content --}}
                    <form method="POST" 
                          action="{{ route('register.institution.submit') }}" 
                          enctype="multipart/form-data" 
                          id="institutionRegisterForm"
                          class="p-8"
                          x-data="institutionForm()">
                        @csrf

                        {{-- STEP 1: Data Instansi --}}
                        <div id="step1-content" class="step-content">
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-800 mb-2">Data Instansi</h2>
                                <p class="text-gray-600">Informasi Lengkap Tentang Instansi Anda</p>
                            </div>

                            <div class="space-y-6">
                                {{-- nama instansi --}}
                                <div class="form-field-group">
                                    <label for="institution_name" class="form-label required">Nama Instansi</label>
                                    <div class="form-input-wrapper">
                                        <input type="text"
                                               id="institution_name"
                                               name="institution_name"
                                               value="{{ old('institution_name') }}"
                                               placeholder="Contoh: Desa Sukamaju"
                                               class="form-input"
                                               autocomplete="organization"
                                               required>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-institution_name"></p>
                                    @error('institution_name')
                                        <p class="error-message">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- jenis instansi --}}
                                <div class="form-field-group">
                                    <label for="institution_type" class="form-label required">Jenis Instansi</label>
                                    <div class="form-input-wrapper">
                                        <select id="institution_type"
                                                name="institution_type"
                                                class="form-input"
                                                autocomplete="off"
                                                required>
                                            <option value="">Pilih Jenis Instansi</option>
                                            <option value="pemerintah_desa" {{ old('institution_type') == 'pemerintah_desa' ? 'selected' : '' }}>Pemerintah Desa</option>
                                            <option value="dinas" {{ old('institution_type') == 'dinas' ? 'selected' : '' }}>Dinas</option>
                                            <option value="ngo" {{ old('institution_type') == 'ngo' ? 'selected' : '' }}>NGO</option>
                                            <option value="puskesmas" {{ old('institution_type') == 'puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                                            <option value="sekolah" {{ old('institution_type') == 'sekolah' ? 'selected' : '' }}>Sekolah</option>
                                            <option value="perguruan_tinggi" {{ old('institution_type') == 'perguruan_tinggi' ? 'selected' : '' }}>Perguruan Tinggi</option>
                                            <option value="lainnya" {{ old('institution_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-institution_type"></p>
                                    @error('institution_type')
                                        <p class="error-message">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- email resmi --}}
                                <div class="form-field-group">
                                    <label for="official_email" class="form-label required">Email Resmi Instansi</label>
                                    <div class="form-input-wrapper">
                                        <input type="email"
                                               id="official_email"
                                               name="official_email"
                                               value="{{ old('official_email') }}"
                                               placeholder="Contoh: info@desasukamaju.go.id"
                                               class="form-input"
                                               autocomplete="email"
                                               required>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-official_email"></p>
                                    @error('official_email')
                                        <p class="error-message">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-end mt-8">
                                <button type="button" onclick="nextStep(2)" class="btn btn-primary">
                                    Lanjutkan
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- STEP 2: Lokasi --}}
                        <div id="step2-content" class="step-content" style="display: none;">
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-800 mb-2">Lokasi</h2>
                                <p class="text-gray-600">Informasi Lokasi Instansi Anda</p>
                            </div>

                            <div class="space-y-6">
                                {{-- alamat lengkap --}}
                                <div class="form-field-group">
                                    <label for="address" class="form-label required">Alamat Lengkap</label>
                                    <textarea id="address"
                                              name="address"
                                              rows="3"
                                              placeholder="Contoh: Jl. Raya Sukamaju No. 123, RT 02/RW 05"
                                              class="form-input"
                                              style="padding-right: 1rem;"
                                              autocomplete="street-address"
                                              required>{{ old('address') }}</textarea>
                                    <p class="error-message hidden" id="error-address"></p>
                                    @error('address')
                                        <p class="error-message">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- provinsi --}}
                                <div class="form-field-group">
                                    <label for="province_id" class="form-label required">Provinsi</label>
                                    <div class="form-input-wrapper">
                                        <select id="province_id"
                                                name="province_id"
                                                x-model="selectedProvince"
                                                @change="loadRegencies()"
                                                class="form-input"
                                                autocomplete="address-level1"
                                                required>
                                            <option value="">Pilih Provinsi</option>
                                            @foreach($provinces as $province)
                                                <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>
                                                    {{ $province->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-province_id"></p>
                                    @error('province_id')
                                        <p class="error-message">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- kabupaten/kota --}}
                                <div class="form-field-group">
                                    <label for="regency_id" class="form-label required">Kabupaten/Kota</label>
                                    <div class="form-input-wrapper">
                                        <select id="regency_id"
                                                name="regency_id"
                                                x-model="selectedRegency"
                                                class="form-input"
                                                autocomplete="address-level2"
                                                required
                                                :disabled="!selectedProvince">
                                            <option value="">Pilih Kabupaten/Kota</option>
                                            <template x-for="regency in regencies" :key="regency.id">
                                                <option :value="regency.id" x-text="regency.name"></option>
                                            </template>
                                        </select>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-regency_id"></p>
                                    @error('regency_id')
                                        <p class="error-message">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-between mt-8">
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

                        {{-- STEP 3: Penanggung Jawab --}}
                        <div id="step3-content" class="step-content" style="display: none;">
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-800 mb-2">Penanggung Jawab</h2>
                                <p class="text-gray-600">Informasi Kontak Dan Dokumen Pendukung</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- nama pic --}}
                                <div class="form-field-group">
                                    <label for="pic_name" class="form-label required">Nama PIC</label>
                                    <div class="form-input-wrapper">
                                        <input type="text"
                                               id="pic_name"
                                               name="pic_name"
                                               value="{{ old('pic_name') }}"
                                               placeholder="Contoh: Budi Santoso"
                                               class="form-input"
                                               autocomplete="name"
                                               required>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-pic_name"></p>
                                    @error('pic_name')
                                        <p class="error-message">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- jabatan pic --}}
                                <div class="form-field-group">
                                    <label for="pic_position" class="form-label required">Jabatan PIC</label>
                                    <div class="form-input-wrapper">
                                        <input type="text"
                                               id="pic_position"
                                               name="pic_position"
                                               value="{{ old('pic_position') }}"
                                               placeholder="Contoh: Sekretaris Desa"
                                               class="form-input"
                                               autocomplete="organization-title"
                                               required>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-pic_position"></p>
                                    @error('pic_position')
                                        <p class="error-message">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- nomor telepon --}}
                                <div class="form-field-group">
                                    <label for="phone_number" class="form-label required">Nomor Telepon</label>
                                    <div class="form-input-wrapper">
                                        <input type="tel"
                                               id="phone_number"
                                               name="phone_number"
                                               value="{{ old('phone_number') }}"
                                               placeholder="Contoh: 081234567890"
                                               class="form-input"
                                               autocomplete="tel"
                                               required>
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-phone_number"></p>
                                    @error('phone_number')
                                        <p class="error-message">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- logo instansi --}}
                                <div class="form-field-group">
                                    <label for="logo" class="form-label">Logo Instansi (Opsional)</label>
                                    <input type="file"
                                           id="logo"
                                           name="logo"
                                           accept="image/*"
                                           class="form-input"
                                           style="padding: 0.5rem 1rem;"
                                           autocomplete="off">
                                    <p class="error-message hidden" id="error-logo"></p>
                                    @error('logo')
                                        <p class="error-message">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- dokumen verifikasi --}}
                                <div class="form-field-group">
                                    <label for="verification_document" class="form-label required">Dokumen Verifikasi</label>
                                    <input type="file"
                                           id="verification_document"
                                           name="verification_document"
                                           accept="application/pdf"
                                           class="form-input"
                                           style="padding: 0.5rem 1rem;"
                                           autocomplete="off"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">Format: PDF, Maksimal 5MB (Surat Tugas/SK/Akta/Surat Pengesahan)</p>
                                    <p class="error-message hidden" id="error-verification_document"></p>
                                    @error('verification_document')
                                        <p class="error-message">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- KTP PIC --}}
                                <div class="form-field-group">
                                    <label for="ktp" class="form-label required">KTP Penanggung Jawab</label>
                                    <input type="file"
                                           id="ktp"
                                           name="ktp"
                                           accept="image/jpeg,image/jpg,image/png"
                                           class="form-input"
                                           style="padding: 0.5rem 1rem;"
                                           autocomplete="off"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG - Maksimal 2MB</p>
                                    <p class="error-message hidden" id="error-ktp"></p>
                                    @error('ktp')
                                        <p class="error-message">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- NPWP Instansi --}}
                                <div class="form-field-group">
                                    <label for="npwp" class="form-label required">NPWP Instansi</label>
                                    <input type="file"
                                           id="npwp"
                                           name="npwp"
                                           accept="image/jpeg,image/jpg,image/png"
                                           class="form-input"
                                           style="padding: 0.5rem 1rem;"
                                           autocomplete="off"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG - Maksimal 2MB</p>
                                    <p class="error-message hidden" id="error-npwp"></p>
                                    @error('npwp')
                                        <p class="error-message">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- website --}}
                                <div class="form-field-group">
                                    <label for="website" class="form-label">Website (Opsional)</label>
                                    <div class="form-input-wrapper">
                                        <input type="url"
                                               id="website"
                                               name="website"
                                               value="{{ old('website') }}"
                                               placeholder="https://www.contoh.go.id"
                                               class="form-input"
                                               autocomplete="url">
                                        <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                        </svg>
                                    </div>
                                    <p class="error-message hidden" id="error-website"></p>
                                    @error('website')
                                        <p class="error-message">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- deskripsi instansi --}}
                                <div class="md:col-span-2 form-field-group">
                                    <label for="description" class="form-label">Deskripsi Instansi (Opsional)</label>
                                    <textarea id="description"
                                              name="description"
                                              rows="4"
                                              placeholder="Ceritakan Tentang Instansi Anda..."
                                              class="form-input"
                                              style="padding-right: 1rem;"
                                              autocomplete="off">{{ old('description') }}</textarea>
                                    <p class="error-message hidden" id="error-description"></p>
                                    @error('description')
                                        <p class="error-message">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-between mt-8">
                                <button type="button" onclick="prevStep(2)" class="btn btn-secondary">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Kembali
                                </button>
                                <button type="button" onclick="nextStep(4)" class="btn btn-primary">
                                    Lanjutkan
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- STEP 4: Akun & Verifikasi --}}
                        <div id="step4-content" class="step-content" style="display: none;">
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-800 mb-2">Akun & Verifikasi</h2>
                                <p class="text-gray-600">Buat Username Dan Password Untuk Akun Anda</p>
                            </div>

                            <div class="space-y-6">
                                <div class="form-field-group">
                                    <label for="username" class="form-label required">Username</label>
                                    <div class="form-input-wrapper">
                                        <input type="text"
                                               id="username"
                                               name="username"
                                               value="{{ old('username') }}"
                                               placeholder="Contoh: desasukamaju"
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
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                
                                {{-- peringatan password requirement --}}
                                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-amber-800 mb-2">Syarat Password:</p>
                                            <ul class="text-sm text-amber-700 space-y-1 list-disc list-inside">
                                                <li>Minimal 8 karakter</li>
                                                <li>Mengandung huruf besar (A-Z)</li>
                                                <li>Mengandung huruf kecil (a-z)</li>
                                                <li>Mengandung simbol (@, #, $, !, %, *, ?, &, _)</li>
                                            </ul>
                                            <p class="text-sm text-amber-700 mt-3">
                                                <span class="font-semibold">Contoh password yang valid:</span> 
                                                <code class="bg-amber-100 px-2 py-1 rounded text-amber-900 font-mono">Desa2024!</code> atau 
                                                <code class="bg-amber-100 px-2 py-1 rounded text-amber-900 font-mono">Sukamaju#2024</code>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
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
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
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
                                </div>
                            </div>

                            <div class="flex justify-between mt-8">
                                <button type="button" onclick="prevStep(3)" class="btn btn-secondary">
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
                            <a href="{{ route('login') }}" class="text-gray-800 hover:text-gray-900 font-semibold transition-colors">
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
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-gray-800"></div>
            <p class="mt-4 text-gray-700 font-semibold">Mendaftarkan Akun Anda...</p>
        </div>
    </div>

    <script>
    // current step tracker
    let currentStep = 1;

    // navigasi bebas antar step tanpa validasi
    function nextStep(step) {
        showStep(step);
        currentStep = step;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function prevStep(step) {
        showStep(step);
        currentStep = step;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // showStep untuk handle 4 steps dengan step-circle
    function showStep(step) {
        // hide semua step content
        for (let i = 1; i <= 4; i++) {
            const content = document.getElementById(`step${i}-content`);
            if (content) content.style.display = 'none';
        }

        // tampilkan step saat ini
        const currentContent = document.getElementById(`step${step}-content`);
        if (currentContent) currentContent.style.display = 'block';

        // update step circle indicators
        for (let i = 1; i <= 4; i++) {
            const circle = document.getElementById(`step${i}-circle`);
            if (!circle) continue;
            
            circle.classList.remove('active', 'completed', 'inactive');
            
            if (i < step) {
                circle.classList.add('completed');
            } else if (i === step) {
                circle.classList.add('active');
            } else {
                circle.classList.add('inactive');
            }
            
            // update connector
            if (i < 4) {
                const connector = document.getElementById(`connector${i}`);
                if (connector) {
                    if (i < step) {
                        connector.classList.add('completed');
                    } else {
                        connector.classList.remove('completed');
                    }
                }
            }
        }
    }

    // Alpine.js untuk dynamic province-regency dropdown
    function institutionForm() {
        return {
            selectedProvince: '{{ old("province_id") }}',
            selectedRegency: '{{ old("regency_id") }}',
            regencies: @json($regencies),
            
            async loadRegencies() {
                if (!this.selectedProvince) {
                    this.regencies = [];
                    this.selectedRegency = '';
                    return;
                }

                try {
                    const response = await fetch(`/api/public/regencies/${this.selectedProvince}`);
                    if (response.ok) {
                        this.regencies = await response.json();
                    }
                } catch (error) {
                    console.error('Failed to load regencies:', error);
                    alert('Gagal Memuat Data Kabupaten/Kota. Silakan Coba Lagi.');
                }
            }
        }
    }

    // password toggle function
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    document.getElementById('institutionRegisterForm')?.addEventListener('submit', async function(e) {
        e.preventDefault(); // batalkan submit bawaan

        const loadingOverlay = document.getElementById('loadingOverlay');
        loadingOverlay.style.display = 'flex';
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch("{{ route('register.institution.submit') }}", {
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
                const step1Fields = ['institution_name', 'institution_type', 'official_email'];
                const step2Fields = ['address', 'province_id', 'regency_id'];
                const step3Fields = ['pic_name', 'pic_position', 'phone_number', 'logo', 'verification_document', 'ktp', 'npwp', 'website', 'description'];
                const step4Fields = ['username', 'password', 'password_confirmation'];

                let errorStep = 4;
                if (errorFields.some(field => step1Fields.includes(field))) {
                    errorStep = 1;
                } else if (errorFields.some(field => step2Fields.includes(field))) {
                    errorStep = 2;
                } else if (errorFields.some(field => step3Fields.includes(field))) {
                    errorStep = 3;
                }
                
                // pindah ke step yang error dan tampilkan pesan
                if (currentStep !== errorStep) {
                    showStep(errorStep);
                    currentStep = errorStep;
                }

                // tampilkan error di bawah setiap input
                Object.keys(data.errors).forEach(field => {
                    const inputEl = document.getElementById(field);
                    const errorMsg = data.errors[field][0];
                    
                    if (inputEl) {
                        // tambah border merah
                        inputEl.classList.add('border-red-500');
                        
                        // cari atau buat elemen error message
                        let errorEl = inputEl.parentElement.querySelector('.error-message');
                        if (!errorEl) {
                            errorEl = document.createElement('p');
                            errorEl.className = 'mt-1 text-sm text-red-600 error-message';
                            inputEl.parentElement.appendChild(errorEl);
                        }
                        errorEl.textContent = errorMsg;
                    }
                });

                loadingOverlay.style.display = 'none';
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            // jika sukses (status 200 dan success = true), redirect
            if (response.ok && data.success && data.redirect_url) {
                // redirect ke dashboard institution
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

    // handle error dari server saat page load (tetap pertahankan)
    @if($errors->any())
        const errorFields = @json($errors->keys());
        const step1Fields = ['institution_name', 'institution_type', 'official_email'];
        const step2Fields = ['address', 'province_id', 'regency_id'];
        const step3Fields = ['pic_name', 'pic_position', 'phone_number', 'logo', 'verification_document', 'ktp', 'npwp', 'website', 'description'];
        const step4Fields = ['username', 'password', 'password_confirmation'];
        
        let errorStep = 1;
        if (errorFields.some(field => step4Fields.includes(field))) {
            errorStep = 4;
        } else if (errorFields.some(field => step3Fields.includes(field))) {
            errorStep = 3;
        } else if (errorFields.some(field => step2Fields.includes(field))) {
            errorStep = 2;
        }
        
        showStep(errorStep);
        currentStep = errorStep;
    @endif
    </script>
</body>
</html>